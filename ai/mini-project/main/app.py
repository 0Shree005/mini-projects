# app.py
import streamlit as st
import pandas as pd
import plotly.graph_objects as go
import json
import os

from engine import run_expert_system
from data import FLEET, INITIAL_CARGO, CITIES

# --- Persistence Layer ---
DB_FILE = "state_db.json"

def save_data():
    data = {
        "cargo_data": st.session_state.cargo_data,
        "results": st.session_state.results,
        "logs": st.session_state.logs
    }
    with open(DB_FILE, "w") as f:
        json.dump(data, f)

def load_data():
    if os.path.exists(DB_FILE):
        with open(DB_FILE, "r") as f:
            return json.load(f)
    return None

# --- Setup ---
st.set_page_config(page_title="SkyFlow AI Command", layout="wide", initial_sidebar_state="expanded")

# --- Custom Styling ---
st.markdown("""
    <style>
    .main { background-color: #0e1117; }
    .stMetric { background-color: #1e2130; padding: 15px; border-radius: 10px; border-left: 5px solid #39FF14; }
    </style>
    """, unsafe_allow_html=True)

st.title("✈️ SkyFlow: Global Logistics Engine")

# --- Initialize Session State from Disk ---
saved_state = load_data()

if 'cargo_data' not in st.session_state:
    st.session_state.cargo_data = saved_state["cargo_data"] if saved_state else INITIAL_CARGO.copy()
if 'results' not in st.session_state:
    st.session_state.results = saved_state["results"] if saved_state else []
if 'logs' not in st.session_state:
    st.session_state.logs = saved_state["logs"] if saved_state else []

# --- Sidebar ---
st.sidebar.header("🕹️ Control Tower")
weather = st.sidebar.select_slider("Weather Severity", options=["Clear", "Cloudy", "Stormy"])

with st.sidebar.expander("➕ Register Cargo"):
    c_id = st.text_input("ID", value=f"C{len(st.session_state.cargo_data)+1:03}")
    c_type = st.selectbox("Category", ["Medical", "Perishable", "General"])
    c_weight = st.number_input("Weight (kg)", 10, 10000, 500)
    c_org = st.selectbox("Origin", list(CITIES.keys()))
    c_dst = st.selectbox("Destination", list(CITIES.keys()), index=1)
    
    if st.button("Add to System", width='stretch'):
        if c_org == c_dst: 
            st.error("Route error: Origin = Destination")
        else:
            prio = {"Medical": 1, "Perishable": 2, "General": 3}[c_type]
            st.session_state.cargo_data.append({
                "id": c_id, "type": c_type, "weight": c_weight, 
                "origin": c_org, "destination": c_dst, "priority": prio
            })
            save_data() # Save to disk
            st.rerun()

if st.sidebar.button("🧹 Reset System", width='stretch'):
    st.session_state.cargo_data, st.session_state.results, st.session_state.logs = [], [], []
    if os.path.exists(DB_FILE):
        os.remove(DB_FILE)
    st.rerun()

# --- Action Button ---
if st.button("🚀 INITIATE GLOBAL INFERENCE", width='stretch'):
    res, lg = run_expert_system([f.copy() for f in FLEET], st.session_state.cargo_data, weather)
    st.session_state.results, st.session_state.logs = res, lg
    save_data() # Save results to disk

# --- Metrics Header ---
m1, m2, m3 = st.columns(3)
m1.metric("Global Fleet Status", f"{len(FLEET)} Active", "Online")
m2.metric("Queue Depth", f"{len(st.session_state.cargo_data)} Shipments")
m3.metric("Safety Protocol", weather, "Active" if weather != "Clear" else None)

# --- UI Grid ---
col1, col2 = st.columns([1, 1.2])

with col1:
    st.subheader("📋 Shipment Manifest")
    st.dataframe(pd.DataFrame(st.session_state.cargo_data), width='stretch', height=250)
    
    if st.session_state.results:
        st.subheader("✅ AI Flight Assignments")
        st.table(pd.DataFrame(st.session_state.results)[['ID', 'Aircraft', 'Origin', 'Dest']])

with col2:
    st.subheader("🌐 Global Traffic Visualization")
    fig = go.Figure()
    
    lats, lons, names = [], [], []
    for k, v in CITIES.items():
        lats.append(v['lat']); lons.append(v['lon']); names.append(k)
    
    fig.add_trace(go.Scattergeo(
        lat=lats, lon=lons, text=names, mode='markers+text',
        marker=dict(size=10, color='#FFCC00', symbol='diamond'),
        textfont=dict(color="white", size=8)
    ))

    COLOR_MAP = {"Medical": "#FF3131", "Perishable": "#FFFF33", "General": "#39FF14"}
    for f in st.session_state.results:
        f_color = COLOR_MAP.get(f.get("Type"), "#00FFFF")
        fig.add_trace(go.Scattergeo(
            lat=[f['lat_start'], f['lat_end']], lon=[f['lon_start'], f['lon_end']],
            mode='lines+markers', line=dict(width=2.5, color=f_color),
            marker=dict(size=6, color="white", symbol="triangle-right"),
            text=f"✈️ {f['Aircraft']} ({f['Type']})", hoverinfo='text', showlegend=False
        ))

    fig.update_layout(
        margin=dict(l=0, r=0, t=0, b=0), height=550,
        geo=dict(
            projection_type="natural earth", showland=True, landcolor="#1e2130",
            showocean=True, oceancolor="#0e1117", bgcolor="rgba(0,0,0,0)",
            showcountries=True, countrycolor="#444"
        )
    )
    st.plotly_chart(
        fig, 
        width='stretch', 
        config={
            'displayModeBar': True,  # ENABLE CONTROLS
            'scrollZoom': True,      # ENABLE MOUSE WHEEL ZOOM
            'modeBarButtonsToAdd': ['dragmode', 'pan2d', 'zoomIn2d', 'zoomOut2d']
        }
    )

if st.session_state.logs:
    with st.expander("🧠 Expert System Reasoning Logs (XAI)"):
        for log in st.session_state.logs: st.write(log)
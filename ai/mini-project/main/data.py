# data.py
CITIES = {
    "Pune": {"lat": 18.5204, "lon": 73.8567},
    "Dubai (DXB)": {"lat": 25.2048, "lon": 55.2708},
    "Singapore (SIN)": {"lat": 1.3521, "lon": 103.8198},
    "Tokyo (NRT)": {"lat": 35.7767, "lon": 140.3189},
    "Anchorage (ANC)": {"lat": 61.1743, "lon": -149.9963},
    "Frankfurt (FRA)": {"lat": 50.0379, "lon": 8.5622},
    "London (LHR)": {"lat": 51.4700, "lon": -0.4543},
    "New York (JFK)": {"lat": 40.6413, "lon": -73.7781},
    "Los Angeles (LAX)": {"lat": 33.9416, "lon": -118.4085},
}

# BUFFED FLEET: Major Hubs now have 15,000kg+ capacity
FLEET = [
    {"id": "SKY-PUNE-1", "type": "Freighter", "capacity": 8000, "status": "Ready", "location": "Pune"},
    {"id": "DESERT-HEAVY-777", "type": "Freighter", "capacity": 15000, "status": "Ready", "location": "Dubai (DXB)"},
    {"id": "LION-CITY-A330", "type": "Passenger", "capacity": 10000, "status": "Ready", "location": "Singapore (SIN)"},
    {"id": "SHOGUN-747-F", "type": "Freighter", "capacity": 20000, "status": "Ready", "location": "Tokyo (NRT)"}, # Buffed!
    {"id": "POLAR-747-MAX", "type": "Freighter", "capacity": 25000, "status": "Ready", "location": "Anchorage (ANC)"}, # Buffed!
    {"id": "LUFT-CARGO-FRA", "type": "Freighter", "capacity": 12000, "status": "Ready", "location": "Frankfurt (FRA)"},
    {"id": "BRITISH-A350", "type": "Passenger", "capacity": 10000, "status": "Ready", "location": "London (LHR)"},
    {"id": "EMPIRE-767", "type": "Freighter", "capacity": 8000, "status": "Ready", "location": "New York (JFK)"},
    {"id": "COAST-A321", "type": "Passenger", "capacity": 8000, "status": "Ready", "location": "Los Angeles (LAX)"},
]

INITIAL_CARGO = [
    {"id": "C001", "type": "Medical", "weight": 400, "origin": "Pune", "destination": "Dubai (DXB)", "priority": 1},
]
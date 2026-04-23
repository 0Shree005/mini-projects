# engine.py
from data import CITIES

def run_expert_system(fleet, cargo_list, weather_condition):
    decisions = []
    logs = []
    sorted_cargo = sorted(cargo_list, key=lambda x: x['priority'])

    for cargo in sorted_cargo:
        # --- RULE 1: Severe Weather Protocol (STORMY) ---
        if weather_condition == "Stormy" and cargo['type'] != "Medical":
            logs.append(f"🛑 **DENIED**: {cargo['id']} grounded. Stormy weather only permits Medical emergencies.")
            continue

        best_plane = None
        for plane in fleet:
            # --- RULE 2: Maintenance/Location Check ---
            if plane['status'] != "Ready" or plane['location'] != cargo['origin']:
                continue

            # --- RULE 3: Aviation Safety Protocol (CLOUDY) ---
            # Rule: Passenger flights grounded in Cloudy weather for safety; Freighters only.
            if weather_condition == "Cloudy" and plane['type'] == "Passenger":
                continue

            # --- RULE 4: Capacity Validation ---
            if plane['capacity'] >= cargo['weight']:
                # Heuristic: Match optimization
                if best_plane is None:
                    best_plane = plane
                elif plane['type'] == "Passenger" and cargo['weight'] <= 500:
                    best_plane = plane

        if best_plane:
            decision = {
                "ID": cargo['id'], "Type": cargo['type'],
                "Aircraft": best_plane['id'], "A-Type": best_plane['type'],
                "Origin": cargo['origin'], "Dest": cargo['destination'],
                "lat_start": CITIES[cargo['origin']]["lat"],
                "lon_start": CITIES[cargo['origin']]["lon"],
                "lat_end": CITIES[cargo['destination']]["lat"],
                "lon_end": CITIES[cargo['destination']]["lon"]
            }
            decisions.append(decision)
            best_plane['capacity'] -= cargo['weight']
            logs.append(f"✅ **ASSIGNED**: {cargo['id']} to {best_plane['id']} ({best_plane['type']})")
        else:
            # Logic for why it failed
            reason = "No plane at location"
            if weather_condition == "Cloudy":
                reason = "All passenger planes grounded / Capacity limit"
            logs.append(f"⚠️ **FAILED**: {cargo['id']} - {reason}")
            
    return decisions, logs
#!/usr/bin/env python3
"""
csv_to_kml.py — WiFi Security Survey Map Generator
Lab 11 — Operation AIRWAVE | Universidad Anahuac Mayab

Converts an airodump-ng .kismet.netxml capture file into a color-coded
KML map suitable for import into Google My Maps or Google Earth.

Usage:
    python3 csv_to_kml.py <capture.kismet.netxml> [output.kml]

Color coding (CRITICAL → MINIMAL):
    Red    — Open / WEP          (CRITICAL)
    Orange — WPA / TKIP          (HIGH)
    Yellow — WPA2 / TKIP         (MEDIUM)
    Green  — WPA2 / CCMP         (LOW)
    Cyan   — WPA3                (MINIMAL)
    Grey   — Unknown             (UNKNOWN)

KML output can be imported at: maps.google.com → My Maps → Create Map → Import
"""

import sys
import xml.etree.ElementTree as ET
from pathlib import Path

# KML colors use AABBGGRR hex format (alpha, blue, green, red)
RISK_COLORS = {
    "CRITICAL": "ff0000ff",   # Red
    "HIGH":     "ff0055ff",   # Orange
    "MEDIUM":   "ff00ffff",   # Yellow
    "LOW":      "ff00c800",   # Green
    "MINIMAL":  "ffffe000",   # Cyan
    "UNKNOWN":  "ff888888",   # Grey
}

RISK_ORDER = ["CRITICAL", "HIGH", "MEDIUM", "LOW", "MINIMAL", "UNKNOWN"]


def classify(enc_str: str) -> tuple[str, str]:
    """Return (risk_level, human_readable_label) for a given encryption string."""
    enc = enc_str.upper().strip() if enc_str else ""

    if not enc or enc in ("OPN", "OPEN", "NONE", ""):
        return "CRITICAL", "Open (No Encryption)"
    if "WEP" in enc and "WPA" not in enc:
        return "CRITICAL", "WEP"
    if "WPA3" in enc or "SAE" in enc:
        return "MINIMAL", "WPA3"
    if "WPA2" in enc or "RSN" in enc:
        if "TKIP" in enc and "CCMP" not in enc:
            return "MEDIUM", "WPA2 / TKIP"
        return "LOW", "WPA2 / CCMP"
    if "WPA" in enc:
        return "HIGH", "WPA / TKIP"

    return "UNKNOWN", enc_str or "Unknown"


def anonymize_bssid(bssid: str) -> str:
    parts = bssid.strip().split(":")
    if len(parts) == 6:
        return ":".join(parts[:3]) + ":XX:XX:XX"
    return bssid


def anonymize_ssid(ssid: str, visible: int = 4) -> str:
    if not ssid or ssid.strip() == "":
        return "<hidden>"
    ssid = ssid.strip()
    if len(ssid) <= visible:
        return ssid
    return ssid[:visible] + "*" * min(len(ssid) - visible, 6)


def parse_netxml(path: Path) -> list[dict]:
    """Parse a .kismet.netxml file and return a list of network dicts."""
    try:
        tree = ET.parse(path)
    except ET.ParseError as e:
        sys.exit(f"XML parse error in {path}: {e}")

    networks = []
    for net in tree.getroot().findall("wireless-network"):
        if net.get("type") != "infrastructure":
            continue

        bssid = (net.findtext("BSSID") or "").strip()
        channel = (net.findtext("channel") or "?").strip()
        manuf = (net.findtext("manuf") or "Unknown").strip()

        ssid, enc = "", ""
        ssid_el = net.find("SSID")
        if ssid_el is not None:
            raw = ssid_el.findtext("ssid") or ""
            ssid = raw.strip()
            enc = (ssid_el.findtext("encryption") or "").strip()

        signal = "?"
        snr = net.find("snr-info")
        if snr is not None:
            signal = (snr.findtext("last_signal_dbm") or "?").strip()

        lat = lon = None
        gps = net.find("gps-info")
        if gps is not None:
            try:
                lat = float(gps.findtext("peak-lat") or "")
                lon = float(gps.findtext("peak-lon") or "")
            except (ValueError, TypeError):
                pass

        if lat is None or lon is None:
            continue  # skip networks without GPS fix

        risk, label = classify(enc)
        networks.append({
            "bssid":   anonymize_bssid(bssid),
            "ssid":    anonymize_ssid(ssid),
            "channel": channel,
            "enc":     label,
            "risk":    risk,
            "signal":  signal,
            "vendor":  manuf,
            "lat":     lat,
            "lon":     lon,
        })

    return networks


def kml_styles() -> str:
    lines = []
    for risk, color in RISK_COLORS.items():
        lines.append(f"""  <Style id="s_{risk}">
    <IconStyle>
      <color>{color}</color>
      <scale>0.9</scale>
      <Icon><href>http://maps.google.com/mapfiles/kml/paddle/wht-circle.png</href></Icon>
    </IconStyle>
    <LabelStyle><color>{color}</color><scale>0.75</scale></LabelStyle>
    <BalloonStyle>
      <text><![CDATA[<b>$[name]</b><br/>$[description]]]></text>
    </BalloonStyle>
  </Style>""")
    return "\n".join(lines)


def kml_placemark(n: dict) -> str:
    desc = "\n".join([
        f"BSSID:    {n['bssid']}",
        f"Channel:  {n['channel']}",
        f"Signal:   {n['signal']} dBm",
        f"Enc:      {n['enc']}",
        f"Risk:     {n['risk']}",
        f"Vendor:   {n['vendor']}",
    ])
    return f"""  <Placemark>
    <name>{n['ssid']}</name>
    <description><![CDATA[<pre>{desc}</pre>]]></description>
    <styleUrl>#s_{n['risk']}</styleUrl>
    <Point><coordinates>{n['lon']},{n['lat']},0</coordinates></Point>
  </Placemark>"""


def build_kml(networks: list[dict]) -> str:
    stats = {r: 0 for r in RISK_ORDER}
    for n in networks:
        stats[n["risk"]] = stats.get(n["risk"], 0) + 1

    stats_lines = "\n".join(
        f"    {r}: {stats[r]}" for r in RISK_ORDER if stats[r] > 0
    )
    placemarks = "\n".join(kml_placemark(n) for n in networks)

    return f"""<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
<Document>
  <name>Operation AIRWAVE — WiFi Security Survey</name>
  <description><![CDATA[
  WiFi Security Survey — Lab 11
  Universidad Anahuac Mayab — Cybersecurity

  Networks mapped: {len(networks)}

  Risk breakdown:
{stats_lines}

  Color key:
    Red    = CRITICAL (Open / WEP)
    Orange = HIGH     (WPA/TKIP)
    Yellow = MEDIUM   (WPA2/TKIP)
    Green  = LOW      (WPA2/CCMP)
    Cyan   = MINIMAL  (WPA3)
  ]]></description>

{kml_styles()}

{placemarks}
</Document>
</kml>"""


def print_summary(networks: list[dict]) -> None:
    stats = {r: 0 for r in RISK_ORDER}
    for n in networks:
        stats[n["risk"]] = stats.get(n["risk"], 0) + 1

    total = len(networks)
    print(f"\n  Networks with GPS data : {total}", file=sys.stderr)
    print("  ─────────────────────────────────────", file=sys.stderr)
    for risk in RISK_ORDER:
        if stats[risk]:
            pct = stats[risk] / total * 100
            bar = "█" * stats[risk]
            print(f"  {risk:<10} {stats[risk]:>3}  ({pct:4.1f}%)  {bar}", file=sys.stderr)
    print("", file=sys.stderr)


def main():
    if len(sys.argv) < 2 or sys.argv[1] in ("-h", "--help"):
        print(__doc__)
        sys.exit(0)

    src = Path(sys.argv[1])
    if not src.exists():
        sys.exit(f"Error: file not found — {src}")

    dst = Path(sys.argv[2]) if len(sys.argv) > 2 else src.with_suffix(".kml")

    print(f"\n  Parsing : {src}", file=sys.stderr)
    networks = parse_netxml(src)

    if not networks:
        print(
            "\n  No networks with GPS coordinates found.\n"
            "  Did you run airodump-ng with --gpsd and get a GPS fix first?\n"
            "  Tip: run  cgps -s  to verify the fix before scanning.\n",
            file=sys.stderr,
        )
        sys.exit(1)

    print_summary(networks)

    dst.write_text(build_kml(networks), encoding="utf-8")
    print(f"  KML saved : {dst}", file=sys.stderr)
    print(f"\n  Import into Google My Maps:", file=sys.stderr)
    print(f"    maps.google.com → ☰ → Your Places → Maps → Create Map → Import\n", file=sys.stderr)


if __name__ == "__main__":
    main()

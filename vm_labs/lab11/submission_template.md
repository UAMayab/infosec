# Lab 11 — Operation AIRWAVE
## Wireless Security Assessment Report

| Field | Value |
|---|---|
| **Student** | [Full Name] — [Student ID] |
| **Date** | [Date of wardriving session] |
| **Duration** | [Start time] → [End time] |
| **Scan Area** | [General description — neighborhood/district, no specific addresses] |
| **OS** | [Kali Linux / ParrotOS — version] |

---

## Part 1: Setup & Environment Verification

### 1.1 Monitor Mode Support

**Interface name:** `[e.g. wlan0]`

**`sudo iw list` — Supported interface modes:**
```
[paste relevant section here]
```

**`sudo airmon-ng` output:**
```
[paste output here]
```

**Card chipset:** [e.g., Intel AX200, Realtek RTL8821CE]

**Monitor mode supported:** Yes / No

---

### 1.2 Tool Versions

```
[paste output of: airmon-ng --version, airodump-ng --version, wash --version, gpsd --version, python3 --version]
```

---

### 1.3 GPS Fix Confirmation

**GPS method used:** [Android + GPSd Forwarder / iOS GPS2IP / USB dongle / other]

**`cgps -s` output (showing confirmed fix):**
```
[paste cgps output showing latitude, longitude, satellites in view]
```

---

## Part 2: Wardriving Session Evidence

### 2.1 Monitor Mode Active

**`sudo airmon-ng start wlan0` output:**
```
[paste output here]
```

**Monitor interface confirmed:** `[e.g. wlan0mon]`

---

### 2.2 Capture Session

**Command used:**
```bash
[paste the exact airodump-ng command you ran]
```

**Scan route description:**
[Describe streets/area covered — no specific addresses. E.g., "5-block radius around downtown plaza, including 3 residential streets and 2 commercial blocks"]

**Approximate route map:** [Text description or embed image if using extended Markdown]

---

### 2.3 Session Files

**`ls -lh ~/wardriving/` output:**
```
[paste file listing here]
```

---

### 2.4 Raw CSV — First 20 Rows (anonymized)

```
[paste first 20 rows of your .csv file here, with BSSIDs anonymized and SSIDs truncated]
```

---

### 2.5 WPS Scan (`wash`) Output

```
[paste full wash output here]
```

**Networks with WPS enabled:** [count]

---

### 2.6 Session Cleanup

**`sudo airmon-ng stop wlan0mon` output:**
```
[paste here]
```

**WiFi reconnected successfully:** Yes / No

---

## Part 3: Data Analysis & Map

### 3.1 KML Generation

**`csv_to_kml.py` output:**
```
[paste the script's summary output here — risk breakdown table]
```

**KML file generated:** `[filename]`

---

### 3.2 Map

[Embed screenshot of your Google My Maps import, or describe the map layout]

> If embedding an image: `![WiFi Survey Map](map_screenshot.png)`
> If describing: provide a text table of approximate locations and risk clusters

**Notable clusters or patterns observed on the map:**

[Describe what geographic patterns you see — e.g., "three critical networks concentrated near the commercial strip on Calle 60", "WPA3 networks only appear in the newer apartment buildings"]

---

### 3.3 OUI Vendor Lookup — Top 5 Notable Networks

| # | BSSID (anon) | Vendor | Notable because... |
|---|---|---|---|
| 1 | | | |
| 2 | | | |
| 3 | | | |
| 4 | | | |
| 5 | | | |

---

## Part 4: Security Assessment Report

### 4.1 Executive Summary

[One page max. Cover: networks found, area, overall risk posture, top 2–3 findings, overall risk rating]

**Overall Risk Rating:** CRITICAL / HIGH / MEDIUM / LOW *(circle one)*

---

### 4.2 Methodology

| Parameter | Value |
|---|---|
| Hardware | [Laptop model, WiFi card, GPS method] |
| OS | [Kali/Parrot version] |
| Tools | [airmon-ng version, airodump-ng version, wash version] |
| Date & Time | [Date, start–end time] |
| Scan Duration | [Minutes] |
| Area Covered | [Description] |
| Limitations | [Any GPS gaps, card issues, restricted areas, etc.] |

---

### 4.3 Findings Table

*All SSIDs truncated to 4 chars + `****`. BSSIDs last 3 octets replaced with XX:XX:XX.*

| # | SSID | BSSID | Ch | Encryption | Cipher | Auth | Signal (dBm) | WPS | Risk |
|---|---|---|---|---|---|---|---|---|---|
| 1 | | | | | | | | | |
| 2 | | | | | | | | | |
| 3 | | | | | | | | | |
| ... | | | | | | | | | |

**Total networks:** [N]

---

### 4.4 Statistical Summary

| Protocol | Count | % of Total | Risk Level |
|---|---|---|---|
| WPA3 | | | MINIMAL |
| WPA2 / CCMP | | | LOW |
| WPA2 / CCMP + WPS | | | MEDIUM |
| WPA2 / TKIP | | | MEDIUM |
| WPA / TKIP | | | HIGH |
| WEP | | | CRITICAL |
| Open | | | CRITICAL |
| **TOTAL** | | **100%** | |

**Networks with WPS enabled:** [N] out of [total] ([%])

---

### 4.5 Notable Findings

*One dedicated section per CRITICAL or HIGH network.*

#### Finding 1 — [SSID truncated]: [Risk Level]

| Field | Detail |
|---|---|
| BSSID | AA:BB:CC:XX:XX:XX |
| Encryption | [e.g., WEP] |
| Risk | CRITICAL |
| Channel | |
| Signal | dBm |
| WPS | Yes / No |
| Vendor | |

**Analysis:** [Why is this risky? What could an attacker do? No exploitation — theoretical only.]

**Recommendation:** [Specific action for this network.]

---

*(Repeat for each CRITICAL and HIGH network)*

---

### 4.6 Recommendations

1. **[Title]:** [Specific, actionable recommendation]
2. **[Title]:** [Specific, actionable recommendation]
3. **[Title]:** [Specific, actionable recommendation]
4. **[Title]:** [Specific, actionable recommendation]
5. **[Title]:** [Specific, actionable recommendation]

---

### 4.7 Conclusion

[Your reflection on the real-world WiFi security landscape of your scan area. What surprised you? What patterns did you observe? Is the community using modern encryption? What is the trend you see?]

---

## Appendices

### Appendix A — Full Raw CSV (first 20 rows, anonymized)

```
[already included in Part 2.4 — reference here or paste again]
```

### Appendix B — WPS Scan Output

```
[already included in Part 2.5 — reference here or paste again]
```

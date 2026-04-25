# Universidad Anahuac Mayab
## Cybersecurity
### Lab 11 — Operation AIRWAVE
#### Wireless Security Assessment with the aircrack-ng Suite

---

- **Submission:** Individual — solo assignment
- **Format:** Single Markdown document — `lab11_[lastname].md`
- **Tools:** Your own Kali Linux or ParrotOS installation
- **Duration:** ~1–2 hour field session + report writing

---

## Legal Disclaimer

```
╔══════════════════════════════════════════════════════════════════════════╗
║                        LEGAL DISCLAIMER                                  ║
║                   FOR EDUCATIONAL PURPOSES ONLY                          ║
╠══════════════════════════════════════════════════════════════════════════╣
║                                                                          ║
║  This lab involves PASSIVE monitoring of radio spectrum only.            ║
║  You will NOT connect to any network. You will NOT attempt to crack      ║
║  any key. You will NOT inject packets. You will NOT capture any          ║
║  personal data.                                                          ║
║                                                                          ║
║  Passive WiFi scanning (listening without connecting) is legal in        ║
║  Mexico for educational purposes. Connecting to a network you do         ║
║  not own is ILLEGAL under LFPDPPP and federal telecom law.               ║
║                                                                          ║
║  You are an observer of public radio signals. Act accordingly.           ║
║                                                                          ║
╚══════════════════════════════════════════════════════════════════════════╝
```

---

## The Briefing — Operation AIRWAVE

*[Read this. It sets the context.]*

---

It is a quiet Friday afternoon. No clients. No ticket queue.

You open a terminal, check your WiFi card, and start thinking like a wireless security researcher. There is an entire city broadcasting its network infrastructure over open radio spectrum — SSID beacons flying through walls, encryption flags visible to anyone listening, WPS vulnerabilities hiding in plain signal.

No one is watching the airwaves. Except you.

Your mission: walk your neighborhood for one hour with your laptop. Collect everything that broadcasts. Identify what is exposed, what is weak, what is dangerously misconfigured. Then write a professional wireless security assessment that a real client could act on.

```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  FIELD ASSIGNMENT — PASSIVE WIRELESS SURVEY
  TYPE    : Solo
  TOOLS   : aircrack-ng suite, gpsd, wash
  METHOD  : Passive spectrum analysis — NO connections, NO attacks
  PRODUCT : Professional wireless security assessment report
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

This is wardriving — a technique used by security researchers, red teams, and compliance auditors worldwide to baseline the wireless security posture of a target area. Today, the target area is your neighborhood.

---

## Learning Objectives

By completing this lab you will demonstrate the ability to:

1. Configure a WiFi adapter in monitor mode on Kali Linux / ParrotOS
2. Use `airmon-ng` and `airodump-ng` to passively capture WiFi beacon data
3. Integrate GPS telemetry into a wireless scan session using `gpsd`
4. Use `wash` to identify WPS-enabled access points
5. Apply a standardized risk framework to classify network security posture
6. Convert raw scan data into a georeferenced KML map
7. Produce a professional wireless security assessment report

---

## Required Hardware & Software

### Your Laptop

This lab runs directly on your **Kali Linux or ParrotOS installation** — no VM required. You will use your laptop's built-in WiFi adapter for monitoring.

> **Before lab day:** Verify your internal WiFi card supports monitor mode (see Part 1.1). Most modern cards on Kali/ParrotOS work. If yours does not, an external USB WiFi adapter (e.g., Alfa AWUS036ACH or AWUS036NHA) will work as a drop-in replacement — plug it in and it will appear as a separate wireless interface.

### GPS Source

You need a GPS signal source. The recommended method uses your **smartphone**:

| Method | What You Need |
|---|---|
| **Android (recommended)** | "GPSd Forwarder" app + USB cable |
| iOS | "GPS2IP Lite" app + USB cable or Wi-Fi |
| USB GPS dongle | Plug-and-play, `gpsd /dev/ttyUSB0` |

The Android + USB tethering approach is covered step-by-step in Part 1.3.

### Software (pre-installed on Kali/ParrotOS)

Verify these are available:

```bash
which airmon-ng airodump-ng wash gpsd cgps python3
```

If any are missing:

```bash
sudo apt update && sudo apt install aircrack-ng gpsd gpsd-clients python3
```

---

## Risk Classification Matrix

You will apply this matrix to every network you discover. The goal is objective, reproducible risk scoring.

| Protocol / Configuration | Risk Level | Rationale |
|---|---|---|
| Open (no encryption) | **CRITICAL** | All traffic is readable by anyone in range |
| WEP | **CRITICAL** | Cracked in minutes; effectively no encryption |
| WPA / TKIP (WPA1) | **HIGH** | Deprecated; TKIP has known cryptographic weaknesses |
| WPA2 / TKIP | **MEDIUM** | WPA2 key exchange is strong but TKIP cipher is weak |
| WPA2 / CCMP (AES) | **LOW** | Acceptable for most use cases |
| WPA2-Enterprise (802.1X) | **LOW** | Certificate-based auth; much harder to attack |
| WPA3 | **MINIMAL** | Current best practice; SAE handshake is resistant to PMKID attacks |
| WPS enabled (any protocol) | **+1 risk level** | WPS PIN can be brute-forced; Pixie Dust attack on WPS 1.0 |
| Hidden SSID | **Note only** | Minimal security value; SSID is recoverable |

> **Important:** WPS is an additive risk modifier. A `WPA2/CCMP + WPS enabled` network is elevated from **LOW** to **MEDIUM**.

---

## Part 1: Setup & Environment Verification — (15 pts)

### 1.1 Verify Monitor Mode Support (5 pts)

Before going into the field, confirm your WiFi card can enter monitor mode:

```bash
# List your wireless interfaces
ip link show | grep wlan

# Check supported interface modes for each wireless card
sudo iw list | grep -A 15 "Supported interface modes"
```

Look for `* monitor` in the output. If it is listed, you are good to go.

```bash
# Also check with airmon-ng — it shows all wireless cards and potential issues
sudo airmon-ng
```

Document in your submission:
- Your WiFi interface name (e.g., `wlan0`)
- Your card chipset (from `airmon-ng` output)
- Confirmation that monitor mode is listed as supported

If your internal card does **not** support monitor mode, attach an external USB WiFi adapter and repeat this step with the new interface.

---

### 1.2 Install and Verify All Tools (5 pts)

Run the following and capture the output for your submission:

```bash
# Verify aircrack-ng suite
airmon-ng --version
airodump-ng --version

# Verify wash (WPS scanner)
wash --version

# Verify GPS stack
gpsd --version
cgps --version

# Verify Python3 (for KML conversion)
python3 --version
```

---

### 1.3 Configure GPS (5 pts)

**Recommended: Android smartphone via USB tethering**

1. On your Android phone, install **GPSd Forwarder** (free, Google Play)
2. Open the app → note the TCP port (default: `29998`)
3. On your phone: Settings → Network → **USB Tethering** (enable it)
4. On your laptop, a new interface appears (`usb0` or similar):

```bash
ip addr show   # find the new usb interface and its assigned IP
```

5. Find your phone's IP on the USB tethering network (usually `192.168.42.129`):

```bash
ip route show dev usb0
```

6. Start `gpsd` pointing to your phone:

```bash
sudo gpsd tcp://192.168.42.129:29998 -F /var/run/gpsd.sock
```

7. Verify you have a GPS fix:

```bash
cgps -s
```

You should see latitude, longitude, altitude, and satellites in view updating in real time. Wait for a proper fix before heading out — this can take 30–60 seconds outdoors.

> **iOS alternative:** Use "GPS2IP Lite" and connect over the same WiFi network (note: your WiFi card cannot be in monitor mode while also connected to WiFi — use a hotspot from your phone for this).
>
> **USB GPS dongle alternative:** `sudo gpsd /dev/ttyUSB0 -F /var/run/gpsd.sock` (plug and play).

**In your submission:** Paste the `cgps -s` output showing a confirmed GPS fix.

---

## Part 2: The Wardriving Session — (25 pts)

### Pre-Session Checklist

Before leaving:

- [ ] GPS fix confirmed (`cgps -s` shows coordinates)
- [ ] WiFi card supports monitor mode
- [ ] All tools installed and verified
- [ ] Laptop charged or power bank packed
- [ ] Terminal open and ready

---

### 2.1 Enable Monitor Mode (5 pts)

Kill processes that interfere with monitor mode, then enable it:

```bash
# Kill NetworkManager, wpa_supplicant, dhclient
sudo airmon-ng check kill

# Put your card in monitor mode
sudo airmon-ng start wlan0
```

> **Note:** Replace `wlan0` with your actual interface name. After running this command, your interface will be renamed — usually `wlan0mon`. Verify:

```bash
ip link show
sudo airmon-ng      # shows current state
```

Paste the output confirming your interface is now in monitor mode.

---

### 2.2 Launch the Wardriving Capture (10 pts)

Create a working directory and start the capture session with GPS integration:

```bash
mkdir -p ~/wardriving
cd ~/wardriving

sudo airodump-ng wlan0mon \
    --gpsd \
    --output-format csv,netxml \
    -w session_$(date +%Y%m%d_%H%M%S)
```

**Explanation of flags:**

| Flag | Purpose |
|---|---|
| `wlan0mon` | Your monitor mode interface |
| `--gpsd` | Read GPS from gpsd socket and tag each network with coordinates |
| `--output-format csv,netxml` | Save in CSV (for analysis) and Kismet XML (for KML map) |
| `-w session_YYYYMMDD_HHMMSS` | Output file prefix (timestamp auto-appended) |

**Leave this terminal running.** Walk your area for at least **45 minutes**, covering as many streets and buildings as possible. The more ground you cover, the richer your findings.

Minimum target: **15 unique networks**. Document your route (street names or landmarks) in your submission.

> **Scanning tips:**
> - Pause briefly near multi-unit buildings — they broadcast many SSIDs
> - Check both street level and near parking structures (signal bounces)
> - Note when the airodump-ng network count increases rapidly — you are in a dense area

---

### 2.3 WPS Scan (5 pts)

In a **second terminal** (leave airodump-ng running), scan for WPS-enabled access points:

```bash
sudo wash -i wlan0mon
```

`wash` will list networks that have WPS enabled, along with the WPS version (1.0 is particularly dangerous — vulnerable to the Pixie Dust attack, though you will not exploit it here).

Let this run for at least 5 minutes to capture the full WPS landscape.

Paste the full `wash` output in your submission.

---

### 2.4 End the Session and Restore WiFi (5 pts)

When you have finished your route:

1. Stop airodump-ng: `Ctrl+C`
2. Stop wash: `Ctrl+C`
3. Stop gpsd: `sudo killall gpsd`
4. Restore normal WiFi:

```bash
sudo airmon-ng stop wlan0mon
sudo systemctl start NetworkManager
```

5. Verify your WiFi reconnects normally to your home/campus network:

```bash
ip addr show wlan0
ping -c 3 8.8.8.8
```

List the files generated by your session:

```bash
ls -lh ~/wardriving/
```

You should see files like:
```
session_20260424_143022-01.csv
session_20260424_143022-01.kismet.netxml
```

Paste this file listing in your submission.

---

## Part 3: Data Analysis & Map Generation — (20 pts)

### 3.1 Parse the CSV Capture (5 pts)

The `.csv` file from airodump-ng contains your full network list. Review it:

```bash
# Count unique networks captured
head -3 ~/wardriving/session_*-01.csv   # Show header
grep -c ":" ~/wardriving/session_*-01.csv  # Count AP entries (approximate)

# View formatted (the file has two sections: APs and Clients)
cat ~/wardriving/session_*-01.csv
```

The CSV columns you care about:

| Column | Description |
|---|---|
| BSSID | MAC address of the access point |
| channel | WiFi channel |
| Privacy | Encryption type (OPN, WEP, WPA, WPA2, WPA3) |
| Cipher | CCMP, TKIP, WRAP, or blank |
| Authentication | PSK, MGT (Enterprise), SAE |
| Power | Signal strength in dBm |
| ESSID | Network name (SSID) |

Paste the first **20 rows** of your CSV in your submission (anonymized — see privacy note below).

---

### 3.2 Generate the KML Map (10 pts)

Use the provided Python script to convert your `.kismet.netxml` file to a color-coded KML map:

```bash
python3 ~/lab11/tools/csv_to_kml.py \
    ~/wardriving/session_*-01.kismet.netxml \
    ~/wardriving/wardriving_map.kml
```

The script color-codes each network by risk level:

| Color | Risk |
|---|---|
| Red | CRITICAL (Open, WEP) |
| Orange | HIGH (WPA/TKIP) |
| Yellow | MEDIUM (WPA2/TKIP) |
| Green | LOW (WPA2/CCMP) |
| Cyan | MINIMAL (WPA3) |

**Import the KML into Google My Maps:**

1. Go to [maps.google.com](https://maps.google.com) → `☰` → **Your Places** → **Maps** → **Create Map**
2. Click **Import** and upload your `wardriving_map.kml`
3. Take a screenshot of the resulting map

> **If gpsd was not working:** The `.kismet.netxml` will lack GPS coordinates. In this case, manually drop pins on Google Maps for each notable network location and include that map screenshot instead. Document the GPS failure and what caused it.

Include the map screenshot (as an image in your Markdown, or describe it clearly as a text-based table of coordinates) in your submission.

---

### 3.3 OUI Vendor Lookup (5 pts)

The first three octets of a BSSID (the OUI — Organizationally Unique Identifier) identify the hardware manufacturer. Knowing the vendor helps profile the AP.

```bash
# Look up a BSSID vendor
python3 -c "
import urllib.request, json
bssid = 'AA:BB:CC'   # Replace with first 3 octets of a BSSID
url = f'https://api.maclookup.app/v2/macs/{bssid}'
try:
    with urllib.request.urlopen(url) as r:
        print(json.load(r))
except:
    print('Lookup failed — check internet connection or try macvendors.com')
"
```

Or manually look up at [macvendors.com](https://macvendors.com).

For your **five most interesting networks** (notable risk findings), document the vendor. This tells you if the AP is a consumer router (TP-Link, Netgear), enterprise gear (Cisco, Aruba), or an IoT device — which affects your risk recommendation.

---

## Part 4: Security Assessment Report — (40 pts)

This is the main deliverable. Your report must be written as if you are delivering it to a real client. Use the sections below as your required structure.

---

### 4.1 Executive Summary (5 pts)

One page maximum. Non-technical. Answer:
- How many networks were found and in what area?
- What was the overall security posture? (distribution of risk levels)
- What are the top 2–3 findings the client must act on immediately?
- What is the overall risk rating: **CRITICAL / HIGH / MEDIUM / LOW**?

---

### 4.2 Methodology (5 pts)

Document:
- Hardware used (laptop model, WiFi card, GPS method)
- Software versions (airmon-ng, airodump-ng, wash, OS version)
- Scan date, time, and duration
- Geographic area covered (neighborhood/district — no specific home addresses)
- Any limitations encountered (GPS gaps, card limitations, etc.)

---

### 4.3 Findings Table (15 pts)

A table of **every network discovered**, with these columns:

| # | SSID (anonymized) | BSSID (anonymized) | Ch | Encryption | Cipher | Auth | Signal (dBm) | WPS | Risk |
|---|---|---|---|---|---|---|---|---|---|
| 1 | HomeNet*** | AA:BB:CC:XX:XX:XX | 6 | WPA2 | CCMP | PSK | -72 | No | LOW |
| 2 | Off****** | DD:EE:FF:XX:XX:XX | 11 | OPN | | | -58 | No | CRITICAL |

**Privacy rules (mandatory):**
- SSID: show first 4 characters only, replace rest with `*` (e.g., `Home****`)
- BSSID: replace last 3 octets with `XX:XX:XX`
- Do not include GPS coordinates in this table

Apply the Risk Classification Matrix from the intro. The Risk column must be consistent with the matrix — graders will verify this.

---

### 4.4 Statistical Summary (5 pts)

A table summarizing your findings by protocol:

| Protocol | Count | % of Total | Risk Level |
|---|---|---|---|
| WPA2 / CCMP | 18 | 60% | LOW |
| WPA2 / TKIP | 4 | 13% | MEDIUM |
| WPA3 | 3 | 10% | MINIMAL |
| WPA / TKIP | 2 | 7% | HIGH |
| WEP | 2 | 7% | CRITICAL |
| Open | 1 | 3% | CRITICAL |
| **TOTAL** | **30** | **100%** | |

Also include: How many networks had WPS enabled?

---

### 4.5 Notable Findings (5 pts)

For each network with **CRITICAL** or **HIGH** risk, write a dedicated paragraph:

- What makes it risky
- What an attacker could do with it (no exploitation — theoretical analysis only)
- Specific recommendation for that network

Minimum: document every CRITICAL and HIGH network individually.

---

### 4.6 Risk Map (included in 4.3 score)

Include your color-coded map (screenshot or link to Google My Maps). The map should be visible and show geographic distribution of risk — clusters of red/orange are a story worth telling.

---

### 4.7 Recommendations (5 pts)

Write a minimum of **5 actionable recommendations** a homeowner or small business owner could implement. Make them specific and practical — not generic advice.

**Bad:** "Use strong encryption."
**Good:** "Disable WPS on all access points — the router admin panel (usually at 192.168.1.1) has a WPS toggle under Wireless Settings. WPS is not needed for modern devices."

---

### 4.8 Conclusion (paragraph)

Reflect on what the data tells you about the real-world WiFi security landscape of your area. What surprised you? What patterns emerged? Is the situation improving or stagnant compared to what you would expect?

---

## Privacy Requirements

The following anonymization rules are **mandatory** for submission:

- **SSID:** Show only the first 4 characters, replace the rest with `*`
- **BSSID:** Replace the last 3 octets with `XX:XX:XX` (e.g., `00:14:22:XX:XX:XX`)
- **Location:** Describe your scan area in general terms (e.g., "residential neighborhood in Mérida, 3-block radius") — no specific addresses
- **GPS coordinates:** Do not include precise coordinates that could identify individual homes

---

## Submission Requirements

Submit a single Markdown file named:

```
lab11_[lastname].md
```

Using `submission_template.md` as your starting structure. Your document must contain:

- [ ] Part 1: Monitor mode evidence + tool versions + GPS fix screenshot
- [ ] Part 2: Wardriving session evidence (CSV first 20 rows, wash output, file listing)
- [ ] Part 3: KML generation output + map screenshot/description
- [ ] Part 4: Full report (all required sections)
- [ ] Minimum 15 unique networks in findings table
- [ ] Risk matrix correctly applied to every network
- [ ] All SSID/BSSID anonymized per privacy rules

---

## A Note on Wardriving History & Ethics

Wardriving was popularized in the early 2000s by researchers with laptops and directional antennas driving highways. The [WiGLE project](https://wigle.net) has crowdsourced over 1 billion WiFi network locations worldwide since 2001 — it is the world's largest wardriving database and a fascinating dataset.

The legal landscape has evolved. In the US, the CFAA was applied to Google's Street View WiFi collection (they collected payload data — a clear line they crossed). Passive beacon collection of publicly-broadcast SSIDs has been consistently treated as legal in most jurisdictions, including Mexico.

The line is clear: **listening is legal. Connecting without authorization is not.**

Skilled defenders wardrive their own environments regularly. Knowing your wireless footprint — what you broadcast, what your neighbors can see, whether your guest network is properly isolated — is basic security hygiene. You are doing legitimate security research today.

---

## Pro Tips

1. **Kill processes before monitor mode.** If NetworkManager is not killed first, it will keep stealing your card back from monitor mode every few minutes.

2. **Check your channel hopping.** By default, airodump-ng hops across all channels. If you want to focus on 5 GHz networks, add `--band a`. For both: `--band abg`.

3. **Signal strength matters.** A `-40 dBm` signal is very close (same room). A `-85 dBm` signal is at the edge of detection. Document unusual signal outliers — a very strong signal in an outdoor scan suggests a powerful AP.

4. **Hidden SSIDs appear as `<length: N>`.** These are not truly hidden — the length reveals the SSID length, and clients probing for the network reveal the full SSID. Note these in your report.

5. **WPS version 1.0 in wash output = flag it.** You are not cracking it, but your report should note it is vulnerable to Pixie Dust.

6. **`airodump-ng` scrolls — the `--write` flag is essential.** You cannot scroll back in the terminal view. Your file output is your record. Always use `-w`.

7. **Cold GPS fix takes time.** If `cgps` shows no satellites, go outside for 60 seconds. Do not start your capture until you have a fix — networks logged before GPS lock will have no coordinates.

---

*"The sky is not the limit — the spectrum is."*

---

**Good luck out there. The airwaves are waiting.**

*— Happy Hacking!*

# Lab 6: Malware Triage with VirusTotal
### Hash Hunt — Identify Malicious Files Like a Blue Teamer

---

## The Day Everything In The Inbox Was A Lie

It was a Tuesday morning in **Lagos** when **Chisom Okafor** got the call.

She was the only SOC analyst on shift at a mid-size fintech company. The call was from a nervous IT manager: three employees had received emails with attachments. Two had opened them. The third had forwarded it to the entire accounting department "because it looked like a real invoice."

Chisom did not have time to spin up a sandbox. She did not have a malware analysis lab. What she had was a workstation, internet access, and fifteen file hashes that her endpoint agent had logged before IT yanked the machines off the network.

She opened a browser tab.

`virustotal.com`

In the next twenty minutes, she triaged all fifteen files. Eight were clean. Six were malicious — two distinct malware families, suggesting a targeted dual-payload campaign. One hash returned no results at all, which was almost more concerning: a custom, possibly novel sample that hadn't been seen before.

She had a written triage report on the CISO's desk before the second cup of coffee was cold.

No sandbox. No reverse engineering. No budget. Just hashes and the world's largest malware crowd-intelligence platform.

That is what you are learning today.

---

## Lab Overview

| Item | Detail |
|---|---|
| **Type** | Individual Assignment |
| **Duration** | 30–45 minutes |
| **Platform** | Any OS — browser only |
| **Tool** | VirusTotal (https://www.virustotal.com) |
| **Account Required** | Free account recommended (history + comments) |
| **Goal** | Triage 10 file hashes and produce a written incident report |

> No VM needed for this lab. No file downloads. Hashes are safe to look up — you are querying metadata, not executing anything.

---

## Background: What Is VirusTotal?

VirusTotal is a free online service owned by Google that aggregates results from **70+ antivirus engines** and dozens of website scanners. When you submit a file hash, URL, domain, or IP address, VT checks it against every engine simultaneously and returns:

- **Detection ratio** — how many engines flag it (e.g., `58/72`)
- **Malware family names** — what each engine calls it
- **File metadata** — type, size, first seen date, PE headers
- **Behavioral data** — sandbox execution reports (dynamic analysis)
- **Community comments** — threat intel notes from researchers worldwide
- **Graph relationships** — files that dropped this file, C2 domains, related samples

### Why Hash Lookup Specifically?

A **cryptographic hash** (MD5, SHA-1, SHA-256) is a file's digital fingerprint. Two files with the same content always produce the same hash. Two files that differ by even one byte produce completely different hashes.

When you submit a hash to VT, you are asking: *"Has anyone ever uploaded a file with this fingerprint before? And if so, what did the engines say about it?"*

This is powerful because:
- You never need to touch the actual file
- Lookup is instant — no upload, no scanning delay
- Hash lookup is safe even for active malware samples
- If the hash is in VT's database, you get the same report as if you had uploaded the file yourself

```
File on Disk  →  sha256sum file.exe  →  Hash  →  VT Lookup  →  Triage Report
              (never executed,            (safe)
               never sent anywhere)
```

---

## How to Use VirusTotal for Hash Lookup

### Option A — Web Interface

1. Go to https://www.virustotal.com
2. Click the **Search** tab
3. Paste a hash (MD5, SHA-1, or SHA-256) into the search box
4. Press Enter

### Option B — Direct URL

```
https://www.virustotal.com/gui/file/<SHA256_HASH>
```

### Option C — CLI (for the hacker in you)

If you have a VT API key (free account):

```bash
# Install the VT CLI
pip3 install vt-cli

# Lookup a hash
vt file <hash>

# Or using curl directly against the API v3
curl -s "https://www.virustotal.com/api/v3/files/<hash>" \
  -H "x-apikey: YOUR_API_KEY" | python3 -m json.tool | grep -E 'meaningful_name|type_description|last_analysis_stats'
```

> Free API key: register at virustotal.com → your account → API key. Free tier allows 500 lookups/day.

---

## Reading a VirusTotal Report

When a result is found, you will see several tabs. Know these:

### Detection Tab
- **Detection ratio** (e.g., `58/72`) — the headline number
- Engine names + what each one calls the file
- Green = clean, Red = malicious, Yellow = suspicious/PUA

### Details Tab
- File type, size, magic bytes
- **First submission date** — when was this first seen in VT?
- **Last analysis date** — when was it last scanned?
- PE metadata (for Windows executables): compiler, sections, imports

### Relations Tab
- Files that dropped this file (parent samples)
- Files this file drops (children)
- Network indicators: domains/IPs contacted in sandbox

### Behavior Tab
- Dynamic sandbox execution results
- Registry changes, file writes, network connections made
- MITRE ATT&CK technique tags

### Community Tab
- Notes from security researchers
- Often the most useful for context on sophisticated samples

---

## Understanding Verdicts

Not every result is black and white. Learn to interpret these scenarios:

| Scenario | What It Means |
|---|---|
| `0/72` — Hash found, 0 detections | Known clean file, or very new/unknown |
| `58/72` — High detection | Confirmed malicious — widely known malware |
| `3/72` — Low detection | Suspicious — possibly new variant, check details |
| `Not found` — Hash not in database | File has never been submitted to VT; treat as unknown/suspicious |
| `72/72` detects as "EICAR-Test-File" | AV test string — NOT real malware, used to verify AV is working |
| Detected by only obscure/low-rep engines | Possible false positive — check community notes |

> **Key concept — False Positive vs. False Negative:**
> A **false positive** is a clean file flagged as malicious. A **false negative** is malware that evades all engines. Both are real. Low detection count does not mean clean; high detection count does not mean the file is dangerous in your specific context. A triage analyst reads the whole picture.

---

## The Hash Hunt — Your 10 Samples

You are a junior analyst at a Managed Security Service Provider (MSSP). Your team has responded to a suspected compromise at a client company. The endpoint detection agent collected 10 file hashes before the machines were isolated.

Your job: triage each hash using VirusTotal and complete the **Analyst Triage Report** below.

> All hashes below are of publicly documented samples. You are querying metadata only — no files are downloaded or executed at any point.

---

### Sample List

| # | Filename (as found on disk) | Location Found | Hash Type | Hash Value |
|---|---|---|---|---|
| 1 | `eicar_test.com` | `C:\IT\av_test\` | SHA-256 | `275a021bbfb6489e54d471899f7db9d1663fc695ec2fe2a2c4538aabf651fd0f` |
| 2 | `mssecsvc.exe` | `C:\Windows\` | SHA-256 | `24d004a104d4d54034dbcffc2a4b19a11f39008a575aa614ea04703480b1022c` |
| 3 | `tasksche.exe` | `C:\Users\Public\` | MD5 | `84c82835a5d21bbcf75a61706d8ab549` |
| 4 | `perfc.dat` | `C:\Windows\` | MD5 | `027cc450ef5f8c5f653329641ec1fed9` |
| 5 | `bot.arm7` | `/tmp/` (Linux server) | MD5 | `5ff465afaabcbf0150d1a3ab2c2e74f3` |
| 6 | `invoice_Q4_2025.doc.exe` | `C:\Users\accounting\Downloads\` | SHA-256 | `a172b471eba69b5cfdc5f37c8d17cc36` |
| 7 | `libcrypto.so` | `/tmp/.hidden/` (Linux server) | SHA-256 | `e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855` |
| 8 | `putty.exe` | `C:\Users\sysadmin\Desktop\` | SHA-256 | `1f63816c96a9bde1a7e4f9a6395a3b7614463ff16f82e5ec22beafba6c6d9b5b` |
| 9 | `chrome_update.exe` | `C:\Users\finance01\AppData\Temp\` | SHA-256 | `[INSTRUCTOR HASH — see instructor key]` |
| 10 | `backup_sync.ps1` | `C:\ProgramData\Microsoft\` | SHA-256 | `[INSTRUCTOR HASH — see instructor key]` |

> **Samples 9 and 10** are populated by your instructor with current samples sourced from [MalwareBazaar](https://bazaar.abuse.ch/) — a free, community-run malware repository used by SOC teams worldwide. This keeps the assignment fresh and exposes you to real current threat actor activity.

---

## Your Deliverable — Analyst Triage Report

Complete the table below for each sample. Then answer the discussion questions.

### Part 1 — Hash Triage Table

For each sample, look up the hash in VirusTotal and fill in every column. If a hash is **not found** in VT, write `NOT IN DATABASE` in the Verdict column and note it in your analysis.

| # | Filename | Detection Ratio | Verdict | Malware Family | Threat Category | First Seen in VT | File Type |
|---|---|---|---|---|---|---|---|
| 1 | eicar_test.com | | | | | | |
| 2 | mssecsvc.exe | | | | | | |
| 3 | tasksche.exe | | | | | | |
| 4 | perfc.dat | | | | | | |
| 5 | bot.arm7 | | | | | | |
| 6 | invoice_Q4_2025.doc.exe | | | | | | |
| 7 | libcrypto.so | | | | | | |
| 8 | putty.exe | | | | | | |
| 9 | chrome_update.exe | | | | | | |
| 10 | backup_sync.ps1 | | | | | | |

**Verdict options:** `MALICIOUS` / `CLEAN` / `SUSPICIOUS` / `TEST FILE` / `NOT IN DATABASE`

**Threat categories:** `Ransomware` / `Trojan` / `Worm` / `Backdoor` / `Stealer` / `Dropper` / `Botnet` / `RAT` / `Wiper` / `PUA` / `N/A`

---

### Part 2 — Per-Sample Analysis

For each sample marked **MALICIOUS** or **SUSPICIOUS**, write a 2–3 sentence analysis covering:

1. What does this malware do? (based on VT's Details, Behavior, and Community tabs)
2. Why is its location on disk suspicious or consistent with that malware family's behavior?
3. What immediate containment action would you recommend?

> You do not need to write an analysis for CLEAN or TEST FILE verdicts — just note why you reached that conclusion.

---

### Part 3 — Incident Summary

Write a short paragraph (5–8 sentences) addressed to a non-technical IT manager summarizing:

- How many of the 10 files are malicious
- Which malware families were identified
- Whether this looks like a targeted attack or opportunistic infection (and why)
- What the most urgent containment step is
- Whether any findings are inconclusive and why

---

## Discussion Questions

Answer each question in 3–5 sentences.

**1. The EICAR test file (Sample 1) triggers 50+ antivirus engines. Does this mean it is dangerous? Why do organizations keep EICAR files?**

**2. Sample 7 (`libcrypto.so`) returned a 0/72 detection ratio and you know why. What does the SHA-256 value `e3b0c44...` represent, and what should an analyst do when a file in `/tmp/.hidden/` returns 0 detections — declare it clean?**

**3. Sample 8 is PuTTY — a legitimate SSH client. How can a blue team analyst determine whether a clean-hashing tool like PuTTY is being abused for malicious purposes (a technique called "Living off the Land")? What tools or data sources beyond VT would you use?**

**4. Samples 2, 3, and 4 share something in common historically. Research each malware family. What was the real-world impact of these attacks and what made them unique compared to typical ransomware?**

**5. A file returns `NOT IN DATABASE` in VirusTotal. A junior analyst on your team says: "VT doesn't know about it, so it must be fine." What is wrong with this reasoning? What steps would you take next?**

---

## Bonus Challenge — CLI Triage Script

If you want to go further: write a Bash script that takes a text file of hashes (one per line) and queries the VirusTotal API v3 for each one, printing a one-line summary per hash.

```bash
# Expected usage:
./vt_triage.sh hashes.txt YOUR_API_KEY

# Expected output:
# [MALICIOUS  58/72] 24d004a1... WannaCry.Ransomware
# [CLEAN       0/72] e3b0c442... empty file
# [NOT FOUND      -] aabbccdd...
```

Your script should:
1. Accept a file of hashes and an API key as arguments
2. Query `https://www.virustotal.com/api/v3/files/<hash>` for each
3. Parse the JSON response to extract `last_analysis_stats` and `meaningful_name`
4. Color-code output: red for malicious, green for clean, yellow for not found
5. Handle API rate limiting (free tier: 4 requests/minute)

---

## Submission

Submit a **single PDF** containing:

1. Completed **Part 1 — Hash Triage Table**
2. **Part 2** analysis paragraphs for all malicious/suspicious samples
3. **Part 3** incident summary paragraph
4. Answers to all **5 Discussion Questions**
5. *(Optional)* Bonus script + sample output

---

## Key Concepts Checklist

Before submitting, confirm you can explain each of these:

- [ ] What a cryptographic hash is and why identical files always produce the same hash
- [ ] The difference between a false positive and a false negative in AV detection
- [ ] Why a `NOT IN DATABASE` result is not the same as "clean"
- [ ] What the EICAR test file is and why it exists
- [ ] What "first seen" date in VT tells an analyst (and what it does NOT tell them)
- [ ] At least two things you can learn from the VT **Behavior** tab that the **Detection** tab does not show

---

## References

- [VirusTotal](https://www.virustotal.com) — the tool itself
- [VirusTotal Documentation](https://docs.virustotal.com) — API reference and report field explanations
- [MalwareBazaar](https://bazaar.abuse.ch/) — free community malware repository (used by SOC teams)
- [ANY.RUN](https://any.run) — free interactive sandbox for behavioral analysis (complements VT)
- [MITRE ATT&CK Matrix](https://attack.mitre.org) — maps malware behavior to adversary techniques
- *The Practice of Network Security Monitoring* — Richard Bejtlich, Chapter 4 — threat intel triage workflow

---

> **Ethics Notice:** All hash lookups in this assignment query public metadata only.
> You are not downloading, executing, or distributing malware. VirusTotal is a legitimate
> professional tool used daily by SOC analysts, incident responders, and threat intelligence
> teams worldwide. Using these skills against systems or files you do not have authorization
> to analyze is **illegal and unethical**. Be a purple teamer — learn offense to build better defense.

---


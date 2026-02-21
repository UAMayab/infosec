# Universidad Anahuac Mayab
## Cybersecurity
### Midterm Exam #1
#### Penetration Testing: Full-Scope Recon & Vulnerability Assessment

---

- **Submission:** Teams of two (2) students
- **Format:** Single PDF document (see deliverables section)
- **Due Date:** March 2dn. Midnight, 2026.

---

## LEGAL DISCLAIMER

```
╔══════════════════════════════════════════════════════════════════════════╗
║                        LEGAL DISCLAIMER                                  ║
║                   FOR EDUCATIONAL PURPOSES ONLY                          ║
╠══════════════════════════════════════════════════════════════════════════╣
║                                                                          ║
║  All activities described in this exam are conducted EXCLUSIVELY for     ║
║  academic and educational purposes under a controlled environment.       ║
║                                                                          ║
║  This exam promotes the ethical application of cybersecurity skills.     ║
║  Students are expected to operate within the defined scope at all        ║
║  times. Violating scope boundaries will result in automatic ZERO         ║
║  and potential disciplinary action.                                      ║
║                                                                          ║
║  By beginning this exam, you acknowledge that you have read,             ║
║  understood, and agree to operate under these terms.                     ║
║                                                                          ║
╚══════════════════════════════════════════════════════════════════════════╝
```

---

## The Briefing: Operation PHANTOM NODE

*[Read this. It matters.]*

---

It's 02:17 AM. The city is asleep.

Your terminal glows in the dark. A secure message just dropped into your encrypted inbox — a PGP-signed message from a handle you recognize: **@WRAITH_ADMIN**. It's your handler from the firm.

```
-----BEGIN PGP SIGNED MESSAGE-----
Hash: SHA512

TO: Red Team Alpha — Cell 7
FROM: WRAITH_ADMIN
CLASSIFICATION: EYES ONLY

A mid-sized manufacturing company, NexusCorp, has retained our firm
for a full-scope penetration test. The board suspects their network
has already been compromised by a threat actor — possibly an
insider threat. They need to know: what does the outside world see?

You have a 72-hour window.
You have scope. You have authority.
What you don't have is time to waste.

Rules of engagement are attached. Stay inside the fence.
When you're done — I need a clean report. Executive-ready.
Something I can hand to the CEO Monday morning.

Good hunting.

— W

-----END PGP SIGNED MESSAGE-----
```

---

Think of yourself not as Jason Bourne — trained but running blind — but as **Elliot Alderson**: methodical, patient, invisible. Bourne reacts. You *plan*. You enumerate *everything* before you touch anything. The best hackers in the world aren't the ones who break in the fastest. They're the ones who understand the target better than it understands itself.

Your mission: perform a professional, documented penetration test against the assigned targets. Leave no stone unturned, and leave no trace of your visit — except in your report.

---

## Mission Objectives

By the end of this exam you will demonstrate the ability to:

1. Perform **passive reconnaissance** using OSINT tools (Maltego, `dig`, `nslookup`)
2. Execute **active scanning and enumeration** with Nmap and NSE scripts
3. Conduct a **vulnerability assessment** using OpenVAS
4. Identify and document **attack surface, open ports, services, and potential vulnerabilities**
5. Produce a **professional penetration testing report** following industry standards

---

## Target Scope — Rules of Engagement

Your target list is in `targets.txt`. Review it carefully.

```
╔══════════════════════════════════════════════════════════════════════════╗
║                         TARGET SELECTION RULES                           ║
╠══════════════════════════════════════════════════════════════════════════╣
║                                                                          ║
║   You MUST choose EXACTLY TWO (2) IP addresses from targets.txt          ║
║                                                                          ║
║       ✔  Target #1 — Must be a WINDOWS system                            ║
║       ✔  Target #2 — Must be a LINUX / UNIX system                       ║
║                                                                          ║
║   All phases of the exam (recon, scanning, OpenVAS) are performed        ║
║   ONLY on these two selected targets. No exceptions.                     ║
║                                                                          ║
╚══════════════════════════════════════════════════════════════════════════╝
```

**Additional Rules:**
- You **may NOT scan or probe any system outside the list** — ever
- You **may NOT attempt active exploitation** — assessment only in this exam
- **Both team members must contribute equally** — the report must reflect joint work

> **Tip:** Before locking in your selection, do a quick `nslookup` on each IP to read the reverse DNS hostname — this often reveals the OS or role of the system (e.g., a hostname containing `win`, `dc`, `ubuntu`, `srv` is a strong signal). Pick the two that look most interesting to investigate.

---

## Penetration Testing Methodology

You will follow the **PTES (Penetration Testing Execution Standard)** methodology, adapted for this assessment. The five phases below map directly to your report structure.

```
┌─────────────────────────────────────────────────────────────────────┐
│                     PTES — ADAPTED FOR THIS EXAM                    │
├───────────┬─────────────────────────────────────────────────────────┤
│  Phase 1  │  Passive Reconnaissance (OSINT)                         │
│  Phase 2  │  Active Scanning & Enumeration                          │
│  Phase 3  │  Vulnerability Identification (OpenVAS)                 │
│  Phase 4  │  Analysis & Risk Prioritization                         │
│  Phase 5  │  Reporting & Remediation Recommendations                │
└───────────┴─────────────────────────────────────────────────────────┘
```

---

## Phase 1 — Passive Reconnaissance

> *"Know your enemy and know yourself and you can fight a hundred battles without disaster."* — Sun Tzu

Passive recon means gathering intelligence **without sending a single packet to the target**. You are invisible at this stage.

### 1.1 — DNS Interrogation with `dig` and `nslookup`

For each target IP/domain, document the following:

**Tasks:**

```bash
# Basic A record lookup
nslookup <target_ip>
nslookup <target_domain>

# Reverse DNS lookup (PTR record)
dig -x <target_ip>

# Query all available DNS record types
dig <target_domain> ANY +noall +answer

# Look for mail server records
dig <target_domain> MX

# Look for name servers
dig <target_domain> NS

# Zone transfer attempt (this is legal passive recon — document if it succeeds!)
dig axfr <target_domain> @<nameserver>

# TTL and authoritative server information
dig <target_domain> SOA
```

**Document in your report:**
- Hostname associated with each IP
- Identified operating system hints (reverse DNS naming patterns, e.g., `win-`, `-dc-`, `ubuntu`)
- Any subdomains discovered
- Mail and name server records
- Whether a zone transfer succeeded or was blocked (and what that means)

---

### 1.2 — OSINT with Maltego (Community Edition)

Open Maltego and create a new graph. Your objective is to map the **digital footprint** of the target organization based on the IPs provided.

**Transforms to run:**

| Entity Type | Transform | What You're Looking For |
|---|---|---|
| IP Address | `To DNS Name [Reverse DNS]` | Hostnames, naming patterns |
| IP Address | `To Netblock [Using whois]` | Who owns the IP block |
| IP Address | `To Autonomous System` | ISP / Hosting provider |
| IP Address | `To Location [city]` | Geographic location |
| DNS Name | `To IP Address` | Confirm resolution |
| Netblock | `To IP Addresses [Routable]` | Adjacent IPs in the range |

**Document in your report:**
- Export your Maltego graph as a PNG and include it in the report
- Organization/ISP that owns the IP block (WHOIS data)
- Geographic location of each target
- Any additional hostnames, emails, or infrastructure discovered
- What the WHOIS registration data reveals about the target

> **Bonus:** Use Maltego's "Have I Been Pwned" or "Shodan" transforms (if available) to check if target IPs appear in breach databases or public threat intel feeds.

---

## Phase 2 — Active Scanning & Enumeration

> *"The quieter you become, the more you are able to hear."* — Kali Linux motto

Now you send packets. This is active — the target **can** see you (in theory). Work methodically.

### 2.1 — Host Discovery

Before deep scanning, confirm which hosts are alive:

```bash
# ICMP ping sweep (may be blocked by firewalls)
nmap -sn <target_ip>

# TCP SYN ping (more reliable when ICMP is blocked)
nmap -PS22,80,443,3389 <target_ip>

# UDP ping
nmap -PU53,161 <target_ip>
```

---

### 2.2 — Port Scanning

Perform a **comprehensive port scan** on each of your two targets. Document **every open port**.

```bash
# Fast scan — Top 1000 ports (start here to identify low-hanging fruit)
nmap -sV -sC -O <target_ip> -oN phase2_quick_<target_ip>.txt

# Full port scan — All 65535 ports (be patient, this takes time)
nmap -p- -sV -T4 <target_ip> -oN phase2_full_<target_ip>.txt

# UDP scan — Top 100 UDP ports (often overlooked!)
sudo nmap -sU --top-ports 100 <target_ip> -oN phase2_udp_<target_ip>.txt

# OS and version detection with aggressive mode
sudo nmap -A <target_ip> -oN phase2_aggressive_<target_ip>.txt
```

> **Save all output files** — include them as appendices in your PDF or reference them directly.

---

### 2.3 — NSE Scripts (Nmap Scripting Engine)

This is where Nmap becomes a scalpel instead of a hammer. Based on what you found in your port scan, run targeted NSE scripts.

**Recommended scripts by service:**

```bash
# ── HTTP / HTTPS ──────────────────────────────────────────────────────
nmap --script http-title,http-headers,http-methods,http-robots.txt \
     -p 80,443,8080,8443 <target_ip>

# Check for common web vulnerabilities
nmap --script http-vuln-cve2017-5638,http-shellshock,http-slowloris-check \
     -p 80,443 <target_ip>

# ── SMB (Windows file sharing) ────────────────────────────────────────
nmap --script smb-os-discovery,smb-security-mode,smb-enum-shares \
     -p 445 <target_ip>

# Check for EternalBlue (MS17-010) — the exploit that took down hospitals
nmap --script smb-vuln-ms17-010 -p 445 <target_ip>

# ── FTP ───────────────────────────────────────────────────────────────
nmap --script ftp-anon,ftp-bounce,ftp-syst,ftp-vsftpd-backdoor \
     -p 21 <target_ip>

# ── SSH ───────────────────────────────────────────────────────────────
nmap --script ssh-auth-methods,ssh-hostkey,ssh2-enum-algos \
     -p 22 <target_ip>

# ── RDP (Remote Desktop — Windows) ───────────────────────────────────
nmap --script rdp-enum-encryption,rdp-vuln-ms12-020 \
     -p 3389 <target_ip>

# ── DNS ───────────────────────────────────────────────────────────────
nmap --script dns-zone-transfer,dns-recursion,dns-brute \
     -p 53 <target_ip>

# ── SMTP ──────────────────────────────────────────────────────────────
nmap --script smtp-commands,smtp-open-relay,smtp-enum-users \
     -p 25,587 <target_ip>

# ── SNMP ──────────────────────────────────────────────────────────────
nmap --script snmp-info,snmp-sysdescr --script-args snmpcommunity=public \
     -p 161 -sU <target_ip>
```

**Document in your report for each NSE script run:**
- Command used (exact)
- Output summary (key findings)
- What this finding means from a security perspective

---

## Phase 3 — Vulnerability Assessment with OpenVAS

OpenVAS (now part of the Greenbone Vulnerability Manager) is your automated vulnerability scanner. Think of it as your tireless assistant that cross-references findings against thousands of CVEs while you sleep.

### 3.1 — Setup & Scan Configuration

```bash
# Start the OpenVAS services
sudo gvm-start

# Open the web interface
# Navigate to: https://127.0.0.1:9392
# Default credentials: admin / (set during setup)
```

**Create a new scan task:**

1. Go to **Scans → Tasks → New Task**
2. Set **Name:** `Midterm_<your_initials>_<target_ip>`
3. **Scan Targets:** Add each target IP
4. **Scan Config:** Use `Full and Fast` for comprehensive results
5. Start the scan and **wait for it to complete** (can take 30–90 min per target)

### 3.2 — Analyzing Results

Once the scan completes, export the report as **PDF and XML** from OpenVAS.

**In your report, document for each vulnerability found:**

| Field | What to Include |
|---|---|
| CVE | CVE number if applicable (e.g., CVE-2017-0144) |
| Name | Vulnerability name |
| Severity | Critical / High / Medium / Low / Info |
| CVSS Score | Numeric score (0–10) |
| Affected Service | Port / Protocol / Service |
| Description | Brief explanation of what the vulnerability is |
| Impact | What an attacker could do with this |
| Remediation | How to fix it |

> **Focus your report** on Critical and High severity findings. Mention Medium findings briefly. You may ignore Low/Info in the main body — reference them in an appendix.

---

## Phase 4 — Analysis & Risk Prioritization

This is where you stop being a tool operator and start being a **security analyst**. Anyone can run nmap. Not everyone can tell a story with the data.

### 4.1 — Attack Surface Mapping

Create a simple diagram (ASCII or drawn tool) showing:
- Open ports and services per target
- Which services are potentially vulnerable
- Potential attack chains (e.g., "SSH with weak cipher → lateral movement risk")

```
Example Attack Surface Summary:

TARGET: 189.172.195.189
┌──────────────────────────────────────────────────────────────┐
│ OS: Windows Server 2019 (estimated)                          │
├─────────┬──────────┬──────────┬──────────────────────────────┤
│ Port    │ Service  │ Version  │ Risk                         │
├─────────┼──────────┼──────────┼──────────────────────────────┤
│ 80/tcp  │ HTTP     │ IIS 10.0 │ Medium — version exposed     │
│ 443/tcp │ HTTPS    │ IIS 10.0 │ Low — check SSL config       │
│ 445/tcp │ SMB      │ Win 2019 │ HIGH — check MS17-010        │
│ 3389/tcp│ RDP      │ -        │ Medium — brute force risk    │
│ 53/udp  │ DNS      │ -        │ Low — zone transfer blocked  │
└─────────┴──────────┴──────────┴──────────────────────────────┘
```

### 4.2 — CVSS-Based Risk Prioritization

List your findings from **most critical to least critical** using a risk table:

| Priority | Finding | CVSS | Target | Recommended Action |
|---|---|---|---|---|
| 1 | EternalBlue (MS17-010) | 9.8 | 189.x.x.x | Patch immediately |
| 2 | FTP Anonymous Login | 7.5 | 187.x.x.x | Disable anonymous access |
| 3 | ... | ... | ... | ... |

---

## Phase 5 — Final Report

Your report is the product. It is what you sell. A pen test that isn't documented never happened.

### Report Structure (required sections in order)

```
1. Cover Page
2. Legal Disclaimer & Statement of Authorization  ← (provided above)
3. Executive Summary                              ← MAX 2 pages
4. Table of Contents
5. Methodology
6. Phase 1: Passive Reconnaissance Findings
7. Phase 2: Active Scanning & Enumeration Findings
8. Phase 3: Vulnerability Assessment Findings
9. Risk Summary & Attack Surface Map
10. Remediation Recommendations
11. Conclusion
12. Appendices (raw tool output, Maltego graphs, OpenVAS XML)
```

---

### Executive Summary — Writing Guide

The executive summary is **the most important section**. The CEO will read this. The IT staff will read the rest.

**Rules:**
- Maximum **2 pages**
- Written in **plain language** — no jargon without explanation
- Must include:
  - What was tested (scope)
  - How it was tested (methodology — one paragraph)
  - What was found (top 3–5 findings, severity)
  - What should be done (top 3 recommendations)
  - Overall risk rating: **Critical / High / Medium / Low**

**Template:**

> *"This report presents the findings of a penetration test conducted against [two target systems] on behalf of [fictional organization]. The assessment was performed by [Team Names] between [dates]. The goal was to identify vulnerabilities in the external attack surface using passive reconnaissance, active scanning, and automated vulnerability assessment.*
>
> *During the assessment, [X] vulnerabilities were identified: [N] Critical, [N] High, [N] Medium. The most significant finding was [top finding] affecting [service/port], which could allow [impact]. Immediate remediation is recommended for [top 2–3 findings].*
>
> *The overall risk posture of the assessed environment is rated: [CRITICAL / HIGH / MEDIUM / LOW]."*

---

## Deliverables

Submit a **single PDF file** named:

```
midterm1_[student_name1]_[student_name2].pdf
```

The PDF must contain all sections listed in the Report Structure above. Include all screenshots and tool output inline or as appendices.

**Checklist before submitting:**

- [ ] Legal disclaimer is the first page of the report
- [ ] Cover page includes team names, date, course name
- [ ] Executive summary is ≤ 2 pages
- [ ] Targets chosen: one Windows, one Linux/Unix
- [ ] Phase 1 results documented (dig/nslookup output + Maltego graph PNG)
- [ ] Phase 2 port scan results documented (open ports table per target)
- [ ] Phase 2 NSE script results documented (minimum 4 scripts per target)
- [ ] Phase 3 OpenVAS report included (minimum 1 scan per target)
- [ ] Risk table with CVE references where applicable
- [ ] Remediation recommendations for each High/Critical finding
- [ ] Raw tool output saved and referenced in appendices

---

## Grading Rubric

| Category | Points | Description |
|---|---|---|
| **Executive Summary** | 15 | Clear, concise, ≤ 2 pages, professional tone, overall risk rating |
| **Phase 1 — Passive Recon** | 15 | `dig`, `nslookup` outputs documented; Maltego graph exported and analyzed |
| **Phase 2 — Nmap Scanning** | 20 | Full port scan, version detection, OS fingerprint, documented findings |
| **Phase 2 — NSE Scripts** | 15 | Minimum 4 scripts per target, correct script selection, results interpreted |
| **Phase 3 — OpenVAS** | 20 | Scans completed, findings documented with CVE, CVSS, and impact |
| **Risk Prioritization** | 10 | Findings ranked by severity, attack surface map included |
| **Remediation Quality** | 5 | Actionable, specific, technically correct recommendations |
| **Report Professionalism** | 10 | Structure, grammar, clarity, legal disclaimer present |
| **Bonus — Zone Transfer / Shodan / HIBP** | +5 | Evidence of zone transfer attempt or external threat intel integration |
| **TOTAL** | **110** | |

---

## Pro Tips from the Field

> These are the things they don't always teach in class but matter in real engagements.

1. **Save everything.** Run all tools with `-oN output.txt` (Nmap) or `tee output.txt`. You will need to reference raw output in your report.

2. **Screenshot as you go.** Don't try to reconstruct findings after the fact. Take a screenshot every time you see something interesting.

3. **Read the banners.** Service banners (`nmap -sV`) often reveal software versions. A version number + `searchsploit` or a quick Google = potential CVE. Document it.

4. **Nmap is not silent.** Active scanning generates logs on the target. In a real engagement, this matters. In this exam, just be aware of it.

5. **OpenVAS takes time.** Start your OpenVAS scans first, then work on your Nmap/dig/nslookup documentation while it runs. Parallel work = time saved.

6. **Scope creep kills careers.** Stay in scope. Always. No exceptions.

7. **The report is the deliverable.** A brilliant hack that isn't documented has zero value to the client. Write clearly. Explain the "so what" for every finding.

---

## Quick Reference — Command Cheatsheet

```bash
# ── PASSIVE RECON ──────────────────────────────────────────────────────
nslookup <ip_or_domain>
dig -x <ip>
dig <domain> ANY +noall +answer
dig <domain> MX NS SOA

# ── HOST DISCOVERY ────────────────────────────────────────────────────
nmap -sn <ip>
nmap -PS22,80,443,3389 <ip>

# ── PORT SCANNING ─────────────────────────────────────────────────────
nmap -sV -sC -O <ip> -oN quick_scan.txt
nmap -p- -sV -T4 <ip> -oN full_scan.txt
sudo nmap -sU --top-ports 100 <ip> -oN udp_scan.txt
sudo nmap -A <ip> -oN aggressive.txt

# ── NSE ESSENTIALS ────────────────────────────────────────────────────
nmap --script smb-os-discovery,smb-security-mode -p 445 <ip>
nmap --script smb-vuln-ms17-010 -p 445 <ip>
nmap --script http-title,http-methods -p 80,443 <ip>
nmap --script ftp-anon,ftp-vsftpd-backdoor -p 21 <ip>
nmap --script ssh-auth-methods -p 22 <ip>
nmap --script rdp-enum-encryption -p 3389 <ip>

# ── OPENVAS ───────────────────────────────────────────────────────────
sudo gvm-start
# → https://127.0.0.1:9392
```

---

## Resources

| Resource | URL / Command |
|---|---|
| PTES Standard | `http://www.pentest-standard.org` |
| NIST NVD (CVE lookup) | `https://nvd.nist.gov/vuln/search` |
| Nmap NSE Script Docs | `https://nmap.org/nsedoc/` |
| CVSS Calculator | `https://www.first.org/cvss/calculator/3.1` |
| Maltego Docs | `https://docs.maltego.com` |
| OpenVAS / Greenbone Docs | `https://greenbone.github.io/docs/` |

---

*"The only truly secure system is one that is powered off, cast in a block of concrete and sealed in a lead-lined room with armed guards — and even then I have my doubts."*
*— Gene Spafford*

---

**Good luck. Stay in scope. Write a great report.**

*— Happy Hacking!*

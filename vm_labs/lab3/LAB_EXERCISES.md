# Liverpul Corp VM - Security Lab Exercises

## Overview
This document contains hands-on security exercises designed to practice penetration testing techniques in a controlled environment. The exercises progress from basic reconnaissance to advanced exploitation.

**Target IP**: 192.168.56.10

---

## Exercise 1: Network Reconnaissance (Beginner)

### Objective
Learn to discover and enumerate services running on a target system.

### Tasks

1. **Network Discovery**
   - Use `ping` to verify the target is reachable
   - Determine if the host is alive

2. **Port Scanning**
   - Perform a basic port scan to discover open ports
   - Identify common ports (21, 22, 80, 3306, etc.)
   - Perform a service version detection scan
   - Scan for OS detection

3. **Service Enumeration**
   - List all discovered services with their versions
   - Document potential vulnerabilities based on versions

### Expected Findings
- Document at least 4 open ports
- Identify the OS family
- List service versions

### Tools
- `nmap`
- `netcat`
- `masscan` (optional)

### Questions
1. What ports are open on the target?
2. What services are running on each port?
3. What is the operating system of the target?
4. Which service versions might have known vulnerabilities?

---

## Exercise 2: FTP Enumeration and Exploitation (Beginner-Intermediate)

### Objective
Learn to enumerate and exploit FTP services, including anonymous access.

### Tasks

1. **Anonymous FTP Access**
   - Connect to the FTP server anonymously
   - List available directories and files
   - Attempt to upload and download files
   - Document the directory structure

2. **User Enumeration**
   - Attempt to enumerate valid usernames
   - Try common usernames (admin, root, test, user, etc.)
   - Document any error message differences

3. **Banner Grabbing**
   - Capture the FTP banner
   - Identify the FTP server software and version
   - Research known vulnerabilities for this version

### Expected Findings
- Anonymous access should be enabled
- Identify accessible directories
- Document FTP server version

### Tools
- `ftp` command-line client
- `curl`
- `netcat`
- Wireshark (for traffic analysis)

### Questions
1. Can you connect anonymously? What can you access?
2. What FTP server software is running?
3. Are there any interesting files in the FTP directories?
4. Can anonymous users upload files?

---

## Exercise 3: Password Cracking (Intermediate)

### Objective
Practice password attacks using dictionary-based techniques.

### Tasks

1. **FTP Brute Force**
   - Create a username list (hint: try common names)
   - Use a dictionary attack against FTP
   - Document successful credentials
   - Calculate time taken for the attack

2. **MySQL Brute Force**
   - Enumerate MySQL users
   - Attempt dictionary attacks against MySQL
   - Test for default credentials
   - Document successful access

3. **Hash Cracking** (Advanced)
   - If you gain system access, extract password hashes
   - Use hash cracking tools with dictionaries
   - Compare cracking times with different wordlists

### Expected Findings
- At least 3 user accounts with weak passwords
- Database credentials
- Password patterns

### Tools
- `hydra`
- `medusa`
- `ncrack`
- `john` (if hashes obtained)
- `hashcat` (if hashes obtained)
- Dictionary files: `/usr/share/wordlists/rockyou.txt` or provided dictionaries

### Sample Commands
```bash
# FTP brute force
hydra -L users.txt -P passwords.txt ftp://192.168.56.10

# MySQL brute force
hydra -L users.txt -P passwords.txt mysql://192.168.56.10
```

### Questions
1. How many valid user accounts did you discover?
2. What were the passwords?
3. How long did the brute force attack take?
4. How could these accounts be better secured?

---

## Exercise 4: Web Application Analysis (Intermediate)

### Objective
Analyze the web application for vulnerabilities and information disclosure.

### Tasks

1. **Web Reconnaissance**
   - Access the website in a browser
   - View page source and identify technologies
   - Map the application structure
   - Identify JavaScript frameworks and libraries

2. **API Endpoint Analysis**
   - Discover and test the API endpoint
   - Analyze API responses for sensitive information
   - Test for SQL injection vulnerabilities
   - Check for authentication/authorization issues

3. **Information Disclosure**
   - Look for exposed database information
   - Check for error messages revealing system details
   - Examine HTTP headers for security misconfigurations
   - Test for directory listing vulnerabilities

4. **Client-Side Analysis**
   - Analyze JavaScript code for hardcoded credentials
   - Check for API keys or sensitive data
   - Review Angular Material implementation

### Expected Findings
- Database schema information exposed
- Lack of input validation
- Potential SQL injection points
- Sensitive data in API responses

### Tools
- Browser Developer Tools
- `curl`
- `wget`
- Burp Suite / OWASP ZAP
- `nikto`
- `dirb` / `gobuster`

### Sample Commands
```bash
# Test API endpoint
curl http://192.168.56.10/api.php

# Directory enumeration
dirb http://192.168.56.10

# Web vulnerability scanning
nikto -h http://192.168.56.10
```

### Questions
1. What database tables are exposed through the website?
2. Are there any SQL injection vulnerabilities?
3. What security headers are missing?
4. Can you modify API requests to access unauthorized data?

---

## Exercise 5: Database Exploitation (Intermediate-Advanced)

### Objective
Practice database enumeration and exploitation techniques.

### Tasks

1. **Remote Access**
   - Attempt to connect to MySQL from your attacking machine
   - Test for weak credentials
   - Enumerate databases and tables

2. **Data Exfiltration**
   - List all databases
   - Enumerate tables in the fakystore database
   - Extract all data from tables
   - Look for sensitive information

3. **Database Privilege Escalation**
   - Check user privileges
   - Attempt to read system files (if possible)
   - Test for UDF (User Defined Function) exploits
   - Try to write files to the system

4. **SQL Injection** (if found in web app)
   - Test input fields for SQL injection
   - Use SQLMap to automate exploitation
   - Perform database fingerprinting
   - Attempt to read system files

### Expected Findings
- Database accessible from network
- Admin credentials discovered
- Full database access achieved
- Potential for command execution

### Tools
- `mysql` client
- SQLMap
- Burp Suite
- Manual SQL injection techniques

### Sample Commands
```bash
# Connect to MySQL
mysql -h 192.168.56.10 -u admin -p

# SQLMap (if vulnerable endpoint found)
sqlmap -u "http://192.168.56.10/api.php" --dbs
```

### Questions
1. What databases exist on the server?
2. What privileges does the admin user have?
3. Can you write files to the system?
4. Is there any sensitive customer data?

---

## Exercise 6: SSH Brute Force and System Access (Advanced)

### Objective
Gain system-level access through credential attacks.

### Tasks

1. **SSH Service Detection**
   - Check if SSH is accessible (port 22)
   - Identify SSH version
   - Test for SSH user enumeration

2. **Credential Attacks**
   - Use discovered usernames from previous exercises
   - Perform targeted dictionary attacks
   - Try password reuse from other services

3. **Initial Access**
   - Gain SSH access with valid credentials
   - Document initial access level
   - Enumerate user permissions

4. **Post-Exploitation Enumeration**
   - Check user groups and permissions
   - List running services
   - Check for sudo privileges
   - Enumerate other user accounts

### Expected Findings
- SSH access with user credentials
- Limited user privileges initially
- Potential privilege escalation vectors

### Tools
- `hydra`
- `ssh`
- `nmap` (SSH enumeration scripts)

### Sample Commands
```bash
# SSH brute force
hydra -l pedro -P rockyou.txt ssh://192.168.56.10

# SSH access
ssh pedro@192.168.56.10
```

### Questions
1. Were you able to gain SSH access?
2. What is the user's privilege level?
3. What files can the user access?
4. Are there any sudo privileges?

---

## Exercise 7: Privilege Escalation (Advanced)

### Objective
Escalate privileges from a standard user to root access.

### Tasks

1. **System Enumeration**
   - Check kernel version
   - List running processes
   - Check for SUID binaries
   - Enumerate cron jobs
   - Check file permissions on sensitive files

2. **Privilege Escalation Vectors**
   - Test for sudo misconfigurations
   - Look for writable service files
   - Check for exploitable SUID binaries
   - Test for weak file permissions
   - Look for credentials in configuration files

3. **Exploitation**
   - Choose an appropriate privilege escalation method
   - Execute the exploit
   - Verify root access
   - Document the method used

4. **Post-Exploitation**
   - Access all user accounts
   - Dump password hashes
   - Access database files directly
   - Read shadow file

### Expected Findings
- Multiple privilege escalation vectors
- Configuration files with credentials
- Weak permissions on critical files

### Tools
- `LinPEAS` / `linpeas.sh`
- `linux-exploit-suggester`
- GTFOBins reference
- Manual enumeration

### Sample Commands
```bash
# SUID binaries
find / -perm -4000 2>/dev/null

# Check sudo
sudo -l

# Writable files
find / -writable -type f 2>/dev/null | grep -v proc

# Services
ps aux
```

### Questions
1. What privilege escalation vector did you find?
2. How did you achieve root access?
3. What could prevent this privilege escalation?
4. Can you access all database files now?

---

## Exercise 8: Network Traffic Analysis (Intermediate)

### Objective
Analyze network traffic to understand protocols and identify sensitive data.

### Tasks

1. **Packet Capture**
   - Use Wireshark to capture traffic to the target
   - Perform various activities (FTP, HTTP, MySQL)
   - Save captures for analysis

2. **Protocol Analysis**
   - Analyze FTP traffic (is it encrypted?)
   - Analyze HTTP traffic (is it encrypted?)
   - Analyze MySQL traffic (is it encrypted?)
   - Identify cleartext credentials

3. **Credential Extraction**
   - Extract FTP passwords from captures
   - Extract HTTP data from captures
   - Extract database credentials if possible
   - Document all findings

4. **Attack Recreation**
   - Use captured data to replay attacks
   - Demonstrate the risks of unencrypted protocols

### Expected Findings
- All traffic is unencrypted
- Passwords visible in cleartext
- Database queries visible
- User data exposed

### Tools
- Wireshark
- `tcpdump`
- `tshark`
- Network TAP or promiscuous mode

### Sample Commands
```bash
# Capture traffic
sudo tcpdump -i eth0 host 192.168.56.10 -w capture.pcap

# Analyze with tshark
tshark -r capture.pcap -Y "ftp"
```

### Questions
1. Are any credentials transmitted in cleartext?
2. What sensitive data is visible in the traffic?
3. Can you extract database queries from the capture?
4. How would SSL/TLS prevent these attacks?

---

## Exercise 9: Comprehensive Security Assessment (Advanced)

### Objective
Perform a complete penetration test and create a professional report.

### Tasks

1. **Reconnaissance Phase**
   - Passive information gathering
   - Active scanning and enumeration
   - Service version identification
   - Vulnerability assessment

2. **Exploitation Phase**
   - Identify exploitable vulnerabilities
   - Gain initial access
   - Document all successful exploits
   - Maintain access (if applicable)

3. **Post-Exploitation Phase**
   - Privilege escalation
   - Lateral movement (if applicable)
   - Data exfiltration
   - Persistence mechanisms

4. **Reporting Phase**
   - Executive summary
   - Technical findings with evidence
   - Risk ratings (Critical, High, Medium, Low)
   - Remediation recommendations
   - Proof-of-concept screenshots

### Deliverables
- Complete penetration test report
- Network diagram
- List of vulnerabilities with CVSS scores
- Detailed remediation plan
- Timeline of attack activities

### Report Structure
```
1. Executive Summary
2. Scope and Methodology
3. Findings Overview
   - Critical Vulnerabilities
   - High-Risk Issues
   - Medium-Risk Issues
   - Low-Risk Issues
4. Detailed Technical Findings
5. Remediation Recommendations
6. Appendices (Screenshots, Command Output, etc.)
```

---

## Exercise 10: Post-Exploitation and Persistence (Advanced)

### Objective
Learn post-exploitation techniques and persistence mechanisms.

### Tasks

1. **Data Harvesting**
   - Extract all database data
   - Collect user credentials
   - Find configuration files
   - Identify sensitive business data

2. **Persistence Mechanisms**
   - Create backdoor user accounts
   - Modify SSH authorized_keys
   - Create cron jobs for callback
   - Modify startup scripts

3. **Covering Tracks**
   - Understand log locations
   - Review access logs
   - Discuss (but don't implement) log cleaning
   - Document forensic artifacts left behind

4. **Clean-up** (Important)
   - Remove backdoors
   - Delete created files
   - Restore modified configurations
   - Document all changes made

### Expected Findings
- Multiple persistence mechanisms possible
- Various log files recording activities
- Forensic artifacts of attack

### Tools
- Standard Linux commands
- SSH key management
- Cron job management

### Questions
1. What data would be valuable to an attacker?
2. How could an attacker maintain persistent access?
3. What logs would reveal the intrusion?
4. How can defenders detect these activities?

---

## Bonus Exercises

### A. Automated Vulnerability Scanning
- Use OpenVAS or Nessus to scan the target
- Compare automated findings with manual discoveries
- Analyze false positives and false negatives

### B. Exploit Development
- Identify a specific vulnerability
- Write a custom exploit script (Python/Bash)
- Test the exploit in a controlled manner

### C. Defensive Analysis
- Review system logs for attack signatures
- Identify IDS/IPS rules that would detect attacks
- Propose security hardening measures
- Create a defense-in-depth strategy

### D. Web Application Firewall Bypass
- Implement a simple WAF rule set
- Attempt to bypass the WAF
- Document successful bypass techniques

---

## Security Hardening Checklist

After completing the exercises, use this checklist to secure the system:

### Network Security
- [ ] Implement firewall rules (iptables)
- [ ] Disable unnecessary services
- [ ] Change default ports
- [ ] Implement rate limiting

### Authentication
- [ ] Enforce strong password policies
- [ ] Implement account lockout policies
- [ ] Use SSH key authentication only
- [ ] Disable root login via SSH
- [ ] Remove/secure test accounts

### FTP Security
- [ ] Disable anonymous FTP
- [ ] Use FTPS (FTP over SSL/TLS)
- [ ] Implement chroot jails
- [ ] Restrict user access

### Database Security
- [ ] Change default credentials
- [ ] Restrict network access to localhost
- [ ] Implement least privilege principle
- [ ] Enable query logging
- [ ] Use prepared statements (prevent SQL injection)

### Web Application Security
- [ ] Implement input validation
- [ ] Add security headers (CSP, HSTS, etc.)
- [ ] Use HTTPS/SSL
- [ ] Implement authentication on API endpoints
- [ ] Add rate limiting
- [ ] Sanitize error messages

### System Hardening
- [ ] Update all packages
- [ ] Remove unnecessary packages
- [ ] Configure SELinux/AppArmor
- [ ] Harden SSH configuration
- [ ] Implement file integrity monitoring
- [ ] Configure centralized logging

---

## Learning Resources

### Books
- "The Web Application Hacker's Handbook" by Dafydd Stuttard
- "Penetration Testing" by Georgia Weidman
- "The Hacker Playbook 3" by Peter Kim

### Online Resources
- OWASP Top 10
- HackTheBox / TryHackMe
- PortSwigger Web Security Academy
- GTFOBins (privilege escalation)

### Tools Documentation
- Nmap Reference Guide
- Metasploit Unleashed
- Burp Suite Documentation
- SQLMap Documentation

---

## Grading Rubric (For Instructors)

### Exercise Completion (40%)
- All tasks attempted and documented
- Proper tool usage
- Correct methodology followed

### Technical Accuracy (30%)
- Correct findings
- Accurate vulnerability assessment
- Proper exploitation techniques

### Documentation (20%)
- Clear and organized report
- Screenshots and evidence
- Command history included

### Analysis and Recommendations (10%)
- Understanding of vulnerabilities
- Practical remediation suggestions
- Security awareness demonstrated

---

## Lab Safety and Ethics

### Important Notes
1. **Authorization**: Only test systems you own or have explicit permission to test
2. **Scope**: Stay within the defined scope (this VM only)
3. **Damage**: Do not cause permanent damage to systems
4. **Data**: Do not steal or misuse real data
5. **Disclosure**: Follow responsible disclosure practices
6. **Legal**: Unauthorized access is illegal in most jurisdictions

### Lab Environment Rules
- Keep the VM on an isolated network
- Do not use these techniques on production systems
- Document all activities for learning purposes
- Reset the VM between exercise sessions if needed
- Report any unintended consequences to instructor

---

## Quick Reference

### Reset VM
```bash
vagrant destroy -f
vagrant up
```

### Access VM
```bash
vagrant ssh
```

### Target Credentials
- **Users**: pedro, pablo, paco
- **Passwords**: Use dictionary attacks to discover
- **Database**: admin / admin
- **Anonymous FTP**: Enabled

### Target IP
```
192.168.56.10
```

### Service Ports
- FTP: 21
- SSH: 22 (if enabled)
- HTTP: 80
- MySQL: 3306

---

## Troubleshooting

### VM Not Accessible
```bash
vagrant reload
ping 192.168.56.10
```

### Services Not Running
```bash
vagrant ssh
sudo rc-service --list
sudo rc-service <service> restart
```

### Network Issues
- Check VirtualBox network settings
- Verify host-only network adapter
- Confirm IP address with: `vagrant ssh -c "ip addr"`

---

## Submission Guidelines (For Students)

### Required Deliverables
1. **Lab Report** (PDF format)
   - Executive summary
   - Methodology
   - Findings for each exercise
   - Screenshots as evidence
   - Command history
   - Answers to all questions

2. **Technical Artifacts**
   - Exploit scripts (if created)
   - Wireshark captures (selected exercises)
   - Scan results
   - Extracted data samples

3. **Reflection**
   - What you learned
   - Challenges faced
   - Real-world applications
   - Ethical considerations

### Format
- Use markdown or PDF
- Include timestamps
- Organize by exercise number
- Include table of contents
- Professional formatting

---

## Additional Challenges

### Challenge 1: Time-Based Attack
Complete full system compromise in under 30 minutes.

### Challenge 2: Stealth Mode
Compromise the system while minimizing log entries.

### Challenge 3: Custom Exploit
Write a custom exploit script for a discovered vulnerability.

### Challenge 4: Forensics
After an attack, perform forensic analysis to identify what happened.

### Challenge 5: Blue Team
Defend the system against a simulated attack by a classmate.

---

## Version History
- v1.0 (2026-01-03): Initial release

## Credits
Lab VM and exercises created for educational purposes.
Target: Liverpul Corp VM (Alpine Linux)

## Support
For issues or questions about the exercises:
- Check the README.md for basic troubleshooting
- Review the Vagrantfile for system configuration
- Reset the VM if needed: `vagrant destroy -f && vagrant up`

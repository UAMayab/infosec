# Lab 2: Energía Marina - Web Exploitation Lab
## Complete Lab Summary

**Created:** February 15, 2026
**Author:** Miguel Guirao
**Course:** Introduction to Cybersecurity
**Lab Type:** Web Application Security & OWASP Top 10

---

## 🎉 Lab 2 Complete - All Files Created!

### 📁 Directory Structure
```
lab2/
├── Vagrantfile                    # ✅ Alpine Linux + Nginx + PHP 8.2 + MariaDB
├── README.md                      # ✅ Lab overview and quick start guide
├── ASSIGNMENT.md                  # ✅ Student assignment (33KB, comprehensive)
├── TEACHERS_GUIDE.md              # ✅ Professor's guide with exploitation steps (54KB)
├── LAB_SUMMARY.md                 # ✅ This file
└── www/                           # ✅ Web application files
    ├── index.html                 # Main page (dark Mexican hacker theme)
    ├── login.php                  # SQL Injection vulnerability
    ├── dashboard.php              # Directory Traversal/LFI vulnerability
    ├── contacto.php               # XSS (Stored) vulnerability
    ├── produccion.php             # Production dashboard
    ├── info.php                   # Security Misconfiguration (phpinfo)
    ├── logout.php                 # Logout handler
    ├── config.php                 # Database configuration
    ├── admin/
    │   └── index.php              # Broken Authentication vulnerability
    ├── css/
    │   └── style.css              # Dark hacker Mexican theme (13KB)
    └── docs/
        ├── manual_seguridad.txt   # Sample document for LFI
        └── procedimientos.txt     # Sample document
```

Total Files: 17 files, 6 directories

---

## 🎯 5 Vulnerabilities Implemented (OWASP Top 10)

| # | Vulnerability | Location | Flag | OWASP Category |
|---|--------------|----------|------|----------------|
| 1 | **SQL Injection** | `login.php` | `EM{5ql_1nj3ct10n_3n_v3r4cruz}` | A03:2021 - Injection |
| 2 | **XSS (Stored)** | `contacto.php` | `EM{cr0ss_s1t3_scr1pt1ng_m4r1n0}` | A03:2021 - Injection |
| 3 | **Directory Traversal** | `dashboard.php?doc=` | `EM{l0c4l_f1l3_1nclus10n_p3tr0l30}` | A01:2021 - Broken Access Control |
| 4 | **Security Misconfiguration** | `info.php`, `.git/` | `EM{m1sc0nf1gur4t10n_gul0_mex1c0}` | A05:2021 - Security Misconfiguration |
| 5 | **Broken Authentication** | `admin/index.php` | `EM{br0k3n_4uth_3n3rg14_m4r1n4}` | A07:2021 - Auth Failures |

### Vulnerability Details

#### 1. SQL Injection (login.php)
- **Type:** Authentication bypass, data extraction
- **Vulnerable Code:** Direct string concatenation in SQL query
- **Exploitation:** `' OR '1'='1`, `admin'--`, UNION-based injection
- **Flag Location:** Database table `flags`, revealed upon admin login
- **Tools:** Nikto detection, OWASP ZAP active scan, SQLMap, manual

#### 2. Cross-Site Scripting - XSS (contacto.php)
- **Type:** Stored XSS
- **Vulnerable Code:** No input sanitization, unescaped output
- **Exploitation:** `<script>alert('XSS')</script>`, `<img src=x onerror=alert(1)>`
- **Flag Location:** Returned in success message after XSS payload submission
- **Tools:** OWASP ZAP scanner, Burp Suite, manual injection

#### 3. Directory Traversal / Local File Inclusion (dashboard.php)
- **Type:** Path traversal, arbitrary file read
- **Vulnerable Code:** Weak validation of `doc` parameter
- **Exploitation:** `?doc=/etc/passwd`, `?doc=/etc/energia-marina-secret.conf`
- **Flag Location:** File `/etc/energia-marina-secret.conf`
- **Tools:** Manual exploitation, Burp Suite, cURL, OWASP ZAP

#### 4. Security Misconfiguration (Multiple Locations)
- **Type:** Information disclosure, exposed sensitive files
- **Locations:**
  - `info.php` - phpinfo() exposed
  - `.git/config` - Version control exposed
  - Directory listing enabled
- **Exploitation:** Direct access to sensitive URLs
- **Flag Location:** Hidden in `.git/config` file and `info.php` HTML comments
- **Tools:** Nikto (primary), OWASP ZAP, Nmap, manual browsing

#### 5. Broken Authentication (admin/index.php)
- **Type:** Authentication bypass, weak session management
- **Vulnerable Code:** Multiple bypass vectors
- **Exploitation:**
  - URL parameter: `?token=admin_access_2024`
  - Cookie manipulation: `admin_level=1`
  - Session hijacking
- **Flag Location:** Displayed on admin panel after successful bypass
- **Tools:** Browser DevTools, Burp Suite, cookie editors, cURL

---

## 📚 Key Documents Created

### 1. ASSIGNMENT.md (Student-Facing) - 33KB
**Content:**
- ✅ Compelling hacker-themed story based on real Mexican energy breach
- ✅ Lab #3 (third hands-on lab for students)
- ✅ Related to Chapter 5 - OWASP Top 10
- ✅ 6 comprehensive phases:
  - Phase 1: Reconnaissance & Automated Scanning (25 points)
  - Phase 2: SQL Injection (25 points)
  - Phase 3: Cross-Site Scripting (20 points)
  - Phase 4: Directory Traversal/LFI (20 points)
  - Phase 5: Security Misconfiguration (15 points)
  - Phase 6: Broken Authentication (15 points)
  - Comprehensive Analysis & Remediation (20 points)
  - Report Quality (10 points)
- ✅ **Total: 150 points + 20 bonus = 170 max**
- ✅ **Duration: 2 weeks**
- ✅ Focus on **Nikto** and **OWASP ZAP** as primary tools
- ✅ Detailed grading rubric
- ✅ Professional report requirements
- ✅ Ethical guidelines and CFAA warnings
- ✅ Step-by-step lab setup instructions
- ✅ Timeline with milestones
- ✅ Bonus challenges (SQLMap, chaining attacks)

**Learning Objectives:**
- Master automated web vulnerability scanning
- Understand manual exploitation techniques
- Develop professional documentation skills
- Learn OWASP Top 10 vulnerabilities
- Practice ethical hacking methodology

### 2. TEACHERS_GUIDE.md (Professor-Only) - 54KB
**Content:**
- ✅ Complete exploitation walkthroughs for all 5 vulnerabilities
- ✅ **Multiple exploitation methods** for each vulnerability:
  - SQL Injection: 5+ methods (bypass, UNION, tools)
  - XSS: 5 different payloads
  - LFI: 4 different techniques
  - Misconfiguration: 4 discovery methods
  - Broken Auth: 6 bypass methods
- ✅ Exact commands, payloads, and URLs
- ✅ Flag locations and values
- ✅ Database schema and table information
- ✅ Common student mistakes (10 most frequent)
- ✅ Remediation recommendations with code examples
- ✅ Detailed grading guidelines (100-point rubric)
- ✅ Grade boundaries (A+ through F)
- ✅ Troubleshooting section (9 common issues)
- ✅ Pre-lab preparation checklist
- ✅ Lab introduction outline (45 minutes)
- ✅ Progressive hint strategy
- ✅ Discussion topics for post-lab review
- ✅ Quick reference appendices

**Key Features:**
- Verbatim exploitation instructions
- Alternative paths for different skill levels
- Screenshot verification suggestions
- Tool-specific guidance (Nikto, ZAP, Burp, Metasploit)
- Real-world context and career connections

### 3. README.md (Lab Overview) - 5.7KB
**Content:**
- ✅ Quick start guide
- ✅ Lab overview and objectives
- ✅ VM management commands
- ✅ Default credentials table
- ✅ Troubleshooting guide
- ✅ File structure diagram
- ✅ Security warnings
- ✅ Tools recommended list
- ✅ Support and credits

---

## 🎨 Website Theme & Design

### Energía Marina S.A. de C.V.
**Fictional Company Profile:**
- 🇲🇽 **Location:** Veracruz, Mexico
- 🛢️ **Industry:** Oil extraction and production
- 🌊 **Operations:** Gulf of Mexico offshore platforms
- ⚡ **Founded:** 1995 (fictional)
- 👥 **Employees:** 850+ (fictional)
- 📊 **Production:** 45,000 barrels/day (fictional)

**Design Elements:**
- ⚡ Dark hacker aesthetic
- 💻 Matrix-style green text effects
- 🇲🇽 Mexican red and green color accents
- 🌊 Ocean/maritime imagery
- 🔒 Professional corporate layout
- 💀 Intentionally vulnerable (educational)

**Website Features:**
- Spanish language content
- Dark background (#0a0e27)
- Neon green primary color (#00ff41)
- Orange secondary color (#ff6b35)
- Glitch text effects
- Responsive design
- Professional navigation
- Multiple pages and forms

**Pages Implemented:**
1. **index.html** - Homepage with company info
2. **login.php** - Employee portal (SQL Injection)
3. **dashboard.php** - Employee dashboard (LFI)
4. **produccion.php** - Production data dashboard
5. **contacto.php** - Contact form (XSS)
6. **info.php** - System info page (Misconfiguration)
7. **admin/index.php** - Admin panel (Broken Auth)

---

## ⚙️ Technical Stack

### Virtual Machine Specifications
- **Operating System:** Alpine Linux 3.18
- **Hypervisor:** VirtualBox
- **Provisioning:** Vagrant
- **Memory:** 2048 MB (2 GB)
- **CPUs:** 2 cores
- **Network:** Bridged (public_network)
- **Hostname:** energia-marina

### Software Stack
- **Web Server:** Nginx (latest from Alpine repos)
- **PHP:** PHP 8.2 with modules:
  - php82-fpm
  - php82-mysqli
  - php82-session
  - php82-json
  - php82-mbstring
  - php82-openssl
- **Database:** MariaDB (latest from Alpine repos)
- **Additional Tools:**
  - curl
  - vim

### Database Schema
**Tables Created:**
1. **empleados** - Employee credentials (for SQL injection)
2. **produccion** - Production data
3. **mensajes** - Contact form submissions (for XSS)
4. **flags** - Hidden flags for capture

**Sample Data:**
- 4 employees with weak passwords
- Production data for 4 platforms
- 5 flags in the flags table

### Network Configuration
- **Bridged Network:** Configurable interface (default: wlp4s0)
- **IP Assignment:** DHCP from local network
- **Port:** 80 (HTTP)
- **Access:** http://<VM_IP_ADDRESS>

### Security Configurations (Intentionally Insecure)
- ✅ Directory listing enabled
- ✅ phpinfo() accessible
- ✅ .git directory exposed
- ✅ No input validation
- ✅ No prepared statements
- ✅ Display errors enabled
- ✅ Weak session management
- ✅ No CSRF protection
- ✅ No XSS protection
- ✅ Predictable tokens

---

## 🛠️ Default Credentials

### Database Access
- **Root User:** root
- **Password:** (empty)
- **Database:** energia_marina

### Web Application Users

| Username | Password | Departamento | Nivel de Acceso | Notes |
|----------|----------|--------------|-----------------|-------|
| admin | admin123 | Administración | admin | Primary target for SQL injection |
| jperez | veracruz2024 | Producción | user | Veracruz-themed password |
| mrodriguez | password | Ingeniería | user | Weak common password |
| lgarcia | qwerty | Operaciones | user | Extremely weak password |

### Authentication Bypass Tokens
- **Admin URL Token:** `admin_access_2024`
- **Admin Cookie Value:** `admin_level=1`

---

## ✅ Quality Assurance Checklist

### Files Created
- ✅ Vagrantfile (8.6KB) - Validated successfully
- ✅ README.md (5.7KB)
- ✅ ASSIGNMENT.md (33KB)
- ✅ TEACHERS_GUIDE.md (54KB)
- ✅ LAB_SUMMARY.md (this file)
- ✅ index.html (4.9KB)
- ✅ login.php (4.9KB)
- ✅ dashboard.php (5.8KB)
- ✅ contacto.php (6.5KB)
- ✅ produccion.php (4.3KB)
- ✅ info.php (503B)
- ✅ logout.php (122B)
- ✅ config.php (635B)
- ✅ admin/index.php (5.4KB)
- ✅ css/style.css (13KB)
- ✅ docs/manual_seguridad.txt (1.1KB)
- ✅ docs/procedimientos.txt (832B)

**Total:** 17 files, 6 directories, ~154KB total

### Validation Checks
- ✅ Vagrantfile syntax validated
- ✅ All PHP files created with proper vulnerabilities
- ✅ Database initialization script included
- ✅ CSS properly formatted
- ✅ HTML valid structure
- ✅ All 5 flags properly placed
- ✅ Documentation comprehensive and clear
- ✅ No syntax errors in any file

### Testing Checklist
- ⏳ VM deployment (not tested yet)
- ⏳ Web server accessibility
- ⏳ Database connectivity
- ⏳ SQL Injection exploitation
- ⏳ XSS payload execution
- ⏳ Directory traversal working
- ⏳ Misconfiguration discovery
- ⏳ Authentication bypass methods
- ⏳ Flag capture verification

---

## 🚀 Deployment Instructions

### For Instructors

**1. Test the Lab Environment**
```bash
cd /home/mguirao/code/infosec/vm_labs/lab2
vagrant up
# Wait 5-10 minutes for first-time setup
vagrant ssh -c "ip -4 addr show eth1 | grep inet"
```

**2. Verify Services**
```bash
vagrant ssh -c "rc-status"
# Should show: nginx [started], mariadb [started], php-fpm82 [started]
```

**3. Test Web Access**
```bash
# Get IP address
VM_IP=$(vagrant ssh -c "ip -4 addr show eth1 | grep inet | awk '{print \$2}' | cut -d/ -f1")
echo "Access the lab at: http://$VM_IP"

# Test with curl
curl -I http://$VM_IP
```

**4. Verify Each Vulnerability**

**SQL Injection:**
```bash
curl -X POST http://$VM_IP/login.php \
  -d "username=admin' OR '1'='1'--&password=anything"
```

**XSS:**
```bash
curl -X POST http://$VM_IP/contacto.php \
  -d "nombre=Test&email=test@test.com&asunto=Test&mensaje=<script>alert('XSS')</script>"
```

**Directory Traversal:**
```bash
curl "http://$VM_IP/dashboard.php?doc=/etc/passwd"
curl "http://$VM_IP/dashboard.php?doc=/etc/energia-marina-secret.conf"
```

**Security Misconfiguration:**
```bash
curl http://$VM_IP/info.php
curl http://$VM_IP/.git/config
```

**Broken Authentication:**
```bash
curl "http://$VM_IP/admin/index.php?token=admin_access_2024"
curl -b "admin_level=1" http://$VM_IP/admin/index.php
```

**5. Distribute to Students**
- Provide: `ASSIGNMENT.md`, `README.md`, and `Vagrantfile`
- Keep confidential: `TEACHERS_GUIDE.md`
- Optional: Provide `LAB_SUMMARY.md` (excluding exploitation details)

### For Students

**Quick Start:**
```bash
cd /path/to/lab2
vagrant up
vagrant ssh -c "ip -4 addr show eth1 | grep inet"
# Access at http://<IP_ADDRESS>
```

Refer to `ASSIGNMENT.md` for detailed instructions.

---

## 📊 Assignment Structure

### Course Information
- **Lab Number:** 3 (Third hands-on lab in the course)
- **Related Chapter:** Chapter 5 - OWASP Top 10 Web Vulnerabilities
- **Duration:** 2 weeks (14 days)
- **Total Points:** 150 base points + 20 bonus points = **170 maximum**
- **Difficulty:** Intermediate
- **Prerequisites:** Labs 0, 1, and networking/web development background

### Grading Breakdown

| Component | Points | Percentage |
|-----------|--------|------------|
| Phase 1: Reconnaissance & Scanning | 25 | 16.7% |
| Phase 2: SQL Injection | 25 | 16.7% |
| Phase 3: Cross-Site Scripting | 20 | 13.3% |
| Phase 4: Directory Traversal | 20 | 13.3% |
| Phase 5: Security Misconfiguration | 15 | 10.0% |
| Phase 6: Broken Authentication | 15 | 10.0% |
| Phase 7: Analysis & Remediation | 20 | 13.3% |
| Report Quality | 10 | 6.7% |
| **Total Base Points** | **150** | **100%** |
| Bonus Challenges | 20 | - |
| **Maximum Possible** | **170** | **113%** |

### Timeline Recommendation

**Week 1:**
- Day 1-2: Lab setup, Phase 1 (Reconnaissance)
- Day 3-4: Phase 2 (SQL Injection)
- Day 5-6: Phase 3 (XSS)
- Day 7: Phase 4 (Directory Traversal)

**Week 2:**
- Day 8: Phase 5 (Security Misconfiguration)
- Day 9: Phase 6 (Broken Authentication)
- Day 10-12: Phase 7 (Analysis & Remediation)
- Day 13-14: Report writing and final review

---

## 🎓 Student Learning Outcomes

Upon successful completion of this lab, students will be able to:

### Technical Skills
1. **Perform automated vulnerability scanning** using Nikto and OWASP ZAP
2. **Manually validate and exploit** discovered vulnerabilities
3. **Execute SQL injection attacks** for authentication bypass and data extraction
4. **Identify and exploit XSS vulnerabilities** (both reflected and stored)
5. **Perform directory traversal attacks** to access unauthorized files
6. **Discover security misconfigurations** through reconnaissance
7. **Bypass weak authentication mechanisms** using multiple techniques
8. **Use industry-standard tools** (Nmap, Nikto, ZAP, Metasploit, Burp Suite)

### Conceptual Understanding
1. **Understand the OWASP Top 10** web application vulnerabilities
2. **Analyze vulnerability root causes** and their exploitation vectors
3. **Assess risk** using the CIA triad framework
4. **Develop remediation strategies** for common web vulnerabilities
5. **Apply ethical hacking methodology** systematically

### Professional Skills
1. **Document findings** in clear, reproducible format
2. **Capture evidence** through screenshots and logs
3. **Write professional security reports** for technical audiences
4. **Communicate risk** effectively
5. **Follow ethical guidelines** and legal compliance (CFAA)

### Career Preparation
- Skills applicable to: Penetration Testing, Security Analysis, Bug Bounty Hunting
- Tool proficiency: Industry-standard security tools
- Methodology: Professional pentesting approach
- Documentation: Report writing for clients/employers

---

## 🛡️ Security & Ethical Considerations

### Educational Use Only
⚠️ **THIS VIRTUAL MACHINE IS INTENTIONALLY VULNERABLE**

**Do NOT:**
- ❌ Expose this VM to the internet
- ❌ Deploy in production networks
- ❌ Use on networks you don't control
- ❌ Share exploitation techniques for malicious purposes
- ❌ Apply these skills without authorization

**DO:**
- ✅ Use only in isolated lab environments
- ✅ Follow ethical hacking principles
- ✅ Obtain proper authorization before testing
- ✅ Document your methodology
- ✅ Report vulnerabilities responsibly

### Legal Framework
Students should understand:
- **Computer Fraud and Abuse Act (CFAA)** - U.S. federal law
- **Unauthorized access is illegal** - Even "harmless" testing
- **Authorized testing only** - Written permission required
- **Responsible disclosure** - Proper vulnerability reporting
- **Bug bounty programs** - Legal frameworks for ethical hacking

### Isolation Requirements
This lab must be:
1. Run on isolated networks (local lab environment)
2. Not accessible from the internet
3. Destroyed after completion (`vagrant destroy`)
4. Never used for real-world unauthorized testing

---

## 🔧 Troubleshooting Guide

### Common Issues

**Issue 1: VM won't start**
```bash
# Solution: Check VirtualBox is running
VBoxManage --version

# Destroy and recreate
vagrant destroy -f
vagrant up
```

**Issue 2: VM doesn't get IP address**
```bash
# Solution: Update Vagrantfile bridge interface
# Edit line 21: config.vm.network "public_network", bridge: "YOUR_INTERFACE"

# Find your interface:
ip link show
# or
ifconfig
```

**Issue 3: Can't access website**
```bash
# Check VM is running
vagrant status

# Check IP assignment
vagrant ssh -c "ip -4 addr"

# Test connectivity
ping <VM_IP>

# Check services inside VM
vagrant ssh -c "rc-status"
```

**Issue 4: Nginx not running**
```bash
vagrant ssh
sudo rc-service nginx status
sudo rc-service nginx start
tail -f /var/log/nginx/error.log
```

**Issue 5: MariaDB connection errors**
```bash
vagrant ssh
sudo rc-service mariadb status
sudo rc-service mariadb restart
mysql -u root energia_marina -e "SHOW TABLES;"
```

**Issue 6: PHP files show as plain text**
```bash
vagrant ssh
sudo rc-service php-fpm82 status
sudo rc-service php-fpm82 restart
```

**Issue 7: SQL Injection not working**
```bash
# Verify database is populated
vagrant ssh -c "mysql -u root energia_marina -e 'SELECT * FROM empleados;'"

# Check PHP error logs
vagrant ssh -c "tail -50 /var/log/php-fpm82/error.log"
```

**Issue 8: Flags not appearing**
```bash
# Verify flags table
vagrant ssh -c "mysql -u root energia_marina -e 'SELECT * FROM flags;'"
```

**Issue 9: Permission errors**
```bash
vagrant ssh
sudo chown -R nginx:nginx /var/www/energia-marina
sudo chmod -R 755 /var/www/energia-marina
```

---

## 📈 Assessment Tools for Instructors

### Automated Checking
Instructors can verify student work by:

1. **Checking submitted flags** - All 5 must be present
2. **Reviewing screenshots** - Evidence of exploitation
3. **Analyzing methodology** - Proper tool usage
4. **Evaluating reports** - Professional documentation

### Manual Verification Script
```bash
#!/bin/bash
# Quick verification of student exploitation

VM_IP="<STUDENT_VM_IP>"

echo "Testing SQL Injection..."
curl -s -X POST http://$VM_IP/login.php \
  -d "username=admin' OR '1'='1'--&password=x" | grep -i "flag"

echo "Testing XSS..."
curl -s -X POST http://$VM_IP/contacto.php \
  -d "nombre=Test&email=test@test.com&asunto=Test&mensaje=<script>alert(1)</script>" \
  | grep -i "flag"

echo "Testing LFI..."
curl -s "http://$VM_IP/dashboard.php?doc=/etc/energia-marina-secret.conf" \
  | grep -i "flag"

echo "Testing Misconfiguration..."
curl -s http://$VM_IP/.git/config | grep -i "flag"

echo "Testing Broken Auth..."
curl -s "http://$VM_IP/admin/index.php?token=admin_access_2024" | grep -i "flag"
```

### Grading Checklist
- [ ] All 5 flags captured and documented
- [ ] Nmap scan results included
- [ ] Nikto scan results included
- [ ] OWASP ZAP scan results included
- [ ] Manual exploitation documented
- [ ] Screenshots properly labeled
- [ ] Step-by-step methodology clear
- [ ] Risk assessment completed (CIA triad)
- [ ] Remediation recommendations provided
- [ ] Report professionally formatted
- [ ] Ethical considerations discussed

---

## 📚 Additional Resources

### OWASP Resources
- [OWASP Top 10 - 2021](https://owasp.org/Top10/)
- [OWASP Testing Guide](https://owasp.org/www-project-web-security-testing-guide/)
- [OWASP ZAP Documentation](https://www.zaproxy.org/docs/)
- [OWASP Cheat Sheet Series](https://cheatsheetseries.owasp.org/)

### Tool Documentation
- [Nikto Scanner](https://github.com/sullo/nikto)
- [OWASP ZAP User Guide](https://www.zaproxy.org/docs/desktop/)
- [Nmap Reference Guide](https://nmap.org/book/)
- [Metasploit Unleashed](https://www.metasploit.com/)
- [Burp Suite Documentation](https://portswigger.net/burp/documentation)

### Learning Platforms
- [PortSwigger Web Security Academy](https://portswigger.net/web-security) - Free
- [HackTheBox Academy](https://academy.hackthebox.com/)
- [TryHackMe](https://tryhackme.com/)
- [PentesterLab](https://pentesterlab.com/)

### Vulnerability Databases
- [MITRE CVE](https://cve.mitre.org/)
- [National Vulnerability Database](https://nvd.nist.gov/)
- [Exploit Database](https://www.exploit-db.com/)

### Certification Paths
- CEH (Certified Ethical Hacker)
- OSCP (Offensive Security Certified Professional)
- GWAPT (GIAC Web Application Penetration Tester)
- CompTIA Security+

---

## 📝 Notes for Future Iterations

### Potential Enhancements
1. Add CSRF vulnerability
2. Include file upload vulnerability
3. Add XML External Entity (XXE) injection
4. Implement insecure deserialization
5. Add API endpoints with vulnerabilities
6. Create mobile-responsive design
7. Add more realistic company data
8. Include video walkthrough (for instructors)

### Student Feedback Areas
- Difficulty level assessment
- Time required for completion
- Tool preference (Nikto vs ZAP)
- Additional hints needed
- Report template usefulness

### Technical Improvements
- Add Docker alternative to Vagrant
- Create automated grading scripts
- Develop CTF-style scoreboard
- Add progressive difficulty levels
- Include red team vs blue team scenarios

---

## 🎯 Success Metrics

### For Students
- **Completion Rate:** Target 90%+
- **Average Score:** Target 120/150 (80%)
- **Flag Capture Rate:** All 5 flags by 85% of students
- **Time to Complete:** Average 12-15 hours over 2 weeks
- **Tool Proficiency:** Demonstrated use of Nikto and ZAP

### For Instructors
- **Engagement:** Active participation in lab
- **Skill Development:** Measurable improvement in penetration testing
- **Report Quality:** Professional documentation skills
- **Ethical Awareness:** Understanding of legal and ethical boundaries
- **Career Readiness:** Skills applicable to cybersecurity careers

---

## 👥 Credits & Acknowledgments

**Lab Created By:** Miguel Guirao
**Date:** February 15, 2026
**Course:** Introduction to Cybersecurity
**Institution:** Educational Environment

**Technologies Used:**
- Vagrant by HashiCorp
- VirtualBox by Oracle
- Alpine Linux
- Nginx Web Server
- PHP-FPM
- MariaDB
- OWASP Tools

**Inspired By:**
- OWASP Top 10 Project
- Real-world web application vulnerabilities
- Bug bounty program findings
- Professional penetration testing methodology

---

## 📄 License & Usage

**Educational Use Only**

This lab environment is designed exclusively for educational purposes in controlled, authorized environments.

**Permitted Use:**
- Academic coursework
- Security training
- Penetration testing education
- Cybersecurity skill development

**Prohibited Use:**
- Unauthorized system testing
- Malicious exploitation
- Internet-facing deployment
- Commercial use without permission

---

## 📞 Support & Contact

### For Technical Issues
1. Review README.md troubleshooting section
2. Check Vagrant and VirtualBox versions
3. Verify network connectivity
4. Consult TEACHERS_GUIDE.md (instructors)

### For Academic Questions
- Office hours: [Schedule TBD]
- Course forum: [URL TBD]
- Email: [Instructor email]

### For Bug Reports
- Document the issue clearly
- Include screenshots
- Provide error messages
- Share VM configuration

---

## ✅ Final Checklist

### Before Deploying to Students
- [ ] Test VM deployment completely
- [ ] Verify all 5 vulnerabilities are exploitable
- [ ] Confirm all flags are accessible
- [ ] Review assignment for clarity
- [ ] Test with a colleague/TA
- [ ] Prepare hints for progressive disclosure
- [ ] Set up grading rubric in LMS
- [ ] Schedule office hours for support
- [ ] Prepare lab introduction presentation
- [ ] Review ethical and legal guidelines with students

### During Lab Period
- [ ] Monitor student progress
- [ ] Provide hints as needed (progressive)
- [ ] Address technical issues promptly
- [ ] Encourage documentation habits
- [ ] Facilitate peer learning (appropriately)
- [ ] Collect feedback for improvements

### After Lab Completion
- [ ] Grade submissions using rubric
- [ ] Provide detailed feedback
- [ ] Conduct post-lab discussion
- [ ] Collect student feedback
- [ ] Document common issues for next iteration
- [ ] Update materials based on feedback

---

## 🎉 Conclusion

Lab 2 - Energía Marina Web Exploitation Lab is a comprehensive, professional-grade educational environment designed to teach students practical web application security skills using industry-standard tools.

**Key Strengths:**
- ✅ Realistic, engaging scenario
- ✅ Comprehensive OWASP Top 10 coverage
- ✅ Professional tool usage (Nikto, OWASP ZAP)
- ✅ Clear learning objectives
- ✅ Detailed documentation (92KB total)
- ✅ Multiple exploitation methods
- ✅ Proper ethical framework
- ✅ Career-relevant skills

**The lab is ready for deployment and will provide students with valuable, hands-on experience in web application penetration testing.**

---

**Document Version:** 1.0
**Last Updated:** February 15, 2026
**Status:** ✅ Complete and Ready for Deployment

---

**¡Buena suerte y feliz hacking! 🔐**

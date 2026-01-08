# Liverpul Corp VM - Complete Lab Package

## 📚 Documentation Overview

This lab package contains everything needed for a comprehensive penetration testing exercise.

---

## 🎯 For Students

### Start Here
1. **[QUICK_START.md](QUICK_START.md)** - Begin here! Setup instructions and first steps
2. **[LAB_EXERCISES.md](LAB_EXERCISES.md)** - Complete exercise descriptions (10 exercises)
3. **[CHEAT_SHEET.md](CHEAT_SHEET.md)** - Command reference and syntax help
4. **[README.md](README.md)** - VM specifications and service information

### Don't Open Yet!
- ❌ **SOLUTIONS.md** - Only after completing exercises or with instructor permission

---

## 👨‍🏫 For Instructors

### Teaching Materials
1. **[LAB_EXERCISES.md](LAB_EXERCISES.md)** - Full exercise descriptions with learning objectives
2. **[SOLUTIONS.md](SOLUTIONS.md)** - Complete solutions guide with expected findings
3. **[README.md](README.md)** - Technical specifications and troubleshooting
4. **[CHEAT_SHEET.md](CHEAT_SHEET.md)** - Can be shared with students as reference

### VM Management
- **Vagrantfile** - VM configuration and provisioning
- **sample_wordlists/** - Sample username and password files for students

---

## 📖 Document Descriptions

### QUICK_START.md (9.1 KB)
**Purpose:** Get students running in 10 minutes
**Contents:**
- Prerequisites and tool installation
- VM startup instructions
- First commands to run
- Quick troubleshooting
- Success checklist

**Use Case:** Hand this to students at the start of lab

---

### LAB_EXERCISES.md (19 KB)
**Purpose:** Complete lab curriculum
**Contents:**
- 10 progressive exercises (Beginner → Advanced)
- Learning objectives for each exercise
- Task descriptions
- Expected findings
- Tool recommendations
- Questions to answer
- Bonus challenges
- Hardening checklist
- Grading rubric

**Exercises Covered:**
1. Network Reconnaissance (Beginner)
2. FTP Enumeration (Beginner-Intermediate)
3. Password Cracking (Intermediate)
4. Web Application Analysis (Intermediate)
5. Database Exploitation (Intermediate-Advanced)
6. SSH Access (Advanced)
7. Privilege Escalation (Advanced)
8. Network Traffic Analysis (Intermediate)
9. Comprehensive Security Assessment (Advanced)
10. Post-Exploitation (Advanced)

**Use Case:** Primary lab manual for students

---

### CHEAT_SHEET.md (14 KB)
**Purpose:** Quick command reference during lab
**Contents:**
- Reconnaissance commands
- FTP enumeration techniques
- SSH attacks
- Web application testing
- MySQL enumeration
- Password cracking syntax
- Privilege escalation commands
- Network traffic analysis
- Tool installation
- Common mistakes to avoid

**Use Case:** Students keep this open while working

---

### SOLUTIONS.md (17 KB)
**Purpose:** Instructor answer guide
**Contents:**
- All credentials (pedro:football, pablo:welcome, paco:sunshine)
- Complete solutions for all 10 exercises
- Expected command outputs
- Sample screenshots descriptions
- Common student mistakes
- Grading rubric
- Teaching notes

**Use Case:** Instructor reference (keep confidential!)

---

### README.md (3.8 KB)
**Purpose:** VM documentation
**Contents:**
- VM specifications (Alpine Linux, 192.168.56.10)
- Service descriptions (MySQL, FTP, Nginx)
- User credentials table
- Usage commands (vagrant up/halt/destroy)
- Testing procedures
- Troubleshooting

**Use Case:** Quick reference for VM details

---

### Vagrantfile (17 KB)
**Purpose:** VM configuration and provisioning
**Contents:**
- Alpine Linux 3.18 base box
- Network configuration (192.168.56.10)
- Service installation (MySQL, vsFTPd, Nginx, PHP)
- User creation with weak passwords
- Database setup with sample data
- Website deployment with Angular Material
- Automated provisioning script

**Use Case:** VM deployment (vagrant up)

---

## 🎓 Suggested Lab Formats

### Format 1: Full Course Lab (6-8 hours)
**Recommended for:** University courses, bootcamps
**Coverage:** All exercises 1-10
**Deliverable:** Complete penetration test report

**Timeline:**
- Session 1 (3-4 hours): Exercises 1-5
- Session 2 (3-4 hours): Exercises 6-10

---

### Format 2: Quick Assessment (2-3 hours)
**Recommended for:** Workshops, short labs
**Coverage:** Exercises 1-5 only
**Deliverable:** Findings summary

**Timeline:**
- 30 min: Reconnaissance
- 30 min: Password attacks
- 30 min: Database exploitation
- 30 min: Web testing
- 30 min: Documentation

---

### Format 3: Advanced Only (2-3 hours)
**Recommended for:** Advanced students
**Coverage:** Exercises 6-10
**Deliverable:** Privilege escalation report

**Prerequisites:** Students already know basic enumeration

---

### Format 4: Capture The Flag (1-2 hours)
**Recommended for:** Competitions
**Coverage:** Timed challenge
**Deliverable:** Flags found

**Flags to Plant:**
- Flag 1: In FTP anonymous directory
- Flag 2: In database table
- Flag 3: In web source code
- Flag 4: In user home directory (SSH access required)
- Flag 5: In root directory (privilege escalation required)

---

## 🔧 Setup for Instructors

### Initial Setup (One Time)
```bash
# Clone or copy lab directory
cd /path/to/infosec/vm_labs/lab3

# Test VM deployment
vagrant up

# Verify all services
vagrant ssh -c "sudo rc-service --list | grep -E '(mariadb|nginx|vsftpd)'"

# Test from host
nmap 192.168.56.10
curl http://192.168.56.10
```

### Before Each Lab Session
```bash
# Ensure VM is fresh
vagrant destroy -f
vagrant up

# Verify services are running
./verify_services.sh  # (create this if needed)

# Share with students:
# - IP address: 192.168.56.10
# - QUICK_START.md
# - LAB_EXERCISES.md
# - CHEAT_SHEET.md
# - sample_wordlists/
```

### During Lab Session
- Monitor student progress
- Answer questions (without giving solutions)
- Troubleshoot VM issues
- Verify students are documenting work
- Remind about time limits

### After Lab Session
```bash
# Collect student reports
# Grade using rubric in SOLUTIONS.md
# Provide feedback
# Reset VM for next session
vagrant destroy -f
```

---

## 📊 Learning Outcomes

### Students Will Learn To:
1. ✅ Perform network reconnaissance with nmap
2. ✅ Enumerate services (FTP, SSH, HTTP, MySQL)
3. ✅ Conduct password attacks using Hydra
4. ✅ Exploit weak credentials
5. ✅ Analyze web applications for vulnerabilities
6. ✅ Exploit database misconfigurations
7. ✅ Gain system access via SSH
8. ✅ Perform Linux privilege escalation
9. ✅ Analyze network traffic with Wireshark
10. ✅ Write professional penetration test reports

### Skills Developed:
- Tool proficiency (nmap, hydra, mysql, curl, etc.)
- Methodical enumeration
- Exploit selection
- Documentation skills
- Report writing
- Security awareness
- Ethical hacking mindset

---

## 🎯 Target Vulnerability Summary

### Critical Vulnerabilities
1. **Weak Passwords** - Dictionary words used
2. **Anonymous FTP** - Unauthenticated access enabled
3. **MySQL Remote Access** - Database exposed to network
4. **Default Credentials** - admin:admin for database

### High Vulnerabilities
5. **No HTTPS** - All traffic in cleartext
6. **API Information Disclosure** - Database schema exposed
7. **No Input Validation** - Potential SQL injection
8. **Missing Security Headers** - Web server misconfiguration

### Medium Vulnerabilities
9. **No Rate Limiting** - Susceptible to brute force
10. **Verbose Error Messages** - Information leakage

---

## 📈 Difficulty Progression

```
Beginner (Exercises 1-2)
    ↓
Intermediate (Exercises 3-5)
    ↓
Advanced (Exercises 6-10)
```

**Prerequisites:**
- Basic Linux command line
- Understanding of TCP/IP
- Familiarity with common protocols

**No Prerequisites For:**
- Specific tool knowledge (covered in exercises)
- Programming skills (not required)
- Advanced networking (basic knowledge sufficient)

---

## 🛠️ Technical Requirements

### For Students
**Operating System:**
- Kali Linux (recommended)
- Parrot Security OS
- Any Linux with pentesting tools
- macOS with Homebrew

**Tools Required:**
- nmap
- hydra
- mysql-client
- curl
- ftp

**Tools Optional:**
- Burp Suite / OWASP ZAP
- Wireshark
- SQLMap
- Nikto
- Metasploit

**Hardware:**
- 4GB RAM minimum (8GB recommended)
- 20GB free disk space
- Network adapter

### For Instructors
**Additional Requirements:**
- VirtualBox 6.0+
- Vagrant 2.0+
- Host machine with 8GB+ RAM
- Ability to create private networks

---

## 📝 Grading and Assessment

### Quick Assessment Rubric
- **Completion:** 40% - All exercises attempted
- **Technical Accuracy:** 30% - Correct findings
- **Documentation:** 20% - Clear, organized report
- **Analysis:** 10% - Understanding demonstrated

### Detailed Rubric
See SOLUTIONS.md for point breakdown by exercise

### Report Requirements
1. Executive Summary (1 page)
2. Methodology (1-2 pages)
3. Technical Findings (3-5 pages)
4. Evidence (screenshots, command output)
5. Recommendations (1-2 pages)

---

## 🔒 Security and Ethics

### Lab Safety
- ✅ VM isolated on private network
- ✅ No real user data
- ✅ Educational purpose only
- ✅ Controlled environment

### Ethical Guidelines
Students must:
- Only test the lab VM (192.168.56.10)
- Not use techniques on unauthorized systems
- Understand legal implications
- Practice responsible disclosure
- Remove persistence mechanisms after testing

### Legal Disclaimer
This lab is for educational purposes. Unauthorized access to computer systems is illegal. Students are responsible for using these techniques ethically and legally.

---

## 🐛 Common Issues and Solutions

### VM Won't Start
```bash
# Check VirtualBox
vboxmanage list vms

# Check Vagrant
vagrant global-status

# Complete reset
vagrant destroy -f && vagrant up
```

### Services Not Accessible
```bash
# Verify VM IP
vagrant ssh -c "ip addr show eth1"

# Check services
vagrant ssh -c "sudo rc-service --list"

# Restart services
vagrant reload --provision
```

### Students Stuck
- Direct to relevant section in CHEAT_SHEET.md
- Ask what they've tried
- Check their methodology
- Verify they're targeting correct IP
- Review command syntax

---

## 📞 Support and Updates

### For Issues
1. Check README.md troubleshooting section
2. Review SOLUTIONS.md for expected behavior
3. Test VM yourself to verify issue
4. Check Vagrant/VirtualBox logs

### Updates and Improvements
To modify the lab:
1. Edit Vagrantfile
2. Test changes with `vagrant destroy -f && vagrant up`
3. Update documentation to match
4. Increment version numbers

---

## 📦 Distribution Checklist

### For Students, Provide:
- [ ] QUICK_START.md
- [ ] LAB_EXERCISES.md
- [ ] CHEAT_SHEET.md
- [ ] README.md
- [ ] sample_wordlists/
- [ ] Vagrantfile
- [ ] VM access instructions

### Do NOT Provide to Students:
- [ ] SOLUTIONS.md
- [ ] This INDEX.md (optional)

---

## 📚 Additional Resources

### Recommended Reading
- "The Web Application Hacker's Handbook"
- "Penetration Testing: A Hands-On Introduction"
- OWASP Testing Guide

### Online Resources
- HackTheBox
- TryHackMe
- PortSwigger Web Security Academy
- OverTheWire Wargames

### Tool Documentation
- Nmap: https://nmap.org/book/
- Hydra: https://github.com/vanhauser-thc/thc-hydra
- SQLMap: https://github.com/sqlmapproject/sqlmap

---

## 🎓 Course Integration

### Suggested Course Topics
This lab reinforces:
- Network security fundamentals
- Penetration testing methodology
- Web application security
- Database security
- Linux system administration
- Security assessment and reporting

### Prerequisite Lectures
Before this lab:
- TCP/IP networking basics
- Common network services
- Authentication mechanisms
- SQL fundamentals
- Linux command line basics

### Follow-Up Topics
After this lab:
- Secure coding practices
- Defense-in-depth strategies
- Incident response
- Security monitoring
- Compliance and regulations

---

## 📈 Success Metrics

### Lab Success Indicators
- 90%+ students complete basic exercises (1-5)
- 70%+ students complete advanced exercises (6-10)
- Average report score > 75%
- Positive student feedback
- Students demonstrate learned skills

### Student Success Indicators
- Can explain vulnerabilities found
- Can recommend specific fixes
- Understands attack methodology
- Documents findings clearly
- Demonstrates ethical awareness

---

## 🔄 Version History

- **v1.0** (2026-01-03): Initial release
  - 10 exercises covering beginner to advanced topics
  - Complete documentation package
  - Automated VM provisioning
  - Sample wordlists and cheat sheet

---

## 📄 File Summary

```
lab3/
├── QUICK_START.md          [9.1 KB]  - Student starting point
├── LAB_EXERCISES.md        [19 KB]   - Complete lab manual
├── CHEAT_SHEET.md          [14 KB]   - Command reference
├── SOLUTIONS.md            [17 KB]   - Instructor guide (confidential)
├── README.md               [3.8 KB]  - VM documentation
├── INDEX.md                [This]    - Complete overview
├── Vagrantfile             [17 KB]   - VM configuration
└── sample_wordlists/
    ├── usernames.txt       [68 B]    - Sample usernames
    └── passwords.txt       [126 B]   - Sample passwords
```

**Total Package Size:** ~80 KB documentation + VM image (~400 MB)

---

## 🚀 Quick Start for Instructors

```bash
# 1. Start VM
vagrant up

# 2. Verify services
curl http://192.168.56.10
mysql -h 192.168.56.10 -u admin -padmin -e "SHOW DATABASES;"

# 3. Distribute to students:
#    - QUICK_START.md
#    - LAB_EXERCISES.md
#    - CHEAT_SHEET.md
#    - sample_wordlists/

# 4. Keep confidential:
#    - SOLUTIONS.md

# 5. Monitor lab progress

# 6. After lab:
vagrant destroy -f
```

---

## ✅ Pre-Lab Checklist

### Instructor Checklist
- [ ] VM tested and working
- [ ] All services accessible
- [ ] Documentation reviewed
- [ ] Grading rubric prepared
- [ ] Time allocated (4-6 hours)
- [ ] Student materials prepared
- [ ] Lab environment configured
- [ ] Backup plan for technical issues

### Student Checklist
- [ ] Tools installed
- [ ] VM accessible
- [ ] Documentation downloaded
- [ ] Workspace prepared
- [ ] Notes file ready
- [ ] Screenshot tool working
- [ ] Sufficient time allocated
- [ ] Ethics guidelines understood

---

**END OF INDEX**

---

**Quick Navigation:**
- Students: Start with [QUICK_START.md](QUICK_START.md)
- Instructors: Review [SOLUTIONS.md](SOLUTIONS.md) and [LAB_EXERCISES.md](LAB_EXERCISES.md)
- Reference: Use [CHEAT_SHEET.md](CHEAT_SHEET.md) during lab
- Details: See [README.md](README.md) for VM specifications

# Lab 1: Web Exploitation & Reconnaissance
## Introduction to Vulnerability Assessment and Exploitation

**Course:** Introduction to Cybersecurity
**Lab Number:** 2
**Related Reading:** Chapter 4 - Web Application Security
**Duration:** 1 Week
**Points:** 100

---

## The Mission

*October 2021 - Security researchers discovered a critical vulnerability in Apache HTTP Server, one of the most widely used web servers on the internet. CVE-2021-41773, a path traversal vulnerability, allowed attackers to read sensitive files outside the web server's intended directory. Within days of disclosure, threat actors began scanning the internet for vulnerable servers, attempting to steal configuration files, credentials, and sensitive data from organizations worldwide.*

*Bug bounty hunter "SSD Disclosure" reported this vulnerability, demonstrating how a simple malformed request could bypass security controls and expose the entire filesystem. The vulnerability was so severe that CISA (Cybersecurity and Infrastructure Security Agency) added it to their Known Exploited Vulnerabilities catalog, urging organizations to patch immediately.*

*Fast forward to today—You are a junior penetration tester at SecureOps Consulting. Your team has been hired by a client to perform a security assessment of their web infrastructure. Your manager has set up a replica of a potentially vulnerable web server in an isolated lab environment. Your mission: conduct reconnaissance, identify vulnerabilities, and demonstrate the risk by safely capturing a hidden flag.*

**Remember:** What separates cybercriminals from ethical hackers isn't technical skill—it's authorization, methodology, and professionalism. You have explicit permission to test this system. Document everything.

---

## Learning Objectives

By completing this lab, you will:

1. **Master Reconnaissance Techniques**
   - Conduct network enumeration to identify active services
   - Perform banner grabbing and service fingerprinting
   - Analyze server responses to identify potential vulnerabilities

2. **Apply Ethical Hacking Methodology**
   - Follow a structured approach to penetration testing
   - Document findings systematically
   - Think like an attacker while acting as a defender

3. **Understand Real-World Vulnerabilities**
   - Investigate CVE-2021-41773 and its impact
   - Understand path traversal vulnerabilities
   - Learn how misconfigurations lead to security breaches

4. **Develop Professional Reporting Skills**
   - Create clear, reproducible documentation
   - Capture evidence through screenshots
   - Communicate technical findings effectively

---

## 🛠Prerequisites & Tools

Before starting this lab, you should be familiar with the following tools and concepts. If you need a refresher, take time to research them:

### Required Tools
- **Vagrant & VirtualBox** - Virtual machine management
- **Nmap** - Network scanning and service enumeration
- **cURL or Wget** - HTTP request manipulation
- **FTP Client** (ftp command or FileZilla) - FTP service interaction
- **Web Browser** - Basic web reconnaissance
- **Text Editor** - Documentation

### Topics to Research
- **CVE (Common Vulnerabilities and Exposures)** - What they are and how to look them up
- **Path Traversal Vulnerabilities** - How they work and common patterns
- **HTTP Request Methods** - GET, POST, and how web servers process requests
- **Network Protocols** - HTTP, FTP, and their default ports
- **Banner Grabbing** - Identifying service versions
- **Ethical Hacking Methodology** - Standard phases of penetration testing

### Useful Resources
- **Class Textbook - Chapter 4:** Review the chapter on Web Application Security before starting this lab
- National Vulnerability Database (NVD): https://nvd.nist.gov/
- MITRE CVE Database: https://cve.mitre.org/
- Apache HTTP Server Documentation

---

## Lab Setup Instructions

### Step 1: Environment Preparation

1. **Navigate to the lab directory:**
   ```bash
   cd /path/to/lab1
   ```

2. **Review the Vagrantfile:**
   - In case you can not connect to your VM
   - Open and read the `Vagrantfile` to understand the lab environment
   - **IMPORTANT:** Check line 21 - you may need to change the bridged network interface
   - Find your network interface: `ip link show` or `ifconfig`
   - Update the bridge parameter if necessary: `bridge: "your-interface-name"`

3. **Start the vulnerable VM:**
   ```bash
   vagrant up
   ```
   ⏱️**Note:** Initial setup takes 10-15 minutes (Apache compilation from source)

4. **Identify the VM's IP address:**
   ```bash
   vagrant ssh -c "ip -4 addr show eth1 | grep inet"
   ```
   **Write down the IP address** - you'll need it for all subsequent steps!

5. **Verify connectivity from your host machine:**
   ```bash
   ping <VM_IP_ADDRESS>
   ```

---

## Lab Challenges

### Phase 1: Reconnaissance (25 points)

**"Knowing is half the battle."** Before exploiting any system, professional penetration testers spend significant time gathering information. This phase is critical to understanding your target.

#### Your Tasks:

1. **Network Discovery**
   - Perform a comprehensive port scan of the target VM
   - Identify all open ports and running services
   - Document service versions and banners

2. **Service Enumeration**
   - Investigate the web service:
     - What web server software is running?
     - What version is installed?
     - Are there any interesting pages or directories?

   - Investigate the FTP service:
     - Does it allow anonymous access?
     - What files or directories are accessible?
     - Is there any relationship between FTP and other services?

3. **Vulnerability Research**
   - Once you identify the web server version, research known vulnerabilities
   - Look up CVEs associated with this specific version
   - Find proof-of-concept exploits or documentation

#### Deliverables for Phase 1:
- Screenshot of port scan results
- Screenshot of web server banner/version
- Screenshot of FTP service interaction
- Brief write-up (1-2 paragraphs) explaining what you discovered and which CVEs appear relevant

---

### Phase 2: Exploitation (40 points)

**"With great power comes great responsibility."** You've identified potential vulnerabilities. Now demonstrate the security risk in a controlled, documented manner.

#### Your Tasks:

1. **Analyze CVE-2021-41773**
   - Research how this path traversal vulnerability works
   - Understand what makes Apache 2.4.49 vulnerable
   - Find or craft HTTP requests that exploit this vulnerability

2. **Attempt Safe Exploitation**
   - Try to access files outside the web root directory
   - Test different path traversal techniques
   - Document which methods work and which don't

3. **Capture the Flag**
   - Locate and retrieve the hidden flag
   - The flag is accessible through the vulnerable web server
   - **Hint:** The flag is a 32-character hexadecimal string

4. **Document the Attack Chain**
   - Show exact commands/requests used
   - Explain why they worked
   - Demonstrate repeatability

#### Hints:
- Path traversal often involves special characters like `../` to navigate directories
- The vulnerability in Apache 2.4.49 requires specific URL encoding techniques
- Try accessing system files like `/etc/passwd` first to confirm the vulnerability
- Remember, the flag file is mentioned in the README.md
- Different HTTP methods might behave differently with this vulnerability

#### Deliverables for Phase 2:
- Screenshots showing successful exploitation attempts
- Screenshot of the captured flag
- Step-by-step documentation of your exploitation process
- Explanation of why the vulnerability exists and how your exploit works

---

### Phase 3: Analysis & Remediation (25 points)

**"A good penetration tester doesn't just break things—they help fix them."** Understanding how to remediate vulnerabilities is crucial for defending systems.

#### Your Tasks:

1. **Vulnerability Analysis**
   - Explain in your own words how CVE-2021-41773 works
   - What specific Apache configuration or code flaw enables this attack?
   - Why is this vulnerability rated as critical?

2. **Risk Assessment**
   - What sensitive information could an attacker access?
   - What is the potential impact on confidentiality, integrity, and availability?
   - In a real-world scenario, what data might be at risk?

3. **Remediation Recommendations**
   - How should this vulnerability be fixed?
   - What is the recommended Apache version to upgrade to?
   - What additional security controls could prevent exploitation?
   - Are there any configuration changes that could mitigate the risk?

4. **Lessons Learned**
   - What did you learn about reconnaissance techniques?
   - How does this lab relate to real-world security incidents?
   - What surprised you during this exercise?

#### Deliverables for Phase 3:
- Vulnerability analysis (2-3 paragraphs)
- Risk assessment with CIA triad impact
- Detailed remediation recommendations
- Personal reflection (1-2 paragraphs)

---

## Submission Requirements

### Report Format

Submit a **single PDF document** containing:

1. **Cover Page**
   - Your name and student ID
   - Course name and lab number
   - Submission date

2. **Executive Summary** (1 paragraph)
   - High-level overview of what you discovered

3. **Detailed Findings** (Organized by Phase)
   - Phase 1: Reconnaissance
   - Phase 2: Exploitation
   - Phase 3: Analysis & Remediation

4. **Appendix**
   - All screenshots (properly labeled and captioned)
   - Command history or scripts used
   - The captured flag

### Screenshot Requirements

Every screenshot must include:
- Your terminal/tool showing the command executed
- The output/result
- Timestamp (if possible)
- Clear caption explaining what the screenshot demonstrates

**Minimum required screenshots:**
- Nmap scan results
- Service version identification
- FTP enumeration
- Successful exploitation attempt
- Captured flag
- Any additional proof of your work

### Writing Guidelines

- Use **clear, professional language**
- Write in **past tense** ("I performed...", "The scan revealed...")
- Be **specific and technical** - include commands, IP addresses, and exact steps
- **Explain your reasoning** - don't just show what you did, explain why
- Make your work **reproducible** - another student should be able to follow your steps

---

## Grading Rubric (100 points total)

| Component | Points | Criteria |
|-----------|--------|----------|
| **Phase 1: Reconnaissance** | 25 | |
| Port scanning & service discovery | 10 | Complete scan with all services identified |
| Banner grabbing & version detection | 8 | Correct version information for web and FTP services |
| Vulnerability research | 7 | Identified CVE-2021-41773 and explained relevance |
| **Phase 2: Exploitation** | 40 | |
| Vulnerability understanding | 10 | Demonstrates clear understanding of path traversal |
| Exploitation methodology | 15 | Shows working exploit with proper documentation |
| Flag capture | 10 | Successfully retrieved the flag |
| Documentation quality | 5 | Clear, step-by-step process with explanations |
| **Phase 3: Analysis** | 25 | |
| Vulnerability analysis | 8 | Thorough explanation of CVE-2021-41773 |
| Risk assessment | 7 | Proper CIA triad analysis and impact description |
| Remediation recommendations | 7 | Practical, specific mitigation strategies |
| Personal reflection | 3 | Thoughtful insights on learning experience |
| **Report Quality** | 10 | |
| Professional formatting | 3 | Well-organized, clear structure |
| Screenshot quality | 4 | All required screenshots, properly labeled |
| Writing clarity | 3 | Clear, technical, professional writing |

### Deductions
- **-10 points**: Missing required screenshots
- **-5 points**: Late submission (per day, up to 3 days)
- **-15 points**: Incomplete flag or fabricated results
- **-5 points**: Poor formatting or unprofessional presentation

---

## ⚠️Important Reminders

### Ethical Guidelines

This lab is conducted in an **isolated, authorized environment**. The skills you learn here must only be applied ethically:

- **DO:** Practice on systems you own or have explicit permission to test
- **DO:** Document your methodology professionally
- **DO:** Think like an attacker to better defend systems
- **DON'T:** Scan or exploit systems without authorization
- **DON'T:** Share exploitation techniques for malicious purposes
- **DON'T:** Use these skills outside of educational or authorized contexts

**Violation of these principles may result in academic discipline and legal consequences.**

### Lab Safety

- This VM is **intentionally vulnerable** - keep it isolated
- Do **NOT** expose this VM to the internet
- Use **only** on your local network in a controlled environment
- When finished, stop the VM: `vagrant halt`
- To destroy the VM: `vagrant destroy`

### Getting Help

If you encounter technical issues:
1. Check that your network interface in Vagrantfile is correct
2. Verify the VM received an IP address
3. Ensure you can ping the VM from your host
4. Review error messages carefully
5. Visit office hours or post on the course forum

**Collaboration Policy:** You may discuss general concepts with classmates, but your report and exploitation work must be your own. Do not share commands, flags, or screenshots.

---

## Timeline

| Milestone | Recommended Completion |
|-----------|----------------------|
| Lab setup & VM deployment | Day 1 |
| Phase 1: Reconnaissance | Day 2-3 |
| Phase 2: Exploitation | Day 4-5 |
| Phase 3: Analysis & Report Writing | Day 6-7 |
| **Final Submission Deadline** | **End of Week 1** |

---

## Bonus Challenge (Optional - 5 Extra Points)

For students seeking an additional challenge:

Research and demonstrate **at least two different methods** of exploiting CVE-2021-41773. Compare their effectiveness and explain the differences in your report.

---

## Additional Resources

- **CVE-2021-41773 Official Advisory:** https://nvd.nist.gov/vuln/detail/CVE-2021-41773
- **OWASP Path Traversal:** https://owasp.org/www-community/attacks/Path_Traversal
- **Apache HTTP Server Security Tips:** https://httpd.apache.org/docs/2.4/misc/security_tips.html
- **FTP Protocol Basics:** RFC 959
- **Nmap Documentation:** https://nmap.org/book/man.html

---

## Conclusion

This lab introduces you to the fundamental skills of ethical hacking: **reconnaissance, exploitation, and analysis**. The path traversal vulnerability you'll exploit (CVE-2021-41773) affected thousands of real-world servers and represents the kind of critical flaws that penetration testers discover daily.

Your ability to think like an attacker—while maintaining ethical standards—will make you a better defender of digital systems. Approach this lab with curiosity, document thoroughly, and remember: every expert was once a beginner who refused to give up.

**Good luck, and happy hacking! **

---

*This lab is designed for educational purposes only. All activities must be performed on the provided VM with explicit authorization. Unauthorized computer access is illegal and unethical.*

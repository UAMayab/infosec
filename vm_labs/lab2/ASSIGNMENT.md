# Lab 2: Energía Marina - Web Application Security Assessment
## Advanced Web Exploitation & OWASP Top 10

**Course:** Introduction to Cybersecurity
**Lab Number:** 3
**Related Reading:** Chapter 5 - OWASP Top 10
**Duration:** 2 Weeks
**Points:** 150

---

## The Mission

*March 2023 - A major Mexican energy company suffered a devastating data breach. Attackers exploited multiple web vulnerabilities in their employee portal, gaining access to geological survey data, drilling plans, and confidential contracts worth millions. The breach went undetected for three months. When discovered, investigators found a trail of exploitation: SQL injection for initial access, XSS for credential harvesting, directory traversal for data exfiltration, and misconfigurations that exposed the company's entire source code repository.*

*The attackers left a calling card—five flags hidden throughout the compromised system, each marking a different vulnerability they exploited. The incident cost the company $12 million in damages, regulatory fines, and reputational harm. The breach made headlines across Latin America and prompted emergency security audits across the entire energy sector.*

*Present day—You are a security consultant hired by **Energía Marina S.A. de C.V.**, a petroleum company based in Veracruz, Mexico. After hearing about the breach at their competitor, Energía Marina's executives are concerned about their own web security posture. They've authorized a comprehensive penetration test of their employee portal system.*

*Your mission: Conduct a thorough security assessment using industry-standard automated scanning tools (Nikto and OWASP ZAP) combined with manual exploitation techniques. Identify vulnerabilities, capture five hidden flags proving exploitability, and provide detailed remediation guidance. The company's security team has replicated their production environment in an isolated lab for your assessment.*

**Remember:** Professional penetration testers combine automated tools with manual expertise. Scanners find the doors; skilled hackers know which ones to open. You have explicit authorization. Document everything methodically.

---

## Learning Objectives

By completing this lab, you will:

1. **Master Web Vulnerability Scanning**
   - Deploy and configure Nikto for comprehensive web server assessment
   - Utilize OWASP ZAP for automated and manual web application testing
   - Interpret scanner results and prioritize findings
   - Understand the strengths and limitations of automated tools

2. **Exploit OWASP Top 10 Vulnerabilities**
   - SQL Injection (A03:2021) - Bypass authentication and extract data
   - Cross-Site Scripting (A03:2021) - Inject malicious scripts
   - Directory Traversal/LFI (A01:2021) - Access unauthorized files
   - Security Misconfiguration (A05:2021) - Identify dangerous exposures
   - Broken Authentication (A07:2021) - Compromise admin access

3. **Develop Professional Methodology**
   - Follow structured penetration testing phases
   - Combine automated scanning with manual validation
   - Create reproducible proof-of-concept exploits
   - Think critically about vulnerability impact and chaining

4. **Build Comprehensive Reporting Skills**
   - Document technical findings with precision
   - Assess risk using industry frameworks
   - Provide actionable remediation guidance
   - Communicate effectively to technical and non-technical audiences

---

## Prerequisites & Tools

Before starting this lab, you should be familiar with the following tools and concepts. If you need a refresher, take time to research them:

### Required Tools

- **Vagrant & VirtualBox** - Virtual machine management
- **Nmap** - Network scanning and service enumeration
- **Nikto** - Open-source web server vulnerability scanner
- **OWASP ZAP (Zed Attack Proxy)** - Web application security testing tool
- **Web Browser with DevTools** - Firefox or Chrome with developer tools
- **cURL or Burp Suite** - HTTP request manipulation
- **sqlmap** (optional) - Automated SQL injection testing
- **Text Editor** - Professional documentation

### Topics to Research

- **OWASP Top 10** - The most critical web application security risks
- **SQL Injection** - Types, impact, and exploitation techniques
- **Cross-Site Scripting (XSS)** - Reflected, stored, and DOM-based XSS
- **Directory Traversal** - Path manipulation and file inclusion vulnerabilities
- **Security Misconfiguration** - Information disclosure, default credentials, exposed files
- **Authentication Vulnerabilities** - Session management, weak credentials, bypass techniques
- **HTTP Protocol** - Request/response structure, methods, headers, cookies
- **Web Application Architecture** - PHP, databases, server-side processing
- **Information Gathering** - Passive and active reconnaissance techniques

### Useful Resources

- **Class Textbook - Chapter 5:** Review the OWASP Top 10 chapter thoroughly
- OWASP Top 10 2021: https://owasp.org/Top10/
- Nikto Documentation: https://cirt.net/Nikto2
- OWASP ZAP User Guide: https://www.zaproxy.org/docs/
- PortSwigger Web Security Academy: https://portswigger.net/web-security
- OWASP Testing Guide: https://owasp.org/www-project-web-security-testing-guide/

---

## Lab Setup Instructions

### Step 1: Environment Preparation

1. **Navigate to the lab directory:**
   ```bash
   cd /path/to/lab2
   ```

2. **Review the Vagrantfile:**
   - Open and read the `Vagrantfile` to understand the lab environment
   - **IMPORTANT:** Check line 21 - you may need to change the bridged network interface
   - Find your network interface: `ip link show` or `ifconfig`
   - Update the bridge parameter if necessary: `bridge: "your-interface-name"`

3. **Start the vulnerable VM:**
   ```bash
   vagrant up
   ```
   ⏱️ **Note:** Initial setup takes 5-10 minutes (packages, web server, database)

4. **Identify the VM's IP address:**
   ```bash
   vagrant ssh -c "ip -4 addr show eth1 | grep inet"
   ```
   **Write down the IP address** - you'll need it for all subsequent steps!

5. **Verify connectivity from your host machine:**
   ```bash
   ping <VM_IP_ADDRESS>
   ```

6. **Access the web application:**
   Open your browser and navigate to: `http://<VM_IP_ADDRESS>`
   You should see the Energía Marina homepage.

---

## Lab Challenges

### Phase 1: Reconnaissance & Automated Scanning (25 points)

**"The scanner is your reconnaissance drone."** Professional penetration tests begin with comprehensive automated scanning to map the attack surface. Learn to use industry-standard tools effectively.

#### Your Tasks:

1. **Network & Service Discovery**
   - Perform a comprehensive port scan with Nmap
   - Identify all open ports and services
   - Document service versions and banners
   - Identify the web server technology stack

2. **Nikto Web Server Scan**
   - Run Nikto against the target web server
   - Analyze the scan results carefully
   - Identify interesting files, directories, and potential vulnerabilities
   - **Command reference:** `nikto -h http://<target>`
   - Document findings that appear exploitable

3. **OWASP ZAP Automated Scan**
   - Launch OWASP ZAP and configure it properly
   - Perform an automated spider/crawl of the entire site
   - Run an active scan to identify vulnerabilities
   - Review alerts by risk level (High, Medium, Low)
   - Export your findings for documentation

4. **Manual Site Exploration**
   - Browse the entire website manually
   - Identify all forms, input fields, and dynamic pages
   - Test basic functionality (login, contact form, dashboard)
   - Look for hidden directories, commented code, or information leaks
   - Use browser DevTools to inspect page source and network traffic

5. **Vulnerability Mapping**
   - Create a comprehensive list of potential vulnerabilities
   - Prioritize findings based on scanner risk ratings
   - Cross-reference Nikto and ZAP results
   - Identify which findings require manual validation

#### Deliverables for Phase 1:
- Screenshot of Nmap scan showing open ports and services
- Screenshot of Nikto scan results
- Screenshot of OWASP ZAP scan summary (alerts dashboard)
- Site map showing all discovered pages and directories
- Brief write-up (2-3 paragraphs) summarizing key findings from automated scans
- Prioritized list of vulnerabilities to investigate manually

---

### Phase 2: SQL Injection Exploitation (25 points)

**"In SQL we trust, in input we inject."** SQL Injection remains one of the most critical web vulnerabilities, capable of completely compromising a database.

#### Your Tasks:

1. **Identify SQL Injection Points**
   - Review scanner results for SQL injection indicators
   - Identify forms or parameters that interact with a database
   - Test input fields with basic SQL injection payloads
   - Look for error messages that reveal database information

2. **Authentication Bypass**
   - Analyze the login form at `login.php`
   - Research SQL injection authentication bypass techniques
   - Craft payloads to bypass login without valid credentials
   - Successfully authenticate to the system

3. **SQL Injection Exploitation**
   - Once authenticated, explore the authenticated areas
   - Test additional parameters for SQL injection
   - Attempt to extract database information
   - Use SQL injection to locate the first flag

4. **Capture Flag #1**
   - The flag format is: `EM{5ql_1nj3ct10n_3n_v3r4cruz}`
   - Document the exact SQL injection payload used
   - Explain why your injection works
   - Demonstrate the vulnerability is repeatable

#### Hints:
- Classic SQL injection authentication bypass: `' OR '1'='1`
- Consider different comment styles: `--`, `#`, `/* */`
- Error messages are valuable reconnaissance
- Try variations if the first payload doesn't work
- The username field might be vulnerable, or the password field, or both
- Once logged in, look for flags in the dashboard or session data

#### Deliverables for Phase 2:
- Screenshot showing unsuccessful normal login attempt
- Screenshot showing successful SQL injection bypass
- Screenshot showing the captured flag
- Detailed explanation of the SQL injection vulnerability
- The exact payload(s) used with explanation of why they work
- Analysis of the vulnerable code (what makes this SQL query unsafe?)

---

### Phase 3: Cross-Site Scripting (XSS) (20 points)

**"Your script is my command."** XSS allows attackers to inject malicious JavaScript that executes in victims' browsers, enabling session hijacking and data theft.

#### Your Tasks:

1. **Identify XSS Injection Points**
   - Review OWASP ZAP results for XSS vulnerabilities
   - Identify pages that accept user input and display it
   - The contact form (`contacto.php`) is a likely candidate
   - Test for reflected and stored XSS

2. **Test XSS Payloads**
   - Start with simple test: `<script>alert('XSS')</script>`
   - If filtered, try encoding or alternative tags
   - Test different injection points (different form fields)
   - Determine if XSS is reflected or stored

3. **Successful XSS Exploitation**
   - Achieve JavaScript execution in the browser
   - Demonstrate the potential impact (cookie theft, page manipulation)
   - Locate the second flag through XSS exploitation
   - The flag might be in the page source, cookies, or triggered by successful XSS

4. **Capture Flag #2**
   - The flag format is: `EM{cr0ss_s1t3_scr1pt1ng_m4r1n0}`
   - Document your XSS payload
   - Explain what makes this XSS possible
   - Screenshot the successful alert/execution

#### Hints:
- Not all input fields may be vulnerable
- Check if your input is reflected back on the page
- Try different HTML tags: `<img>`, `<svg>`, `<iframe>`
- Event handlers can execute JavaScript: `onerror`, `onload`, `onclick`
- The flag might appear after successful XSS injection
- View page source to find hidden flags or comments

#### Deliverables for Phase 3:
- Screenshot showing normal form submission
- Screenshot showing XSS payload in input field
- Screenshot showing successful JavaScript execution (alert box or console output)
- Screenshot showing the captured flag
- Explanation of the XSS vulnerability and why input sanitization failed
- Discussion of the potential real-world impact of this XSS vulnerability

---

### Phase 4: Directory Traversal / Local File Inclusion (20 points)

**"Every path is a journey—some lead to secrets."** Directory traversal vulnerabilities allow attackers to access files outside the intended web directory.

#### Your Tasks:

1. **Identify File Inclusion Points**
   - Look for pages that load files based on parameters
   - The dashboard (`dashboard.php`) likely has a document viewing feature
   - Check URLs for parameters like `?file=`, `?doc=`, `?page=`
   - Review Nikto and ZAP results for file inclusion indicators

2. **Test Directory Traversal**
   - Try accessing files using relative paths: `../../etc/passwd`
   - Test different traversal sequences
   - Try URL encoding: `%2e%2e%2f` for `../`
   - Test both Linux and Windows path formats

3. **Local File Inclusion Exploitation**
   - Successfully read sensitive system files
   - Access the `/etc/passwd` file to prove the vulnerability
   - Explore what other files you can access
   - Locate configuration files or other sensitive data

4. **Capture Flag #3**
   - The flag format is: `EM{l0c4l_f1l3_1nclus10n_p3tr0l30}`
   - The flag is stored in a file accessible via directory traversal
   - Document the exact parameter and payload used
   - Explain the vulnerability mechanism

#### Hints:
- You may need to be authenticated to access the dashboard
- Look for a document parameter in the URL
- Try accessing `/etc/passwd` first as a proof-of-concept
- The number of `../` sequences needed depends on directory depth
- The flag might be in `/etc/flag.txt` or similar location
- Some systems need null byte injection: `%00` (though less common in modern PHP)

#### Deliverables for Phase 4:
- Screenshot showing normal document viewing functionality
- Screenshot showing successful `/etc/passwd` access
- Screenshot showing the captured flag file
- Detailed explanation of the directory traversal vulnerability
- The exact URL/payload used
- Discussion of why this vulnerability exists (insufficient input validation)

---

### Phase 5: Security Misconfiguration (15 points)

**"The devil is in the configuration."** Misconfigurations are surprisingly common and can expose sensitive information or provide direct attack paths.

#### Your Tasks:

1. **Information Disclosure**
   - Search for exposed configuration files
   - Look for PHP info pages (`info.php`, `phpinfo.php`)
   - Check for exposed version control directories (`.git/`, `.svn/`)
   - Look for directory listing vulnerabilities
   - Search for backup files (`.bak`, `.old`, `~`)

2. **Exposed Sensitive Information**
   - Access the `info.php` page if it exists
   - Analyze what information is revealed
   - Check if `.git` directory is accessible
   - Look for exposed source code or configuration

3. **Git Repository Exploitation**
   - If `.git/` is accessible, download repository contents
   - Use tools like `git-dumper` or manual `.git` extraction
   - Review source code for hardcoded credentials or secrets
   - Look for additional vulnerabilities in the code

4. **Capture Flag #4**
   - The flag format is: `EM{m1sc0nf1gur4t10n_gul0_mex1c0}`
   - The flag is hidden in exposed configuration or source files
   - Document what misconfiguration allowed this discovery
   - Demonstrate the information disclosure risk

#### Hints:
- Try accessing `http://<target>/info.php`
- Try accessing `http://<target>/.git/`
- Directory listing might reveal sensitive files
- phpinfo() reveals extensive server configuration
- Check for database credentials in exposed source code
- Nikto should have flagged some of these misconfigurations

#### Deliverables for Phase 5:
- Screenshot of exposed phpinfo page
- Screenshot of accessible .git directory or other misconfigurations
- Screenshot showing the captured flag
- List of all misconfigurations discovered
- Explanation of the security risks each misconfiguration presents
- Discussion of why these exposures are dangerous in production

---

### Phase 6: Broken Authentication (15 points)

**"Authentication is only as strong as its weakest link."** Broken authentication allows attackers to compromise user accounts or access restricted areas.

#### Your Tasks:

1. **Identify Admin Areas**
   - Look for administrative interfaces
   - Check common admin paths: `/admin/`, `/administrator/`, `/management/`
   - Review scanner results for restricted directories
   - The admin panel should be at `/admin/index.php`

2. **Authentication Analysis**
   - Attempt to access the admin panel
   - Analyze authentication mechanisms
   - Test for weak credentials
   - Check for authentication bypass vulnerabilities

3. **Exploit Broken Authentication**
   - Test default credentials (admin/admin, admin/password, etc.)
   - Try credential stuffing with common passwords
   - Check for SQL injection in admin login
   - Look for session management vulnerabilities
   - Test for authorization bypass techniques

4. **Capture Flag #5**
   - The flag format is: `EM{br0k3n_4uth_3n3rg14_m4r1n4}`
   - Successfully access the admin panel
   - Locate the final flag
   - Document the authentication weakness exploited

#### Hints:
- Default credentials are surprisingly common
- Try variations: admin/admin123, administrator/password
- The regular SQL injection might work here too
- Check HTTP headers and cookies for authentication tokens
- Some admin panels have weak password policies
- Look for flags in the admin dashboard after successful login

#### Deliverables for Phase 6:
- Screenshot showing attempted access to admin area (access denied)
- Screenshot showing successful authentication bypass or credential use
- Screenshot showing the admin panel and captured flag
- Explanation of the authentication vulnerability
- Discussion of proper authentication mechanisms that should be implemented

---

## Comprehensive Analysis & Remediation (20 points)

**"A good penetration tester doesn't just break things—they help fix them."** This phase demonstrates your understanding of defensive security.

#### Your Tasks:

1. **Vulnerability Analysis (7 points)**
   - Provide detailed technical analysis of each vulnerability discovered
   - Explain the root cause (code-level understanding)
   - Map vulnerabilities to OWASP Top 10 categories
   - Assess the exploitability and impact of each finding

2. **Risk Assessment (5 points)**
   - Rate each vulnerability using CVSS or similar framework
   - Analyze impact on Confidentiality, Integrity, and Availability (CIA triad)
   - Consider the business impact for Energía Marina
   - Prioritize vulnerabilities by risk (Critical, High, Medium, Low)
   - Discuss potential attack chains (combining multiple vulnerabilities)

3. **Remediation Recommendations (5 points)**
   - Provide specific, actionable fixes for each vulnerability
   - Include code-level remediation where appropriate
   - Recommend security controls and best practices
   - Suggest tools and frameworks for prevention (prepared statements, input validation libraries, etc.)
   - Address both tactical fixes and strategic improvements

4. **Lessons Learned (3 points)**
   - What did you learn about automated vs. manual testing?
   - How do Nikto and OWASP ZAP complement each other?
   - What surprised you about web application security?
   - How would you approach a real-world web application assessment?
   - What defensive strategies are most important to prevent these vulnerabilities?

#### Deliverables for Comprehensive Analysis:
- Detailed vulnerability analysis document (3-4 pages)
- Risk assessment matrix with CVSS scores
- Comprehensive remediation plan for each vulnerability
- Personal reflection on learning outcomes (2-3 paragraphs)

---

## Submission Requirements

### Report Format

Submit a **single PDF document** containing:

1. **Cover Page**
   - Your name and student ID
   - Course name and lab number
   - Target system: Energía Marina Web Application
   - Assessment date range
   - Submission date

2. **Executive Summary** (1 page)
   - High-level overview of the assessment
   - Summary of critical findings
   - Total number of vulnerabilities by severity
   - Overall risk rating
   - Key recommendations

3. **Methodology** (1 paragraph)
   - Tools used
   - Assessment approach
   - Testing timeline

4. **Detailed Findings** (Organized by Phase)
   - **Phase 1:** Reconnaissance & Automated Scanning
   - **Phase 2:** SQL Injection Exploitation (Flag #1)
   - **Phase 3:** Cross-Site Scripting (Flag #2)
   - **Phase 4:** Directory Traversal / LFI (Flag #3)
   - **Phase 5:** Security Misconfiguration (Flag #4)
   - **Phase 6:** Broken Authentication (Flag #5)
   - **Comprehensive Analysis:** Risk assessment and remediation

5. **All 5 Captured Flags**
   - Flag #1: `EM{5ql_1nj3ct10n_3n_v3r4cruz}`
   - Flag #2: `EM{cr0ss_s1t3_scr1pt1ng_m4r1n0}`
   - Flag #3: `EM{l0c4l_f1l3_1nclus10n_p3tr0l30}`
   - Flag #4: `EM{m1sc0nf1gur4t10n_gul0_mex1c0}`
   - Flag #5: `EM{br0k3n_4uth_3n3rg14_m4r1n4}`

6. **Appendix**
   - All screenshots (properly labeled and captioned)
   - Nikto and ZAP scan exports
   - Command history or scripts used
   - Any additional proof-of-concept code

### Screenshot Requirements

Every screenshot must include:
- Your terminal/browser showing the action performed
- The output/result clearly visible
- Timestamp (if possible)
- Clear caption explaining what the screenshot demonstrates
- VM IP address visible (to prove this is your work)

**Minimum required screenshots:**
- Nmap scan results (1-2 screenshots)
- Nikto scan output (1-2 screenshots)
- OWASP ZAP dashboard with alerts (1-2 screenshots)
- Each flag capture with exploitation proof (5 screenshots minimum)
- Successful exploitation of each vulnerability (10+ screenshots)
- Any additional proof of your work

### Writing Guidelines

- Use **clear, professional language** appropriate for a client report
- Write in **past tense** ("I performed...", "The scan revealed...", "Testing demonstrated...")
- Be **specific and technical** - include exact URLs, payloads, commands
- **Explain your reasoning** - don't just show what you did, explain why
- Make your work **reproducible** - another tester should be able to replicate your findings
- **Cite sources** when referencing OWASP or other security resources
- Use **proper formatting** - headings, bullet points, code blocks

---

## Grading Rubric (150 points total)

| Component | Points | Criteria |
|-----------|--------|----------|
| **Phase 1: Reconnaissance & Scanning** | 25 | |
| Nmap service discovery | 5 | Complete scan with all services properly identified |
| Nikto scan execution & analysis | 7 | Thorough scan with meaningful analysis of results |
| OWASP ZAP scan & configuration | 7 | Proper configuration, spider, active scan, and alert review |
| Manual site exploration | 4 | Comprehensive site mapping and functionality testing |
| Vulnerability prioritization | 2 | Clear prioritized list with justification |
| **Phase 2: SQL Injection** | 25 | |
| Vulnerability identification | 5 | Correctly identified SQL injection point |
| Successful exploitation | 9 | Demonstrated authentication bypass |
| Flag #1 capture | 7 | Successfully retrieved and documented |
| Technical explanation | 4 | Clear understanding of SQLi mechanism |
| **Phase 3: Cross-Site Scripting** | 20 | |
| XSS identification | 4 | Correctly identified XSS vulnerability |
| Successful payload execution | 7 | Demonstrated JavaScript execution |
| Flag #2 capture | 6 | Successfully retrieved and documented |
| Impact analysis | 3 | Explained potential real-world consequences |
| **Phase 4: Directory Traversal/LFI** | 20 | |
| File inclusion identification | 4 | Correctly identified vulnerable parameter |
| Successful exploitation | 7 | Demonstrated file access outside webroot |
| Flag #3 capture | 6 | Successfully retrieved and documented |
| Technical explanation | 3 | Clear understanding of path traversal |
| **Phase 5: Security Misconfiguration** | 15 | |
| Information disclosure findings | 5 | Identified multiple misconfigurations |
| Exploitation of misconfigurations | 5 | Accessed sensitive information |
| Flag #4 capture | 3 | Successfully retrieved and documented |
| Risk analysis | 2 | Explained dangers of misconfigurations |
| **Phase 6: Broken Authentication** | 15 | |
| Admin panel discovery | 3 | Located administrative interface |
| Authentication bypass | 6 | Successfully gained admin access |
| Flag #5 capture | 4 | Successfully retrieved and documented |
| Remediation recommendations | 2 | Suggested proper authentication controls |
| **Comprehensive Analysis** | 20 | |
| Vulnerability analysis | 7 | Thorough technical analysis of all findings |
| Risk assessment | 5 | Proper CIA impact and CVSS-style scoring |
| Remediation recommendations | 5 | Specific, actionable fixes for each vulnerability |
| Lessons learned | 3 | Thoughtful reflection on methodology and learning |
| **Report Quality** | 10 | |
| Professional formatting | 3 | Well-organized, clear structure, executive summary |
| Screenshot quality & completeness | 4 | All required screenshots, properly labeled |
| Writing clarity & professionalism | 3 | Clear, technical, client-ready writing |
| **DEDUCTIONS** | | |
| Missing flags | -15 each | Each flag not captured |
| Missing required screenshots | -10 | Critical proof missing |
| Late submission | -10/day | Up to 3 days, then 0 |
| Fabricated results | -50 | Using someone else's screenshots or flags |
| Poor formatting | -10 | Unprofessional or unreadable presentation |

### Bonus Points (Up to 20 additional points)

**Bonus Challenge 1: Advanced Exploitation (10 points)**
- Use SQLmap to demonstrate advanced SQL injection techniques
- Extract the entire database schema and contents
- Document the process and findings

**Bonus Challenge 2: Vulnerability Chaining (10 points)**
- Demonstrate a realistic attack chain combining multiple vulnerabilities
- For example: Use XSS to steal admin session, then access admin panel
- Document the multi-step attack path

**Bonus Challenge 3: Remediation Proof (5 points)**
- Provide actual code fixes for at least 2 vulnerabilities
- Include before/after code examples
- Explain why your fix prevents the vulnerability

---

## Important Reminders

### Ethical Guidelines

This lab is conducted in an **isolated, authorized environment**. The skills you learn here must only be applied ethically:

- **DO:** Practice on systems you own or have explicit written permission to test
- **DO:** Document your methodology professionally and thoroughly
- **DO:** Think like an attacker to better defend systems
- **DO:** Report vulnerabilities responsibly in real-world scenarios
- **DON'T:** Scan or exploit systems without authorization
- **DON'T:** Use these techniques for malicious purposes
- **DON'T:** Share exploitation details publicly without responsible disclosure
- **DON'T:** Apply these skills outside of educational or authorized contexts

**Violation of these principles may result in academic discipline, expulsion, and legal consequences including federal prosecution under the Computer Fraud and Abuse Act (CFAA).**

### Lab Safety

- This VM is **INTENTIONALLY VULNERABLE** - keep it isolated
- Do **NOT** expose this VM to the internet or production networks
- Use **ONLY** on your local network in a controlled environment
- **STOP** the VM when not actively testing: `vagrant halt`
- **DESTROY** the VM after completing the lab: `vagrant destroy`
- Never deploy vulnerable code to production systems

### Getting Help

If you encounter technical issues:
1. Review the README.md for troubleshooting steps
2. Verify network configuration in Vagrantfile
3. Check that VM received an IP address: `vagrant ssh -c "ip addr"`
4. Test connectivity: `ping <VM_IP>`
5. Verify web server is running: `vagrant ssh -c "sudo rc-service nginx status"`
6. Review error logs: `vagrant ssh -c "tail /var/log/nginx/error.log"`
7. Visit instructor office hours or post on course forum (without sharing flags or exploits)

**Collaboration Policy:**
- You may discuss general concepts and tool usage with classmates
- You may help each other with technical setup issues
- You **MUST NOT** share: flags, specific exploits, payloads, screenshots, or report content
- Your exploitation work and report must be entirely your own
- Sharing or receiving flags is considered academic dishonesty

---

## Timeline & Milestones

| Milestone | Recommended Completion | Time Investment |
|-----------|----------------------|-----------------|
| Lab setup & VM deployment | Week 1, Day 1 | 1 hour |
| Phase 1: Reconnaissance & Scanning | Week 1, Days 2-3 | 4-6 hours |
| Phase 2: SQL Injection | Week 1, Days 4-5 | 3-4 hours |
| Phase 3: Cross-Site Scripting | Week 1, Days 5-6 | 2-3 hours |
| Phase 4: Directory Traversal | Week 1, Day 7 | 2-3 hours |
| Phase 5: Security Misconfiguration | Week 2, Days 1-2 | 2-3 hours |
| Phase 6: Broken Authentication | Week 2, Days 2-3 | 2-3 hours |
| Comprehensive Analysis & Report Writing | Week 2, Days 4-7 | 6-8 hours |
| **Final Submission Deadline** | **End of Week 2** | **Total: 20-30 hours** |

**Time Management Tips:**
- Start early - vulnerabilities can be tricky to exploit
- Don't get stuck on one flag for hours - move on and come back
- Document as you go - don't leave report writing for the last day
- Take breaks - fresh eyes often find solutions faster
- Use office hours if stuck for more than 2 hours on one phase

---

## Additional Resources

### OWASP Resources
- **OWASP Top 10 2021:** https://owasp.org/Top10/
- **OWASP Web Security Testing Guide:** https://owasp.org/www-project-web-security-testing-guide/
- **OWASP Cheat Sheet Series:** https://cheatsheetseries.owasp.org/
- **OWASP ZAP Getting Started:** https://www.zaproxy.org/getting-started/

### Tools & Documentation
- **Nikto Documentation:** https://cirt.net/Nikto2
- **Nmap Reference Guide:** https://nmap.org/book/man.html
- **SQLmap User Manual:** https://github.com/sqlmapproject/sqlmap/wiki/Usage
- **Burp Suite Documentation:** https://portswigger.net/burp/documentation

### Learning Resources
- **PortSwigger Web Security Academy:** https://portswigger.net/web-security (Free, interactive labs)
- **HackTheBox Academy:** https://academy.hackthebox.com/ (Web exploitation modules)
- **OWASP WebGoat:** https://owasp.org/www-project-webgoat/ (Vulnerable app for learning)
- **PentesterLab:** https://pentesterlab.com/ (Web penetration testing exercises)

### Vulnerability Databases
- **National Vulnerability Database:** https://nvd.nist.gov/
- **OWASP Vulnerability Database:** https://owasp.org/www-community/vulnerabilities/
- **CWE (Common Weakness Enumeration):** https://cwe.mitre.org/

---

## Conclusion

This lab represents a comprehensive introduction to web application security testing. You will use the same tools and techniques employed by professional penetration testers worldwide. The five vulnerabilities you'll exploit (SQL Injection, XSS, Directory Traversal, Security Misconfiguration, and Broken Authentication) are consistently among the most critical and commonly exploited flaws in real-world applications.

**The Energía Marina scenario is fictional, but the vulnerabilities are very real.** Companies suffer data breaches daily because of the exact flaws you'll exploit in this lab. According to the 2023 Verizon Data Breach Investigations Report, web application attacks account for over 40% of all breaches, with SQL injection and XSS remaining prevalent attack vectors.

Your mission is twofold: **learn to think like an attacker, but act as a defender.** Understanding how to exploit vulnerabilities is the best way to learn how to prevent them. Professional security engineers who can both break and build secure systems are in high demand.

**Key Takeaways:**
- Automated scanning is essential but insufficient - manual validation is critical
- The OWASP Top 10 provides a framework for understanding web security risks
- Proper input validation and output encoding prevent most web vulnerabilities
- Security must be built in from the beginning, not bolted on afterward
- Documentation and communication skills are as important as technical skills

**Approach this lab with:**
- **Curiosity** - Explore every input, every parameter, every page
- **Persistence** - Some flags are tricky; don't give up
- **Methodology** - Follow a structured approach; document everything
- **Ethics** - Remember the power and responsibility you're learning

You have two weeks and five flags to capture. Each flag represents a different critical vulnerability that could compromise a real organization. Use your time wisely, leverage both automated tools and manual techniques, and create a professional report that demonstrates not just what you found, but that you truly understand web application security.

**Good luck, and happy hunting!**

---

*This lab is designed for educational purposes only. All activities must be performed on the provided VM with explicit authorization. Unauthorized computer access is illegal under federal law (18 U.S.C. § 1030) and most state laws. Violators will be prosecuted.*

*The Energía Marina company and scenario are entirely fictional. Any resemblance to real entities is coincidental.*

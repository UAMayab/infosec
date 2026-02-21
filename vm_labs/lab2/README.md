# Lab 2: Energía Marina - Web Exploitation Lab

## Overview

This lab features a vulnerable web application for **Energía Marina**, a fictional oil company based in Veracruz, Mexico. The site is intentionally vulnerable and contains 5 distinct web security flaws based on the OWASP Top 10.

## Lab Information

- **Company:** Energía Marina S.A. de C.V.
- **Location:** Veracruz, Golfo de México
- **Technology Stack:** Alpine Linux, Nginx, PHP 8.2, MariaDB
- **Theme:** Dark hacker aesthetic with Mexican elements

## Vulnerabilities Implemented

This lab contains **5 flags** to capture, each corresponding to a different OWASP Top 10 vulnerability:

1. **SQL Injection** (OWASP A03:2021)
   - Location: Login form (`login.php`)
   - Flag: `EM{5ql_1nj3ct10n_3n_v3r4cruz}`

2. **Cross-Site Scripting - XSS** (OWASP A03:2021)
   - Location: Contact form (`contacto.php`)
   - Flag: `EM{cr0ss_s1t3_scr1pt1ng_m4r1n0}`

3. **Directory Traversal / Local File Inclusion** (OWASP A01:2021)
   - Location: Dashboard document viewer (`dashboard.php?doc=`)
   - Flag: `EM{l0c4l_f1l3_1nclus10n_p3tr0l30}`

4. **Security Misconfiguration** (OWASP A05:2021)
   - Location: phpinfo page, exposed .git directory, directory listing
   - Flag: `EM{m1sc0nf1gur4t10n_gul0_mex1c0}`

5. **Broken Authentication** (OWASP A07:2021)
   - Location: Admin panel (`admin/index.php`)
   - Flag: `EM{br0k3n_4uth_3n3rg14_m4r1n4}`

## Quick Start

### Prerequisites
- Vagrant installed
- VirtualBox installed
- At least 2GB RAM available
- Network interface for bridged networking

### Setup Instructions

1. **Navigate to lab directory:**
   ```bash
   cd lab2
   ```

2. **Update bridged network interface in Vagrantfile (if needed):**
   - Check line 21 of `Vagrantfile`
   - Change `bridge: "wlp4s0"` to your network interface
   - Find your interface: `ip link show` or `ifconfig`

3. **Start the VM:**
   ```bash
   vagrant up
   ```
   ⏱️ First run takes 5-10 minutes

4. **Get the VM's IP address:**
   ```bash
   vagrant ssh -c "ip -4 addr show eth1 | grep inet"
   ```

5. **Access the website:**
   ```
   http://<VM_IP_ADDRESS>
   ```

## VM Services

- **Web Server:** Nginx (port 80)
- **Database:** MariaDB (localhost only)
- **PHP Version:** 8.2

## Tools Recommended

Students should use the following tools:
- **Nmap** - Port scanning and service enumeration
- **Nikto** - Web server vulnerability scanner
- **OWASP ZAP** - Web application security scanner
- **Metasploit** - Exploitation framework
- **Browser DevTools** - Inspect, modify cookies, network traffic
- **cURL** - Manual HTTP requests

## File Structure

```
lab2/
├── Vagrantfile                    # VM configuration
├── README.md                      # This file
├── ASSIGNMENT.md                  # Student assignment document
├── TEACHERS_GUIDE.md              # Teacher's exploitation guide
└── www/                           # Web application files
    ├── index.html                 # Main homepage
    ├── login.php                  # Login (SQL Injection)
    ├── dashboard.php              # Dashboard (LFI/Directory Traversal)
    ├── contacto.php               # Contact form (XSS)
    ├── produccion.php             # Production data page
    ├── info.php                   # phpinfo (Misconfiguration)
    ├── logout.php                 # Logout script
    ├── config.php                 # Database configuration
    ├── admin/
    │   └── index.php              # Admin panel (Broken Auth)
    ├── css/
    │   └── style.css              # Dark theme stylesheet
    └── docs/
        ├── manual_seguridad.txt   # Sample document
        └── procedimientos.txt     # Sample document
```

## VM Management

**Stop the VM:**
```bash
vagrant halt
```

**Restart the VM:**
```bash
vagrant reload
```

**Destroy the VM:**
```bash
vagrant destroy
```

**SSH into the VM:**
```bash
vagrant ssh
```

## Default Credentials

For SQL injection testing and authentication bypass:

| Username | Password | Level |
|----------|----------|-------|
| admin | admin123 | admin |
| jperez | veracruz2024 | user |
| mrodriguez | password | user |
| lgarcia | qwerty | user |

## Security Warning

⚠️ **This VM is INTENTIONALLY VULNERABLE**

- Do NOT expose to the internet
- Use ONLY in isolated lab environments
- Do NOT deploy in production networks
- All vulnerabilities are for educational purposes

## Troubleshooting

**VM doesn't get an IP:**
- Check bridged network interface name in Vagrantfile
- Verify your host machine has network connectivity
- Try using a different network interface

**Can't access the website:**
- Verify VM is running: `vagrant status`
- Check IP address: `vagrant ssh -c "ip -4 addr"`
- Ping the VM from your host machine
- Check firewall settings

**Database connection errors:**
- SSH into VM: `vagrant ssh`
- Check MariaDB status: `sudo rc-service mariadb status`
- Restart if needed: `sudo rc-service mariadb restart`

## Learning Objectives

By completing this lab, students will:
- Master web vulnerability scanning with Nikto and OWASP ZAP
- Understand and exploit OWASP Top 10 vulnerabilities
- Practice reconnaissance and enumeration techniques
- Develop professional penetration testing documentation skills
- Learn remediation strategies for web vulnerabilities

## Support

For technical issues or questions:
- Review the ASSIGNMENT.md document
- Consult the TEACHERS_GUIDE.md (instructors only)
- Check VM logs: `vagrant ssh -c "tail -f /var/log/nginx/error.log"`

## Credits

- **Created:** February 2026
- **Author:** Miguel Guirao
- **Course:** Introduction to Cybersecurity
- **Institution:** Educational lab environment

---

**Good luck and happy hacking! 🔐**

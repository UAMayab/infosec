# Nmap Workshop - Vulnerable VM Lab

![Security Workshop](https://img.shields.io/badge/Security-Workshop-red)
![Vagrant](https://img.shields.io/badge/Vagrant-Alpine_Linux-blue)
![Intentionally Vulnerable](https://img.shields.io/badge/Status-Intentionally_Vulnerable-orange)

## Overview

This is an **intentionally vulnerable** virtual machine designed for educational purposes to teach network scanning with Nmap. The VM contains multiple misconfigured services that students can discover and analyze.

⚠️ **WARNING**: This VM is intentionally insecure and should ONLY be used in isolated lab environments. Never deploy this on production networks or expose it to the internet.

## Features

### Vulnerable Services

The VM includes the following intentionally misconfigured services:

1. **SSH Server (Port 22)**
   - Root login enabled

2. **Apache Web Server (Port 80)**
   - HTTP Basic Authentication

3. **Telnet Server (Port 23)**

4. **MySQL Server (Port 3306)**
   - Remote root access enabled

## Prerequisites

Before starting, ensure you have the following installed:

- [Vagrant](https://www.vagrantup.com/downloads) (2.2.0 or higher)
- [VirtualBox](https://www.virtualbox.org/wiki/Downloads) (6.1 or higher)
- Basic knowledge of Linux command line
- Nmap installed on your host machine

### Installing Nmap

**Linux (Debian/Ubuntu):**
```bash
sudo apt update
sudo apt install nmap
```

**macOS:**
```bash
brew install nmap
```

**Windows:**
Download from [nmap.org](https://nmap.org/download.html)

## Quick Start

### 1. Launch the Virtual Machine

```bash
# Navigate to the lab directory
cd lab0

# Start the VM (first run will download Alpine Linux box)
vagrant up

# Wait for provisioning to complete (2-3 minutes)
```

### 2. Verify VM is Running

```bash
# Check VM status
vagrant status

# Should show: "running (virtualbox)"
```

### 3. Access the Web Interface

Open your browser and navigate to:
```
http://localhost:8080
```

## Nmap Workshop Exercises

### Exercise 1: Basic Host Discovery

Check if the target is alive:

```bash
# Ping scan
nmap -sn 192.168.56.10

# Host discovery
nmap -Pn 192.168.56.10
```

### Exercise 2: Port Scanning

Discover open ports on the target:

```bash
# Scan common ports
nmap 192.168.56.10

# Scan all ports
nmap -p- 192.168.56.10

# Scan specific ports
nmap -p 22,23,80,3306 192.168.56.10

# Fast scan
nmap -F 192.168.56.10
```

### Exercise 3: Service Version Detection

Identify service versions:

```bash
# Version detection
nmap -sV 192.168.56.10

# Aggressive version detection
nmap -sV --version-intensity 9 192.168.56.10

# OS detection
nmap -O 192.168.56.10
```

### Exercise 4: Script Scanning

Use NSE (Nmap Scripting Engine):

```bash
# Default scripts
nmap -sC 192.168.56.10

# Vulnerability scripts
nmap --script vuln 192.168.56.10

# Specific service scripts
nmap --script ssh-* 192.168.56.10
nmap --script mysql-* 192.168.56.10
```

### Exercise 5: Comprehensive Scan

Perform a thorough security assessment:

```bash
# Comprehensive scan
nmap -sV -sC -p- -A -T4 192.168.56.10 -oA nmap_results

# Explanation:
# -sV: Version detection
# -sC: Default scripts
# -p-: All ports
# -A: Aggressive scan (OS, version, scripts, traceroute)
# -T4: Faster timing template
# -oA: Output in all formats
```

### Exercise 6: Output Formats

Save scan results in different formats:

```bash
# Normal output
nmap -oN output.txt 192.168.56.10

# XML output
nmap -oX output.xml 192.168.56.10

# Grepable output
nmap -oG output.grep 192.168.56.10

# All formats
nmap -oA scan_results 192.168.56.10
```

### Security Issues to Identify

Students should be able to identify:

1. **SSH** - Permits root login with empty password
2. **Telnet** - Unencrypted protocol, no authentication
3. **HTTP** - Basic authentication (easily intercepted)
4. **MySQL** - Remote root access with weak password

## Manual Testing

### SSH Access

```bash
# Connect via SSH (no password required)
ssh root@192.168.56.10

# Or use port forwarding
ssh root@localhost -p 2222
```

### Telnet Access

```bash
# Connect via Telnet
telnet 192.168.56.10

# Or use port forwarding
telnet localhost 2323
```

### MySQL Access

```bash
# Connect to MySQL
mysql -h 192.168.56.10 -u root -pqwerty

# Or use port forwarding
mysql -h 127.0.0.1 -u root -pqwerty
```

### Web Access

```bash
# Using curl with authentication
curl -u admin:12345 http://192.168.56.10

# Or via browser
http://localhost:8080
```

## VM Management

### Useful Vagrant Commands

```bash
# Start the VM
vagrant up

# Stop the VM
vagrant halt

# Restart the VM
vagrant reload

# SSH into the VM
vagrant ssh

# Destroy the VM
vagrant destroy

# Reload with provisioning
vagrant reload --provision

# Check VM status
vagrant status
```

### Troubleshooting

**VM won't start:**
```bash
# Check VirtualBox is running
VBoxManage list vms

# Remove and recreate
vagrant destroy -f
vagrant up
```

**Can't connect to services:**
```bash
# Check VM is running
vagrant status

# Verify port forwarding
vagrant port

# SSH into VM and check services
vagrant ssh
sudo rc-status
```

**Provisioning failed:**
```bash
# Re-run provisioning
vagrant provision

# Or reload with provisioning
vagrant reload --provision
```

## Network Configuration

The VM is configured with:

- **Private Network IP**: 192.168.56.10
- **Port Forwarding**:
  - SSH: localhost:2222 → VM:22
  - HTTP: localhost:8080 → VM:80
  - Telnet: localhost:2323 → VM:23
  - MySQL: localhost:3306 → VM:3306

## Workshop Learning Objectives

By the end of this workshop, students should be able to:

1. ✅ Perform basic port scans using Nmap
2. ✅ Identify running services and their versions
3. ✅ Use NSE scripts for vulnerability detection
4. ✅ Interpret scan results and identify security issues
5. ✅ Understand the difference between secure and insecure configurations
6. ✅ Document findings in professional security reports

## Advanced Exercises

### Challenge 1: Stealth Scanning

Try to scan without being detected:

```bash
# SYN stealth scan
sudo nmap -sS 192.168.56.10

# Fragmented packets
sudo nmap -f 192.168.56.10

# Decoy scan
sudo nmap -D RND:10 192.168.56.10
```

### Challenge 2: Service Enumeration

Gather detailed information:

```bash
# Banner grabbing
nmap --script banner 192.168.56.10

# HTTP enumeration
nmap --script http-enum 192.168.56.10

# MySQL enumeration
nmap --script mysql-info,mysql-enum 192.168.56.10
```

### Challenge 3: Vulnerability Assessment

Find specific vulnerabilities:

```bash
# SSH vulnerabilities
nmap --script ssh-auth-methods,ssh-hostkey 192.168.56.10

# Web vulnerabilities
nmap --script http-sql-injection,http-passwd 192.168.56.10

# Database vulnerabilities
nmap --script mysql-empty-password,mysql-vuln-* 192.168.56.10
```

## Security Considerations

### DO:
- ✅ Use this VM only in isolated lab environments
- ✅ Keep the VM on a private network
- ✅ Use this for learning and education
- ✅ Document your findings
- ✅ Practice responsible disclosure

### DON'T:
- ❌ Expose this VM to the internet
- ❌ Use these configurations in production
- ❌ Scan networks without permission
- ❌ Use discovered techniques for malicious purposes
- ❌ Share access to this VM publicly

## File Structure

```
lab0/
├── Vagrantfile              # VM configuration
├── provision.sh             # Setup script for vulnerable services
├── www/                     # Web application files
│   ├── index.html          # Dark futuristic login page
│   ├── styles.css          # Cyberpunk styling
│   └── script.js           # Interactive effects
└── README.md               # This file
```

## Customization

### Adding More Services

Edit `provision.sh` to add additional vulnerable services:

```bash
# Example: Add FTP server
apk add vsftpd
rc-update add vsftpd
rc-service vsftpd start
```

### Modifying Web Content

Edit files in the `www/` directory and reload:

```bash
vagrant reload --provision
```

### Changing Network Settings

Modify `Vagrantfile`:

```ruby
# Change IP address
config.vm.network "private_network", ip: "192.168.56.20"

# Add more port forwarding
config.vm.network "forwarded_port", guest: 21, host: 2121
```

## Resources

### Nmap Documentation
- [Official Nmap Documentation](https://nmap.org/docs.html)
- [Nmap Scripting Engine](https://nmap.org/book/nse.html)
- [Nmap Cheat Sheet](https://hackertarget.com/nmap-cheatsheet-a-quick-reference-guide/)

### Security Learning
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Pentesting Standards](http://www.pentest-standard.org/)
- [Hack The Box](https://www.hackthebox.com/)

### Vagrant Resources
- [Vagrant Documentation](https://www.vagrantup.com/docs)
- [Vagrant Cloud Boxes](https://app.vagrantup.com/boxes/search)

## License

This project is provided for educational purposes only. Use at your own risk.

## Contributing

Found an issue or want to add more vulnerable services? Feel free to contribute!

## Credits

Created for security education and Nmap training workshops.

---

**Remember**: With great power comes great responsibility. Use these skills ethically and legally! 🔒

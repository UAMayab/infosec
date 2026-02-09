# Nmap Workshop - Quick Reference Cheat Sheet

## VM Information
- **IP Address**: 192.168.56.10
- **Hostname**: nmap-target

## Vulnerable Services
Those services with a username, have very easy to guess passwords that you may want to try!
Did I say very easy passwords?

| Service | Port | Credentials | Access Method |
|---------|------|-------------|---------------|
| SSH | 22 | root / *** | `ssh root@192.168.56.10` |
| Telnet | 23 | *** / *** | `telnet 192.168.56.10` |
| HTTP | 80 | admin / *** | `http://192.168.56.10` |
| MySQL | 3306 | root / *** | `mysql -h 192.168.56.10 -u root -p` |

## Essential Nmap Commands

### Host Discovery
```bash
# Ping scan
nmap -sn 192.168.56.10

# No ping (treat host as online)
nmap -Pn 192.168.56.10
```

### Port Scanning
```bash
# Default scan (common 1000 ports)
nmap 192.168.56.10

# All ports
nmap -p- 192.168.56.10

# Specific ports
nmap -p 22,23,80,3306 192.168.56.10

# Port range
nmap -p 1-1000 192.168.56.10

# Fast scan (100 most common ports)
nmap -F 192.168.56.10

# Top ports
nmap --top-ports 20 192.168.56.10
```

### Service Detection
```bash
# Version detection
nmap -sV 192.168.56.10

# Aggressive version detection
nmap -sV --version-intensity 9 192.168.56.10

# Light version detection (faster)
nmap -sV --version-intensity 0 192.168.56.10
```

### OS Detection
```bash
# OS detection (requires root)
sudo nmap -O 192.168.56.10

# Aggressive OS detection
sudo nmap -O --osscan-guess 192.168.56.10
```

### Script Scanning (NSE)
```bash
# Default scripts
nmap -sC 192.168.56.10

# Vulnerability scripts
nmap --script vuln 192.168.56.10

# Specific category
nmap --script discovery 192.168.56.10
nmap --script auth 192.168.56.10

# Specific script
nmap --script ssh-brute 192.168.56.10
nmap --script http-enum 192.168.56.10

# Multiple scripts
nmap --script "ssh-* and not ssh-brute" 192.168.56.10

# Get script help
nmap --script-help http-enum
```

### Comprehensive Scans
```bash
# Basic comprehensive
nmap -sV -sC 192.168.56.10

# Aggressive scan
nmap -A 192.168.56.10

# Full comprehensive
nmap -sV -sC -O -p- -A 192.168.56.10

# With timing template
nmap -sV -sC -T4 192.168.56.10
```

### Scan Types
```bash
# TCP Connect scan
nmap -sT 192.168.56.10

# SYN stealth scan (requires root)
sudo nmap -sS 192.168.56.10

# UDP scan (slow)
sudo nmap -sU 192.168.56.10

# TCP and UDP
sudo nmap -sS -sU 192.168.56.10
```

### Timing Templates
```bash
# T0 - Paranoid (slowest, IDS evasion)
nmap -T0 192.168.56.10

# T1 - Sneaky
nmap -T1 192.168.56.10

# T2 - Polite (less bandwidth)
nmap -T2 192.168.56.10

# T3 - Normal (default)
nmap -T3 192.168.56.10

# T4 - Aggressive (fast)
nmap -T4 192.168.56.10

# T5 - Insane (fastest, may miss ports)
nmap -T5 192.168.56.10
```

### Output Formats
```bash
# Normal output
nmap -oN output.txt 192.168.56.10

# XML output
nmap -oX output.xml 192.168.56.10

# Grepable output
nmap -oG output.grep 192.168.56.10

# All formats
nmap -oA scan_results 192.168.56.10

# Script kiddie output (for fun)
nmap -oS output.skid 192.168.56.10
```

### Useful NSE Scripts for This Lab

#### SSH Scripts
```bash
# Check SSH authentication methods
nmap --script ssh-auth-methods 192.168.56.10

# Get SSH host key
nmap --script ssh-hostkey 192.168.56.10

# SSH version detection
nmap --script ssh2-enum-algos 192.168.56.10
```

#### HTTP Scripts
```bash
# Enumerate directories
nmap --script http-enum 192.168.56.10

# Get HTTP headers
nmap --script http-headers 192.168.56.10

# Check for HTTP auth
nmap --script http-auth 192.168.56.10

# Detect HTTP methods
nmap --script http-methods 192.168.56.10

# Get HTTP title
nmap --script http-title 192.168.56.10
```

#### MySQL Scripts
```bash
# Get MySQL info
nmap --script mysql-info 192.168.56.10

# Check for empty password
nmap --script mysql-empty-password 192.168.56.10

# Enumerate databases
nmap --script mysql-databases 192.168.56.10

# Enumerate users
nmap --script mysql-users 192.168.56.10

# Check MySQL variables
nmap --script mysql-variables 192.168.56.10
```

#### Telnet Scripts
```bash
# Get Telnet encryption
nmap --script telnet-encryption 192.168.56.10

# Try Telnet without auth
nmap --script telnet-brute 192.168.56.10
```

### Evasion Techniques
```bash
# Fragment packets
sudo nmap -f 192.168.56.10

# Use decoys
sudo nmap -D RND:10 192.168.56.10

# Spoof source port
sudo nmap --source-port 53 192.168.56.10

# Use random data length
nmap --data-length 25 192.168.56.10

# Randomize host order
nmap --randomize-hosts 192.168.56.10
```

### Practical Examples for This Lab

#### Quick Scan
```bash
nmap -F -T4 192.168.56.10
```

#### Standard Security Audit
```bash
nmap -sV -sC -p- -T4 192.168.56.10 -oA security_audit
```

#### Vulnerability Assessment
```bash
nmap -sV --script vuln -p 22,23,80,3306 192.168.56.10 -oN vulnerabilities.txt
```

#### Service Enumeration
```bash
nmap -sV -sC --script "ssh-*,http-*,mysql-*" -p 22,23,80,3306 192.168.56.10
```

#### Complete Penetration Test
```bash
sudo nmap -sS -sV -O -sC --script vuln -p- -T4 -A 192.168.56.10 -oA pentest_full
```

## Vagrant Commands

```bash
# Start VM
vagrant up

# Stop VM
vagrant halt

# Restart VM
vagrant reload

# SSH into VM
vagrant ssh

# Check status
vagrant status

# Destroy VM
vagrant destroy

# Re-provision
vagrant provision
```

## Manual Testing

### Test SSH
```bash
# No password required
ssh root@192.168.56.10

# Or via port forwarding
ssh root@localhost -p 2222
```

### Test Telnet
```bash
telnet 192.168.56.10

# Or via port forwarding
telnet localhost 2323
```

### Test HTTP
```bash
# Using curl
curl -u admin:12345 http://192.168.56.10

# Get headers only
curl -I -u admin:12345 http://192.168.56.10

# Or in browser
http://localhost:8080
```

### Test MySQL
```bash
# Connect
mysql -h 192.168.56.10 -u root -pqwerty

# Quick test
mysql -h 192.168.56.10 -u root -pqwerty -e "SHOW DATABASES;"

# Or via port forwarding
mysql -h 127.0.0.1 -u root -pqwerty
```

## Common Issues

### Can't reach VM
```bash
# Check VM is running
vagrant status

# Check VM IP
vagrant ssh -c "ip addr show eth1"

# Ping test
ping 192.168.56.10
```

### Ports not accessible
```bash
# Check port forwarding
vagrant port

# Restart VM
vagrant reload
```

### Nmap too slow
```bash
# Use faster timing
nmap -T4 192.168.56.10

# Scan fewer ports
nmap --top-ports 100 192.168.56.10

# Skip host discovery
nmap -Pn 192.168.56.10
```

## Learning Path

1. Start with basic host discovery
2. Practice different port scanning techniques
3. Learn service version detection
4. Explore NSE scripts
5. Try stealth and evasion techniques
6. Perform comprehensive assessments

## Pro Tips

- Always use `-Pn` if the host blocks ping
- Use `-T4` for faster scans in labs (not in production!)
- Combine `-sV -sC` for standard enumeration
- Save output with `-oA` for documentation
- Read `--script-help` before using NSE scripts
- Use `--reason` to understand why Nmap made decisions
- Add `-v` or `-vv` for verbose output

## Security Reminders

- Only scan authorized systems
- This lab is intentionally vulnerable
- Never expose this VM to the internet
- Use findings for education only
- Practice responsible disclosure
- Document your findings professionally

---

**Happy Scanning!** 🔍

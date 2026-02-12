# tcpdump & BPF Rules: Zero to Hero Workshop
**Ethical Hacking Class - Network Traffic Analysis**

---

## Table of Contents
1. [Introduction](#introduction)
2. [Networking Fundamentals Review](#networking-fundamentals-review)
3. [Getting Started with tcpdump](#getting-started-with-tcpdump)
4. [Understanding Packet Capture Output](#understanding-packet-capture-output)
5. [Berkeley Packet Filter (BPF) Syntax](#berkeley-packet-filter-bpf-syntax)
6. [Progressive Examples](#progressive-examples)
7. [Advanced Techniques](#advanced-techniques)
8. [Best Practices & Ethics](#best-practices--ethics)
9. [Quick Reference Cheat Sheet](#quick-reference-cheat-sheet)

---

## Introduction

### What is tcpdump?
- **Command-line packet analyzer** for Unix-like systems
- Captures and displays TCP/IP and other network packets
- Uses **libpcap** library for packet capture
- Industry-standard tool for network analysis, troubleshooting, and security investigation

### Why tcpdump in Ethical Hacking?
- **Reconnaissance**: Understanding network traffic patterns
- **Incident Response**: Analyzing suspicious activity
- **Forensics**: Post-breach investigation
- **Penetration Testing**: Monitoring your own attack traffic
- **Defense**: Baseline network behavior and detect anomalies

### Legal & Ethical Considerations ⚖️
**CRITICAL**: Only capture traffic on networks you own or have explicit written permission to monitor. Unauthorized packet capture is illegal in most jurisdictions.

---

## Networking Fundamentals Review

### OSI Model Quick Recap
```
Layer 7 - Application  (HTTP, DNS, SSH)
Layer 4 - Transport    (TCP, UDP)
Layer 3 - Network      (IP)
Layer 2 - Data Link    (Ethernet, MAC)
Layer 1 - Physical     (Cables, signals)
```

### Key Protocols
- **TCP** (Transmission Control Protocol): Connection-oriented, reliable
  - Flags: SYN, ACK, FIN, RST, PSH, URG
  - 3-way handshake: SYN → SYN-ACK → ACK
- **UDP** (User Datagram Protocol): Connectionless, fast
- **ICMP** (Internet Control Message Protocol): Ping, traceroute
- **Common Ports**:
  - 80 (HTTP), 443 (HTTPS), 22 (SSH), 21 (FTP)
  - 53 (DNS), 25 (SMTP), 3389 (RDP)

### Packet Structure
```
[Ethernet Header] [IP Header] [TCP/UDP Header] [Application Data]
```

---

## Getting Started with tcpdump

### Installation
```bash
# Debian/Ubuntu
sudo apt-get install tcpdump

# Red Hat/CentOS
sudo yum install tcpdump

# Check installation
tcpdump --version
```

### Required Permissions
```bash
# Run with sudo (captures on all interfaces)
sudo tcpdump

# Or grant capabilities (Linux)
sudo setcap cap_net_raw,cap_net_admin=eip /usr/bin/tcpdump
```

### Basic Syntax
```bash
tcpdump [options] [filter expression]
```

### Essential Options

| Option | Description | Example |
|--------|-------------|---------|
| `-i` | Interface to capture on | `-i eth0` |
| `-c` | Number of packets to capture | `-c 100` |
| `-n` | Don't resolve hostnames | `-n` |
| `-nn` | Don't resolve hostnames or ports | `-nn` |
| `-v, -vv, -vvv` | Verbose output (increasing levels) | `-vv` |
| `-w` | Write to file (pcap format) | `-w capture.pcap` |
| `-r` | Read from file | `-r capture.pcap` |
| `-A` | Print packet in ASCII | `-A` |
| `-X` | Print packet in hex and ASCII | `-X` |
| `-s` | Snapshot length (bytes to capture) | `-s 0` (full packet) |
| `-D` | List available interfaces | `-D` |

### Your First Capture
```bash
# List interfaces
sudo tcpdump -D

# Capture 10 packets on eth0
sudo tcpdump -i eth0 -c 10

# Capture with no name resolution (faster)
sudo tcpdump -i eth0 -nn -c 10
```

---

## Understanding Packet Capture Output

### Default Output Format
```
15:43:25.123456 IP 192.168.1.100.54321 > 93.184.216.34.80: Flags [S], seq 123456, win 29200, length 0
```

**Breaking it down:**
- `15:43:25.123456` - Timestamp
- `IP` - Protocol (IP layer)
- `192.168.1.100.54321` - Source IP and port
- `>` - Direction
- `93.184.216.34.80` - Destination IP and port
- `Flags [S]` - TCP flags (S = SYN)
- `seq 123456` - Sequence number
- `win 29200` - Window size
- `length 0` - Payload length

### TCP Flags
- **S** - SYN (synchronize)
- **A** - ACK (acknowledge)
- **F** - FIN (finish)
- **R** - RST (reset)
- **P** - PSH (push)
- **.** - No flags (sometimes used for ACK)

### Example: TCP 3-Way Handshake
```
14:23:10.001 IP client.12345 > server.80: Flags [S]    # SYN
14:23:10.002 IP server.80 > client.12345: Flags [S.]   # SYN-ACK
14:23:10.003 IP client.12345 > server.80: Flags [.]    # ACK
```

---

## Berkeley Packet Filter (BPF) Syntax

BPF filters allow you to capture **exactly** what you need, reducing noise and storage.

### Filter Primitives

#### 1. **Type** Qualifiers (What to match)
- `host` - Match specific host
- `net` - Match network
- `port` - Match port
- `portrange` - Match port range

```bash
# Specific host
sudo tcpdump host 192.168.1.100

# Network (CIDR notation)
sudo tcpdump net 192.168.1.0/24

# Specific port
sudo tcpdump port 80

# Port range
sudo tcpdump portrange 1000-2000
```

#### 2. **Direction** Qualifiers (Traffic direction)
- `src` - Source
- `dst` - Destination
- `src or dst` - Either (default)

```bash
# Traffic FROM specific host
sudo tcpdump src host 192.168.1.100

# Traffic TO specific host
sudo tcpdump dst host 192.168.1.100

# Traffic TO specific port
sudo tcpdump dst port 443
```

#### 3. **Protocol** Qualifiers
- `tcp`, `udp`, `icmp`, `ip`, `ip6`, `arp`, `ether`

```bash
# Only TCP traffic
sudo tcpdump tcp

# Only ICMP (ping) traffic
sudo tcpdump icmp

# Only ARP traffic
sudo tcpdump arp
```

### Logical Operators

Combine filters using Boolean logic:
- `and` or `&&` - Both conditions must be true
- `or` or `||` - Either condition must be true
- `not` or `!` - Negation

```bash
# HTTP traffic to/from specific host
sudo tcpdump host 192.168.1.100 and port 80

# SSH or RDP traffic
sudo tcpdump port 22 or port 3389

# All traffic except SSH
sudo tcpdump not port 22

# Complex: TCP traffic on port 80 or 443, not from localhost
sudo tcpdump 'tcp and (port 80 or port 443) and not src host 127.0.0.1'
```

**Note**: Use quotes for complex expressions to prevent shell interpretation!

### Advanced BPF: TCP Flags

Match specific TCP flags using byte offsets:

```bash
# TCP SYN packets (connection attempts)
sudo tcpdump 'tcp[tcpflags] & tcp-syn != 0'

# TCP SYN-ACK packets
sudo tcpdump 'tcp[tcpflags] & (tcp-syn|tcp-ack) == (tcp-syn|tcp-ack)'

# TCP RST packets (connection resets)
sudo tcpdump 'tcp[tcpflags] & tcp-rst != 0'

# TCP FIN packets (connection termination)
sudo tcpdump 'tcp[tcpflags] & tcp-fin != 0'
```

### Advanced BPF: Payload Matching

Search for patterns in packet payload:

```bash
# HTTP GET requests
sudo tcpdump -A 'tcp port 80 and (tcp[((tcp[12:1] & 0xf0) >> 2):4] = 0x47455420)'

# Simplified: Look for "GET" in payload (less precise)
sudo tcpdump -A -s 0 'tcp port 80' | grep -i 'GET'

# Packets with specific payload size
sudo tcpdump 'ip[2:2] > 1500'
```

---

## Progressive Examples

### Level 1: Basic Filtering

```bash
# Capture all HTTP traffic
sudo tcpdump -i eth0 port 80

# Capture all HTTPS traffic
sudo tcpdump -i eth0 port 443

# Capture DNS queries
sudo tcpdump -i eth0 port 53

# Capture traffic to/from specific IP
sudo tcpdump -i eth0 host 8.8.8.8

# Capture all traffic on local subnet
sudo tcpdump -i eth0 net 192.168.1.0/24
```

### Level 2: Combining Filters

```bash
# Web traffic (HTTP and HTTPS) from specific host
sudo tcpdump -i eth0 'host 192.168.1.50 and (port 80 or port 443)'

# All traffic except SSH and DNS (reduce noise)
sudo tcpdump -i eth0 'not port 22 and not port 53'

# TCP traffic on non-standard ports (potential backdoor)
sudo tcpdump -i eth0 'tcp and not (port 80 or port 443 or port 22 or port 21)'

# Capture traffic between two specific hosts
sudo tcpdump -i eth0 'host 192.168.1.100 and host 192.168.1.200'
```

### Level 3: Protocol Analysis

```bash
# Capture TCP SYN scans (port scanning detection)
sudo tcpdump -i eth0 'tcp[tcpflags] & tcp-syn != 0 and tcp[tcpflags] & tcp-ack == 0'

# Capture TCP connections with data (PSH flag)
sudo tcpdump -i eth0 'tcp[tcpflags] & tcp-push != 0'

# Detect TCP RST attacks
sudo tcpdump -i eth0 'tcp[tcpflags] & tcp-rst != 0'

# ARP requests (detect ARP spoofing attempts)
sudo tcpdump -i eth0 arp

# ICMP echo requests (ping detection)
sudo tcpdump -i eth0 'icmp[icmptype] == icmp-echo'
```

### Level 4: Advanced Security Monitoring

```bash
# Detect potential SYN flood attack
sudo tcpdump -i eth0 -c 1000 'tcp[tcpflags] & tcp-syn != 0' | wc -l

# Capture SMB traffic (common in lateral movement)
sudo tcpdump -i eth0 'port 445 or port 139'

# Monitor for potential data exfiltration (large outbound transfers)
sudo tcpdump -i eth0 -nn 'dst net not 192.168.0.0/16 and greater 1000'

# Detect cleartext credentials (FTP, Telnet, HTTP Auth)
sudo tcpdump -i eth0 -A -s 0 'port 21 or port 23 or (port 80 and tcp[20:4] = 0x41757468)'

# Capture traffic to known malicious IP (IOC hunting)
sudo tcpdump -i eth0 'host 185.220.101.1'
```

---

## Advanced Techniques

### Writing and Reading Capture Files

```bash
# Write capture to file (full packets)
sudo tcpdump -i eth0 -s 0 -w capture.pcap

# Write with rotation (1000 packets per file, max 5 files)
sudo tcpdump -i eth0 -w capture.pcap -C 10 -W 5

# Read from file
tcpdump -r capture.pcap

# Read from file with filter applied
tcpdump -r capture.pcap 'port 80'

# Read and display hex/ASCII
tcpdump -r capture.pcap -X
```

### Filtering After Capture
```bash
# Capture everything first
sudo tcpdump -i eth0 -s 0 -w full_capture.pcap

# Then analyze specific aspects
tcpdump -r full_capture.pcap 'host 192.168.1.100' -w host_traffic.pcap
tcpdump -r full_capture.pcap 'port 80' -w http_traffic.pcap
```

### Time-Based Filtering
```bash
# Capture for specific duration (60 seconds)
sudo timeout 60 tcpdump -i eth0 -w timed_capture.pcap

# Or use -G for rotation
sudo tcpdump -i eth0 -G 60 -w capture_%Y%m%d_%H%M%S.pcap
```

### Capturing on Multiple Interfaces
```bash
# Capture on all interfaces
sudo tcpdump -i any

# Capture on multiple specific interfaces
sudo tcpdump -i eth0 -i wlan0
```

### Advanced Display Options
```bash
# Show packet contents with absolute sequence numbers
sudo tcpdump -i eth0 -S

# Show link-level headers (Ethernet)
sudo tcpdump -i eth0 -e

# Timestamp in readable format
sudo tcpdump -i eth0 -tttt

# Display packet with full protocol decode
sudo tcpdump -i eth0 -vvv -X
```

---

## Best Practices & Ethics

### Performance Considerations
1. **Use specific filters** - Reduce CPU and disk usage
2. **Limit snapshot length** - Use `-s 96` for headers only if payload not needed
3. **Write to fast disk** - SSDs preferred for high-traffic captures
4. **Monitor disk space** - Use rotation (`-C` and `-W` options)

### Security & Privacy
1. **Encrypt stored captures** - Contains sensitive data
2. **Limit access** - Only authorized personnel should access captures
3. **Sanitize before sharing** - Remove sensitive info from pcaps
4. **Delete after analysis** - Don't retain longer than necessary
5. **Follow data protection laws** - GDPR, HIPAA, etc.

### Ethical Guidelines
✅ **DO:**
- Obtain written permission before capturing network traffic
- Document your capture activities (what, when, why)
- Capture only what's necessary for your objective
- Secure capture files appropriately
- Notify relevant parties when required

❌ **DON'T:**
- Capture traffic on networks you don't own/authorize
- Share captures containing personal/sensitive information
- Use captured data outside the scope of authorization
- Capture traffic for malicious purposes
- Ignore legal requirements in your jurisdiction

---

## Quick Reference Cheat Sheet

### Common One-Liners

```bash
# Basic captures
sudo tcpdump -i eth0 -nn                          # Capture all traffic, no DNS resolution
sudo tcpdump -i eth0 -nn -c 100                   # Capture 100 packets
sudo tcpdump -i eth0 -w capture.pcap              # Save to file

# Protocol-specific
sudo tcpdump -i eth0 icmp                         # ICMP only (ping)
sudo tcpdump -i eth0 tcp                          # TCP only
sudo tcpdump -i eth0 udp                          # UDP only

# Port-based
sudo tcpdump -i eth0 port 80                      # HTTP
sudo tcpdump -i eth0 port 443                     # HTTPS
sudo tcpdump -i eth0 port 53                      # DNS
sudo tcpdump -i eth0 'port 80 or port 443'        # Web traffic

# Host-based
sudo tcpdump -i eth0 host 192.168.1.100           # Specific host
sudo tcpdump -i eth0 src host 192.168.1.100       # From host
sudo tcpdump -i eth0 dst host 192.168.1.100       # To host
sudo tcpdump -i eth0 net 192.168.1.0/24           # Subnet

# Security monitoring
sudo tcpdump -i eth0 'tcp[tcpflags] & tcp-syn != 0 and tcp[tcpflags] & tcp-ack == 0'  # SYN scan
sudo tcpdump -i eth0 'tcp[tcpflags] & tcp-rst != 0'                                    # RST packets
sudo tcpdump -i eth0 -A 'port 21 or port 23'                                           # Cleartext protocols

# Advanced
sudo tcpdump -i eth0 'tcp and (port 80 or port 443) and not host 192.168.1.1'        # Complex filter
sudo tcpdump -i eth0 -s 0 -A 'tcp dst port 80 and tcp[32:4] = 0x47455420'            # HTTP GET
sudo tcpdump -r capture.pcap -qns 0 -A | grep -i 'password'                           # Search in pcap
```

### Essential BPF Operators Summary

| Operator | Meaning | Example |
|----------|---------|---------|
| `and`, `&&` | Logical AND | `host 192.168.1.1 and port 80` |
| `or`, `\|\|` | Logical OR | `port 80 or port 443` |
| `not`, `!` | Logical NOT | `not port 22` |
| `>` | Greater than | `greater 1500` |
| `<` | Less than | `less 64` |
| `=`, `==` | Equals | `tcp[tcpflags] == tcp-syn` |
| `!=` | Not equals | `tcp[tcpflags] & tcp-syn != 0` |

---

## Hands-On Practice Exercises

### Exercise 1: Basic Capture
1. List available interfaces
2. Capture 20 packets on your primary interface
3. Identify different protocols in the output

### Exercise 2: Filtered Capture
1. Capture only ICMP traffic while pinging a website
2. Capture only DNS queries (port 53)
3. Capture HTTP traffic while visiting a website

### Exercise 3: BPF Filters
1. Write a filter for: TCP traffic on port 80 from your IP
2. Write a filter for: All traffic except SSH
3. Write a filter for: Detect TCP SYN packets

### Exercise 4: Save and Analyze
1. Capture web browsing traffic to a file
2. Read the file and filter for a specific host
3. Extract packets with ASCII payload using `-A`

---

## Additional Resources

- **Official Documentation**: `man tcpdump`
- **BPF Syntax**: `man pcap-filter`
- **Wireshark**: GUI tool for deeper analysis of pcap files
- **tcpreplay**: Replay captured traffic for testing
- **Online Practice**:
  - Malware-traffic-analysis.net (pcap samples)
  - PacketLife.net (capture samples)

---

## Summary: Key Takeaways

1. **tcpdump is powerful** - Essential tool for network analysis and security
2. **BPF filters are critical** - Reduce noise, capture exactly what you need
3. **Start simple, build complexity** - Master basic syntax before advanced filters
4. **Always get permission** - Legal and ethical considerations are paramount
5. **Practice regularly** - Hands-on experience is the best teacher
6. **Combine with other tools** - Wireshark, tshark, grep, awk for analysis
7. **Document your work** - Keep notes on useful filters for your use cases

---

**Happy Packet Hunting! 🔍📡**

*Remember: With great power comes great responsibility. Use these skills ethically and legally.*

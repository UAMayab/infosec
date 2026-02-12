# Sample PCAP Files for tcpdump Workshop

## File Descriptions

### 01_port_scan.pcap
**Use Case**: Incident Response - Port Scan Detection
**Contents**: TCP SYN scan targeting common ports (21, 22, 23, 25, 80, 110, 143, 443, 445, 3306, 3389, 8080)
**Practice**: Detect scanning patterns, identify open ports, extract attacker IP

**Useful Commands**:
```bash
# View all traffic
tcpdump -r 01_port_scan.pcap -nn

# Extract SYN packets only
tcpdump -r 01_port_scan.pcap -nn 'tcp[tcpflags] & tcp-syn != 0 and tcp[tcpflags] & tcp-ack == 0'

# Count scan attempts
tcpdump -r 01_port_scan.pcap -nn 'tcp[tcpflags] & tcp-syn != 0' | wc -l

# Find which ports were scanned
tcpdump -r 01_port_scan.pcap -nn 'tcp[tcpflags] & tcp-syn != 0' | awk '{print $5}' | cut -d'.' -f5 | sort -n | uniq
```

---

### 02_http_credentials.pcap
**Use Case**: Penetration Testing - Credential Capture
**Contents**: HTTP POST requests with cleartext credentials, SQL injection attempts, XSS payloads
**Practice**: Extract credentials, identify attack payloads, document security findings

**Useful Commands**:
```bash
# View HTTP traffic with payload
tcpdump -r 02_http_credentials.pcap -A -nn

# Extract POST requests
tcpdump -r 02_http_credentials.pcap -A 'tcp port 80' | grep -A 5 'POST'

# Find credentials
tcpdump -r 02_http_credentials.pcap -A | grep -E -i '(username|password)'

# Find SQL injection attempts
tcpdump -r 02_http_credentials.pcap -A | grep -E "(SELECT|UNION|OR 1=1)"

# Find XSS attempts
tcpdump -r 02_http_credentials.pcap -A | grep -i '<script'
```

---

### 03_web_browsing.pcap
**Use Case**: General Analysis
**Contents**: Normal web browsing traffic (HTTP/HTTPS)
**Practice**: Identify protocols, analyze connection patterns, practice basic filtering

**Useful Commands**:
```bash
# View all traffic
tcpdump -r 03_web_browsing.pcap -nn

# HTTP only
tcpdump -r 03_web_browsing.pcap -nn 'port 80'

# HTTPS only
tcpdump -r 03_web_browsing.pcap -nn 'port 443'

# Unique destination IPs
tcpdump -r 03_web_browsing.pcap -nn | awk '{print $5}' | cut -d'.' -f1-4 | sort -u
```

---

### 04_tcp_handshake.pcap
**Use Case**: Protocol Learning
**Contents**: Clean TCP 3-way handshakes
**Practice**: Identify SYN, SYN-ACK, ACK sequence, understand TCP flags

**Useful Commands**:
```bash
# View all with verbose output
tcpdump -r 04_tcp_handshake.pcap -nn -vv

# Show only SYN packets
tcpdump -r 04_tcp_handshake.pcap -nn 'tcp[tcpflags] & tcp-syn != 0'

# Show handshake sequence
tcpdump -r 04_tcp_handshake.pcap -nn 'tcp[tcpflags] & (tcp-syn|tcp-ack) != 0' | head -20
```

---

### 05_icmp_ping.pcap
**Use Case**: Network Troubleshooting
**Contents**: ICMP echo request and reply packets
**Practice**: Analyze ping responses, calculate RTT, detect network connectivity

**Useful Commands**:
```bash
# View all ICMP traffic
tcpdump -r 05_icmp_ping.pcap -nn

# Echo requests only
tcpdump -r 05_icmp_ping.pcap -nn 'icmp[icmptype] == icmp-echo'

# Echo replies only
tcpdump -r 05_icmp_ping.pcap -nn 'icmp[icmptype] == icmp-echoreply'

# Verbose for TTL and timing
tcpdump -r 05_icmp_ping.pcap -nn -vv
```

---

### 06_dns_queries.pcap
**Use Case**: Network Analysis
**Contents**: DNS queries and responses
**Practice**: Extract domain names, identify DNS servers, analyze query types

**Useful Commands**:
```bash
# View all DNS traffic
tcpdump -r 06_dns_queries.pcap -nn

# DNS queries only
tcpdump -r 06_dns_queries.pcap -nn 'udp port 53'

# Verbose DNS with full decode
tcpdump -r 06_dns_queries.pcap -nn -vv

# ASCII payload view
tcpdump -r 06_dns_queries.pcap -nn -A
```

---

## Workshop Exercise Suggestions

### Beginner Exercises
1. Open each PCAP and identify the primary protocol
2. Count the total number of packets in each file
3. Find the source and destination IPs in each capture
4. Identify TCP flags in the handshake PCAP

### Intermediate Exercises
1. Extract all unique destination ports from the port scan PCAP
2. Find and document all credentials in the HTTP PCAP
3. Calculate ping RTT from ICMP PCAP
4. List all domain names queried in DNS PCAP

### Advanced Exercises
1. Write a BPF filter that isolates only the port scan attack traffic
2. Create a penetration test report using evidence from HTTP PCAP
3. Build a timeline of events from the port scan PCAP
4. Correlate DNS queries with subsequent HTTP connections

---

## Tips for Analysis

1. **Always start with basic viewing**: `tcpdump -r file.pcap -nn`
2. **Use -A for ASCII payloads**: Useful for HTTP, DNS, clear text protocols
3. **Use -X for hex dump**: Detailed packet inspection
4. **Use -vv or -vvv**: More verbose protocol details
5. **Combine with grep/awk**: Extract specific information
6. **Import into Wireshark**: For visual analysis and advanced features

---

## Regenerating PCAPs

To regenerate these PCAPs with fresh traffic, run:
```bash
sudo ./generate_sample_pcaps.sh
```

Customize the script variables for your environment:
- `TARGET_IP` - IP to scan in port scan PCAP
- `ATTACKER_IP` - Source IP for attacks

---

**Created for**: tcpdump & BPF Rules Workshop
**License**: Educational Use
**Note**: These PCAPs are safe for educational analysis and contain no real sensitive data.

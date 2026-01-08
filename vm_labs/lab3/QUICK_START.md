# Liverpul Corp VM - Quick Start Guide

## Prerequisites

### Required Tools
Install these on your attacking machine (Kali Linux recommended):

```bash
sudo apt update
sudo apt install -y nmap hydra mysql-client curl ftp
```

### Optional Tools
```bash
sudo apt install -y wireshark sqlmap nikto gobuster
```

---

## Step 1: Start the VM

```bash
# Navigate to lab directory
cd /home/mguirao/code/infosec/vm_labs/lab3

# Start the VM (first time will download the box)
vagrant up

# Wait for provisioning to complete (~5 minutes)
```

**Expected Output:**
```
=== Setup Complete ===
IP Address: 192.168.56.10
```

---

## Step 2: Verify VM is Running

```bash
# Check VM status
vagrant status

# Should show: "running (virtualbox)"

# Test connectivity
ping -c 4 192.168.56.10
```

---

## Step 3: Your First Scan

```bash
# Quick port scan
nmap -F 192.168.56.10

# Expected open ports: 21, 22, 80, 3306
```

---

## Step 4: Test Each Service

### Test Web Server
```bash
# In browser, visit:
http://192.168.56.10

# Or use curl:
curl http://192.168.56.10
```

**What to see:** Angular Material website with "Welcome to Liverpul Corp"

### Test FTP
```bash
ftp 192.168.56.10
# Username: anonymous
# Password: (just press Enter)

ls
bye
```

**What to see:** Directory listing with `pub` directory

### Test Database
```bash
mysql -h 192.168.56.10 -u admin -padmin -e "SHOW DATABASES;"
```

**What to see:** List of databases including `fakystore`

---

## Step 5: Begin Exercises

### Recommended Order
1. Start with **Exercise 1: Network Reconnaissance**
2. Read the [LAB_EXERCISES.md](LAB_EXERCISES.md) file
3. Keep the [CHEAT_SHEET.md](CHEAT_SHEET.md) open for reference
4. Document everything!

### Create Your Workspace
```bash
# On your attacking machine
mkdir -p ~/labs/liverpul_corp
cd ~/labs/liverpul_corp

# Create notes file
cat > notes.md << 'EOF'
# Liverpul Corp Penetration Test Notes

## Target Information
- IP: 192.168.56.10
- Hostname: liverpul-corp
- Start Time: $(date)

## Findings
[Document your findings here]

## Commands Used
[Keep a command history]
EOF

# Create directories for evidence
mkdir screenshots scans captures
```

---

## Common First Steps

### 1. Comprehensive Scan
```bash
nmap -sV -sC -oA scans/initial_scan 192.168.56.10
```

### 2. Service Enumeration
```bash
# FTP
nmap --script ftp-* 192.168.56.10 -p 21

# HTTP
nikto -h http://192.168.56.10

# MySQL
nmap --script mysql-* 192.168.56.10 -p 3306
```

### 3. Create Wordlists
```bash
# Copy sample wordlists
cp /home/mguirao/code/infosec/vm_labs/lab3/sample_wordlists/* .

# Or use system wordlists
cp /usr/share/wordlists/rockyou.txt .
```

### 4. Start Password Attack
```bash
# FTP brute force
hydra -L usernames.txt -P passwords.txt ftp://192.168.56.10 -V
```

---

## Tips for Success

### 1. Take Screenshots
- Use `scrot` or `gnome-screenshot` on Linux
- Press `PrintScreen` and save images
- Organize by exercise number

### 2. Document Commands
```bash
# Save command history
history > commands.txt

# Or use script to record session
script -a session.log
# ... do your work ...
exit
```

### 3. Use Tabs
Keep multiple terminal tabs open:
- Tab 1: Scanning and enumeration
- Tab 2: Exploitation attempts
- Tab 3: Notes and documentation
- Tab 4: Packet capture (if needed)

### 4. Check Your Progress
Use the checklist in LAB_EXERCISES.md to track completed exercises.

---

## Quick Reference

### Target Details
| Service | Port | Credentials | Status |
|---------|------|-------------|--------|
| FTP | 21 | anonymous:(blank) | ✅ Accessible |
| SSH | 22 | ??? | 🔒 Need to discover |
| HTTP | 80 | N/A | ✅ Accessible |
| MySQL | 3306 | ??? | 🔒 Need to discover |

### Useful Commands
```bash
# Service version scan
nmap -sV 192.168.56.10

# Test anonymous FTP
curl ftp://192.168.56.10/ --user anonymous:

# Test web API
curl http://192.168.56.10/api.php | jq .

# Password attack
hydra -l USERNAME -P passwords.txt SERVICE://192.168.56.10
```

---

## Troubleshooting

### VM Not Responding
```bash
# Check VM status
vagrant status

# Reload VM
vagrant reload

# If still issues, restart
vagrant halt
vagrant up
```

### Can't Connect to Services
```bash
# Verify VM IP
vagrant ssh -c "ip addr show eth1"

# Check services are running
vagrant ssh -c "sudo rc-service --list | grep -E '(mariadb|nginx|vsftpd)'"

# Restart services
vagrant ssh -c "sudo rc-service nginx restart"
vagrant ssh -c "sudo rc-service mariadb restart"
vagrant ssh -c "sudo rc-service vsftpd restart"
```

### MySQL Connection Refused
```bash
# Check if MySQL is listening on network
vagrant ssh -c "sudo netstat -tlnp | grep 3306"

# Should show: 0.0.0.0:3306 (listening on all interfaces)
```

### Hydra Not Working
```bash
# Check hydra version
hydra -h

# Make sure wordlists exist
ls -lh usernames.txt passwords.txt

# Try with verbose mode
hydra -V -l admin -P passwords.txt mysql://192.168.56.10
```

---

## Safety Reminders

### ✅ DO
- Document everything
- Test only this VM (192.168.56.10)
- Learn from mistakes
- Ask for help when stuck
- Take breaks
- Read error messages carefully

### ❌ DON'T
- Test systems without permission
- Skip documentation
- Give up too quickly
- Use excessive force (e.g., full rockyou.txt immediately)
- Forget to clean up persistence mechanisms
- Share solutions with classmates before completion

---

## Getting Help

### If You're Stuck
1. Re-read the exercise instructions carefully
2. Check the [CHEAT_SHEET.md](CHEAT_SHEET.md) for command examples
3. Review your command history for errors
4. Check the troubleshooting section
5. Ask your instructor (don't look at SOLUTIONS.md yet!)

### Red Flags You're On the Right Track
- ✅ Finding open ports
- ✅ Discovering service versions
- ✅ Getting successful logins
- ✅ Accessing databases
- ✅ Finding weak passwords

### Red Flags You Might Be Lost
- ❌ All scans return nothing
- ❌ Can't connect to any service
- ❌ No passwords work after 100+ attempts
- ❌ Tools crash or hang
- ❌ Services don't respond

---

## Time Management

### Recommended Schedule (4-hour session)

**Hour 1: Reconnaissance**
- 0:00-0:15: Setup and initial scan
- 0:15-0:30: Service enumeration
- 0:30-0:45: FTP enumeration
- 0:45-1:00: Web application analysis

**Hour 2: Exploitation**
- 1:00-1:30: Password attacks (FTP + MySQL)
- 1:30-1:45: Database enumeration
- 1:45-2:00: Data extraction

**Hour 3: Advanced Access**
- 2:00-2:30: Gain SSH access
- 2:30-3:00: Privilege escalation

**Hour 4: Documentation**
- 3:00-3:30: Organize findings
- 3:30-4:00: Write report

---

## Success Criteria

### You've Completed the Basic Lab When:
- [ ] All 4 ports are identified
- [ ] Service versions are documented
- [ ] Anonymous FTP access confirmed
- [ ] 3 user accounts discovered
- [ ] Database accessed
- [ ] All database tables enumerated
- [ ] Web application tested
- [ ] Report written with screenshots

### You've Completed the Advanced Lab When:
- [ ] SSH access obtained
- [ ] Privilege escalation successful
- [ ] Password hashes extracted
- [ ] Network traffic analyzed
- [ ] Comprehensive report completed
- [ ] All persistence mechanisms removed

---

## Next Steps After Completion

### 1. Write Your Report
Use the template in LAB_EXERCISES.md Exercise 9

### 2. Practice Defense
Try the hardening checklist in LAB_EXERCISES.md

### 3. Reset and Repeat
```bash
vagrant destroy -f && vagrant up
# Try to complete it faster!
```

### 4. Advanced Challenges
- Complete the entire assessment in under 30 minutes
- Write custom exploit scripts
- Set up IDS/IPS rules to detect your attacks
- Perform the same attacks from a different OS (Windows)

---

## Resources During Lab

### Keep These Open
1. This QUICK_START.md file
2. [LAB_EXERCISES.md](LAB_EXERCISES.md) - Full exercise descriptions
3. [CHEAT_SHEET.md](CHEAT_SHEET.md) - Command reference
4. Your notes.md file
5. Terminal with multiple tabs

### Online Resources (If Allowed)
- GTFOBins: https://gtfobins.github.io/
- Nmap Documentation: https://nmap.org/book/
- Hydra Manual: `man hydra`

### Don't Look At (Yet!)
- ❌ [SOLUTIONS.md](SOLUTIONS.md) - Only for instructors or after completion

---

## Final Checklist Before Starting

- [ ] VM is running (`vagrant status`)
- [ ] Can ping 192.168.56.10
- [ ] Tools are installed (nmap, hydra, etc.)
- [ ] Workspace directory created
- [ ] Notes file ready
- [ ] Screenshot tool working
- [ ] Lab exercises document open
- [ ] Cheat sheet accessible
- [ ] Sufficient time allocated (4-6 hours)

---

## Emergency Commands

```bash
# Complete reset
vagrant destroy -f && vagrant up

# Quick restart
vagrant reload

# SSH into VM
vagrant ssh

# Check VM IP
vagrant ssh -c "ip addr"

# Stop VM
vagrant halt

# VM status
vagrant status
```

---

## Good Luck!

Remember:
- **Enumerate thoroughly** - The more you know, the easier exploitation becomes
- **Document everything** - You'll thank yourself during report writing
- **Be systematic** - Work through exercises in order
- **Learn from failures** - Failed attempts teach as much as successes
- **Have fun** - Ethical hacking is challenging but rewarding!

**Start Time:** ___________________

**Target IP:** 192.168.56.10

**Let's begin!**

---

## First Command to Run

```bash
nmap -sV -sC 192.168.56.10 -oA initial_scan
```

Now open LAB_EXERCISES.md and start with Exercise 1!

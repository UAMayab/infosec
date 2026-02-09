# Introduction to nmap (network mapper)
All commands must be executed with root privileges using sudo.

## Host Discovery

### Scans
- Check if nmap is installed: nmap --version
- Perform a Ping scan or Ping sweep: sudo nmap -sn 10.0.0.0/24 (-sn disables port scanning). Packets send: TCP SYN packet to port 443, a TCP ACK packet to port 80, and an ICMP echo and timestamp requests.
- sudo nmap -sn --traceroute 10.0.0.46 10.0.0.78
- sudo nmap -sn --script dns-brute websec.mx
- sudo nmap -sn --script broadcast-ping --script-args=newtargets 10.0.0.0/24


## Port Scanning

### Port possible states.
- Open: Open indicates that a service is listening for connections on this port.
- Closed: Closed indicates that the probes were received, but it was concluded that there was no service running on this port.
- Filtered: Filtered indicates that there were no signs that the probes were received and the state could not be established. This could indicate that the probes are being dropped by some kind of filtering.
- Unfiltered: Unfiltered indicates that the probes were received but a state could not be established.
- Open/Filtered: This indicates that the port was filtered or open but the state could not be established.
- Closed/Filtered: This indicates that the port was filtered or closed but the state could not be established.

### Scans
- sudo nmap 189.172.186.62 # SYN stealth scan
- sudo nmap -Pn 189.172.186.62 # do not perform host discovery
- sudo nmap -Pn -n 189.172.186.62 # and do not perform reverse DNS


### Scanning specific port ranges
- Port list separated by commas: $ nmap -p80,443 localhost
- Port range denoted with hyphens: $ nmap -p1-100 localhost
- Alias for all ports from 1 to 65535: # nmap -p- localhost
- Specific ports by protocol: # nmap -pT:25,U:53 <target>
- Service name: # nmap -p smtp <target>
- Service name with wildcards: # nmap -p smtp* <target>
- Only ports registered in the Nmap services database: # nmap -p[1-65535] <target>

### Scans
- sudo nmap -Pn -n 10.0.0.* --exclude 10.0.0.1 # excluding IPs 
- sudo nmap -Pn -n -iL exclude_ips.txt # reading IPs from a file


## Fingerprinting OS
- nmap -O <target> # OS detection
- nmap -O --ososcan-guess <target> # OS detection, if all fails!
- nmap -O -v <target> # OS detection, verbose mode

## Version Detection
- nmap -sV <target> # version detection
- nmap -sV --version-intensity [0-9] <target> # version detection intensity

- nmap -A <target> # agressive detection, -O, -sV, -sC and --traceroute all combined, very noisy


## Nmap Scripting Engine (NSE)
- nmap -sC <target> # run default scripts

NSE scripts are divided into the following categories:
- auth: This category is for scripts related to user authentication.
- broadcast: This is a very interesting category of scripts that use broadcast petitions to gather information.
- brute: This category is for scripts that conduct brute-force password auditing attacks.
- default: This category is for scripts that are executed when a script scan is executed (-sC). Scripts in this category are considered safe and non-intrusive.
- discovery: This category is for scripts related to host and service discovery.
- dos: This category is for scripts related to denial-of-service attacks.
- exploit: This category is for scripts that exploit security vulnerabilities.
- external: This category is for scripts that depend on a third-party service.
- fuzzer: This category is for NSE scripts that are focused on fuzzing.
- intrusive: This category is for scripts that might crash something or generate a lot of network noise; scripts that system administrators may consider intrusive belong to this category.
- malware: This category is for scripts related to malware detection.
- safe: This category is for scripts that are considered safe in all situations.
- version: This category is for scripts that are used for the advanced versioning of services.
- vuln: This category is for scripts related to security vulnerabilities.

## NSE arguments
- nmap --script http-title --script-args http.useragent="Mozilla 4.20" <target> # select a script and configure parameters
- nmap -p80 --script http-trace --script-args http-trace.path <target>
- nmap --script dns-brute <target> # specifying a single script to run
- nmap --script http-headers,http-title <target>
- nmap -sV --script vuln <target> # run all scripts in one category
- nmap -sV --script="version,discovery" <target> # multiple scripts selection
- nmap -sV --script "not exploit" <target>
- nmap -sV --script "(http-*) and not(http-slowloris or http- brute)" <target> # and, not or, * special keywords



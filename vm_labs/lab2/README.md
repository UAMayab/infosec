# Faky Faky Website - Social Engineering Lab

**Created by:** mguirao
**Date:** 2026-01-02
**Purpose:** Educational lab environment for learning about social engineering

---

## Overview

This project creates a local virtual machine running Alpine Linux with Apache HTTP Server, hosting a demonstration website for social engineering education. The website includes a login form and educational content about social engineering techniques and defenses.

## Project Structure

```
lab2/
├── Vagrantfile          # VM configuration
├── README.md            # This file
└── www/                 # Website files
    ├── index.html       # Main webpage with login form
    ├── style.css        # Stylesheet
    └── script.js        # JavaScript for form interaction
```

## Configuration Details

- **Operating System:** Alpine Linux (generic/alpine316)
- **Web Server:** Apache HTTP Server (latest)
- **VM IP Address:** 192.168.56.10
- **Domain Name:** fakyfakywebsite.com
- **Memory:** 512 MB
- **CPUs:** 1

## Setup Instructions

### Prerequisites

1. Install [VirtualBox](https://www.virtualbox.org/wiki/Downloads)
2. Install [Vagrant](https://www.vagrantup.com/downloads)

### Step 1: Start the Virtual Machine

Navigate to the project directory and start the VM:

```bash
cd /home/mguirao/code/infosec/vm_labs/lab2
vagrant up
```

This will:
- Download the Alpine Linux box (first time only)
- Configure the VM with a private network IP
- Install Apache HTTP Server
- Copy website files to the web root
- Start the Apache service

### Step 2: Configure Your Host File

To access the website using the domain name `fakyfakywebsite.com`, you need to add an entry to your host machine's hosts file.

#### On Linux/Mac:

```bash
sudo nano /etc/hosts
```

Add this line:
```
192.168.56.10  fakyfakywebsite.com
```

Save and exit (Ctrl+X, then Y, then Enter)

#### On Windows:

1. Open Notepad as Administrator
2. Open: `C:\Windows\System32\drivers\etc\hosts`
3. Add this line:
   ```
   192.168.56.10  fakyfakywebsite.com
   ```
4. Save the file

### Step 3: Access the Website

Open your web browser and navigate to:

- **Using domain name:** http://fakyfakywebsite.com
- **Using IP address:** http://192.168.56.10
- **Using port forwarding:** http://localhost:8080

## Demo Login Credentials

For testing the login form:

- **Username:** demo
- **Password:** demo123

Alternative (demonstrates weak credentials):
- **Username:** admin
- **Password:** password

**Note:** The authentication is client-side only for demonstration purposes. Real applications should never implement authentication this way!

## Vagrant Commands

Useful commands for managing your VM:

```bash
# Start the VM
vagrant up

# Stop the VM
vagrant halt

# Restart the VM
vagrant reload

# Delete the VM
vagrant destroy

# SSH into the VM
vagrant ssh

# Check VM status
vagrant status

# Re-run provisioning
vagrant provision
```

## Inside the VM

If you need to SSH into the VM and work with Apache:

```bash
vagrant ssh
```

Once inside the VM:

```bash
# Check Apache status
sudo rc-status | grep apache2

# Start Apache
sudo service apache2 start

# Stop Apache
sudo service apache2 stop

# Restart Apache
sudo service apache2 restart

# View Apache logs
sudo tail -f /var/log/apache2/fakyfakywebsite-access.log
sudo tail -f /var/log/apache2/fakyfakywebsite-error.log

# View website files
ls -la /var/www/fakyfakywebsite.com/public_html/
```

## Features

### Website Components

1. **Login Form**
   - Username and password fields
   - Remember me checkbox
   - Password recovery link
   - Registration link
   - Client-side validation

2. **Educational Content**
   - Introduction to social engineering
   - Visual examples with placeholder images
   - Information cards about:
     - Psychological manipulation
     - Common techniques
     - Protection strategies
     - Training importance

3. **Interactive JavaScript**
   - Form validation
   - Visual feedback
   - Demo authentication
   - Educational messages

### Security Education Features

- Demonstrates the importance of strong credentials
- Shows client-side validation (and its limitations)
- Includes educational warnings about production security
- Console messages with security best practices

## Troubleshooting

### VM won't start
```bash
# Check VirtualBox is running
VBoxManage --version

# Destroy and recreate the VM
vagrant destroy -f
vagrant up
```

### Website not loading
```bash
# Check if Apache is running in the VM
vagrant ssh -c "sudo rc-status | grep apache2"

# Check website files exist
vagrant ssh -c "ls -la /var/www/fakyfakywebsite.com/public_html/"

# Restart Apache
vagrant ssh -c "sudo service apache2 restart"
```

### Domain name not working
- Verify the hosts file entry is correct
- Try accessing via IP: http://192.168.56.10
- Clear browser cache
- Try a different browser

### Port conflict (8080 already in use)
Edit the Vagrantfile and change the forwarded port:
```ruby
config.vm.network "forwarded_port", guest: 80, host: 8081, host_ip: "127.0.0.1"
```
Then run `vagrant reload`

## Educational Context

This lab environment is designed for:
- Understanding social engineering attack vectors
- Learning about web application security
- Practicing secure development concepts
- Demonstrating common vulnerabilities

**IMPORTANT:** This is for educational purposes only. Do not use these techniques for malicious purposes. Always obtain proper authorization before performing security testing.

## Original Prompt

> Create a Vagrantfile using the Linux Alpine distro, with the latest Apache http server. Make sure the webserver can be reached on a LAN by using the internal domain name fakyfakywebsite.com. Maybe you will need to modify the host file in order to achieve this. Once you make sure that the http service is running and accessible using the internal domain name, also create a basic webpage using HTML, CSS and Javascript that presents to the user a login form to the "Faky Faky Website" where users can learn all about Social Engineering. This VM should have assigned a local IP address so it can be reached by a LAN IP address. Save this prompt as part of the documentation of the Vagrantfile, along with my username, date and time. If you have any question, ask me. Include a couple of images on the webpage related to Social Engineering.

---

## License

Educational Use Only - Information Security Lab Environment

## Support

For issues or questions, refer to the Vagrant and VirtualBox documentation:
- [Vagrant Documentation](https://www.vagrantup.com/docs)
- [VirtualBox Documentation](https://www.virtualbox.org/wiki/Documentation)


VM Status:
  - Alpine Linux VM running
  - Apache HTTP Server installed and started
  - Website files deployed correctly (index.html, style.css, script.js)
  - Accessible at IP: http://192.168.56.10

  What's Working:
  - HTTP server responding on port 80
  - All 3 website files present in /var/www/fakyfakywebsite.com/public_html/
  - Apache service running and set to start on boot

  Next Steps

  1. Add Host Entry (Required for domain name access)

  On Linux/Mac:
  sudo nano /etc/hosts
  Add this line:
  192.168.56.10  fakyfakywebsite.com

  On Windows:
  Run Notepad as Administrator, open C:\Windows\System32\drivers\etc\hosts, and add:
  192.168.56.10  fakyfakywebsite.com

  2. Access the Website

  Open your browser and visit:
  - http://fakyfakywebsite.com (after adding hosts entry)
  - http://192.168.56.10 (works now without hosts file)
  - http://localhost:8080 (port forwarding)

  3. Try the Login Form

  Demo credentials:
  - Username: demo
  - Password: demo123

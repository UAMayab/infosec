#!/bin/sh
# Provisioning script for vulnerable nmap workshop VM
# WARNING: This creates an INTENTIONALLY INSECURE system for educational purposes only!

echo "==> Starting provisioning of vulnerable services..."

# Update package index
apk update

# ===========================================
# 1. SSH Server - Root login without password
# ===========================================
echo "==> Configuring SSH server (root access, no password)..."
apk add openssh
rc-update add sshd

# Configure SSH for insecure access
sed -i 's/#PermitRootLogin.*/PermitRootLogin yes/' /etc/ssh/sshd_config
sed -i 's/#PermitEmptyPasswords.*/PermitEmptyPasswords yes/' /etc/ssh/sshd_config
sed -i 's/PasswordAuthentication.*/PasswordAuthentication yes/' /etc/ssh/sshd_config

# Remove root password
passwd -d root

# Start SSH service
rc-service sshd restart

# ===========================================
# 2. Apache Web Server
# ===========================================
echo "==> Installing Apache web server..."
apk add apache2 apache2-utils
rc-update add apache2

# Configure Apache
mkdir -p /var/www/localhost/htdocs
chown -R apache:apache /var/www/localhost/htdocs

# Start Apache
rc-service apache2 start

# ===========================================
# 3. Telnet Server - No credentials required
# ===========================================
echo "==> Configuring Telnet server (no authentication)..."
apk add busybox-extras

# Create OpenRC service for telnetd
cat > /etc/init.d/telnetd << 'EOF'
#!/sbin/openrc-run

name="telnetd"
description="Telnet Server (Insecure - for lab only)"
command="/usr/sbin/telnetd"
command_args="-F -l /bin/sh -p 23"
command_background=true
pidfile="/run/${RC_SVCNAME}.pid"

depend() {
    need net
    after firewall
}
EOF

# Make it executable
chmod +x /etc/init.d/telnetd

# Start telnet service
rc-update add telnetd default
rc-service telnetd start

# ===========================================
# 4. MySQL Server - Remote root access
# ===========================================
echo "==> Installing MySQL server (insecure root access)..."
apk add mysql mysql-client

# Initialize MySQL data directory
mysql_install_db --user=mysql --datadir=/var/lib/mysql

# Configure MySQL to allow remote connections
cat > /etc/my.cnf.d/remote.cnf << 'EOF'
[mysqld]
bind-address = 0.0.0.0
skip-networking = 0
EOF

# Start MySQL
rc-update add mariadb
rc-service mariadb start

# Wait for MySQL to start
sleep 5

# Configure MySQL root user with insecure access
mysql -u root << 'MYSQL_SCRIPT'
-- Allow root login from anywhere with password 'qwerty'
UPDATE mysql.user SET Password=PASSWORD('qwerty') WHERE User='root';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'qwerty' WITH GRANT OPTION;
FLUSH PRIVILEGES;

-- Create a sample database for the workshop
CREATE DATABASE IF NOT EXISTS vulnerable_db;
USE vulnerable_db;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    password VARCHAR(50),
    email VARCHAR(100)
);
INSERT INTO users VALUES (1, 'admin', 'admin123', 'admin@example.com');
INSERT INTO users VALUES (2, 'user', 'password', 'user@example.com');
MYSQL_SCRIPT

# ===========================================
# 5. Configure HTTP Authentication for website
# ===========================================
echo "==> Configuring HTTP Basic Authentication..."

# Create password file for HTTP authentication
htpasswd -cb /etc/apache2/.htpasswd admin 12345

# Configure Apache to use HTTP authentication
cat > /var/www/localhost/htdocs/.htaccess << 'EOF'
AuthType Basic
AuthName "Restricted Area - Lab Access"
AuthUserFile /etc/apache2/.htpasswd
Require valid-user
EOF

# Allow .htaccess overrides
sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/httpd.conf

# Restart Apache to apply changes
rc-service apache2 restart

# ===========================================
# Additional vulnerable services (bonus)
# ===========================================
echo "==> Installing additional services for scanning practice..."

# FTP server (optional - commented out by default)
# apk add vsftpd
# rc-update add vsftpd
# rc-service vsftpd start

# ===========================================
# Summary
# ===========================================
echo ""
echo "================================================================"
echo "  VULNERABLE VM PROVISIONING COMPLETE!"
echo "================================================================"
echo ""
echo "⚠️  WARNING: This VM is INTENTIONALLY INSECURE!"
echo "    Use only in isolated lab environments!"
echo ""
echo "Vulnerable Services Running:"
echo "  • SSH (port 22)     - root login, no password"
echo "  • HTTP (port 80)    - Apache with HTTP auth (admin/12345)"
echo "  • Telnet (port 23)  - No authentication required"
echo "  • MySQL (port 3306) - root/qwerty, remote access enabled"
echo ""
echo "VM IP Address: 192.168.56.10"
echo ""
echo "Example nmap scans:"
echo "  nmap -sV 192.168.56.10"
echo "  nmap -p- 192.168.56.10"
echo "  nmap -sC -sV -p22,80,23,3306 192.168.56.10"
echo ""
echo "================================================================"

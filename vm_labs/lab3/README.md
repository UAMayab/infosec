# Liverpul Corp VM - Lab 3

A vulnerable VM environment for security testing and practice.

## VM Specifications

- **OS**: Alpine Linux 3.18
- **IP Address**: 192.168.56.10 (Private Network)
- **Hostname**: liverpul-corp
- **Resources**: 1GB RAM, 2 CPUs

## Installed Services

### 1. MySQL/MariaDB (Port 3306)
- **Admin User**: `admin`
- **Admin Password**: `admin`
- **Database**: `fakystore`
- **Tables**:
  - `products` - Product catalog
  - `customers` - Customer information
  - `orders` - Order records

**Access**:
```bash
mysql -h 192.168.56.10 -u admin -padmin fakystore
```

### 2. vsFTPd (Port 21)
- **Anonymous Login**: Enabled
- **Local Users**: pedro, pablo, paco
- **Passive Ports**: 21100-21110

**Access**:
```bash
# Anonymous FTP
ftp 192.168.56.10
# Username: anonymous
# Password: (any or blank)

# User FTP
ftp 192.168.56.10
# Username: pedro/pablo/paco
# Password: See credentials below
```

### 3. Nginx Web Server (Port 80)
- **URL**: http://192.168.56.10
- **Document Root**: `/var/www/liverpul`
- **Features**: Angular Material dashboard displaying database tables

**Access**:
```bash
# From your browser or command line
curl http://192.168.56.10
```

## User Credentials

| Username | Password  | Purpose |
|----------|-----------|---------|
| pedro    | football  | System user with FTP access |
| pablo    | welcome   | System user with FTP access |
| paco     | sunshine  | System user with FTP access |

## Usage

### Starting the VM
```bash
vagrant up
```

### Accessing the VM
```bash
vagrant ssh
```

### Stopping the VM
```bash
vagrant halt
```

### Destroying the VM
```bash
vagrant destroy
```

### Reloading after changes
```bash
vagrant reload --provision
```

## Testing Services

### Test MySQL
```bash
mysql -h 192.168.56.10 -u admin -padmin -e "SHOW DATABASES;"
```

### Test FTP
```bash
ftp 192.168.56.10
# Try anonymous and user logins
```

### Test Web Server
```bash
curl http://192.168.56.10
# Or open in browser: http://192.168.56.10
```

## Website Features

The homepage displays:
- **Welcome banner** with "Liverpul Corp" title
- **Statistics cards** showing counts of products, customers, and orders
- **Interactive tables** displaying all database records using Angular Material
- **Responsive design** with gradient background

## Security Notes

⚠️ **This VM is intentionally vulnerable for educational purposes:**
- Weak dictionary passwords
- Anonymous FTP access enabled
- Database accessible from network
- Simple admin credentials
- No SSL/TLS encryption

**Do not deploy this in production or on untrusted networks!**

## Troubleshooting

### Services not starting
```bash
vagrant ssh
sudo rc-service mariadb status
sudo rc-service vsftpd status
sudo rc-service nginx status
sudo rc-service php-fpm81 status
```

### Restart services
```bash
vagrant ssh
sudo rc-service mariadb restart
sudo rc-service vsftpd restart
sudo rc-service nginx restart
sudo rc-service php-fpm81 restart
```

### View logs
```bash
vagrant ssh
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/vsftpd.log
```

## Network Configuration

The VM is configured on a private network (192.168.56.10) which means:
- Accessible from your host machine
- Accessible from other VMs on the same private network
- Not directly accessible from the internet
- All services are exposed on standard ports

## Lab Exercises

This VM can be used for:
1. **Password cracking**: Users have dictionary passwords
2. **FTP enumeration**: Anonymous access and user enumeration
3. **Database exploitation**: Test SQL injection (website uses direct queries)
4. **Service fingerprinting**: Identify service versions
5. **Network scanning**: Practice with nmap and other tools
6. **Web application testing**: Angular Material interface
7. **Brute force attacks**: Test against FTP and MySQL

## License

For educational purposes only.

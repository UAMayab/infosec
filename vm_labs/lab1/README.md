Web Exploitation Lab - Successfully Created!

  VM Details:
  - Hostname: web-exploitation-lab
  - IP Address: 192.168.1.73
  - OS: Alpine Linux 3.18

  Services Running:

  1. Apache Web Server 2.4.49 (CVE-2021-41773)
    - Accessible at: http://192.168.1.73
    - Flag accessible at: http://192.168.1.73/flag.txt
    - Compiled from source with CGI support enabled
  2. FTP Service (vsftpd)
    - Accessible at: ftp://192.168.1.73
    - Anonymous login enabled (username: anonymous, no password)
    - Shares the web root directory

  Test Results:

  ✓ Web Server: Successfully serving pages
  ✓ Flag File: 5f4dcc3b5aa765d61d8327deb882cf99
  ✓ FTP Access: Anonymous login working, can list files

  Files Created:
  - Vagrantfile: /home/mguirao/code/infosec/vm_labs/Vagrantfile
    - Includes your original prompt as a comment
    - Creation date: 2025-12-30
    - Author: mguirao
    - Updated with corrected Apache configuration

  Security Note:
  This VM is intentionally vulnerable (Apache 2.4.49 has CVE-2021-41773 path traversal vulnerability) 
and should only be used in isolated lab environments for educational purposes.


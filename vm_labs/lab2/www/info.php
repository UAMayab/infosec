<?php
/**
 * Energía Marina - System Information
 *
 * VULNERABILITY: Security Misconfiguration (OWASP A05:2021)
 * phpinfo() exposed publicly - reveals sensitive system information
 */

// FLAG for Security Misconfiguration
echo "<!-- FLAG_CONFIG: EM{m1sc0nf1gur4t10n_gul0_mex1c0} -->\n";
echo "<!-- This phpinfo() page should NEVER be accessible in production! -->\n";
echo "<!-- Exposing system information aids attackers in reconnaissance -->\n\n";

// Display full PHP configuration
phpinfo();
?>

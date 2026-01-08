# Universidad Anahuac Mayab
## Ingeniería en Tecnologías de la Información y Negocios Digitales
### Midterm Hands-On Project
### Due date: Sep 16, 2025
### Delivery Method: Github and Brightspace
### Deliverable: Video


## GOAL: Attack & Analyze - Penetration Testing and CIA Triad Impact Assessment

**Virtual Environment:** Ubuntu or Kali Linux (Attacker), Metasploitable3 (Target)  
**Tools:** Nmap, Nessus Essentials, Metasploit Framework

### Tools:
- [Network Mapper](https://nmap.org/)
- [Metasploitable3](https://github.com/rapid7/metasploitable3)
- [Metasploit](https://github.com/rapid7/metasploit-framework)
- [Nessus Essentials for Education](https://www.tenable.com/tenable-for-education/nessus-essentials?edu=true)

---

## Scenario

You are a penetration tester contracted by **AcmeTech Solutions**, a fictitious mid-sized IT company. Your mission is to:
1. Identify active hosts and services.
2. Assess the vulnerabilities present.
3. Exploit at least three vulnerabilities.
4. Analyze the impact of these vulnerabilities on the business, focusing on the **CIA Triad**.

---

## Objectives

1. Perform host and service discovery.
2. Conduct a vulnerability assessment.
3. Exploit vulnerabilities.
4. Evaluate the impact of vulnerabilities on the CIA Triad.

NOTE: For bullet point 4, you need to evaluate the impact that each vulnerability exploited will have for the organization, framed by the CIA Triad.

---

## Part 1: Host and Service Discovery (Nmap)

### Tasks
- Discover the IP address of the target VM.

### Deliverables
- Nmap output 
- List of discovered services and ports

---

## Part 2: Vulnerability Assessment

### Tasks
- Launch a Nessus scan against the target.
- Identify high and critical vulnerabilities.
- Record:
  - CVE ID
  - Vulnerable service
  - Description
  - Severity (CVSS score) **IMPORTANT**

### Deliverables
- PDF or HTML report from Nessus (show this on your video and explain)
- Table listing 3 selected key vulnerabilities (explain in depth this 3 vulnerabilities) **IMPORTANT**

---

## Part 3: Exploitation with Metasploit

### Tasks
- Search and configure appropriate Metasploit modules:
  - Set options, run exploits
- Capture results (flags, shells, sessions, show and explain these in your video) **IMPORTANT**

### Deliverables
- Screenshot or text output of successful exploits
- Summary of each exploit used

---

## Part 4: CIA Triad & Business Impact Analysis

For this section you will create some slides that you need to show and explain in your video.

### Tasks
For each exploited vulnerability:
- Identify which CIA component is affected
- Explain how it impacts AcmeTech’s business
- Suggest at least one mitigation

---

## Submission Checklist

- [ ] Nmap scan output and screenshots
- [ ] Nessus report and vulnerability table
- [ ] Metasploit exploit evidence
- [ ] CIA Triad impact report

---

## Ethics Reminder

All activities must be conducted within your controlled lab environment. Unauthorized scanning or exploitation of systems is strictly prohibited.

---

**Good luck, and think like an attacker—defend like a pro!**

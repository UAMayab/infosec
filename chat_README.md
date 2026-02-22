# Message Server

This chat server is available to students to submit their answers to real-time quizes happening during the class.

** Remember! **
Every student start the quiz with a grade of 6.
- First correct answer gets 0.5 additional points.
- Second correct answer gets 0.3 additional points.
- All remaining correct answers get 0.2 additional points.

### Send a Message
```bash
echo "<student_id>\$Hello World" | nc <server_ip_address> 9999
```

### Connect Interactively
```bash
nc <server_ip_address> 9999
```
Then type: `<student_id>$Your message here`

## Message Format
```
<Team ID>$<Message>
```

**Examples:**
- `TeamA$Hello from Team A`
- `Team1$Mission accomplished`
- `RedTeam$Status update received`


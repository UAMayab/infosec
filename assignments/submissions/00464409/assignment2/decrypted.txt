import random
import datetime

# generate a random number between 1 and 100
random_number = random.randint(1, 100)
message = "welcome to computer security class 101!"
current_time = datetime.datetime.now().strftime("%H:%M")
# get the current date with the format month, day, year
current_date = datetime.datetime.now().strftime("%B %d, %Y")
date_and_time = f"{current_date} at {current_time}"
message_len = len(message)

print("*" * message_len)
print(f"{message}".title())
print(date_and_time.center(message_len, " "))
print("*" * message_len)
print(f"Your lucky number is: {random_number}")
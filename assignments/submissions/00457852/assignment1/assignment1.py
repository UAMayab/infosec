# Caesar's cipher

import string

def cipher(text):

    result = ""

    for i in range(len(text)):
        char = text[i]

        if char.isupper():
            result += chr((ord(char) - 65 + 3) % 26 + 65)
        elif char.islower():
            result += chr((ord(char) - 97 + 3) % 26 + 97)
        else:
            result += char

    return result


def decoded(text):

    result = ''

    for i in range(len(text)):
        char = text[i]

        if char.isupper():
            result += chr((ord(char) - 65 -3 ) % 26 + 65)
        elif char.islower():
            result += chr((ord(char) -97 - 3) %26 + 97)
        else:
            result += char

    return result

# The code must be run inside the infosec folder

with open("./assignments/data/mini_treasureisland.txt", "r", encoding="utf-8") as data:
    text = data.read()

with open("./assignments/submissions/00457852/assignment1/encoded_text.txt", "w", encoding="utf-8") as encode:
    cypher_text = cipher(text)
    encode.write(cypher_text)

with open("./assignments/submissions/00457852/assignment1/encoded_text.txt", "r", encoding="utf-8") as encode:
    encoded_text = encode.read()

    with open("./assignments/submissions/00457852/assignment1/decoded_text.txt", "w", encoding="utf-8") as decode:
        decoded_text = decoded(encoded_text)
        decode.write(decoded_text)


def init_letter_count():
    return {letter: 0 for letter in string.ascii_uppercase + string.ascii_lowercase}

def count_letters(text):
    count = init_letter_count()
    for char in text:
        if char in count:
            count[char] += 1
    return count

encoded_alphabet = count_letters(encoded_text)
decoded_alphabet = count_letters(decoded_text)

print("Frecuency cypher text:")
print(encoded_alphabet)
print("\nFrecuency decoded text:")
print(decoded_alphabet)


def estimate_shift(encoded_freq, decoded_freq):
    from collections import Counter

    filtered_encoded = {k: v for k, v in encoded_freq.items() if k.islower()}
    filtered_decoded = {k: v for k, v in decoded_freq.items() if k.islower()}

    most_common_encoded = max(filtered_encoded, key=filtered_encoded.get)
    most_common_decoded = max(filtered_decoded, key=filtered_decoded.get)

    shift = (ord(most_common_encoded) - ord(most_common_decoded)) % 26

    return most_common_encoded, most_common_decoded, shift

enc_letter, dec_letter, estimated_shift = estimate_shift(encoded_alphabet, decoded_alphabet)

print(f"\nLetra más común en texto codificado: {enc_letter}")
print(f"Letra más común en texto decodificado: {dec_letter}")
print(f"Desplazamiento estimado del cifrado: {estimated_shift}")

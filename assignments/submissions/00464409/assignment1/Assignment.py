import os
import string
import collections
import matplotlib.pyplot as plt

ALPHABET = string.ascii_uppercase

# ------------------ Caesar Cipher ------------------
def encrypt_caesar(text, shift):
    result = []
    for ch in text.upper():
        if ch in ALPHABET:
            idx = (ALPHABET.index(ch) + shift) % len(ALPHABET)
            result.append(ALPHABET[idx])
        else:
            result.append(ch)
    return ''.join(result)

def decrypt_caesar(text, shift):
    return encrypt_caesar(text, -shift)

# ------------------ Cryptoanalysis ------------------
def brute_force(ciphertext):
    candidates = {}
    for shift in range(len(ALPHABET)):
        candidates[shift] = decrypt_caesar(ciphertext, shift)
    return candidates


def frequency_distribution(text):
    counts = collections.Counter(ch for ch in text.upper() if ch in ALPHABET)
    total = sum(counts.values()) or 1
    return {ch: counts[ch] / total for ch in ALPHABET}


def chi_squared_stat(obs_dist, exp_dist):
    chi2 = 0.0
    for letter in ALPHABET:
        observed = obs_dist.get(letter, 0)
        expected = exp_dist.get(letter, 1e-6)
        chi2 += (observed - expected) ** 2 / expected
    return chi2


def analyze_shift(ciphertext, corpus_dist):
    best_shift, best_stat = 0, float('inf')
    for shift in range(len(ALPHABET)):
        plain = decrypt_caesar(ciphertext, shift)
        freq = frequency_distribution(plain)
        stat = chi_squared_stat(freq, corpus_dist)
        if stat < best_stat:
            best_stat, best_shift = stat, shift
    return best_shift, decrypt_caesar(ciphertext, best_shift)

# ------------------ Plotting ------------------
def plot_frequency(dist, title="Letter Frequency"):
    letters = list(dist.keys())
    freqs = [dist[ch] for ch in letters]
    plt.figure()
    plt.bar(letters, freqs)
    plt.xlabel('Letter')
    plt.ylabel('Frequency')
    plt.title(title)
    plt.show()

# ------------------ File Utilities ------------------
def load_text(path):
    with open(path, 'r', encoding='utf-8') as f:
        return f.read()

# ------------------ Interactive Menu ------------------
def menu():
    print("\n--- Caesar Cipher Tool ---")
    print("1) Encriptar")
    print("2) Desencriptar")
    print("3) Fuerza bruta")
    print("4) Criptoanálisis")
    print("5) Graficar frecuencias del corpus")
    print("6) Salir")
    return input("Elige una opción (1-6): ").strip()


def save_text(path, text):
    with open(path, 'w', encoding='utf-8') as f:
        f.write(text)
    print(f"Archivo guardado en: {path}")


def run():
    while True:
        choice = menu()
        if choice == '6':
            print("Saliendo...")
            break

        if choice in {'1', '2', '3', '4', '5'}:
            path = input("Ruta al archivo de texto (.txt): ").strip()
            if choice in {'1', '2', '3', '4'} and not os.path.isfile(path):
                print("Archivo no encontrado. Intenta de nuevo.")
                continue

        if choice == '1':  # Encriptar
            shift = int(input("Desplazamiento (número): "))
            output = input("Ruta de salida para el archivo encriptado: ").strip()
            text = load_text(path)
            result = encrypt_caesar(text, shift)
            save_text(output, result)

        elif choice == '2':  # Desencriptar
            shift = int(input("Desplazamiento (número): "))
            output = input("Ruta de salida para el archivo desencriptado: ").strip()
            text = load_text(path)
            result = decrypt_caesar(text, shift)
            save_text(output, result)

        elif choice == '3':  # Fuerza bruta
            text = load_text(path)
            candidates = brute_force(text)
            for s, cand in candidates.items():
                print(f"Shift {s}: {cand[:100]}...")

        elif choice == '4':  # Criptoanálisis
            corpus_dir = input("Ruta a carpeta de corpus (.txt): ").strip()
            if not os.path.isdir(corpus_dir):
                print("Directorio de corpus no válido. Intenta de nuevo.")
                continue
            combined = ''
            for fname in os.listdir(corpus_dir):
                if fname.endswith('.txt'):
                    combined += load_text(os.path.join(corpus_dir, fname))
            corpus_dist = frequency_distribution(combined)
            text = load_text(path)
            best_shift, plain = analyze_shift(text, corpus_dist)
            print(f"\nMejor desplazamiento: {best_shift}\nTexto claro:\n{plain}")

        elif choice == '5':  # Graficar
            corpus_dir = input("Ruta a carpeta de corpus (.txt): ").strip()
            if not os.path.isdir(corpus_dir):
                print("Directorio de corpus no válido. Intenta de nuevo.")
                continue
            for fname in os.listdir(corpus_dir):
                if fname.endswith('.txt'):
                    content = load_text(os.path.join(corpus_dir, fname))
                    dist = frequency_distribution(content)
                    plot_frequency(dist, title=f"Frecuencia: {fname}")

        else:
            print("Opción no válida, intenta otra vez.")

if __name__ == '__main__':
    run()
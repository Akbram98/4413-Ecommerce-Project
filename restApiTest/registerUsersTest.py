import requests

# Define the URL for the POST request
url = "http://localhost/eecs4413/controller/authController/registerUser"

# Define user data
users = [
    {"userName": "user01", "firstName": "Alice", "lastName": "Smith", "password": "password123"},
    {"userName": "user02", "firstName": "Bob", "lastName": "Johnson", "password": "securepass"},
    {"userName": "user03", "firstName": "Charlie", "lastName": "Williams", "password": "mypassword"},
    {"userName": "user04", "firstName": "David", "lastName": "Brown", "password": "pass1234"},
    {"userName": "user05", "firstName": "Ella", "lastName": "Jones", "password": "1234pass"},
    {"userName": "user06", "firstName": "Fiona", "lastName": "Garcia", "password": "wordpass"},
    {"userName": "user07", "firstName": "George", "lastName": "Martinez", "password": "myp@ssword"},
    {"userName": "user08", "firstName": "Hannah", "lastName": "Lopez", "password": "pass@123"},
    {"userName": "user09", "firstName": "Ian", "lastName": "Clark", "password": "word1234"},
    {"userName": "user10", "firstName": "Jack", "lastName": "Rodriguez", "password": "secure123"},
    {"userName": "user11", "firstName": "Karen", "lastName": "Lewis", "password": "mysecurepass"},
    {"userName": "user12", "firstName": "Liam", "lastName": "Walker", "password": "strongpass"},
    {"userName": "user13", "firstName": "Mia", "lastName": "Hall", "password": "mypassword123"},
    {"userName": "user14", "firstName": "Nathan", "lastName": "Allen", "password": "myp4ssword"},
    {"userName": "user15", "firstName": "Olivia", "lastName": "Young", "password": "passme123"},
    {"userName": "user16", "firstName": "Paul", "lastName": "King", "password": "password@2024"},
    {"userName": "user17", "firstName": "Quinn", "lastName": "Scott", "password": "letmein123"},
    {"userName": "user18", "firstName": "Rachel", "lastName": "Green", "password": "openup123"},
    {"userName": "user19", "firstName": "Sam", "lastName": "Adams", "password": "unlockme123"},
    {"userName": "user20", "firstName": "Tina", "lastName": "Morris", "password": "123secure"},
]

# Register users
for user in users:
    # Send POST request with form-data
    response = requests.post(url, data=user)
    
    # Print response for debugging
    print(f"Registering user: {user['userName']} -> Status: {response.status_code}")
    print(response.text)

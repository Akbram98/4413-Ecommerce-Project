import requests
import json

# Define the URL for the PUT request
url = "http://localhost/eecs4413/controller/authController/updateUser"

# Define the user profiles with the appropriate fields
user_updates = [
    {"userName": "user01", "firstName": "Alice", "lastName": "Smith", "age": 25},
    {"userName": "user02", "street": "123 Maple Street", "city": "Toronto", "province": "Ontario", "postal": "M1K 4L5"},
    {"userName": "user03", "street": "456 Oak Rd", "province": "Ontario", "postal": "L5N 2T3", "city": "Hamilton"},
    {"userName": "user04", "province": "Ontario", "age": 30},  # Missing required fields: street, city, postal
    {"userName": "user05", "street": "789 Pine Ave", "city": "Toronto", "province": "Ontario", "postal": "M5T 1R6"},
    {"userName": "user06", "card_num": 1234567812345678, "cvv": 123, "expiry": "12/25"},
    {"userName": "user07", "expiry": "12/25", "cvv": 321, "card_num": 9876543212345678},
    {"userName": "user08", "age": 22, "postal": "L6W 3T7"},
    {"userName": "user09", "street": "456 Queen St", "province": "Ontario", "postal": "L7P 2N6"},
    {"userName": "user10", "cvv": 987, "card_num": 1234567890123456, "expiry": "01/28"},
    {"userName": "user11", "city": "Ottawa", "postal": "K1A 0B1", "province": "Ontario"},
    {"userName": "user12", "street": "123 Elm St", "city": "Vancouver", "province": "British Columbia", "postal": "V6B 1A1"},
    {"userName": "user13", "province": "Quebec", "city": "Montreal", "postal": "H2X 1Z5"},
    {"userName": "user14", "card_num": 9876543210987654, "expiry": "11/25", "cvv": 456},
    {"userName": "user15", "age": 29, "expiry": "05/26", "card_num": 4567890123456789, "cvv": 654},
    {"userName": "user16", "city": "Toronto", "province": "Ontario", "postal": "M5A 1A1"},
    {"userName": "user17", "postal": "L5N 2T3", "street": "789 Birch Rd", "province": "Ontario", "cvv": 321},
    {"userName": "user18", "province": "Manitoba", "postal": "R3C 0A1", "expiry": "11/25", "card_num": 9998887776665555, "cvv": 789},
    {"userName": "user19", "street": "789 Birch Rd", "cvv": 654, "card_num": 1122334455667788, "expiry": "09/25"},
    {"userName": "user20", "age": 31, "expiry": "01/28", "card_num": 4567890123456789, "cvv": 123, "postal": "K1C 3Y3"}
]

# Update user profiles
for update in user_updates:
    try:
        # Send PUT request with JSON payload
        response = requests.put(url, json=update)

        # Print the status of the update
        if response.status_code == 200:
            print(f"Update successful for user: {update['userName']}")
        else:
            print(f"Failed to update user: {update['userName']} - {response.status_code}: {response.text}")
    except Exception as e:
        print(f"Error updating user: {update['userName']} - {str(e)}")

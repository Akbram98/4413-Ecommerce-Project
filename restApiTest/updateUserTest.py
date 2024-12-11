import requests
import json

# Define the URL for the PUT request
url = "http://localhost/eecs4413/controller/authController/updateUser"

# Define the user profiles with the appropriate fields
user_updates = [
    {"userName": "user01", "firstName": "Alice", "lastName": "Smith", "age": 25, "street": "123 Oak St", "city": "Toronto", "province": "Ontario", "postal": "M5A 1A1", "card_num": "1234 5678 1234 5678", "cvv": 123, "expiry": "12/25"},
    {"userName": "user02", "firstName": "Bob", "lastName": "Johnson", "age": 28, "street": "123 Maple Street", "city": "Toronto", "province": "Ontario", "postal": "M1K 4L5", "card_num": "2345 6789 2345 6789", "cvv": 234, "expiry": "11/24"},
    {"userName": "user03", "firstName": "Charlie", "lastName": "Brown", "age": 32, "street": "456 Oak Rd", "city": "Hamilton", "province": "Ontario", "postal": "L5N 2T3", "card_num": "3456 7890 3456 7890", "cvv": 345, "expiry": "10/23"},
    {"userName": "user04", "firstName": "Dana", "lastName": "Green", "age": 30, "street": "789 Birch Lane", "city": "Ottawa", "province": "Ontario", "postal": "K1A 0A9", "card_num": "4567 8901 4567 8901", "cvv": 456, "expiry": "09/22"},
    {"userName": "user05", "firstName": "Eva", "lastName": "Williams", "age": 22, "street": "789 Pine Ave", "city": "Toronto", "province": "Ontario", "postal": "M5T 1R6", "card_num": "5678 9012 5678 9012", "cvv": 567, "expiry": "08/21"},
    {"userName": "user06", "firstName": "Grace", "lastName": "Miller", "age": 27, "street": "123 Maple Ave", "city": "Mississauga", "province": "Ontario", "postal": "L5B 1A1", "card_num": "6789 0123 6789 0123", "cvv": 678, "expiry": "07/20"},
    {"userName": "user07", "firstName": "Hannah", "lastName": "Taylor", "age": 29, "street": "456 Birch St", "city": "Brampton", "province": "Ontario", "postal": "L6S 3X2", "card_num": "7890 1234 7890 1234", "cvv": 789, "expiry": "06/19"},
    {"userName": "user08", "firstName": "Ivy", "lastName": "Davis", "age": 22, "street": "101 Pine Rd", "city": "Mississauga", "province": "Ontario", "postal": "L6W 3T7", "card_num": "8901 2345 8901 2345", "cvv": 890, "expiry": "05/18"},
    {"userName": "user09", "firstName": "Jack", "lastName": "Martinez", "age": 35, "street": "456 Queen St", "city": "Burlington", "province": "Ontario", "postal": "L7P 2N6", "card_num": "9012 3456 9012 3456", "cvv": 901, "expiry": "04/17"},
    {"userName": "user10", "firstName": "Kara", "lastName": "Wilson", "age": 24, "street": "123 King St", "city": "Ottawa", "province": "Ontario", "postal": "K1C 3Y3", "card_num": "1234 5678 9012 3456", "cvv": 987, "expiry": "01/28"},
    {"userName": "user11", "firstName": "Leo", "lastName": "Moore", "age": 33, "street": "22 Maple Rd", "city": "Ottawa", "province": "Ontario", "postal": "K1A 0B1", "card_num": "2345 6789 0123 4567", "cvv": 234, "expiry": "12/27"},
    {"userName": "user12", "firstName": "Mia", "lastName": "Scott", "age": 26, "street": "123 Elm St", "city": "Vancouver", "province": "British Columbia", "postal": "V6B 1A1", "card_num": "3456 7890 1234 5678", "cvv": 345, "expiry": "11/26"},
    {"userName": "user13", "firstName": "Nina", "lastName": "Clark", "age": 23, "street": "567 Maple Ave", "city": "Montreal", "province": "Quebec", "postal": "H2X 1Z5", "card_num": "4567 8901 2345 6789", "cvv": 456, "expiry": "10/25"},
    {"userName": "user14", "firstName": "Olivia", "lastName": "Lee", "age": 27, "street": "789 Elm Rd", "city": "Toronto", "province": "Ontario", "postal": "M5G 1Z8", "card_num": "5678 9012 3456 7890", "cvv": 567, "expiry": "09/24"},
    {"userName": "user15", "firstName": "Paul", "lastName": "Harris", "age": 29, "street": "123 Birch Rd", "city": "Vancouver", "province": "British Columbia", "postal": "V6K 2N5", "card_num": "6789 0123 4567 8901", "cvv": 678, "expiry": "08/23"},
    {"userName": "user16", "firstName": "Quinn", "lastName": "Young", "age": 31, "street": "234 King St", "city": "Toronto", "province": "Ontario", "postal": "M5A 1A1", "card_num": "7890 1234 5678 9012", "cvv": 789, "expiry": "07/22"},
    {"userName": "user17", "firstName": "Riley", "lastName": "King", "age": 28, "street": "789 Birch Rd", "city": "Brampton", "province": "Ontario", "postal": "L5N 2T3", "card_num": "8901 2345 6789 0123", "cvv": 890, "expiry": "06/21"},
    {"userName": "user18", "firstName": "Samantha", "lastName": "Adams", "age": 33, "street": "101 Birch St", "city": "Winnipeg", "province": "Manitoba", "postal": "R3C 0A1", "card_num": "9012 3456 7890 1234", "cvv": 901, "expiry": "05/20"},
    {"userName": "user19", "firstName": "Taylor", "lastName": "Evans", "age": 26, "street": "789 Birch Rd", "city": "Calgary", "province": "Alberta", "postal": "T2P 3P9", "card_num": "1234 5678 9012 3456", "cvv": 654, "expiry": "04/19"},
    {"userName": "user20", "firstName": "Uma", "lastName": "Brown", "age": 31, "street": "123 Oak Rd", "city": "Ottawa", "province": "Ontario", "postal": "K1C 3Y3", "card_num": "2345 6789 0123 4567", "cvv": 123, "expiry": "03/18"}
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

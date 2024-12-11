import requests
import random

# API Endpoints
get_items_url = "http://localhost/eecs4413/controller/authController/getItems"
get_customers_url = "http://localhost/eecs4413/controller/authController/getCustomers?userName=akbram"
add_transaction_url = "http://localhost/eecs4413/controller/authController/addUserTransactions"

# Fetch items from the API
def fetch_items():
    try:
        response = requests.get(get_items_url)
        if response.status_code == 200:
            data = response.json()
            return data.get("items", [])
        else:
            print(f"Failed to fetch items: {response.status_code}")
            return []
    except Exception as e:
        print(f"Error fetching items: {e}")
        return []

# Fetch customers from the API
def fetch_customers():
    try:
        response = requests.get(get_customers_url)
        if response.status_code == 200:
            data = response.json()
            return data.get("users", [])
        else:
            print(f"Failed to fetch customers: {response.status_code}")
            return []
    except Exception as e:
        print(f"Error fetching customers: {e}")
        return []

# Add user transaction
def add_user_transaction(user, items):
    full_name = f"{user['profile']['firstName']} {user['profile']['lastName']}"
    card_num = user['profile'].get('cardNum', "1111222233334444")
    cvv = user['profile'].get('cvv', "123")
    expiry = user['profile'].get('expiry', "12/25")

    # Randomize the number of items a user will buy
    num_items = random.randint(1, min(5, len(items)))
    purchased_items = []
    used_item_ids = set()  # Track used item IDs for the current user

    for _ in range(num_items):
        # Find an item that hasn't been purchased yet by this user
        available_items = [item for item in items if item["itemId"] not in used_item_ids]
        if not available_items:
            break  # No more unique items available for this user
        
        item = random.choice(available_items)
        max_quantity = int(item["quantity"])
        quantity = random.randint(1, max_quantity // 10)  # Ensure we don't exhaust inventory
        purchased_items.append({"itemid": int(item["itemId"]), "quantity": quantity})

        # Mark the item ID as used for this user
        used_item_ids.add(item["itemId"])

    payload = {
        "items": purchased_items,
        "payment": {
            "fullName": full_name,
            "card_num": card_num,
            "cvv": cvv,
            "expiry": expiry,
            "userName": user["userName"]
        }
    }

    try:
        response = requests.post(add_transaction_url, json=payload)
        if response.status_code == 200:
            print(f"Transaction successful for user {user['userName']}.")
        else:
            print(f"Failed to add transaction for user {user['userName']}: {response.status_code} - {response.text}")
    except Exception as e:
        print(f"Error adding transaction for user {user['userName']}: {e}")

# Main function to process transactions
def main():
    items = fetch_items()
    customers = fetch_customers()

    if items and customers:
        for customer in customers:
            # Skip customers with userName "akbram" or "mikdol"
            if customer["userName"] in ["akbram", "mikdol"]:
                continue  # Skip this iteration and move to the next customer

            # Proceed with adding transaction for other users
            add_user_transaction(customer, items)
if __name__ == "__main__":
    main()

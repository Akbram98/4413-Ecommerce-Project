import requests
import random

# API Endpoints
get_items_url = "http://localhost/eecs4413/controller/authController/getItems"
update_item_url = "http://localhost/eecs4413/controller/authController/adminUpdateItem"

# Administrator username
admin_username = "akbram"

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

# Update item quantities
def update_item_quantity(itemid, quantity):
    payload = {
        "userName": admin_username,
        "itemid": itemid,
        "quantity": quantity
    }
    try:
        response = requests.put(update_item_url, json=payload)
        if response.status_code == 200:
            print(f"Successfully updated item {itemid} to quantity {quantity}.")
        else:
            print(f"Failed to update item {itemid}: {response.status_code} - {response.text}")
    except Exception as e:
        print(f"Error updating item {itemid}: {e}")

# Main function to fetch and update items
def main():
    items = fetch_items()
    if items:
        for item in items:
            itemid = item.get("itemId")
            if itemid:
                # Generate a random quantity between 1000 and 2000
                new_quantity = random.randint(1000, 2000)
                update_item_quantity(itemid, new_quantity)

if __name__ == "__main__":
    main()

import requests

# API endpoint
url = "http://localhost/eecs4413/controller/authController/adminAddItem"

# List of 20 items with common popular products
items = [
    {"name": "Laptop", "price": 999.99, "description": "A high-performance laptop.", "brand": "Dell", "quantity": 10, "image": "laptop.jpeg"},
    {"name": "Smartphone", "price": 799.99, "description": "A flagship smartphone.", "brand": "Samsung", "quantity": 15, "image": "smartphone.jpeg"},
    {"name": "Headphones", "price": 199.99, "description": "Noise-cancelling headphones.", "brand": "Sony", "quantity": 20, "image": "headphones.jpeg"},
    {"name": "Tablet", "price": 499.99, "description": "A powerful tablet for work and play.", "brand": "Apple", "quantity": 8, "image": "tablet.jpeg"},
    {"name": "Smartwatch", "price": 249.99, "description": "A stylish smartwatch.", "brand": "Garmin", "quantity": 12, "image": "smartwatch.jpeg"},
    {"name": "Keyboard", "price": 99.99, "description": "A mechanical keyboard.", "brand": "Logitech", "quantity": 25, "image": "keyboard.jpeg"},
    {"name": "Mouse", "price": 49.99, "description": "A precision gaming mouse.", "brand": "Razer", "quantity": 30, "image": "mouse.jpeg"},
    {"name": "Monitor", "price": 299.99, "description": "A 4K UHD monitor.", "brand": "LG", "quantity": 5, "image": "monitor.jpeg"},
    {"name": "Speaker", "price": 149.99, "description": "A portable Bluetooth speaker.", "brand": "Bose", "quantity": 18, "image": "speaker.jpeg"},
    {"name": "Charger", "price": 29.99, "description": "A fast USB-C charger.", "brand": "Anker", "quantity": 40, "image": "charger.jpeg"},
    {"name": "Camera", "price": 699.99, "description": "A DSLR camera with 4K recording.", "brand": "Canon", "quantity": 7, "image": "camera.jpeg"},
    {"name": "Gaming Console", "price": 499.99, "description": "Next-gen gaming console.", "brand": "Sony", "quantity": 10, "image": "console.jpeg"},
    {"name": "External HDD", "price": 129.99, "description": "2TB external hard drive.", "brand": "Seagate", "quantity": 15, "image": "hdd.jpeg"},
    {"name": "Flash Drive", "price": 19.99, "description": "64GB USB flash drive.", "brand": "SanDisk", "quantity": 50, "image": "flashdrive.jpeg"},
    {"name": "Router", "price": 89.99, "description": "Wi-Fi 6 router with fast speeds.", "brand": "Netgear", "quantity": 20, "image": "router.jpeg"},
    {"name": "E-Reader", "price": 139.99, "description": "E-reader with adjustable backlight.", "brand": "Kindle", "quantity": 10, "image": "ereader.jpeg"},
    {"name": "Webcam", "price": 79.99, "description": "1080p HD webcam for streaming.", "brand": "Logitech", "quantity": 25, "image": "webcam.jpeg"},
    {"name": "Microphone", "price": 129.99, "description": "Professional condenser microphone.", "brand": "Blue", "quantity": 12, "image": "microphone.jpeg"},
    {"name": "Power Bank", "price": 49.99, "description": "10000mAh portable charger.", "brand": "Xiaomi", "quantity": 30, "image": "powerbank.jpeg"},
    {"name": "Smart Light", "price": 24.99, "description": "Wi-Fi-enabled smart bulb.", "brand": "Philips", "quantity": 35, "image": "smartlight.jpeg"}
]

# Loop through each item and send a POST request
for item in items:
    data = {
        "userName": "akbram",
        "name": item["name"],
        "price": item["price"],
        "description": item["description"],
        "brand": item["brand"],
        "quantity": item["quantity"],
        "image": item["image"]
    }
    try:
        # Sending form-data
        response = requests.post(url, data=data)
        if response.status_code == 200:
            print(f"Successfully added item: {item['name']}")
        else:
            print(f"Failed to add item: {item['name']} - Status Code: {response.status_code}, Response: {response.text}")
    except Exception as e:
        print(f"Error while adding item: {item['name']} - {e}")

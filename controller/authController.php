<?php
// Include necessary files (Database, DAO classes, etc.)
include_once '../dao/Database.php';
include_once '../dao/userDAO.php';
include_once '../dao/adminDAO.php';
include_once '../dao/itemDAO.php';
include_once '../dao/userDAOImpl.php';
include_once '../dao/adminDAOImpl.php';
include_once '../dao/itemDAOImpl.php';


class AuthController {
    private $pdo;
    private $routes = []; // Simulate a route mapping
    private AdminDAO $adminDAO;
    private UserDAO $userDAO;
    private ItemDAO $itemDAO;


    // Constructor to initialize the PDO connection
    public function __construct() {
        $database = new Database();

        $this->pdo = $database->getConnection();
        $this->itemDAO = new ItemDAOImpl($this->pdo);
        $this->userDAO = new UserDAOImpl($this->pdo);
        $this->adminDAO = new AdminDAOImpl($this->pdo);
        
        $this->defineRoutes();
        $this->handleRequest();


    }

    // Define your routes
    private function defineRoutes()
    {
        // Map HTTP methods and routes to class methods
        $this->routes = [
            'GET' => [
                '/getItems' => 'getItems',
                '/getCustomers' => 'adminGetCustomers',
                '/getUserTransactions' => 'getUserTransactions',
                '/adminGetTransactions' => 'adminGetAllTransactions',
            ],
            'PUT' => [
                '/signinUser' => 'signinUser',
                '/adminUpdateItem' => 'adminUpdateItem',
                '/updateUser' => 'updateUserProfile',
                '/adminUpdateUser' => 'adminUpdateUserProfile',
                '/updateItemInventory' => 'updateItemInventory',
            ],
            'POST' => [
                '/registerUser' => 'registerUser',
                '/adminAddItem' => 'adminAddItem',
                '/addUserTransactions' => 'addUserTransactions',
            ],
            'DELETE' => [
                '/deleteItem' => 'adminDeleteItem',
                '/deleteCustomer' => 'adminDeleteCustomer',
            ],
        ];
    }

    // Handle the incoming request
    private function handleRequest()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
       // Retrieve the URL path
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


        // Remove the base part of the URL
        $lastSlashPosition = strrpos($requestUri, '/');

        $path = substr($requestUri, $lastSlashPosition);

        // Check if the route exists
        if (isset($this->routes[$requestMethod][$path])) {
            $methodName = $this->routes[$requestMethod][$path];
            if (method_exists($this, $methodName)) {
                call_user_func([$this, $methodName]);
            } else {
                $this->sendResponse(500, "Method '$methodName' not implemented!");
            }
        } else {
            $this->sendResponse(404, "Route not found!");
        }
    }

    /**
     * Handles user signin (POST request).
     * Verifies the user credentials and sends a success message if valid.
     */
    public function signinUser() {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {

            // Read the raw input stream
            $input = file_get_contents("php://input");

            // Decode the JSON payload
            $data = json_decode($input, true);

            $userName = $data['userName'] ?? null;
            $password = $data['password'] ?? null;

            if ($userName && $password) {
                if($this->adminDAO->isAdmin($userName)){
                    if($this->adminDAO->updateLastLogon($userName))
                        echo json_encode(["status" => "success", "message" => "admin"]);
                    else
                        echo json_encode(["status" => "error", "message" => "admin login but last logon failed to update."]);
                    return;
                }

                
                // Register the user and profile
                $isValidUser = $this->userDAO->validateUser($userName, $password);
                header('Content-Type: application/json'); // set the content type
                if ($isValidUser) {
                    
                    if($this->userDAO->updateLastLogon($userName)){
                        http_response_code(200); // set the http response code
                        echo json_encode(["status" => "success", "message" => "user"]);
                    }
                    else{
                        http_response_code(500);
                        echo json_encode(["status" => "error", "message" => "User login but last logon failed to update."]);
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(["status" => "error", "message" => "User validation not successful"]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "All fields are required (firstName, lastName)"]);
            }
        } else {
            http_response_code(405);
            echo json_encode(["status" => "error", "message" => "Invalid request method. Use POST."]);
        }
    }

    /**
     * Handles user registration (POST request).
     * Registers a new user and their profile.
     */
    public function registerUser() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $firstName = $_POST['firstName'] ?? null;
            $lastName = $_POST['lastName'] ?? null;
            $userName = $_POST['userName'] ?? null;
            $password = $_POST['password'] ?? null;

            if ($firstName && $lastName && $userName && $password) {
                // Create UserDAO instance

                // Register the user and profile
                $isRegistered = $this->userDAO->registerUser($userName, $password, $firstName, $lastName);

                header('Content-Type: application/json');
                if ($isRegistered) {
                    http_response_code(200);
                    echo json_encode(["status" => "success", "message" => "User registered successfully."]);
                } else {
                    http_response_code(403);
                    echo json_encode(["status" => "error", "message" => "Username already exists."]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "All fields are required (firstName, lastName, userName, password)."]);
            }
        } else {
            http_response_code(405);
            echo json_encode(["status" => "error", "message" => "Invalid request method. Use POST."]);
        }
    }

     /**
     * Handles a POST request register profile fields IF defined at registration by customer 
     * In the body of the POST request should have these possible data:
     *  username, street, city, province, postal, card_num, cvv, and expiry
     */
    // TODO: this is a POST request to add additional fields to user profiles
    // assigned-To: Hiraku
    /*
    public function registerProfileFields() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $userName = $_POST['userName'] ?? null;
            $street = $_POST['street'] ?? null;
            $city = $_POST['city'] ?? null;
            $province = $_POST['province'] ?? null;
            $postal = $_POST['postal'] ?? null;
            $card = $_POST['card_num'] ?? null;
            $cvv = $_POST['cvv'] ?? null;
            $expiry = $_POST['expiry'] ?? null;

            if ($userName && $street && $city && $province && $postal && $card && $cvv && $expiry){
                
            }
            echo json_encode(["status" => "success", "message" => "admin add item test successful"]);
        }
    }*/

    

    /**
     * Handles a POST request from Administrator to add an item.
     * the body must have these fields: name, price, description, brand, quantity, image
     * You will use the addItem() function in AdminDAO
     */
    // TODO: this is a POST request to add additional fields to user profiles
    //assignedTo: Rasengan
    public function adminAddItem() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            
            $item = null;

            //getting fields from request body 

            $itemid = $_POST['item_id'] ?? null;
            $name = $_POST['name'] ?? null;
            $price = $_POST['price'] ?? null;
            $description = $_POST['description'] ?? null;
            $brand = $_POST['brand'] ?? null;
            $date = $_POST['date'] ?? null;
            $quantity = $_POST['quantity'] ?? null;
            $image = $_POST['image'] ?? null;
            
            
            //checking if any fields are null
            if($itemid && $name && $price && $description && $brand && $date && $quantity && $image){

                //item object creation

                $item = new Item(
                    $itemid, 
                    $name,
                    $price,
                    $description,
                    $brand,
                    $date,
                    $quantity,
                    $image);
                 
                    //Adding the item to the database
                  $addedItem =  $this->adminDAO->addItem($item);
                    header('Content-Type: application/json');
                    
                    //If success http response code is 200 else response code is 403
                  if($addedItem){
                    http_response_code(200);
                    echo json_encode(["status" => "success", "message" => "admin add item test successful"]);
                  }else{
                    http_response_code(403);
                    echo json_encode(["status" => "error", "message" => "item already exists"]); 
                  }
                  //if any of the fields are null then http response code 403 
            }else{
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "all fields are required (itemid, name, price, description, brand, date, quantity, image)"]);
            }

            

            //if incorrect request method then http response code is 405
        }else{
            http_response_code(405);
            echo json_encode(["status" => "error", "message" => "Invalid request method. Use POST"]);
        }
    }

    /**
     * Handles PUT requests to update an item in the Inventory table.
     *
     * This REST endpoint receives data via a PUT request, processes the fields
     * needed to create an `Item` object, and calls the `updateItemFields` method
     * of the DAO to update the corresponding database entry.
     *
     * The response is returned in JSON format with a status message and appropriate
     * HTTP status codes indicating the success or failure of the operation.
     *
     * Example Response:
     * {
     *   "status": "success",
     *   "message": "Item updated successfully"
     * }
     *
     * @return void Outputs a JSON-encoded response.
     */
    public function adminUpdateItem() {
        // Set response header
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            // Retrieve and decode the input data
            parse_str(file_get_contents("php://input"), $inputData);

            // Validate required fields
            if (!isset($inputData['itemId']) || 
                !isset($inputData['name']) || 
                !isset($inputData['price']) || 
                !isset($inputData['description']) || 
                !isset($inputData['brand']) || 
                !isset($inputData['date']) || 
                !isset($inputData['quantity']) || 
                !isset($inputData['image'])) {
                
                http_response_code(400); // Bad Request
                echo json_encode([
                    "status" => "error",
                    "message" => "Missing required fields"
                ]);
                return;
            }

            try {
                // Create the Item object
                $item = new Item(
                    $inputData['itemId'],
                    $inputData['name'],
                    $inputData['price'],
                    $inputData['description'],
                    $inputData['brand'],
                    $inputData['date'],
                    $inputData['quantity'],
                    $inputData['image']
                );

                // Call the DAO's updateItemFields method
                $result = $this->adminDAO->updateItemFields($item);

                // Respond based on the DAO's result
                if ($result) {
                    http_response_code(200); // OK
                    echo json_encode([
                        "status" => "success",
                        "message" => "Item updated successfully"
                    ]);
                } else {
                    http_response_code(500); // Internal Server Error
                    echo json_encode([
                        "status" => "error",
                        "message" => "Failed to update item"
                    ]);
                }
            } catch (Exception $e) {
                // Handle exceptions and provide a meaningful error response
                http_response_code(500); // Internal Server Error
                echo json_encode([
                    "status" => "error",
                    "message" => "An error occurred: " . $e->getMessage()
                ]);
            }
        } else {
            // Invalid request method
            http_response_code(405); // Method Not Allowed
            echo json_encode([
                "status" => "error",
                "message" => "Invalid request method"
            ]);
        }
    }


    /**
     * Handles a PUT request from Administrator to update an Item's inventory levels.
     * 
     * This endpoint allows an administrator to update the inventory levels of an item
     * after a customer checkout. It expects the `itemId` and the `amount` to be provided.
     * The inventory levels of the corresponding item are adjusted accordingly.
     * 
     * The response is returned in JSON format, and appropriate HTTP status codes
     * are set based on the success or failure of the operation.
     * 
     * @return void Outputs a JSON-encoded response.
     * assignTo: Hiraku
     */

    public function updateItemInventory() {
        // Set response header
        header('Content-Type: application/json');

        // Check if the request method is PUT
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            // Retrieve and decode the input data
            parse_str(file_get_contents("php://input"), $inputData);

            // Validate required fields
            if (!isset($inputData['itemId']) || !isset($inputData['amount'])) {
                http_response_code(400); // Bad Request
                echo json_encode([
                    "status" => "error",
                    "message" => "Missing required fields: itemId and amount"
                ]);
                return;
            }

            // Extract the itemId and amount
            $itemId = $inputData['itemId'];
            $amount = $inputData['amount'];

            // Validate amount (it should be an integer and non-negative)
            if (!is_numeric($amount) || $amount <= 0) {
                http_response_code(400); // Bad Request
                echo json_encode([
                    "status" => "error",
                    "message" => "Invalid amount value"
                ]);
                return;
            }

            try {
                // Call the function to update the item inventory
                $result = $this->itemDAO->updateItemInventory($itemId, $amount);

                // Respond based on the result of the update
                if ($result) {
                    http_response_code(200); // OK
                    echo json_encode([
                        "status" => "success",
                        "message" => "Inventory updated successfully"
                    ]);
                } else {
                    http_response_code(500); // Internal Server Error
                    echo json_encode([
                        "status" => "error",
                        "message" => "Failed to update inventory"
                    ]);
                }
            } catch (Exception $e) {
                // Handle exceptions
                http_response_code(500); // Internal Server Error
                echo json_encode([
                    "status" => "error",
                    "message" => "An error occurred: " . $e->getMessage()
                ]);
            }
        } else {
            // Invalid request method
            http_response_code(405); // Method Not Allowed
            echo json_encode([
                "status" => "error",
                "message" => "Invalid request method"
            ]);
        }
    }


    /**
     * Handles a DELETE request from Administrator to remove an item.
     * needs the itemid, and call deleteItem(itemid) from AdminDAO
     * Check signinUser for clues on what to do to retrieve the itemid from the request
     * assignedTo: Rasengan
     */
    public function adminDeleteItem() {
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

            $data = json_decode(file_get_contents('php://input'), true);


            $itemid = $data['item_id'] ?? null;
           

             //checking if itemid is null
             if($itemid){    
                
                $deletedItem = $this->userDAO->deleteItem();
                

                header('Content-Type: application/json');

                //Checks if the item has been deleted. On success http response code is 200. Else response code is 404
                if($deletedItem){
                    http_response_code(200);
                    echo json_encode(["status" => "success", "message" => "delete item test success"]);
                }else{
                    http_response_code(404);
                    echo json_encode(["status" => "error", "message" => "item not found"]);
                }
               
                //If itemid is null http response code is 400
            } else{
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Item id is null"]);
            }


        //if request method is not DELETE http response code is 405
        }else{
            http_response_code(405);
            echo json_encode(["status" => "error", "message" => "Invalid request method. Use DELETE"]);
        }
    }

    /**
     * Handles a DELETE request from Administrator to remove a customer given their username.
     * 
     * This endpoint allows an administrator to delete a customer record from the database
     * by specifying the customer's username. If successful, the response will indicate success,
     * otherwise an error message will be returned.
     * 
     * @return void Outputs a JSON-encoded response.
     * assignedTo: Hiraku
     */
    public function adminDeleteCustomer() {
        // Set response header
        header('Content-Type: application/json');

        // Check if the request method is DELETE
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            // Retrieve and decode the input data
            parse_str(file_get_contents("php://input"), $inputData);

            // Validate required field (userName)
            if (!isset($inputData['userName'])) {
                http_response_code(400); // Bad Request
                echo json_encode([
                    "status" => "error",
                    "message" => "Missing required field: userName"
                ]);
                return;
            }

            // Extract the userName
            $userName = $inputData['userName'];

            try {
                // Call the DAO function to delete the customer record
                $result = $this->adminDAO->deleteCustomerRecords($userName);

                // Respond based on the result of the deletion
                if ($result) {
                    http_response_code(200); // OK
                    echo json_encode([
                        "status" => "success",
                        "message" => "Customer deleted successfully"
                    ]);
                } else {
                    http_response_code(500); // Internal Server Error
                    echo json_encode([
                        "status" => "error",
                        "message" => "Failed to delete customer"
                    ]);
                }
            } catch (Exception $e) {
                // Handle exceptions
                http_response_code(500); // Internal Server Error
                echo json_encode([
                    "status" => "error",
                    "message" => "An error occurred: " . $e->getMessage()
                ]);
            }
        } else {
            // Invalid request method
            http_response_code(405); // Method Not Allowed
            echo json_encode([
                "status" => "error",
                "message" => "Invalid request method"
            ]);
        }
    }

    /**
     * Handles a GET request to retrieve all items from inventory.
     *
     * assignedTo: Rasengan
     */
    //TODO: the function you should call is in the ItemDAO 
    public function getItems() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {

            //Call getAllItems() from the itemDAO
            $items = $this->itemDAO->getAllItems();
            header('Content-Type: application/json');

            //Checks if $items is null. If not return $items and set response code to 200.
            //If it is false then set the response code to 404
            if($items){
                http_response_code(200);
            echo json_encode(["status" => "success", "items" => $items]);
            }else{
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Items not found"]);
                
            }
        //If invalid request method is used then set the response code to 405
        }else{
            http_response_code(405);
            echo json_encode(["status" => "error", "message" => "Invalid request method. Use GET"]);
        }
    }

    /**
     * Handles a GET request to retrieve all transactions made by the user, given user's username.
     * assignedTo: Hiraku
     */
    public function getUserTransactions() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            // Check if the username parameter is provided
            if (isset($_GET['username']) && !empty($_GET['username'])) {
                $username = $_GET['username'];

                try {
                    // Call the getUserTransactions() method
                    $transactions = $this->getUserTransactions($username);

                    if (!empty($transactions)) {
                        // Return success response with transactions data
                        http_response_code(200); // HTTP Status 200: OK
                        echo json_encode([
                            "status" => "success",
                            "message" => "Transactions retrieved successfully.",
                            "data" => $transactions
                        ]);
                    } else {
                        // If no transactions found, return a 404 response
                        http_response_code(404); // HTTP Status 404: Not Found
                        echo json_encode([
                            "status" => "error",
                            "message" => "No transactions found for the given username."
                        ]);
                    }
                } catch (Exception $e) {
                    // Handle any errors with a 500 response
                    http_response_code(500); // HTTP Status 500: Internal Server Error
                    echo json_encode([
                        "status" => "error",
                        "message" => "Failed to retrieve transactions: " . $e->getMessage()
                    ]);
                }
            } else {
                // If the username parameter is missing, return a 400 response
                http_response_code(400); // HTTP Status 400: Bad Request
                echo json_encode([
                    "status" => "error",
                    "message" => "Missing or invalid username parameter."
                ]);
            }
        } else {
            // If the request method is not GET, return a 405 response
            http_response_code(405); // HTTP Status 405: Method Not Allowed
            echo json_encode([
                "status" => "error",
                "message" => "Invalid request method. Only GET is allowed."
            ]);
        }
    }

        /**
     * Handles a POST request to add user transactions, given item IDs and payment details.
     * assignedTo: Hiraku
     */
    public function addUserTransactions() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get the raw POST data
            $data = json_decode(file_get_contents('php://input'), true);

            // Check if all required fields are present in the request
            if (isset($data['items']) && isset($data['payment'])) {
                $items = $data['items']; // Array of objects with itemId and quantity
                $paymentData = $data['payment']; // Payment details

                // Extract payment details
                $fullName = $paymentData['fullName'];
                $userName = $paymentData['userName'];
                $cardNum = $paymentData['card_num'];
                $cvv = $paymentData['cvv'];
                $expiry = $paymentData['expiry'];

                // Initialize an empty array to hold the transaction objects
                $transactions = [];
                $totalPrice = 0;

                try {
                    // Loop through the items and get item details
                    foreach ($items as $item) {
                        // Check if both itemId and quantity are present
                        if (isset($item['itemId']) && isset($item['quantity'])) {
                            $itemId = $item['itemId'];
                            $quantity = $item['quantity'];

                            // Get item data using itemDAO
                            $itemModel = $this->itemDAO->getItemById($itemId);

                            // Check if the item exists
                            if ($itemModel) {
                                // Calculate the price for the quantity ordered
                                $itemPrice = $itemModel->getPrice();
                                $totalPrice += $itemPrice * $quantity;

                                // Create a new transaction model for each item
                                $transaction = new Transaction(
                                    null, // trans_id will be assigned later
                                    $itemModel->getItemId(),
                                    $userName,
                                    $quantity, // Set the quantity from the request
                                    null
                                );

                                // Add the transaction to the transactions array
                                $transactions[] = $transaction;
                            } else {
                                // Handle the case where the item doesn't exist
                                echo json_encode(["status" => "error", "message" => "Item ID $itemId not found"]);
                                return;
                            }
                        } else {
                            // Missing itemId or quantity in the request
                            echo json_encode(["status" => "error", "message" => "Invalid item data (missing itemId or quantity)"]);
                            return;
                        }
                    }

                    // Create a payment model
                    $payment = new Payment(
                        null, // trans_id will be generated on insert
                        $cardNum,
                        $cvv,
                        $expiry,
                        $totalPrice,
                        1, // Set processed status as 1 (successful)
                        null, // Date of payment
                        $fullName
                    );

                    // Add transactions to the payment model
                    foreach ($transactions as $transaction) {
                        $payment->addTransaction($transaction);
                    }

                    // Call userDAO's addUserTransactions to insert payment and transactions into the database
                    $result = $this->userDAO->addUserTransactions($payment);

                    $updated_inventory = $itemModel.getQuantity() - $quantity;
                    $itemDAO->updateItemInventory($updated_inventory);

                    // Return success response
                    echo json_encode(["status" => "success", "message" => "Transactions added successfully", "data" => $result]);

                } catch (Exception $e) {
                    // Handle exceptions (log error, rethrow, etc.)
                    echo json_encode(["status" => "error", "message" => "Error processing transactions: " . $e->getMessage()]);
                }
            } else {
                // Missing required fields in the request
                echo json_encode(["status" => "error", "message" => "Missing required fields"]);
            }
        } else {
            // Invalid HTTP method (only POST is allowed)
            echo json_encode(["status" => "error", "message" => "Invalid request method"]);
        }
    }




     /**
     * Handles a GET request to retrieve all transactions made by all users.
     * I think its just one function call from AdminDAO
     * assignedTo: Rasengan
     */
    public function adminGetAllTransactions() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {

            //Call getSalesHistory from adminDAO
            $sales = $this->adminDAO->getSalesHistory();

            header('Content-Type: application/json');

            //Check if $sales is null. If not return $sales and set the response code to 200.
            //If false response code is 404 
            if($sales){

                http_response_code(200);
                echo json_encode(["status" => "success", "sales" => $sales]);
            
            }else{
                
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Sales history not found"]);
            }

            //If invalid request method is used response code is 405
        }else{

            http_response_code(405);
            echo json_encode(["status" => "error", "message" => "Invalid request method. Use GET"]);
        }
    }

    /**
     * Handles a GET request to retrieve all customers from the database.
     * assignedTo: Hiraku
     */
    public function adminGetCustomers() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            try {
                // Fetch all users using the getAllUsers() function
                $users = $adminDAO->getAllUsers();

                if (!empty($users)) {
                    // Return success response with users data
                    http_response_code(200); // HTTP Status 200: OK
                    echo json_encode([
                        "status" => "success",
                        "message" => "Customers retrieved successfully.",
                        "data" => $users
                    ]);
                } else {
                    // If no users found, return a 404 response
                    http_response_code(404); // HTTP Status 404: Not Found
                    echo json_encode([
                        "status" => "error",
                        "message" => "No customers found."
                    ]);
                }
            } catch (Exception $e) {
                // Handle any errors with a 500 response
                http_response_code(500); // HTTP Status 500: Internal Server Error
                echo json_encode([
                    "status" => "error",
                    "message" => "Failed to retrieve customers: " . $e->getMessage()
                ]);
            }
        } else {
            // If the request method is not GET, return a 405 response
            http_response_code(405); // HTTP Status 405: Method Not Allowed
            echo json_encode([
                "status" => "error",
                "message" => "Invalid request method. Only GET is allowed."
            ]);
        }
    }


    /**
     * Handles a PUT request to update user details.
     * You'll be calling this function updateProfileFields(Profile $profile)
     * So you need to create a Profile instance and fill out the fields
     *
     */
    // TODO: this is a PUT request to update fields to user profiles given the user's username and details to update
    //assignedTo: Rasengan
    public function updateUserProfile() {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            //get json body
            $data = json_decode(file_get_contents('php://input'), true);

            $userName = $data['userName'] ?? null;
            $firstName = $data['firstName'] ?? null;
            $lastName = $data['lastName'] ?? null;
            $age = $data['age'] ?? null;
            $street = $data['street'] ?? null;
            $city = $data['city'] ?? null;
            $province = $data['province'] ?? null;
            $postal = $data['postal'] ?? null;
            $cardNum = $data['card_num'] ?? null;
            $cvv = $data['cvv'] ?? null;
            $expiry = $data['expiry'] ?? null;

            //create a Profile object
            $userProfile = new Profile(
                $userName,
                $firstName,
                $lastName,
                $age,
                $street,
                $city,
                $province,
                $postal,
                $cardNum,
                $cvv,
                $expiry);

            //Call the updateProfileFields function in userDAO
            $updateProfile = $this->userDAO->updateProfileFields($userProfile);

            header('Content-Type: application/json');
            
            //Check if $updateProfile is null. If not http response code is 200.
            //If false http response code is 404
            if($updateProfile){
                http_response_code(200);
                echo json_encode(["status" => "success", "message" => "updateUserProfile test successful"]);
            }else{
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Profile not found"]);
            }
            

        //If invalid request method is used set the response code to 405 
        }else{
            http_response_code(405);
            echo json_encode(["status" => "error", "message" => "Invalid request method. Use PUT"]);
        }
    }

    /**
     * Handles a PUT request to update user profile by admin, given the user's username and details to update.
     * assignedTo: Hiraku
     */
    public function adminUpdateUserProfile() {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            // Get the raw PUT data
            $data = json_decode(file_get_contents('php://input'), true);

            // Check if at least one of the fields is provided
            if (isset($data['username']) && !empty($data['username'])) {
                $username = $data['username'];
                $fullName = isset($data['fullName']) ? $data['fullName'] : null;
                $address = isset($data['address']) ? $data['address'] : null;
                $cardNum = isset($data['card_num']) ? $data['card_num'] : null;
                $cvv = isset($data['cvv']) ? $data['cvv'] : null;
                $expiry = isset($data['expiry']) ? $data['expiry'] : null;
                $phone = isset($data['phone']) ? $data['phone'] : null;

                // Check if at least one field is not null
                if (is_null($fullName) && is_null($address) && is_null($cardNum) && is_null($cvv) && is_null($expiry) && is_null($phone)) {
                    // If all fields are null, return an error
                    echo json_encode(["status" => "error", "message" => "No fields provided for update"]);
                    return;
                }

                // Populate Profile model with the updated details
                $profile = new Profile(
                    $username,   // Username to identify the user
                    $fullName,   // Full Name (can be null)
                    $address,    // Address (can be null)
                    $cardNum,    // Card Number (can be null)
                    $cvv,        // CVV (can be null)
                    $expiry,     // Expiry (can be null)
                    $phone       // Phone (can be null)
                );

                try {
                    // Call the adminDAO to update the customer records in the database
                    $result = $this->adminDAO->updateCustomerRecords($profile);

                    // Return a success response
                    echo json_encode(["status" => "success", "message" => "User profile updated successfully", "data" => $result]);
                } catch (Exception $e) {
                    // Handle any exceptions
                    echo json_encode(["status" => "error", "message" => "Error updating profile: " . $e->getMessage()]);
                }
            } else {
                // If username is missing, return an error
                echo json_encode(["status" => "error", "message" => "Username is required"]);
            }
        } else {
            // If not a PUT request, return an error
            echo json_encode(["status" => "error", "message" => "Invalid request method"]);
        }
    }


}

new AuthController();
?>

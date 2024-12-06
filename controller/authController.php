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
                '/getTransactions' => 'getTransactions',
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
                '/addUserProfile' => 'addUserProfile',
                '/registerProfile' => 'registerProfileFields',
            ],
            'DELETE' => [
                '/deleteItem' => 'adminDeleteItem',
                '/deleteProfileFields' => 'deleteProfileFields',
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
    }

    

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
     * Handles a PUT request from Administrator to update an Item.
     * 
     */
    // TODO: this is a PUT request to update Items in inventory
    // assignedTo: Hiraku
    public function adminUpdateItem() {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            echo json_encode(["status" => "success", "message" => "update item test successful"]);
        }
    }

     /**
     * Handles a PUT request from Administrator to update an Item.
     * 
     */
    // TODO: this is a PUT request to update inventory levels after customer checkout
    // assignedTo: Hiraku
    public function updateItemInventory() {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            echo json_encode(["status" => "success", "message" => "update item inventory test successful"]);
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
     * Handles a DELETE request from Administrator to remove an customer given username.
     * assignedTo: Hiraku
     */
    //TODO
    public function adminDeleteCustomer() {
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            echo json_encode(["status" => "success", "message" => "delete customer test success"]);
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

            $items = $this->itemDAO->getAllItems();
            header('Content-Type: application/json');
            if($items){
                http_response_code(200);
            echo json_encode(["status" => "success", "items" => $items]);
            }else{
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Items not found"]);
                
            }
        }else{
            http_response_code(405);
            echo json_encode(["status" => "error", "message" => "Invalid request method. Use GET"]);
        }
    }

     /**
     * Handles a GET request to retrieve all transactions made by the user, given user's username.
     * assignedTo: Hiraku
     */
    public function getTransactions() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            echo json_encode(["status" => "success", "message" => "get item test success"]);
        }
    }

     /**
     * Handles a GET request to retrieve all transactions made by all users.
     * I think its just one function call from AdminDAO
     * assignedTo: Rasengan
     */
    public function adminGetAllTransactions() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {

            $sales = $this->adminDAO->getSalesHistory();

            header('Content-Type: application/json');
            if($sales){

                http_response_code(200);
                echo json_encode(["status" => "success", "sales" => $sales]);
            
            }else{
                
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Sales history not found"]);
            }
        }else{

            http_response_code(405);
            echo json_encode(["status" => "error", "message" => "Invalid request method. Use GET"]);
        }
    }

    /**
     * Handles a GET request to retrieve all items from inventory.
     * assignedTo: Hiraku
     */
    public function adminGetCustomers() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            echo json_encode(["status" => "success", "message" => "delete item test success"]);
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
            echo json_encode(["status" => "success", "message" => "updateUserProfile test successful"]);
        }
    }

    // TODO: this is a PUT request to update fields to user profiles by admin given the user's username and details to update
    // assignedTo: Hiraku
    public function adminUpdateUserProfile() {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            echo json_encode(["status" => "success", "message" => "updateUserProfile test successful"]);
        }
    }

}

new AuthController();
?>

<?php
// Include necessary files (Database, DAO classes, etc.)
include_once 'dao/Database.php';
include_once 'dao/userDAO.php';
include_once 'dao/adminDAO.php';

class AuthController {
    private $pdo;

    // Constructor to initialize the PDO connection
    public function __construct() {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    /**
     * Handles user signin (POST request).
     * Verifies the user credentials and sends a success message if valid.
     */
    public function signinUser() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userName = $_POST['userName'] ?? null;
            $password = $_POST['password'] ?? null;

            if ($userName && $password) {
                // Create UserDAO instance
                $adminDAO = new AdminDAO($this->pdo);
                if($adminDAO->isAdmin($userName)){
                    if($adminDAO->updateLastLogon($userName))
                        echo json_encode(["status" => "success", "message" => "admin"]);
                    else
                        echo json_encode(["status" => "error", "message" => "admin login but last logon failed to update."]);
                    return;
                }

                $userDAO = new UserDAO($this->pdo);

                // Register the user and profile
                $isValidUser = $userDAO->validateUser($userName, $password);

                if ($isValidUser) {
                    
                    if($userDAO->updateLastLogon($userName))
                        echo json_encode(["status" => "success", "message" => "user"]);
                    else
                        echo json_encode(["status" => "error", "message" => "User login but last logon failed to update."]);
                } else {
                    echo json_encode(["status" => "error", "message" => "User validation not successful"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "All fields are required (firstName, lastName)"]);
            }
        } else {
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
                $userDAO = new UserDAO($this->pdo);

                // Register the user and profile
                $isRegistered = $userDAO->registerUser($userName, $password, $firstName, $lastName);

                if ($isRegistered) {
                    echo json_encode(["status" => "success", "message" => "User registered successfully."]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Username already exists."]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "All fields are required (firstName, lastName, userName, password)."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid request method. Use POST."]);
        }
    }

    /**
     * Handles a PUT request from Administrator to add an item.
     * 
     */
    // TODO: this is a PUT request to add additional fields to user profiles
    public function adminAddItem() {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            echo json_encode(["status" => "success", "message" => "updateUserProfile test successful"]);
        }
    }

    /**
     * Handles a PUT request from Administrator to update an Item.
     * 
     */
    // TODO: this is a PUT request to add additional fields to user profiles
    public function adminUpdateItem() {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            echo json_encode(["status" => "success", "message" => "add item test successful"]);
        }
    }

    public function adminUpdateItem() {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            echo json_encode(["status" => "success", "message" => "update item test successful"]);
        }
    }

    /**
     * Handles a DELETE request from Administrator to remove an item.
     */
    public function adminDeleteItem() {
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            echo json_encode(["status" => "success", "message" => "delete item test success"]);
        }
    }


    /**
     * Handles a PUT request to update user details.
     * 
     */
    // TODO: this is a PUT request to add additional fields to user profiles
    public function updateUserProfile() {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            echo json_encode(["status" => "success", "message" => "updateUserProfile test successful"]);
        }
    }

    /**
     * Handles a DELETE request to remove a user.
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            echo json_encode(["status" => "success", "message" => "delete test success"]);
        }
    }

    /**
     * Handles a GET request to retrieve user details.
     */
    public function getUser() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            echo json_encode(["status" => "success", "message" => "getUser Test success."]);
        }
    }
}
?>

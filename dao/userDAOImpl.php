<?php
/**
 * Class UserDAO
 *
 * Data Access Object (DAO) for interacting with the User and Profile tables in the database.
 * 
 * Fields:
 * - pdo (PDO): A PDO connection object for interacting with the database.
 *
 * Methods:
 * - __construct(PDO $pdo): Initializes the DAO with a PDO connection.
 * - getUserByUserName(string $userName): Retrieves a UserModel populated with data from the User table
 *   and an associated ProfileModel from the Profile table, if available.
 * - updateProfileFields(ProfileModel $profile): Updates null fields in the Profile table for the 
 *   specified user using ProfileModel data.
 *
 * Usage:
 * This class is designed to handle the retrieval and updating of user and profile data from the 
 * database. It also used to validate login as well as registering new users.
 * It ensures safe and efficient interaction with the database using prepared statements 
 * to prevent SQL injection.
 *
 */

//include_once '../model/userModel.php';
//include_once '../model/profileModel.php';
include_once 'userDAO.php';

class UserDAOImpl implements UserDAO {
    private $pdo;

    /**
     * Constructor
     * @param PDO $pdo - A PDO connection to the database
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Retrieves a user by userName and populates the User with its associated ProfileModel.
     * @param string $userName - The userName of the user to retrieve
     * @return User|null - Returns a User if the user is found, otherwise null
     */
    public function getUserByUserName($userName) {
        try {
            // Query to get user information from User table
            $userQuery = "SELECT * FROM User WHERE userName = :userName";
            $stmt = $this->pdo->prepare($userQuery);
            $stmt->bindParam(':userName', $userName);
            $stmt->execute();
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$userRow) {
                return null; // User not found
            }

            // Create a User object
            $user = new User(
                $userRow['userName'],
                $userRow['password'],
                $userRow['salt'],
                $userRow['last_logon'],
                $userRow['admin']
            );

            // Query to get profile information from Profile table
            $profileQuery = "SELECT * FROM Profile WHERE userName = :userName";
            $stmt = $this->pdo->prepare($profileQuery);
            $stmt->bindParam(':userName', $userName);
            $stmt->execute();
            $profileRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($profileRow) {
                // Create and populate a Profile object
                $profile = new Profile(
                    $profileRow['userName'],
                    $profileRow['firstName'],
                    $profileRow['lastName'],
                    $profileRow['age'],
                    $profileRow['street'],
                    $profileRow['city'],
                    $profileRow['province'],
                    $profileRow['postal'],
                    $profileRow['card_num'],
                    $profileRow['cvv'],
                    $profileRow['expiry']
                );

                // Set the profile in the UserModel
                $user->setProfile($profile);
            }

            return $user;

        } catch (PDOException $e) {
            // Handle exceptions (log error, rethrow, etc.)
            echo "Error retrieving user: " . $e->getMessage();
            return null;
        }
    }

    /**
     * Validates a user's login credentials by checking the username and password
     * against the User table.
     * 
     * @param string $userName - The username provided by the user
     * @param string $password - The password provided by the user
     * @return bool - Returns true if the login is successful, false otherwise
     */
    public function validateUser($userName, $password) {
        try {
            // Query to get the user by username
            $query = "SELECT * FROM User WHERE userName = :userName";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':userName', $userName);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // If the user exists
            if ($user) {
                // Get the hashed password and salt from the database
                $storedHash = $user['password'];
                $salt = $user['salt'];

                // Reconstruct the hashed password using the salt
                //$hashedPassword = password_hash($password . $salt, PASSWORD_BCRYPT);
                $passwordWithSalt = $password . $salt;
                // Validate the password using password_verify
                if (password_verify($passwordWithSalt, $storedHash)) {
                    return true;  // Login successful
                } else {
                    return false; // Invalid password
                }
            } else {
                return false; // Invalid username
            }

        } catch (PDOException $e) {
            // Handle exceptions (log error, rethrow, etc.)
            echo "Error validating user: " . $e->getMessage();
            return false; // An error occurred during validation
        }
    }


     /**
     * Updates the last_logon field to the current timestamp when the user logs in.
     * 
     * @param string $userName - The username whose last_logon needs to be updated
     * @return bool - Returns true if the last_logon field was updated successfully, false otherwise
     */
    public function updateLastLogon($userName) {
        try {
            // Prepare the SQL query to update the last_logon field
            $query = "UPDATE User SET last_logon = CURRENT_TIMESTAMP WHERE userName = :userName";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':userName', $userName);

            // Execute the query and check if the update was successful
            if ($stmt->execute()) {
                return true;
            } else {
                return false; // Failed to update the last_logon
            }

        } catch (PDOException $e) {
            // Handle exceptions (log error, rethrow, etc.)
            echo "Error updating last_logon: " . $e->getMessage();
            return false; // Return false in case of any error
        }
    }

    /**
     * Registers a new user by hashing and salting the password before storing it in the User table.
     * After registering the user, it also adds the user profile (userName, firstName, lastName) in the Profile table.
     * 
     * @param string $userName - The username to be registered
     * @param string $password - The password to be registered
     * @param string $firstName - The first name of the user
     * @param string $lastName - The last name of the user
     * @return bool - Returns true if the user and profile were registered successfully, false otherwise
     */
    public function registerUser($userName, $password, $firstName, $lastName) {
        try {
            // Check if the username already exists in the database
            $query = "SELECT * FROM User WHERE userName = :userName";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':userName', $userName);
            $stmt->execute();

            // If the username already exists, return false
            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                return false; // User already exists
            }

            // Generate a salt for the password (this can be done in many ways, here we use random_bytes)
            $salt = bin2hex(random_bytes(32)); // Generate a random 32-byte salt
            // Hash the password with the salt
            $hashedPassword = password_hash($password . $salt, PASSWORD_BCRYPT);

            // Start a transaction to ensure both the user and profile are inserted successfully
            $this->pdo->beginTransaction();

            // Prepare the query to insert the new user into the User table
            $query = "INSERT INTO User (userName, password, salt) VALUES (:userName, :password, :salt)";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':userName', $userName);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':salt', $salt);

            // Execute the query and insert the user into the User table
            if ($stmt->execute()) {
                // Now insert the user's profile into the Profile table
                $query = "INSERT INTO Profile (userName, firstName, lastName) VALUES (:userName, :firstName, :lastName)";
                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(':userName', $userName);
                $stmt->bindParam(':firstName', $firstName);
                $stmt->bindParam(':lastName', $lastName);

                // Execute the query and insert the profile into the Profile table
                if ($stmt->execute()) {
                    // Commit the transaction if both the user and profile are inserted successfully
                    $this->pdo->commit();
                    return true; // User and profile were successfully added
                } else {
                    // Rollback the transaction if the profile insert fails
                    $this->pdo->rollBack();
                    return false; // Failed to insert profile
                }
            } else {
                // Rollback the transaction if the user insert fails
                $this->pdo->rollBack();
                return false; // Failed to insert user
            }

        } catch (PDOException $e) {
            // Handle exceptions (log error, rethrow, etc.)
            $this->pdo->rollBack();
            echo "Error registering user: " . $e->getMessage();
            return false; // Return false in case of any error
        }
    }

    /**
     * Fetches the purchase history for a given user.
     * This retrieves the transactions associated with the user and
     * the corresponding payments, returning an array of Payment objects.
     * 
     * @param string $userName - The username for which to retrieve the purchase history
     * @return array - An array of Payment objects, each containing the associated transactions
     */
    /*
    public function getPurchaseHistory($userName) {
        try {
            // Step 1: Retrieve all transactions associated with the userName
            $query = "SELECT * FROM Transaction WHERE userName = :userName";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':userName', $userName);
            $stmt->execute();
            
            // Step 2: Create an array to hold all transaction data
            $transactions = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $transactions[] = new Transaction(
                    $row['trans_id'],
                    $row['item_id'],
                    $row['userName'],
                    $row['quantity'],
                    $row['date']
                );
            }

            return $transactions;

        } catch (PDOException $e) {
            // Handle any errors (e.g., log the error or rethrow)
            echo "Error fetching purchase history: " . $e->getMessage();
            return [];
        }
    }*/



    /**
 * Updates fields in the Profile table for a specific user.
 * Only non-null fields will be updated in the query.
 * @param Profile $profile - The ProfileModel containing updated data
 * @return bool - Returns true if the update was successful, otherwise false
 */
public function updateProfileFields(Profile $profile) {
    try {
        // Initialize the base query and parameters array
        $updateQuery = "UPDATE Profile SET ";
        $parameters = [];

        // Dynamically construct the query based on non-null fields
        if (!is_null($profile->getFirstName())) {
            $updateQuery .= "firstName = :firstName, ";
            $parameters[':firstName'] = $profile->getFirstName();
        }
        if (!is_null($profile->getLastName())) {
            $updateQuery .= "lastName = :lastName, ";
            $parameters[':lastName'] = $profile->getLastName();
        }
        if (!is_null($profile->getAge())) {
            $updateQuery .= "age = :age, ";
            $parameters[':age'] = $profile->getAge();
        }
        if (!is_null($profile->getStreet())) {
            $updateQuery .= "street = :street, ";
            $parameters[':street'] = $profile->getStreet();
        }
        if (!is_null($profile->getCity())) {
            $updateQuery .= "city = :city, ";
            $parameters[':city'] = $profile->getCity();
        }
        if (!is_null($profile->getProvince())) {
            $updateQuery .= "province = :province, ";
            $parameters[':province'] = $profile->getProvince();
        }
        if (!is_null($profile->getPostal())) {
            $updateQuery .= "postal = :postal, ";
            $parameters[':postal'] = $profile->getPostal();
        }
        if (!is_null($profile->getCardNum())) {
            $updateQuery .= "card_num = :cardNum, ";
            $parameters[':cardNum'] = $profile->getCardNum();
        }
        if (!is_null($profile->getCvv())) {
            $updateQuery .= "cvv = :cvv, ";
            $parameters[':cvv'] = $profile->getCvv();
        }
        if (!is_null($profile->getExpiry())) {
            $updateQuery .= "expiry = :expiry, ";
            $parameters[':expiry'] = $profile->getExpiry();
        }

        // Remove the trailing comma and space
        $updateQuery = rtrim($updateQuery, ', ');

        // Add the WHERE clause
        $updateQuery .= " WHERE userName = :userName";
        $parameters[':userName'] = $profile->getUserName();

        // Prepare and execute the query
        $stmt = $this->pdo->prepare($updateQuery);

        // Bind parameters
        foreach ($parameters as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        return $stmt->execute();

    } catch (PDOException $e) {
        // Handle exceptions (log error, rethrow, etc.)
        echo "Error updating profile: " . $e->getMessage();
        return false;
    }
}


    /**
     * Retrieves a list of transactions for a specific user and groups them by transaction ID.
     * 
     * @param string $userName The username to fetch transactions for.
     *
     * @return array An array of Payment objects, each containing related transactions.
     */
    public function getUserTransactions($userName) {
        // Initialize an empty array to hold payment objects
        $payments = [];
    
        try {
            // Fetch transactions for the given username
            $sql = "SELECT * FROM Transaction WHERE userName = :userName ORDER BY trans_id ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':userName', $userName);
            $stmt->execute();
    
            // Fetch all transactions for the user
            $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Group transactions by trans_id
            $groupedTransactions = [];
            foreach ($transactions as $transaction) {
                $transId = $transaction['trans_id'];
                // Create Transaction object
                $transactionModel = new Transaction(
                    $transaction['trans_id'],
                    $transaction['item_id'],
                    $transaction['userName'],
                    $transaction['quantity'],
                    $transaction['date']
                );

                $transactionModel->setItemPrice($transaction['price']);
    
                // Group by trans_id
                if (!isset($groupedTransactions[$transId])) {
                    $groupedTransactions[$transId] = [];
                }
                $groupedTransactions[$transId][] = $transactionModel;
            }
    
            // Create Payment objects and add corresponding transactions
            foreach ($groupedTransactions as $transId => $transactions) {
                // Fetch payment details for the trans_id
                $sql2 = "SELECT trans_id, card_num, cvv, expiry, total_price, fullName, date 
                         FROM Payment WHERE trans_id = :trans_id AND processed = 1";
                $stmt = $this->pdo->prepare($sql2);
                $stmt->bindParam(':trans_id', $transId);
                $stmt->execute();
    
                // Fetch the result as an associative array
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
                // If no processed payment is found, skip to the next group
                if (!$result) {
                    continue;
                }
    
                // Populate variables with the values from the result
                $cardNum = $result['card_num'];
                $cvv = $result['cvv'];
                $expiry = $result['expiry'];
                $totalPrice = $result['total_price'];
                $date = $result['date'];
                $fullName = $result['fullName'];
                $processed = $result['processed'];
    
                // Create a Payment object
                $payment = new Payment(
                    $transId,
                    $cardNum,
                    $cvv,
                    $expiry,
                    $totalPrice,
                    $processed, // Processed is always true here
                    $date,
                    [] // Start with an empty transaction array
                );
    
                // Add transactions to the Payment object
                foreach ($transactions as $transaction) {
                    $payment->addTransaction($transaction);
                }
    
                // Set the full name in the Payment object
                $payment->setFullName($fullName);
    
                // Add the populated Payment object to the payments array
                $payments[] = $payment;
            }
    
            // Return the list of payment objects
            return $payments;
    
        } catch (PDOException $e) {
            // Handle any errors
            echo json_encode(["status" => "error", "message" => "Failed to retrieve transactions: " . $e->getMessage()]);
            return [];
        }
    }
    

    /**
     * Adds a payment and its associated transactions to the database.
     *
     * This function performs the following:
     * 1. Inserts payment information into the `Payment` table.
     * 2. Inserts each transaction associated with the payment into the `Transaction` table.
     * 3. Uses a database transaction to ensure both operations succeed together. If any error occurs, the transaction is rolled back.
     *
     * @param Payment $payment The Payment object containing payment details and associated transactions.
     *
     * @return array An associative array indicating the status and message of the operation:
     *               - "status" => "success" or "error"
     *               - "message" => A success or error message.
     *
     * @throws Exception If any database operation fails, the exception message will be included in the error response.
     * assignedTo: Hiraku
     */
    public function addUserTransaction(Payment $payment) {
    
        $transId = $payment->getTransId();
        $cardNum = $payment->getCardNum();
        $cvv = $payment->getCvv();
        $expiry = $payment->getExpiry();
        $totalPrice = $payment->getTotalPrice();
        $fullName = $payment->getFullName();
        $transactions = $payment->getTransactions(); // Array of Transaction objects
    
        try {
            // Start a transaction to ensure both operations (Payment and Transactions) succeed together
            $this->pdo->beginTransaction();
            // Insert each transaction associated with the payment into the Transaction table
            $firstTrans = true;
            
            $sql = "INSERT INTO Transaction (item_id, userName, quantity, price) 
                        VALUES (:item_id, :userName, :quantity, :price)";
                        
            foreach ($transactions as $transaction) {
                $itemId = $transaction->getItemId();
                $userName = $transaction->getUserName();
                $quantity = $transaction->getQuantity();
                $price = $transaction->getItemPrice();

                $stmt = $this->pdo->prepare($sql);

                // Insert each transaction into the Transaction table

                if(!$firstTrans)
                    $stmt->bindParam(':trans_id', $transId); 

                $stmt->bindParam(':item_id', $itemId);
                $stmt->bindParam(':userName', $userName);
                $stmt->bindParam(':quantity', $quantity);
                $stmt->bindParam(':price', $price);
    
                $stmt->execute();
    
                if($firstTrans){
                    // Get the last inserted ID (trans_id)
                    $transId = $this->pdo->lastInsertId();


                    $sql = "INSERT INTO Transaction (trans_id, item_id, userName, quantity, price) 
                        VALUES (:trans_id, :item_id, :userName, :quantity, :price)";

                    $firstTrans = false;

                }

            }
            // Insert the payment information into the Payment table
            $sql = "INSERT INTO Payment (trans_id, card_num, cvv, expiry, total_price, processed, fullName) 
                    VALUES (:trans_id, :card_num, :cvv, :expiry, :total_price, 1, :fullName)"; // processed is set to 1 (successful)
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':trans_id', $transId);
            $stmt->bindParam(':card_num', $cardNum);
            $stmt->bindParam(':cvv', $cvv);
            $stmt->bindParam(':expiry', $expiry);
            $stmt->bindParam(':total_price', $totalPrice);
            $stmt->bindParam(':fullName', $fullName);
    
            // Execute the payment insertion
            $stmt->execute();
    
            // Commit the transaction if all insertions were successful
            $this->pdo->commit();


    
            // Return success message
            return true;
        } catch (Exception $e) {
            // If an error occurs, rollback the transaction
            $this->pdo->rollBack();
    
            // Return failure message
            return false;
        }
    }    
    

}
?>

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

include_once 'model/userModel.php';
include_once 'model/profileModel.php';

class UserDAO {
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
    }



    /**
     * Updates null fields in the Profile table for a specific user.
     * @param Profile $profile - The ProfileModel containing updated data
     * @return bool - Returns true if the update was successful, otherwise false
     */
    public function updateProfileFields(Profile $profile) {
        try {
            // Query to update only null fields in the Profile table
            $updateQuery = "
                UPDATE Profile
                SET 
                    firstName = COALESCE(firstName, :firstName),
                    lastName = COALESCE(lastName, :lastName),
                    age = COALESCE(age, :age),
                    street = COALESCE(street, :street),
                    city = COALESCE(city, :city),
                    province = COALESCE(province, :province),
                    postal = COALESCE(postal, :postal),
                    card_num = COALESCE(card_num, :cardNum),
                    cvv = COALESCE(cvv, :cvv),
                    expiry = COALESCE(expiry, :expiry)
                WHERE userName = :userName
            ";
            $stmt = $this->pdo->prepare($updateQuery);

            // Bind parameters
            $stmt->bindParam(':firstName', $profile->getFirstName());
            $stmt->bindParam(':lastName', $profile->getLastName());
            $stmt->bindParam(':age', $profile->getAge());
            $stmt->bindParam(':street', $profile->getStreet());
            $stmt->bindParam(':city', $profile->getCity());
            $stmt->bindParam(':province', $profile->getProvince());
            $stmt->bindParam(':postal', $profile->getPostal());
            $stmt->bindParam(':cardNum', $profile->getCardNum());
            $stmt->bindParam(':cvv', $profile->getCvv());
            $stmt->bindParam(':expiry', $profile->getExpiry());
            $stmt->bindParam(':userName', $profile->getUserName());

            return $stmt->execute();

        } catch (PDOException $e) {
            // Handle exceptions (log error, rethrow, etc.)
            echo "Error updating profile: " . $e->getMessage();
            return false;
        }
    }
}
?>

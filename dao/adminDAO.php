<?php

include_once '../model/paymentModel.php';
include_once '../model/userModel.php';
include_once '../model/transactionModel.php';
include_once '../model/profileModel.php';
include_once '../model/inventoryModel.php';
include_once '../model/itemModel.php';

class AdminDAO {
    private $pdo;

    /**
     * Constructor
     * @param PDO $pdo - A PDO connection to the database
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Retrieves all payments from the Payment table, including the associated transactions for each payment.
     * @return array - An array of PaymentModel objects, each containing an array of TransactionModel objects
     */
    public function getSalesHistory() {
        try {
            $salesHistory = [];

            // Query to get all payments from the Payment table
            $paymentQuery = "SELECT * FROM Payment";
            $stmt = $this->pdo->prepare($paymentQuery);
            $stmt->execute();
            $paymentRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($paymentRows as $paymentRow) {
                // Create a PaymentModel for each payment
                $payment = new Payment(
                    $paymentRow['trans_id'],
                    $paymentRow['card_num'],
                    $paymentRow['cvv'],
                    $paymentRow['expiry'],
                    $paymentRow['total_price'],
                    $paymentRow['processed'],
                    $paymentRow['date']
                );

                // Query to get transactions related to this payment
                $transactionQuery = "SELECT * FROM Transaction WHERE trans_id = :trans_id";
                $stmt = $this->pdo->prepare($transactionQuery);
                $stmt->bindParam(':trans_id', $paymentRow['trans_id']);
                $stmt->execute();
                $transactionRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $transactions = new Transaction();
                foreach ($transactionRows as $transactionRow) {
                    // Create a TransactionModel for each transaction
                    $transaction = new TransactionModel(
                        $transactionRow['trans_id'],
                        $transactionRow['item_id'],
                        $transactionRow['userName'],
                        $transactionRow['quantity'],
                        $transactionRow['date']
                    );
                    $transactions.addItem($transaction);
                }

                // Set the array of transactions in the PaymentModel
                $payment->setTransactions($transactions);

                // Add the PaymentModel to the sales history
                $salesHistory[] = $payment;
            }

            return $salesHistory;

        } catch (PDOException $e) {
            // Handle exceptions (log error, rethrow, etc.)
            echo "Error retrieving sales history: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Retrieves all users and their associated profiles from the User and Profile tables.
     * @return array - An array of UserModel objects, each containing a ProfileModel
     */
    public function getAllUsers() {
        try {
            $usersWithProfiles = [];

            // Query to get all users from the User table
            $userQuery = "SELECT * FROM User";
            $stmt = $this->pdo->prepare($userQuery);
            $stmt->execute();
            $userRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($userRows as $userRow) {
                // Create a UserModel for each user
                $user = new User(
                    $userRow['userName'],
                    $userRow['password'],
                    $userRow['salt'],
                    $userRow['last_logon'],
                    $userRow['admin']
                );

                // Query to get the profile data for the user
                $profileQuery = "SELECT * FROM Profile WHERE userName = :userName";
                $stmt = $this->pdo->prepare($profileQuery);
                $stmt->bindParam(':userName', $userRow['userName']);
                $stmt->execute();
                $profileRow = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($profileRow) {
                    // Create a ProfileModel and associate it with the UserModel
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

                // Add the UserModel to the users array
                $usersWithProfiles[] = $user;
            }

            return $usersWithProfiles;

        } catch (PDOException $e) {
            // Handle exceptions (log error, rethrow, etc.)
            echo "Error retrieving users with profiles: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Verifies if a user is an admin.
     *
     * @param userName $userName The username to check.
     * @return bool True if the user is an admin, false otherwise.
     */
    public function isAdmin($userName){
        // SQL query to check if the user is an admin
        $query = "SELECT admin FROM User WHERE userName = :userName LIMIT 1";

        // Prepare and execute the query
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userName', $userName, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return true if admin field is 1, false otherwise
        return $result && $result['admin'] == 1;
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
     * Deletes an Item from inventory.
     * 
     * @param string $itemid - The id of the item
     * @return bool - Returns true if the item was deleted successfully
     */
    //TODO: Connect to the database via the pdo defined above, delete the item from Inventory table where the entry has the specified itemid.
    //Assigned-to: Rasengan
    public function deleteItem($itemid){

        return true;
    }

   /**
    * Adds an item to the inventory.
    * 
    * @param Item $item data to be added to inventory.
    * 
    * @return bool Returns true if the item was added successfully.
    * assignedTo: Hiraku
    */
    public function addItem(Item $item){
        return true;
    }

    /**
     * Deletes Customer records with associated username.
     * 
     * @param string $userName - The username of the customer
     * @return bool - Returns true if the customer is deleted successfully
     */
    //TODO: Connect to the database using pdo defined above, and perform the task to follow the same convention as the other methods
    // Keep in mind, you must delete all entries from all the tables having an association with this username
    //Assigned-to: Rasengan
    public function deleteCustomerRecords($userName){

    }

    /**
     * Updates customer records with the provided information.
     *
     * @param Profile - containing details of customer profile to be updated
     *
     * @return bool - if customer informationupdated successfully
     * TODO:// 
     * assigned-To: Hiraku
     */
    public function updateCustomerRecords(Profile $profile){

        return true;
    }
}
?>

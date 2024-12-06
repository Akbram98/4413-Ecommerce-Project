<?php
include_once '../model/profileModel.php';
/**
 * Interface UserDAO
 *
 * Defines the contract for data access operations related to users and their profiles.
 */
interface UserDAO {
    /**
     * Retrieves a user by userName and populates it with its associated ProfileModel.
     * @param string $userName - The userName of the user to retrieve
     * @return User|null - Returns a User if found, otherwise null
     */
    public function getUserByUserName($userName);

    /**
     * Validates a user's login credentials.
     * @param string $userName - The username provided by the user
     * @param string $password - The password provided by the user
     * @return bool - Returns true if login is successful, false otherwise
     */
    public function validateUser($userName, $password);

    /**
     * Updates the last_logon field for a user.
     * @param string $userName - The username to update
     * @return bool - Returns true if the update was successful, false otherwise
     */
    public function updateLastLogon($userName);

    /**
     * Registers a new user and creates an associated profile.
     * @param string $userName - The username to register
     * @param string $password - The password to register
     * @param string $firstName - The first name of the user
     * @param string $lastName - The last name of the user
     * @return bool - Returns true if registration is successful, false otherwise
     */
    public function registerUser($userName, $password, $firstName, $lastName);

    /**
     * Fetches the purchase history for a given user.
     * @param string $userName - The username whose history is retrieved
     * @return array - An array of Transaction objects
     */
   // public function getPurchaseHistory($userName);

    /**
     * Updates null fields in the profile for a specific user.
     * @param Profile $profile - The ProfileModel containing updated data
     * @return bool - Returns true if the update was successful, otherwise false
     */
    public function updateProfileFields(Profile $profile);

     /**
     * Retrieves a list of transactions for a specific user and groups them by transaction ID.
     * 
     * @param string $userName The username to fetch transactions for.
     * @param string $cardNum The credit card number associated with the payment.
     * @param string $cvv The CVV associated with the payment.
     * @param string $expiry The expiration date of the credit card.
     * @param string $fullName The full name associated with the payment.
     * @return array An array of Payment objects, each containing related transactions.
     */
    public function getUserTransactions($userName, $cardNum, $cvv, $expiry, $fullName);

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
    public function addUserTransaction(Payment $payment);
}
?>

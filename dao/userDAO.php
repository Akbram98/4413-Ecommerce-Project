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
    public function getPurchaseHistory($userName);

    /**
     * Updates null fields in the profile for a specific user.
     * @param Profile $profile - The ProfileModel containing updated data
     * @return bool - Returns true if the update was successful, otherwise false
     */
    public function updateProfileFields(Profile $profile);
}
?>

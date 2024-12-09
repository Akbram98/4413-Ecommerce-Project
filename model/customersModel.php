<?php
include_once 'userModel.php';
/**
 * Class Customers
 *
 * Represents a collection of customers, where each customer is a User 
 * associated with a Profile. The class implements the JsonSerializable 
 * interface to allow serialization of the customers and their profiles into 
 * a JSON-compatible format.
 *
 */
class Customers {
    private $users; // Array of User objects

    // Constructor
    public function __construct($users = []) {
        $this->users = $users;
    }

    // Add a single User to the customers array
    public function addUser(User $user) {
        $this->users[] = $user;
    }

    // Get all users in the customers array
    public function getUsers() {
        return $this->users;
    }

    public function toJson(){
        $usersJson = [];

    // Loop through the array of User objects
        foreach ($this->users as $user) {
            // Initialize the profile data array
            $profileData = [];

            // Manually build the profile JSON object, checking for null values before adding
            if ($user->getProfile()) {
                $profile = $user->getProfile();

                // Add profile fields only if they are not null
                if ($profile->getUserName() !== null) {
                    $profileData["userName"] = $profile->getUserName();
                }
                if ($profile->getFirstName() !== null) {
                    $profileData["firstName"] = $profile->getFirstName();
                }
                if ($profile->getLastName() !== null) {
                    $profileData["lastName"] = $profile->getLastName();
                }
                if ($profile->getAge() !== null) {
                    $profileData["age"] = $profile->getAge();
                }
                if ($profile->getStreet() !== null) {
                    $profileData["street"] = $profile->getStreet();
                }
                if ($profile->getCity() !== null) {
                    $profileData["city"] = $profile->getCity();
                }
                if ($profile->getProvince() !== null) {
                    $profileData["province"] = $profile->getProvince();
                }
                if ($profile->getPostal() !== null) {
                    $profileData["postal"] = $profile->getPostal();
                }
                if ($profile->getCardNum() !== null) {
                    $profileData["cardNum"] = $profile->getCardNum();
                }
                if ($profile->getCvv() !== null) {
                    $profileData["cvv"] = $profile->getCvv();
                }
                if ($profile->getExpiry() !== null) {
                    $profileData["expiry"] = $profile->getExpiry();
                }
            }

            // Manually build the user JSON object
            $userJson = [
                "userName" => $user->getUserName(),  // Call the function to get the userName
                "password" => $user->getPassword(),  // Call the function to get the password
                "salt" => $user->getSalt(),          // Call the function to get the salt
                "lastLogon" => $user->getLastLogon(),// Call the function to get lastLogon
                "profile" => $profileData            // Add the profile data (only fields that are not null)
            ];

            // Add the manually created JSON object to the usersJson array
            $usersJson[] = $userJson;
        }
       
        return $usersJson;
    }
}
?>

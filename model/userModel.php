<?php
/**
 * Class User
 *
 * Represents a user in the system, containing authentication details and
 * a reference to their associated profile information.
 *
 * Fields:
 * - userName (string): The unique username of the user (primary key).
 * - password (string): The hashed password for user authentication.
 * - salt (string): The salt used for hashing the password.
 * - lastLogon (string|null): The timestamp of the user's last login.
 * - admin (bool): A flag indicating whether the user is an administrator.
 * - profile (ProfileModel): The profile object associated with the user.
 *
 * Methods:
 * - Constructor: Initializes the user with optional values, including a profile object.
 * - Getters and setters for all properties to access and modify user data.
 * - Integration with ProfileModel for accessing and modifying related profile information.
 */

include_once 'profileModel.php';

class User {
    private $userName; 
    private $password;
    private $salt;
    private $lastLogon;
    private $admin;
    private $profile; // This will hold the users profile

    // Constructor
    public function __construct($userName = null, $password = null, $salt = null, $lastLogon = null, $admin = false, $profile = null) {
        $this->userName = $userName;
        $this->password = $password;
        $this->salt = $salt;
        $this->lastLogon = $lastLogon;
        $this->admin = $admin;
        $this->profile = new Profile();
    }

    // Getters and Setters
    public function getUserName() {
        return $this->userName;
    }

    public function setUserName($userName) {
        $this->userName = $userName;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getSalt() {
        return $this->salt;
    }

    public function setSalt($salt) {
        $this->salt = $salt;
    }

    public function getLastLogon() {
        return $this->lastLogon;
    }

    public function setLastLogon($lastLogon) {
        $this->lastLogon = $lastLogon;
    }

    public function isAdmin() {
        return $this->admin;
    }

    public function getProfile() {
        return $this->profile;
    }

    public function setProfile($profile) {
        if ($profile instanceof Profile) { // checks if its a valid profile object
            $this->profile = $profile;
        } else {
            throw new Exception('Invalid ProfileModel object');
        }
    }
}
?>

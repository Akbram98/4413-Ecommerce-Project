<?php
/**
 * Class Profile
 *
 * Represents the profile information associated with a user. 
 * This includes personal details such as name, address, payment details, 
 * and optional fields like age.
 *
 * Fields:
 * - userName (string): The username associated with the profile (foreign key, NOT NULL).
 * - firstName (string): The user's first name (NOT NULL).
 * - lastName (string): The user's last name (NOT NULL).
 * - age (int|null): The user's age.
 * - street (string|null): The user's street address.
 * - city (string|null): The user's city of residence.
 * - province (string|null): The user's province or state.
 * - postal (string|null): The user's postal or ZIP code.
 * - cardNum (string|null): The user's credit card number.
 * - cvv (string|null): The CVV for the user's credit card.
 * - expiry (string|null): The expiration date of the user's credit card.
 *
 * Methods:
 * - Constructor: Initializes the profile with optional values.
 * - Getters and setters for all properties to access and modify profile data.
 */

class Profile implements JsonSerializable {
    private $userName;   // Foreign key, also NOT NULL
    private $firstName;  // NOT NULL
    private $lastName;   // NOT NULL
    private $age;
    private $street;
    private $city;
    private $province;
    private $postal;
    private $cardNum;
    private $cvv;
    private $expiry;

    // Constructor
    public function __construct(
        $userName = null,
        $firstName = null,
        $lastName = null,
        $age = null,
        $street = null,
        $city = null,
        $province = null,
        $postal = null,
        $cardNum = null,
        $cvv = null,
        $expiry = null
    ) {
        $this->userName = $userName;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->age = $age;
        $this->street = $street;
        $this->city = $city;
        $this->province = $province;
        $this->postal = $postal;
        $this->cardNum = $cardNum;
        $this->cvv = $cvv;
        $this->expiry = $expiry;
    }

    // Getters and Setters

    public function getUserName() {
        return $this->userName;
    }

    public function setUserName($userName) {
        $this->userName = $userName;
    }

    public function getFirstName() {
        return $this->firstName;
    }

    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    public function getAge() {
        return $this->age;
    }

    public function setAge($age) {
        $this->age = $age;
    }

    public function getStreet() {
        return $this->street;
    }

    public function setStreet($street) {
        $this->street = $street;
    }

    public function getCity() {
        return $this->city;
    }

    public function setCity($city) {
        $this->city = $city;
    }

    public function getProvince() {
        return $this->province;
    }

    public function setProvince($province) {
        $this->province = $province;
    }

    public function getPostal() {
        return $this->postal;
    }

    public function setPostal($postal) {
        $this->postal = $postal;
    }

    public function getCardNum() {
        return $this->cardNum;
    }

    public function setCardNum($cardNum) {
        $this->cardNum = $cardNum;
    }

    public function getCvv() {
        return $this->cvv;
    }

    public function setCvv($cvv) {
        $this->cvv = $cvv;
    }

    public function getExpiry() {
        return $this->expiry;
    }

    public function setExpiry($expiry) {
        $this->expiry = $expiry;
    }

     // Implement the JsonSerializable interface method
     public function jsonSerialize() {
        $data = [
            'userName' => $this->userName,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'age' => $this->age,
            'street' => $this->street,
            'city' => $this->city,
            'province' => $this->province,
            'postal' => $this->postal,
            'cardNum' => $this->cardNum,
            'cvv' => $this->cvv,
            'expiry' => $this->expiry
        ];

        // Remove any null fields
        return array_filter($data, function ($value) {
            return !is_null($value);
        });
    }
}

?>

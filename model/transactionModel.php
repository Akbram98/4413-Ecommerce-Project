<?php
/**
 * Class Transaction
 *
 * Represents a transaction in the system, linking users, items, and purchase details.
 *
 * Fields:
 * - transId (int): The unique ID of the transaction (primary key, auto-incremented).
 * - itemId (int): The ID of the item involved in the transaction (foreign key referencing Inventory).
 * - userName (string): The username of the user making the transaction (foreign key referencing User).
 * - quantity (int): The quantity of items purchased in the transaction.
 * - date (string): The date and time when the transaction occurred.
 *
 * Methods:
 * - Constructor: Initializes the transaction with optional values.
 * - Getters and setters for all properties to access and modify transaction data.
 */

class Transaction {
    private $transId;   
    private $itemId;    
    private $userName;
    private $quantity;
    private $date;

    // Constructor
    public function __construct($transId = null, $itemId = null, $userName = null, $quantity = null, $date = null) {
        $this->transId = $transId;
        $this->itemId = $itemId;
        $this->userName = $userName;
        $this->quantity = $quantity;
        $this->date = $date;
    }

    // Getters and Setters

    public function getTransId() {
        return $this->transId;
    }

    public function getItemId() {
        return $this->itemId;
    }

    public function getUserName() {
        return $this->userName;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
    }
}
?>

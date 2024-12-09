<?php
/**
 * Class Payment
 *
 * Represents a payment in the system, including payment details and 
 * an array of associated transactions.
 *
 * Fields:
 * - transId (int): The transaction ID (foreign key referencing Transaction and primary key).
 * - cardNum (string): The credit card number used for the payment.
 * - cvv (string): The CVV of the credit card.
 * - expiry (string): The expiration date of the credit card.
 * - totalPrice (float): The total amount for the payment.
 * - processed (bool): Indicates whether the payment has been processed.
 * - date (string): The date and time of the payment.
 * - transactions (array): An array of Transaction objects representing related transactions.
 *
 * Methods:
 * - Constructor: Initializes payment properties and optional transactions.
 * - Getters and setters for all fields.
 * - `addTransaction`: Adds a single Transaction object to the transactions array.
 */

include_once 'transactionModel.php';

class Payment implements JsonSerializable {
    private $transId;         
    private $cardNum;
    private $cvv;
    private $expiry;
    private $totalPrice;
    private $processed;
    private $date;
    private $transactions;    // Array of Transactions
    private $fullName = null;

    // Constructor
    public function __construct(
        $transId = null,
        $cardNum = null,
        $cvv = null,
        $expiry = null,
        $totalPrice = 0.00,
        $processed = null,
        $date = null,
        $transactions = []
    ) {
        $this->transId = $transId;
        $this->cardNum = $cardNum;
        $this->cvv = $cvv;
        $this->expiry = $expiry;
        $this->totalPrice = $totalPrice;
        $this->processed = $processed;
        $this->date = $date;
        $this->transactions = $transactions;
    }

    // Getters and Setters

    public function getTransId() {
        return $this->transId;
    }

    public function setFullName($fullName){
        $this->fullName = $fullName;
    }

    public function getFullName(){
        return $this->fullName;
    }
    
    public function setTransId($transId) {
        $this->transId = $transId;
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

    public function getTotalPrice() {
        return $this->totalPrice;
    }

    public function setTotalPrice($totalPrice) {
        $this->totalPrice = $totalPrice;
    }

    public function calculateTotalPrice() {
        $totalPrice = 0.0;
    
        // Loop through each transaction and calculate the total price
        foreach ($this->transactions as $transaction) {
            // Assuming you have access to an Item class to get the price of the item
            $itemPrice = $this->getItemPrice($transaction->getItemId()); // Method to fetch the price of the item by item_id
            $totalPrice += transaction->getItemPrice() * $transaction->getQuantity();
        }

        $this->totalPrice = $totalPrice;
    }

    public function getProcessed() {
        return $this->processed;
    }

    public function setProcessed($processed) {
        $this->processed = $processed;
    }

    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function getTransactions() {
        return $this->transactions;
    }

    public function setTransactions(array $transactions) {
        $this->transactions = $transactions;
    }

    // Add a single TransactionModel to the transactions array
    public function addTransaction(Transaction $transaction) {
        $this->transactions[] = $transaction;
    }

    // Implement jsonSerialize to define the JSON structure
    public function jsonSerialize() {
        return [
            "transId" => $this->transId,
            "cardNum" => $this->cardNum,
            "cvv" => $this->cvv,
            "expiry" => $this->expiry,
            "totalPrice" => $this->totalPrice,
            "processed" => $this->processed,
            "date" => $this->date,
            "transactions" => array_map(function($transaction) {
                return $transaction->jsonSerialize(); // Assuming Transaction model is JsonSerializable
            }, $this->transactions)
        ];
    }

}
?>

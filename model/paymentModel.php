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

class Payment {
    private $transId;         
    private $cardNum;
    private $cvv;
    private $expiry;
    private $totalPrice;
    private $processed;
    private $date;
    private $transactions;    // Array of Transactions

    // Constructor
    public function __construct(
        $transId = null,
        $cardNum = null,
        $cvv = null,
        $expiry = null,
        $totalPrice = null,
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
}
?>
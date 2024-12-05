<?php
include_once '../model/itemModel.php';
include_once '../model/profileModel.php';

interface AdminDAO {
    /**
     * Retrieves all payments from the Payment table, including the associated transactions for each payment.
     * @return array - An array of PaymentModel objects, each containing an array of TransactionModel objects
     */
    public function getSalesHistory();

    /**
     * Retrieves all users and their associated profiles from the User and Profile tables.
     * @return array - An array of UserModel objects, each containing a ProfileModel
     */
    public function getAllUsers();

    /**
     * Verifies if a user is an admin.
     * 
     * @param string $userName - The username to check.
     * @return bool - True if the user is an admin, false otherwise.
     */
    public function isAdmin($userName);

    /**
     * Updates the last_logon field to the current timestamp when the user logs in.
     * 
     * @param string $userName - The username whose last_logon needs to be updated.
     * @return bool - Returns true if the last_logon field was updated successfully, false otherwise.
     */
    public function updateLastLogon($userName);

    /**
     * Deletes an item from inventory.
     * 
     * @param string $itemId - The ID of the item.
     * @return bool - Returns true if the item was deleted successfully.
     */
    public function deleteItem($itemId);

    /**
     * Adds an item to the inventory.
     * 
     * @param Item $item - The data to be added to inventory.
     * @return bool - Returns true if the item was added successfully.
     */
    public function addItem(Item $item);

    /**
     * Deletes customer records associated with the given username.
     * 
     * @param string $userName - The username of the customer.
     * @return bool - Returns true if the customer was deleted successfully.
     */
    public function deleteCustomerRecords($userName);

    /**
     * Updates customer records with the provided information.
     * 
     * @param Profile $profile - The profile containing customer details to be updated.
     * @return bool - Returns true if the customer information was updated successfully.
     */
    public function updateCustomerRecords(Profile $profile);
}
?>

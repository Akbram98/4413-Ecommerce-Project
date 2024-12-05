<?php

interface ItemDAO {
    /**
     * Retrieves all items from the Inventory table.
     * 
     * @return array - An array of Inventory objects.
     */
    public function getAllItems();

    /**
     * Retrieves an item from the Inventory table by its ID.
     * 
     * @param string $itemid - The ID of the item to retrieve.
     * @return Item|null - Returns an Item object if found, null otherwise.
     */
    public function getItemById($itemid);

    /**
     * Updates the inventory quantity of a specific item.
     * 
     * @param string $itemid - The ID of the item to update.
     * @param int $amount - The new quantity to set in inventory.
     * @return bool - Returns true if the inventory was successfully updated.
     */
    public function updateItemInventory($itemid, $amount);
}

?>

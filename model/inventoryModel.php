<?php
/**
 * Class Inventory
 *
 * Represents the inventory as a collection of items.
 *
 * Fields:
 * - items (array): An array of ItemModel objects representing the inventory.
 *
 * Methods:
 * - __construct: Initializes the inventory with an optional array of items.
 * - addItem: Adds a single Item object to the inventory.
 * - getAllItems: Retrieves all Item objects in the inventory.
 */

include_once 'itemModel.php';

class Inventory implements JsonSerializable {
    private $items; // Array of Item objects

    // Constructor
    public function __construct($items = []) {
        $this->items = $items;
    }

    // Add a single item to the inventory
    public function addItem(Item $item) {
        $this->items[] = $item;
    }

    // Get all items in the inventory
    public function getItems() {
        return $this->items;
    }

    // Implement jsonSerialize to define the JSON structure
    public function jsonSerialize() {
        // Convert each Item object to JSON (assuming Item class is JsonSerializable)
        return array_map(function($item) {
            return $item->jsonSerialize(); // Calling jsonSerialize method on each Item object
        }, $this->items);
    }
}
?>

<?php
/**
 * Class Item
 *
 * Represents an item in the inventory, mapped to the Inventory table.
 *
 * Fields:
 * - itemId (int): The unique ID of the item (primary key).
 * - name (string): The name of the item.
 * - price (float): The price of the item.
 * - description (string): A detailed description of the item.
 * - brand (string): The brand of the item.
 * - date (string): The date when the item was added to the inventory.
 * - quantity (int): The available stock quantity of the item.
 * - image (string): The path or URL to the image representing the item.
 *
 * Methods:
 * - Constructor: Initializes the item properties.
 * - Getters and setters for each property to access and modify item data.
 */

class Item {
    private $itemId;
    private $name;
    private $price;
    private $description;
    private $brand;
    private $date;
    private $quantity;
    private $image;

    // Constructor
    public function __construct(
        $itemId = null,
        $name = null,
        $price = null,
        $description = null,
        $brand = null,
        $date = null,
        $quantity = null,
        $image = null
    ) {
        $this->itemId = $itemId;
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
        $this->brand = $brand;
        $this->date = $date;
        $this->quantity = $quantity;
        $this->image = $image;
    }

    // Getters and Setters

    public function getItemId() {
        return $this->itemId;
    }

    public function setItemId($itemId) {
        $this->itemId = $itemId;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getPrice() {
        return $this->price;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getBrand() {
        return $this->brand;
    }

    public function setBrand($brand) {
        $this->brand = $brand;
    }

    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    public function getImage() {
        return $this->image;
    }

    public function setImage($image) {
        $this->image = $image;
    }
}
?>

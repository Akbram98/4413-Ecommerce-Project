<?php
include_once 'itemDAO.php';
include_once '../model/inventoryModel.php';
include_once '../model/itemModel.php';

class ItemDAOImpl implements ItemDAO {

    private $pdo;

    /**
     * Constructor
     * @param PDO $pdo - A PDO connection to the database
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }


     /**
     * Retrieves all items from the Inventory table using the Inventory class.
     * @return array - An array of Inventory objects
     */
    public function getAllItems() {
        try {
            $inventory = new Inventory();

            // Query to get all items from the Inventory table
            $inventoryQuery = "SELECT * FROM Inventory";
            $stmt = $this->pdo->prepare($inventoryQuery);
            $stmt->execute();
            $inventoryRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($inventoryRows as $itemRow) {
                // Create an Inventory object for each item
                $item = new Item(
                    $itemRow['item_id'],
                    $itemRow['name'],
                    $itemRow['price'],
                    $itemRow['description'],
                    $itemRow['brand'],
                    $itemRow['date'],
                    $itemRow['quantity'],
                    $itemRow['image']
                );

                // Add the Inventory object to the items array
                $inventory->addItem($item);
            }

            return $inventory;

        } catch (PDOException $e) {
            // Handle exceptions (log error, rethrow, etc.)
            echo "Error retrieving items from inventory: " . $e->getMessage();
            return [];
        }
    }


     /**
     * Retrieves all items from the Inventory table using the Inventory class.
     * @return array - An array of Inventory objects
     */
    public function getItemById($itemid) {
        try {
            $item = null;

            // Query to get all items from the Inventory table
            $query = "SELECT * FROM Inventory WHERE item_id = :itemid";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':itemid', $itemid);
            $stmt->execute();
            $itemRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if($itemRow) {
                // Create an Inventory object for each item
                $item = new Item(
                    $itemRow['item_id'],
                    $itemRow['name'],
                    $itemRow['price'],
                    $itemRow['description'],
                    $itemRow['brand'],
                    $itemRow['date'],
                    $itemRow['quantity'],
                    $itemRow['image']
                );
            }

            return $item;

        } catch (PDOException $e) {
            // Handle exceptions (log error, rethrow, etc.)
            echo "Error retrieving items from inventory: " . $e->getMessage();
            return [];
        }
    }


    /**
     * Updates Item inventory.
     * 
     * @param string $itemid - The id of the item
     * @param int $amount - the amount of items in stock, to be updated in inventory
     * @return bool - Returns true if  item associated with the itemid is updated
     */
    //TODO: Connect to the database using pdo defined above, and perform the task to follow the same convention as the other methods
    //Assigned-to: Rasengan
    public function updateItemInventory($itemid, $amount){
        
        //try to update the amount of the item at $itemid to the requested $amount
        try{
            

            $query = "UPDATE Inventory SET quantity = :amount WHERE item_id = :itemid";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':itemid', $itemid);
            
            //execute query if true return true else false
            if($stmt->execute()){

                return true;
            }else{
                return false;
            }

            

            
        //catch block 
        }catch(PDOException $e){
            echo "Item inventory was not updated: " . $e->getMessage();
            return false;
        }

        
    }

}

?>
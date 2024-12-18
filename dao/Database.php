<?php
/**
 * Database Class
 *
 * This class is responsible for establishing and managing a connection to the MySQL database
 * using PDO (PHP Data Objects). It initializes the connection with the provided database 
 * credentials and returns the PDO connection object for interacting with the database.
 *
 * It also handles errors by setting the PDO error mode to exceptions, ensuring better error 
 * handling during database operations.
 *
 * Usage:
 * $db = new Database();
 * $conn = $db->getConnection();  // Returns the PDO connection object
 *
 */

class Database {
    private $host = 'localhost';    // Database host (usually 'localhost')
    private $db_name = 'eecs4413';  // Database name
    private $username = 'root';     // Database username
    private $password = '';         // Database password
    private $conn = null;           // Database connection object

    /**
     * Establishes and returns a PDO database connection.
     *
     * This method checks if a connection to the database already exists. If not, it establishes 
     * a new connection using the configured database credentials. It uses PDO for database access 
     * and sets the error mode to exceptions to handle potential connection issues.
     *
     * @return PDO The PDO connection object to interact with the database.
     */
    public function getConnection() {
        // Check if connection already exists
        if ($this->conn === null) {
            try {
                // Set the DSN (Data Source Name)
                $dsn = "mysql:host={$this->host};dbname={$this->db_name}";
                
                // Create a new PDO instance
                $this->conn = new PDO($dsn, $this->username, $this->password);
                
                // Set PDO error mode to exception for better error handling
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Optional: set character encoding to UTF-8
                $this->conn->exec("SET NAMES 'utf8'");

            } catch (PDOException $e) {
                // If an error occurs during connection, show the error message
                echo "Connection failed: " . $e->getMessage();
            }
        }

        // Return the connection object
        return $this->conn;
    }
}

?>

<?php

// Classe pour gérer la connexion à la base de données
class Connection {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "location"; 
    public $conn;

    public function __construct() {
        $this->conn = new mysqli($this->servername, $this->username, $this->password);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        $this->createDatabase($this->dbname);
        
        $this->conn->select_db($this->dbname);
    }

    public function createDatabase($dbName) {
        $stmt = $this->conn->prepare("CREATE DATABASE IF NOT EXISTS `$dbName`");
        $stmt->execute();
        $stmt->close();
        $this->conn->select_db($dbName);
    }

    public function createTable($query) {
        if ($this->conn->query($query) === TRUE) {
            echo "Table created successfully<br>";
        } else {
            echo "Error creating table: " . $this->conn->error . "<br>";
        }
    }

    public function closeConnection() {
        $this->conn->close();
    }
}
?>
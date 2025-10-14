<?php
require_once('db_credentials.php');

class Database
{
    private $server = DB_SERVER;
    private $username = DB_USERNAME;
    private $password = DB_PASSWORD;
    private $dbname = DB_NAME;
    public $connection;

    // When class is created, automatically connect & prepare DB
    public function __construct()
    {
        $this->connectServer();
        $this->createDatabase();
        $this->connectDatabase();
        $this->createTable();
    }

    // Step 1: Connect to MySQL server (no DB yet)
    private function connectServer()
    {
        $this->connection = new mysqli($this->server, $this->username, $this->password);
        if ($this->connection->connect_error) {
            die("Server connection failed: " . $this->connection->connect_error);
        }
    }

    // Step 2: Create the database if not exists
    private function createDatabase()
    {
        $sql = "CREATE DATABASE IF NOT EXISTS " . $this->dbname;
        if (!$this->connection->query($sql)) {
            die("Failed to create database: " . $this->connection->error);
        }
    }

    // Step 3: Connect to that database
    private function connectDatabase()
    {
        $this->connection->select_db($this->dbname);
    }

    // Step 4: Create the main table
    private function createTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS restaurants (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255),
            email VARCHAR(255),
            ph VARCHAR(20),
            fssai VARCHAR(50),
            state VARCHAR(100),
            area VARCHAR(100),
            pincode VARCHAR(10),
            website VARCHAR(255),
            tags VARCHAR(255)
        )";
        if (!$this->connection->query($sql)) {
            die("Failed to create table: " . $this->connection->error);
        }
    }

    // ✅ General-purpose query function
    public function query($sql)
    {
        $result = $this->connection->query($sql);
        if (!$result) {
            die("Query failed: " . $this->connection->error);
        }
        return $result;
    }

    // ✅ Close connection cleanly
    public function close()
    {
        if (isset($this->connection)) {
            $this->connection->close();
        }
    }
}

// Auto-create one shared instance
$db = new Database();

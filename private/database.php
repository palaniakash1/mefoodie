<?php
require_once('db_credentials.php');

// OOP Database class
class Database
{
    private $server = DB_SERVER;
    private $username = DB_USERNAME;
    private $password = DB_PASSWORD;
    private $dbname = DB_NAME;
    public $connection;

    public function __construct()
    {
        $this->connectServer();
        $this->createDatabase();
        $this->connectDatabase();
        $this->createTable();
    }

    // Step 1: Connect to MySQL server
    private function connectServer()
    {
        $this->connection = new mysqli($this->server, $this->username, $this->password);
        if ($this->connection->connect_error) {
            die("Server connection failed: " . $this->connection->connect_error);
        }
    }

    // Step 2: Create database if not exists
    private function createDatabase()
    {
        $sql = "CREATE DATABASE IF NOT EXISTS " . $this->dbname;
        if (!$this->connection->query($sql)) {
            die("Failed to create database: " . $this->connection->error);
        }
    }

    // Step 3: Select the database
    private function connectDatabase()
    {
        $this->connection->select_db($this->dbname);
    }

    // Step 4: Create main table with new columns
    private function createTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS businesses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255),
            email VARCHAR(255),
            ph VARCHAR(20),
            fssai VARCHAR(50),
            state VARCHAR(100),
            city VARCHAR(100),
            district VARCHAR(100),
            pincode VARCHAR(10),
            website VARCHAR(255),
            tags JSON,
            status VARCHAR(255),
            latitude DECIMAL(10,8),
            longitude DECIMAL(11,8),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        if (!$this->connection->query($sql)) {
            die("Failed to create table: " . $this->connection->error);
        }
    }

    // General-purpose query function
    public function query($sql)
    {
        $result = $this->connection->query($sql);
        if (!$result) {
            die("Query failed: " . $this->connection->error);
        }
        return $result;
    }

    public function close()
    {
        if (isset($this->connection)) {
            $this->connection->close();
        }
    }

    // Fetch all businesses (admin)
    public function fetchAllbusinesses()
    {
        // $sql = "SELECT * FROM businesses ORDER BY id DESC";
        $sql = "SELECT * FROM businesses WHERE status != 'deleted' ORDER BY id DESC";
        $result = $this->connection->query($sql);
        if (!$result) {
            die("Database query failed: " . $this->connection->error);
        }

        $businesses = [];
        while ($row = $result->fetch_assoc()) {
            $row['tags'] = json_decode($row['tags'], true) ?? [];
            $businesses[] = $row;
        }
        return $businesses;
    }

    // Fetch only approved businesses
    public function fetchApprovedbusinesses()
    {
        $sql = "SELECT * FROM businesses WHERE status = 'approved' ORDER BY id DESC";
        $result = $this->connection->query($sql);
        if (!$result) {
            die("Database query failed: " . $this->connection->error);
        }

        $businesses = [];
        while ($row = $result->fetch_assoc()) {
            $row['tags'] = json_decode($row['tags'], true) ?? [];
            $businesses[] = $row;
        }

        return $businesses;
    }


    // Insert new business (called during registration)
    public function insertbusiness($data)
    {
        $stmt = $this->connection->prepare(
            "INSERT INTO businesses (name, email, ph, fssai, state, city, district, pincode, website, tags, status, latitude, longitude)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?)"
        );

        // Convert comma-separated tags → JSON array
        $tags = isset($data['tags'])
            ? json_encode(array_map('trim', explode(',', $data['tags'])))
            : json_encode([]);

        $stmt->bind_param(
            "ssssssssssdd",
            $data['name'],
            $data['email'],
            $data['ph'],
            $data['fssai'],
            $data['state'],
            $data['city'],
            $data['district'],
            $data['pincode'],
            $data['website'],
            $tags,
            $data['latitude'],
            $data['longitude']
        );

        if (!$stmt->execute()) {
            die("Insert failed: " . $stmt->error);
        }

        return $stmt->insert_id;
    }
}

// Global instance
$db = new Database();

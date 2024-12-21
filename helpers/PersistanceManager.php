<?php

/**
 * Project Name: Tooth Care - Channeling Appoinments
 * Author: Musab Ibn Siraj
 */

class PersistanceManager
{
    private $pdo;

    public function __construct()
    {
        try {
            $this->pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            // Create a tables if it doesn't exist
            $this->createTables();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function createTables()
    {
        // Users Table
        $query_users = "CREATE TABLE IF NOT EXISTS `users` (
            `UserID` INT AUTO_INCREMENT PRIMARY KEY,
            `Username` VARCHAR(50) NOT NULL UNIQUE,
            `Password` VARCHAR(255) NOT NULL,
            `Role` ENUM('Admin', 'Member', 'Guest') NOT NULL,
            `FullName` VARCHAR(100) NOT NULL,
            `Email` VARCHAR(100) UNIQUE,
            `ContactNumber` VARCHAR(15),
            `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `UpdatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $this->pdo->exec($query_users);

        // Categories Table
        $query_categories = "CREATE TABLE IF NOT EXISTS `categories` (
            `CategoryID` INT AUTO_INCREMENT PRIMARY KEY,
            `CategoryName` VARCHAR(50) NOT NULL UNIQUE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $this->pdo->exec($query_categories);

        // Books Table
        $query_books = "CREATE TABLE IF NOT EXISTS `books` (
            `BookID` INT AUTO_INCREMENT PRIMARY KEY,
            `Title` VARCHAR(200) NOT NULL,
            `Author` VARCHAR(100) NOT NULL,
            `CategoryID` INT NOT NULL,
            `ISBN` VARCHAR(20) NOT NULL UNIQUE,
            `Quantity` INT NOT NULL CHECK (Quantity >= 0),
            FOREIGN KEY (`CategoryID`) REFERENCES `categories`(`CategoryID`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $this->pdo->exec($query_books);

        // BorrowRecords Table
        $query_borrow_records = "CREATE TABLE IF NOT EXISTS `borrow_records` (
            `BorrowID` INT AUTO_INCREMENT PRIMARY KEY,
            `MemberID` INT NOT NULL,
            `BookID` INT NOT NULL,
            `BorrowDate` DATE NOT NULL,
            `DueDate` DATE NOT NULL,
            `ReturnDate` DATE,
            `Fine` DECIMAL(10, 2) DEFAULT 0.00,
            FOREIGN KEY (`MemberID`) REFERENCES `users`(`UserID`) ON DELETE CASCADE,
            FOREIGN KEY (`BookID`) REFERENCES `books`(`BookID`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $this->pdo->exec($query_borrow_records);

        // Fines Table
        $query_fines = "CREATE TABLE IF NOT EXISTS `fines` (
            `FineID` INT AUTO_INCREMENT PRIMARY KEY,
            `MemberID` INT NOT NULL,
            `FineAmount` DECIMAL(10, 2) NOT NULL,
            `PaymentStatus` ENUM('Paid', 'Unpaid') DEFAULT 'Unpaid',
            FOREIGN KEY (`MemberID`) REFERENCES `users`(`UserID`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $this->pdo->exec($query_fines);
    }

    public function getCount($query, $param = null)
    {
        $result = $this->executeQuery($query, $param, true);
        return $result['c'];
    }

    public function run($query, $param = null, $fetchFirstRecOnly = false)
    {
        return $this->executeQuery($query, $param, $fetchFirstRecOnly);
    }

    public function insertAndGetLastRowId($query, $param = null)
    {
        return $this->executeQuery($query, $param, true, true);
    }

    private function executeQuery($query, $param = null, $fetchFirstRecOnly = false, $getLastInsertedId = false)
    {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($param);

            if ($getLastInsertedId) {
                return $this->pdo->lastInsertId();
            }

            if ($fetchFirstRecOnly)
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            else
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


            $stmt->closeCursor();
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return -1;
        }
    }
}

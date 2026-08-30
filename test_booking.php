<?php
// Test trainer booking functionality
require_once 'crud/connect.php';

try {
    $conn = getConnection();
    echo "Database connection successful!\n";
    
    // Check if trainer_sessions table exists
    $result = $conn->query("DESCRIBE trainer_sessions");
    if ($result) {
        echo "trainer_sessions table structure:\n";
        while ($row = $result->fetch_assoc()) {
            echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        echo "trainer_sessions table does not exist\n";
    }
    
    // Check if trainers table exists and has data
    $result = $conn->query("SELECT id, name FROM trainers LIMIT 3");
    if ($result && $result->num_rows > 0) {
        echo "\nSample trainers:\n";
        while ($row = $result->fetch_assoc()) {
            echo "- ID: " . $row['id'] . ", Name: " . $row['name'] . "\n";
        }
    } else {
        echo "No trainers found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
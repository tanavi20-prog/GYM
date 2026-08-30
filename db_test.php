<?php
require_once 'crud/connect.php';

try {
    $conn = getConnection();
    echo "Database connection successful!\n";
    
    // Check if trainers table exists
    $result = $conn->query("SHOW TABLES LIKE 'trainers'");
    if ($result && $result->num_rows > 0) {
        echo "Trainers table exists\n";
    } else {
        echo "Trainers table does not exist\n";
    }
    
    // Check if trainer_sessions table exists
    $result = $conn->query("SHOW TABLES LIKE 'trainer_sessions'");
    if ($result && $result->num_rows > 0) {
        echo "Trainer sessions table exists\n";
    } else {
        echo "Trainer sessions table does not exist\n";
    }
    
    // Check if user_selected_trainers table exists
    $result = $conn->query("SHOW TABLES LIKE 'user_selected_trainers'");
    if ($result && $result->num_rows > 0) {
        echo "User selected trainers table exists\n";
    } else {
        echo "User selected trainers table does not exist\n";
    }
    
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}
?>
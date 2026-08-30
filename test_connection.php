<?php
// Test database connection and required functions
require_once 'crud/connect.php';

echo "Testing database connection...\n";

try {
    $conn = getConnection();
    echo "✓ Database connection successful\n";
    
    // Test if trainer_sessions table exists
    $result = $conn->query("SHOW TABLES LIKE 'trainer_sessions'");
    if ($result && $result->num_rows > 0) {
        echo "✓ trainer_sessions table exists\n";
    } else {
        echo "✗ trainer_sessions table does not exist\n";
    }
    
    // Test if trainers table exists
    $result = $conn->query("SHOW TABLES LIKE 'trainers'");
    if ($result && $result->num_rows > 0) {
        echo "✓ trainers table exists\n";
    } else {
        echo "✗ trainers table does not exist\n";
    }
    
    echo "All tests completed.\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>
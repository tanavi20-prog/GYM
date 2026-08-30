<?php
// Debug trainer booking functionality
require_once 'crud/connect.php';

echo "Debugging trainer booking functionality...\n";

try {
    $conn = getConnection();
    echo "✓ Database connection successful\n";
    
    // Check if trainer_sessions table exists
    $result = $conn->query("SHOW TABLES LIKE 'trainer_sessions'");
    if ($result && $result->num_rows > 0) {
        echo "✓ trainer_sessions table exists\n";
        
        // Check table structure
        $result = $conn->query("DESCRIBE trainer_sessions");
        echo "Table structure:\n";
        while ($row = $result->fetch_assoc()) {
            echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        echo "✗ trainer_sessions table does not exist\n";
    }
    
    // Check if trainers table exists and has data
    $result = $conn->query("SELECT COUNT(*) as count FROM trainers");
    $row = $result->fetch_assoc();
    echo "Found " . $row['count'] . " trainers in database\n";
    
    if ($row['count'] > 0) {
        // Get first trainer
        $result = $conn->query("SELECT id, name, hourly_rate, available FROM trainers LIMIT 1");
        $trainer = $result->fetch_assoc();
        echo "Sample trainer: ID=" . $trainer['id'] . ", Name=" . $trainer['name'] . 
             ", Rate=" . $trainer['hourly_rate'] . ", Available=" . ($trainer['available'] ? 'Yes' : 'No') . "\n";
    }
    
    echo "Debug completed.\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>
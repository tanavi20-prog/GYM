<?php
// Check database tables
require_once 'crud/connect.php';

echo "Checking database tables...\n";

try {
    $conn = getConnection();
    echo "✓ Database connection successful\n";
    
    // List all tables
    $result = $conn->query("SHOW TABLES");
    echo "Existing tables:\n";
    while ($row = $result->fetch_row()) {
        echo "  - " . $row[0] . "\n";
    }
    
    // Check specific tables
    $tables_to_check = ['trainers', 'trainer_sessions', 'users'];
    
    foreach ($tables_to_check as $table) {
        echo "\n--- Checking {$table} table ---\n";
        $result = $conn->query("SHOW TABLES LIKE '{$table}'");
        if ($result && $result->num_rows > 0) {
            echo "✓ Table exists\n";
            
            // Show structure
            $result = $conn->query("DESCRIBE {$table}");
            echo "Structure:\n";
            while ($row = $result->fetch_assoc()) {
                echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
            }
            
            // Count records
            $result = $conn->query("SELECT COUNT(*) as count FROM {$table}");
            $row = $result->fetch_assoc();
            echo "Records: " . $row['count'] . "\n";
        } else {
            echo "✗ Table does not exist\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\nCheck completed.\n";
?>
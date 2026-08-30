<?php
// Simple database check script
require_once 'crud/connect.php';

echo "Checking database connection and tables...\n\n";

try {
    $conn = getConnection();
    echo "✓ Database connection successful\n";
    
    // List all tables
    echo "\n--- ALL TABLES ---\n";
    $result = $conn->query("SHOW TABLES");
    while ($row = $result->fetch_row()) {
        echo "- " . $row[0] . "\n";
    }
    
    // Check specific tables
    echo "\n--- SPECIFIC TABLE CHECKS ---\n";
    $tables_to_check = ['users', 'trainers', 'trainer_sessions'];
    foreach ($tables_to_check as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "✓ Table '$table' exists\n";
        } else {
            echo "✗ Table '$table' does not exist\n";
        }
    }
    
    // Check trainers data
    echo "\n--- TRAINER DATA ---\n";
    $result = $conn->query("SELECT COUNT(*) as count FROM trainers");
    $row = $result->fetch_assoc();
    echo "Total trainers: " . $row['count'] . "\n";
    
    if ($row['count'] > 0) {
        $result = $conn->query("SELECT id, name, hourly_rate, available FROM trainers LIMIT 3");
        while ($trainer = $result->fetch_assoc()) {
            echo "ID: {$trainer['id']}, Name: {$trainer['name']}, Rate: \${$trainer['hourly_rate']}, Available: " . ($trainer['available'] ? 'Yes' : 'No') . "\n";
        }
    }
    
    // Check users data
    echo "\n--- USER DATA ---\n";
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    $row = $result->fetch_assoc();
    echo "Total users: " . $row['count'] . "\n";
    
    if ($row['count'] > 0) {
        $result = $conn->query("SELECT id, name, email FROM users LIMIT 3");
        while ($user = $result->fetch_assoc()) {
            echo "ID: {$user['id']}, Name: {$user['name']}, Email: {$user['email']}\n";
        }
    }
    
    // Check trainer_sessions data
    echo "\n--- TRAINER SESSIONS DATA ---\n";
    $result = $conn->query("SELECT COUNT(*) as count FROM trainer_sessions");
    $row = $result->fetch_assoc();
    echo "Total bookings: " . $row['count'] . "\n";
    
    if ($row['count'] > 0) {
        $result = $conn->query("SELECT id, user_id, trainer_id, scheduled_date, price FROM trainer_sessions LIMIT 3");
        while ($session = $result->fetch_assoc()) {
            echo "Booking ID: {$session['id']}, User: {$session['user_id']}, Trainer: {$session['trainer_id']}, Date: {$session['scheduled_date']}, Price: \${$session['price']}\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>
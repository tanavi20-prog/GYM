<?php
// Diagnostic script for trainer booking issues
require_once 'crud/connect.php';

echo "=== TRAINER BOOKING DIAGNOSTIC ===\n\n";

try {
    $conn = getConnection();
    echo "✓ Database connection successful\n";
    
    // Check trainers table structure
    echo "\n--- TRAINERS TABLE STRUCTURE ---\n";
    $result = $conn->query("DESCRIBE trainers");
    while ($row = $result->fetch_assoc()) {
        echo "{$row['Field']} ({$row['Type']}) - " . ($row['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
    }
    
    // Check trainer_sessions table structure
    echo "\n--- TRAINER_SESSIONS TABLE STRUCTURE ---\n";
    $result = $conn->query("DESCRIBE trainer_sessions");
    while ($row = $result->fetch_assoc()) {
        echo "{$row['Field']} ({$row['Type']}) - " . ($row['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
    }
    
    // Check if there are any trainers
    echo "\n--- TRAINER DATA ---\n";
    $result = $conn->query("SELECT id, name, hourly_rate, available FROM trainers LIMIT 5");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['id']}, Name: {$row['name']}, Rate: \${$row['hourly_rate']}, Available: " . ($row['available'] ? 'Yes' : 'No') . "\n";
        }
    } else {
        echo "No trainers found in database\n";
    }
    
    // Check for existing bookings
    echo "\n--- EXISTING BOOKINGS ---\n";
    $result = $conn->query("SELECT COUNT(*) as count FROM trainer_sessions");
    $row = $result->fetch_assoc();
    echo "Total bookings: {$row['count']}\n";
    
    if ($row['count'] > 0) {
        $result = $conn->query("SELECT * FROM trainer_sessions LIMIT 3");
        while ($row = $result->fetch_assoc()) {
            echo "Booking ID: {$row['id']}, User: {$row['user_id']}, Trainer: {$row['trainer_id']}, Date: {$row['scheduled_date']}\n";
        }
    }
    
    echo "\n=== COMMON ISSUES TO CHECK ===\n";
    echo "1. Make sure you're logged in as a user\n";
    echo "2. Check that trainers have hourly_rate set\n";
    echo "3. Verify trainer availability is set to 1/TRUE\n";
    echo "4. Check browser console for JavaScript errors\n";
    echo "5. Ensure Apache/PHP is running without errors\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
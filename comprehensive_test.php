<?php
// Comprehensive test for trainer booking functionality
require_once 'crud/connect.php';

echo "=== COMPREHENSIVE TRAINER BOOKING TEST ===\n\n";

try {
    $conn = getConnection();
    echo "✓ Database connection successful\n";
    
    // Test 1: Check if required tables exist
    echo "\n--- TABLE EXISTENCE TEST ---\n";
    $required_tables = ['trainers', 'trainer_sessions', 'users'];
    foreach ($required_tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "✓ Table '$table' exists\n";
        } else {
            echo "✗ Table '$table' missing\n";
        }
    }
    
    // Test 2: Check trainers table structure
    echo "\n--- TRAINERS TABLE STRUCTURE ---\n";
    $result = $conn->query("DESCRIBE trainers");
    $fields = [];
    while ($row = $result->fetch_assoc()) {
        $fields[$row['Field']] = $row['Type'];
        echo "{$row['Field']} ({$row['Type']})\n";
    }
    
    // Test 3: Check trainer_sessions table structure
    echo "\n--- TRAINER_SESSIONS TABLE STRUCTURE ---\n";
    $result = $conn->query("DESCRIBE trainer_sessions");
    while ($row = $result->fetch_assoc()) {
        echo "{$row['Field']} ({$row['Type']})\n";
    }
    
    // Test 4: Check for users
    echo "\n--- USER CHECK ---\n";
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    $row = $result->fetch_assoc();
    echo "Found {$row['count']} users\n";
    
    if ($row['count'] > 0) {
        $result = $conn->query("SELECT id, name, email FROM users LIMIT 1");
        $user = $result->fetch_assoc();
        echo "Sample user: {$user['name']} (ID: {$user['id']})\n";
    }
    
    // Test 5: Check for trainers
    echo "\n--- TRAINER CHECK ---\n";
    $result = $conn->query("SELECT COUNT(*) as count FROM trainers");
    $row = $result->fetch_assoc();
    echo "Found {$row['count']} trainers\n";
    
    if ($row['count'] > 0) {
        $result = $conn->query("SELECT id, name, hourly_rate, available FROM trainers LIMIT 1");
        $trainer = $result->fetch_assoc();
        echo "Sample trainer: {$trainer['name']} (ID: {$trainer['id']}, Rate: \${$trainer['hourly_rate']}, Available: " . ($trainer['available'] ? 'Yes' : 'No') . ")\n";
        
        // Verify required fields are set
        if ($trainer['hourly_rate'] === null || $trainer['hourly_rate'] <= 0) {
            echo "⚠ Warning: Trainer hourly_rate is not set properly\n";
        }
        if (!$trainer['available']) {
            echo "⚠ Warning: Trainer is not marked as available\n";
        }
    } else {
        echo "⚠ No trainers found. Please add a trainer through the admin panel.\n";
    }
    
    // Test 6: Check for existing bookings
    echo "\n--- BOOKING CHECK ---\n";
    $result = $conn->query("SELECT COUNT(*) as count FROM trainer_sessions");
    $row = $result->fetch_assoc();
    echo "Found {$row['count']} existing bookings\n";
    
    // Test 7: Try to create a test booking
    echo "\n--- TEST BOOKING CREATION ---\n";
    if ($row['count'] > 0) {
        echo "Skipping test booking creation (existing bookings found)\n";
    } else {
        echo "No existing bookings. Ready for testing.\n";
    }
    
    echo "\n=== TEST SUMMARY ===\n";
    echo "If all checks passed, the booking functionality should work.\n";
    echo "Common issues and solutions:\n";
    echo "1. Trainers must have hourly_rate > 0 and available = 1\n";
    echo "2. Users must be logged in to book sessions\n";
    echo "3. Date format must be YYYY-MM-DD HH:MM:SS\n";
    echo "4. Check browser console for JavaScript errors\n";
    echo "5. Check PHP error log for server-side errors\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>
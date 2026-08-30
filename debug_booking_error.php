<?php
// Comprehensive debugging script for booking errors
require_once 'crud/connect.php';

echo "=== TRAINER BOOKING ERROR DIAGNOSTIC ===\n\n";

try {
    $conn = getConnection();
    echo "✓ Database connection successful\n";
    
    // Check all required tables
    echo "\n--- DATABASE TABLE CHECK ---\n";
    $tables = ['users', 'trainers', 'trainer_sessions'];
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "✓ Table '$table' exists\n";
        } else {
            echo "✗ CRITICAL: Table '$table' MISSING\n";
            exit;
        }
    }
    
    // Check users
    echo "\n--- USER CHECK ---\n";
    $result = $conn->query("SELECT id, name, email FROM users LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "✓ User found: {$user['name']} (ID: {$user['id']})\n";
        $user_id = $user['id'];
    } else {
        echo "✗ CRITICAL: No users found. You must be logged in to book.\n";
        exit;
    }
    
    // Check trainers
    echo "\n--- TRAINER CHECK ---\n";
    $result = $conn->query("SELECT id, name, hourly_rate, available FROM trainers LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $trainer = $result->fetch_assoc();
        echo "✓ Trainer found: {$trainer['name']} (ID: {$trainer['id']})\n";
        echo "  Hourly Rate: \${$trainer['hourly_rate']}\n";
        echo "  Available: " . ($trainer['available'] ? 'Yes' : 'No') . "\n";
        
        if ($trainer['hourly_rate'] === null || $trainer['hourly_rate'] <= 0) {
            echo "✗ ERROR: Trainer hourly_rate is not set properly\n";
        }
        
        if (!$trainer['available']) {
            echo "✗ ERROR: Trainer is not marked as available\n";
        }
        
        $trainer_id = $trainer['id'];
        $hourly_rate = $trainer['hourly_rate'];
    } else {
        echo "✗ CRITICAL: No trainers found. Add trainers through admin panel.\n";
        exit;
    }
    
    // Test direct booking simulation
    echo "\n--- DIRECT BOOKING TEST ---\n";
    $scheduled_date = date('Y-m-d H:i:s', strtotime('+1 day 10:00:00'));
    $duration_minutes = 60;
    $price = $hourly_rate;
    $notes = 'Debug test booking';
    
    echo "Attempting to book with:\n";
    echo "  User ID: $user_id\n";
    echo "  Trainer ID: $trainer_id\n";
    echo "  Date: $scheduled_date\n";
    echo "  Duration: $duration_minutes minutes\n";
    echo "  Price: \$$price\n";
    
    $stmt = $conn->prepare("
        INSERT INTO trainer_sessions 
        (user_id, trainer_id, scheduled_date, duration_minutes, notes, price) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    if (!$stmt) {
        echo "✗ PREPARE ERROR: " . $conn->error . "\n";
        exit;
    }
    
    $stmt->bind_param("iiisis", $user_id, $trainer_id, $scheduled_date, $duration_minutes, $notes, $price);
    
    if ($stmt->execute()) {
        $booking_id = $stmt->insert_id;
        echo "✓ DIRECT BOOKING SUCCESSFUL! Booking ID: $booking_id\n";
        
        // Verify booking
        $verify = $conn->query("SELECT * FROM trainer_sessions WHERE id = $booking_id");
        if ($verify && $verify->num_rows > 0) {
            echo "✓ Booking verified in database\n";
        } else {
            echo "✗ Booking not found in database\n";
        }
    } else {
        echo "✗ DIRECT BOOKING FAILED: " . $stmt->error . "\n";
    }
    $stmt->close();
    
    // Test fetching bookings
    echo "\n--- BOOKING RETRIEVAL TEST ---\n";
    $stmt = $conn->prepare("
        SELECT ts.*, t.name as trainer_name
        FROM trainer_sessions ts
        JOIN trainers t ON ts.trainer_id = t.id
        WHERE ts.user_id = ?
        ORDER BY ts.scheduled_date ASC
    ");
    
    if (!$stmt) {
        echo "✗ RETRIEVAL PREPARE ERROR: " . $conn->error . "\n";
        exit;
    }
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "Found {$result->num_rows} bookings for user ID $user_id:\n";
    while ($row = $result->fetch_assoc()) {
        echo "  - Booking ID: {$row['id']}, Trainer: {$row['trainer_name']}, Date: {$row['scheduled_date']}\n";
    }
    $stmt->close();
    
    echo "\n=== DEBUGGING COMPLETE ===\n";
    echo "Check the output above to identify where the error occurs.\n";
    echo "Common issues and solutions:\n";
    echo "1. If direct booking works but UI doesn't: JavaScript/frontend issue\n";
    echo "2. If direct booking fails: Database/data issue\n";
    echo "3. If no users: You're not logged in\n";
    echo "4. If no trainers: Add trainers through admin panel\n";
    echo "5. If trainer rate <= 0: Fix in admin panel\n";
    echo "6. If trainer not available: Set available = 1 in database\n";
    
} catch (Exception $e) {
    echo "✗ FATAL ERROR: " . $e->getMessage() . "\n";
    echo "This indicates a fundamental database connection or configuration issue.\n";
}
?>
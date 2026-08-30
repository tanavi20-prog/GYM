<?php
// Test script to simulate the booking process
require_once 'crud/connect.php';

echo "=== TRAINER BOOKING TEST ===\n\n";

try {
    $conn = getConnection();
    echo "✓ Database connection successful\n";
    
    // Check if we have any users
    $result = $conn->query("SELECT id, name FROM users LIMIT 1");
    if ($result->num_rows == 0) {
        echo "✗ No users found. Please register a user first.\n";
        exit;
    }
    $user = $result->fetch_assoc();
    $user_id = $user['id'];
    echo "✓ Using user: {$user['name']} (ID: {$user_id})\n";
    
    // Check if we have any trainers
    $result = $conn->query("SELECT id, name, hourly_rate, available FROM trainers WHERE available = 1 LIMIT 1");
    if ($result->num_rows == 0) {
        echo "✗ No available trainers found.\n";
        echo "  Please add a trainer through the admin panel and make sure:\n";
        echo "  1. The trainer has 'available' set to 1\n";
        echo "  2. The trainer has an 'hourly_rate' set\n";
        exit;
    }
    $trainer = $result->fetch_assoc();
    $trainer_id = $trainer['id'];
    $hourly_rate = $trainer['hourly_rate'];
    echo "✓ Using trainer: {$trainer['name']} (ID: {$trainer_id}, Rate: \${$hourly_rate}/hr)\n";
    
    // Test booking data
    $scheduled_date = date('Y-m-d H:i:s', strtotime('+1 day 10:00:00'));
    $duration_minutes = 60;
    $price = $hourly_rate; // 1 hour session
    $notes = 'Test booking from diagnostic script';
    
    echo "\n--- BOOKING DETAILS ---\n";
    echo "Scheduled Date: {$scheduled_date}\n";
    echo "Duration: {$duration_minutes} minutes\n";
    echo "Price: \${$price}\n";
    echo "Notes: {$notes}\n";
    
    // Attempt to book the trainer
    echo "\n--- ATTEMPTING TO BOOK TRAINER ---\n";
    $stmt = $conn->prepare("
        INSERT INTO trainer_sessions 
        (user_id, trainer_id, scheduled_date, duration_minutes, notes, price) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    if (!$stmt) {
        echo "✗ Failed to prepare statement: " . $conn->error . "\n";
        exit;
    }
    
    $stmt->bind_param("iiisis", $user_id, $trainer_id, $scheduled_date, $duration_minutes, $notes, $price);
    
    if ($stmt->execute()) {
        $booking_id = $stmt->insert_id;
        echo "✓ Successfully booked trainer. Booking ID: {$booking_id}\n";
        
        // Verify the booking was inserted
        $verify = $conn->query("SELECT * FROM trainer_sessions WHERE id = {$booking_id}");
        if ($verify && $verify->num_rows > 0) {
            $booking = $verify->fetch_assoc();
            echo "✓ Booking verified in database:\n";
            echo "  User ID: {$booking['user_id']}\n";
            echo "  Trainer ID: {$booking['trainer_id']}\n";
            echo "  Scheduled Date: {$booking['scheduled_date']}\n";
            echo "  Price: \${$booking['price']}\n";
        } else {
            echo "✗ Booking not found in database\n";
        }
    } else {
        echo "✗ Failed to book trainer: " . $stmt->error . "\n";
    }
    $stmt->close();
    
    // Test fetching bookings for the user
    echo "\n--- TESTING BOOKING RETRIEVAL ---\n";
    $stmt = $conn->prepare("
        SELECT ts.*, t.name as trainer_name
        FROM trainer_sessions ts
        JOIN trainers t ON ts.trainer_id = t.id
        WHERE ts.user_id = ?
        ORDER BY ts.scheduled_date ASC
    ");
    
    if (!$stmt) {
        echo "✗ Failed to prepare retrieval statement: " . $conn->error . "\n";
        exit;
    }
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "Found {$result->num_rows} bookings for user ID {$user_id}:\n";
    while ($row = $result->fetch_assoc()) {
        echo "  - Booking ID: {$row['id']}, Trainer: {$row['trainer_name']}, Date: {$row['scheduled_date']}\n";
    }
    $stmt->close();
    
    echo "\n=== TEST COMPLETED ===\n";
    echo "If this test worked but the UI doesn't, the issue is likely in the JavaScript/frontend code.\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>
<?php
// Specific debugging for "booking failed" errors
require_once 'crud/connect.php';
require_once 'includes/session.php';

echo "=== BOOKING FAILURE DEBUG ===\n\n";

try {
    $conn = getConnection();
    echo "✓ Database connection: OK\n";
    
    // Check if user is logged in
    echo "\n--- USER AUTHENTICATION ---\n";
    if (!is_logged_in()) {
        echo "✗ ERROR: User is NOT logged in\n";
        echo "  You must be logged in to book trainers.\n";
        echo "  Solution: Go to login page and log in first.\n";
        exit;
    }
    
    $user_id = get_current_user_id();
    echo "✓ User logged in. ID: $user_id\n";
    
    // Get user details
    $user_result = $conn->query("SELECT id, name, email FROM users WHERE id = $user_id");
    if ($user_result && $user_row = $user_result->fetch_assoc()) {
        echo "  Name: {$user_row['name']}\n";
        echo "  Email: {$user_row['email']}\n";
    }
    
    // Check for available trainers
    echo "\n--- TRAINER AVAILABILITY ---\n";
    $trainer_result = $conn->query("SELECT id, name, hourly_rate, available FROM trainers WHERE available = 1 LIMIT 1");
    if (!$trainer_result || $trainer_result->num_rows == 0) {
        echo "✗ ERROR: No available trainers found\n";
        echo "  Solution: Add trainers through admin panel and mark them as available.\n";
        
        // Show all trainers for debugging
        echo "\n  All trainers in database:\n";
        $all_trainers = $conn->query("SELECT id, name, hourly_rate, available FROM trainers");
        if ($all_trainers) {
            while ($t = $all_trainers->fetch_assoc()) {
                echo "    ID: {$t['id']}, Name: {$t['name']}, Rate: \${$t['hourly_rate']}, Available: " . ($t['available'] ? 'Yes' : 'No') . "\n";
            }
        }
        exit;
    }
    
    $trainer = $trainer_result->fetch_assoc();
    echo "✓ Available trainer found:\n";
    echo "  ID: {$trainer['id']}\n";
    echo "  Name: {$trainer['name']}\n";
    echo "  Hourly Rate: \${$trainer['hourly_rate']}\n";
    
    // Validate trainer data
    if ($trainer['hourly_rate'] === null || $trainer['hourly_rate'] <= 0) {
        echo "✗ ERROR: Trainer hourly_rate is invalid ({$trainer['hourly_rate']})\n";
        echo "  Solution: Set hourly_rate > 0 in admin panel.\n";
        exit;
    }
    
    // Test booking with actual data that would be sent
    echo "\n--- BOOKING SIMULATION ---\n";
    $trainer_id = $trainer['id'];
    $scheduled_date = date('Y-m-d H:i:s', strtotime('+1 day 10:00:00'));
    $duration_minutes = 60;
    $price = $trainer['hourly_rate'];
    $notes = 'Test booking';
    
    echo "Attempting booking with:\n";
    echo "  User ID: $user_id\n";
    echo "  Trainer ID: $trainer_id\n";
    echo "  Scheduled Date: $scheduled_date\n";
    echo "  Duration: $duration_minutes minutes\n";
    echo "  Price: \$$price\n";
    echo "  Notes: $notes\n";
    
    // Try the actual booking query
    echo "\n--- DATABASE INSERT TEST ---\n";
    $stmt = $conn->prepare("
        INSERT INTO trainer_sessions 
        (user_id, trainer_id, scheduled_date, duration_minutes, notes, price) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    if (!$stmt) {
        echo "✗ PREPARE ERROR: " . $conn->error . "\n";
        exit;
    }
    
    echo "✓ Statement prepared successfully\n";
    
    $bind_result = $stmt->bind_param("iiisis", $user_id, $trainer_id, $scheduled_date, $duration_minutes, $notes, $price);
    if (!$bind_result) {
        echo "✗ BIND PARAM ERROR: " . $stmt->error . "\n";
        $stmt->close();
        exit;
    }
    
    echo "✓ Parameters bound successfully\n";
    
    $execute_result = $stmt->execute();
    if (!$execute_result) {
        echo "✗ EXECUTE ERROR: " . $stmt->error . "\n";
        $stmt->close();
        exit;
    }
    
    echo "✓ Booking inserted successfully!\n";
    $booking_id = $stmt->insert_id;
    echo "  Booking ID: $booking_id\n";
    $stmt->close();
    
    // Verify the booking was inserted
    echo "\n--- BOOKING VERIFICATION ---\n";
    $verify = $conn->query("SELECT * FROM trainer_sessions WHERE id = $booking_id");
    if ($verify && $verify_row = $verify->fetch_assoc()) {
        echo "✓ Booking verified in database:\n";
        echo "  User ID: {$verify_row['user_id']}\n";
        echo "  Trainer ID: {$verify_row['trainer_id']}\n";
        echo "  Scheduled Date: {$verify_row['scheduled_date']}\n";
        echo "  Price: \${$verify_row['price']}\n";
    } else {
        echo "✗ Booking NOT found in database\n";
    }
    
    // Test fetching the booking for dashboard
    echo "\n--- DASHBOARD FETCH TEST ---\n";
    $fetch_stmt = $conn->prepare("
        SELECT ts.*, t.name as trainer_name
        FROM trainer_sessions ts
        JOIN trainers t ON ts.trainer_id = t.id
        WHERE ts.user_id = ?
        ORDER BY ts.scheduled_date ASC
    ");
    
    if ($fetch_stmt) {
        echo "✓ Fetch statement prepared successfully\n";
        $fetch_stmt->bind_param("i", $user_id);
        $fetch_stmt->execute();
        $fetch_result = $fetch_stmt->get_result();
        
        echo "Found {$fetch_result->num_rows} bookings for user:\n";
        while ($booking = $fetch_result->fetch_assoc()) {
            echo "  - Booking ID: {$booking['id']}, Trainer: {$booking['trainer_name']}, Date: {$booking['scheduled_date']}\n";
        }
        $fetch_stmt->close();
    } else {
        echo "✗ Fetch statement prepare failed: " . $conn->error . "\n";
    }
    
    echo "\n=== SUCCESS! ===\n";
    echo "If this test worked but the UI doesn't, the issue is definitely in the JavaScript/frontend.\n";
    echo "Check the browser console (F12) for JavaScript errors when clicking 'Book'.\n";
    
} catch (Exception $e) {
    echo "✗ EXCEPTION ERROR: " . $e->getMessage() . "\n";
    echo "This indicates a serious system issue.\n";
}
?>
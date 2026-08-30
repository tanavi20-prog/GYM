<?php
// Add a sample booking to test the dashboard display
require_once 'crud/connect.php';

echo "Adding sample booking...\n";

try {
    $conn = getConnection();
    echo "✓ Database connection successful\n";
    
    // Check if we have any users
    $result = $conn->query("SELECT id FROM users LIMIT 1");
    if ($result->num_rows == 0) {
        echo "✗ No users found. Please register a user first.\n";
        exit;
    }
    $user = $result->fetch_assoc();
    $user_id = $user['id'];
    echo "✓ Using user ID: {$user_id}\n";
    
    // Check if we have any trainers
    $result = $conn->query("SELECT id, name, hourly_rate FROM trainers WHERE available = TRUE LIMIT 1");
    if ($result->num_rows == 0) {
        echo "✗ No available trainers found.\n";
        exit;
    }
    $trainer = $result->fetch_assoc();
    $trainer_id = $trainer['id'];
    $hourly_rate = $trainer['hourly_rate'];
    echo "✓ Using trainer: {$trainer['name']} (ID: {$trainer_id}, Rate: \${$hourly_rate}/hr)\n";
    
    // Add a sample booking for tomorrow
    $scheduled_date = date('Y-m-d H:i:s', strtotime('+1 day 10:00:00'));
    $duration_minutes = 60;
    $price = $hourly_rate; // 1 hour session
    $notes = 'Sample booking for testing dashboard display';
    
    $stmt = $conn->prepare("
        INSERT INTO trainer_sessions 
        (user_id, trainer_id, scheduled_date, duration_minutes, notes, price) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->bind_param("iiisis", $user_id, $trainer_id, $scheduled_date, $duration_minutes, $notes, $price);
    
    if ($stmt->execute()) {
        $booking_id = $stmt->insert_id;
        echo "✓ Successfully added sample booking. Booking ID: {$booking_id}\n";
        echo "  Scheduled for: " . date('M j, Y g:i A', strtotime($scheduled_date)) . "\n";
        echo "  Price: $" . number_format($price, 2) . "\n";
    } else {
        echo "✗ Failed to add sample booking: " . $stmt->error . "\n";
    }
    $stmt->close();
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "Test completed.\n";
?>
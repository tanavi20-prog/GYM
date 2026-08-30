<?php
// Test the trainer booking API directly
require_once 'crud/connect.php';
require_once 'includes/session.php';

echo "=== TRAINER BOOKING API TEST ===\n\n";

// Simulate a logged in user
$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = 1; // Assuming user ID 1 exists

echo "Simulating logged in user with ID: {$_SESSION['user_id']}\n";

try {
    $conn = getConnection();
    echo "✓ Database connection successful\n";
    
    // Check if user exists
    $user_result = $conn->query("SELECT id, name FROM users WHERE id = {$_SESSION['user_id']}");
    if ($user_result && $user_result->num_rows > 0) {
        $user = $user_result->fetch_assoc();
        echo "✓ User found: {$user['name']}\n";
    } else {
        echo "✗ User with ID {$_SESSION['user_id']} not found\n";
        exit;
    }
    
    // Check for available trainers
    $trainer_result = $conn->query("SELECT id, name, hourly_rate FROM trainers WHERE available = 1 LIMIT 1");
    if ($trainer_result && $trainer_result->num_rows > 0) {
        $trainer = $trainer_result->fetch_assoc();
        echo "✓ Available trainer found: {$trainer['name']} (ID: {$trainer['id']})\n";
    } else {
        echo "✗ No available trainers found\n";
        exit;
    }
    
    // Simulate the API call data
    $test_data = [
        'trainer_id' => $trainer['id'],
        'scheduled_date' => date('Y-m-d H:i:s', strtotime('+1 day 10:00:00')),
        'duration_minutes' => 60,
        'notes' => 'API test booking'
    ];
    
    echo "\n--- SIMULATING API CALL ---\n";
    echo "Data sent to API:\n";
    foreach ($test_data as $key => $value) {
        echo "  $key: $value\n";
    }
    
    // Include and test the actual API function
    echo "\n--- TESTING ACTUAL API FUNCTION ---\n";
    include 'api/trainer_booking.php';
    
    // Since we can't easily call the function directly, let's test the core logic
    echo "\n--- TESTING CORE BOOKING LOGIC ---\n";
    
    $trainer_id = $test_data['trainer_id'];
    $scheduled_date = $test_data['scheduled_date'];
    $duration_minutes = $test_data['duration_minutes'];
    $notes = $test_data['notes'];
    $user_id = $_SESSION['user_id'];
    
    // Validate datetime format
    $date = DateTime::createFromFormat('Y-m-d H:i:s', $scheduled_date);
    if (!$date) {
        echo "✗ Invalid date format\n";
        exit;
    }
    echo "✓ Date format valid\n";
    
    // Check if trainer exists and is available
    $check = $conn->prepare("SELECT id, name, hourly_rate FROM trainers WHERE id = ? AND available = TRUE");
    $check->bind_param("i", $trainer_id);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows === 0) {
        echo "✗ Trainer not found or not available\n";
        // Check what's actually in the database
        $check_all = $conn->query("SELECT id, name, hourly_rate, available FROM trainers WHERE id = $trainer_id");
        if ($check_all && $trainer_info = $check_all->fetch_assoc()) {
            echo "  Trainer info: ID={$trainer_info['id']}, Name={$trainer_info['name']}, Rate=\${$trainer_info['hourly_rate']}, Available=" . ($trainer_info['available'] ? 'Yes' : 'No') . "\n";
        }
        $check->close();
        exit;
    }
    
    $trainer_data = $result->fetch_assoc();
    $check->close();
    echo "✓ Trainer validated: {$trainer_data['name']}\n";
    
    // Calculate price
    $hours = $duration_minutes / 60;
    $price = $trainer_data['hourly_rate'] * $hours;
    echo "✓ Price calculated: \$$price ({$trainer_data['hourly_rate']} × $hours hours)\n";
    
    // Insert booking
    $stmt = $conn->prepare("
        INSERT INTO trainer_sessions 
        (user_id, trainer_id, scheduled_date, duration_minutes, notes, price) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    if (!$stmt) {
        echo "✗ Failed to prepare insert statement: " . $conn->error . "\n";
        exit;
    }
    
    $stmt->bind_param("iiisis", $user_id, $trainer_id, $scheduled_date, $duration_minutes, $notes, $price);
    
    if ($stmt->execute()) {
        $booking_id = $stmt->insert_id;
        echo "✓ Booking successful! Booking ID: $booking_id\n";
        
        // Verify booking
        $verify = $conn->query("SELECT * FROM trainer_sessions WHERE id = $booking_id");
        if ($verify && $verify->num_rows > 0) {
            echo "✓ Booking verified in database\n";
        } else {
            echo "✗ Booking not found in database\n";
        }
    } else {
        echo "✗ Booking failed: " . $stmt->error . "\n";
    }
    $stmt->close();
    
    echo "\n=== API TEST COMPLETE ===\n";
    echo "If this worked, the issue is likely in the JavaScript frontend.\n";
    echo "Check browser console for errors when clicking 'Book'.\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>
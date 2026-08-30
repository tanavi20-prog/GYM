<?php
// Complete diagnostic to identify exactly where booking fails
echo "=== COMPLETE BOOKING SYSTEM DIAGNOSTIC ===\n\n";

// Test 1: Check if all required files exist
echo "--- FILE SYSTEM CHECK ---\n";
$required_files = [
    'crud/connect.php',
    'includes/session.php',
    'api/trainer_booking.php',
    'pages/trainer.php',
    'pages/dashboard.php'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "✓ $file exists\n";
    } else {
        echo "✗ $file MISSING\n";
    }
}

// Test 2: Database connection and structure
echo "\n--- DATABASE CONNECTION AND STRUCTURE ---\n";
try {
    require_once 'crud/connect.php';
    $conn = getConnection();
    echo "✓ Database connection successful\n";
    
    // Check required tables
    $required_tables = ['users', 'trainers', 'trainer_sessions'];
    foreach ($required_tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "✓ Table '$table' exists\n";
        } else {
            echo "✗ Table '$table' MISSING\n";
        }
    }
    
    // Show table structures
    echo "\nTrainers table structure:\n";
    $result = $conn->query("DESCRIBE trainers");
    while ($row = $result->fetch_assoc()) {
        echo "  {$row['Field']} ({$row['Type']})\n";
    }
    
    echo "\nTrainer_sessions table structure:\n";
    $result = $conn->query("DESCRIBE trainer_sessions");
    while ($row = $result->fetch_assoc()) {
        echo "  {$row['Field']} ({$row['Type']})\n";
    }
    
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    exit;
}

// Test 3: Session and authentication
echo "\n--- SESSION AND AUTHENTICATION ---\n";
try {
    require_once 'includes/session.php';
    
    if (is_logged_in()) {
        $user_id = get_current_user_id();
        echo "✓ User logged in. ID: $user_id\n";
    } else {
        echo "⚠ User NOT logged in. This will cause booking to fail.\n";
        echo "  Solution: Log in first before trying to book.\n";
    }
} catch (Exception $e) {
    echo "✗ Session system error: " . $e->getMessage() . "\n";
}

// Test 4: Data integrity check
echo "\n--- DATA INTEGRITY CHECK ---\n";
try {
    // Check for users
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    $row = $result->fetch_assoc();
    echo "Users in database: {$row['count']}\n";
    
    if ($row['count'] > 0) {
        $result = $conn->query("SELECT id, name FROM users LIMIT 1");
        $user = $result->fetch_assoc();
        echo "  Sample user: {$user['name']} (ID: {$user['id']})\n";
    }
    
    // Check for trainers
    $result = $conn->query("SELECT COUNT(*) as count FROM trainers");
    $row = $result->fetch_assoc();
    echo "Trainers in database: {$row['count']}\n";
    
    if ($row['count'] > 0) {
        $result = $conn->query("SELECT id, name, hourly_rate, available FROM trainers LIMIT 3");
        while ($trainer = $result->fetch_assoc()) {
            echo "  Trainer: {$trainer['name']} (ID: {$trainer['id']}, Rate: \${$trainer['hourly_rate']}, Available: " . ($trainer['available'] ? 'Yes' : 'No') . ")\n";
            
            if ($trainer['hourly_rate'] === null || $trainer['hourly_rate'] <= 0) {
                echo "    ⚠ WARNING: Hourly rate is invalid!\n";
            }
            
            if (!$trainer['available']) {
                echo "    ⚠ WARNING: Trainer is not available!\n";
            }
        }
    } else {
        echo "  ⚠ No trainers found. Add trainers through admin panel.\n";
    }
    
    // Check for existing bookings
    $result = $conn->query("SELECT COUNT(*) as count FROM trainer_sessions");
    $row = $result->fetch_assoc();
    echo "Existing bookings: {$row['count']}\n";
    
} catch (Exception $e) {
    echo "✗ Data integrity check failed: " . $e->getMessage() . "\n";
}

// Test 5: Direct booking test
echo "\n--- DIRECT BOOKING TEST ---\n";
if (is_logged_in()) {
    try {
        // Get a user and trainer for testing
        $user_result = $conn->query("SELECT id FROM users LIMIT 1");
        $trainer_result = $conn->query("SELECT id, hourly_rate FROM trainers WHERE available = 1 LIMIT 1");
        
        if ($user_result->num_rows > 0 && $trainer_result->num_rows > 0) {
            $user = $user_result->fetch_assoc();
            $trainer = $trainer_result->fetch_assoc();
            
            echo "Testing booking with:\n";
            echo "  User ID: {$user['id']}\n";
            echo "  Trainer ID: {$trainer['id']}\n";
            echo "  Hourly Rate: \${$trainer['hourly_rate']}\n";
            
            if ($trainer['hourly_rate'] > 0) {
                $scheduled_date = date('Y-m-d H:i:s', strtotime('+1 day 10:00:00'));
                $duration_minutes = 60;
                $price = $trainer['hourly_rate'];
                $notes = 'Diagnostic test booking';
                
                $stmt = $conn->prepare("
                    INSERT INTO trainer_sessions 
                    (user_id, trainer_id, scheduled_date, duration_minutes, notes, price) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                
                if ($stmt) {
                    $stmt->bind_param("iiisis", $user['id'], $trainer['id'], $scheduled_date, $duration_minutes, $notes, $price);
                    
                    if ($stmt->execute()) {
                        $booking_id = $stmt->insert_id;
                        echo "✓ DIRECT BOOKING SUCCESSFUL! Booking ID: $booking_id\n";
                        
                        // Clean up the test booking
                        $conn->query("DELETE FROM trainer_sessions WHERE id = $booking_id");
                        echo "  Test booking cleaned up.\n";
                    } else {
                        echo "✗ Direct booking failed: " . $stmt->error . "\n";
                    }
                    $stmt->close();
                } else {
                    echo "✗ Failed to prepare statement: " . $conn->error . "\n";
                }
            } else {
                echo "✗ Cannot test booking - trainer hourly rate is invalid.\n";
            }
        } else {
            echo "✗ Cannot test booking - missing user or available trainer.\n";
        }
    } catch (Exception $e) {
        echo "✗ Direct booking test failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "⚠ Skipping direct booking test (not logged in).\n";
}

echo "\n=== DIAGNOSTIC COMPLETE ===\n";
echo "Check the output above to identify where the booking process is failing.\n";
echo "Common issues and solutions:\n";
echo "1. Not logged in → Log in first\n";
echo "2. No trainers → Add trainers in admin panel\n";
echo "3. Invalid hourly rates → Set proper rates in admin panel\n";
echo "4. Trainers not available → Mark as available in admin panel\n";
echo "5. Database errors → Check database connection and tables\n";
echo "6. JavaScript errors → Check browser console (F12)\n";
?>
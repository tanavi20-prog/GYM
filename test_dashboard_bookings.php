<?php
// Test script to verify dashboard booking display
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/helpers.php';
require_once 'crud/connect.php';

echo "<h1>Dashboard Booking Display Test</h1>\n";

try {
    $conn = getConnection();
    echo "<p>✓ Database connection successful</p>\n";
    
    // Check if we have users
    $result = $conn->query("SELECT id, name FROM users LIMIT 1");
    if ($result->num_rows == 0) {
        echo "<p>❌ No users found. Please register a user first.</p>\n";
        exit;
    }
    $user = $result->fetch_assoc();
    echo "<p>✓ Using test user: {$user['name']} (ID: {$user['id']})</p>\n";
    
    // Simulate user login for testing
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user'] = $user;
    
    // Check for trainers
    $result = $conn->query("SELECT id, name, hourly_rate FROM trainers WHERE available = TRUE LIMIT 1");
    if ($result->num_rows == 0) {
        echo "<p>❌ No available trainers found.</p>\n";
        exit;
    }
    $trainer = $result->fetch_assoc();
    echo "<p>✓ Found trainer: {$trainer['name']} (Rate: ₹" . round($trainer['hourly_rate'] * 83) . "/hr)</p>\n";
    
    // Check existing bookings for this user
    $result = $conn->query("SELECT COUNT(*) as count FROM trainer_sessions WHERE user_id = {$user['id']}");
    $count = $result->fetch_assoc()['count'];
    echo "<p>✓ User has {$count} existing bookings</p>\n";
    
    // Add a test booking if none exist
    if ($count == 0) {
        echo "<p>Adding test booking...</p>\n";
        $scheduled_date = date('Y-m-d H:i:s', strtotime('+2 days 14:00:00'));
        $stmt = $conn->prepare("
            INSERT INTO trainer_sessions 
            (user_id, trainer_id, scheduled_date, duration_minutes, price, status, notes) 
            VALUES (?, ?, ?, 60, ?, 'scheduled', 'Test booking for dashboard display')
        ");
        $price = $trainer['hourly_rate'];
        $stmt->bind_param("iisd", $user['id'], $trainer['id'], $scheduled_date, $price);
        
        if ($stmt->execute()) {
            echo "<p>✅ Test booking added successfully!</p>\n";
        } else {
            echo "<p>❌ Failed to add test booking: " . $stmt->error . "</p>\n";
        }
        $stmt->close();
    }
    
    // Test the dashboard query
    echo "<h2>Testing Dashboard Query...</h2>\n";
    $stmt = $conn->prepare("
        SELECT ts.*, 
               t.name as trainer_name, 
               t.image_url,
               t.experience_years,
               t.rating,
               t.hourly_rate,
               t.bio
        FROM trainer_sessions ts
        JOIN trainers t ON ts.trainer_id = t.id
        WHERE ts.user_id = ?
        ORDER BY ts.scheduled_date ASC
        LIMIT 5
    ");
    
    if (!$stmt) {
        echo "<p>❌ Failed to prepare statement: " . $conn->error . "</p>\n";
        exit;
    }
    
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "<p>✓ Query executed successfully</p>\n";
    echo "<p>✓ Found " . $result->num_rows . " booked trainers</p>\n";
    
    if ($result->num_rows > 0) {
        echo "<h3>Booked Trainers:</h3>\n";
        echo "<ul>\n";
        while ($row = $result->fetch_assoc()) {
            echo "<li>{$row['trainer_name']} - " . date('M j, Y g:i A', strtotime($row['scheduled_date'])) . " - Status: {$row['status']}</li>\n";
        }
        echo "</ul>\n";
    }
    
    $stmt->close();
    
    echo "<h2>Test Results:</h2>\n";
    echo "<ol>\n";
    echo "<li>Database connection: ✅ Working</li>\n";
    echo "<li>User session: ✅ Simulated</li>\n";
    echo "<li>Trainer data: ✅ Available</li>\n";
    echo "<li>Booking query: ✅ Executing without errors</li>\n";
    echo "<li>Dashboard display: <a href='/?page=dashboard'>Test Dashboard</a></li>\n";
    echo "</ol>\n";
    
    echo "<p><strong>Next steps:</strong></p>\n";
    echo "<ol>\n";
    echo "<li>Log in as the test user</li>\n";
    echo "<li>Visit the dashboard to see booked trainers</li>\n";
    echo "<li>Book a session from the trainer page to test real-time updates</li>\n";
    echo "</ol>\n";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>\n";
}
?>
<?php
// Fix trainer availability
require_once 'crud/connect.php';

echo "<h1>🔧 Fix Trainer Availability</h1>\n";

try {
    $conn = getConnection();
    echo "<p style='color: green;'>✅ Database connection successful</p>\n";
    
    // Check current status
    $result = $conn->query("SELECT COUNT(*) as total, SUM(available) as available FROM trainers");
    $row = $result->fetch_assoc();
    
    echo "<h2>Current Status:</h2>\n";
    echo "<p>Total trainers: {$row['total']}</p>\n";
    echo "<p>Available trainers: {$row['available']}</p>\n";
    
    if ($row['total'] == 0) {
        echo "<p style='color: red;'>❌ No trainers found in database!</p>\n";
        echo "<p><a href='add_sample_trainers.php'>Click here to add sample trainers</a></p>\n";
        exit;
    }
    
    if ($row['available'] == $row['total']) {
        echo "<p style='color: green;'>✅ All trainers are already available!</p>\n";
        echo "<p>No fixes needed.</p>\n";
        exit;
    }
    
    // Fix availability
    echo "<h2>Fixing Availability...</h2>\n";
    
    $result = $conn->query("UPDATE trainers SET available = 1 WHERE available = 0");
    
    if ($result) {
        $affected = $conn->affected_rows;
        echo "<p style='color: green;'>✅ Successfully updated $affected trainers to available status</p>\n";
        
        // Verify the fix
        $result = $conn->query("SELECT COUNT(*) as available FROM trainers WHERE available = 1");
        $row = $result->fetch_assoc();
        echo "<p>Now you have {$row['available']} available trainers</p>\n";
        
        echo "<h2>Next Steps:</h2>\n";
        echo "<p>1. <a href='trainer_test_page.php'>Test trainer display</a></p>\n";
        echo "<p>2. <a href='pages/registration.php'>Register a new user</a> and test</p>\n";
        echo "<p>3. <a href='pages/trainer.php'>Visit trainer page</a> directly</p>\n";
    } else {
        echo "<p style='color: red;'>❌ Failed to update trainers: " . $conn->error . "</p>\n";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>\n";
}
?>
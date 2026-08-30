<?php
// Verification script for trainer functionality
require_once 'crud/connect.php';

echo "<h1>Trainer Functionality Verification</h1>\n";

try {
    $conn = getConnection();
    echo "<p style='color: green;'>✓ Database connection successful</p>\n";
    
    // Check trainers table
    $tables = $conn->query("SHOW TABLES LIKE 'trainers'");
    if ($tables && $tables->num_rows > 0) {
        echo "<p style='color: green;'>✓ Trainers table exists</p>\n";
        
        // Count trainers
        $result = $conn->query("SELECT COUNT(*) as count FROM trainers");
        $row = $result->fetch_assoc();
        echo "<p>Found {$row['count']} trainers in database</p>\n";
        
        if ($row['count'] > 0) {
            // Show sample trainer
            $result = $conn->query("SELECT id, name, specialties, rating, hourly_rate, available FROM trainers LIMIT 1");
            $trainer = $result->fetch_assoc();
            echo "<h3>Sample Trainer:</h3>\n";
            echo "<ul>\n";
            echo "<li>ID: {$trainer['id']}</li>\n";
            echo "<li>Name: {$trainer['name']}</li>\n";
            echo "<li>Rating: {$trainer['rating']}</li>\n";
            echo "<li>Hourly Rate: \${$trainer['hourly_rate']}</li>\n";
            echo "<li>Available: " . ($trainer['available'] ? 'Yes' : 'No') . "</li>\n";
            echo "</ul>\n";
        }
    } else {
        echo "<p style='color: red;'>✗ Trainers table does not exist</p>\n";
    }
    
    // Check trainer_sessions table
    $tables = $conn->query("SHOW TABLES LIKE 'trainer_sessions'");
    if ($tables && $tables->num_rows > 0) {
        echo "<p style='color: green;'>✓ Trainer sessions table exists</p>\n";
        
        // Count sessions
        $result = $conn->query("SELECT COUNT(*) as count FROM trainer_sessions");
        $row = $result->fetch_assoc();
        echo "<p>Found {$row['count']} trainer sessions in database</p>\n";
    } else {
        echo "<p style='color: red;'>✗ Trainer sessions table does not exist</p>\n";
    }
    
    echo "<h2>Functionality Check:</h2>\n";
    echo "<ol>\n";
    echo "<li>Admin can add trainers through <a href='/admin/add_trainer.php'>/admin/add_trainer.php</a></li>\n";
    echo "<li>Trainers appear on <a href='/?page=trainer'>Trainer Page</a></li>\n";
    echo "<li>Featured trainers appear on <a href='/?page=plan'>Plan Page</a></li>\n";
    echo "<li>Trainer sessions managed in <a href='/admin/sessions.php'>Admin Sessions</a></li>\n";
    echo "<li>Booked trainers appear on <a href='/?page=dashboard'>User Dashboard</a> (when logged in)</li>\n";
    echo "</ol>\n";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>\n";
}
?>
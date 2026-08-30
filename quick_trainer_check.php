<?php
// Direct database check for trainers
require_once 'crud/connect.php';

echo "=== TRAINER DATABASE CHECK ===\n\n";

try {
    $conn = getConnection();
    echo "✅ Database connection: SUCCESS\n\n";
    
    // Check if trainers table exists
    $result = $conn->query("SHOW TABLES LIKE 'trainers'");
    if ($result && $result->num_rows > 0) {
        echo "✅ Trainers table exists\n";
    } else {
        echo "❌ Trainers table NOT FOUND\n";
        exit;
    }
    
    // Count total trainers
    $result = $conn->query("SELECT COUNT(*) as count FROM trainers");
    $row = $result->fetch_assoc();
    $totalTrainers = $row['count'];
    echo "Total trainers in database: $totalTrainers\n\n";
    
    if ($totalTrainers == 0) {
        echo "🚨 ISSUE FOUND: No trainers in database!\n";
        echo "This is why no trainers are showing.\n\n";
        echo "SOLUTION: Run add_sample_trainers.php to add trainers\n";
        exit;
    }
    
    // Check available trainers
    $result = $conn->query("SELECT COUNT(*) as count FROM trainers WHERE available = 1");
    $row = $result->fetch_assoc();
    $availableTrainers = $row['count'];
    echo "Available trainers (available = 1): $availableTrainers\n\n";
    
    if ($availableTrainers == 0) {
        echo "🚨 ISSUE FOUND: No trainers marked as available!\n";
        echo "SOLUTION: Update trainers to set available = 1\n";
        echo "Run this SQL: UPDATE trainers SET available = 1;\n\n";
    }
    
    // Show trainer details
    echo "=== TRAINER DETAILS ===\n";
    $result = $conn->query("SELECT id, name, available, hourly_rate FROM trainers ORDER BY id");
    while ($trainer = $result->fetch_assoc()) {
        $status = $trainer['available'] ? 'AVAILABLE' : 'NOT AVAILABLE';
        echo "ID: {$trainer['id']} | Name: {$trainer['name']} | Status: $status | Rate: ₹{$trainer['hourly_rate']}\n";
    }
    
    echo "\n=== SESSION CHECK ===\n";
    session_start();
    echo "Session started: " . (session_status() === PHP_SESSION_ACTIVE ? 'YES' : 'NO') . "\n";
    echo "User logged in: " . (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'YES' : 'NO') . "\n";
    if (isset($_SESSION['user_id'])) {
        echo "Current user ID: {$_SESSION['user_id']}\n";
    }
    
    echo "\n=== RECOMMENDATIONS ===\n";
    if ($totalTrainers == 0) {
        echo "1. Visit: http://localhost/gymmm/add_sample_trainers.php\n";
        echo "2. This will add sample trainers to your database\n";
    } elseif ($availableTrainers == 0) {
        echo "1. Run this SQL query in your database:\n";
        echo "   UPDATE trainers SET available = 1;\n";
        echo "2. Or visit: http://localhost/gymmm/add_sample_trainers.php\n";
    } else {
        echo "✅ Trainers look good in database\n";
        echo "Check if user is properly logged in\n";
        echo "Try clearing browser cache/cookies\n";
    }
    
} catch (Exception $e) {
    echo "❌ DATABASE ERROR: " . $e->getMessage() . "\n";
    echo "Check your database connection settings\n";
}
?>
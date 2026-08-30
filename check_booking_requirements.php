<?php
// Check booking requirements and database setup
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'crud/connect.php';

echo "<h1>🔍 Booking Requirements Check</h1>";

try {
    $conn = getConnection();
    echo "<p>✅ Database connection successful</p>";
    
    // Check required tables
    $tables = ['users', 'trainers', 'trainer_sessions'];
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "<p>✅ Table '$table' exists</p>";
        } else {
            echo "<p>❌ Table '$table' MISSING - This is likely the problem!</p>";
        }
    }
    
    echo "<h2>👤 Checking Users...</h2>";
    $users = $conn->query("SELECT id, name, email FROM users LIMIT 5");
    if ($users && $users->num_rows > 0) {
        echo "<p>✅ Found " . $users->num_rows . " users:</p><ul>";
        while ($user = $users->fetch_assoc()) {
            $currentUser = (is_logged_in() && get_current_user_id() == $user['id']) ? " (CURRENT USER)" : "";
            echo "<li>ID: {$user['id']}, Name: {$user['name']}, Email: {$user['email']} $currentUser</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>❌ No users found. You need to register and log in first.</p>";
    }
    
    echo "<h2>🏋️ Checking Trainers...</h2>";
    $trainers = $conn->query("SELECT id, name, hourly_rate, available, rating, experience_years FROM trainers ORDER BY rating DESC LIMIT 10");
    if ($trainers && $trainers->num_rows > 0) {
        echo "<p>✅ Found " . $trainers->num_rows . " trainers:</p><ul>";
        while ($trainer = $trainers->fetch_assoc()) {
            $availability = $trainer['available'] ? "✅ Available" : "❌ Not Available";
            $rate = $trainer['hourly_rate'] ? "$" . $trainer['hourly_rate'] : "❌ No Rate Set";
            echo "<li>ID: {$trainer['id']}, Name: {$trainer['name']}, Rate: $rate, Available: $availability, Rating: {$trainer['rating']}/5</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>❌ No trainers found. You need to add trainers first.</p>";
    }
    
    echo "<h2>📅 Checking Existing Bookings...</h2>";
    $bookings = $conn->query("SELECT id, user_id, trainer_id, scheduled_date, status, price FROM trainer_sessions ORDER BY scheduled_date DESC LIMIT 5");
    if ($bookings && $bookings->num_rows > 0) {
        echo "<p>✅ Found " . $bookings->num_rows . " bookings:</p><ul>";
        while ($booking = $bookings->fetch_assoc()) {
            echo "<li>ID: {$booking['id']}, User: {$booking['user_id']}, Trainer: {$booking['trainer_id']}, Date: {$booking['scheduled_date']}, Price: \${$booking['price']}, Status: {$booking['status']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>ℹ️ No bookings found yet (this is normal for new installations).</p>";
    }
    
    echo "<h2>🔑 Current Session Status:</h2>";
    if (is_logged_in()) {
        $currentUserId = get_current_user_id();
        echo "<p>✅ User is logged in (ID: $currentUserId)</p>";
        
        // Check if current user has valid data
        $userCheck = $conn->query("SELECT id, name, email FROM users WHERE id = $currentUserId");
        if ($userCheck && $userCheck->num_rows > 0) {
            $userData = $userCheck->fetch_assoc();
            echo "<p>✅ Current user data verified: {$userData['name']} ({$userData['email']})</p>";
        } else {
            echo "<p>❌ Current user ID doesn't match any user in database</p>";
        }
    } else {
        echo "<p>❌ No user is currently logged in. Please log in first.</p>";
    }
    
    echo "<h2>💡 Booking Requirements Checklist:</h2>";
    echo "<ul>";
    echo "<li>" . (is_logged_in() ? "✅" : "❌") . " User is logged in</li>";
    
    $userCount = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
    echo "<li>" . ($userCount > 0 ? "✅" : "❌") . " At least one user exists</li>";
    
    $trainerCount = $conn->query("SELECT COUNT(*) as count FROM trainers WHERE available = 1 AND hourly_rate IS NOT NULL AND hourly_rate > 0")->fetch_assoc()['count'];
    echo "<li>" . ($trainerCount > 0 ? "✅" : "❌") . " At least one available trainer with valid hourly rate</li>";
    
    $tablesExist = true;
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if (!$result || $result->num_rows == 0) {
            $tablesExist = false;
            break;
        }
    }
    echo "<li>" . ($tablesExist ? "✅" : "❌") . " All required tables exist</li>";
    echo "</ul>";
    
    if (is_logged_in() && $userCount > 0 && $trainerCount > 0 && $tablesExist) {
        echo "<h2>🎉 All requirements met! Booking should work.</h2>";
        echo "<p>Try booking a trainer now. If it still fails, check browser console for JavaScript errors.</p>";
    } else {
        echo "<h2>⚠️ Some requirements are missing. Please fix the issues above.</h2>";
    }

} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
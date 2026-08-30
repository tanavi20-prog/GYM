<?php
// Direct database connection test
echo "=== TRAINER ISSUE DIAGNOSTIC ===\n\n";

// Direct connection to check database
$mysqli = new mysqli("localhost", "root", "", "fitness_db");

if ($mysqli->connect_error) {
    die("❌ Database connection failed: " . $mysqli->connect_error . "\n");
}
echo "✅ Database connection: SUCCESS\n";

// Check if trainers table exists
$result = $mysqli->query("SHOW TABLES LIKE 'trainers'");
if ($result && $result->num_rows > 0) {
    echo "✅ Trainers table exists\n";
} else {
    echo "❌ Trainers table NOT FOUND\n";
    echo "Creating trainers table...\n";
    $createTable = "CREATE TABLE IF NOT EXISTS trainers (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        phone VARCHAR(15) DEFAULT NULL,
        specialties JSON DEFAULT NULL,
        experience_years INT(2) DEFAULT 0,
        rating DECIMAL(2,1) DEFAULT 0.0,
        hourly_rate DECIMAL(6,2) DEFAULT NULL,
        bio TEXT DEFAULT NULL,
        certifications JSON DEFAULT NULL,
        available BOOLEAN DEFAULT TRUE,
        location VARCHAR(100) DEFAULT NULL,
        image_url VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    if ($mysqli->query($createTable)) {
        echo "✅ Trainers table created successfully\n";
    } else {
        echo "❌ Failed to create trainers table: " . $mysqli->error . "\n";
        exit;
    }
}

// Count trainers
$result = $mysqli->query("SELECT COUNT(*) as count FROM trainers");
$row = $result->fetch_assoc();
$totalTrainers = $row['count'];
echo "📊 Total trainers in database: $totalTrainers\n";

if ($totalTrainers == 0) {
    echo "🚨 CRITICAL ISSUE: No trainers in database!\n";
    echo "This is DEFINITELY why new users can't see trainers.\n\n";
    
    echo "=== SOLUTION ===\n";
    echo "Run this command in your browser:\n";
    echo "http://localhost/gymmm/add_sample_trainers.php\n\n";
    
    echo "Or add trainers manually with this SQL:\n";
    echo "INSERT INTO trainers (name, email, hourly_rate, available, location) VALUES\n";
    echo "('Test Trainer', 'test@gymmm.com', 800, 1, 'Mumbai, India');\n";
    exit;
}

// Check available trainers
$result = $mysqli->query("SELECT COUNT(*) as count FROM trainers WHERE available = 1");
$row = $result->fetch_assoc();
$availableTrainers = $row['count'];
echo "📊 Available trainers (available = 1): $availableTrainers\n";

if ($availableTrainers == 0) {
    echo "🚨 ISSUE: Trainers exist but none are marked as available!\n";
    echo "Fixing this now...\n";
    
    if ($mysqli->query("UPDATE trainers SET available = 1")) {
        echo "✅ Successfully made all trainers available\n";
        $availableTrainers = $totalTrainers;
    } else {
        echo "❌ Failed to update trainer availability: " . $mysqli->error . "\n";
    }
}

// Show trainer details
echo "\n=== TRAINER LIST ===\n";
$result = $mysqli->query("SELECT id, name, available, hourly_rate FROM trainers ORDER BY id");
while ($trainer = $result->fetch_assoc()) {
    $status = $trainer['available'] ? 'AVAILABLE' : 'NOT AVAILABLE';
    echo "ID: {$trainer['id']} | Name: {$trainer['name']} | Status: $status | Rate: ₹{$trainer['hourly_rate']}\n";
}

// Check session
echo "\n=== SESSION CHECK ===\n";
session_start();
echo "Session started: " . (session_status() === PHP_SESSION_ACTIVE ? 'YES' : 'NO') . "\n";
echo "User logged in: " . (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'YES' : 'NO') . "\n";

if (isset($_SESSION['user_id'])) {
    echo "Current user ID: {$_SESSION['user_id']}\n";
    
    // Check if this user exists
    $userResult = $mysqli->query("SELECT name, email FROM users WHERE id = {$_SESSION['user_id']}");
    if ($userResult && $userResult->num_rows > 0) {
        $user = $userResult->fetch_assoc();
        echo "User found: {$user['name']} ({$user['email']})\n";
    } else {
        echo "❌ User ID {$_SESSION['user_id']} not found in database!\n";
    }
}

// Test the exact query used in trainer.php
echo "\n=== TRAINER DISPLAY QUERY TEST ===\n";
$query = "SELECT t.*, 
          COUNT(v.id) as video_count,
          GROUP_CONCAT(DISTINCT vc.name) as video_categories
   FROM trainers t 
   LEFT JOIN videos v ON t.name = v.instructor_name AND v.is_active = 1
   LEFT JOIN video_categories vc ON v.category_id = vc.id
   GROUP BY t.id
   ORDER BY t.rating DESC, t.experience_years DESC";

$result = $mysqli->query($query);
if ($result) {
    echo "✅ Query executed successfully\n";
    $displayableTrainers = 0;
    while ($row = $result->fetch_assoc()) {
        if (isset($row['available']) && $row['available']) {
            $displayableTrainers++;
        }
    }
    echo "Trainers that would display: $displayableTrainers\n";
    
    if ($displayableTrainers == 0) {
        echo "❌ ISSUE: No trainers pass the availability filter!\n";
        echo "Check that 'available' column is properly set to 1\n";
    }
} else {
    echo "❌ Query failed: " . $mysqli->error . "\n";
}

$mysqli->close();

echo "\n=== RECOMMENDATIONS ===\n";
if ($totalTrainers == 0) {
    echo "1. 🔴 ADD TRAINERS IMMEDIATELY\n";
    echo "   Visit: http://localhost/gymmm/add_sample_trainers.php\n";
} elseif ($availableTrainers == 0) {
    echo "1. 🔴 FIX TRAINER AVAILABILITY\n";
    echo "   Run: UPDATE trainers SET available = 1;\n";
} elseif ($displayableTrainers == 0) {
    echo "1. 🔴 CHECK TRAINER DATA\n";
    echo "   Some trainers exist but aren't displaying properly\n";
} else {
    echo "✅ Trainers should be displaying correctly\n";
    echo "If still not showing, check:\n";
    echo "1. User is properly logged in\n";
    echo "2. Browser cache/cookies\n";
    echo "3. JavaScript errors in console\n";
}

echo "\nTest page: http://localhost/gymmm/trainer_test_page.php\n";
?>
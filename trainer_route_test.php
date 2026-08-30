<?php
// Quick test to see what's happening with trainer display
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/helpers.php';
require_once 'crud/connect.php';

echo "<h1>🔍 Trainer Page Route Test</h1>";

// Check if we can access the page as non-logged-in user
echo "<h2>Current Session Status:</h2>";
echo "Is logged in: " . (is_logged_in() ? 'YES' : 'NO') . "<br>";
echo "User ID: " . get_current_user_id() . "<br>";

// Check trainer count directly
$conn = getConnection();
$allTrainers = $conn->query("SELECT COUNT(*) as total FROM trainers")->fetch_assoc()['total'];
$availableTrainers = $conn->query("SELECT COUNT(*) as available FROM trainers WHERE available = 1")->fetch_assoc()['available'];

echo "<h2>Trainer Database Status:</h2>";
echo "Total trainers in database: $allTrainers<br>";
echo "Available trainers: $availableTrainers<br>";

if ($availableTrainers > 0) {
    echo "<h3>Sample Available Trainers:</h3>";
    $result = $conn->query("SELECT id, name, location, hourly_rate, rating FROM trainers WHERE available = 1 LIMIT 5");
    while ($trainer = $result->fetch_assoc()) {
        echo "• ID: {$trainer['id']}, Name: {$trainer['name']}, Location: {$trainer['location']}, Rate: ₹{$trainer['hourly_rate']}, Rating: {$trainer['rating']}<br>";
    }
} else {
    echo "<p style='color: red;'><strong>ISSUE: No available trainers in database!</strong></p>";
    echo "<p>You need to run the sample trainer script:</p>";
    echo "<a href='add_sample_trainers.php' style='background: #22c55e; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-block;'>Add Sample Trainers</a>";
}

echo "<h2>Quick Actions:</h2>";
echo "<a href='?page=trainer' style='margin-right: 10px; background: #3b82f6; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-block;'>Visit Trainer Page</a>";
echo "<a href='?page=registration' style='background: #10b981; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-block;'>Register New User</a>";

// Check if we can simulate the trainer loading logic
echo "<h2>Simulating Trainer Loading Logic:</h2>";

$trainers = [];
$result = $conn->query("SELECT t.*, 
                               COUNT(v.id) as video_count,
                               GROUP_CONCAT(DISTINCT vc.name) as video_categories
                        FROM trainers t 
                        LEFT JOIN videos v ON t.name = v.instructor_name AND v.is_active = 1
                        LEFT JOIN video_categories vc ON v.category_id = vc.id
                        WHERE t.available = 1
                        GROUP BY t.id
                        ORDER BY t.rating DESC, t.experience_years DESC");

while ($row = $result->fetch_assoc()) {
    $trainers[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'rating' => $row['rating'],
        'location' => $row['location'],
        'hourly_rate' => $row['hourly_rate'],
        'video_count' => $row['video_count']
    ];
}

echo "Trainers that would be loaded: " . count($trainers) . "<br>";
if (count($trainers) > 0) {
    echo "<p style='color: green;'>✅ Trainers are ready to display!</p>";
} else {
    echo "<p style='color: red;'>❌ No trainers will be displayed!</p>";
}
?>
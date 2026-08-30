<?php
require_once 'crud/connect.php';

$conn = getConnection();

echo "Checking database contents...\n";

// Check users
$userResult = $conn->query("SELECT COUNT(*) as count FROM users");
$userCount = $userResult->fetch_assoc()['count'];
echo "Users: $userCount\n";

// Check trainers
$trainerResult = $conn->query("SELECT COUNT(*) as count FROM trainers");
$trainerCount = $trainerResult->fetch_assoc()['count'];
echo "Trainers: $trainerCount\n";

// Check plans
$planResult = $conn->query("SELECT COUNT(*) as count FROM plans");
$planCount = $planResult->fetch_assoc()['count'];
echo "Plans: $planCount\n";

// Check sessions
$sessionResult = $conn->query("SELECT COUNT(*) as count FROM sessions");
$sessionCount = $sessionResult->fetch_assoc()['count'];
echo "Sessions: $sessionCount\n";

// Check videos
$videoResult = $conn->query("SELECT COUNT(*) as count FROM videos");
$videoCount = $videoResult->fetch_assoc()['count'];
echo "Videos: $videoCount\n";

// Check video categories
$categoryResult = $conn->query("SELECT COUNT(*) as count FROM video_categories");
$categoryCount = $categoryResult->fetch_assoc()['count'];
echo "Video Categories: $categoryCount\n";

if ($userCount > 0 && $trainerCount > 0) {
    echo "\n✓ Ready to add plans and sessions\n";
} else {
    echo "\n⚠ Need to add users and trainers first\n";
}

if ($videoCount > 0) {
    echo "✓ Videos available\n";
} else {
    echo "⚠ No videos found\n";
}
?>
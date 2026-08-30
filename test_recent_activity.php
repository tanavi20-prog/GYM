<?php
// Test recent activity functionality
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
require_once 'crud/connect.php';

// Simulate logged in user
$_SESSION['user_id'] = 1;
$_SESSION['logged_in'] = true;

$user = get_logged_in_user();
if (!$user) {
    echo "No user found. Please log in first.";
    exit;
}

echo "<h1>Recent Activity Test</h1>";
echo "<p>User: " . $user['name'] . " (ID: " . $user['id'] . ")</p>";

// Test the getUserRecentActivities function
function getUserRecentActivities($userId, $limit = 5) {
    global $conn;
    $activities = [];
    
    // Get recent completed sessions
    $stmt = $conn->prepare("SELECT ts.*, t.name as trainer_name, p.goal as plan_goal FROM trainer_sessions ts LEFT JOIN trainers t ON ts.trainer_id = t.id LEFT JOIN plans p ON ts.user_id = p.user_id WHERE ts.user_id = ? ORDER BY ts.scheduled_date DESC LIMIT ?");
    $stmt->bind_param("ii", $userId, $limit);
    $stmt->execute();
    $sessions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    foreach ($sessions as $session) {
        $activities[] = [
            'type' => 'session',
            'title' => $session['trainer_name'] ? "Session with {$session['trainer_name']}" : "Workout Session",
            'description' => $session['plan_goal'] ? ucfirst(str_replace('-', ' ', $session['plan_goal'])) . " training" : "General fitness session",
            'date' => $session['scheduled_date'],
            'status' => $session['status'],
            'duration' => $session['duration_minutes']
        ];
    }
    
    // Get recent plan creations
    $stmt = $conn->prepare("SELECT * FROM plans WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
    $stmt->bind_param("ii", $userId, $limit);
    $stmt->execute();
    $plans = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    foreach ($plans as $plan) {
        $activities[] = [
            'type' => 'plan',
            'title' => "New Plan Created",
            'description' => ucfirst(str_replace('-', ' ', $plan['goal'])) . " plan",
            'date' => $plan['created_at'],
            'status' => 'created'
        ];
    }
    
    // Sort all activities by date
    usort($activities, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    // Limit to requested number
    return array_slice($activities, 0, $limit);
}

$recent_activities = getUserRecentActivities($user['id'], 5);

echo "<h2>Recent Activities:</h2>";
if (empty($recent_activities)) {
    echo "<p>No recent activities found.</p>";
    echo "<p>This is expected if the user hasn't completed any sessions or created any plans yet.</p>";
} else {
    echo "<ul>";
    foreach ($recent_activities as $activity) {
        echo "<li>";
        echo "<strong>" . htmlspecialchars($activity['title']) . "</strong> - ";
        echo htmlspecialchars($activity['description']) . " - ";
        echo "<em>" . date('M j, Y g:i A', strtotime($activity['date'])) . "</em>";
        echo " (" . ucfirst($activity['status']) . ")";
        if (isset($activity['duration'])) {
            echo " - " . $activity['duration'] . " min";
        }
        echo "</li>";
    }
    echo "</ul>";
}

echo "<h2>Database Check:</h2>";
$conn = getConnection();

// Check if tables exist
$tables = ['trainer_sessions', 'plans', 'trainers'];
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        echo "<p>✅ Table '$table' exists</p>";
    } else {
        echo "<p>❌ Table '$table' does not exist</p>";
    }
}

// Check user's data
echo "<h3>User Data:</h3>";
echo "<p>Workout sessions: ";
$result = $conn->query("SELECT COUNT(*) as count FROM trainer_sessions WHERE user_id = " . $user['id']);
$count = $result->fetch_assoc()['count'];
echo $count . "</p>";

echo "<p>Plans: ";
$result = $conn->query("SELECT COUNT(*) as count FROM plans WHERE user_id = " . $user['id']);
$count = $result->fetch_assoc()['count'];
echo $count . "</p>";

echo "<br><a href='/gymmm/'>← Back to Dashboard</a>";
?>
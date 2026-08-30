<?php
require_once 'crud/connect.php';

echo "<h2>Trainer Debug Report</h2>";

$conn = getConnection();

if (!$conn) {
    echo "<p style='color: red;'>ERROR: No database connection!</p>";
    exit;
}

echo "<p style='color: green;'>✓ Database connection successful</p>";

// Check if trainers table exists
$result = $conn->query("SHOW TABLES LIKE 'trainers'");
if ($result->num_rows === 0) {
    echo "<p style='color: red;'>ERROR: trainers table does not exist!</p>";
    exit;
}
echo "<p style='color: green;'>✓ trainers table exists</p>";

// Check total trainers count
$result = $conn->query('SELECT COUNT(*) as count FROM trainers');
$row = $result->fetch_assoc();
echo "<p>Total trainers in database: <strong>" . $row['count'] . "</strong></p>";

if ($row['count'] === 0) {
    echo "<p style='color: orange;'>⚠️ WARNING: Database has 0 trainers!</p>";
    echo "<p>This is why trainers are not showing. You need to add trainers to the database first.</p>";
    exit;
}

// Show actual trainers
echo "<h3>Sample Trainers:</h3>";
$result = $conn->query('SELECT id, name, available, specialties, rating, experience_years FROM trainers LIMIT 5');
echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr><th>ID</th><th>Name</th><th>Available</th><th>Rating</th><th>Experience</th><th>Specialties</th></tr>";
while ($r = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $r['id'] . "</td>";
    echo "<td>" . htmlspecialchars($r['name']) . "</td>";
    echo "<td>" . ($r['available'] ? '<span style="color: green;">YES</span>' : '<span style="color: red;">NO</span>') . "</td>";
    echo "<td>" . $r['rating'] . "</td>";
    echo "<td>" . $r['experience_years'] . "</td>";
    echo "<td>" . htmlspecialchars(substr($r['specialties'] ?? '', 0, 50)) . "...</td>";
    echo "</tr>";
}
echo "</table>";

// Test the exact query from trainer.php
echo "<h3>Testing trainer.php Query:</h3>";
$result = $conn->query("SELECT t.*, 
                               COUNT(v.id) as video_count,
                               GROUP_CONCAT(DISTINCT vc.name) as video_categories
                        FROM trainers t 
                        LEFT JOIN videos v ON t.name = v.instructor_name AND v.is_active = 1
                        LEFT JOIN video_categories vc ON v.category_id = vc.id
                        GROUP BY t.id
                        ORDER BY t.rating DESC, t.experience_years DESC");

if (!$result) {
    echo "<p style='color: red;'>ERROR: Query failed: " . $conn->error . "</p>";
} else {
    $count = $result->num_rows;
    echo "<p>Query returned <strong>" . $count . "</strong> rows</p>";
    
    if ($count > 0) {
        echo "<p style='color: green;'>✓ Query is working correctly!</p>";
        $row = $result->fetch_assoc();
        echo "<p><strong>First trainer:</strong> " . htmlspecialchars($row['name']) . " (Available: " . ($row['available'] ? 'YES' : 'NO') . ")</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Query returned 0 rows - might be filtered out!</p>";
    }
}
?>

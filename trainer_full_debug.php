<?php
require_once 'crud/connect.php';

echo "<h2>COMPREHENSIVE TRAINER DEBUG</h2>";

$conn = getConnection();

if (!$conn) {
    echo "<p style='color: red;'>❌ NO DATABASE CONNECTION</p>";
    exit;
}

echo "<p style='color: green;'>✓ Database connected</p><br>";

// Check trainers count
$result = $conn->query('SELECT COUNT(*) as count FROM trainers');
$row = $result->fetch_assoc();
$total = $row['count'];
echo "<p><strong>Total trainers in DB:</strong> " . $total . "</p>";

if ($total === 0) {
    echo "<p style='color: red;'>❌ NO TRAINERS IN DATABASE!</p>";
    exit;
}

// Check trainers structure
echo "<h3>Database Structure:</h3>";
$result = $conn->query('DESCRIBE trainers');
$fields = [];
while ($row = $result->fetch_assoc()) {
    $fields[] = $row['Field'];
}
echo "<p>Fields: " . implode(", ", $fields) . "</p><br>";

// Check actual trainers
echo "<h3>All Trainers in Database:</h3>";
$result = $conn->query('SELECT id, name, available FROM trainers');
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Name</th><th>Available</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr><td>" . $row['id'] . "</td><td>" . $row['name'] . "</td><td>" . ($row['available'] ? 'YES (1)' : 'NO (0)') . "</td></tr>";
}
echo "</table>";

// Now simulate what trainer.php does
echo "<h3>Simulating trainer.php Logic:</h3>";
echo "<p>Running the exact query from trainer.php...</p>";

$trainers = [];
$result = $conn->query("SELECT t.*, 
                               COUNT(v.id) as video_count,
                               GROUP_CONCAT(DISTINCT vc.name) as video_categories
                        FROM trainers t 
                        LEFT JOIN videos v ON t.name = v.instructor_name AND v.is_active = 1
                        LEFT JOIN video_categories vc ON v.category_id = vc.id
                        GROUP BY t.id
                        ORDER BY t.rating DESC, t.experience_years DESC");

if (!$result) {
    echo "<p style='color: red;'>❌ Query error: " . $conn->error . "</p>";
    exit;
}

$row_count = 0;
while ($row = $result->fetch_assoc()) {
    $row_count++;
    echo "<p>Row $row_count: " . $row['name'] . " | available=" . ($row['available'] ?? 'NULL') . "</p>";
    
    // This is the code that was filtering them out
    if (!isset($row['available']) || !$row['available']) {
        echo "<span style='color: red;'>&nbsp;&nbsp;&nbsp;&nbsp;❌ WOULD BE SKIPPED (not available)</span><br>";
    } else {
        echo "<span style='color: green;'>&nbsp;&nbsp;&nbsp;&nbsp;✓ WOULD BE SHOWN</span><br>";
    }
}

echo "<p><br><strong>Total rows from query: " . $row_count . "</strong></p>";
if ($row_count === 0) {
    echo "<p style='color: red;'>❌ Query returned 0 rows!</p>";
}

?>

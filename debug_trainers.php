<?php
require_once 'crud/connect.php';

$conn = getConnection();

echo "=== DEBUG TRAINERS ===\n\n";

// Check total trainers
$result = $conn->query('SELECT COUNT(*) as count FROM trainers');
$row = $result->fetch_assoc();
echo "1. Total trainers in DB: " . $row['count'] . "\n";

// Check available trainers
$result = $conn->query('SELECT COUNT(*) as count FROM trainers WHERE available = 1');
$row = $result->fetch_assoc();
echo "2. Available trainers (available=1): " . $row['count'] . "\n";

// Check all trainers with their status
echo "\n3. All trainers with details:\n";
$result = $conn->query('SELECT id, name, available, rating, experience_years FROM trainers');
$total = 0;
while ($r = $result->fetch_assoc()) {
    $total++;
    echo "   ID: " . $r['id'] . " | Name: " . $r['name'] . " | Available: " . ($r['available'] ?? 'NULL') . " | Rating: " . $r['rating'] . " | Years: " . $r['experience_years'] . "\n";
}
echo "   Total rows: " . $total . "\n";

// Check the actual query used in trainer.php
echo "\n4. Running trainer.php's database query:\n";
$result = $conn->query("SELECT t.*, 
                               COUNT(v.id) as video_count,
                               GROUP_CONCAT(DISTINCT vc.name) as video_categories
                        FROM trainers t 
                        LEFT JOIN videos v ON t.name = v.instructor_name AND v.is_active = 1
                        LEFT JOIN video_categories vc ON v.category_id = vc.id
                        GROUP BY t.id
                        ORDER BY t.rating DESC, t.experience_years DESC");

if (!$result) {
    echo "   ERROR: " . $conn->error . "\n";
} else {
    $count = 0;
    while ($row = $result->fetch_assoc()) {
        $count++;
        echo "   Row $count: Name=" . $row['name'] . " | Available=" . ($row['available'] ?? 'NULL') . "\n";
    }
    echo "   Total results: " . $count . "\n";
}

// Check if they are being filtered out in the code
echo "\n5. Testing the filter condition from trainer.php:\n";
$result = $conn->query('SELECT id, name, available FROM trainers LIMIT 3');
while ($row = $result->fetch_assoc()) {
    $skip = (!isset($row['available']) || !$row['available']);
    echo "   " . $row['name'] . " (available=" . ($row['available'] ?? 'NULL') . ") - Would be skipped: " . ($skip ? 'YES' : 'NO') . "\n";
}

?>

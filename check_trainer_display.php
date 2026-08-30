<?php
require_once 'crud/connect.php';

$conn = getConnection();

// Check total trainers
$result = $conn->query('SELECT COUNT(*) as count FROM trainers');
$row = $result->fetch_assoc();
echo "Total trainers in DB: " . $row['count'] . "\n";

// Check available trainers
$result = $conn->query('SELECT COUNT(*) as count FROM trainers WHERE available = 1');
$row = $result->fetch_assoc();
echo "Available trainers: " . $row['count'] . "\n";

// Check sample trainers
echo "\nSample trainers:\n";
$result = $conn->query('SELECT id, name, available, specialties FROM trainers LIMIT 5');
while ($r = $result->fetch_assoc()) {
    echo $r['id'] . ' - ' . $r['name'] . ' (available: ' . ($r['available'] ? 'YES' : 'NO') . ') specialties: ' . $r['specialties'] . "\n";
}

// Check the actual query used in trainer.php
echo "\nRunning the trainer.php query:\n";
$result = $conn->query("SELECT t.*, 
                               COUNT(v.id) as video_count,
                               GROUP_CONCAT(DISTINCT vc.name) as video_categories
                        FROM trainers t 
                        LEFT JOIN videos v ON t.name = v.instructor_name AND v.is_active = 1
                        LEFT JOIN video_categories vc ON v.category_id = vc.id
                        GROUP BY t.id
                        ORDER BY t.rating DESC, t.experience_years DESC");

$count = 0;
while ($row = $result->fetch_assoc()) {
    $count++;
    echo "Trainer: " . $row['name'] . " (available: " . ($row['available'] ? 'YES' : 'NO') . ")\n";
}
echo "Total results from trainer.php query: " . $count . "\n";

?>

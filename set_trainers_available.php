<?php
require_once 'crud/connect.php';

$conn = getConnection();

// Update all trainers to be available
$stmt = $conn->prepare("UPDATE trainers SET available = 1 WHERE available = 0 OR available IS NULL");
$stmt->execute();
$affected = $conn->affected_rows;

echo "Updated $affected trainers to available = 1\n";

// Check result
$result = $conn->query('SELECT COUNT(*) as count FROM trainers WHERE available = 1');
$row = $result->fetch_assoc();
echo "Total available trainers now: " . $row['count'] . "\n";

?>

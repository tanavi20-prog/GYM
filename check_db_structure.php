<?php
require_once 'crud/connect.php';

$conn = getConnection();

echo "Checking database structure...\n\n";

// Check trainers table
echo "=== TRAINERS TABLE ===\n";
$result = $conn->query("DESCRIBE trainers");
while ($row = $result->fetch_assoc()) {
    echo "{$row['Field']} ({$row['Type']}) " . ($row['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
}

echo "\n=== TRAINER_SESSIONS TABLE ===\n";
$result = $conn->query("DESCRIBE trainer_sessions");
while ($row = $result->fetch_assoc()) {
    echo "{$row['Field']} ({$row['Type']}) " . ($row['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
}

echo "\n=== SAMPLE TRAINER DATA ===\n";
$result = $conn->query("SELECT * FROM trainers LIMIT 1");
if ($row = $result->fetch_assoc()) {
    foreach ($row as $key => $value) {
        echo "$key: $value\n";
    }
} else {
    echo "No trainers found\n";
}
?>
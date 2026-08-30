<?php



require_once 'crud/connect.php';

$conn = getConnection();

// Check plans table structure
echo "=== PLANS TABLE STRUCTURE ===\n";
$result = $conn->query("DESCRIBE plans");
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}

echo "\n=== SESSIONS TABLE STRUCTURE ===\n";
$result = $conn->query("DESCRIBE sessions");
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}

echo "\n=== CURRENT DATA IN PLANS TABLE ===\n";
$result = $conn->query("SELECT COUNT(*) as count FROM plans");
$row = $result->fetch_assoc();
echo "Total plans: " . $row['count'] . "\n";

echo "\n=== CURRENT DATA IN SESSIONS TABLE ===\n";
$result = $conn->query("SELECT COUNT(*) as count FROM sessions");
$row = $result->fetch_assoc();
echo "Total sessions: " . $row['count'] . "\n";
?>
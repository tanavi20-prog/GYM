<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'crud/connect.php';

try {
    $conn = getConnection();
    if ($conn) {
        echo "Database connection: SUCCESS\n";
        $result = $conn->query("SELECT COUNT(*) as count FROM trainers");
        $row = $result->fetch_assoc();
        echo "Trainers in database: " . $row['count'] . "\n";
        
        // Test a sample query
        $result2 = $conn->query("SELECT name, specialties, location FROM trainers LIMIT 5");
        echo "\nSample trainers:\n";
        while ($trainer = $result2->fetch_assoc()) {
            echo "- " . $trainer['name'] . " (" . $trainer['location'] . ")\n";
            echo "  Specialties: " . $trainer['specialties'] . "\n";
        }
    } else {
        echo "Database connection: FAILED\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
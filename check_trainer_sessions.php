<?php
// Check trainer_sessions table structure
require_once 'crud/connect.php';

try {
    $conn = getConnection();
    
    echo "<h2>trainer_sessions Table Structure</h2>\n";
    
    $result = $conn->query("DESCRIBE trainer_sessions");
    if ($result) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>\n";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>\n";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    }
    
    echo "<h2>Sample Data</h2>\n";
    $result = $conn->query("SELECT * FROM trainer_sessions LIMIT 3");
    if ($result && $result->num_rows > 0) {
        echo "<pre>\n";
        while ($row = $result->fetch_assoc()) {
            print_r($row);
            echo "\n---\n";
        }
        echo "</pre>\n";
    } else {
        echo "<p>No data found in trainer_sessions table</p>\n";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>\n";
}
?>
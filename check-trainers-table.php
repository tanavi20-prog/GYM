<?php
// Check trainers table structure
require_once 'crud/connect.php';

try {
    $conn = getConnection();
    
    echo "<h1>Checking Trainers Table Structure</h1>";
    
    // Check if trainers table exists
    $result = $conn->query("SHOW TABLES LIKE 'trainers'");
    if ($result->num_rows == 0) {
        echo "<p style='color: red;'>❌ Trainers table doesn't exist.</p>";
        
        // Show all tables
        echo "<h2>Available Tables:</h2>";
        $tables = $conn->query("SHOW TABLES");
        if ($tables) {
            while ($table = $tables->fetch_array()) {
                echo "<p>- " . $table[0] . "</p>";
            }
        }
    } else {
        echo "<p style='color: green;'>✅ Trainers table exists.</p>";
        
        // Show table structure
        echo "<h2>Trainers Table Structure:</h2>";
        $columns = $conn->query("DESCRIBE trainers");
        
        if ($columns) {
            echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; margin-top: 1rem;'>";
            echo "<tr style='background: #f0f0f0;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            
            while ($column = $columns->fetch_assoc()) {
                echo "<tr>";
                echo "<td><strong>" . $column['Field'] . "</strong></td>";
                echo "<td>" . $column['Type'] . "</td>";
                echo "<td>" . $column['Null'] . "</td>";
                echo "<td>" . $column['Key'] . "</td>";
                echo "<td>" . ($column['Default'] ?? 'NULL') . "</td>";
                echo "<td>" . $column['Extra'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        // Show sample data if any exists
        echo "<h2>Sample Data:</h2>";
        $data = $conn->query("SELECT * FROM trainers LIMIT 3");
        
        if ($data && $data->num_rows > 0) {
            echo "<p>Found " . $data->num_rows . " trainer(s):</p>";
            echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; margin-top: 1rem;'>";
            
            // Get column names
            $fields = $data->fetch_fields();
            echo "<tr style='background: #f0f0f0;'>";
            foreach ($fields as $field) {
                echo "<th>" . $field->name . "</th>";
            }
            echo "</tr>";
            
            // Show data
            $data->data_seek(0); // Reset pointer
            while ($row = $data->fetch_array()) {
                echo "<tr>";
                foreach ($row as $key => $value) {
                    if (!is_numeric($key)) { // Skip numeric indices
                        echo "<td>" . (strlen($value) > 50 ? substr($value, 0, 50) . "..." : $value) . "</td>";
                    }
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No trainers found in the table.</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database Error: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Check Trainers Table - RKT FitVibe</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 20px; 
            background: #f5f5f5;
        }
        h1, h2 { color: #22c55e; }
        table { 
            background: white; 
            margin-top: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 100%;
        }
        th { background: #22c55e; color: white; }
        td { background: white; }
        tr:nth-child(even) td { background: #f9f9f9; }
    </style>
</head>
<body>
</body>
</html>
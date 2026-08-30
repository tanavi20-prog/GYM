<?php
// Check trainer data
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'crud/connect.php';

echo "<h1>Trainer Data Check</h1>\n";

try {
    $conn = getConnection();
    if ($conn) {
        echo "<p style='color: green;'>✓ Database connection successful</p>\n";
        
        // Check if trainers table exists
        $result = $conn->query("SHOW TABLES LIKE 'trainers'");
        if ($result && $result->num_rows > 0) {
            echo "<p style='color: green;'>✓ Trainers table exists</p>\n";
            
            // Count trainers
            $result = $conn->query("SELECT COUNT(*) as count FROM trainers");
            $row = $result->fetch_assoc();
            echo "<p>Total trainers: " . $row['count'] . "</p>\n";
            
            // Count available trainers
            $result = $conn->query("SELECT COUNT(*) as count FROM trainers WHERE available = 1");
            $row = $result->fetch_assoc();
            echo "<p>Available trainers: " . $row['count'] . "</p>\n";
            
            // Show sample data
            echo "<h2>Sample Trainers:</h2>\n";
            $result = $conn->query("SELECT id, name, specialties, location, available FROM trainers LIMIT 10");
            if ($result && $result->num_rows > 0) {
                echo "<table border='1' style='border-collapse: collapse; width: 100%;'>\n";
                echo "<tr><th>ID</th><th>Name</th><th>Specialties</th><th>Location</th><th>Available</th></tr>\n";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['specialties']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                    echo "<td>" . ($row['available'] ? 'Yes' : 'No') . "</td>";
                    echo "</tr>\n";
                }
                echo "</table>\n";
                
                // Test specific search
                echo "<h2>Test Searches:</h2>\n";
                
                // Test 1: Search for "Strength" in specialties
                echo "<h3>Search for 'Strength' in specialties:</h3>\n";
                $result = $conn->query("SELECT * FROM trainers WHERE available = 1");
                $trainers = [];
                while ($row = $result->fetch_assoc()) {
                    $trainers[] = $row;
                }
                
                $filtered = array_filter($trainers, function($t) {
                    $specialties = json_decode($t['specialties'], true);
                    if (is_array($specialties)) {
                        foreach ($specialties as $s) {
                            if (stripos($s, 'Strength') !== false) {
                                return true;
                            }
                        }
                    }
                    return false;
                });
                
                echo "<p>Found " . count($filtered) . " trainers with 'Strength' in specialties</p>\n";
                if (!empty($filtered)) {
                    echo "<ul>\n";
                    foreach ($filtered as $t) {
                        echo "<li>" . htmlspecialchars($t['name']) . "</li>\n";
                    }
                    echo "</ul>\n";
                }
                
                // Test 2: Search for "india" in location
                echo "<h3>Search for 'india' in location:</h3>\n";
                $filtered2 = array_filter($trainers, function($t) {
                    return stripos($t['location'], 'india') !== false;
                });
                
                echo "<p>Found " . count($filtered2) . " trainers with 'india' in location</p>\n";
                if (!empty($filtered2)) {
                    echo "<ul>\n";
                    foreach ($filtered2 as $t) {
                        echo "<li>" . htmlspecialchars($t['name']) . " - " . htmlspecialchars($t['location']) . "</li>\n";
                    }
                    echo "</ul>\n";
                }
                
            } else {
                echo "<p style='color: red;'>✗ No trainers found in database</p>\n";
            }
        } else {
            echo "<p style='color: red;'>✗ Trainers table does not exist</p>\n";
        }
    } else {
        echo "<p style='color: red;'>✗ Database connection failed</p>\n";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}

echo "<br><a href='?page=trainer'>Back to Trainers Page</a> | ";
echo "<a href='test_search.html'>Test Search Page</a> | ";
echo "<a href='debug_trainer_search.php'>Debug Search</a>\n";
?>
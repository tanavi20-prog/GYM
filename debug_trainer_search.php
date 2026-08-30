<?php
// Debug trainer search
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'crud/connect.php';

echo "<h2>Trainer Search Debug</h2>\n";

// Show GET parameters
echo "<h3>GET Parameters:</h3>\n";
echo "<pre>";
print_r($_GET);
echo "</pre>\n";

// Check database connection
try {
    $conn = getConnection();
    if ($conn) {
        echo "<p style='color: green;'>✓ Database connection successful</p>\n";
        
        // Count total trainers
        $result = $conn->query("SELECT COUNT(*) as count FROM trainers");
        $row = $result->fetch_assoc();
        echo "<p>Total trainers in database: " . $row['count'] . "</p>\n";
        
        // Show sample trainers
        echo "<h3>Sample Trainers:</h3>\n";
        $result2 = $conn->query("SELECT id, name, specialties, location, available FROM trainers LIMIT 10");
        echo "<table border='1' style='border-collapse: collapse;'>\n";
        echo "<tr><th>ID</th><th>Name</th><th>Specialties</th><th>Location</th><th>Available</th></tr>\n";
        while ($trainer = $result2->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $trainer['id'] . "</td>";
            echo "<td>" . htmlspecialchars($trainer['name']) . "</td>";
            echo "<td>" . htmlspecialchars($trainer['specialties']) . "</td>";
            echo "<td>" . htmlspecialchars($trainer['location']) . "</td>";
            echo "<td>" . ($trainer['available'] ? 'Yes' : 'No') . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
        
        // Test the actual filtering logic
        if (isset($_GET['specialty']) || isset($_GET['location'])) {
            echo "<h3>Filter Test Results:</h3>\n";
            
            $specialtyFilter = trim($_GET['specialty'] ?? '');
            $locationFilter = trim($_GET['location'] ?? '');
            
            echo "<p>Specialty filter: '" . htmlspecialchars($specialtyFilter) . "'</p>\n";
            echo "<p>Location filter: '" . htmlspecialchars($locationFilter) . "'</p>\n";
            
            // Get all trainers for filtering
            $result3 = $conn->query("SELECT * FROM trainers WHERE available = 1");
            $trainers = [];
            while ($row = $result3->fetch_assoc()) {
                $trainers[] = $row;
            }
            
            echo "<p>Available trainers for filtering: " . count($trainers) . "</p>\n";
            
            // Apply filters
            $filteredTrainers = $trainers;
            
            if ($specialtyFilter) {
                $filteredTrainers = array_filter($filteredTrainers, function($trainer) use ($specialtyFilter) {
                    // Decode specialties JSON
                    $specialties = json_decode($trainer['specialties'], true);
                    if (is_array($specialties)) {
                        foreach ($specialties as $specialty) {
                            if (stripos($specialty, $specialtyFilter) !== false) {
                                return true;
                            }
                        }
                    }
                    return false;
                });
                $filteredTrainers = array_values($filteredTrainers);
            }
            
            if ($locationFilter) {
                $filteredTrainers = array_filter($filteredTrainers, function($trainer) use ($locationFilter) {
                    return stripos($trainer['location'], $locationFilter) !== false;
                });
                $filteredTrainers = array_values($filteredTrainers);
            }
            
            echo "<p>Filtered trainers found: " . count($filteredTrainers) . "</p>\n";
            
            if (!empty($filteredTrainers)) {
                echo "<h4>Matching Trainers:</h4>\n";
                echo "<ul>\n";
                foreach ($filteredTrainers as $trainer) {
                    echo "<li>" . htmlspecialchars($trainer['name']) . " - " . htmlspecialchars($trainer['location']) . "</li>\n";
                }
                echo "</ul>\n";
            }
        }
        
    } else {
        echo "<p style='color: red;'>✗ Database connection failed</p>\n";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}

echo "<br><a href='?page=trainer'>Back to Trainers Page</a>\n";
?>
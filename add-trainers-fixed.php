<?php
// Fixed script to add Indian trainers - adapts to existing table structure
require_once 'crud/connect.php';

try {
    $conn = getConnection();
    
    echo "<h1>Adding Indian Trainers - Fixed Version</h1>";
    
    // First, check the actual table structure
    $result = $conn->query("SHOW TABLES LIKE 'trainers'");
    if ($result->num_rows == 0) {
        echo "<p style='color: red;'>❌ Trainers table doesn't exist. Please run database setup first.</p>";
        echo "<p><a href='check-trainers-table.php'>Check Database Structure</a></p>";
        exit;
    }
    
    // Get the actual column structure
    $columns = $conn->query("DESCRIBE trainers");
    $tableColumns = [];
    if ($columns) {
        while ($column = $columns->fetch_assoc()) {
            $tableColumns[] = $column['Field'];
        }
    }
    
    echo "<p style='color: green;'>✅ Found trainers table with columns: " . implode(', ', $tableColumns) . "</p>";
    
    // Prepare trainer data that adapts to different column names
    $trainers = [
        [
            'name' => 'Tanavi Desai',
            'email' => 'tanavi.desai@fitvibe.com',
            'bio' => 'Certified yoga instructor and wellness coach from Surat. Specializes in traditional Hatha Yoga and modern fitness fusion.',
            'specialties' => json_encode(['Yoga', 'Pilates', 'Mindfulness', 'Wellness Coaching']),
            'specialization' => 'Yoga & Wellness', // Alternative field name
            'rating' => 4.8,
            'experience_years' => 6,
            'experience' => 6, // Alternative field name
            'hourly_rate' => 1200.00,
            'available' => 1,
            'certifications' => json_encode(['RYT-500 Yoga Alliance', 'Pilates Instructor', 'Wellness Coach']),
            'location' => 'Surat, Gujarat, India',
            'image_url' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
            'phone' => '+91-98765-43210'
        ],
        [
            'name' => 'Khushi Chauhan',
            'email' => 'khushi.chauhan@fitvibe.com',
            'bio' => 'Dynamic fitness trainer from Delhi specializing in HIIT and strength training. Former state-level athlete.',
            'specialties' => json_encode(['HIIT', 'Strength Training', 'Athletic Training', 'Weight Loss']),
            'specialization' => 'HIIT & Strength',
            'rating' => 4.9,
            'experience_years' => 4,
            'experience' => 4,
            'hourly_rate' => 1500.00,
            'available' => 1,
            'certifications' => json_encode(['NASM-CPT', 'HIIT Specialist', 'Athletic Performance Coach']),
            'location' => 'New Delhi, India',
            'image_url' => 'https://images.unsplash.com/photo-1494790108755-2616c5e5166c?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
            'phone' => '+91-87654-32109'
        ],
        [
            'name' => 'Ritika Kumawat',
            'email' => 'ritika.kumawat@fitvibe.com',
            'bio' => 'Experienced dance fitness instructor from Kolkata. Combines traditional Indian dance forms with modern fitness.',
            'specialties' => json_encode(['Dance Fitness', 'Zumba', 'Bollywood Dance', 'Cardio']),
            'specialization' => 'Dance Fitness',
            'rating' => 4.7,
            'experience_years' => 5,
            'experience' => 5,
            'hourly_rate' => 1000.00,
            'available' => 1,
            'certifications' => json_encode(['Zumba Instructor', 'Dance Fitness Specialist', 'Bollywood Dance Teacher']),
            'location' => 'Kolkata, West Bengal, India',
            'image_url' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
            'phone' => '+91-76543-21098'
        ]
    ];
    
    $added = 0;
    $skipped = 0;
    
    foreach ($trainers as $trainer) {
        // Check if trainer already exists (try different possible column names)
        $checkQuery = "SELECT id FROM trainers WHERE ";
        if (in_array('email', $tableColumns)) {
            $checkQuery .= "email = ?";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bind_param("s", $trainer['email']);
        } else if (in_array('name', $tableColumns)) {
            $checkQuery .= "name = ?";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bind_param("s", $trainer['name']);
        } else {
            // Use the first text column we can find
            $checkQuery .= "1 = 0"; // Skip check if no identifiable column
            $checkStmt = $conn->prepare($checkQuery);
        }
        
        if ($checkStmt) {
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            
            if ($result->num_rows > 0) {
                echo "<p style='color: orange;'>⚠️ Trainer {$trainer['name']} already exists. Skipping...</p>";
                $skipped++;
                continue;
            }
        }
        
        // Build dynamic insert query based on available columns
        $insertColumns = [];
        $insertValues = [];
        $insertParams = [];
        $paramTypes = "";
        
        foreach ($trainer as $key => $value) {
            if (in_array($key, $tableColumns)) {
                $insertColumns[] = $key;
                $insertValues[] = "?";
                $insertParams[] = $value;
                
                // Determine parameter type
                if (is_string($value)) {
                    $paramTypes .= "s";
                } else if (is_float($value)) {
                    $paramTypes .= "d";
                } else if (is_int($value)) {
                    $paramTypes .= "i";
                } else {
                    $paramTypes .= "s";
                }
            }
        }
        
        if (empty($insertColumns)) {
            echo "<p style='color: red;'>❌ No matching columns found for trainer {$trainer['name']}</p>";
            continue;
        }
        
        $insertQuery = "INSERT INTO trainers (" . implode(', ', $insertColumns) . ") VALUES (" . implode(', ', $insertValues) . ")";
        $insertStmt = $conn->prepare($insertQuery);
        
        if ($insertStmt && $insertStmt->bind_param($paramTypes, ...$insertParams)) {
            if ($insertStmt->execute()) {
                echo "<p style='color: green;'>✅ Added trainer: {$trainer['name']} (columns: " . implode(', ', $insertColumns) . ")</p>";
                $added++;
            } else {
                echo "<p style='color: red;'>❌ Failed to add trainer: {$trainer['name']} - Error: " . $insertStmt->error . "</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Failed to prepare statement for trainer: {$trainer['name']}</p>";
        }
    }
    
    echo "<br><h2>Summary:</h2>";
    echo "<p><strong>✅ Trainers Added:</strong> $added</p>";
    echo "<p><strong>⚠️ Trainers Skipped:</strong> $skipped</p>";
    
    // Show current trainers
    echo "<br><h2>Current Trainers in Database:</h2>";
    $result = $conn->query("SELECT * FROM trainers ORDER BY id");
    
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
        
        // Table header
        $fields = $result->fetch_fields();
        echo "<tr style='background: #22c55e; color: white;'>";
        foreach ($fields as $field) {
            echo "<th>" . ucfirst($field->name) . "</th>";
        }
        echo "</tr>";
        
        // Table data
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                if (strlen($value) > 100) {
                    $value = substr($value, 0, 100) . "...";
                }
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No trainers found in the database.</p>";
    }
    
    echo "<br><div style='margin-top: 2rem; padding: 1rem; background: #e8f5e8; border: 1px solid #4CAF50; border-radius: 5px;'>";
    echo "<h3>🎉 Trainer addition process completed!</h3>";
    echo "<p>Check your trainer page: <a href='?page=trainer'>http://localhost/gymmm/?page=trainer</a></p>";
    echo "<p>Debug info: <a href='check-trainers-table.php'>View table structure</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database Error: " . $e->getMessage() . "</p>";
    echo "<p>Please run: <a href='check-trainers-table.php'>Check table structure first</a></p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Trainers (Fixed) - RKT FitVibe</title>
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
        }
        th { background: #22c55e; color: white; }
        td { background: white; }
        tr:nth-child(even) td { background: #f9f9f9; }
        a { color: #22c55e; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
</body>
</html>
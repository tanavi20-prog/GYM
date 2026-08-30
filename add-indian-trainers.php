<?php
// Script to add Indian trainers to the database
require_once 'crud/connect.php';

try {
    $conn = getConnection();
    
    echo "<h1>Adding Indian Trainers to Database</h1>";
    
    // Check if trainers table exists
    $result = $conn->query("SHOW TABLES LIKE 'trainers'");
    if ($result->num_rows == 0) {
        echo "<p style='color: red;'>❌ Trainers table doesn't exist. Please run database setup first.</p>";
        exit;
    }
    
    // Indian trainer data with realistic details
    $indianTrainers = [
        [
            'name' => 'Tanavi Desai',
            'email' => 'tanavi.desai@fitvibe.com',
            'bio' => 'Certified yoga instructor and wellness coach from Surat. Specializes in traditional Hatha Yoga and modern fitness fusion. Helped over 150+ clients achieve their wellness goals through mindful movement and nutrition guidance.',
            'specialties' => json_encode(['Yoga', 'Pilates', 'Mindfulness', 'Wellness Coaching']),
            'rating' => 4.8,
            'experience_years' => 6,
            'hourly_rate' => 1200.00, // INR
            'available' => 1,
            'certifications' => json_encode(['RYT-500 Yoga Alliance', 'Pilates Instructor', 'Wellness Coach', 'Nutrition Consultant']),
            'location' => 'Surat, Gujarat, India',
            'image_url' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
            'phone' => '+91-98765-43210'
        ],
        [
            'name' => 'Khushi Chauhan', 
            'email' => 'khushi.chauhan@fitvibe.com',
            'bio' => 'Dynamic fitness trainer from Delhi specializing in HIIT and strength training. Former state-level athlete turned fitness coach. Known for her motivational training style and results-driven approach.',
            'specialties' => json_encode(['HIIT', 'Strength Training', 'Athletic Training', 'Weight Loss']),
            'rating' => 4.9,
            'experience_years' => 4,
            'hourly_rate' => 1500.00, // INR
            'available' => 1,
            'certifications' => json_encode(['NASM-CPT', 'HIIT Specialist', 'Athletic Performance Coach', 'Sports Nutrition']),
            'location' => 'New Delhi, India',
            'image_url' => 'https://images.unsplash.com/photo-1494790108755-2616c5e5166c?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
            'phone' => '+91-87654-32109'
        ],
        [
            'name' => 'Ritika Kumawat',
            'email' => 'ritika.kumawat@fitvibe.com', 
            'bio' => 'Experienced dance fitness instructor from Kolkata. Combines traditional Indian dance forms with modern fitness routines. Creates fun, energetic workouts that make fitness feel like celebration.',
            'specialties' => json_encode(['Dance Fitness', 'Zumba', 'Bollywood Dance', 'Cardio']),
            'rating' => 4.7,
            'experience_years' => 5,
            'hourly_rate' => 1000.00, // INR
            'available' => 1,
            'certifications' => json_encode(['Zumba Instructor', 'Dance Fitness Specialist', 'Bollywood Dance Teacher', 'Group Fitness Instructor']),
            'location' => 'Kolkata, West Bengal, India',
            'image_url' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
            'phone' => '+91-76543-21098'
        ],
        [
            'name' => 'Priya Sharma',
            'email' => 'priya.sharma@fitvibe.com',
            'bio' => 'Holistic fitness coach from Mumbai specializing in women\'s fitness and prenatal training. Believes in empowering women through fitness and has trained over 200+ women in achieving their health goals.',
            'specialties' => json_encode(['Women\'s Fitness', 'Prenatal Training', 'Postnatal Recovery', 'Functional Training']),
            'rating' => 4.8,
            'experience_years' => 7,
            'hourly_rate' => 1800.00, // INR
            'available' => 1,
            'certifications' => json_encode(['Women\'s Fitness Specialist', 'Prenatal Exercise Specialist', 'Postnatal Corrective Exercise', 'Functional Movement Screen']),
            'location' => 'Mumbai, Maharashtra, India',
            'image_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
            'phone' => '+91-98765-43211'
        ],
        [
            'name' => 'Ananya Patel',
            'email' => 'ananya.patel@fitvibe.com',
            'bio' => 'Certified nutritionist and fitness trainer from Ahmedabad. Specializes in weight management and lifestyle coaching. Combines Indian dietary wisdom with modern fitness science.',
            'specialties' => json_encode(['Weight Management', 'Nutrition Coaching', 'Lifestyle Training', 'Metabolic Training']),
            'rating' => 4.6,
            'experience_years' => 3,
            'hourly_rate' => 1300.00, // INR
            'available' => 1,
            'certifications' => json_encode(['Certified Nutritionist', 'Lifestyle Coach', 'Weight Management Specialist', 'Metabolic Conditioning']),
            'location' => 'Ahmedabad, Gujarat, India',
            'image_url' => 'https://images.unsplash.com/photo-1607746882042-944635dfe10e?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
            'phone' => '+91-87654-32110'
        ]
    ];
    
    // Prepare the insert statement
    $stmt = $conn->prepare("INSERT INTO trainers (name, email, bio, specialties, rating, experience_years, hourly_rate, available, certifications, location, image_url, phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $added = 0;
    $skipped = 0;
    
    foreach ($indianTrainers as $trainer) {
        // Check if trainer already exists
        $checkStmt = $conn->prepare("SELECT id FROM trainers WHERE email = ? OR name = ?");
        $checkStmt->bind_param("ss", $trainer['email'], $trainer['name']);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result->num_rows > 0) {
            echo "<p style='color: orange;'>⚠️ Trainer {$trainer['name']} already exists. Skipping...</p>";
            $skipped++;
            continue;
        }
        
        // Insert new trainer
        if ($stmt->bind_param("ssssdisissss", 
            $trainer['name'],
            $trainer['email'], 
            $trainer['bio'],
            $trainer['specialties'],
            $trainer['rating'],
            $trainer['experience_years'],
            $trainer['hourly_rate'],
            $trainer['available'],
            $trainer['certifications'],
            $trainer['location'],
            $trainer['image_url'],
            $trainer['phone']
        )) {
            if ($stmt->execute()) {
                echo "<p style='color: green;'>✅ Added trainer: {$trainer['name']} from {$trainer['location']}</p>";
                $added++;
            } else {
                echo "<p style='color: red;'>❌ Failed to add trainer: {$trainer['name']} - Error: " . $stmt->error . "</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Failed to bind parameters for trainer: {$trainer['name']}</p>";
        }
    }
    
    echo "<br><h2>Summary:</h2>";
    echo "<p><strong>✅ Trainers Added:</strong> $added</p>";
    echo "<p><strong>⚠️ Trainers Skipped (already exist):</strong> $skipped</p>";
    
    // Show all current trainers
    echo "<br><h2>Current Trainers in Database:</h2>";
    $result = $conn->query("SELECT id, name, location, specialties, rating, experience_years FROM trainers ORDER BY name");
    
    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'><th>ID</th><th>Name</th><th>Location</th><th>Specialties</th><th>Rating</th><th>Experience</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            $specialties = json_decode($row['specialties'], true);
            $specialtyList = is_array($specialties) ? implode(', ', $specialties) : 'N/A';
            
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td><strong>{$row['name']}</strong></td>";
            echo "<td>{$row['location']}</td>";
            echo "<td>{$specialtyList}</td>";
            echo "<td>⭐ {$row['rating']}</td>";
            echo "<td>{$row['experience_years']} years</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<br><div style='margin-top: 2rem; padding: 1rem; background: #e8f5e8; border: 1px solid #4CAF50; border-radius: 5px;'>";
    echo "<h3>🎉 Success! Indian trainers have been added to your database.</h3>";
    echo "<p>You can now see them on your trainer page at: <a href='?page=trainer'>http://localhost/gymmm/?page=trainer</a></p>";
    echo "<p>To view workout videos: <a href='?page=trainer&show_videos=1'>http://localhost/gymmm/?page=trainer&show_videos=1</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database Error: " . $e->getMessage() . "</p>";
    echo "<p>Make sure your WAMP server is running and the database connection is working.</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Indian Trainers - RKT FitVibe</title>
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
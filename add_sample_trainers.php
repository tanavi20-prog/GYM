<?php
// Add sample trainers to database for testing
require_once 'crud/connect.php';

echo "<h1>➕ Add Sample Trainers to Database</h1>\n";

try {
    $conn = getConnection();
    echo "<p style='color: green;'>✅ Database connection successful</p>\n";
    
    // Check if trainers already exist
    $result = $conn->query("SELECT COUNT(*) as count FROM trainers");
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        echo "<p>Found {$row['count']} existing trainers in database.</p>\n";
        echo "<p>Showing existing trainers:</p>\n";
        $result = $conn->query("SELECT id, name, available, hourly_rate FROM trainers");
        echo "<ul>\n";
        while ($trainer = $result->fetch_assoc()) {
            $status = $trainer['available'] ? 'Available' : 'Not Available';
            echo "<li>ID: {$trainer['id']}, Name: {$trainer['name']}, Status: $status, Rate: ₹{$trainer['hourly_rate']}</li>\n";
        }
        echo "</ul>\n";
        
        echo "<p><a href='debug_new_user_trainer_issue.php'>Run Debug Script</a> to check if trainers display properly</p>\n";
        exit;
    }
    
    // Add sample trainers
    $sampleTrainers = [
        [
            'name' => 'Rahul Sharma',
            'email' => 'rahul@gymmm.com',
            'bio' => 'Certified fitness trainer with 8+ years of experience in strength training and weight loss programs.',
            'specialties' => json_encode(['Strength Training', 'Weight Loss', 'HIIT']),
            'rating' => 4.8,
            'total_clients' => 156,
            'experience_years' => 8,
            'hourly_rate' => 800,
            'available' => 1,
            'certifications' => json_encode(['NASM-CPT', 'ACE']),
            'youtube_video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'location' => 'Mumbai, India'
        ],
        [
            'name' => 'Priya Patel',
            'email' => 'priya@gymmm.com',
            'bio' => 'Expert in yoga and meditation with focus on mental wellness and flexibility training.',
            'specialties' => json_encode(['Yoga', 'Meditation', 'Flexibility']),
            'rating' => 4.9,
            'total_clients' => 203,
            'experience_years' => 6,
            'hourly_rate' => 700,
            'available' => 1,
            'certifications' => json_encode(['RYT-200', 'Mindfulness Instructor']),
            'youtube_video' => 'https://www.youtube.com/watch?v=abcdef12345',
            'location' => 'Delhi, India'
        ],
        [
            'name' => 'Amit Kumar',
            'email' => 'amit@gymmm.com',
            'bio' => 'Sports nutrition specialist and personal trainer focused on athletic performance and bodybuilding.',
            'specialties' => json_encode(['Bodybuilding', 'Sports Nutrition', 'Athletic Training']),
            'rating' => 4.7,
            'total_clients' => 89,
            'experience_years' => 5,
            'hourly_rate' => 900,
            'available' => 1,
            'certifications' => json_encode(['ISSA', 'Sports Nutritionist']),
            'youtube_video' => 'https://www.youtube.com/watch?v=xyz789',
            'location' => 'Bangalore, India'
        ],
        [
            'name' => 'Sneha Reddy',
            'email' => 'sneha@gymmm.com',
            'bio' => 'Prenatal and postnatal fitness specialist helping new mothers regain strength and fitness.',
            'specialties' => json_encode(['Prenatal Fitness', 'Postnatal Recovery', 'Functional Training']),
            'rating' => 4.6,
            'total_clients' => 134,
            'experience_years' => 4,
            'hourly_rate' => 750,
            'available' => 1,
            'certifications' => json_encode(['Pre/Postnatal Certified', 'Functional Movement']),
            'youtube_video' => 'https://www.youtube.com/watch?v=pqr456',
            'location' => 'Hyderabad, India'
        ]
    ];
    
    echo "<h2>Adding Sample Trainers:</h2>\n";
    
    $stmt = $conn->prepare("INSERT INTO trainers (name, email, bio, specialties, rating, total_clients, experience_years, hourly_rate, available, certifications, youtube_video, location) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $addedCount = 0;
    foreach ($sampleTrainers as $trainer) {
        $stmt->bind_param("ssssdiiissss", 
            $trainer['name'],
            $trainer['email'],
            $trainer['bio'],
            $trainer['specialties'],
            $trainer['rating'],
            $trainer['total_clients'],
            $trainer['experience_years'],
            $trainer['hourly_rate'],
            $trainer['available'],
            $trainer['certifications'],
            $trainer['youtube_video'],
            $trainer['location']
        );
        
        if ($stmt->execute()) {
            $addedCount++;
            echo "<p style='color: green;'>✅ Added: {$trainer['name']}</p>\n";
        } else {
            echo "<p style='color: red;'>❌ Failed to add: {$trainer['name']} - " . $stmt->error . "</p>\n";
        }
    }
    
    $stmt->close();
    
    echo "<h2>Summary:</h2>\n";
    echo "<p>Successfully added $addedCount trainers to the database.</p>\n";
    
    // Verify the trainers were added
    $result = $conn->query("SELECT COUNT(*) as count FROM trainers");
    $row = $result->fetch_assoc();
    echo "<p>Total trainers in database now: {$row['count']}</p>\n";
    
    echo "<h2>Next Steps:</h2>\n";
    echo "<p>1. <a href='debug_new_user_trainer_issue.php'>Run Debug Script</a> to verify trainers display properly</p>\n";
    echo "<p>2. <a href='pages/trainer.php'>Visit Trainer Page</a> to see trainers directly</p>\n";
    echo "<p>3. <a href='pages/registration.php'>Register a New User</a> and test the flow</p>\n";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>\n";
    echo "<p>Make sure your database is set up correctly and the trainers table exists.</p>\n";
}
?>
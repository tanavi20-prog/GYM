<?php
// Test adding a trainer directly to database
require_once 'crud/connect.php';

echo "Testing trainer addition...\n";

try {
    $conn = getConnection();
    echo "✓ Database connection successful\n";
    
    // Add a test trainer
    $name = "Test Trainer";
    $email = "test@trainer.com";
    $bio = "This is a test trainer for verification purposes.";
    $specialties = json_encode(["Test Training", "Demo Fitness"]);
    $experience_years = 2;
    $hourly_rate = 45.00;
    $rating = 4.8;
    $available = 1;
    $location = "Test City, TC";
    $image_url = "default.jpg";
    
    $sql = "INSERT INTO trainers (name, email, specialties, bio, image_url, experience_years, hourly_rate, rating, available, location) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssdiidi", $name, $email, $specialties, $bio, $image_url, $experience_years, $hourly_rate, $rating, $available, $location);
    
    if ($stmt->execute()) {
        $trainer_id = $stmt->insert_id;
        echo "✓ Successfully added test trainer. ID: {$trainer_id}\n";
        
        // Verify the trainer was added correctly
        $result = $conn->query("SELECT * FROM trainers WHERE id = {$trainer_id}");
        if ($result && $result->num_rows > 0) {
            $trainer = $result->fetch_assoc();
            echo "✓ Trainer data verified:\n";
            echo "  Name: {$trainer['name']}\n";
            echo "  Email: {$trainer['email']}\n";
            echo "  Available: " . ($trainer['available'] ? 'Yes' : 'No') . "\n";
            echo "  Rating: {$trainer['rating']}\n";
        }
    } else {
        echo "✗ Failed to add test trainer: " . $conn->error . "\n";
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "Test completed.\n";
?>
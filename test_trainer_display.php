<?php
// Test script to verify trainer display for non-logged-in users
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/helpers.php';
require_once '../crud/connect.php';

echo "<h1>Trainer Display Test for Non-Logged-In Users</h1>";

// Simulate non-logged-in state
session_start();
if (isset($_SESSION['logged_in'])) {
    unset($_SESSION['logged_in']);
}
if (isset($_SESSION['user_id'])) {
    unset($_SESSION['user_id']);
}

echo "<p>Session state simulated as non-logged-in user</p>";

// Replicate the trainer loading logic from trainer.php
$trainers = [];

try {
    $conn = getConnection();
    $result = $conn->query("SELECT t.*, 
                                   COUNT(v.id) as video_count,
                                   GROUP_CONCAT(DISTINCT vc.name) as video_categories
                            FROM trainers t 
                            LEFT JOIN videos v ON t.name = v.instructor_name AND v.is_active = 1
                            LEFT JOIN video_categories vc ON v.category_id = vc.id
                            GROUP BY t.id
                            ORDER BY t.rating DESC, t.experience_years DESC");

    while ($row = $result->fetch_assoc()) {
        // Only show available trainers
        if (!isset($row['available']) || !$row['available']) {
            continue;
        }
        
        // Handle Specialties correctly (JSON or Comma-separated)
        $specialtiesRaw = $row['specialties'];
        $specialtiesArray = [];

        if (!empty($specialtiesRaw)) {
            // Try to decode as JSON
            $decoded = json_decode($specialtiesRaw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $specialtiesArray = $decoded;
            } else {
                // If not JSON, explode by comma
                $specialtiesArray = explode(',', $specialtiesRaw);
            }
        } else {
            // Fallback if empty
            $specialtiesArray = ['Personal Training']; 
        }

        // Clean up whitespace
        $specialtiesArray = array_map('trim', $specialtiesArray);

        $trainers[] = [
            'id' => $row['id'],
            'name' => function_exists('format_name') ? format_name($row['name']) : $row['name'],
            'specialties' => $specialtiesArray, 
            'rating' => (float)($row['rating'] ?? 5.0),
            'review_count' => rand(50, 200), // Mock data
            'clients' => rand(80, 250), // Mock data
            'experience' => ($row['experience_years'] ?? 1) . '+ years',
            'location' => !empty($row['location']) ? $row['location'] : 'Location not specified',
            'hourly_rate' => (float)($row['hourly_rate'] ?? 50.00),
            'available' => (bool)($row['available'] ?? true),
            'bio' => !empty($row['bio']) ? $row['bio'] : 'Experienced fitness professional',
            'certifications' => ['Certified Professional'], // Mock data
            'languages' => ['English'], 
            'image' => !empty($row['image_url']) ? $row['image_url'] : 'https://images.unsplash.com/photo-1594736797933-d0501ba2fe65?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
            'video_count' => (int)($row['video_count'] ?? 0),
            'video_categories' => $row['video_categories'] ?? '',
            'youtube_video' => $row['youtube_video'] ?? ''
        ];
    }
} catch (Exception $e) {
    error_log("Failed to load trainers: " . $e->getMessage());
}

echo "<h2>Trainers loaded: " . count($trainers) . "</h2>";

if (empty($trainers)) {
    echo "<p style='color: red;'>No trainers available! This confirms the issue.</p>";
    
    // Check the raw database
    try {
        $conn = getConnection();
        $result = $conn->query("SELECT COUNT(*) as total FROM trainers");
        $row = $result->fetch_assoc();
        echo "<p>Total trainers in database: " . $row['total'] . "</p>";
        
        if ($row['total'] > 0) {
            $result = $conn->query("SELECT COUNT(*) as available FROM trainers WHERE available = 1");
            $row = $result->fetch_assoc();
            echo "<p>Available trainers (available = 1): " . $row['available'] . "</p>";
        }
    } catch (Exception $e) {
        echo "<p>Error checking database: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: green;'>Trainers are loaded and should be displayed!</p>";
    echo "<h3>Sample trainers that should display:</h3>";
    foreach (array_slice($trainers, 0, 3) as $trainer) {
        echo "<p><strong>" . $trainer['name'] . "</strong> - " . $trainer['location'] . " - " . usd_to_inr_formatted($trainer['hourly_rate']) . "/hr</p>";
    }
}

// Check if user is considered logged in
echo "<h2>Current session status:</h2>";
echo "<p>is_logged_in(): " . (is_logged_in() ? 'YES' : 'NO') . "</p>";
echo "<p>Session data:</p><pre>" . print_r($_SESSION, true) . "</pre>";

echo "<h3><a href='?page=trainer'>Visit Trainer Page</a></h3>";
echo "<h3><a href='?page=registration'>Register New User</a></h3>";
?>
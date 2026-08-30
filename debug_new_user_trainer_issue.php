<?php
// Debug script to identify why trainers aren't showing for new users
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
require_once 'crud/connect.php';

echo "<h1>🔍 Debug: New User Trainer Display Issue</h1>\n";

try {
    $conn = getConnection();
    echo "<p style='color: green;'>✅ Database connection successful</p>\n";
    
    // 1. Check if there are trainers in the database
    echo "<h2>1. Trainers Database Check</h2>\n";
    $result = $conn->query("SELECT COUNT(*) as count FROM trainers");
    $row = $result->fetch_assoc();
    echo "<p>Total trainers in database: {$row['count']}</p>\n";
    
    if ($row['count'] > 0) {
        echo "<p style='color: green;'>✅ Trainers exist in database</p>\n";
        // Show sample trainers
        $result = $conn->query("SELECT id, name, available, hourly_rate FROM trainers LIMIT 3");
        echo "<p>Sample trainers:</p><ul>\n";
        while ($trainer = $result->fetch_assoc()) {
            $status = $trainer['available'] ? 'Available' : 'Not Available';
            echo "<li>ID: {$trainer['id']}, Name: {$trainer['name']}, Status: $status, Rate: ₹{$trainer['hourly_rate']}</li>\n";
        }
        echo "</ul>\n";
    } else {
        echo "<p style='color: red;'>❌ No trainers found in database!</p>\n";
        echo "<p>This explains why no trainers are showing.</p>\n";
    }
    
    // 2. Check user authentication status
    echo "<h2>2. Current User Session Status</h2>\n";
    if (is_logged_in()) {
        $currentUserId = get_current_user_id();
        echo "<p style='color: green;'>✅ User is logged in (ID: $currentUserId)</p>\n";
        
        $user = get_logged_in_user();
        if ($user) {
            echo "<p>User name: {$user['name']}</p>\n";
            echo "<p>User email: {$user['email']}</p>\n";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ No user is currently logged in</p>\n";
        echo "<p>A new user needs to register and log in first.</p>\n";
    }
    
    // 3. Test trainer display logic
    echo "<h2>3. Trainer Display Logic Test</h2>\n";
    
    // Simulate what trainer.php does
    $trainers = [];
    $result = $conn->query("SELECT t.*, 
                                   COUNT(v.id) as video_count,
                                   GROUP_CONCAT(DISTINCT vc.name) as video_categories
                            FROM trainers t 
                            LEFT JOIN videos v ON t.name = v.instructor_name AND v.is_active = 1
                            LEFT JOIN video_categories vc ON v.category_id = vc.id
                            GROUP BY t.id
                            ORDER BY t.rating DESC, t.experience_years DESC");
    
    if ($result) {
        echo "<p>Query executed successfully</p>\n";
        $trainerCount = 0;
        while ($row = $result->fetch_assoc()) {
            // Check if trainer is available (this is the key filter)
            if (!isset($row['available']) || !$row['available']) {
                echo "<p>Skipping trainer {$row['name']} - not available</p>\n";
                continue;
            }
            
            $trainers[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'available' => (bool)($row['available'] ?? true),
                'hourly_rate' => (float)($row['hourly_rate'] ?? 50.00)
            ];
            $trainerCount++;
        }
        echo "<p>Available trainers found: $trainerCount</p>\n";
        
        if (empty($trainers)) {
            echo "<p style='color: red;'>❌ No available trainers found after filtering!</p>\n";
            echo "<p>Possible causes:</p>\n";
            echo "<ul>\n";
            echo "<li>Trainers exist but 'available' column is set to 0/false</li>\n";
            echo "<li>Database connection issue</li>\n";
            echo "<li>Query filtering is too restrictive</li>\n";
            echo "</ul>\n";
        } else {
            echo "<p style='color: green;'>✅ Found {$trainerCount} available trainers</p>\n";
            foreach ($trainers as $trainer) {
                echo "<p>• {$trainer['name']} (ID: {$trainer['id']}) - ₹{$trainer['hourly_rate']}/hour</p>\n";
            }
        }
    } else {
        echo "<p style='color: red;'>❌ Query failed: " . $conn->error . "</p>\n";
    }
    
    // 4. Check session variables
    echo "<h2>4. Session Variables Check</h2>\n";
    echo "<p>Session started: " . (session_status() === PHP_SESSION_ACTIVE ? 'Yes' : 'No') . "</p>\n";
    echo "<p>Session ID: " . session_id() . "</p>\n";
    echo "<p>Session data:</p>\n";
    echo "<pre>" . print_r($_SESSION, true) . "</pre>\n";
    
    // 5. Recommendations
    echo "<h2>5. Troubleshooting Recommendations</h2>\n";
    echo "<ol>\n";
    echo "<li><strong>Check if trainers are marked as available:</strong> Run database query to verify 'available' column values</li>\n";
    echo "<li><strong>Verify new user registration:</strong> Make sure the registration process completes successfully</li>\n";
    echo "<li><strong>Test user login:</strong> Ensure the user can log in after registration</li>\n";
    echo "<li><strong>Check trainer.php directly:</strong> Visit the trainer page to see if trainers display there</li>\n";
    echo "<li><strong>Database setup:</strong> Run setup scripts if trainers table is empty</li>\n";
    echo "</ol>\n";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>\n";
}

echo "<h2>Quick Fix Options:</h2>\n";
echo "<p><a href='dev-tools/setup/add_sample_trainer_videos.php' target='_blank'>Add Sample Trainers</a> - Run this to populate the trainers table</p>\n";
echo "<p><a href='pages/trainer.php' target='_blank'>View Trainers Page</a> - Check if trainers display directly</p>\n";
echo "<p><a href='pages/registration.php' target='_blank'>Register New User</a> - Test the registration flow</p>\n";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Trainer Display Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .status { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .trainer-card { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 6px; background: #fafafa; }
        .trainer-name { font-size: 18px; font-weight: bold; color: #333; }
        .trainer-info { color: #666; margin: 5px 0; }
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 5px; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏋️ Trainer Display Diagnostic</h1>
        
        <?php
        require_once 'crud/connect.php';
        
        try {
            $conn = getConnection();
            echo "<div class='status success'>✅ Database connection successful</div>";
            
            // Check trainers
            $result = $conn->query("SELECT COUNT(*) as count FROM trainers");
            $row = $result->fetch_assoc();
            $totalTrainers = $row['count'];
            
            echo "<h2>📊 Database Status</h2>";
            echo "<p><strong>Total trainers:</strong> $totalTrainers</p>";
            
            if ($totalTrainers == 0) {
                echo "<div class='status error'>❌ No trainers found in database!</div>";
                echo "<p>This is why trainers aren't showing. You need to add trainers first.</p>";
                echo "<a href='add_sample_trainers.php' class='btn'>Add Sample Trainers Now</a>";
            } else {
                // Check available trainers
                $result = $conn->query("SELECT COUNT(*) as count FROM trainers WHERE available = 1");
                $row = $result->fetch_assoc();
                $availableTrainers = $row['count'];
                
                echo "<p><strong>Available trainers:</strong> $availableTrainers</p>";
                
                if ($availableTrainers == 0) {
                    echo "<div class='status warning'>⚠️ Trainers exist but none are marked as available</div>";
                    echo "<p>Trainers need to have 'available = 1' to be displayed.</p>";
                    echo "<a href='fix_trainer_availability.php' class='btn'>Fix Availability</a>";
                } else {
                    echo "<div class='status success'>✅ Found $availableTrainers available trainers</div>";
                    
                    // Show trainers
                    echo "<h2>📋 Available Trainers</h2>";
                    $result = $conn->query("SELECT id, name, hourly_rate, location FROM trainers WHERE available = 1 ORDER BY name");
                    
                    while ($trainer = $result->fetch_assoc()) {
                        echo "<div class='trainer-card'>";
                        echo "<div class='trainer-name'>{$trainer['name']}</div>";
                        echo "<div class='trainer-info'>📍 {$trainer['location']}</div>";
                        echo "<div class='trainer-info'>💰 ₹{$trainer['hourly_rate']}/hour</div>";
                        echo "<div class='trainer-info'>ID: {$trainer['id']}</div>";
                        echo "</div>";
                    }
                }
            }
            
            // Check session
            session_start();
            echo "<h2>👤 Session Status</h2>";
            echo "<p><strong>Session active:</strong> " . (session_status() === PHP_SESSION_ACTIVE ? 'Yes' : 'No') . "</p>";
            echo "<p><strong>User logged in:</strong> " . (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'Yes' : 'No') . "</p>";
            
            if (isset($_SESSION['user_id'])) {
                echo "<p><strong>Current user ID:</strong> {$_SESSION['user_id']}</p>";
            }
            
        } catch (Exception $e) {
            echo "<div class='status error'>❌ Database error: " . $e->getMessage() . "</div>";
        }
        ?>
        
        <h2>🔧 Quick Actions</h2>
        <a href="add_sample_trainers.php" class="btn">Add Sample Trainers</a>
        <a href="pages/registration.php" class="btn">Register New User</a>
        <a href="pages/login.php" class="btn">Login Page</a>
        <a href="pages/trainer.php" class="btn">View Trainers Page</a>
        <a href="pages/dashboard.php" class="btn">View Dashboard</a>
        
        <h2>❓ Troubleshooting Steps</h2>
        <ol>
            <li>If no trainers show above → Click "Add Sample Trainers"</li>
            <li>If trainers exist but show as "Not Available" → Click "Fix Availability"</li>
            <li>Register a new user and test the flow</li>
            <li>Check browser console for JavaScript errors</li>
            <li>Clear browser cache and cookies</li>
        </ol>
    </div>
</body>
</html>
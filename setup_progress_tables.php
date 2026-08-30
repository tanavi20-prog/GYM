<?php
// Simple setup script to create progress tracking tables
require_once 'crud/connect.php';

echo "<h1>📊 Progress Tracking Tables Setup</h1>\n";

try {
    $conn = getConnection();
    
    // Check if tables already exist
    $tables_to_check = ['user_meals', 'user_workouts', 'user_progress', 'user_streaks'];
    $existing_tables = [];
    
    foreach ($tables_to_check as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            $existing_tables[] = $table;
        }
    }
    
    if (count($existing_tables) == count($tables_to_check)) {
        echo "<p style='color: green;'>✅ All progress tracking tables already exist!</p>\n";
        echo "<p>Existing tables: " . implode(', ', $existing_tables) . "</p>\n";
    } else {
        echo "<p>Creating progress tracking tables...</p>\n";
        
        // Create user_meals table
        $sql = "CREATE TABLE IF NOT EXISTS user_meals (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            user_id INT(11) NOT NULL,
            meal_type ENUM('breakfast', 'lunch', 'dinner', 'snack') NOT NULL,
            meal_name VARCHAR(255) NOT NULL,
            calories INT(6) DEFAULT 0,
            protein_g DECIMAL(5,2) DEFAULT 0,
            carbs_g DECIMAL(5,2) DEFAULT 0,
            fat_g DECIMAL(5,2) DEFAULT 0,
            date DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_date (user_id, date),
            INDEX idx_date (date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        if ($conn->query($sql)) {
            echo "<p style='color: green;'>✅ Created user_meals table</p>\n";
        } else {
            echo "<p style='color: red;'>❌ Error creating user_meals table: " . $conn->error . "</p>\n";
        }
        
        // Create user_workouts table
        $sql = "CREATE TABLE IF NOT EXISTS user_workouts (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            user_id INT(11) NOT NULL,
            trainer_id INT(11) DEFAULT NULL,
            workout_type VARCHAR(100) NOT NULL,
            duration_minutes INT(4) NOT NULL,
            calories_burned INT(6) DEFAULT 0,
            date DATE NOT NULL,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (trainer_id) REFERENCES trainers(id) ON DELETE SET NULL,
            INDEX idx_user_date (user_id, date),
            INDEX idx_date (date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        if ($conn->query($sql)) {
            echo "<p style='color: green;'>✅ Created user_workouts table</p>\n";
        } else {
            echo "<p style='color: red;'>❌ Error creating user_workouts table: " . $conn->error . "</p>\n";
        }
        
        // Create user_progress table
        $sql = "CREATE TABLE IF NOT EXISTS user_progress (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            user_id INT(11) NOT NULL,
            metric_type ENUM('weight', 'body_fat', 'muscle_mass', 'waist') NOT NULL,
            value DECIMAL(6,2) NOT NULL,
            date DATE NOT NULL,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_metric (user_id, metric_type, date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        if ($conn->query($sql)) {
            echo "<p style='color: green;'>✅ Created user_progress table</p>\n";
        } else {
            echo "<p style='color: red;'>❌ Error creating user_progress table: " . $conn->error . "</p>\n";
        }
        
        // Create user_streaks table
        $sql = "CREATE TABLE IF NOT EXISTS user_streaks (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            user_id INT(11) NOT NULL,
            streak_type ENUM('workout', 'diet', 'active_days') NOT NULL,
            current_streak INT(5) DEFAULT 0,
            longest_streak INT(5) DEFAULT 0,
            last_activity_date DATE,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_streak (user_id, streak_type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        if ($conn->query($sql)) {
            echo "<p style='color: green;'>✅ Created user_streaks table</p>\n";
        } else {
            echo "<p style='color: red;'>❌ Error creating user_streaks table: " . $conn->error . "</p>\n";
        }
        
        // Insert initial streak records for existing users
        $result = $conn->query("SELECT id FROM users");
        $user_count = 0;
        while ($user = $result->fetch_assoc()) {
            $user_id = $user['id'];
            
            $streak_types = ['workout', 'diet', 'active_days'];
            foreach ($streak_types as $streak_type) {
                $sql = "INSERT IGNORE INTO user_streaks (user_id, streak_type, current_streak, longest_streak) 
                        VALUES (?, ?, 0, 0)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("is", $user_id, $streak_type);
                if ($stmt->execute()) {
                    $user_count++;
                }
            }
        }
        
        echo "<p style='color: green;'>✅ Initialized streak records for users</p>\n";
    }
    
    echo "<h2>Database Status Check</h2>\n";
    
    // Check required tables
    $required_tables = ['users', 'trainers', 'trainer_sessions', 'plans'];
    foreach ($required_tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "<p style='color: green;'>✓ $table table exists</p>\n";
        } else {
            echo "<p style='color: red;'>✗ $table table missing</p>\n";
        }
    }
    
    // Check progress tables
    foreach ($tables_to_check as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "<p style='color: green;'>✓ $table table exists</p>\n";
        } else {
            echo "<p style='color: orange;'>⚠ $table table not created</p>\n";
        }
    }
    
    echo "<h2>Next Steps</h2>\n";
    echo "<ol>\n";
    echo "<li><a href='test_realtime_dashboard.php'>Test the real-time dashboard functionality</a></li>\n";
    echo "<li><a href='?page=dashboard'>View your real-time dashboard</a></li>\n";
    echo "<li><a href='check_db_tables.php'>Check database tables status</a></li>\n";
    echo "</ol>\n";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>\n";
}
?>
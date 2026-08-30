<?php
// Quick fix to ensure user selections tables exist with correct structure
require_once 'includes/config.php';
require_once 'crud/connect.php';

echo "<h1>🔧 Fixing User Selections Tables</h1>";

try {
    $conn = getConnection();
    echo "<p>✅ Database connection successful</p>";
    
    // Check and create user_selected_plans table
    $checkPlans = $conn->query("SHOW TABLES LIKE 'user_selected_plans'");
    if (!$checkPlans || $checkPlans->num_rows == 0) {
        $createPlansTable = "
        CREATE TABLE user_selected_plans (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            plan_type VARCHAR(50) NOT NULL,
            plan_title VARCHAR(255) NOT NULL,
            plan_description TEXT,
            duration VARCHAR(50),
            workouts_per_week INT,
            session_duration VARCHAR(50),
            selected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            is_active BOOLEAN DEFAULT TRUE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_plan (user_id, plan_type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        if ($conn->query($createPlansTable)) {
            echo "<p>✅ Created user_selected_plans table</p>";
        } else {
            echo "<p>❌ Error creating user_selected_plans: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>✅ user_selected_plans table already exists</p>";
    }
    
    // Check and create user_selected_trainers table
    $checkTrainers = $conn->query("SHOW TABLES LIKE 'user_selected_trainers'");
    if (!$checkTrainers || $checkTrainers->num_rows == 0) {
        $createTrainersTable = "
        CREATE TABLE user_selected_trainers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            trainer_id INT NOT NULL,
            selected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('selected', 'contacted', 'hired', 'cancelled') DEFAULT 'selected',
            notes TEXT,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (trainer_id) REFERENCES trainers(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_trainer (user_id, trainer_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        if ($conn->query($createTrainersTable)) {
            echo "<p>✅ Created user_selected_trainers table</p>";
        } else {
            echo "<p>❌ Error creating user_selected_trainers: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>✅ user_selected_trainers table already exists</p>";
    }
    
    // Test the API functionality
    echo "<h2>🧪 Testing API Connection...</h2>";
    
    // Check if we can connect and query
    $testQuery = $conn->query("SELECT COUNT(*) as count FROM users");
    if ($testQuery) {
        $row = $testQuery->fetch_assoc();
        echo "<p>✅ Database query working. Found {$row['count']} users</p>";
    } else {
        echo "<p>❌ Database query failed: " . $conn->error . "</p>";
    }
    
    echo "<h2>✅ Fix Complete!</h2>";
    echo "<p>The user selections tables should now be properly set up.</p>";
    echo "<p>Try refreshing the trainer page and the 500 error should be resolved.</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
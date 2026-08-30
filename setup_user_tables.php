<?php
// Quick database setup for user selections
require_once 'includes/config.php';
require_once 'crud/connect.php';

echo "Setting up user selections tables...\n";

try {
    $conn = getConnection();
    
    // Create user_selected_plans table
    $sql1 = "CREATE TABLE IF NOT EXISTS user_selected_plans (
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
    
    if ($conn->query($sql1)) {
        echo "✓ user_selected_plans table created/verified\n";
    } else {
        echo "✗ Error creating user_selected_plans: " . $conn->error . "\n";
    }
    
    // Create user_selected_trainers table
    $sql2 = "CREATE TABLE IF NOT EXISTS user_selected_trainers (
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
    
    if ($conn->query($sql2)) {
        echo "✓ user_selected_trainers table created/verified\n";
    } else {
        echo "✗ Error creating user_selected_trainers: " . $conn->error . "\n";
    }
    
    // Test the tables
    echo "\nTesting tables...\n";
    
    $result1 = $conn->query("SHOW TABLES LIKE 'user_selected_plans'");
    if ($result1 && $result1->num_rows > 0) {
        echo "✓ user_selected_plans table exists\n";
    } else {
        echo "✗ user_selected_plans table missing\n";
    }
    
    $result2 = $conn->query("SHOW TABLES LIKE 'user_selected_trainers'");
    if ($result2 && $result2->num_rows > 0) {
        echo "✓ user_selected_trainers table exists\n";
    } else {
        echo "✗ user_selected_trainers table missing\n";
    }
    
    echo "\nSetup complete! Try refreshing the trainer page now.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';

echo "<h2>Community Database Test</h2>";

try {
    $db = db();
    if (!$db) {
        echo "<p style='color: red;'>❌ Database connection failed</p>";
        exit;
    }
    echo "<p style='color: green;'>✅ Database connected successfully</p>";
    
    // Check if community table exists
    $tables = $db->fetchAll("SHOW TABLES LIKE 'community'");
    if (empty($tables)) {
        echo "<p style='color: red;'>❌ Community table does not exist</p>";
        
        // Try to create it
        $createSql = "CREATE TABLE IF NOT EXISTS community (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            user_id INT(11) NOT NULL,
            title VARCHAR(200) NOT NULL,
            content TEXT NOT NULL,
            type ENUM('post', 'question', 'achievement', 'tip') DEFAULT 'post',
            category VARCHAR(50) DEFAULT NULL,
            likes_count INT(10) DEFAULT 0,
            comments_count INT(10) DEFAULT 0,
            is_featured BOOLEAN DEFAULT FALSE,
            status ENUM('active', 'hidden', 'reported') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_type (type),
            INDEX idx_status (status),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->query($createSql);
        echo "<p style='color: green;'>✅ Community table created</p>";
    } else {
        echo "<p style='color: green;'>✅ Community table exists</p>";
    }
    
    // Check if we can insert a test post
    $user_id = 1; // Test user ID
    $test_data = [
        ':user_id' => $user_id,
        ':title' => 'Test Post',
        ':content' => 'This is a test post to verify the community system is working.',
        ':category' => 'test'
    ];
    
    $sql = "INSERT INTO community (user_id, title, content, category) VALUES (:user_id, :title, :content, :category)";
    $stmt = $db->query($sql, $test_data);
    $insertId = $db->lastInsertId();
    
    if ($insertId) {
        echo "<p style='color: green;'>✅ Test post inserted successfully (ID: $insertId)</p>";
        
        // Fetch the inserted post
        $post = $db->fetch(
            "SELECT c.id, c.title, c.content, c.category, c.created_at, u.name as author_name FROM community c JOIN users u ON c.user_id = u.id WHERE c.id = ?",
            [$insertId]
        );
        
        if ($post) {
            echo "<p style='color: green;'>✅ Test post retrieved successfully</p>";
            echo "<pre>" . print_r($post, true) . "</pre>";
        } else {
            echo "<p style='color: red;'>❌ Failed to retrieve test post</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Failed to insert test post</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
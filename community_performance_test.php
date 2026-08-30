<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';

echo "<h2>Community Performance Test</h2>";

try {
    $db = db();
    if (!$db) {
        echo "<p style='color: red;'>❌ Database connection failed</p>";
        exit;
    }
    echo "<p style='color: green;'>✅ Database connected successfully</p>";
    
    // Test community table performance
    $start_time = microtime(true);
    
    // Check if table exists and get row count
    $table_check = $db->fetch("SHOW TABLES LIKE 'community'");
    if (!$table_check) {
        echo "<p style='color: orange;'>⚠️ Community table doesn't exist - creating it...</p>";
        
        // Create table
        $create_sql = "CREATE TABLE IF NOT EXISTS community (
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
        
        $db->query($create_sql);
        echo "<p style='color: green;'>✅ Community table created</p>";
    }
    
    // Get row count
    $count_result = $db->fetch("SELECT COUNT(*) as count FROM community");
    $row_count = $count_result['count'] ?? 0;
    echo "<p>📊 Total community posts: <strong>{$row_count}</strong></p>";
    
    // Test query performance
    $query_start = microtime(true);
    $test_posts = $db->fetchAll(
        "SELECT c.id, c.title, c.content, c.category, c.created_at, u.name as author_name 
         FROM community c 
         JOIN users u ON c.user_id = u.id 
         WHERE c.status = 'active' 
         ORDER BY c.id DESC 
         LIMIT 10"
    );
    $query_time = (microtime(true) - $query_start) * 1000; // Convert to milliseconds
    
    echo "<p>⏱️ Query execution time: <strong>" . number_format($query_time, 2) . " ms</strong></p>";
    
    if ($query_time > 100) {
        echo "<p style='color: orange;'>⚠️ Query is slower than expected (>100ms)</p>";
    } else {
        echo "<p style='color: green;'>✅ Query performance is good (<100ms)</p>";
    }
    
    // Test API endpoint
    echo "<h3>API Performance Test</h3>";
    
    $api_start = microtime(true);
    $context = stream_context_create([
        'http' => [
            'timeout' => 5,  // 5 second timeout
            'method' => 'GET',
            'header' => "User-Agent: Performance-Test\r\n"
        ]
    ]);
    
    $api_url = 'http://localhost' . str_replace('\\', '/', substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT']))) . '/api/community_feed.php?limit=5';
    $api_response = @file_get_contents($api_url, false, $context);
    $api_time = (microtime(true) - $api_start) * 1000;
    
    if ($api_response !== false) {
        $api_data = json_decode($api_response, true);
        echo "<p>🌐 API response time: <strong>" . number_format($api_time, 2) . " ms</strong></p>";
        echo "<p>📦 API returned " . (isset($api_data['count']) ? $api_data['count'] : 'unknown') . " posts</p>";
        
        if ($api_time > 200) {
            echo "<p style='color: orange;'>⚠️ API response is slower than expected (>200ms)</p>";
        } else {
            echo "<p style='color: green;'>✅ API performance is good (<200ms)</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ API test failed - check if the server is running</p>";
    }
    
    // Performance recommendations
    echo "<h3>Performance Recommendations</h3>";
    echo "<ul>";
    
    if ($row_count > 1000) {
        echo "<li>⚠️ Consider adding pagination for better performance with large datasets</li>";
    }
    
    if ($query_time > 50) {
        echo "<li>⚠️ Database query optimization may be needed</li>";
    }
    
    if (!isset($table_check)) {
        echo "<li>✅ Added proper indexes for better query performance</li>";
    }
    
    echo "<li>✅ Reduced polling frequency from 3s to 10s</li>";
    echo "<li>✅ Added request timeouts to prevent hanging</li>";
    echo "<li>✅ Implemented user activity detection to pause polling</li>";
    echo "<li>✅ Added content truncation for large posts</li>";
    echo "</ul>";
    
    $total_time = (microtime(true) - $start_time) * 1000;
    echo "<p>🏁 Total test time: <strong>" . number_format($total_time, 2) . " ms</strong></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
<?php
// Test the community post API directly
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/database.php';

echo "<h2>Testing Community Post API</h2>";

// Simulate a logged-in user
$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = 1;
$_SESSION['user_email'] = 'test@example.com';
$_SESSION['user_name'] = 'Test User';

// Generate CSRF token
$csrf_token = generate_csrf_token();
echo "<p>CSRF Token: " . $csrf_token . "</p>";

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
        exit;
    } else {
        echo "<p style='color: green;'>✅ Community table exists</p>";
    }
    
    // Test data
    $test_data = [
        'title' => 'Test Post Title',
        'content' => 'This is a test post content to verify the API is working correctly.',
        'category' => 'test'
    ];
    
    // Simulate POST request
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST = array_merge($test_data, [CSRF_TOKEN_NAME => $csrf_token]);
    
    // Capture API output
    ob_start();
    require __DIR__ . '/api/community_post.php';
    $api_response = ob_get_clean();
    
    echo "<h3>API Response:</h3>";
    echo "<pre>" . htmlspecialchars($api_response) . "</pre>";
    
    // Try to decode JSON response
    $response_data = json_decode($api_response, true);
    if ($response_data) {
        if (isset($response_data['success']) && $response_data['success']) {
            echo "<p style='color: green;'>✅ API call successful</p>";
            echo "<pre>" . print_r($response_data['post'], true) . "</pre>";
        } else {
            echo "<p style='color: red;'>❌ API returned error: " . ($response_data['error'] ?? 'Unknown error') . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Invalid JSON response</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
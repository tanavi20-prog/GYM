<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/session.php';

// Simulate a logged-in user for testing
if (!is_logged_in()) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_email'] = 'test@example.com';
    $_SESSION['user_name'] = 'Test User';
    $_SESSION['logged_in'] = true;
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    // Check CSRF token
    $token = $_POST[CSRF_TOKEN_NAME] ?? '';
    if (!verify_csrf_token($token)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid CSRF token']);
        exit;
    }
    
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = trim($_POST['category'] ?? '');
    
    if (empty($title) || empty($content)) {
        http_response_code(400);
        echo json_encode(['error' => 'Title and content are required']);
        exit;
    }
    
    // Simulate successful post creation
    $post = [
        'id' => rand(1000, 9999),
        'title' => $title,
        'content' => $content,
        'category' => $category,
        'author_name' => $_SESSION['user_name'],
        'author_avatar' => strtoupper(substr($_SESSION['user_name'], 0, 1)),
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode(['success' => true, 'post' => $post]);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Community Post Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:disabled { background: #ccc; }
        #feedback { margin-top: 15px; padding: 10px; border-radius: 4px; display: none; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h1>Community Post Test</h1>
    
    <form id="new-post-form">
        <?= csrf_field() ?>
        
        <div class="form-group">
            <label for="post-title">Title:</label>
            <input type="text" id="post-title" name="title" required>
        </div>
        
        <div class="form-group">
            <label for="post-content">Content:</label>
            <textarea id="post-content" name="content" rows="4" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="post-category">Category (optional):</label>
            <input type="text" id="post-category" name="category">
        </div>
        
        <button type="submit" id="post-submit">Post</button>
        <div id="post-feedback"></div>
    </form>
    
    <div id="result" style="margin-top: 30px;"></div>

    <script>
        const newPostForm = document.getElementById('new-post-form');
        const submitBtn = document.getElementById('post-submit');
        const feedback = document.getElementById('post-feedback');
        const result = document.getElementById('result');
        
        if (newPostForm) {
            newPostForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                console.log('Form submitted');
                
                submitBtn.disabled = true;
                feedback.style.display = 'none';
                
                const formData = new FormData(newPostForm);
                
                try {
                    console.log('Sending request to:', window.location.href);
                    const res = await fetch(window.location.href, {
                        method: 'POST',
                        credentials: 'same-origin',
                        body: formData
                    });
                    
                    console.log('Response status:', res.status);
                    const data = await res.json();
                    console.log('Response data:', data);
                    
                    if (res.ok && data.success) {
                        feedback.style.display = 'block';
                        feedback.className = 'success';
                        feedback.textContent = 'Posted successfully!';
                        
                        result.innerHTML = '<h3>Posted Content:</h3><pre>' + JSON.stringify(data.post, null, 2) + '</pre>';
                        
                        newPostForm.reset();
                    } else {
                        feedback.style.display = 'block';
                        feedback.className = 'error';
                        feedback.textContent = (data && data.error) ? data.error : 'Failed to post';
                    }
                } catch (err) {
                    console.error('Error:', err);
                    feedback.style.display = 'block';
                    feedback.className = 'error';
                    feedback.textContent = 'Network error: ' + err.message;
                }
                
                submitBtn.disabled = false;
            });
        } else {
            console.error('Form not found');
        }
    </script>
</body>
</html>
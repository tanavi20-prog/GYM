<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/session.php';

// Ensure user is logged in for testing
if (!is_logged_in()) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_email'] = 'test@example.com';
    $_SESSION['user_name'] = 'Test User';
    $_SESSION['logged_in'] = true;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Community Post Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        button { background: #007cba; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #005a87; }
        button:disabled { background: #ccc; cursor: not-allowed; }
        #feedback { margin-top: 15px; padding: 12px; border-radius: 4px; display: none; font-weight: bold; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        #posts-container { margin-top: 30px; }
        .post { background: white; padding: 15px; border-radius: 8px; margin-bottom: 15px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="card">
        <h1>Community Post Test</h1>
        <p>Test the community post functionality with proper debugging.</p>
    </div>
    
    <div class="card">
        <h2>Create New Post</h2>
        <form id="new-post-form">
            <?= csrf_field() ?>
            
            <div class="form-group">
                <label for="post-title">Title *</label>
                <input type="text" id="post-title" name="title" required placeholder="Enter post title">
            </div>
            
            <div class="form-group">
                <label for="post-content">Content *</label>
                <textarea id="post-content" name="content" rows="4" required placeholder="Share your thoughts..."></textarea>
            </div>
            
            <div class="form-group">
                <label for="post-category">Category (optional)</label>
                <input type="text" id="post-category" name="category" placeholder="e.g., motivation, nutrition, progress">
            </div>
            
            <button type="submit" id="post-submit">Post</button>
            <div id="post-feedback"></div>
        </form>
    </div>
    
    <div class="card">
        <h2>Recent Posts</h2>
        <div id="posts-container">
            <p style="text-align: center; color: #666;">No posts yet. Create your first post above!</p>
        </div>
    </div>

    <script>
        // Debug logging
        console.log('Page loaded');
        console.log('CSRF Token Name:', '<?= CSRF_TOKEN_NAME ?>');
        
        const form = document.getElementById('new-post-form');
        const submitBtn = document.getElementById('post-submit');
        const feedback = document.getElementById('post-feedback');
        const postsContainer = document.getElementById('posts-container');
        
        console.log('Elements found:', {
            form: !!form,
            submitBtn: !!submitBtn,
            feedback: !!feedback,
            postsContainer: !!postsContainer
        });
        
        if (form && submitBtn && feedback) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                console.log('Form submission started');
                
                // Get form data
                const formData = new FormData(form);
                console.log('Form data:', [...formData.entries()]);
                
                // Validate required fields
                const title = formData.get('title')?.trim();
                const content = formData.get('content')?.trim();
                
                if (!title || !content) {
                    showFeedback('Please fill in both title and content fields.', 'error');
                    return;
                }
                
                // Disable button and show loading state
                submitBtn.disabled = true;
                submitBtn.textContent = 'Posting...';
                feedback.style.display = 'none';
                
                try {
                    // Send request
                    const response = await fetch('<?= APP_URL ?>/api/community_post.php', {
                        method: 'POST',
                        credentials: 'same-origin',
                        body: formData
                    });
                    
                    console.log('Response status:', response.status);
                    console.log('Response headers:', [...response.headers.entries()]);
                    
                    const data = await response.json();
                    console.log('Response data:', data);
                    
                    if (response.ok && data.success) {
                        showFeedback('Posted successfully!', 'success');
                        form.reset();
                        
                        // Add post to display
                        addPostToDisplay(data.post);
                        
                        // Hide success message after 3 seconds
                        setTimeout(() => {
                            feedback.style.display = 'none';
                        }, 3000);
                    } else {
                        const errorMessage = data.error || 'Failed to post. Please try again.';
                        showFeedback(errorMessage, 'error');
                    }
                } catch (error) {
                    console.error('Network error:', error);
                    showFeedback('Network error. Please check your connection and try again.', 'error');
                } finally {
                    // Re-enable button
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Post';
                }
            });
        } else {
            console.error('Required elements not found');
        }
        
        function showFeedback(message, type) {
            feedback.textContent = message;
            feedback.className = type;
            feedback.style.display = 'block';
        }
        
        function addPostToDisplay(post) {
            const postElement = document.createElement('div');
            postElement.className = 'post';
            postElement.innerHTML = `
                <h3>${escapeHtml(post.title)}</h3>
                <p><strong>by ${escapeHtml(post.author_name)}</strong> - ${escapeHtml(post.created_at)}</p>
                <p>${escapeHtml(post.content)}</p>
                ${post.category ? `<span style="background: #e9ecef; padding: 2px 8px; border-radius: 4px; font-size: 12px;">${escapeHtml(post.category)}</span>` : ''}
            `;
            postsContainer.insertBefore(postElement, postsContainer.firstChild);
            
            // Remove the "no posts" message if it exists
            const noPostsMsg = postsContainer.querySelector('p[style*="text-align: center"]');
            if (noPostsMsg) {
                noPostsMsg.remove();
            }
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>
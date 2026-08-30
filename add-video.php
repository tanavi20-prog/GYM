<?php
// Admin page to add new YouTube videos
require_once 'crud/connect.php';

$message = '';
$error = '';

// Handle form submission
if ($_POST && isset($_POST['add_video'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $youtube_url = trim($_POST['youtube_url']);
    $duration_minutes = (int)$_POST['duration_minutes'];
    $difficulty = $_POST['difficulty'];
    $category_id = (int)$_POST['category_id'];
    $instructor_name = trim($_POST['instructor_name']);
    $calories_per_minute = (float)$_POST['calories_per_minute'];
    
    // Extract YouTube ID from URL
    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $youtube_url, $match);
    $youtube_id = $match[1] ?? '';
    
    if ($title && $youtube_url && $youtube_id && $duration_minutes && $difficulty && $category_id) {
        try {
            $conn = getConnection();
            
            $thumbnail_url = "https://img.youtube.com/vi/$youtube_id/maxresdefault.jpg";
            
            $stmt = $conn->prepare("INSERT INTO videos (title, description, youtube_url, youtube_id, thumbnail_url, duration_minutes, difficulty, category_id, instructor_name, calories_per_minute) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->bind_param("sssssisssf", 
                $title, $description, $youtube_url, $youtube_id, $thumbnail_url,
                $duration_minutes, $difficulty, $category_id, $instructor_name, $calories_per_minute
            );
            
            if ($stmt->execute()) {
                $message = "✅ Video added successfully!";
            } else {
                $error = "❌ Error adding video: " . $stmt->error;
            }
        } catch (Exception $e) {
            $error = "❌ Database error: " . $e->getMessage();
        }
    } else {
        $error = "❌ Please fill in all required fields and provide a valid YouTube URL.";
    }
}

// Get categories for dropdown
$categories = [];
try {
    $conn = getConnection();
    $result = $conn->query("SELECT * FROM video_categories ORDER BY name");
    $categories = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
} catch (Exception $e) {
    $error = "Database connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Video - RKT FitVibe</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #22c55e;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        input, textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        textarea {
            height: 100px;
            resize: vertical;
        }
        
        .btn {
            background: #22c55e;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .btn:hover {
            background: #16a34a;
        }
        
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .nav-links {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .nav-links a {
            display: inline-block;
            margin-right: 15px;
            color: #22c55e;
            text-decoration: none;
        }
        
        .nav-links a:hover {
            text-decoration: underline;
        }
        
        .required {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New YouTube Video</h1>
        
        <?php if ($message): ?>
            <div class="message success"><?= $message ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="title">Video Title <span class="required">*</span></label>
                <input type="text" id="title" name="title" required placeholder="e.g., 10 Minute Morning Workout">
            </div>
            
            <div class="form-group">
                <label for="youtube_url">YouTube URL <span class="required">*</span></label>
                <input type="url" id="youtube_url" name="youtube_url" required placeholder="https://www.youtube.com/watch?v=...">
            </div>
            
            <div class="form-group">
                <label for="category_id">Category <span class="required">*</span></label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['icon'] . ' ' . $category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="instructor_name">Instructor Name</label>
                <input type="text" id="instructor_name" name="instructor_name" placeholder="e.g., Yoga with Adriene">
            </div>
            
            <div class="form-group">
                <label for="duration_minutes">Duration (minutes) <span class="required">*</span></label>
                <input type="number" id="duration_minutes" name="duration_minutes" required min="1" max="180" placeholder="e.g., 15">
            </div>
            
            <div class="form-group">
                <label for="difficulty">Difficulty <span class="required">*</span></label>
                <select id="difficulty" name="difficulty" required>
                    <option value="">Select difficulty</option>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="calories_per_minute">Calories per Minute (estimated)</label>
                <input type="number" id="calories_per_minute" name="calories_per_minute" step="0.1" min="0" max="20" placeholder="e.g., 8.5" value="5.0">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Brief description of the workout..."></textarea>
            </div>
            
            <button type="submit" name="add_video" class="btn">Add Video</button>
        </form>
        
        <div class="nav-links">
            <a href="test-videos.php">🔍 Test Videos</a>
            <a href="pages/videos.php">📹 Video Library</a>
            <a href="index.php">🏠 Dashboard</a>
        </div>
    </div>
</body>
</html>
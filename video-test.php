<?php
// Simple Video Test Page
require_once 'includes/config.php';
require_once 'crud/connect.php';

try {
    $conn = getConnection();
    $categories = [];
    $videos = [];
    
    if ($conn) {
        // Get categories
        $cat_result = $conn->query("SELECT * FROM video_categories ORDER BY name");
        if ($cat_result) {
            $categories = $cat_result->fetch_all(MYSQLI_ASSOC);
        }
        
        // Get videos
        $video_result = $conn->query("SELECT v.*, vc.name as category_name, vc.color as category_color 
                                      FROM videos v 
                                      LEFT JOIN video_categories vc ON v.category_id = vc.id 
                                      WHERE v.is_active = 1 
                                      ORDER BY v.created_at DESC");
        if ($video_result) {
            $videos = $video_result->fetch_all(MYSQLI_ASSOC);
        }
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Test - RKT fitvibe</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root {
            --primary: #22c55e;
            --primary-hover: #16a34a;
            --background: #ffffff;
            --foreground: #1f2937;
            --card: #ffffff;
            --border: #e5e7eb;
            --muted-foreground: #6b7280;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(145deg, #ffffff 0%, #f0fdf4 50%, #ecfdf5 100%);
            color: var(--foreground);
            margin: 0;
            padding: 2rem;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 2rem;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }
        
        .status-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .video-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            transition: transform 0.2s ease;
        }
        
        .video-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }
        
        .video-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1f2937;
        }
        
        .video-meta {
            display: flex;
            gap: 1rem;
            margin: 0.5rem 0;
            font-size: 0.9rem;
            color: #6b7280;
        }
        
        .category-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .back-link {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 2rem;
            transition: background 0.2s;
        }
        
        .back-link:hover {
            background: var(--primary-hover);
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">← Back to Dashboard</a>
        
        <div class="header">
            <h1>🎥 Video System Test</h1>
            <p>Testing video library functionality and styling</p>
        </div>
        
        <div class="status-card">
            <h3>System Status</h3>
            <?php if (isset($error)): ?>
                <p style="color: #ef4444;">❌ Error: <?= htmlspecialchars($error) ?></p>
            <?php else: ?>
                <p style="color: #22c55e;">✅ Database connection successful</p>
                <p><strong>Categories found:</strong> <?= count($categories) ?></p>
                <p><strong>Videos found:</strong> <?= count($videos) ?></p>
                
                <?php if (!empty($categories)): ?>
                    <p><strong>Categories:</strong> 
                    <?php foreach ($categories as $cat): ?>
                        <span style="background: <?= $cat['color'] ?? '#22c55e' ?>20; color: <?= $cat['color'] ?? '#22c55e' ?>; padding: 0.25rem 0.5rem; border-radius: 4px; margin-right: 0.5rem;">
                            <?= htmlspecialchars($cat['name']) ?>
                        </span>
                    <?php endforeach; ?>
                    </p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($videos)): ?>
            <div class="video-grid">
                <?php foreach ($videos as $video): ?>
                    <div class="video-card">
                        <?php if ($video['category_name']): ?>
                            <div class="category-badge" style="background-color: <?= $video['category_color'] ?? '#22c55e' ?>20; color: <?= $video['category_color'] ?? '#22c55e' ?>;">
                                <?= htmlspecialchars($video['category_name']) ?>
                            </div>
                        <?php endif; ?>
                        
                        <h3 class="video-title"><?= htmlspecialchars($video['title']) ?></h3>
                        
                        <div class="video-meta">
                            <span>⏱️ <?= $video['duration_minutes'] ?> min</span>
                            <span>💪 <?= ucfirst($video['difficulty']) ?></span>
                            <span>🔥 <?= round($video['calories_per_minute'] * $video['duration_minutes']) ?> cal</span>
                        </div>
                        
                        <?php if ($video['instructor_name']): ?>
                            <p><strong>Instructor:</strong> <?= htmlspecialchars($video['instructor_name']) ?></p>
                        <?php endif; ?>
                        
                        <p style="color: #6b7280; font-size: 0.9rem; line-height: 1.4;">
                            <?= htmlspecialchars(substr($video['description'], 0, 150)) ?><?= strlen($video['description']) > 150 ? '...' : '' ?>
                        </p>
                        
                        <div style="margin-top: 1rem;">
                            <a href="?page=video-player&id=<?= $video['id'] ?>" style="display: inline-block; padding: 0.5rem 1rem; background: var(--primary); color: white; text-decoration: none; border-radius: 6px; font-size: 0.9rem;">
                                ▶ Watch Video
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="status-card">
                <h3>No Videos Found</h3>
                <p>No videos are currently available in the database.</p>
                <p><a href="dev-tools/setup/simple_video_data.php">Run setup script to add sample videos</a></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
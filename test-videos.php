<?php
// Test script to check if videos are working
require_once 'crud/connect.php';

try {
    $conn = getConnection();
    
    echo "<h1>Database Connection Test</h1>";
    echo "<p>✅ Connected to database successfully!</p>";
    
    // Check video categories
    $categories_result = $conn->query("SELECT * FROM video_categories");
    $categories = $categories_result ? $categories_result->fetch_all(MYSQLI_ASSOC) : [];
    
    echo "<h2>Video Categories (" . count($categories) . ")</h2>";
    foreach ($categories as $category) {
        echo "<div style='margin: 10px; padding: 10px; border: 1px solid #ccc;'>";
        echo "<strong>{$category['icon']} {$category['name']}</strong><br>";
        echo "<span style='color: {$category['color']};'>Color: {$category['color']}</span><br>";
        echo "<small>{$category['description']}</small>";
        echo "</div>";
    }
    
    // Check videos
    $videos_result = $conn->query("SELECT v.*, vc.name as category_name FROM videos v LEFT JOIN video_categories vc ON v.category_id = vc.id WHERE v.is_active = 1 LIMIT 5");
    $videos = $videos_result ? $videos_result->fetch_all(MYSQLI_ASSOC) : [];
    
    echo "<h2>Sample Videos (" . count($videos) . ")</h2>";
    foreach ($videos as $video) {
        echo "<div style='margin: 15px; padding: 15px; border: 1px solid #ddd; border-radius: 8px;'>";
        echo "<h3><a href='pages/video-player.php?id={$video['id']}' target='_blank'>{$video['title']}</a></h3>";
        echo "<p><strong>Instructor:</strong> {$video['instructor_name']}</p>";
        echo "<p><strong>Category:</strong> {$video['category_name']}</p>";
        echo "<p><strong>Duration:</strong> {$video['duration_minutes']} minutes</p>";
        echo "<p><strong>Difficulty:</strong> {$video['difficulty']}</p>";
        echo "<p><strong>YouTube URL:</strong> <a href='{$video['youtube_url']}' target='_blank'>{$video['youtube_url']}</a></p>";
        echo "<p><strong>Description:</strong> {$video['description']}</p>";
        
        // YouTube embed test
        echo "<div style='margin-top: 10px;'>";
        echo "<iframe width='300' height='169' src='https://www.youtube.com/embed/{$video['youtube_id']}?rel=0' frameborder='0' allowfullscreen></iframe>";
        echo "</div>";
        echo "</div>";
    }
    
    echo "<h2>Navigation Links</h2>";
    echo "<ul>";
    echo "<li><a href='pages/videos.php'>Go to Video Library</a></li>";
    echo "<li><a href='index.php'>Go to Main Dashboard</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h1>❌ Error</h1>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
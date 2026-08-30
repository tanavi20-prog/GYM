<?php
echo "<h1>Database Connection Test</h1>";

try {
    require_once 'crud/connect.php';
    $conn = getConnection();
    echo "<p>✅ Database connected successfully!</p>";
    
    // Show all tables
    $result = $conn->query('SHOW TABLES');
    echo "<h2>Available Tables:</h2><ul>";
    while ($row = $result->fetch_array()) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
    
    // Check if video tables exist
    $videos_exist = $conn->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = 'fitness_db' AND table_name = 'videos'")->fetch_assoc();
    $categories_exist = $conn->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = 'fitness_db' AND table_name = 'video_categories'")->fetch_assoc();
    
    if ($videos_exist['count'] > 0 && $categories_exist['count'] > 0) {
        echo "<p>✅ Video tables exist!</p>";
        
        $video_count = $conn->query('SELECT COUNT(*) as count FROM videos')->fetch_assoc();
        $category_count = $conn->query('SELECT COUNT(*) as count FROM video_categories')->fetch_assoc();
        
        echo "<p>Videos: " . $video_count['count'] . "</p>";
        echo "<p>Categories: " . $category_count['count'] . "</p>";
        
        if ($video_count['count'] > 0) {
            echo "<h3>Sample Videos:</h3>";
            $videos = $conn->query('SELECT * FROM videos LIMIT 3');
            while ($video = $videos->fetch_assoc()) {
                echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
                echo "<strong>" . htmlspecialchars($video['title']) . "</strong><br>";
                echo "YouTube: <a href='" . $video['youtube_url'] . "' target='_blank'>" . $video['youtube_url'] . "</a><br>";
                echo "Duration: " . $video['duration_minutes'] . " minutes<br>";
                echo "</div>";
            }
        }
        
        echo "<p><a href='pages/videos.php'>Go to Video Library</a></p>";
        echo "<p><a href='pages/video-player.php?id=1'>Test Video Player</a></p>";
        
    } else {
        echo "<p>❌ Video tables don't exist. Creating them now...</p>";
        // Tables will be created automatically by the connection class
        echo "<p>Please refresh this page.</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
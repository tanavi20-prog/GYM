<?php
require_once 'crud/connect.php';
require_once 'includes/helpers.php';

// Test YouTube video ID extraction
function getYoutubeVideoId($url) {
    if (empty($url)) return '';
    
    $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.*\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/';
    preg_match($pattern, $url, $matches);
    return isset($matches[1]) ? $matches[1] : '';
}

// Test URLs
$testUrls = [
    'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    'https://youtu.be/dQw4w9WgXcQ',
    'https://www.youtube.com/embed/dQw4w9WgXcQ',
    'https://www.youtube.com/watch?v=dQw4w9WgXcQ&feature=share',
    'invalid-url'
];

echo "<h2>YouTube Video ID Extraction Test</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>URL</th><th>Extracted ID</th><th>Status</th></tr>";

foreach ($testUrls as $url) {
    $videoId = getYoutubeVideoId($url);
    $status = !empty($videoId) ? '✅ Valid' : '❌ Invalid';
    echo "<tr><td>" . htmlspecialchars($url) . "</td><td>" . htmlspecialchars($videoId) . "</td><td>$status</td></tr>";
}

echo "</table>";

// Test database connection and trainers
echo "<h2>Trainers with YouTube Videos</h2>";

try {
    $result = $conn->query("SELECT id, name, youtube_video FROM trainers WHERE youtube_video IS NOT NULL AND youtube_video != ''");
    
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Name</th><th>YouTube URL</th><th>Video ID</th><th>Embed Preview</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            $videoId = getYoutubeVideoId($row['youtube_video']);
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['youtube_video']) . "</td>";
            echo "<td>" . htmlspecialchars($videoId) . "</td>";
            echo "<td>";
            if (!empty($videoId)) {
                echo "<iframe width='200' height='113' src='https://www.youtube.com/embed/$videoId' frameborder='0' allowfullscreen></iframe>";
            } else {
                echo "Invalid URL";
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No trainers with YouTube videos found.</p>";
        echo "<p>Database connection: " . ($conn ? "✅ Connected" : "❌ Failed") . "</p>";
    }
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

echo "<h2>Add Sample Data</h2>";
echo "<p><a href='dev-tools/setup/add_youtube_column.php'>Run Database Update Script</a></p>";
echo "<p><a href='pages/trainer.php'>View Trainers Page</a></p>";
?>
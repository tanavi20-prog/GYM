<?php
// Simple database table checker
require_once 'crud/connect.php';

try {
    $conn = getConnection();
    
    echo "<h2>Database Tables Check</h2>\n";
    
    // Get all tables
    $result = $conn->query("SHOW TABLES");
    $tables = [];
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }
    
    echo "<h3>Existing Tables:</h3>\n<ul>\n";
    foreach ($tables as $table) {
        echo "<li>$table</li>\n";
    }
    echo "</ul>\n";
    
    // Check for specific tables we need
    $required_tables = ['users', 'trainers', 'trainer_sessions', 'plans'];
    $progress_tables = ['user_meals', 'user_workouts', 'user_progress', 'user_streaks'];
    
    echo "<h3>Required Tables Status:</h3>\n";
    foreach ($required_tables as $table) {
        if (in_array($table, $tables)) {
            echo "<p style='color: green;'>✓ $table exists</p>\n";
        } else {
            echo "<p style='color: red;'>✗ $table missing</p>\n";
        }
    }
    
    echo "<h3>Progress Tracking Tables Status:</h3>\n";
    foreach ($progress_tables as $table) {
        if (in_array($table, $tables)) {
            echo "<p style='color: green;'>✓ $table exists</p>\n";
        } else {
            echo "<p style='color: orange;'>⚠ $table not created yet</p>\n";
        }
    }
    
    // Show sample data from existing tables
    if (in_array('trainer_sessions', $tables)) {
        echo "<h3>Sample Trainer Sessions:</h3>\n";
        $result = $conn->query("SELECT COUNT(*) as count FROM trainer_sessions");
        $row = $result->fetch_assoc();
        echo "<p>Total sessions: " . $row['count'] . "</p>\n";
        
        if ($row['count'] > 0) {
            $result = $conn->query("SELECT * FROM trainer_sessions LIMIT 3");
            echo "<pre>\n";
            while ($row = $result->fetch_assoc()) {
                print_r($row);
            }
            echo "</pre>\n";
        }
    }
    
    if (in_array('plans', $tables)) {
        echo "<h3>Sample Plans:</h3>\n";
        $result = $conn->query("SELECT COUNT(*) as count FROM plans");
        $row = $result->fetch_assoc();
        echo "<p>Total plans: " . $row['count'] . "</p>\n";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>\n";
}
?>
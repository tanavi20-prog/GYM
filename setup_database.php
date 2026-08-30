<?php
// Database setup script
require_once 'includes/config.php';

echo "<h1>🏋️ Gym Database Setup</h1>";

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    if ($conn === null) {
        throw new Exception("Failed to connect to database");
    }
    
    echo "<p>✅ Database connection successful!</p>";
    
    // Read and execute SQL file
    $sqlContent = file_get_contents('fitness_db.sql');
    if ($sqlContent === false) {
        throw new Exception("Failed to read fitness_db.sql file");
    }
    
    // Split SQL commands by semicolon
    $sqlCommands = array_filter(array_map('trim', explode(';', $sqlContent)));
    
    foreach ($sqlCommands as $sql) {
        if (!empty($sql)) {
            $conn->exec($sql);
        }
    }
    
    echo "<p>✅ Database tables created successfully!</p>";
    
    // Insert sample data for demonstration
    $sampleUsers = [
        [
            'email' => 'john.doe@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'name' => 'John Doe',
            'age' => 28,
            'gender' => 'male',
            'weight' => 75.5,
            'height' => 180.0,
            'fitnessgoal' => 'muscle-gain',
            'dietarypreference' => 'none',
            'activitylevel' => 'intermediate',
            'targetweight' => 80.0
        ],
        [
            'email' => 'jane.smith@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'name' => 'Jane Smith',
            'age' => 25,
            'gender' => 'female',
            'weight' => 62.0,
            'height' => 165.0,
            'fitnessgoal' => 'weight-loss',
            'dietarypreference' => 'vegetarian',
            'activitylevel' => 'beginner',
            'targetweight' => 58.0
        ],
        [
            'email' => 'mike.wilson@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'name' => 'Mike Wilson',
            'age' => 32,
            'gender' => 'male',
            'weight' => 85.0,
            'height' => 175.0,
            'fitnessgoal' => 'strength',
            'dietarypreference' => 'keto',
            'activitylevel' => 'advanced',
            'targetweight' => 88.0
        ]
    ];
    
    foreach ($sampleUsers as $user) {
        $db->insert('users', $user);
    }
    
    echo "<p>✅ Sample data inserted successfully!</p>";
    echo "<p><strong>Sample users created:</strong></p>";
    echo "<ul>";
    echo "<li>john.doe@example.com (password: password123)</li>";
    echo "<li>jane.smith@example.com (password: password123)</li>";
    echo "<li>mike.wilson@example.com (password: password123)</li>";
    echo "</ul>";
    
    echo "<div style='margin-top: 2rem; padding: 1rem; background: #e8f5e8; border: 1px solid #4caf50; border-radius: 5px;'>";
    echo "<h3>✅ Setup Complete!</h3>";
    echo "<p>Your gym database is ready with CRUD operations!</p>";
    echo "<a href='?page=crud' style='color: #1976d2; text-decoration: none; font-weight: bold;'>→ Go to CRUD Management Page</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='padding: 1rem; background: #ffeaea; border: 1px solid #f44336; border-radius: 5px;'>";
    echo "<h3>❌ Setup Failed!</h3>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Make sure:</strong></p>";
    echo "<ul>";
    echo "<li>WAMP/XAMPP server is running</li>";
    echo "<li>MySQL service is started</li>";
    echo "<li>Database credentials in config.php are correct</li>";
    echo "</ul>";
    echo "</div>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 2rem auto;
    padding: 2rem;
    background: #f5f5f5;
}
h1 {
    color: #ff6b35;
    text-align: center;
}
p {
    margin: 1rem 0;
}
ul {
    background: white;
    padding: 1rem;
    border-radius: 5px;
}
</style>
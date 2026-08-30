<?php
// Login debugging script
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';

echo "<h1>🔍 Login System Debug</h1>";

echo "<h2>1. Database Connection Test</h2>";
try {
    $db = db();
    if ($db && $db->getConnection()) {
        echo "✅ Database connection: SUCCESS<br>";
        
        // Test query
        $result = $db->fetch("SELECT 1 as test");
        if ($result) {
            echo "✅ Database query: SUCCESS<br>";
        } else {
            echo "❌ Database query: FAILED<br>";
        }
    } else {
        echo "❌ Database connection: FAILED<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<h2>2. Users in Database</h2>";
try {
    $users = $db->fetchAll("SELECT id, email, name, createdat FROM users ORDER BY id");
    if ($users) {
        echo "✅ Found " . count($users) . " users:<br>";
        foreach ($users as $user) {
            echo "- ID: {$user['id']}, Email: {$user['email']}, Name: {$user['name']}, Created: {$user['createdat']}<br>";
        }
    } else {
        echo "❌ No users found in database<br>";
    }
} catch (Exception $e) {
    echo "❌ Error fetching users: " . $e->getMessage() . "<br>";
}

echo "<h2>3. Test Login Function</h2>";
if (isset($_POST['test_email']) && isset($_POST['test_password'])) {
    $email = $_POST['test_email'];
    $password = $_POST['test_password'];
    
    echo "Testing login with:<br>";
    echo "Email: $email<br>";
    echo "Password: $password<br><br>";
    
    try {
        $user = authenticate_user($email, $password);
        echo "✅ Login SUCCESS!<br>";
        echo "User ID: " . $user['id'] . "<br>";
        echo "User Name: " . $user['name'] . "<br>";
        echo "User Email: " . $user['email'] . "<br>";
    } catch (Exception $e) {
        echo "❌ Login FAILED: " . $e->getMessage() . "<br>";
        
        // Additional debugging
        $test_user = $db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
        if ($test_user) {
            echo "<br>🔍 User found in database:<br>";
            echo "- ID: {$test_user['id']}<br>";
            echo "- Email: {$test_user['email']}<br>";
            echo "- Name: {$test_user['name']}<br>";
            echo "- Password hash: {$test_user['password']}<br>";
            echo "- Created: {$test_user['createdat']}<br>";
            
            // Test password verification
            if (password_verify($password, $test_user['password'])) {
                echo "✅ Password verification: SUCCESS<br>";
            } else {
                echo "❌ Password verification: FAILED<br>";
                echo "This means the password doesn't match the stored hash.<br>";
            }
        } else {
            echo "❌ User not found in database<br>";
        }
    }
}

echo "<h2>4. Test Form</h2>";
echo '<form method="post">';
echo 'Email: <input type="email" name="test_email" placeholder="test@example.com" required><br><br>';
echo 'Password: <input type="password" name="test_password" placeholder="password" required><br><br>';
echo '<button type="submit">Test Login</button>';
echo '</form>';

echo "<h2>5. Create Test User</h2>";
if (isset($_POST['create_test_user'])) {
    try {
        $test_data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'age' => 25,
            'gender' => 'male',
            'weight' => 70,
            'height' => 175,
            'fitnessGoal' => 'general',
            'password' => 'test123456',
            'confirmPassword' => 'test123456',
            'security_question' => 'What was the name of your first pet?',
            'security_answer' => 'Fluffy'
        ];
        
        $user = register_user($test_data);
        echo "✅ Test user created successfully!<br>";
        echo "Email: test@example.com<br>";
        echo "Password: test123456<br>";
    } catch (Exception $e) {
        echo "❌ Error creating test user: " . $e->getMessage() . "<br>";
    }
}

echo '<form method="post">';
echo '<button type="submit" name="create_test_user">Create Test User</button>';
echo '</form>';

echo "<br><a href='/gymmm/'>← Back to Main Site</a>";
?>
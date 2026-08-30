<?php
// Test page to verify trainer booking API is working
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/helpers.php';
require_once 'crud/connect.php';

echo "<h1>🔧 Testing Trainer Booking API</h1>";

// Simulate a logged-in user for testing
if (!is_logged_in()) {
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = 1;
    $_SESSION['user'] = ['id' => 1, 'name' => 'Test User', 'email' => 'test@example.com'];
    echo "<p>✅ Simulated user login (ID: 1)</p>";
} else {
    echo "<p>✅ User already logged in (ID: " . get_current_user_id() . ")</p>";
}

try {
    $conn = getConnection();
    echo "<p>✅ Database connection successful</p>";
    
    // Check if required tables exist
    $tables = ['users', 'trainers', 'trainer_sessions'];
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "<p>✅ Table '$table' exists</p>";
        } else {
            echo "<p>❌ Table '$table' MISSING</p>";
        }
    }
    
    // Check for available trainers
    $trainer_result = $conn->query("SELECT id, name, hourly_rate, available FROM trainers WHERE available = 1 LIMIT 1");
    if ($trainer_result && $trainer_result->num_rows > 0) {
        $trainer = $trainer_result->fetch_assoc();
        echo "<p>✅ Available trainer found: {$trainer['name']} (ID: {$trainer['id']}, Rate: \${$trainer['hourly_rate']})</p>";
        $trainer_id = $trainer['id'];
    } else {
        echo "<p>❌ No available trainers found</p>";
        $trainer_id = null;
    }
    
    if ($trainer_id) {
        echo "<h2>🧪 Testing API Call</h2>";
        echo "<p>Click the button below to test the booking API:</p>";
        ?>
        
        <button onclick="testBooking(<?= $trainer_id ?>)" style="padding: 10px 20px; background: #22c55e; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Test Book Trainer (ID: <?= $trainer_id ?>)
        </button>
        
        <div id="result" style="margin-top: 20px; padding: 15px; border-radius: 5px; display: none;"></div>
        
        <script>
        async function testBooking(trainerId) {
            const resultDiv = document.getElementById('result');
            resultDiv.style.display = 'block';
            resultDiv.style.background = '#f0f0f0';
            resultDiv.innerHTML = '<p>⏳ Sending booking request...</p>';
            
            try {
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                tomorrow.setHours(10, 0, 0, 0);
                const scheduledDate = tomorrow.toISOString().slice(0, 19).replace('T', ' ');
                
                const response = await fetch('/gymmm/api/trainer_booking.php?action=book_session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        trainer_id: trainerId,
                        scheduled_date: scheduledDate,
                        duration_minutes: 60,
                        notes: 'Test booking from API test page'
                    })
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    resultDiv.style.background = '#d1fae5';
                    resultDiv.innerHTML = `
                        <h3 style="color: #065f46;">✅ Booking Successful!</h3>
                        <p><strong>Trainer:</strong> ${data.trainer_name}</p>
                        <p><strong>Price:</strong> $${data.price.toFixed(2)}</p>
                        <p><strong>Booking ID:</strong> ${data.booking_id}</p>
                        <p><strong>Message:</strong> ${data.message}</p>
                    `;
                } else {
                    resultDiv.style.background = '#fee2e2';
                    resultDiv.innerHTML = `
                        <h3 style="color: #991b1b;">❌ Booking Failed</h3>
                        <p><strong>Error:</strong> ${data.error || 'Unknown error'}</p>
                        <p><strong>Status:</strong> ${response.status} ${response.statusText}</p>
                    `;
                }
            } catch (error) {
                resultDiv.style.background = '#fee2e2';
                resultDiv.innerHTML = `
                    <h3 style="color: #991b1b;">❌ Network Error</h3>
                    <p><strong>Message:</strong> ${error.message}</p>
                `;
            }
        }
        </script>
        
        <?php
    }
    
    echo "<h2>📋 Current Setup Status</h2>";
    echo "<ul>";
    echo "<li>✅ Database connection: Working</li>";
    echo "<li>✅ User session: " . (is_logged_in() ? 'Active' : 'Inactive') . "</li>";
    echo "<li>✅ Trainer data: " . ($trainer_id ? 'Available' : 'Missing') . "</li>";
    echo "<li>✅ API endpoint: /gymmm/api/trainer_booking.php</li>";
    echo "</ul>";
    
    echo "<h2>💡 Next Steps</h2>";
    echo "<p>If the test above works, try booking from the trainer page. If it fails, check:</p>";
    echo "<ol>";
    echo "<li>Apache is running</li>";
    echo "<li>PHP is working correctly</li>";
    echo "<li>Database connection is active</li>";
    echo "<li>Trainer has hourly_rate > 0 and available = 1</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
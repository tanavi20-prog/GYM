<?php
// Test page for real-time dashboard functionality
require_once 'includes/config.php';
require_once 'includes/auth.php';

$user = get_logged_in_user();
if (!$user) {
    header('Location: /gymmm/?page=login');
    exit;
}

$page_title = 'Real-time Dashboard Test';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .test-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin: 2rem 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .stat-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1rem 0;
            border-left: 4px solid var(--primary);
        }
        
        .btn-test {
            background: var(--primary);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin: 0.5rem;
            font-weight: 500;
        }
        
        .btn-test:hover {
            background: var(--primary-dark);
        }
        
        .btn-test.success {
            background: #10b981;
        }
        
        .btn-test.danger {
            background: #ef4444;
        }
        
        .api-response {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 1rem;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            margin: 1rem 0;
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1rem;">
        <h1>📊 Real-time Dashboard Test</h1>
        <p>Test the real-time progress tracking functionality</p>
        
        <div class="test-section">
            <h2>Current User Information</h2>
            <div class="stat-box">
                <strong>Name:</strong> <?= escape_output($user['name']) ?><br>
                <strong>Email:</strong> <?= escape_output($user['email']) ?><br>
                <strong>User ID:</strong> <?= $user['id'] ?><br>
                <?php if ($user['weight']): ?>
                    <strong>Weight:</strong> <?= $user['weight'] ?> kg<br>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="test-section">
            <h2>API Test - Progress Summary</h2>
            <button class="btn-test" onclick="testProgressAPI()">Fetch Progress Data</button>
            <div id="progress-response" class="api-response">Click button to test API</div>
        </div>
        
        <div class="test-section">
            <h2>Simulate Workout Logging</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
                <div>
                    <label>Duration (minutes):</label>
                    <input type="number" id="workout-duration" value="45" min="1" max="180" class="form-input">
                </div>
                <div>
                    <label>Notes:</label>
                    <input type="text" id="workout-notes" placeholder="Optional notes" class="form-input">
                </div>
            </div>
            <button class="btn-test success" onclick="logTestWorkout()">Log Test Workout</button>
            <div id="workout-response" class="api-response"></div>
        </div>
        
        <div class="test-section">
            <h2>Simulate Meal Logging</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div>
                    <label>Meal Type:</label>
                    <select id="meal-type" class="form-input">
                        <option value="breakfast">Breakfast</option>
                        <option value="lunch">Lunch</option>
                        <option value="dinner">Dinner</option>
                        <option value="snack">Snack</option>
                    </select>
                </div>
                <div>
                    <label>Meal Name:</label>
                    <input type="text" id="meal-name" value="Protein Shake" class="form-input">
                </div>
                <div>
                    <label>Calories:</label>
                    <input type="number" id="meal-calories" value="250" class="form-input">
                </div>
                <div>
                    <label>Protein (g):</label>
                    <input type="number" id="meal-protein" value="25" step="0.1" class="form-input">
                </div>
            </div>
            <button class="btn-test success" onclick="logTestMeal()">Log Test Meal</button>
            <div id="meal-response" class="api-response"></div>
        </div>
        
        <div class="test-section">
            <h2>Real-time Updates Test</h2>
            <p>Open your dashboard in another tab and watch these values update in real-time:</p>
            <button class="btn-test" onclick="startRealTimeTest()">Start Real-time Test</button>
            <button class="btn-test danger" onclick="stopRealTimeTest()">Stop Test</button>
            <div id="realtime-status" style="margin-top: 1rem; padding: 1rem; background: #e8f4fd; border-radius: 8px;">
                Test not running
            </div>
        </div>
        
        <div style="text-align: center; margin: 2rem 0;">
            <a href="/gymmm/?page=dashboard" class="btn-test">View Dashboard</a>
            <a href="/gymmm/" class="btn-test" style="background: #6c757d;">Back to Home</a>
        </div>
    </div>

    <script>
        let realtimeInterval;
        
        async function testProgressAPI() {
            try {
                const response = await fetch('/gymmm/api/progress.php?action=get_progress_summary');
                const data = await response.json();
                
                document.getElementById('progress-response').innerHTML = 
                    '<strong>Status:</strong> ' + response.status + '\n' +
                    '<strong>Response:</strong>\n' + 
                    JSON.stringify(data, null, 2);
            } catch (error) {
                document.getElementById('progress-response').innerHTML = 
                    '<strong>Error:</strong> ' + error.message;
            }
        }
        
        async function logTestWorkout() {
            const workoutData = {
                trainer_id: 1, // Default trainer ID for testing
                duration_minutes: parseInt(document.getElementById('workout-duration').value),
                notes: document.getElementById('workout-notes').value
            };
            
            try {
                const response = await fetch('/gymmm/api/progress.php?action=log_workout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(workoutData)
                });
                
                const data = await response.json();
                
                document.getElementById('workout-response').innerHTML = 
                    '<strong>Status:</strong> ' + response.status + '\n' +
                    '<strong>Response:</strong>\n' + 
                    JSON.stringify(data, null, 2);
                    
                if (data.success) {
                    // Auto-refresh progress data
                    setTimeout(testProgressAPI, 1000);
                }
            } catch (error) {
                document.getElementById('workout-response').innerHTML = 
                    '<strong>Error:</strong> ' + error.message;
            }
        }
        
        async function logTestMeal() {
            const mealData = {
                meal_type: document.getElementById('meal-type').value,
                meal_name: document.getElementById('meal-name').value,
                calories: parseInt(document.getElementById('meal-calories').value),
                protein_g: parseFloat(document.getElementById('meal-protein').value)
            };
            
            try {
                const response = await fetch('/gymmm/api/progress.php?action=log_meal', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(mealData)
                });
                
                const data = await response.json();
                
                document.getElementById('meal-response').innerHTML = 
                    '<strong>Status:</strong> ' + response.status + '\n' +
                    '<strong>Response:</strong>\n' + 
                    JSON.stringify(data, null, 2);
                    
                if (data.success) {
                    // Auto-refresh progress data
                    setTimeout(testProgressAPI, 1000);
                }
            } catch (error) {
                document.getElementById('meal-response').innerHTML = 
                    '<strong>Error:</strong> ' + error.message;
            }
        }
        
        function startRealTimeTest() {
            if (realtimeInterval) {
                clearInterval(realtimeInterval);
            }
            
            document.getElementById('realtime-status').innerHTML = 
                '🔄 Real-time test running... Logging a workout every 30 seconds';
            
            // Log a workout immediately
            logTestWorkout();
            
            // Then log every 30 seconds
            realtimeInterval = setInterval(() => {
                logTestWorkout();
                document.getElementById('realtime-status').innerHTML = 
                    '🔄 Real-time test running... Next workout in 30 seconds. ' + 
                    'Check your dashboard for real-time updates!';
            }, 30000);
        }
        
        function stopRealTimeTest() {
            if (realtimeInterval) {
                clearInterval(realtimeInterval);
                realtimeInterval = null;
                document.getElementById('realtime-status').innerHTML = 
                    '⏹️ Real-time test stopped';
            }
        }
        
        // Load initial data
        window.addEventListener('load', testProgressAPI);
    </script>
</body>
</html>
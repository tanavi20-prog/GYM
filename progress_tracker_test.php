<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/database.php';

// Test user login simulation
if (!is_logged_in()) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_email'] = 'test@example.com';
    $_SESSION['user_name'] = 'Test User';
    $_SESSION['logged_in'] = true;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Real-time Progress Tracker Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        button { background: #007cba; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin: 5px; }
        button:hover { background: #005a87; }
        button:disabled { background: #ccc; cursor: not-allowed; }
        #feedback { margin-top: 15px; padding: 12px; border-radius: 4px; display: none; font-weight: bold; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        #workouts-container { margin-top: 30px; }
        .workout-item { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 10px; }
        .stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin: 20px 0; }
        .stat-card { text-align: center; padding: 15px; background: #e9ecef; border-radius: 8px; }
        .stat-value { font-size: 24px; font-weight: bold; color: #007cba; }
        .stat-label { font-size: 14px; color: #6c757d; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Real-time Progress Tracker Test</h1>
        <p>Test the real-time workout tracking functionality with database integration.</p>
    </div>
    
    <div class="card">
        <h2>Log New Workout</h2>
        <form id="workout-form">
            <div class="form-group">
                <label for="workout-name">Exercise Name *</label>
                <input type="text" id="workout-name" required placeholder="e.g., Push-ups, Running, etc.">
            </div>
            
            <div class="form-group">
                <label for="workout-duration">Duration (minutes) *</label>
                <input type="number" id="workout-duration" min="1" required placeholder="30">
            </div>
            
            <div class="form-group">
                <label for="workout-intensity">Intensity *</label>
                <select id="workout-intensity" required>
                    <option value="">Select intensity</option>
                    <option value="low">Low</option>
                    <option value="moderate">Moderate</option>
                    <option value="high">High</option>
                    <option value="very-high">Very High</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="calories-burned">Calories Burned (optional)</label>
                <input type="number" id="calories-burned" min="0" placeholder="200">
            </div>
            
            <button type="submit" id="log-btn">Log Workout</button>
            <button type="button" onclick="loadWorkouts()">Refresh Data</button>
            <button type="button" onclick="clearAllWorkouts()" style="background: #dc3545;">Clear All</button>
        </form>
        <div id="feedback"></div>
    </div>
    
    <div class="card">
        <h2>Weekly Stats</h2>
        <div class="stats">
            <div class="stat-card">
                <div class="stat-value" id="week-workouts">0</div>
                <div class="stat-label">Workouts</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="week-minutes">0</div>
                <div class="stat-label">Minutes</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <h2>Recent Workouts</h2>
        <div id="workouts-container">
            <p style="text-align: center; color: #666;">Loading workouts...</p>
        </div>
    </div>
    
    <div class="card">
        <h2>Real-time Updates</h2>
        <p>Workouts are automatically saved to the database and updated in real-time.</p>
        <p>Last updated: <span id="last-update">Never</span></p>
        <button onclick="startRealTimeUpdates()">Start Auto-Refresh</button>
        <button onclick="stopRealTimeUpdates()">Stop Auto-Refresh</button>
    </div>

    <script>
        let autoRefreshInterval = null;
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadWorkouts();
        });
        
        // Form submission
        document.getElementById('workout-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            await logWorkout();
        });
        
        async function logWorkout() {
            const name = document.getElementById('workout-name').value;
            const duration = parseInt(document.getElementById('workout-duration').value);
            const intensity = document.getElementById('workout-intensity').value;
            const calories = parseInt(document.getElementById('calories-burned').value) || 0;
            
            if (!name || !duration || !intensity) {
                showFeedback('Please fill in all required fields', 'error');
                return;
            }
            
            const logBtn = document.getElementById('log-btn');
            logBtn.disabled = true;
            logBtn.textContent = 'Logging...';
            
            try {
                const response = await fetch('<?= APP_URL ?>/api/user_progress.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'log_workout',
                        workout: {
                            name: name,
                            duration: duration,
                            intensity: intensity,
                            calories: calories,
                            date: new Date().toISOString().split('T')[0],
                            time: new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
                        }
                    })
                });
                
                const result = await response.json();
                console.log('API Response:', result);
                
                if (result.success) {
                    showFeedback('Workout logged successfully!', 'success');
                    document.getElementById('workout-form').reset();
                    loadWorkouts();
                } else {
                    showFeedback('Error: ' + (result.error || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Error logging workout:', error);
                showFeedback('Network error. Please try again.', 'error');
            } finally {
                logBtn.disabled = false;
                logBtn.textContent = 'Log Workout';
            }
        }
        
        async function loadWorkouts() {
            try {
                const response = await fetch('<?= APP_URL ?>/api/user_progress.php?action=get_workouts&limit=10');
                const result = await response.json();
                console.log('Workouts data:', result);
                
                if (result.success && result.workouts) {
                    displayWorkouts(result.workouts);
                    updateWeeklyStats(result.workouts);
                } else {
                    document.getElementById('workouts-container').innerHTML = 
                        '<p style="text-align: center; color: #666;">No workouts found</p>';
                }
                
                document.getElementById('last-update').textContent = new Date().toLocaleTimeString();
            } catch (error) {
                console.error('Error loading workouts:', error);
                document.getElementById('workouts-container').innerHTML = 
                    '<p style="text-align: center; color: #dc3545;">Error loading workouts</p>';
            }
        }
        
        function displayWorkouts(workouts) {
            const container = document.getElementById('workouts-container');
            
            if (workouts.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #666;">No workouts logged yet</p>';
                return;
            }
            
            container.innerHTML = workouts.map(workout => `
                <div class="workout-item">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>${workout.name}</strong>
                            <div style="font-size: 14px; color: #666;">
                                ${workout.date} at ${workout.time}
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div>${workout.duration} minutes • ${workout.intensity}</div>
                            ${workout.calories ? `<div>${workout.calories} calories</div>` : ''}
                            <button onclick="deleteWorkout(${workout.id})" style="background: #dc3545; padding: 5px 10px; font-size: 12px; margin-top: 5px;">Delete</button>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        function updateWeeklyStats(workouts) {
            const weekAgo = new Date();
            weekAgo.setDate(weekAgo.getDate() - 7);
            
            const thisWeek = workouts.filter(w => {
                const workoutDate = new Date(w.date);
                return workoutDate >= weekAgo;
            });
            
            document.getElementById('week-workouts').textContent = thisWeek.length;
            document.getElementById('week-minutes').textContent = thisWeek.reduce((total, w) => total + w.duration, 0);
        }
        
        async function deleteWorkout(id) {
            if (confirm('Delete this workout?')) {
                try {
                    const response = await fetch('<?= APP_URL ?>/api/user_progress.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            action: 'delete_workout',
                            id: id
                        })
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        showFeedback('Workout deleted successfully', 'success');
                        loadWorkouts();
                    } else {
                        showFeedback('Error: ' + result.error, 'error');
                    }
                } catch (error) {
                    console.error('Error deleting workout:', error);
                    showFeedback('Error deleting workout', 'error');
                }
            }
        }
        
        async function clearAllWorkouts() {
            if (confirm('Clear all workout data? This cannot be undone.')) {
                try {
                    const response = await fetch('<?= APP_URL ?>/api/user_progress.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            action: 'clear_workouts'
                        })
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        showFeedback('All workouts cleared successfully', 'success');
                        loadWorkouts();
                    } else {
                        showFeedback('Error: ' + result.error, 'error');
                    }
                } catch (error) {
                    console.error('Error clearing workouts:', error);
                    showFeedback('Error clearing workouts', 'error');
                }
            }
        }
        
        function startRealTimeUpdates() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }
            
            autoRefreshInterval = setInterval(() => {
                loadWorkouts();
            }, 10000); // Update every 10 seconds
            
            showFeedback('Auto-refresh started (every 10 seconds)', 'success');
        }
        
        function stopRealTimeUpdates() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshInterval = null;
                showFeedback('Auto-refresh stopped', 'success');
            }
        }
        
        function showFeedback(message, type) {
            const feedback = document.getElementById('feedback');
            feedback.textContent = message;
            feedback.className = type;
            feedback.style.display = 'block';
            
            setTimeout(() => {
                feedback.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>
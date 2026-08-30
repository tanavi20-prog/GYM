<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/session.php';

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
    <title>Enhanced Progress Chart Demo</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            max-width: 1000px; 
            margin: 20px auto; 
            padding: 20px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container { 
            background: white; 
            border-radius: 15px; 
            padding: 30px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .card { 
            background: #f8f9fa; 
            padding: 25px; 
            border-radius: 12px; 
            margin-bottom: 25px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .form-group { margin-bottom: 20px; }
        label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 600; 
            color: #333; 
            font-size: 14px;
        }
        input, select { 
            width: 100%; 
            padding: 12px 15px; 
            border: 2px solid #e9ecef; 
            border-radius: 8px; 
            font-size: 15px;
            transition: all 0.3s ease;
        }
        input:focus, select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
            outline: none;
        }
        button { 
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white; 
            padding: 14px 28px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-size: 16px; 
            font-weight: 600;
            margin: 5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,123,255,0.3);
        }
        button:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,123,255,0.4);
        }
        button:active {
            transform: translateY(0);
        }
        button:disabled { 
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        #feedback { 
            margin-top: 20px; 
            padding: 15px; 
            border-radius: 8px; 
            display: none; 
            font-weight: 600;
            animation: slideIn 0.3s ease;
        }
        .success { 
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white; 
        }
        .error { 
            background: linear-gradient(135deg, #dc3545, #e74c3c);
            color: white; 
        }
        #workouts-container { 
            margin-top: 30px;
        }
        .workout-item { 
            background: white; 
            padding: 20px; 
            border-radius: 10px; 
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            border-left: 4px solid #007bff;
            transition: all 0.3s ease;
        }
        .workout-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .stats { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 20px; 
            margin: 25px 0; 
        }
        .stat-card { 
            text-align: center; 
            padding: 25px; 
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .stat-value { 
            font-size: 32px; 
            font-weight: 800; 
            background: linear-gradient(135deg, #007bff, #0056b3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .stat-label { 
            font-size: 16px; 
            color: #6c757d; 
            font-weight: 600;
            margin-top: 8px;
        }
        #progressChart {
            width: 100%;
            height: 300px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 12px;
            margin: 25px 0;
            box-shadow: inset 0 2px 10px rgba(0,0,0,0.1);
        }
        .chart-controls {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 20px 0;
        }
        .time-range-btn {
            background: #6c757d;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .time-range-btn.active {
            background: linear-gradient(135deg, #007bff, #0056b3);
            transform: scale(1.05);
        }
        .motivation-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            margin-top: 15px;
            animation: pulse 2s infinite;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .intensity-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .intensity-low { background: #d4edda; color: #155724; }
        .intensity-moderate { background: #cce5ff; color: #004085; }
        .intensity-high { background: #fff3cd; color: #856404; }
        .intensity-very-high { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="text-align: center; color: #333; margin-bottom: 10px;">🎯 Enhanced Progress Tracker</h1>
        <p style="text-align: center; color: #6c757d; margin-bottom: 30px;">Track your fitness journey with beautiful visualizations</p>
        
        <div class="card">
            <h2>💪 Log New Workout</h2>
            <form id="workout-form">
                <div class="form-group">
                    <label for="workout-name">Exercise Name *</label>
                    <input type="text" id="workout-name" required placeholder="e.g., Push-ups, Running, Yoga">
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
                
                <button type="submit" id="log-btn">🚀 Log Workout</button>
                <button type="button" onclick="loadWorkouts()">🔄 Refresh Data</button>
                <button type="button" onclick="clearAllWorkouts()" style="background: linear-gradient(135deg, #dc3545, #c82333);">🗑️ Clear All</button>
            </form>
            <div id="feedback"></div>
        </div>
        
        <div class="card">
            <h2>📊 Weekly Performance</h2>
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-value" id="week-workouts">0</div>
                    <div class="stat-label">Workouts</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="week-minutes">0</div>
                    <div class="stat-label">Minutes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="avg-duration">0</div>
                    <div class="stat-label">Avg Duration</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="total-calories">0</div>
                    <div class="stat-label">Calories</div>
                </div>
            </div>
            <div id="motivation-message"></div>
        </div>
        
        <div class="card">
            <h2>📈 Progress Visualization</h2>
            <div class="chart-controls">
                <button class="time-range-btn active" onclick="setTimeRange('7')">7 Days</button>
                <button class="time-range-btn" onclick="setTimeRange('14')">14 Days</button>
                <button class="time-range-btn" onclick="setTimeRange('30')">30 Days</button>
            </div>
            <canvas id="progressChart" width="800" height="300"></canvas>
        </div>
        
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>📋 Recent Workouts</h2>
                <div style="font-size: 14px; color: #6c757d;">
                    Last updated: <span id="last-update">Never</span>
                </div>
            </div>
            <div id="workouts-container">
                <p style="text-align: center; color: #666; padding: 40px;">Loading your workout history...</p>
            </div>
        </div>
    </div>

    <script>
        let currentRange = 7;
        let autoRefreshInterval = null;
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadWorkouts();
            startAutoRefresh();
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
                
                if (result.success) {
                    showFeedback('🎉 Workout logged successfully!', 'success');
                    document.getElementById('workout-form').reset();
                    loadWorkouts();
                } else {
                    showFeedback('❌ Error: ' + (result.error || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Error logging workout:', error);
                showFeedback('🌐 Network error. Please try again.', 'error');
            } finally {
                logBtn.disabled = false;
                logBtn.textContent = '🚀 Log Workout';
            }
        }
        
        async function loadWorkouts() {
            try {
                const response = await fetch(`<?= APP_URL ?>/api/user_progress.php?action=get_workouts&limit=50`);
                const result = await response.json();
                
                if (result.success && result.workouts) {
                    displayWorkouts(result.workouts);
                    updateStats(result.workouts);
                    updateChart(result.workouts);
                } else {
                    document.getElementById('workouts-container').innerHTML = 
                        '<p style="text-align: center; color: #666; padding: 40px;">No workouts found</p>';
                    updateChart([]);
                }
                
                document.getElementById('last-update').textContent = new Date().toLocaleTimeString();
            } catch (error) {
                console.error('Error loading workouts:', error);
                document.getElementById('workouts-container').innerHTML = 
                    '<p style="text-align: center; color: #dc3545; padding: 40px;">❌ Error loading workouts</p>';
            }
        }
        
        function updateStats(workouts) {
            const weekAgo = new Date();
            weekAgo.setDate(weekAgo.getDate() - 7);
            
            const thisWeek = workouts.filter(w => {
                const workoutDate = new Date(w.date);
                return workoutDate >= weekAgo;
            });
            
            const totalMinutes = thisWeek.reduce((total, w) => total + w.duration, 0);
            const totalCalories = thisWeek.reduce((total, w) => total + (w.calories || 0), 0);
            const avgDuration = thisWeek.length > 0 ? Math.round(totalMinutes / thisWeek.length) : 0;
            
            document.getElementById('week-workouts').textContent = thisWeek.length;
            document.getElementById('week-minutes').textContent = totalMinutes;
            document.getElementById('avg-duration').textContent = avgDuration;
            document.getElementById('total-calories').textContent = totalCalories;
            
            // Update motivation message
            updateMotivationMessage(avgDuration);
        }
        
        function updateMotivationMessage(avgDuration) {
            let message = '';
            let badgeClass = '';
            
            if (avgDuration > 60) {
                message = '🔥 You\'re on fire! Exceptional consistency!';
                badgeClass = 'background: linear-gradient(135deg, #ff6b6b, #ffa500); color: white;';
            } else if (avgDuration > 45) {
                message = '💪 Excellent work! Keep up the great routine!';
                badgeClass = 'background: linear-gradient(135deg, #4ecdc4, #44a08d); color: white;';
            } else if (avgDuration > 30) {
                message = '👍 Great progress! You\'re building solid habits!';
                badgeClass = 'background: linear-gradient(135deg, #a8edea, #fed6e3); color: #333;';
            } else if (avgDuration > 15) {
                message = '🚀 Good start! Every workout counts!';
                badgeClass = 'background: linear-gradient(135deg, #ffecd2, #fcb69f); color: #333;';
            } else {
                message = '🌟 Keep going! Consistency is key!';
                badgeClass = 'background: linear-gradient(135deg, #d4fc79, #96e6a1); color: #333;';
            }
            
            document.getElementById('motivation-message').innerHTML = 
                `<div class="motivation-badge" style="${badgeClass}">${message}</div>`;
        }
        
        function updateChart(workouts) {
            const canvas = document.getElementById('progressChart');
            if (!canvas) return;
            
            const ctx = canvas.getContext('2d');
            const width = canvas.width;
            const height = canvas.height;
            
            // Clear canvas
            ctx.clearRect(0, 0, width, height);
            
            if (workouts.length === 0) {
                // Empty state
                ctx.fillStyle = '#6c757d';
                ctx.font = 'bold 20px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('No workout data yet', width / 2, height / 2 - 10);
                
                ctx.font = '16px Arial';
                ctx.fillStyle = '#adb5bd';
                ctx.fillText('Start logging workouts to see your progress!', width / 2, height / 2 + 20);
                return;
            }
            
            // Filter workouts by current range
            const rangeAgo = new Date();
            rangeAgo.setDate(rangeAgo.getDate() - currentRange);
            
            const filteredWorkouts = workouts.filter(w => {
                const workoutDate = new Date(w.date);
                return workoutDate >= rangeAgo;
            }).reverse();
            
            if (filteredWorkouts.length === 0) {
                ctx.fillStyle = '#6c757d';
                ctx.font = 'bold 20px Arial';
                ctx.textAlign = 'center';
                ctx.fillText(`No workouts in the last ${currentRange} days`, width / 2, height / 2);
                return;
            }
            
            // Chart configuration
            const padding = 50;
            const chartWidth = width - 2 * padding;
            const chartHeight = height - 2 * padding;
            const maxValue = Math.max(...filteredWorkouts.map(w => w.duration), 60);
            
            // Draw grid
            ctx.strokeStyle = '#e9ecef';
            ctx.lineWidth = 1;
            
            // Horizontal grid lines
            for (let i = 0; i <= 5; i++) {
                const y = padding + (chartHeight * i / 5);
                ctx.beginPath();
                ctx.moveTo(padding, y);
                ctx.lineTo(padding + chartWidth, y);
                ctx.stroke();
                
                // Y-axis labels
                ctx.fillStyle = '#6c757d';
                ctx.font = '12px Arial';
                ctx.textAlign = 'right';
                const value = Math.round(maxValue - (maxValue * i / 5));
                ctx.fillText(value, padding - 10, y + 4);
            }
            
            // Draw bars
            const barWidth = chartWidth / filteredWorkouts.length;
            const barSpacing = barWidth * 0.3;
            const actualBarWidth = barWidth - barSpacing;
            
            filteredWorkouts.forEach((workout, index) => {
                const barHeight = (workout.duration / maxValue) * chartHeight;
                const x = padding + index * barWidth + barSpacing / 2;
                const y = padding + chartHeight - barHeight;
                
                // Create gradient based on intensity
                const gradient = ctx.createLinearGradient(x, y, x, y + barHeight);
                
                switch(workout.intensity) {
                    case 'low':
                        gradient.addColorStop(0, '#4caf50');
                        gradient.addColorStop(1, '#81c784');
                        break;
                    case 'moderate':
                        gradient.addColorStop(0, '#2196f3');
                        gradient.addColorStop(1, '#64b5f6');
                        break;
                    case 'high':
                        gradient.addColorStop(0, '#ff9800');
                        gradient.addColorStop(1, '#ffb74d');
                        break;
                    case 'very-high':
                        gradient.addColorStop(0, '#f44336');
                        gradient.addColorStop(1, '#e57373');
                        break;
                    default:
                        gradient.addColorStop(0, '#9c27b0');
                        gradient.addColorStop(1, '#ba68c8');
                }
                
                ctx.fillStyle = gradient;
                ctx.fillRect(x, y, actualBarWidth, barHeight);
                
                // Bar border
                ctx.strokeStyle = '#333';
                ctx.lineWidth = 1;
                ctx.strokeRect(x, y, actualBarWidth, barHeight);
                
                // Date label
                const date = new Date(workout.date);
                const dateStr = date.getDate() + '/' + (date.getMonth() + 1);
                
                ctx.fillStyle = '#495057';
                ctx.font = 'bold 12px Arial';
                ctx.textAlign = 'center';
                ctx.fillText(dateStr, x + actualBarWidth / 2, padding + chartHeight + 25);
                
                // Value on bar
                if (barHeight > 25) {
                    ctx.fillStyle = '#ffffff';
                    ctx.font = 'bold 11px Arial';
                    ctx.fillText(workout.duration, x + actualBarWidth / 2, y + 18);
                }
            });
            
            // Chart title
            ctx.fillStyle = '#212529';
            ctx.font = 'bold 18px Arial';
            ctx.textAlign = 'center';
            ctx.fillText(`Last ${currentRange} Days - Daily Minutes`, width / 2, 30);
        }
        
        function setTimeRange(range) {
            currentRange = parseInt(range);
            
            // Update active button
            document.querySelectorAll('.time-range-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Update chart
            loadWorkouts();
        }
        
        function displayWorkouts(workouts) {
            const container = document.getElementById('workouts-container');
            
            if (workouts.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #666; padding: 40px;">No workouts logged yet</p>';
                return;
            }
            
            container.innerHTML = workouts.slice(0, 10).map(workout => `
                <div class="workout-item">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="font-size: 18px;">${workout.name}</strong>
                            <div style="font-size: 14px; color: #666; margin-top: 5px;">
                                ${workout.date} at ${workout.time}
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 16px; margin-bottom: 8px;">
                                <span style="font-weight: bold; color: #007bff;">${workout.duration} minutes</span>
                            </div>
                            <div class="intensity-badge intensity-${workout.intensity}">
                                ${workout.intensity}
                            </div>
                            ${workout.calories ? `<div style="font-size: 14px; color: #28a745; margin-top: 5px;">🔥 ${workout.calories} calories</div>` : ''}
                            <button onclick="deleteWorkout(${workout.id})" style="background: #dc3545; color: white; padding: 6px 12px; border: none; border-radius: 4px; font-size: 12px; margin-top: 10px; cursor: pointer;">Delete</button>
                        </div>
                    </div>
                </div>
            `).join('');
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
                        showFeedback('✅ Workout deleted successfully', 'success');
                        loadWorkouts();
                    } else {
                        showFeedback('❌ Error: ' + result.error, 'error');
                    }
                } catch (error) {
                    console.error('Error deleting workout:', error);
                    showFeedback('❌ Error deleting workout', 'error');
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
                        showFeedback('🗑️ All workouts cleared successfully', 'success');
                        loadWorkouts();
                    } else {
                        showFeedback('❌ Error: ' + result.error, 'error');
                    }
                } catch (error) {
                    console.error('Error clearing workouts:', error);
                    showFeedback('❌ Error clearing workouts', 'error');
                }
            }
        }
        
        function startAutoRefresh() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }
            
            autoRefreshInterval = setInterval(() => {
                loadWorkouts();
            }, 30000); // Update every 30 seconds
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
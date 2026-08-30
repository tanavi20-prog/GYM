<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dramatic Theme Demo - Gym Website</title>
    <style>
        /* LIGHT THEME - White with Green */
        :root {
            --bg-color: #ffffff;
            --text-color: #1f2937;
            --primary-color: #22c55e;
            --accent-color: #10b981;
            --card-bg: #f0fdf4;
            --border-color: #d1fae5;
            --shadow: 0 4px 16px rgba(34, 197, 94, 0.1);
        }

        /* DARK THEME - Green on Black */
        [data-theme="dark"] {
            --bg-color: #000000;
            --text-color: #ffffff;
            --primary-color: #00ff00;
            --accent-color: #22c55e;
            --card-bg: #0d1117;
            --border-color: #333333;
            --shadow: 0 8px 32px rgba(0, 255, 0, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: var(--bg-color);
            color: var(--text-color);
            min-height: 100vh;
            transition: all 0.5s ease;
            padding: 2rem;
        }

        /* Light theme background - White with Green tints */
        body:not([data-theme="dark"]) {
            background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 50%, #ecfdf5 100%);
        }

        /* Dark theme background - Pure Black with Green */
        [data-theme="dark"] body {
            background: linear-gradient(135deg, #000000 0%, #0d1117 50%, #111111 100%);
            position: relative;
        }

        [data-theme="dark"] body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 20%, rgba(0,255,0,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(34,197,94,0.08) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }


        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        h1 {
            font-size: 3rem;
            margin-bottom: 2rem;
            text-align: center;
            transition: all 0.5s ease;
        }

        /* Light theme heading */
        body:not([data-theme="dark"]) h1 {
            color: var(--primary-color);
            text-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
        }

        /* Dark theme heading - simple green */
        [data-theme="dark"] h1 {
            color: var(--primary-color);
            text-shadow: 0 0 5px var(--primary-color);
        }


        .theme-toggle {
            position: fixed;
            top: 2rem;
            right: 2rem;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .theme-toggle:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow);
        }

        /* Dark theme toggle button glow */
        [data-theme="dark"] .theme-toggle {
            background: var(--primary-color);
            box-shadow: 
                0 0 20px var(--primary-color),
                0 4px 16px rgba(255, 0, 128, 0.4);
        }

        [data-theme="dark"] .theme-toggle:hover {
            box-shadow: 
                0 0 30px var(--primary-color),
                0 6px 20px rgba(255, 0, 128, 0.6);
        }

        .card {
            background: var(--card-bg);
            border: 2px solid var(--border-color);
            border-radius: 20px;
            padding: 2rem;
            margin: 2rem 0;
            box-shadow: var(--shadow);
            transition: all 0.5s ease;
        }

        /* Light theme card */
        body:not([data-theme="dark"]) .card {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border-color: var(--primary-color);
        }

        body:not([data-theme="dark"]) .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 32px rgba(37, 99, 235, 0.2);
        }

        /* Dark theme card with neon border */
        [data-theme="dark"] .card {
            background: linear-gradient(145deg, var(--card-bg), #1a1a2e);
            border-color: var(--primary-color);
            position: relative;
        }

        [data-theme="dark"] .card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, var(--primary-color), var(--accent-color), var(--primary-color));
            border-radius: 20px;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        [data-theme="dark"] .card:hover::before {
            opacity: 0.3;
        }

        [data-theme="dark"] .card:hover {
            transform: translateY(-8px);
            box-shadow: 
                0 12px 40px rgba(255, 0, 128, 0.4),
                0 0 60px rgba(0, 255, 255, 0.2);
        }


        .feature {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1rem 0;
            padding: 1rem;
            background: var(--card-bg);
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            transition: all 0.3s ease;
        }

        /* Dark theme feature icon glow */
        [data-theme="dark"] .feature-icon {
            box-shadow: 
                0 0 20px var(--primary-color),
                0 0 40px var(--primary-color);
        }

        .button {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 1rem;
        }

        /* Light theme button */
        body:not([data-theme="dark"]) .button:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
        }

        /* Dark theme button with glow */
        [data-theme="dark"] .button {
            box-shadow: 0 0 15px var(--primary-color);
        }

        [data-theme="dark"] .button:hover {
            background: #e6007a;
            transform: translateY(-3px);
            box-shadow: 
                0 8px 25px rgba(255, 0, 128, 0.5),
                0 0 30px var(--primary-color);
        }

        .theme-info {
            text-align: center;
            margin: 2rem 0;
            font-size: 1.2rem;
        }

        /* Dark theme text glow */
        [data-theme="dark"] .theme-info {
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body data-theme="light">
    <button class="theme-toggle" onclick="toggleTheme()">
        🌙 Switch to Dark Theme
    </button>

    <div class="container">
        <h1>🏋️ GYM THEME DEMO</h1>
        
        <div class="theme-info">
            <p>Click the theme toggle button to see the DRAMATIC difference!</p>
        </div>

        <div class="card">
            <h2>🌟 Theme Features</h2>
            
            <div class="feature">
                <div class="feature-icon">💡</div>
                <div>
                    <h3>Light Theme</h3>
                    <p>Clean white background with professional green colors</p>
                </div>
            </div>

            <div class="feature">
                <div class="feature-icon">🚀</div>
                <div>
                    <h3>Dark Theme</h3>
                    <p>Pure black background with bright green neon effects</p>
                </div>
            </div>

            <div class="feature">
                <div class="feature-icon">✨</div>
                <div>
                    <h3>Special Effects</h3>
                    <p>Hover over cards and buttons to see theme-specific animations</p>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>🎯 Interactive Elements</h2>
            <p>Try hovering over these buttons to see theme differences:</p>
            <button class="button">Light Theme Button</button>
            <button class="button">Another Button</button>
        </div>

        <div class="card">
            <h2>🎨 Color Comparison</h2>
            <p><strong>Light Theme:</strong> White background (#ffffff) with green (#22c55e)</p>
            <p><strong>Dark Theme:</strong> Pure black (#000000) with bright green (#00ff00)</p>
            <p><strong>Background:</strong> Clean white gradient vs Pure black with green neon overlays</p>
        </div>
    </div>

    <script>
        function toggleTheme() {
            const body = document.body;
            const button = document.querySelector('.theme-toggle');
            const currentTheme = body.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            body.setAttribute('data-theme', newTheme);
            
            if (newTheme === 'dark') {
                button.textContent = '☀️ Switch to Light Theme';
            } else {
                button.textContent = '🌙 Switch to Dark Theme';
            }
            
            // Save theme preference
            localStorage.setItem('gym-theme', newTheme);
        }

        // Load saved theme
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('gym-theme') || 'light';
            const body = document.body;
            const button = document.querySelector('.theme-toggle');
            
            body.setAttribute('data-theme', savedTheme);
            
            if (savedTheme === 'dark') {
                button.textContent = '☀️ Switch to Light Theme';
            } else {
                button.textContent = '🌙 Switch to Dark Theme';
            }
        });
    </script>
</body>
</html>
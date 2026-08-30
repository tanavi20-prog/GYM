<?php
// RKT fitvibe - Main Index Page with Simple Routing
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load config and session early so we can check authentication for routing
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/session.php';

// Simple routing for backward compatibility
if (isset($_GET['page'])) {
    $page = $_GET['page'];
    $allowed_pages = ['home', 'login', 'registration', 'trainer', 'community', 'plan', 'tool', 'yoga', 'diet', 'dashboard', 'profile', 'crud', 'logout', 'videos', 'video-player', 'trainer-workouts', 'explore-workouts', 'help', 'about', 'trainer-profile', 'forgot-password'];

    // Guest users may only access a limited set of pages
    $guest_allowed = ['home', 'login', 'registration', 'forgot-password'];
    $isGuest = !function_exists('is_logged_in') || !is_logged_in();

    if ($isGuest && !in_array($page, $guest_allowed)) {
        // Redirect guests to home page (preserve friendly message)
        header('Location: /gymmm/?page=home');
        exit;
    }

    if (in_array($page, $allowed_pages)) {
        $file_path = "pages/{$page}.php";
        if (file_exists($file_path)) {
            // Change to the pages directory to fix relative paths
            $original_dir = getcwd();
            chdir('pages');
            
            // Include the page
            try {
                include $page . '.php';
            } catch (Exception $e) {
                // If page fails, show error and go back to main directory
                chdir($original_dir);
                echo "<div style='padding: 2rem; background: #fee; border: 1px solid #f00; margin: 2rem; border-radius: 8px;'>";
                echo "<h2>Page Error: {$page}.php</h2>";
                echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
                echo "<p><a href='/'>&larr; Back to Dashboard</a></p>";
                echo "</div>";
                exit;
            }
            
            // Change back to original directory
            chdir($original_dir);
            exit;
        } else {
            // File doesn't exist
            echo "<div style='padding: 2rem; background: #fee; border: 1px solid #f00; margin: 2rem; border-radius: 8px;'>";
            echo "<h2>File Not Found: {$page}.php</h2>";
            echo "<p>The file 'pages/{$page}.php' does not exist.</p>";
            echo "<p><a href='/'>&larr; Back to Dashboard</a></p>";
            echo "</div>";
            exit;
        }
    } else {
        // Page not in allowed pages
        echo "<div style='padding: 2rem; background: #fee; border: 1px solid #f00; margin: 2rem; border-radius: 8px;'>";
        echo "<h2>Page Not Allowed: {$page}</h2>";
        echo "<p>The page '{$page}' is not in the list of allowed pages.</p>";
        echo "<p>Allowed pages: " . implode(', ', $allowed_pages) . "</p>";
        echo "<p><a href='/'>&larr; Back to Dashboard</a></p>";
        echo "</div>";
        exit;
    }
}

// Handle old direct file requests by redirecting
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $request_uri = $_SERVER['REQUEST_URI'];
    $script_name = $_SERVER['SCRIPT_NAME'];
    
    // Extract the requested file name from URL
    if (preg_match('/\/gymmm\/([^?]+\.php)/', $request_uri, $matches)) {
        $requested_file = $matches[1];
        
        // Check if it's a page that should be in pages/ folder
        $page_files = ['home.php', 'login.php', 'registration.php', 'trainer.php', 'community.php', 'plan.php', 'tool.php', 'yoga.php', 'diet.php', 'dashboard.php', 'profile.php'];
        
        if (in_array($requested_file, $page_files)) {
            $page_name = str_replace('.php', '', $requested_file);
            header("Location: /gymmm/?page={$page_name}");
            exit;
        }
    }
}

// Test if configuration works
$config_loaded = false;
try {
    if (file_exists('includes/config.php')) {
        require_once 'includes/config.php';
        $config_loaded = true;
    }
} catch (Exception $e) {
    $config_loaded = false;
}

// Check system status
$system_status = [
    'php' => true,
    'config' => $config_loaded,
    'css' => file_exists('assets/css/style.css'),
    'images' => is_dir('assets/images/trainers'),
    'database' => false
];

// Test database connection with multiple methods
try {
    // First try with Database class (PDO)
    if ($config_loaded && file_exists('includes/database.php')) {
        require_once 'includes/database.php';
        $db = Database::getInstance();
        if ($db && $db->getConnection()) {
            // Test with a simple query
            $pdo = $db->getConnection();
            $stmt = $pdo->query("SELECT 1");
            if ($stmt) {
                $system_status['database'] = true;
            }
        }
    }
    
    // If PDO failed, try with mysqli connection
    if (!$system_status['database'] && file_exists('crud/connect.php')) {
        require_once 'crud/connect.php';
        $conn = getConnection();
        if ($conn && !$conn->connect_error) {
            // Test with a simple query
            $result = $conn->query("SELECT 1");
            if ($result) {
                $system_status['database'] = true;
            }
        }
    }
} catch (Exception $e) {
    $system_status['database'] = false;
}
?>

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RKT fitvibe - Welcome Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f0f0f, #1a1a1a);
            color: #fff;
            min-height: 100vh;
            padding: 2rem;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        :root {
            --primary: #22c55e;
            --gradient-primary: linear-gradient(135deg, #22c55e, #16a34a);
            --shadow-primary: 0 4px 20px rgba(34, 197, 94, 0.2);
        }
        h1 {
            color: var(--primary);
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 2rem;
            text-shadow: 0 0 20px rgba(34, 197, 94, 0.3);
        }
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        .card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 2rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 107, 53, 0.3);
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(255, 107, 53, 0.3);
        }
        .card h2 {
            color: var(--primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0.5rem 0;
        }
        .status.success { color: #22c55e; }
        .status.error { color: #ef4444; }
        .nav-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem;
            background: var(--gradient-primary);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .nav-link:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-primary);
        }
        .nav-link.secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .nav-link.secondary:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .footer {
            text-align: center;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #ccc;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏋️ RKT fitvibe Dashboard</h1>
        
        <div class="dashboard">
            <!-- System Status Card -->
            <div class="card">
                <h2>🔧 System Status</h2>
                <?php foreach ($system_status as $component => $status): ?>
                    <div class="status <?= $status ? 'success' : 'error' ?>">
                        <?= $status ? '✅' : '❌' ?>
                        <?= ucfirst($component) ?>: <?= $status ? 'Working' : 'Error' ?>
                    </div>
                <?php endforeach; ?>
                
                <?php if (!$system_status['database']): ?>
                    <div style="margin-top: 1rem; padding: 1rem; background: rgba(239, 68, 68, 0.2); border-radius: 8px; border: 1px solid rgba(239, 68, 68, 0.3);">
                        <strong>⚠️ Database Issue Detected</strong><br>
                        <small>Please run the database setup to fix registration issues.</small>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Main Navigation Card -->
            <div class="card">
                <h2>🚀 Main Application</h2>
                <p>Access your fitness platform:</p>
                <div class="nav-links">
                    <a href="?page=home" class="nav-link">
                        🏠 Home Page
                    </a>
                    <a href="?page=registration" class="nav-link">
                        📝 Register
                    </a>
                    <a href="?page=login" class="nav-link">
                        🔐 Login
                    </a>
                    <a href="?page=trainer" class="nav-link">
                        👨‍💼 Trainers
                    </a>
                </div>
            </div>
            
            <!-- Quick Actions Card -->
            <div class="card">
                <h2>⚡ Quick Actions</h2>
                <div class="nav-links">
                    <a href="?page=videos" class="nav-link" style="background: linear-gradient(135deg, #dc2626, #b91c1c);">
                        🎥 Video Library
                    </a>
                    <a href="?page=plan" class="nav-link" style="background: linear-gradient(135deg, #22c55e, #16a34a);">
                        📊 Workout Plans
                    </a>
                    <a href="?page=trainer" class="nav-link" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                        👨‍💼 Trainers
                    </a>
                    <a href="?page=tool" class="nav-link" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        🛠️ Fitness Tools
                    </a>
                </div>
            </div>
            
            <!-- More Pages Card -->
            <div class="card">
                <h2>📊 More Features</h2>
                <p>Additional platform features:</p>
                <div class="nav-links">
                    <a href="?page=community" class="nav-link secondary">
                        👥 Community
                    </a>
                    <a href="?page=yoga" class="nav-link secondary">
                        🧘 Yoga
                    </a>
                    <a href="?page=diet" class="nav-link secondary">
                        🥗 Diet Plans
                    </a>
                    <a href="?page=dashboard" class="nav-link secondary">
                        📈 Dashboard
                    </a>
                    <a href="?page=crud" class="nav-link secondary" style="background: linear-gradient(135deg, #ff6b35, #f7931e); color: white;">
                        📊 CRUD Operations
                    </a>
                </div>
            </div>
            
            <!-- Developer Tools Card -->
            <div class="card" style="border: 2px solid #2563eb;">
                <h2>🛠️ Developer Tools</h2>
                <p>Development and maintenance tools:</p>
                <div class="nav-links">
                    <a href="dev-tools/setup/" class="nav-link secondary">
                        ⚙️ Setup Tools
                    </a>
                    <a href="dev-tools/tests/" class="nav-link secondary">
                        🧪 Test Files
                    </a>
                    <a href="dev-tools/debug/" class="nav-link secondary">
                        🐛 Debug Tools
                    </a>
                    <a href="docs/" class="nav-link secondary">
                        📚 Documentation
                    </a>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; 2025 RKT fitvibe. PHP <?= phpversion() ?> | Server Time: <?= date('Y-m-d H:i:s') ?></p>
            <p>Transform Your Body, Transform Your Life</p>
        </div>
    </div>
</body>
</html>

# RKT FitVibe - Developer Quick Reference

## 🚀 Quick Development Setup

### Local Environment
```bash
# Start WAMP services
# Navigate to: http://localhost/gymmm

# Database connection test
http://localhost/gymmm/pages/db-test.php

# Video player test
http://localhost/gymmm/pages/test-player.php
```

### Key Configuration Files
- `includes/config.php` - Database and app configuration
- `includes/session.php` - User session management
- `index.php` - Main router with allowed pages

## 🔗 URL Routing System

### Page Navigation Pattern
```php
// Base URL format
http://localhost/gymmm/?page=PAGENAME

// Examples
http://localhost/gymmm/?page=home
http://localhost/gymmm/?page=trainer&show_videos=1
http://localhost/gymmm/?page=video-player&video_id=123
```

### Allowed Pages Array (index.php)
```php
$allowed_pages = [
    'home', 'login', 'registration', 'trainer', 'community', 
    'plan', 'tool', 'yoga', 'diet', 'dashboard', 'profile', 
    'crud', 'logout', 'videos', 'video-player', 
    'trainer-workouts', 'explore-workouts', 'help', 'about'
];
```

## 🗄️ Database Quick Reference

### Connection Pattern
```php
// Standard connection (config.php)
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
```

### Core Tables Schema

#### Users Table
```sql
users (id, username, email, password, full_name, age, weight, height, fitness_goal, activity_level, created_at)
```

#### Trainers Table
```sql
trainers (id, name, specialization, experience_years, rating, location, bio, image_url, hourly_rate, available, created_at)
```

#### Videos Table
```sql
videos (id, title, description, youtube_id, category_id, difficulty_level, duration_minutes, view_count, trainer_id, thumbnail_url, created_at)
```

#### Video Categories Table
```sql
video_categories (id, name, description, icon)
```

## 🎥 YouTube Video Integration

### Video ID Extraction
```php
// Extract YouTube ID from URL
function extractYouTubeId($url) {
    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
    return isset($matches[1]) ? $matches[1] : false;
}
```

### Video Embedding
```html
<!-- Standard embed format -->
<iframe width="560" height="315" 
        src="https://www.youtube.com/embed/<?= $youtube_id ?>" 
        frameborder="0" allowfullscreen></iframe>

<!-- Thumbnail format -->
<img src="https://img.youtube.com/vi/<?= $youtube_id ?>/maxresdefault.jpg" 
     alt="Video thumbnail">
```

## 🔐 Security Implementation

### Password Hashing
```php
// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Verify password
if (password_verify($input_password, $stored_hash)) {
    // Login successful
}
```

### Prepared Statements
```php
// Safe database queries
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
$stmt->bind_param("ss", $email, $password_hash);
$stmt->execute();
$result = $stmt->get_result();
```

### Input Sanitization
```php
// Escape output
function escape_output($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Clean input
$clean_input = filter_var($input, FILTER_SANITIZE_STRING);
```

## 📱 Responsive Design Classes

### Common CSS Classes
```css
/* Grid system */
.grid { display: grid; gap: 1.5rem; }
.grid-2 { grid-template-columns: repeat(2, 1fr); }
.grid-3 { grid-template-columns: repeat(3, 1fr); }

/* Buttons */
.btn { padding: 0.75rem 1.5rem; border-radius: 0.5rem; }
.btn-primary { background-color: var(--primary); }
.btn-secondary { background-color: var(--secondary); }

/* Cards */
.card { background: var(--card); padding: 1.5rem; border-radius: 0.75rem; }
```

## 🔧 Common Development Tasks

### Adding New Pages
1. Create PHP file in `/pages/` directory
2. Add page name to `$allowed_pages` in `index.php`
3. Use template structure:
```php
<?php
$page_title = 'Page Title';
ob_start();
?>
<!-- Page content here -->
<?php
$content = ob_get_clean();
include '../templates/layout.php';
?>
```

### Database Operations
```php
// Insert new record
$stmt = $conn->prepare("INSERT INTO table_name (col1, col2) VALUES (?, ?)");
$stmt->bind_param("ss", $value1, $value2);
$stmt->execute();

// Update record
$stmt = $conn->prepare("UPDATE table_name SET col1 = ? WHERE id = ?");
$stmt->bind_param("si", $value, $id);
$stmt->execute();

// Delete record
$stmt = $conn->prepare("DELETE FROM table_name WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
```

### Session Management
```php
// Start session
session_start();

// Set session variable
$_SESSION['user_id'] = $user_id;
$_SESSION['username'] = $username;

// Check if logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Logout
session_destroy();
```

## 🎨 UI Components

### Video Card Component
```html
<div class="video-card" onclick="playVideo('<?= $video['youtube_id'] ?>')">
    <div class="video-thumbnail">
        <img src="https://img.youtube.com/vi/<?= $video['youtube_id'] ?>/maxresdefault.jpg" 
             alt="<?= escape_output($video['title']) ?>">
        <div class="play-overlay">
            <div class="play-button">▶</div>
        </div>
    </div>
    <div class="video-info">
        <h3><?= escape_output($video['title']) ?></h3>
        <p><?= escape_output($video['description']) ?></p>
        <div class="video-meta">
            <span class="difficulty <?= $video['difficulty_level'] ?>">
                <?= ucfirst($video['difficulty_level']) ?>
            </span>
            <span class="duration"><?= $video['duration_minutes'] ?> min</span>
        </div>
    </div>
</div>
```

### Trainer Card Component
```html
<div class="trainer-card">
    <div class="trainer-image">
        <img src="<?= escape_output($trainer['image_url']) ?>" 
             alt="<?= escape_output($trainer['name']) ?>">
    </div>
    <div class="trainer-info">
        <h3><?= escape_output($trainer['name']) ?></h3>
        <p class="specialization"><?= escape_output($trainer['specialization']) ?></p>
        <div class="trainer-stats">
            <span class="rating">⭐ <?= $trainer['rating'] ?></span>
            <span class="experience"><?= $trainer['experience_years'] ?> years</span>
            <span class="rate">₹<?= $trainer['hourly_rate'] ?>/hour</span>
        </div>
    </div>
</div>
```

## 🔍 Debugging Tips

### Common Error Checks
```php
// Database connection
if ($conn->connect_error) {
    error_log("DB Connection failed: " . $conn->connect_error);
}

// File includes
if (!file_exists('../includes/config.php')) {
    die('Configuration file not found');
}

// Session debugging
if (session_status() !== PHP_SESSION_ACTIVE) {
    error_log('Session not started');
}
```

### Useful Debug Functions
```php
// Debug variable
function debug_var($var, $label = '') {
    echo "<pre><strong>$label</strong>\n";
    print_r($var);
    echo "</pre>";
}

// Log to file
error_log("Debug info: " . print_r($variable, true));

// Check SQL errors
if ($stmt->error) {
    error_log("SQL Error: " . $stmt->error);
}
```

## 📊 Performance Tips

### Database Optimization
- Use prepared statements for repeated queries
- Add indexes to frequently searched columns
- Limit results with LIMIT clause
- Use appropriate data types

### Frontend Optimization
- Minify CSS/JS in production
- Optimize images before upload
- Use lazy loading for videos
- Enable browser caching

## 🌐 Deployment Checklist

### Pre-deployment
- [ ] Update database credentials in config.php
- [ ] Set production APP_URL
- [ ] Enable error logging, disable display errors
- [ ] Test all page routes
- [ ] Verify YouTube video functionality
- [ ] Check responsive design on multiple devices

### Production Security
- [ ] Use HTTPS with SSL certificate
- [ ] Set secure database passwords
- [ ] Configure proper file permissions
- [ ] Enable Apache security modules
- [ ] Set up regular database backups

---

*For complete documentation, refer to `WEBSITE_DOCUMENTATION.md`*
<?php
// Simple trainer display test - no authentication required
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/helpers.php';
require_once '../crud/connect.php';

$page_title = 'Trainer Showcase';
$page_description = 'Browse our expert fitness trainers';

// Get trainers from database (same logic as trainer.php but without auth requirement)
$trainers = [];

try {
    $conn = getConnection();
    // Get trainers with their video counts
    $result = $conn->query("SELECT t.*, 
                                   COUNT(v.id) as video_count,
                                   GROUP_CONCAT(DISTINCT vc.name) as video_categories
                            FROM trainers t 
                            LEFT JOIN videos v ON t.name = v.instructor_name AND v.is_active = 1
                            LEFT JOIN video_categories vc ON v.category_id = vc.id
                            GROUP BY t.id
                            ORDER BY t.rating DESC, t.experience_years DESC");

    while ($row = $result->fetch_assoc()) {
        // Only show available trainers
        if (!isset($row['available']) || !$row['available']) {
            continue;
        }
        
        // Handle Specialties correctly (JSON or Comma-separated)
        $specialtiesRaw = $row['specialties'];
        $specialtiesArray = [];

        if (!empty($specialtiesRaw)) {
            // Try to decode as JSON
            $decoded = json_decode($specialtiesRaw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $specialtiesArray = $decoded;
            } else {
                // If not JSON, explode by comma
                $specialtiesArray = explode(',', $specialtiesRaw);
            }
        } else {
            // Fallback if empty
            $specialtiesArray = ['Personal Training']; 
        }

        // Clean up whitespace
        $specialtiesArray = array_map('trim', $specialtiesArray);

        $trainers[] = [
            'id' => $row['id'],
            'name' => function_exists('format_name') ? format_name($row['name']) : $row['name'],
            'specialties' => $specialtiesArray, 
            'rating' => (float)($row['rating'] ?? 5.0),
            'review_count' => rand(50, 200), // Mock data
            'clients' => rand(80, 250), // Mock data
            'experience' => ($row['experience_years'] ?? 1) . '+ years',
            'location' => !empty($row['location']) ? $row['location'] : 'Location not specified',
            'hourly_rate' => (float)($row['hourly_rate'] ?? 50.00),
            'available' => (bool)($row['available'] ?? true),
            'bio' => !empty($row['bio']) ? $row['bio'] : 'Experienced fitness professional',
            'certifications' => ['Certified Professional'], // Mock data
            'languages' => ['English'], 
            'image' => !empty($row['image_url']) ? $row['image_url'] : 'https://images.unsplash.com/photo-1594736797933-d0501ba2fe65?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
            'video_count' => (int)($row['video_count'] ?? 0),
            'video_categories' => $row['video_categories'] ?? '',
            'youtube_video' => $row['youtube_video'] ?? ''
        ];
    }
} catch (Exception $e) {
    error_log("Failed to load trainers: " . $e->getMessage());
}

// If no trainers in database, use mock data
if (empty($trainers)) {
    $trainers = [
        [
            'id' => 1,
            'name' => 'Alex Thompson',
            'specialties' => ['Strength Training', 'Weight Loss'],
            'rating' => 4.8,
            'review_count' => 127,
            'clients' => 89,
            'experience' => '5+ years',
            'location' => 'Chicago, IL',
            'hourly_rate' => 80,
            'available' => true,
            'bio' => 'Certified personal trainer with expertise in strength training and weight loss.',
            'certifications' => ['NASM-CPT', 'NSCA-CSCS'],
            'languages' => ['English'],
            'image' => 'https://images.unsplash.com/photo-1594736797933-d0501ba2fe65?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
            'youtube_video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - RKT FitVibe</title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css?v=<?= time() ?>">
    <style>
        .trainer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }
        .trainer-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        .trainer-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }
        .trainer-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .trainer-info {
            padding: 1.5rem;
        }
        .trainer-name {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }
        .trainer-specialties {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin: 1rem 0;
        }
        .specialty-tag {
            background: #e1f0ff;
            color: #0066cc;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
        }
        .trainer-stats {
            display: flex;
            justify-content: space-between;
            margin: 1rem 0;
            font-size: 0.875rem;
            color: #666;
        }
        .price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #22c55e;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #22c55e;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            text-align: center;
            width: 100%;
            margin-top: 1rem;
            transition: background 0.3s ease;
        }
        .btn:hover {
            background: #16a34a;
        }
        .rating {
            color: #f59e0b;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 2rem;">
        <header style="text-align: center; margin-bottom: 3rem;">
            <h1>🌟 Expert Fitness Trainers</h1>
            <p style="color: #666; font-size: 1.125rem; margin-top: 0.5rem;">
                Connect with certified professionals who will guide your fitness journey
            </p>
        </header>

        <?php if (empty($trainers)): ?>
            <div style="text-align: center; padding: 3rem;">
                <h2>No Trainers Available</h2>
                <p style="color: #666; margin: 1rem 0;">
                    We're currently adding new trainers to our platform.
                </p>
                <a href="<?= APP_URL ?>/add_sample_trainers.php" class="btn" style="display: inline-block; width: auto;">
                    Add Sample Trainers
                </a>
            </div>
        <?php else: ?>
            <div class="trainer-grid">
                <?php foreach ($trainers as $trainer): ?>
                    <div class="trainer-card">
                        <img src="<?= $trainer['image'] ?>" alt="<?= $trainer['name'] ?>" class="trainer-image">
                        <div class="trainer-info">
                            <h3 class="trainer-name"><?= htmlspecialchars($trainer['name']) ?></h3>
                            <p style="color: #666; margin-bottom: 1rem;"><?= htmlspecialchars($trainer['bio']) ?></p>
                            
                            <div class="trainer-specialties">
                                <?php foreach ($trainer['specialties'] as $specialty): ?>
                                    <span class="specialty-tag"><?= htmlspecialchars($specialty) ?></span>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="trainer-stats">
                                <div>
                                    <span class="rating">★ <?= number_format($trainer['rating'], 1) ?></span>
                                    <span>(<?= $trainer['review_count'] ?> reviews)</span>
                                </div>
                                <div><?= $trainer['experience'] ?></div>
                                <div><?= $trainer['clients'] ?> clients</div>
                            </div>
                            
                            <div class="price">₹<?= number_format($trainer['hourly_rate']) ?>/hour</div>
                            <div style="color: #666; font-size: 0.875rem; margin: 0.5rem 0;">
                                📍 <?= htmlspecialchars($trainer['location']) ?>
                            </div>
                            
                            <a href="<?= APP_URL ?>/pages/trainer-profile.php?id=<?= $trainer['id'] ?>" class="btn">
                                View Profile & Book Session
                            </a>
                            
                            <?php if (!empty($trainer['youtube_video'])): ?>
                                <a href="<?= $trainer['youtube_video'] ?>" target="_blank" class="btn" style="background: #ff0000; margin-top: 0.5rem;">
                                    Watch Intro Video
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 3rem; padding: 2rem; background: #f8f9fa; border-radius: 12px;">
            <h3>Ready to Start Your Fitness Journey?</h3>
            <p style="color: #666; margin: 1rem 0;">
                Register an account to book sessions, track your progress, and connect with trainers
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="<?= APP_URL ?>/pages/registration.php" class="btn" style="background: #2563eb; width: auto;">
                    Create Free Account
                </a>
                <a href="<?= APP_URL ?>/pages/login.php" class="btn" style="background: #4b5563; width: auto;">
                    Login to Account
                </a>
                <a href="<?= APP_URL ?>/pages/trainer.php" class="btn" style="background: #7c3aed; width: auto;">
                    Full Trainer Search
                </a>
            </div>
        </div>
    </div>
</body>
</html>
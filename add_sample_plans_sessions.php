<?php
require_once 'crud/connect.php';

$conn = getConnection();

echo "Adding sample workout plans and sessions...\n";

// First, let's check if we have any users and trainers
$userResult = $conn->query("SELECT id FROM users LIMIT 1");
$trainerResult = $conn->query("SELECT id FROM trainers LIMIT 1");

if ($userResult->num_rows == 0 || $trainerResult->num_rows == 0) {
    echo "Error: Need at least one user and one trainer to create plans and sessions.\n";
    echo "Please create users and trainers first through the admin panel.\n";
    exit(1);
}

// Get a sample user and trainer
$userRow = $userResult->fetch_assoc();
$userId = $userRow['id'];

$trainerRow = $trainerResult->fetch_assoc();
$trainerId = $trainerRow['id'];

// Sample workout plans
$plans = [
    [
        'user_id' => $userId,
        'goal' => 'muscle-gain',
        'dietary_preference' => 'none',
        'activity_level' => 'intermediate',
        'plan_details' => json_encode([
            'title' => 'Muscle Building Program',
            'description' => '8-week program focused on hypertrophy and strength gains',
            'duration' => '8 weeks',
            'workouts_per_week' => 4,
            'session_duration' => '60 minutes',
            'exercises' => [
                'Monday: Chest & Triceps',
                'Tuesday: Back & Biceps',
                'Thursday: Legs',
                'Friday: Shoulders & Arms'
            ],
            'nutrition' => [
                'Protein intake: 2g per kg of body weight',
                'Carb cycling: High carbs on workout days',
                'Healthy fats: 20% of daily calories'
            ]
        ])
    ],
    [
        'user_id' => $userId,
        'goal' => 'weight-loss',
        'dietary_preference' => 'vegetarian',
        'activity_level' => 'beginner',
        'plan_details' => json_encode([
            'title' => 'Weight Loss Starter',
            'description' => 'Beginner-friendly program for sustainable fat loss',
            'duration' => '12 weeks',
            'workouts_per_week' => 3,
            'session_duration' => '45 minutes',
            'exercises' => [
                'Monday: Full Body Circuit',
                'Wednesday: Cardio & Core',
                'Friday: Full Body Strength'
            ],
            'nutrition' => [
                'Calorie deficit: 500 calories below maintenance',
                'Plant-based proteins: Lentils, tofu, quinoa',
                'Vegetables: 5 servings per day minimum'
            ]
        ])
    ],
    [
        'user_id' => $userId,
        'goal' => 'endurance',
        'dietary_preference' => 'none',
        'activity_level' => 'advanced',
        'plan_details' => json_encode([
            'title' => 'Marathon Training Plan',
            'description' => 'Advanced program for long-distance runners preparing for a marathon',
            'duration' => '16 weeks',
            'workouts_per_week' => 6,
            'session_duration' => '90 minutes',
            'exercises' => [
                'Monday: Easy Run (60 min)',
                'Tuesday: Speed Work (45 min)',
                'Wednesday: Cross Training (45 min)',
                'Thursday: Tempo Run (50 min)',
                'Friday: Rest or Easy Walk',
                'Saturday: Long Run (varies by week)',
                'Sunday: Recovery Run or Rest'
            ],
            'nutrition' => [
                'Carbohydrate loading: 7-12g per kg of body weight',
                'Hydration: Electrolyte drinks during long runs',
                'Recovery: Protein within 30 minutes post-run'
            ]
        ])
    ]
];

// Insert plans
echo "Inserting sample plans...\n";
$stmt = $conn->prepare("INSERT INTO plans (user_id, goal, dietary_preference, activity_level, plan_details) VALUES (?, ?, ?, ?, ?)");

foreach ($plans as $plan) {
    $stmt->bind_param("issss", 
        $plan['user_id'], 
        $plan['goal'], 
        $plan['dietary_preference'], 
        $plan['activity_level'], 
        $plan['plan_details']
    );
    
    if ($stmt->execute()) {
        echo "✓ Added plan: " . json_decode($plan['plan_details'], true)['title'] . "\n";
    } else {
        echo "✗ Error adding plan: " . $stmt->error . "\n";
    }
}

$stmt->close();

// Now let's add some sessions
echo "\nInserting sample sessions...\n";

// Get the plan IDs we just created
$planResult = $conn->query("SELECT id FROM plans WHERE user_id = $userId ORDER BY id DESC LIMIT 3");
$planIds = [];
while ($row = $planResult->fetch_assoc()) {
    $planIds[] = $row['id'];
}

$sessions = [
    [
        'user_id' => $userId,
        'trainer_id' => $trainerId,
        'plan_id' => $planIds[0] ?? null,
        'session_date' => date('Y-m-d H:i:s', strtotime('+3 days')),
        'duration_minutes' => 60,
        'status' => 'scheduled',
        'notes' => 'First session of muscle building program'
    ],
    [
        'user_id' => $userId,
        'trainer_id' => $trainerId,
        'plan_id' => $planIds[1] ?? null,
        'session_date' => date('Y-m-d H:i:s', strtotime('+5 days')),
        'duration_minutes' => 45,
        'status' => 'scheduled',
        'notes' => 'Introductory weight loss session'
    ],
    [
        'user_id' => $userId,
        'trainer_id' => $trainerId,
        'plan_id' => $planIds[2] ?? null,
        'session_date' => date('Y-m-d H:i:s', strtotime('+7 days')),
        'duration_minutes' => 90,
        'status' => 'scheduled',
        'notes' => 'Long run session for marathon training'
    ],
    [
        'user_id' => $userId,
        'trainer_id' => $trainerId,
        'plan_id' => $planIds[0] ?? null,
        'session_date' => date('Y-m-d H:i:s', strtotime('-2 days')),
        'duration_minutes' => 60,
        'status' => 'completed',
        'notes' => 'Completed chest and triceps workout',
        'rating' => 5,
        'feedback' => 'Great session! Felt strong throughout all exercises.'
    ]
];

$stmt = $conn->prepare("INSERT INTO sessions (user_id, trainer_id, plan_id, session_date, duration_minutes, status, notes, rating, feedback) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

foreach ($sessions as $session) {
    $stmt->bind_param("iiiisisis", 
        $session['user_id'],
        $session['trainer_id'],
        $session['plan_id'],
        $session['session_date'],
        $session['duration_minutes'],
        $session['status'],
        $session['notes'],
        $session['rating'] ?? null,
        $session['feedback'] ?? null
    );
    
    if ($stmt->execute()) {
        echo "✓ Added session on " . date('M j', strtotime($session['session_date'])) . " (" . $session['status'] . ")\n";
    } else {
        echo "✗ Error adding session: " . $stmt->error . "\n";
    }
}

$stmt->close();

echo "\nData insertion complete!\n";

// Verify the data
echo "\n=== VERIFICATION ===\n";
$planCount = $conn->query("SELECT COUNT(*) as count FROM plans WHERE user_id = $userId")->fetch_assoc()['count'];
echo "Total plans for user: $planCount\n";

$sessionCount = $conn->query("SELECT COUNT(*) as count FROM sessions WHERE user_id = $userId")->fetch_assoc()['count'];
echo "Total sessions for user: $sessionCount\n";

echo "\nYou can now view this data in the admin panel under the 'Plans' and 'Sessions' sections.\n";
?>
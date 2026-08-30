<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'crud/connect.php';

// Simulate logged in user for testing
if (!is_logged_in()) {
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = 1;
    $_SESSION['user_email'] = 'test@example.com';
    $_SESSION['user_name'] = 'Test User';
}

$conn = getConnection();
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trainer_id = (int)$_POST['trainer_id'];
    $scheduled_date = $_POST['scheduled_date'];
    
    try {
        // Check if trainer exists
        $stmt = $conn->prepare("SELECT id, name, hourly_rate FROM trainers WHERE id = ? AND available = TRUE");
        $stmt->bind_param("i", $trainer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $message = "Error: Trainer not found or not available.";
        } else {
            $trainer = $result->fetch_assoc();
            
            // Calculate price
            $duration_minutes = 60;
            $hours = $duration_minutes / 60;
            $price = $trainer['hourly_rate'] * $hours;
            
            // Insert booking
            $stmt = $conn->prepare("
                INSERT INTO trainer_sessions 
                (user_id, trainer_id, scheduled_date, duration_minutes, notes, price) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $notes = 'Test booking from web interface';
            $stmt->bind_param("iiisis", $_SESSION['user_id'], $trainer_id, $scheduled_date, $duration_minutes, $notes, $price);
            
            if ($stmt->execute()) {
                $booking_id = $stmt->insert_id;
                $message = "Success: Trainer booked successfully! Booking ID: {$booking_id}";
            } else {
                $message = "Error: Failed to book trainer - " . $stmt->error;
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Get available trainers
$trainers = [];
$result = $conn->query("SELECT id, name, hourly_rate, available FROM trainers ORDER BY name");
while ($row = $result->fetch_assoc()) {
    $trainers[] = $row;
}

// Get user bookings
$bookings = [];
$stmt = $conn->prepare("
    SELECT ts.*, t.name as trainer_name 
    FROM trainer_sessions ts
    JOIN trainers t ON ts.trainer_id = t.id
    WHERE ts.user_id = ?
    ORDER BY ts.scheduled_date ASC
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Trainer Booking Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #005a87; }
        .message { padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Trainer Booking Test</h1>
        
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'Success') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h2>Book a Trainer</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="trainer_id">Select Trainer:</label>
                    <select name="trainer_id" id="trainer_id" required>
                        <option value="">-- Select a Trainer --</option>
                        <?php foreach ($trainers as $trainer): ?>
                            <option value="<?php echo $trainer['id']; ?>" <?php echo !$trainer['available'] ? 'disabled' : ''; ?>>
                                <?php echo htmlspecialchars($trainer['name']); ?> 
                                ($<?php echo $trainer['hourly_rate']; ?>/hr)
                                <?php echo !$trainer['available'] ? '(Not Available)' : ''; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="scheduled_date">Date and Time:</label>
                    <input type="datetime-local" name="scheduled_date" id="scheduled_date" 
                           value="<?php echo date('Y-m-d\TH:i', strtotime('+1 day')); ?>" required>
                </div>
                
                <button type="submit">Book Trainer</button>
            </form>
        </div>
        
        <div class="card">
            <h2>Your Bookings</h2>
            <?php if (empty($bookings)): ?>
                <p>You have no bookings yet.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Trainer</th>
                            <th>Date/Time</th>
                            <th>Duration</th>
                            <th>Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['trainer_name']); ?></td>
                                <td><?php echo date('M j, Y g:i A', strtotime($booking['scheduled_date'])); ?></td>
                                <td><?php echo $booking['duration_minutes']; ?> min</td>
                                <td><?php echo usd_to_inr_formatted($booking['price']); ?></td>
                                <td><?php echo ucfirst($booking['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h2>Available Trainers</h2>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Hourly Rate</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trainers as $trainer): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($trainer['name']); ?></td>
                            <td><?php echo usd_to_inr_formatted($trainer['hourly_rate']); ?>/hr</td>
                            <td><?php echo $trainer['available'] ? 'Available' : 'Not Available'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php
// Simple page to view online yoga bookings with class types
require_once 'config.php';

try {
    $dbConfig = Config::getDatabase();
    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['name']};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass']);
    
    // Get online yoga bookings
    $stmt = $pdo->prepare("
        SELECT booking_id, name, email, phone, program, class_type, membership_plan, 
               amount, payment_status, created_at 
        FROM bookings 
        WHERE program = 'Online Yoga at Home' 
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Yoga Bookings - Natureland YogChetna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Online Yoga Bookings</h1>
        <p class="text-muted">View all online yoga class bookings with specific class types</p>
        
        <?php if (empty($bookings)): ?>
            <div class="alert alert-info">
                <h4>No Online Yoga Bookings Yet</h4>
                <p>When users book online yoga classes, they will appear here with details like:</p>
                <ul>
                    <li><strong>Class Type:</strong> Online Meditation Class, Online Therapeutic Yoga, etc.</li>
                    <li><strong>Membership Plan:</strong> Weekly (₹1,499), Monthly (₹3,999), etc.</li>
                    <li><strong>User Details:</strong> Name, email, phone</li>
                    <li><strong>Payment Status:</strong> Pending, Success, Failed</li>
                </ul>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Class Type</th>
                            <th>Membership Plan</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?= htmlspecialchars($booking['booking_id']) ?></td>
                            <td><?= htmlspecialchars($booking['name']) ?></td>
                            <td><?= htmlspecialchars($booking['email']) ?></td>
                            <td>
                                <span class="badge bg-primary">
                                    <?= htmlspecialchars($booking['class_type'] ?: 'Online Yoga at Home') ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    <?= htmlspecialchars($booking['membership_plan']) ?>
                                </span>
                            </td>
                            <td>₹<?= number_format($booking['amount'], 2) ?></td>
                            <td>
                                <?php
                                $statusClass = match($booking['payment_status']) {
                                    'success' => 'bg-success',
                                    'pending' => 'bg-warning',
                                    'failed' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?= $statusClass ?>">
                                    <?= ucfirst($booking['payment_status']) ?>
                                </span>
                            </td>
                            <td><?= date('M j, Y g:i A', strtotime($booking['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <div class="mt-4">
            <h3>Example Booking Data Structure</h3>
            <div class="alert alert-light">
                <p>When a user books "Online Meditation Class" with "Monthly Plan", the database will save:</p>
                <pre><code>{
    "booking_id": "YR20241122001",
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+91-9876543210",
    "program": "Online Yoga at Home",
    "class_type": "Online Meditation Class",
    "membership_plan": "monthly",
    "amount": 3999.00,
    "payment_status": "success",
    "created_at": "2024-11-22 15:30:00"
}</code></pre>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="admin.html" class="btn btn-secondary">← Back to Admin</a>
            <a href="online-classes.html" class="btn btn-primary">View Online Classes Page</a>
        </div>
    </div>
</body>
</html>
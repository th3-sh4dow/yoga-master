<?php
// Database Data Print Script
// सभी bookings का data print करने के लिए

// Database configuration
$host = 'mysql.hostinger.in';        // Hostinger MySQL host
$dbname = 'u686650017_yoga_retreat'; // Database name
$username = 'u686650017_natureyog';  // Your DB user
$password = 'Naturelandyogchetna@mydbsql0987'; // Your DB user password

echo "<h1>Yoga Retreat Booking Data</h1>\n";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .success { color: green; }
    .error { color: red; }
    .pending { color: orange; }
    .failed { color: red; }
</style>\n";

try {
    // Database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p class='success'>✓ Database connection successful!</p>\n";
    
    // Get all bookings
    $stmt = $pdo->query("SELECT * FROM bookings ORDER BY created_at DESC");
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($bookings) > 0) {
        echo "<h2>Total Bookings: " . count($bookings) . "</h2>\n";
        
        // Summary by status
        $statusCount = [];
        foreach ($bookings as $booking) {
            $status = $booking['payment_status'];
            $statusCount[$status] = ($statusCount[$status] ?? 0) + 1;
        }
        
        echo "<h3>Status Summary:</h3>\n";
        echo "<ul>\n";
        foreach ($statusCount as $status => $count) {
            echo "<li><strong>" . ucfirst($status) . ":</strong> $count bookings</li>\n";
        }
        echo "</ul>\n";
        
        // Detailed table
        echo "<h3>Detailed Booking Information:</h3>\n";
        echo "<table>\n";
        echo "<tr>
                <th>Booking ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Program</th>
                <th>Accommodation</th>
                <th>Occupancy</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Created Date</th>
                <th>Payment Date</th>
                <th>Transaction ID</th>
              </tr>\n";
        
        foreach ($bookings as $booking) {
            $statusClass = $booking['payment_status'];
            echo "<tr>\n";
            echo "<td>{$booking['booking_id']}</td>\n";
            echo "<td>{$booking['name']}</td>\n";
            echo "<td>{$booking['email']}</td>\n";
            echo "<td>{$booking['phone']}</td>\n";
            echo "<td>{$booking['program']}</td>\n";
            echo "<td>{$booking['accommodation']}</td>\n";
            echo "<td>{$booking['occupancy']}</td>\n";
            echo "<td>₹{$booking['amount']}</td>\n";
            echo "<td class='$statusClass'>" . ucfirst($booking['payment_status']) . "</td>\n";
            echo "<td>" . date('d-M-Y H:i', strtotime($booking['created_at'])) . "</td>\n";
            echo "<td>" . ($booking['payment_date'] ? date('d-M-Y H:i', strtotime($booking['payment_date'])) : '-') . "</td>\n";
            echo "<td>" . ($booking['transaction_id'] ?: '-') . "</td>\n";
            echo "</tr>\n";
        }
        echo "</table>\n";
        
        // Revenue calculation
        $totalRevenue = 0;
        $successfulBookings = 0;
        foreach ($bookings as $booking) {
            if ($booking['payment_status'] === 'success') {
                $totalRevenue += $booking['amount'];
                $successfulBookings++;
            }
        }
        
        echo "<h3>Revenue Summary:</h3>\n";
        echo "<ul>\n";
        echo "<li><strong>Total Revenue:</strong> ₹" . number_format($totalRevenue) . "</li>\n";
        echo "<li><strong>Successful Bookings:</strong> $successfulBookings</li>\n";
        echo "<li><strong>Average Booking Value:</strong> ₹" . ($successfulBookings > 0 ? number_format($totalRevenue / $successfulBookings) : 0) . "</li>\n";
        echo "</ul>\n";
        
    } else {
        echo "<p>No bookings found in the database.</p>\n";
        
        // Check if table exists but is empty
        $stmt = $pdo->query("SHOW TABLES LIKE 'bookings'");
        if ($stmt->rowCount() > 0) {
            echo "<p>The 'bookings' table exists but is empty.</p>\n";
        } else {
            echo "<p class='error'>The 'bookings' table does not exist. Please run setup-database.sql first.</p>\n";
        }
    }
    
    // Show table structure
    echo "<h3>Database Table Structure:</h3>\n";
    try {
        $stmt = $pdo->query("DESCRIBE bookings");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table>\n";
        echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>\n";
        foreach ($columns as $column) {
            echo "<tr>\n";
            echo "<td>{$column['Field']}</td>\n";
            echo "<td>{$column['Type']}</td>\n";
            echo "<td>{$column['Null']}</td>\n";
            echo "<td>{$column['Key']}</td>\n";
            echo "<td>{$column['Default']}</td>\n";
            echo "</tr>\n";
        }
        echo "</table>\n";
    } catch (Exception $e) {
        echo "<p class='error'>Could not fetch table structure: " . $e->getMessage() . "</p>\n";
    }
    
} catch(PDOException $e) {
    echo "<p class='error'>✗ Database connection failed!</p>\n";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>\n";
    
    echo "<h3>Troubleshooting:</h3>\n";
    echo "<ul>\n";
    echo "<li>Make sure remote MySQL is enabled in cPanel</li>\n";
    echo "<li>Add your IP address to allowed hosts</li>\n";
    echo "<li>Check if database credentials are correct</li>\n";
    echo "<li>Verify database name and user permissions</li>\n";
    echo "</ul>\n";
    
    // Try with localhost as fallback
    echo "<hr>\n";
    echo "<h3>Trying with localhost...</h3>\n";
    
    try {
        $pdo_local = new PDO("mysql:host=localhost;dbname=$dbname;charset=utf8", $username, $password);
        $pdo_local->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<p class='success'>✓ Localhost connection successful!</p>\n";
        echo "<p>Use 'localhost' as host when deploying on the server.</p>\n";
    } catch(PDOException $e2) {
        echo "<p class='error'>✗ Localhost connection also failed: " . $e2->getMessage() . "</p>\n";
    }
}

echo "<hr>\n";
echo "<p><small>Generated on: " . date('d-M-Y H:i:s') . "</small></p>\n";
?>
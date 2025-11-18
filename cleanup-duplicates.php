<?php
/**
 * Cleanup Duplicate Records Script
 * Run this once to remove duplicate booking records
 */

// Database configuration
$host = 'mysql.hostinger.in';
$dbname = 'u686650017_yoga_retreat';
$username = 'u686650017_natureyog';
$password = 'Naturelandyogchetna@mydbsql0987';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_PERSISTENT => false
    ]);
    
    echo "Connected to database successfully.\n";
    
    // Find duplicate bookings (same email, phone, and created within 1 hour)
    $find_duplicates = $pdo->prepare("
        SELECT booking_id, name, email, phone, created_at, COUNT(*) as count
        FROM bookings 
        GROUP BY email, phone, DATE(created_at), HOUR(created_at)
        HAVING COUNT(*) > 1
        ORDER BY created_at DESC
    ");
    
    $find_duplicates->execute();
    $duplicates = $find_duplicates->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($duplicates)) {
        echo "No duplicate records found.\n";
        exit;
    }
    
    echo "Found " . count($duplicates) . " sets of duplicate records:\n";
    
    foreach ($duplicates as $duplicate) {
        echo "- {$duplicate['name']} ({$duplicate['email']}) - {$duplicate['count']} records\n";
        
        // Get all records for this duplicate set
        $get_records = $pdo->prepare("
            SELECT id, booking_id, payment_status, transaction_id, created_at
            FROM bookings 
            WHERE email = ? AND phone = ? AND DATE(created_at) = DATE(?) AND HOUR(created_at) = HOUR(?)
            ORDER BY created_at ASC, id ASC
        ");
        
        $get_records->execute([
            $duplicate['email'], 
            $duplicate['phone'], 
            $duplicate['created_at'], 
            $duplicate['created_at']
        ]);
        
        $records = $get_records->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($records) > 1) {
            // Keep the first record (oldest), delete the rest
            $keep_record = array_shift($records);
            echo "  Keeping: {$keep_record['booking_id']} (ID: {$keep_record['id']})\n";
            
            foreach ($records as $record) {
                echo "  Deleting: {$record['booking_id']} (ID: {$record['id']})\n";
                
                // Delete from payment_transactions first (foreign key constraint)
                $delete_txn = $pdo->prepare("DELETE FROM payment_transactions WHERE booking_id = ?");
                $delete_txn->execute([$record['booking_id']]);
                
                // Delete from notifications
                $delete_notif = $pdo->prepare("DELETE FROM notifications WHERE booking_id = ?");
                $delete_notif->execute([$record['booking_id']]);
                
                // Delete the booking record
                $delete_booking = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
                $delete_booking->execute([$record['id']]);
            }
        }
    }
    
    echo "\nCleanup completed successfully!\n";
    
    // Show remaining records count
    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings");
    $count_stmt->execute();
    $total_bookings = $count_stmt->fetchColumn();
    
    echo "Total bookings remaining: $total_bookings\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
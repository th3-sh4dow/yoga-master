<?php
/**
 * Test Payment Flow Script
 * Use this to test the complete payment flow
 */

echo "<h2>Payment Flow Test</h2>";

// Test 1: Create a test booking
echo "<h3>Test 1: Creating Test Booking</h3>";

$test_data = [
    'action' => 'create_booking',
    'name' => 'Test User ' . date('His'),
    'email' => 'test' . date('His') . '@example.com',
    'phone' => '9999999999',
    'program' => 'Test Program',
    'accommodation' => 'Test Accommodation',
    'occupancy' => 'single',
    'amount' => '299.00',
    'check_in_date' => date('Y-m-d', strtotime('+7 days')),
    'check_out_date' => date('Y-m-d', strtotime('+8 days'))
];

// Simulate booking creation
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://' . $_SERVER['HTTP_HOST'] . '/booking-system.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($test_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $http_code<br>";
echo "Response: $response<br>";

$booking_result = json_decode($response, true);

if ($booking_result && $booking_result['success']) {
    $booking_id = $booking_result['booking_id'];
    echo "<strong>✓ Booking created successfully: $booking_id</strong><br>";
    
    // Test 2: Simulate payment webhook
    echo "<h3>Test 2: Simulating Payment Webhook</h3>";
    
    $webhook_data = [
        'type' => 'PAYMENT_FORM_ORDER_WEBHOOK',
        'data' => [
            'order' => [
                'order_id' => $booking_id,
                'order_status' => 'PAID',
                'order_amount' => $test_data['amount'],
                'transaction_id' => 'TXN' . time(),
                'customer_details' => [
                    'customer_name' => $test_data['name'],
                    'customer_email' => $test_data['email'],
                    'customer_phone' => $test_data['phone']
                ]
            ]
        ]
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://' . $_SERVER['HTTP_HOST'] . '/payment-webhook.php');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhook_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $webhook_response = curl_exec($ch);
    $webhook_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Webhook HTTP Code: $webhook_http_code<br>";
    echo "Webhook Response: $webhook_response<br>";
    
    $webhook_result = json_decode($webhook_response, true);
    
    if ($webhook_result && $webhook_result['status'] === 'success') {
        echo "<strong>✓ Webhook processed successfully</strong><br>";
        
        // Test 3: Check database status
        echo "<h3>Test 3: Checking Database Status</h3>";
        
        try {
            $pdo = new PDO("mysql:host=mysql.hostinger.in;dbname=u686650017_yoga_retreat", "u686650017_natureyog", "Naturelandyogchetna@mydbsql0987");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $pdo->prepare("SELECT * FROM bookings WHERE booking_id = ?");
            $stmt->execute([$booking_id]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($booking) {
                echo "<strong>✓ Booking found in database</strong><br>";
                echo "Payment Status: " . $booking['payment_status'] . "<br>";
                echo "Transaction ID: " . $booking['transaction_id'] . "<br>";
                echo "Amount: ₹" . $booking['amount'] . "<br>";
                
                if ($booking['payment_status'] === 'success') {
                    echo "<strong style='color: green;'>✓ Payment status updated correctly!</strong><br>";
                } else {
                    echo "<strong style='color: red;'>✗ Payment status not updated</strong><br>";
                }
            } else {
                echo "<strong style='color: red;'>✗ Booking not found in database</strong><br>";
            }
            
            // Check for duplicates
            $dup_stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE email = ? AND phone = ?");
            $dup_stmt->execute([$test_data['email'], $test_data['phone']]);
            $dup_count = $dup_stmt->fetchColumn();
            
            if ($dup_count > 1) {
                echo "<strong style='color: orange;'>⚠ Warning: $dup_count duplicate records found</strong><br>";
            } else {
                echo "<strong style='color: green;'>✓ No duplicate records</strong><br>";
            }
            
        } catch (Exception $e) {
            echo "<strong style='color: red;'>✗ Database error: " . $e->getMessage() . "</strong><br>";
        }
        
        // Test 4: Test success page URL
        echo "<h3>Test 4: Testing Success Page URL</h3>";
        
        $success_url = "payment-success.php?booking_id=" . urlencode($booking_id) . 
                      "&amount=" . urlencode($test_data['amount']) . 
                      "&transaction_id=" . urlencode($webhook_data['data']['order']['transaction_id']) . 
                      "&status=success";
        
        echo "Success URL: <a href='$success_url' target='_blank'>$success_url</a><br>";
        echo "<strong>✓ Test completed successfully!</strong><br>";
        
    } else {
        echo "<strong style='color: red;'>✗ Webhook failed</strong><br>";
    }
    
} else {
    echo "<strong style='color: red;'>✗ Booking creation failed</strong><br>";
}

echo "<hr>";
echo "<h3>Summary</h3>";
echo "This test creates a booking, simulates a payment webhook, and checks the database status.<br>";
echo "If all tests pass, your payment flow is working correctly.<br>";
echo "<br>";
echo "<a href='cleanup-duplicates.php'>Run Cleanup Script</a> | ";
echo "<a href='admin-bookings.html'>View Admin Panel</a>";
?>
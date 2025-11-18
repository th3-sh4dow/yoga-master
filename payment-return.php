<?php
// Simple payment return handler
// This page receives users after payment completion from Cashfree

// Enable error logging for debugging
error_reporting(E_ALL);
ini_set('log_errors', 1);

// Log all received parameters for debugging
error_log("Payment return GET params: " . print_r($_GET, true));
error_log("Payment return POST params: " . print_r($_POST, true));

// Get all possible parameters from Cashfree (they might come via GET or POST)
$all_params = array_merge($_GET, $_POST);

// Extract booking details with multiple fallback options
$booking_id = $all_params['order_id'] ?? $all_params['booking_id'] ?? $all_params['orderId'] ?? '';
$amount = $all_params['order_amount'] ?? $all_params['amount'] ?? $all_params['orderAmount'] ?? '';
$payment_status = $all_params['payment_status'] ?? $all_params['status'] ?? $all_params['paymentStatus'] ?? 'success';
$transaction_id = $all_params['cf_payment_id'] ?? $all_params['transaction_id'] ?? $all_params['payment_id'] ?? $all_params['paymentId'] ?? '';

// Additional Cashfree specific parameters
$order_token = $all_params['order_token'] ?? '';
$signature = $all_params['signature'] ?? '';

// Log extracted values
error_log("Extracted values - Booking ID: $booking_id, Amount: $amount, Status: $payment_status, Transaction ID: $transaction_id");

// If we don't have booking_id, try to extract from other sources
if (empty($booking_id)) {
    // Check if there's a custom parameter or session
    session_start();
    $booking_id = $_SESSION['current_booking_id'] ?? '';
    error_log("Fallback booking ID from session: $booking_id");
}

// Update payment status in database if we have booking details
if (!empty($booking_id)) {
    try {
        $pdo = new PDO("mysql:host=mysql.hostinger.in;dbname=u686650017_yoga_retreat", "u686650017_natureyog", "Naturelandyogchetna@mydbsql0987");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get booking details from database
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE booking_id = ?");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($booking) {
            // Use database amount if not provided in return
            if (empty($amount)) {
                $amount = $booking['amount'];
            }
            
            // Update payment status
            $update_stmt = $pdo->prepare("UPDATE bookings SET payment_status = ?, transaction_id = ?, payment_date = NOW() WHERE booking_id = ?");
            $final_status = (strtolower($payment_status) === 'success' || strtolower($payment_status) === 'paid') ? 'success' : 'failed';
            $update_stmt->execute([$final_status, $transaction_id, $booking_id]);
            
            error_log("Updated booking $booking_id with status: $final_status");
        }
    } catch (Exception $e) {
        error_log("Database error in payment return: " . $e->getMessage());
    }
}

// Redirect based on payment status
if (strtolower($payment_status) === 'success' || strtolower($payment_status) === 'paid') {
    // Success - redirect to success page with details
    $redirect_url = "payment-success.php?booking_id=" . urlencode($booking_id) . 
                   "&amount=" . urlencode($amount) . 
                   "&transaction_id=" . urlencode($transaction_id) . 
                   "&status=success";
} else {
    // Failed - redirect to failure page
    $redirect_url = "payment-failed.html?booking_id=" . urlencode($booking_id) . 
                   "&amount=" . urlencode($amount) . 
                   "&status=" . urlencode($payment_status);
}

error_log("Redirecting to: $redirect_url");

// Redirect
header("Location: " . $redirect_url);
exit();
?>
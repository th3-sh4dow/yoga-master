<?php
/**
 * Simple Cashfree Webhook Handler
 * This version works without complex configuration system
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

header('Content-Type: application/json; charset=utf-8');

// Simple logging function
function simple_log($message, $data = null) {
    $log_entry = date('Y-m-d H:i:s') . " - " . $message;
    if ($data) {
        $log_entry .= " - " . json_encode($data);
    }
    $log_entry .= PHP_EOL;
    file_put_contents('webhook_debug.log', $log_entry, FILE_APPEND | LOCK_EX);
}

// Handle test requests
if (isset($_GET['test']) && $_GET['test'] === '1') {
    simple_log("Test request received");
    echo json_encode([
        'status' => 'ok',
        'message' => 'Webhook endpoint is accessible',
        'timestamp' => date('c'),
        'method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown'
    ]);
    exit;
}

simple_log("Webhook request started");

// Read raw payload
$rawPayload = file_get_contents('php://input');
simple_log("Raw payload received", ['length' => strlen($rawPayload), 'preview' => substr($rawPayload, 0, 200)]);

// Get headers
$headers = function_exists('getallheaders') ? getallheaders() : [];
if (empty($headers)) {
    $headers = [];
    foreach ($_SERVER as $key => $value) {
        if (strpos($key, 'HTTP_') === 0) {
            $header = str_replace('_', '-', substr($key, 5));
            $headers[$header] = $value;
        }
    }
}

simple_log("Headers received", $headers);

// Basic database configuration (hardcoded for testing)
$db_config = [
    'host' => 'mysql.hostinger.in',
    'name' => 'u686650017_yoga_retreat',
    'user' => 'u686650017_natureyog',
    'pass' => 'Naturelandyogchetna@mydbsql0987'
];

// Skip signature verification for testing
simple_log("Skipping signature verification for testing");

// Decode payload
$webhook_data = json_decode($rawPayload, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    simple_log("JSON decode error", ['error' => json_last_error_msg()]);
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON payload']);
    exit;
}

simple_log("Webhook data decoded", $webhook_data);

// Extract order information based on Cashfree Payment Form format
$webhook_type = $webhook_data['type'] ?? '';
$order_id = null;
$payment_status = '';
$amount = 0;
$transaction_id = '';

if ($webhook_type === 'PAYMENT_FORM_ORDER_WEBHOOK' && isset($webhook_data['data']['order'])) {
    $order_data = $webhook_data['data']['order'];
    $order_id = $order_data['order_id'] ?? null;
    $payment_status = strtolower($order_data['order_status'] ?? '');
    $amount = $order_data['order_amount'] ?? 0;
    $transaction_id = $order_data['transaction_id'] ?? '';
    
    simple_log("Payment Form webhook detected", [
        'order_id' => $order_id,
        'status' => $payment_status,
        'amount' => $amount,
        'transaction_id' => $transaction_id
    ]);
} else {
    // Fallback for other formats
    $order_id = $webhook_data['order_id'] ?? null;
    $payment_status = strtolower($webhook_data['payment_status'] ?? $webhook_data['status'] ?? '');
    $amount = $webhook_data['order_amount'] ?? $webhook_data['amount'] ?? 0;
    $transaction_id = $webhook_data['transaction_id'] ?? '';
    
    simple_log("Standard webhook format", [
        'order_id' => $order_id,
        'status' => $payment_status,
        'amount' => $amount
    ]);
}

if (!$order_id) {
    simple_log("Missing order_id");
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing order_id']);
    exit;
}

// Map payment status
$status_mapping = [
    'paid' => 'success',
    'success' => 'success',
    'completed' => 'success',
    'failed' => 'failed',
    'cancelled' => 'failed',
    'pending' => 'pending',
    'created' => 'pending',
];

$mapped_status = $status_mapping[$payment_status] ?? 'pending';
simple_log("Status mapped", ['original' => $payment_status, 'mapped' => $mapped_status]);

// Database operations
try {
    $dsn = "mysql:host={$db_config['host']};dbname={$db_config['name']};charset=utf8mb4";
    $pdo = new PDO($dsn, $db_config['user'], $db_config['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_PERSISTENT => false,
    ]);
    
    simple_log("Database connected successfully");
    
    $pdo->beginTransaction();
    
    // First, try to find booking using the order mapping table
    $check_mapping = $pdo->prepare("
        SELECT om.booking_id, b.booking_id as booking_exists 
        FROM order_mappings om 
        LEFT JOIN bookings b ON om.booking_id = b.booking_id 
        WHERE om.cashfree_order_id = :order_id
    ");
    $check_mapping->execute([':order_id' => $order_id]);
    $mapping_result = $check_mapping->fetch(PDO::FETCH_ASSOC);
    
    $booking_exists = false;
    $actual_booking_id = null;
    
    if ($mapping_result && $mapping_result['booking_exists']) {
        $booking_exists = true;
        $actual_booking_id = $mapping_result['booking_id'];
        simple_log("Found booking via order mapping", ['cashfree_order_id' => $order_id, 'booking_id' => $actual_booking_id]);
    } else {
        // Try direct match (in case the order_id is actually our booking_id)
        $check_direct = $pdo->prepare("SELECT booking_id FROM bookings WHERE booking_id = :booking_id");
        $check_direct->execute([':booking_id' => $order_id]);
        $direct_result = $check_direct->fetch(PDO::FETCH_ASSOC);
        
        if ($direct_result) {
            $booking_exists = true;
            $actual_booking_id = $direct_result['booking_id'];
            simple_log("Found booking by direct match", ['booking_id' => $actual_booking_id]);
        } else {
            // Try to find by transaction_id (in case it was already processed)
            if (!empty($transaction_id)) {
                $check_txn = $pdo->prepare("SELECT booking_id FROM bookings WHERE transaction_id = :txn");
                $check_txn->execute([':txn' => $transaction_id]);
                $txn_result = $check_txn->fetch(PDO::FETCH_ASSOC);
                if ($txn_result) {
                    $booking_exists = true;
                    $actual_booking_id = $txn_result['booking_id'];
                    simple_log("Found booking by transaction_id", ['txn_id' => $transaction_id, 'booking_id' => $actual_booking_id]);
                }
            }
            
            // If still not found, try to match by customer email and recent bookings
            if (!$booking_exists && isset($webhook_data['data']['order']['customer_details'])) {
                $customer_details = $webhook_data['data']['order']['customer_details'];
                $customer_email = $customer_details['customer_email'] ?? '';
                $customer_name = $customer_details['customer_name'] ?? '';
                
                if (!empty($customer_email)) {
                    // Look for recent pending bookings with this email (within last 24 hours)
                    $check_email = $pdo->prepare("
                        SELECT booking_id, name, amount
                        FROM bookings 
                        WHERE email = :email 
                        AND payment_status = 'pending' 
                        AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                        ORDER BY created_at DESC 
                        LIMIT 1
                    ");
                    $check_email->execute([':email' => $customer_email]);
                    $email_result = $check_email->fetch(PDO::FETCH_ASSOC);
                    
                    if ($email_result) {
                        $booking_exists = true;
                        $actual_booking_id = $email_result['booking_id'];
                        
                        // Create the mapping for future reference
                        try {
                            $create_mapping = $pdo->prepare("
                                INSERT INTO order_mappings (booking_id, cashfree_order_id, customer_email, customer_name, amount)
                                VALUES (:booking_id, :cashfree_order_id, :email, :name, :amount)
                                ON DUPLICATE KEY UPDATE customer_email = :email, customer_name = :name
                            ");
                            $create_mapping->execute([
                                ':booking_id' => $actual_booking_id,
                                ':cashfree_order_id' => $order_id,
                                ':email' => $customer_email,
                                ':name' => $customer_name,
                                ':amount' => $amount
                            ]);
                            simple_log("Created order mapping", ['booking_id' => $actual_booking_id, 'cashfree_order_id' => $order_id]);
                        } catch (PDOException $e) {
                            simple_log("Failed to create order mapping", ['error' => $e->getMessage()]);
                        }
                        
                        simple_log("Found booking by customer email", ['email' => $customer_email, 'booking_id' => $actual_booking_id]);
                    }
                }
            }
        }
    }
    
    simple_log("Booking check", ['order_id' => $order_id, 'exists' => $booking_exists, 'actual_booking_id' => $actual_booking_id]);
    
    if ($booking_exists) {
        // Update existing booking
        $update = $pdo->prepare("
            UPDATE bookings 
            SET payment_status = :status, transaction_id = :txn, payment_date = NOW()
            WHERE booking_id = :booking_id
        ");
        $update->execute([
            ':status' => $mapped_status,
            ':txn' => $transaction_id,
            ':booking_id' => $actual_booking_id
        ]);
        
        simple_log("Booking updated", ['booking_id' => $actual_booking_id, 'status' => $mapped_status]);
    } else {
        // Log that booking doesn't exist
        simple_log("Booking not found for webhook", ['order_id' => $order_id, 'transaction_id' => $transaction_id]);
        
        // For testing purposes, let's create a placeholder booking to avoid 404 errors
        // This should be removed in production once the ID mapping is properly configured
        if (isset($webhook_data['data']['order']['customer_details'])) {
            $customer = $webhook_data['data']['order']['customer_details'];
            $customer_name = $customer['customer_name'] ?? 'Unknown';
            $customer_email = $customer['customer_email'] ?? 'unknown@example.com';
            $customer_phone = $customer['customer_phone'] ?? '0000000000';
            
            // Generate a booking ID for this webhook
            $webhook_booking_id = 'WH' . date('Ymd') . substr($order_id, -4);
            
            $create_booking = $pdo->prepare("
                INSERT INTO bookings 
                (booking_id, name, email, phone, program, accommodation, occupancy, amount, payment_status, transaction_id, created_at)
                VALUES (:booking_id, :name, :email, :phone, :program, :accommodation, :occupancy, :amount, :status, :txn, NOW())
            ");
            
            $create_booking->execute([
                ':booking_id' => $webhook_booking_id,
                ':name' => $customer_name,
                ':email' => $customer_email,
                ':phone' => $customer_phone,
                ':program' => 'Webhook Payment',
                ':accommodation' => 'Unknown',
                ':occupancy' => 'Unknown',
                ':amount' => $amount,
                ':status' => $mapped_status,
                ':txn' => $transaction_id
            ]);
            
            $actual_booking_id = $webhook_booking_id;
            simple_log("Created placeholder booking from webhook", ['booking_id' => $webhook_booking_id]);
        } else {
            // Return success but log the issue
            simple_log("No booking found and insufficient data to create placeholder");
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'message' => 'Webhook received but no matching booking found',
                'order_id' => $order_id
            ]);
            exit;
        }
    }
    
    // Insert transaction log only if it doesn't exist
    $check_txn_log = $pdo->prepare("SELECT COUNT(*) as count FROM payment_transactions WHERE transaction_id = :txn");
    $check_txn_log->execute([':txn' => $transaction_id]);
    $txn_exists = $check_txn_log->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    
    if (!$txn_exists && !empty($transaction_id)) {
        $insert_txn = $pdo->prepare("
            INSERT INTO payment_transactions
            (booking_id, transaction_id, payment_method, amount, status, gateway_response, created_at)
            VALUES (:booking_id, :txn, :method, :amount, :status, :gateway_response, NOW())
        ");
        
        $insert_txn->execute([
            ':booking_id' => $actual_booking_id,
            ':txn' => $transaction_id,
            ':method' => 'online',
            ':amount' => $amount,
            ':status' => $mapped_status,
            ':gateway_response' => json_encode($webhook_data)
        ]);
        
        simple_log("Transaction logged", ['booking_id' => $actual_booking_id]);
    } else {
        simple_log("Transaction already exists or empty transaction_id", ['txn_id' => $transaction_id]);
    }
    
    $pdo->commit();
    
    // Success response
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Webhook processed successfully',
        'order_id' => $order_id,
        'booking_id' => $actual_booking_id,
        'payment_status' => $mapped_status,
        'amount' => $amount
    ]);
    
    simple_log("Webhook processed successfully", [
        'order_id' => $order_id,
        'booking_id' => $actual_booking_id,
        'status' => $mapped_status,
        'amount' => $amount
    ]);
    
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    simple_log("Database error", ['error' => $e->getMessage()]);
    
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error',
        'error' => $e->getMessage()
    ]);
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    simple_log("General error", ['error' => $e->getMessage()]);
    
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Processing error',
        'error' => $e->getMessage()
    ]);
}

simple_log("Webhook request completed");
?>
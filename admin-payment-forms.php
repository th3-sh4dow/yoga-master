<?php
/**
 * Admin interface for managing payment form mappings
 */

require_once 'payment-forms-config.php';

// Handle form updates
if ($_POST && isset($_POST['action'])) {
    if ($_POST['action'] === 'test_combination') {
        $test_program = $_POST['program'];
        $test_accommodation = $_POST['accommodation'];
        $test_occupancy = $_POST['occupancy'];
        
        $payment_url = PaymentFormsConfig::getPaymentFormUrl($test_program, $test_accommodation, $test_occupancy);
        $debug_info = PaymentFormsConfig::getDebugInfo($test_program, $test_accommodation, $test_occupancy);
        
        $test_result = [
            'program' => $test_program,
            'accommodation' => $test_accommodation,
            'occupancy' => $test_occupancy,
            'payment_url' => $payment_url,
            'debug_info' => $debug_info
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Forms Admin - Natureland YogChetna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .valid-mapping { background-color: #d4edda; }
        .fallback-mapping { background-color: #fff3cd; }
        .invalid-mapping { background-color: #f8d7da; }
        .form-url { font-family: monospace; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1>Payment Forms Management</h1>
        <p class="text-muted">Manage and test Cashfree payment form mappings</p>

        <!-- Test Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Test Payment Form Selection</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="test_combination">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="program" class="form-label">Program</label>
                            <select name="program" id="program" class="form-select" required>
                                <?php foreach (PaymentFormsConfig::getAvailablePrograms() as $program): ?>
                                    <option value="<?= htmlspecialchars($program) ?>" 
                                        <?= (isset($test_result) && $test_result['program'] === $program) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($program) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="accommodation" class="form-label">Accommodation</label>
                            <select name="accommodation" id="accommodation" class="form-select" required>
                                <?php foreach (PaymentFormsConfig::getAvailableAccommodations() as $accommodation): ?>
                                    <option value="<?= htmlspecialchars($accommodation) ?>"
                                        <?= (isset($test_result) && $test_result['accommodation'] === $accommodation) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars(str_replace('_', ' ', ucwords($accommodation, '_'))) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="occupancy" class="form-label">Occupancy</label>
                            <select name="occupancy" id="occupancy" class="form-select" required>
                                <?php foreach (PaymentFormsConfig::getAvailableOccupancies() as $occupancy): ?>
                                    <option value="<?= htmlspecialchars($occupancy) ?>"
                                        <?= (isset($test_result) && $test_result['occupancy'] === $occupancy) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars(ucfirst($occupancy)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Test Combination</button>
                </form>

                <?php if (isset($test_result)): ?>
                    <div class="mt-4">
                        <h4>Test Result</h4>
                        <div class="alert alert-info">
                            <strong>Selected Payment Form:</strong><br>
                            <a href="<?= htmlspecialchars($test_result['payment_url']) ?>" target="_blank" class="form-url">
                                <?= htmlspecialchars($test_result['payment_url']) ?>
                            </a>
                        </div>
                        
                        <h5>Debug Information</h5>
                        <pre class="bg-light p-3"><?= json_encode($test_result['debug_info'], JSON_PRETTY_PRINT) ?></pre>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- All Mappings Table -->
        <div class="card">
            <div class="card-header">
                <h3>All Payment Form Mappings</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Program</th>
                                <th>Accommodation</th>
                                <th>Occupancy</th>
                                <th>Payment Form URL</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $programs = PaymentFormsConfig::getAvailablePrograms();
                            $accommodations = PaymentFormsConfig::getAvailableAccommodations();
                            $occupancies = PaymentFormsConfig::getAvailableOccupancies();
                            
                            foreach ($programs as $program):
                                foreach ($accommodations as $accommodation):
                                    foreach ($occupancies as $occupancy):
                                        $url = PaymentFormsConfig::getPaymentFormUrl($program, $accommodation, $occupancy);
                                        $isValid = PaymentFormsConfig::isValidCombination($program, $accommodation, $occupancy);
                                        $statusClass = $isValid ? 'valid-mapping' : 'fallback-mapping';
                                        $status = $isValid ? 'Direct Match' : 'Fallback';
                            ?>
                                <tr class="<?= $statusClass ?>">
                                    <td><?= htmlspecialchars($program) ?></td>
                                    <td><?= htmlspecialchars(str_replace('_', ' ', ucwords($accommodation, '_'))) ?></td>
                                    <td><?= htmlspecialchars(ucfirst($occupancy)) ?></td>
                                    <td class="form-url">
                                        <a href="<?= htmlspecialchars($url) ?>" target="_blank">
                                            <?= htmlspecialchars(basename(parse_url($url, PHP_URL_PATH))) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge <?= $isValid ? 'bg-success' : 'bg-warning' ?>">
                                            <?= $status ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= htmlspecialchars($url) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            Test Form
                                        </a>
                                    </td>
                                </tr>
                            <?php
                                    endforeach;
                                endforeach;
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Your Cashfree Forms -->
        <div class="card mt-4">
            <div class="card-header">
                <h3>Your Cashfree Payment Forms</h3>
            </div>
            <div class="card-body">
                <p class="text-muted">These are the payment forms you've created in Cashfree:</p>
                <div class="row">
                    <?php
                    $your_forms = [
                        '3-days-Premium-Cottage-Single' => '₹18,000.00',
                        '3-days-Garden-Cottage-Double' => '₹14,000.00',

                        '1-week-Premium-Cottage-Double' => '₹34,000.00',
                        '1-week-Premium-Cottage-Single' => '₹39,000.00',
                        '1-week-Garden-Cottage-Double' => '₹30,000.00',
                        '1-Week-Garden-Cottage-Single' => '₹35,000.00',
                        '3days-garden-cottage' => '₹10,000.00',
                        '3days-Premium-Cottage' => '₹16,000.00',
                        'online-yoga-weekly-1499' => '₹1,499.00',
                        'online-yoga-monthly-3999' => '₹3,999.00',
                        'online-yoga-quarterly-9999' => '₹9,999.00',
                        'online-yoga-flexible-500' => '₹500.00'
                    ];
                    
                    foreach ($your_forms as $form_name => $amount):
                    ?>
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title"><?= htmlspecialchars($form_name) ?></h6>
                                    <p class="card-text">Amount: <strong><?= htmlspecialchars($amount) ?></strong></p>
                                    <a href="https://payments.cashfree.com/forms/<?= htmlspecialchars($form_name) ?>" 
                                       target="_blank" class="btn btn-sm btn-primary">
                                        Open Form
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
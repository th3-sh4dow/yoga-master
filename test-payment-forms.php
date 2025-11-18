<?php
/**
 * Test script to verify payment form mappings
 * Run this to see which payment forms will be selected for different combinations
 */

require_once 'payment-forms-config.php';

echo "<h2>Payment Forms Test</h2>";
echo "<style>
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .valid { background-color: #d4edda; }
    .invalid { background-color: #f8d7da; }
</style>";

// Test all combinations
$programs = [
    'Weekend Wellness Yoga Retreat',
    '3-Day Wellness & Retreat',
    '5-Day Wellness & Retreat',
    '7 Days Yoga & Wellness Detox Retreat'
];

$accommodations = ['garden_cottage', 'premium_cottage'];
$occupancies = ['single', 'double'];

echo "<table>";
echo "<tr><th>Program</th><th>Accommodation</th><th>Occupancy</th><th>Payment Form URL</th><th>Status</th></tr>";

foreach ($programs as $program) {
    foreach ($accommodations as $accommodation) {
        foreach ($occupancies as $occupancy) {
            $url = PaymentFormsConfig::getPaymentFormUrl($program, $accommodation, $occupancy);
            $isValid = PaymentFormsConfig::isValidCombination($program, $accommodation, $occupancy);
            $statusClass = $isValid ? 'valid' : 'invalid';
            $status = $isValid ? 'Valid' : 'Fallback';
            
            echo "<tr class='{$statusClass}'>";
            echo "<td>{$program}</td>";
            echo "<td>{$accommodation}</td>";
            echo "<td>{$occupancy}</td>";
            echo "<td><a href='{$url}' target='_blank'>" . basename($url) . "</a></td>";
            echo "<td>{$status}</td>";
            echo "</tr>";
        }
    }
}

echo "</table>";

echo "<h3>Available Payment Forms</h3>";
echo "<ul>";
foreach (PaymentFormsConfig::getPaymentForms() as $key => $url) {
    echo "<li><strong>{$key}</strong>: <a href='{$url}' target='_blank'>{$url}</a></li>";
}
echo "</ul>";

echo "<h3>Test Specific Combination</h3>";
if (isset($_GET['program']) && isset($_GET['accommodation']) && isset($_GET['occupancy'])) {
    $test_program = $_GET['program'];
    $test_accommodation = $_GET['accommodation'];
    $test_occupancy = $_GET['occupancy'];
    
    $debug_info = PaymentFormsConfig::getDebugInfo($test_program, $test_accommodation, $test_occupancy);
    
    echo "<h4>Debug Info for: {$test_program} + {$test_accommodation} + {$test_occupancy}</h4>";
    echo "<pre>" . json_encode($debug_info, JSON_PRETTY_PRINT) . "</pre>";
}

echo "<form method='GET'>";
echo "<p>Test a specific combination:</p>";
echo "<select name='program'>";
foreach ($programs as $program) {
    $selected = (isset($_GET['program']) && $_GET['program'] === $program) ? 'selected' : '';
    echo "<option value='{$program}' {$selected}>{$program}</option>";
}
echo "</select>";

echo "<select name='accommodation'>";
foreach ($accommodations as $accommodation) {
    $selected = (isset($_GET['accommodation']) && $_GET['accommodation'] === $accommodation) ? 'selected' : '';
    echo "<option value='{$accommodation}' {$selected}>{$accommodation}</option>";
}
echo "</select>";

echo "<select name='occupancy'>";
foreach ($occupancies as $occupancy) {
    $selected = (isset($_GET['occupancy']) && $_GET['occupancy'] === $occupancy) ? 'selected' : '';
    echo "<option value='{$occupancy}' {$selected}>{$occupancy}</option>";
}
echo "</select>";

echo "<button type='submit'>Test</button>";
echo "</form>";
?>
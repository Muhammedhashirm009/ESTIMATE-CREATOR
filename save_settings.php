<?php
session_start();
header('Content-Type: application/json');

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt');

// Function to log errors with timestamp
function logError($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] SAVE_SETTINGS: $message" . PHP_EOL;
    file_put_contents('error_log.txt', $logMessage, FILE_APPEND | LOCK_EX);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    logError("Save settings attempt started");
    logError("POST data: " . json_encode($_POST));
    $response = ['success' => true, 'message' => 'Settings saved successfully'];
    
    // Logo upload is now handled separately by upload_logo.php
    // This endpoint only saves other settings
    
    // Store other settings in session if logo upload succeeded or no logo was uploaded
    if ($response['success']) {
        $_SESSION['shopName'] = $_POST['shopName'] ?? 'COMPUTER SOFT';
        $_SESSION['shopAddress'] = $_POST['shopAddress'] ?? '1st Floor, Global Village, Bank Road, Kannur-1';
        $_SESSION['shopPhone'] = $_POST['shopPhone'] ?? '0497 2767015 / 9142 927 321';
        $_SESSION['shopEmail'] = $_POST['shopEmail'] ?? 'computersoftknr@gmail.com';
        $_SESSION['shopWebsite'] = $_POST['shopWebsite'] ?? 'www.computersoft.in';
        $_SESSION['shop_gst'] = $_POST['shopGST'] ?? '';
        $_SESSION['bank_name'] = $_POST['bankName'] ?? '';
        $_SESSION['account_number'] = $_POST['accountNumber'] ?? '';
        $_SESSION['ifsc_code'] = $_POST['ifscCode'] ?? '';
        $_SESSION['account_holder'] = $_POST['accountHolder'] ?? '';
        logError("Settings saved to session successfully");
    }
    
    logError("Settings save response: " . json_encode($response));
    echo json_encode($response);
} else {
    logError("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
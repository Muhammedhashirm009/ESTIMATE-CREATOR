<?php
// Simple error log viewer for debugging
$logFile = 'error_log.txt';

if (isset($_GET['clear']) && $_GET['clear'] === '1') {
    file_put_contents($logFile, '');
    header('Location: view_error_log.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Error Log Viewer</title>
    <style>
        body { font-family: monospace; margin: 20px; }
        .log-content { background: #f5f5f5; padding: 15px; border: 1px solid #ddd; white-space: pre-wrap; max-height: 500px; overflow-y: scroll; }
        .header { margin-bottom: 15px; }
        .btn { padding: 8px 15px; margin-right: 10px; text-decoration: none; background: #007cba; color: white; border-radius: 4px; }
        .btn:hover { background: #005a87; }
        .clear-btn { background: #dc3545; }
        .clear-btn:hover { background: #c82333; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Error Log Viewer</h2>
        <a href="view_error_log.php" class="btn">Refresh</a>
        <a href="view_error_log.php?clear=1" class="btn clear-btn" onclick="return confirm('Are you sure you want to clear the log?')">Clear Log</a>
        <a href="index.php" class="btn">Back to App</a>
    </div>
    
    <div class="log-content">
<?php
if (file_exists($logFile)) {
    $content = file_get_contents($logFile);
    if (empty($content)) {
        echo "Log file is empty.";
    } else {
        echo htmlspecialchars($content);
    }
} else {
    echo "Log file does not exist yet.";
}
?>
    </div>
</body>
</html>
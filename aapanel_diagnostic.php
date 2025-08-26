<?php
// AAPanel Diagnostic Script
echo "<h2>AAPanel Upload Diagnostic</h2>";

echo "<h3>PHP Configuration:</h3>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "file_uploads: " . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "<br>";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "<br>";
echo "memory_limit: " . ini_get('memory_limit') . "<br>";
echo "max_execution_time: " . ini_get('max_execution_time') . "<br>";

echo "<h3>Directory Status:</h3>";
$uploadDir = 'uploads/';
echo "Upload directory exists: " . (file_exists($uploadDir) ? 'Yes' : 'No') . "<br>";
echo "Upload directory writable: " . (is_writable($uploadDir) ? 'Yes' : 'No') . "<br>";
echo "Upload directory permissions: " . (file_exists($uploadDir) ? substr(sprintf('%o', fileperms($uploadDir)), -4) : 'N/A') . "<br>";

echo "<h3>Server Information:</h3>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";

echo "<h3>Test File Upload:</h3>";
if ($_POST) {
    if (isset($_FILES['test_file'])) {
        echo "<strong>File Upload Test Results:</strong><br>";
        echo "File error code: " . $_FILES['test_file']['error'] . "<br>";
        echo "File size: " . $_FILES['test_file']['size'] . " bytes<br>";
        echo "File type: " . $_FILES['test_file']['type'] . "<br>";
        echo "Temp name: " . $_FILES['test_file']['tmp_name'] . "<br>";
        echo "Original name: " . $_FILES['test_file']['name'] . "<br>";
        
        if ($_FILES['test_file']['error'] === UPLOAD_ERR_OK) {
            $testPath = $uploadDir . 'test_' . time() . '.txt';
            if (move_uploaded_file($_FILES['test_file']['tmp_name'], $testPath)) {
                echo "<span style='color: green;'>✓ File upload successful!</span><br>";
                echo "Saved to: " . $testPath . "<br>";
                // Clean up test file
                unlink($testPath);
            } else {
                echo "<span style='color: red;'>✗ Failed to move uploaded file</span><br>";
            }
        } else {
            echo "<span style='color: red;'>✗ Upload error occurred</span><br>";
        }
    }
} else {
    echo '<form method="post" enctype="multipart/form-data">';
    echo '<input type="file" name="test_file" accept=".txt,.jpg,.png" required><br><br>';
    echo '<input type="submit" value="Test Upload" style="padding: 10px; background: #007cba; color: white; border: none; cursor: pointer;">';
    echo '</form>';
}

echo "<h3>Error Log Check:</h3>";
if (file_exists('error_log.txt')) {
    echo "<a href='view_error_log.php' target='_blank'>View Error Log</a><br>";
} else {
    echo "No error log file found yet.<br>";
}

echo "<h3>JavaScript Test:</h3>";
echo '<script>
console.log("AAPanel Diagnostic: JavaScript is working");
document.addEventListener("DOMContentLoaded", function() {
    console.log("AAPanel Diagnostic: DOM loaded successfully");
});
</script>';
echo "Check browser console for JavaScript test messages.";
?>
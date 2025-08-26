<?php
session_start();
header('Content-Type: application/json');

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt');

// Function to log errors with timestamp
function logError($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] UPLOAD_LOGO: $message" . PHP_EOL;
    file_put_contents('error_log.txt', $logMessage, FILE_APPEND | LOCK_EX);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    logError("Upload attempt started");
    $response = ['success' => false, 'message' => 'No file uploaded'];
    
    // Log all POST data and FILES data for debugging
    logError("POST data: " . json_encode($_POST));
    logError("FILES data: " . json_encode($_FILES));
    
    if (isset($_FILES['shopLogo'])) {
        logError("File upload detected, processing...");
        $uploadError = $_FILES['shopLogo']['error'];
        
        // Check for upload errors first
        if ($uploadError !== UPLOAD_ERR_OK) {
            logError("Upload error detected: " . $uploadError);
            switch ($uploadError) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $response = ['success' => false, 'message' => 'File too large. Maximum size is 2MB.'];
                    logError("File too large error");
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $response = ['success' => false, 'message' => 'File upload was interrupted. Please try again.'];
                    logError("Partial upload error");
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $response = ['success' => false, 'message' => 'No file selected. Please choose a file first.'];
                    logError("No file selected error");
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $response = ['success' => false, 'message' => 'Server error: No temporary directory.'];
                    logError("No temp directory error");
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $response = ['success' => false, 'message' => 'Server error: Cannot write file.'];
                    logError("Cannot write file error");
                    break;
                default:
                    $response = ['success' => false, 'message' => 'Upload failed with error code: ' . $uploadError];
                    logError("Unknown upload error: " . $uploadError);
                    break;
            }
        } elseif ($_FILES['shopLogo']['size'] > 0) {
            logError("File validation started. Size: " . $_FILES['shopLogo']['size'] . " bytes");
            $uploadDir = 'uploads/';
            $allowedMimes = ['image/jpeg', 'image/pjpeg', 'image/png', 'image/gif'];
            $maxSize = 2 * 1024 * 1024; // 2MB
            
            $fileType = $_FILES['shopLogo']['type'];
            $fileSize = $_FILES['shopLogo']['size'];
            $fileName = $_FILES['shopLogo']['name'];
            $tmpName = $_FILES['shopLogo']['tmp_name'];
            
            // Validate file extension
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            logError("File details - Name: $fileName, Type: $fileType, Size: $fileSize, Extension: $fileExtension");
            
            if (!in_array($fileExtension, $allowedExtensions)) {
                $response = ['success' => false, 'message' => 'Invalid file type. Please upload JPG, PNG, or GIF files only.'];
                logError("Invalid file extension: $fileExtension");
            } elseif ($fileSize > $maxSize) {
                $response = ['success' => false, 'message' => 'File too large. Maximum size is 2MB.'];
                logError("File too large: $fileSize bytes");
            } elseif (!in_array($fileType, $allowedMimes)) {
                $response = ['success' => false, 'message' => 'Invalid MIME type. Please upload a valid image file.'];
                logError("Invalid MIME type: $fileType");
            } elseif (!is_uploaded_file($tmpName)) {
                $response = ['success' => false, 'message' => 'Invalid file upload.'];
                logError("Not an uploaded file: $tmpName");
            } else {
                logError("File validation passed, proceeding with upload");
                // Ensure upload directory exists and is writable
                logError("Checking upload directory: $uploadDir");
                if (!file_exists($uploadDir)) {
                    logError("Creating upload directory");
                    if (!mkdir($uploadDir, 0777, true)) {
                        logError("Failed to create upload directory");
                        $response = ['success' => false, 'message' => 'Failed to create upload directory.'];
                    }
                }
                
                if (!is_writable($uploadDir)) {
                    logError("Upload directory is not writable");
                    $response = ['success' => false, 'message' => 'Upload directory is not writable.'];
                } else {
                    logError("Upload directory is writable, proceeding...");
                    // Generate unique filename
                    $newFileName = 'logo_' . md5(session_id() . time()) . '_' . time() . '.' . $fileExtension;
                    $uploadPath = $uploadDir . $newFileName;
                    
                    // Remove old logo if exists
                    if (isset($_SESSION['shopLogo']) && $_SESSION['shopLogo'] && file_exists($_SESSION['shopLogo'])) {
                        unlink($_SESSION['shopLogo']);
                    }
                    
                    logError("Attempting to move file from $tmpName to $uploadPath");
                    if (move_uploaded_file($tmpName, $uploadPath)) {
                        logError("File moved successfully");
                        $_SESSION['shopLogo'] = $uploadPath;
                        $_SESSION['shopLogoName'] = $fileName;
                        $response = [
                            'success' => true, 
                            'message' => 'Logo uploaded successfully!',
                            'logoPath' => $uploadPath,
                            'logoName' => $fileName
                        ];
                        logError("Upload completed successfully: $uploadPath");
                    } else {
                        logError("Failed to move uploaded file from $tmpName to $uploadPath");
                        $response = ['success' => false, 'message' => 'Failed to move uploaded file. Please check server permissions.'];
                    }
                }
            }
        } else {
            logError("File size is 0 or negative");
            $response = ['success' => false, 'message' => 'No file selected or file is empty.'];
        }
    } else {
        logError("No shopLogo in FILES array");
        $response = ['success' => false, 'message' => 'No file data received.'];
    }
    
    logError("Final response: " . json_encode($response));
    echo json_encode($response);
} else {
    logError("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
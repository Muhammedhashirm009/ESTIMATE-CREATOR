<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => 'Failed to remove logo'];
    
    // Check if logo exists in session
    if (isset($_SESSION['shopLogo']) && $_SESSION['shopLogo']) {
        $logoPath = $_SESSION['shopLogo'];
        
        // Remove the physical file if it exists
        if (file_exists($logoPath)) {
            if (unlink($logoPath)) {
                // Clear logo from session
                unset($_SESSION['shopLogo']);
                unset($_SESSION['shopLogoName']);
                
                $response = ['success' => true, 'message' => 'Logo removed successfully'];
            } else {
                $response = ['success' => false, 'message' => 'Failed to delete logo file'];
            }
        } else {
            // Clear session even if file doesn't exist
            unset($_SESSION['shopLogo']);
            unset($_SESSION['shopLogoName']);
            $response = ['success' => true, 'message' => 'Logo removed successfully'];
        }
    } else {
        $response = ['success' => false, 'message' => 'No logo found to remove'];
    }
    
    echo json_encode($response);
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
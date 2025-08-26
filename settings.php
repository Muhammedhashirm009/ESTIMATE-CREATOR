<?php
session_start();

// Initialize default settings if not set
if (!isset($_SESSION['settings'])) {
    $_SESSION['settings'] = [
        'shop_name' => 'Computer Accessories Store',
        'shop_address' => '',
        'shop_phone' => '',
        'shop_email' => '',
        'estimate_prefix' => 'EST'
    ];
}

// Handle form submission
if ($_POST) {
    $_SESSION['settings']['shop_name'] = $_POST['shop_name'] ?? 'Computer Accessories Store';
    $_SESSION['settings']['shop_address'] = $_POST['shop_address'] ?? '';
    $_SESSION['settings']['shop_phone'] = $_POST['shop_phone'] ?? '';
    $_SESSION['settings']['shop_email'] = $_POST['shop_email'] ?? '';
    $_SESSION['settings']['estimate_prefix'] = $_POST['estimate_prefix'] ?? 'EST';
    
    $success_message = "Settings saved successfully!";
}

$settings = $_SESSION['settings'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Estimate Maker</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .tabs {
            display: flex;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .tab-button {
            flex: 1;
            padding: 15px;
            background: transparent;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            text-decoration: none;
            color: #666;
            text-align: center;
        }
        
        .tab-button.active {
            background: #3498db;
            color: white;
        }
        
        .tab-button:hover:not(.active) {
            background: #e9ecef;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Estimate Maker Settings</h1>
        
        <div class="tabs">
            <a href="index.php" class="tab-button">Create Estimate</a>
            <button class="tab-button active">Settings</button>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="settings.php">
            <div class="section">
                <h2>Shop Information</h2>
                
                <div class="form-group">
                    <label for="shop_name">Shop Name:</label>
                    <input type="text" id="shop_name" name="shop_name" value="<?php echo htmlspecialchars($settings['shop_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="shop_address">Address:</label>
                    <textarea id="shop_address" name="shop_address" rows="3" placeholder="Street address, City, State, ZIP"><?php echo htmlspecialchars($settings['shop_address']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="shop_phone">Phone:</label>
                    <input type="tel" id="shop_phone" name="shop_phone" value="<?php echo htmlspecialchars($settings['shop_phone']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="shop_email">Email:</label>
                    <input type="email" id="shop_email" name="shop_email" value="<?php echo htmlspecialchars($settings['shop_email']); ?>">
                </div>
            </div>
            
            <div class="section">
                <h2>Estimate Settings</h2>
                
                <div class="form-group">
                    <label for="estimate_prefix">Estimate Number Prefix:</label>
                    <input type="text" id="estimate_prefix" name="estimate_prefix" value="<?php echo htmlspecialchars($settings['estimate_prefix']); ?>" maxlength="10">
                    <small style="color: #666; font-size: 14px;">This will appear before the estimate number (e.g., EST-001, QUOTE-001)</small>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary">Save Settings</button>
                <a href="index.php" class="btn-secondary" style="text-decoration: none; display: inline-block; text-align: center;">Back to Estimates</a>
            </div>
        </form>
    </div>
</body>
</html>
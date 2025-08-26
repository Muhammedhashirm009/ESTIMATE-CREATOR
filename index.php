<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimate Generator</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    <!-- Custom CSS -->
    <link href="assets/styles.css" rel="stylesheet">
    <style>
    /* Upload button styling for consistency across environments */
    .upload-logo-btn[data-disabled="true"] {
        opacity: 0.6 !important;
        cursor: not-allowed !important;
        pointer-events: none;
    }
    .upload-logo-btn[data-disabled="false"] {
        opacity: 1 !important;
        cursor: pointer !important;
        pointer-events: auto;
    }
    .btn-disabled {
        opacity: 0.6 !important;
        cursor: not-allowed !important;
    }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid px-2">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <!-- Header -->
                <div class="text-center py-3">
                    <img src="assets/logo.svg" alt="Logo" class="logo mb-2">
                    <h1 class="h3 text-primary">Estimate Generator</h1>
                    <p class="text-muted">Professional estimates for computer accessories</p>
                </div>

                <!-- Navigation Tabs -->
                <ul class="nav nav-pills nav-justified mb-4" id="mainTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="estimate-tab" data-bs-toggle="pill" data-bs-target="#estimate" type="button" role="tab">
                            <i data-feather="file-text" class="me-1"></i>
                            <span>Estimate</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="settings-tab" data-bs-toggle="pill" data-bs-target="#settings" type="button" role="tab">
                            <i data-feather="settings" class="me-1"></i>
                            <span>Settings</span>
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="mainTabContent">
                    <!-- Estimate Tab -->
                    <div class="tab-pane fade show active" id="estimate" role="tabpanel">
                        <form id="estimateForm" method="POST" action="generate_pdf.php">
                            <!-- Hidden fields for shop settings -->
                            <input type="hidden" id="hiddenShopName" name="shopName">
                            <input type="hidden" id="hiddenShopAddress" name="shopAddress">
                            <input type="hidden" id="hiddenShopPhone" name="shopPhone">
                            <input type="hidden" id="hiddenShopEmail" name="shopEmail">
                            <input type="hidden" id="hiddenShopWebsite" name="shopWebsite">
                            <input type="hidden" id="hiddenShopGST" name="shopGST">
                            <input type="hidden" id="hiddenBankName" name="bankName">
                            <input type="hidden" id="hiddenAccountNumber" name="accountNumber">
                            <input type="hidden" id="hiddenIfscCode" name="ifscCode">
                            <input type="hidden" id="hiddenAccountHolder" name="accountHolder">
                            
                            <!-- Estimate Details -->
                            <div class="card mb-4">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="card-title mb-0">
                                        <i data-feather="file-text" class="me-2"></i>
                                        Estimate Details
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <label for="estimateNumber" class="form-label">Estimate Number</label>
                                            <input type="text" class="form-control form-control-lg" id="estimateNumber" name="estimateNumber" placeholder="EST-20250821-0001">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label for="estimateDate" class="form-label">Date</label>
                                            <input type="date" class="form-control form-control-lg" id="estimateDate" name="estimateDate">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Customer Information -->
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">
                                        <i data-feather="user" class="me-2"></i>
                                        Customer Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="customerName" class="form-label">Customer Name *</label>
                                            <input type="text" class="form-control form-control-lg" id="customerName" name="customerName" required>
                                        </div>
                                        <div class="col-12">
                                            <label for="customerEmail" class="form-label">Email</label>
                                            <input type="email" class="form-control form-control-lg" id="customerEmail" name="customerEmail">
                                        </div>
                                        <div class="col-12">
                                            <label for="customerPhone" class="form-label">Phone</label>
                                            <input type="tel" class="form-control form-control-lg" id="customerPhone" name="customerPhone">
                                        </div>
                                        <div class="col-12">
                                            <label for="customerAddress" class="form-label">Address</label>
                                            <textarea class="form-control form-control-lg" id="customerAddress" name="customerAddress" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Items -->
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="card-title mb-0">
                                        <i data-feather="shopping-cart" class="me-2"></i>
                                        Items
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div id="itemsContainer">
                                        <!-- Items will be added dynamically -->
                                    </div>
                                    <button type="button" class="btn btn-outline-success btn-lg w-100 mt-3" id="addItemBtn">
                                        <i data-feather="plus" class="me-2"></i>
                                        Add Item
                                    </button>
                                </div>
                            </div>

                            <!-- Total -->
                            <div class="card mb-4">
                                <div class="card-body bg-light">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h4 class="mb-0">Total Amount:</h4>
                                        </div>
                                        <div class="col-6 text-end">
                                            <h3 class="text-primary mb-0" id="totalAmount">₹0.00</h3>
                                            <input type="hidden" name="total" id="totalInput" value="0">
                                            <input type="number" class="form-control form-control-lg text-end" id="manualTotalInput" 
                                                   placeholder="Enter total amount" min="0" step="0.01" style="display: none;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- PDF Options -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="hideAmountsToggle">
                                        <label class="form-check-label" for="hideAmountsToggle">
                                            <strong>Hide Item Prices</strong>
                                            <small class="text-muted d-block">Show only items, quantities and total amount (no individual prices)</small>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Generate PDF Button -->
                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-4" id="generatePdfBtn">
                                <i data-feather="download" class="me-2"></i>
                                Generate PDF Estimate
                            </button>
                        </form>
                    </div>

                    <!-- Settings Tab -->
                    <div class="tab-pane fade" id="settings" role="tabpanel">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h5 class="card-title mb-0">
                                    <i data-feather="settings" class="me-2"></i>
                                    Shop Settings
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="shopName" class="form-label">Shop Name</label>
                                        <input type="text" class="form-control form-control-lg" id="shopName" placeholder="COMPUTER SOFT">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Address Lines</label>
                                        <div id="addressContainer">
                                            <!-- Address lines will be added dynamically -->
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addAddressLineBtn">
                                            <i data-feather="plus" class="me-1"></i>
                                            Add Address Line
                                        </button>
                                        <textarea class="form-control" id="shopAddress" name="shopAddress" style="display: none;" readonly></textarea>
                                        <small class="form-text text-muted">Add as many address lines as needed. Each line will appear separately on the letterhead.</small>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="shopPhone" class="form-label">Phone</label>
                                        <input type="tel" class="form-control form-control-lg" id="shopPhone" placeholder="0497 2767015 / 9142 927 321">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="shopEmail" class="form-label">Email</label>
                                        <input type="email" class="form-control form-control-lg" id="shopEmail" placeholder="computersoftknr@gmail.com">
                                    </div>
                                    <div class="col-12">
                                        <label for="shopWebsite" class="form-label">Website</label>
                                        <input type="url" class="form-control form-control-lg" id="shopWebsite" placeholder="www.computersoft.in">
                                    </div>
                                    <div class="col-12">
                                        <label for="shopGST" class="form-label">GST Number</label>
                                        <input type="text" class="form-control form-control-lg" id="shopGST" placeholder="29ABCDE1234F1Z5">
                                    </div>
                                    
                                    <!-- Bank Details Section -->
                                    <div class="col-12 mt-4">
                                        <h6 class="text-info mb-3">Bank Account Details</h6>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="bankName" class="form-label">Bank Name</label>
                                        <input type="text" class="form-control form-control-lg" id="bankName" placeholder="State Bank of India">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="accountNumber" class="form-label">Account Number</label>
                                        <input type="text" class="form-control form-control-lg" id="accountNumber" placeholder="1234567890">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="ifscCode" class="form-label">IFSC Code</label>
                                        <input type="text" class="form-control form-control-lg" id="ifscCode" placeholder="SBIN0001234">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="accountHolder" class="form-label">Account Holder Name</label>
                                        <input type="text" class="form-control form-control-lg" id="accountHolder" placeholder="COMPUTER SOFT">
                                    </div>
                                    <div class="col-12">
                                        <label for="shopLogo" class="form-label">Shop Logo</label>
                                        <div class="input-group input-group-lg">
                                            <input type="file" class="form-control" id="shopLogo" accept="image/*">
                                            <button type="button" class="btn btn-primary upload-logo-btn" id="uploadLogoBtn" data-disabled="true" style="opacity: 0.6; cursor: not-allowed;">
                                                <i data-feather="upload" class="me-1"></i>
                                                Upload Logo
                                            </button>
                                        </div>
                                        <small class="form-text text-muted">Select a logo image (JPG, PNG, GIF under 2MB), then click Upload Logo.</small>
                                        <div id="logoPreview" class="mt-3" style="display: none;">
                                            <div class="card">
                                                <div class="card-body p-2">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center">
                                                            <img id="logoImage" src="" alt="Logo Preview" style="max-width: 60px; max-height: 60px; object-fit: contain;" class="me-2">
                                                            <div>
                                                                <small class="text-success d-block">Current logo:</small>
                                                                <strong id="logoFileName" class="text-muted"></strong>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-outline-danger btn-sm" id="removeLogoBtn" title="Remove current logo">
                                                            <i data-feather="trash-2" class="me-1"></i>
                                                            Remove
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="logoUploadAlert" class="mt-2"></div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-success btn-lg w-100 mt-4" id="saveSettingsBtn">
                                    <i data-feather="save" class="me-2"></i>
                                    Save Settings
                                </button>
                                <div id="settingsAlert" class="mt-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="assets/script.js"></script>
    <script>
        // Initialize Feather Icons
        feather.replace();
        
        // Fallback initialization for upload button (in case of timing issues)
        window.addEventListener('load', function() {
            setTimeout(function() {
                const logoInput = document.getElementById('shopLogo');
                const uploadBtn = document.getElementById('uploadLogoBtn');
                
                if (logoInput && uploadBtn) {
                    // Ensure event listeners are attached
                    if (!logoInput.hasAttribute('data-initialized')) {
                        logoInput.addEventListener('change', function() {
                            if (logoInput.files.length > 0) {
                                uploadBtn.disabled = false;
                                uploadBtn.setAttribute('data-disabled', 'false');
                                uploadBtn.style.opacity = '1';
                                uploadBtn.style.cursor = 'pointer';
                                uploadBtn.classList.remove('btn-disabled');
                            } else {
                                uploadBtn.disabled = true;
                                uploadBtn.setAttribute('data-disabled', 'true');
                                uploadBtn.style.opacity = '0.6';
                                uploadBtn.style.cursor = 'not-allowed';
                                uploadBtn.classList.add('btn-disabled');
                            }
                        });
                        logoInput.setAttribute('data-initialized', 'true');
                    }
                    
                    // Force initial state check
                    if (logoInput.files.length === 0) {
                        uploadBtn.disabled = true;
                        uploadBtn.setAttribute('data-disabled', 'true');
                        uploadBtn.style.opacity = '0.6';
                        uploadBtn.style.cursor = 'not-allowed';
                        uploadBtn.classList.add('btn-disabled');
                    }
                }
            }, 500);
        });
    </script>
</body>
</html>

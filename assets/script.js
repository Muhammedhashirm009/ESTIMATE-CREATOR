// Estimate Generator JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize application
    initializeApp();
    
    // Load saved settings
    loadSettings();
    
    // Add first item row
    addItemRow();
    
    // Set today's date as default
    const today = new Date();
    const formattedDate = today.getFullYear() + '-' + 
                         String(today.getMonth() + 1).padStart(2, '0') + '-' + 
                         String(today.getDate()).padStart(2, '0');
    const dateField = document.getElementById('estimateDate');
    if (dateField) {
        dateField.value = formattedDate;
    }
    
    // Generate default estimate number
    const estimateNumber = document.getElementById('estimateNumber');
    if (estimateNumber && !estimateNumber.value) {
        const dateStr = today.getFullYear() + 
                       String(today.getMonth() + 1).padStart(2, '0') + 
                       String(today.getDate()).padStart(2, '0');
        estimateNumber.value = `EST-${dateStr}-0001`;
    }
});

let itemCounter = 0;

function initializeApp() {
    // Event listeners with error handling
    try {
        document.getElementById('addItemBtn').addEventListener('click', addItemRow);
        document.getElementById('saveSettingsBtn').addEventListener('click', saveSettings);
        
        // Initialize address lines
        initializeAddressLines();
        const addAddressBtn = document.getElementById('addAddressLineBtn');
        if (addAddressBtn) {
            addAddressBtn.addEventListener('click', addAddressLine);
        } else {
            console.error('Add address line button not found');
        }
        
        // Upload logo button with fallback
        const uploadBtn = document.getElementById('uploadLogoBtn');
        if (uploadBtn) {
            console.log('Adding click listener to upload button');
            uploadBtn.addEventListener('click', function(e) {
                console.log('Upload button clicked!', e);
                uploadLogo();
            });
            // Ensure initial state
            handleLogoSelection();
        } else {
            console.error('Upload button not found during initialization');
        }
        
        // Remove logo button with fallback  
        const removeBtn = document.getElementById('removeLogoBtn');
        if (removeBtn) {
            removeBtn.addEventListener('click', removeLogo);
        }
        
        // File input with fallback
        const logoInput = document.getElementById('shopLogo');
        if (logoInput) {
            logoInput.addEventListener('change', handleLogoSelection);
            // Also add input event for better compatibility
            logoInput.addEventListener('input', handleLogoSelection);
        }
        
        document.getElementById('estimateForm').addEventListener('submit', validateForm);
        
        // Handle PDF toggle
        const hideAmountsToggle = document.getElementById('hideAmountsToggle');
        if (hideAmountsToggle) {
            hideAmountsToggle.addEventListener('change', updatePdfAction);
        }
    } catch (error) {
        console.error('Error initializing app:', error);
    }
    
    // Tab switching event
    const tabLinks = document.querySelectorAll('[data-bs-toggle="pill"]');
    tabLinks.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            // Refresh icons when tab is shown
            feather.replace();
        });
    });
}

function addItemRow() {
    itemCounter++;
    const container = document.getElementById('itemsContainer');
    
    const itemRow = document.createElement('div');
    itemRow.className = 'item-row fade-in';
    itemRow.setAttribute('data-item-id', itemCounter);
    
    itemRow.innerHTML = `
        <div class="row g-3 align-items-center">
            <div class="col-12 col-md-1">
                <div class="item-number">${itemCounter}</div>
            </div>
            <div class="col-12 col-md-5">
                <label class="form-label">Item Description *</label>
                <input type="text" class="form-control form-control-lg item-name" 
                       name="items[]" required 
                       placeholder="e.g., Wireless Mouse, USB Cable">
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label">Quantity *</label>
                <input type="number" class="form-control form-control-lg item-quantity" 
                       name="quantities[]" min="1" step="1" value="1" required>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label price-label">Unit Price *</label>
                <div class="input-group">
                    <span class="input-group-text price-label">₹</span>
                    <input type="number" class="form-control form-control-lg item-price" 
                           name="prices[]" min="0" step="0.01" required 
                           placeholder="0.00">
                </div>
            </div>
            <div class="col-12 col-md-1">
                <label class="form-label d-none d-md-block">&nbsp;</label>
                <button type="button" class="remove-item-btn" onclick="removeItemRow(${itemCounter})" 
                        title="Remove Item">
                    <i data-feather="trash-2" size="16"></i>
                </button>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-12">
                <div class="text-end">
                    <small class="text-muted">Subtotal: </small>
                    <span class="item-subtotal text-primary fw-bold">₹0.00</span>
                </div>
            </div>
        </div>
    `;
    
    container.appendChild(itemRow);
    
    // Add event listeners for real-time calculation
    const quantityInput = itemRow.querySelector('.item-quantity');
    const priceInput = itemRow.querySelector('.item-price');
    
    quantityInput.addEventListener('input', calculateTotals);
    priceInput.addEventListener('input', calculateTotals);
    
    // Refresh Feather icons
    feather.replace();
    
    // Focus on the new item name input
    itemRow.querySelector('.item-name').focus();
}

function removeItemRow(itemId) {
    const itemRow = document.querySelector(`[data-item-id="${itemId}"]`);
    if (itemRow) {
        // Add fade out animation
        itemRow.style.animation = 'fadeOut 0.3s ease-out';
        setTimeout(() => {
            itemRow.remove();
            updateItemNumbers();
            calculateTotals();
        }, 300);
    }
}

function updateItemNumbers() {
    const itemRows = document.querySelectorAll('.item-row');
    itemRows.forEach((row, index) => {
        const numberDiv = row.querySelector('.item-number');
        if (numberDiv) {
            numberDiv.textContent = index + 1;
        }
    });
}

function calculateTotals() {
    let total = 0;
    const itemRows = document.querySelectorAll('.item-row');
    
    itemRows.forEach(row => {
        const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const subtotal = quantity * price;
        
        // Update item subtotal display
        const subtotalElement = row.querySelector('.item-subtotal');
        if (subtotalElement) {
            subtotalElement.textContent = `₹${subtotal.toFixed(2)}`;
        }
        
        total += subtotal;
    });
    
    // Update total display
    document.getElementById('totalAmount').textContent = `₹${total.toFixed(2)}`;
    document.getElementById('totalInput').value = total.toFixed(2);
    
    // Add animation to total
    const totalElement = document.getElementById('totalAmount');
    totalElement.style.transform = 'scale(1.05)';
    setTimeout(() => {
        totalElement.style.transform = 'scale(1)';
    }, 200);
}

function saveSettings() {
    const settings = {
        shopName: document.getElementById('shopName').value,
        shopAddress: document.getElementById('shopAddress').value,
        shopPhone: document.getElementById('shopPhone').value,
        shopEmail: document.getElementById('shopEmail').value,
        shopWebsite: document.getElementById('shopWebsite').value,
        shopGST: document.getElementById('shopGST').value,
        bankName: document.getElementById('bankName').value,
        accountNumber: document.getElementById('accountNumber').value,
        ifscCode: document.getElementById('ifscCode').value,
        accountHolder: document.getElementById('accountHolder').value
    };
    
    // Save to session storage
    Object.keys(settings).forEach(key => {
        if (settings[key]) {
            sessionStorage.setItem(key, settings[key]);
        }
    });
    
    // Send to server session (logo is handled separately)
    const formData = new FormData();
    Object.keys(settings).forEach(key => {
        formData.append(key, settings[key]);
    });
    
    fetch('save_settings.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error saving settings:', error);
        showAlert('Error saving settings. Please try again.', 'danger');
    });
}

function loadSettings() {
    const settingFields = ['shopName', 'shopPhone', 'shopEmail', 'shopWebsite', 'shopGST', 'bankName', 'accountNumber', 'ifscCode', 'accountHolder'];
    
    settingFields.forEach(field => {
        const savedValue = sessionStorage.getItem(field);
        if (savedValue) {
            const element = document.getElementById(field);
            if (element) {
                element.value = savedValue;
            }
        }
    });
    
    // Address is handled by initializeAddressLines() function
    
    // Show current logo if exists
    const logoName = sessionStorage.getItem('shopLogoName');
    const logoPath = sessionStorage.getItem('shopLogoPath');
    if (logoName && logoPath) {
        document.getElementById('logoFileName').textContent = logoName;
        document.getElementById('logoImage').src = logoPath;
        document.getElementById('logoPreview').style.display = 'block';
    }
}

function handleLogoSelection() {
    console.log('handleLogoSelection called');
    const fileInput = document.getElementById('shopLogo');
    const uploadBtn = document.getElementById('uploadLogoBtn');
    
    console.log('File input:', fileInput);
    console.log('Upload button:', uploadBtn);
    
    if (fileInput && uploadBtn) {
        console.log('Files selected:', fileInput.files.length);
        if (fileInput.files.length > 0) {
            console.log('Enabling upload button');
            // Enable button
            uploadBtn.disabled = false;
            uploadBtn.setAttribute('data-disabled', 'false');
            uploadBtn.style.opacity = '1';
            uploadBtn.style.cursor = 'pointer';
            uploadBtn.classList.remove('btn-disabled');
        } else {
            console.log('Disabling upload button');
            // Disable button
            uploadBtn.disabled = true;
            uploadBtn.setAttribute('data-disabled', 'true');
            uploadBtn.style.opacity = '0.6';
            uploadBtn.style.cursor = 'not-allowed';
            uploadBtn.classList.add('btn-disabled');
        }
    } else {
        console.error('Missing elements - fileInput:', fileInput, 'uploadBtn:', uploadBtn);
    }
}

function uploadLogo() {
    console.log('uploadLogo function called');
    const fileInput = document.getElementById('shopLogo');
    const uploadBtn = document.getElementById('uploadLogoBtn');
    
    console.log('Upload elements:', { fileInput, uploadBtn });
    console.log('Button disabled state:', uploadBtn ? uploadBtn.disabled : 'no button');
    console.log('Button data-disabled:', uploadBtn ? uploadBtn.getAttribute('data-disabled') : 'no button');
    
    // Check if button is disabled
    if (uploadBtn && (uploadBtn.disabled || uploadBtn.getAttribute('data-disabled') === 'true')) {
        console.log('Button is disabled, showing alert');
        showLogoAlert('Please select a file first.', 'danger');
        return;
    }
    
    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        console.log('No file selected, showing alert');
        showLogoAlert('Please select a file first.', 'danger');
        return;
    }
    
    console.log('Starting upload process for file:', fileInput.files[0].name);
    
    // Show loading state
    const originalContent = uploadBtn.innerHTML;
    uploadBtn.innerHTML = '<i data-feather="loader" class="me-1"></i>Uploading...';
    uploadBtn.disabled = true;
    
    const formData = new FormData();
    formData.append('shopLogo', fileInput.files[0]);
    
    console.log('FormData created, making fetch request to upload_logo.php');
    
    fetch('upload_logo.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Upload response received:', response);
        return response.json();
    })
    .then(data => {
        console.log('Upload response data:', data);
        if (data.success) {
            // Show logo preview
            document.getElementById('logoFileName').textContent = data.logoName;
            document.getElementById('logoImage').src = data.logoPath;
            document.getElementById('logoPreview').style.display = 'block';
            
            // Save to session storage
            sessionStorage.setItem('shopLogoName', data.logoName);
            sessionStorage.setItem('shopLogoPath', data.logoPath);
            
            // Clear file input and disable upload button
            fileInput.value = '';
            uploadBtn.disabled = true;
            
            // Show success message
            showLogoAlert(data.message, 'success');
        } else {
            showLogoAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error uploading logo:', error);
        console.log('Full error details:', error);
        showLogoAlert('Error uploading logo. Please try again.', 'danger');
    })
    .finally(() => {
        // Restore button and refresh state
        uploadBtn.innerHTML = originalContent;
        setTimeout(() => {
            handleLogoSelection();
        }, 100);
        // Refresh icons
        feather.replace();
    });
}

function removeLogo() {
    // Show confirmation dialog
    if (!confirm('Are you sure you want to remove the current logo?')) {
        return;
    }
    
    // Show loading state on remove button
    const removeBtn = document.getElementById('removeLogoBtn');
    const originalContent = removeBtn.innerHTML;
    removeBtn.innerHTML = '<i data-feather="loader" class="me-1"></i>Removing...';
    removeBtn.disabled = true;
    
    fetch('remove_logo.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hide logo preview
            document.getElementById('logoPreview').style.display = 'none';
            // Clear the file input and refresh upload button state
            const logoInput = document.getElementById('shopLogo');
            if (logoInput) {
                logoInput.value = '';
            }
            setTimeout(() => {
                handleLogoSelection();
            }, 100);
            // Remove from session storage
            sessionStorage.removeItem('shopLogoName');
            sessionStorage.removeItem('shopLogoPath');
            // Show success message
            showLogoAlert(data.message, 'success');
        } else {
            showLogoAlert(data.message, 'danger');
            // Restore button
            removeBtn.innerHTML = originalContent;
            removeBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error removing logo:', error);
        showLogoAlert('Error removing logo. Please try again.', 'danger');
        // Restore button
        removeBtn.innerHTML = originalContent;
        removeBtn.disabled = false;
    })
    .finally(() => {
        // Refresh icons
        feather.replace();
    });
}

function showLogoAlert(message, type = 'info') {
    const alertContainer = document.getElementById('logoUploadAlert');
    const alertClass = type === 'success' ? 'alert-success' : 
                      (type === 'error' || type === 'danger') ? 'alert-danger' : 'alert-info';
    
    alertContainer.innerHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i data-feather="${type === 'success' ? 'check-circle' : 'info'}" class="me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Refresh icons
    feather.replace();
    
    // Auto-hide after 3 seconds
    setTimeout(() => {
        const alert = alertContainer.querySelector('.alert');
        if (alert) {
            alert.classList.remove('show');
            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 150);
        }
    }, 3000);
}

function showAlert(message, type = 'info') {
    const alertContainer = document.getElementById('settingsAlert');
    const alertClass = type === 'success' ? 'alert-success' : 
                      (type === 'error' || type === 'danger') ? 'alert-danger' : 'alert-info';
    
    alertContainer.innerHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i data-feather="${type === 'success' ? 'check-circle' : 'info'}" class="me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Refresh icons
    feather.replace();
    
    // Auto-hide after 3 seconds
    setTimeout(() => {
        const alert = alertContainer.querySelector('.alert');
        if (alert) {
            alert.classList.remove('show');
            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 150);
        }
    }, 3000);
}

function updateHiddenFields() {
    // Update hidden form fields with current shop settings from sessionStorage
    const settingFields = ['shopName', 'shopAddress', 'shopPhone', 'shopEmail', 'shopWebsite', 'shopGST', 'bankName', 'accountNumber', 'ifscCode', 'accountHolder'];
    
    settingFields.forEach(field => {
        const savedValue = sessionStorage.getItem(field) || '';
        const hiddenField = document.getElementById('hidden' + field.charAt(0).toUpperCase() + field.slice(1));
        if (hiddenField) {
            hiddenField.value = savedValue;
        }
    });
}

function validateForm(event) {
    // Update hidden fields with current shop settings before validation
    updateHiddenFields();
    
    let isValid = true;
    const errors = [];
    
    // Validate customer name
    const customerName = document.getElementById('customerName');
    if (!customerName.value.trim()) {
        customerName.classList.add('is-invalid');
        errors.push('Customer name is required');
        isValid = false;
    } else {
        customerName.classList.remove('is-invalid');
        customerName.classList.add('is-valid');
    }
    
    // Check if we're in simple mode (hide amounts)
    const hideAmountsToggle = document.getElementById('hideAmountsToggle');
    const isSimpleMode = hideAmountsToggle && hideAmountsToggle.checked;
    
    // Validate at least one item
    const itemRows = document.querySelectorAll('.item-row');
    let hasValidItem = false;
    
    itemRows.forEach(row => {
        const name = row.querySelector('.item-name');
        const quantity = row.querySelector('.item-quantity');
        const price = row.querySelector('.item-price');
        
        let rowValid = true;
        
        // Always validate name
        if (!name.value.trim()) {
            name.classList.add('is-invalid');
            rowValid = false;
        } else {
            name.classList.remove('is-invalid');
            name.classList.add('is-valid');
        }
        
        // Always validate quantity
        if (!quantity.value || quantity.value <= 0) {
            quantity.classList.add('is-invalid');
            rowValid = false;
        } else {
            quantity.classList.remove('is-invalid');
            quantity.classList.add('is-valid');
        }
        
        // Only validate price in normal mode, skip in simple mode
        if (!isSimpleMode) {
            if (!price.value || price.value < 0) {
                price.classList.add('is-invalid');
                rowValid = false;
            } else {
                price.classList.remove('is-invalid');
                price.classList.add('is-valid');
            }
        } else {
            // In simple mode, clear any price validation states
            price.classList.remove('is-invalid', 'is-valid');
        }
        
        // A row is valid if name and quantity are valid (price only in normal mode)
        if (name.value.trim() && quantity.value && quantity.value > 0) {
            if (!isSimpleMode) {
                // Normal mode: also need valid price
                if (price.value && price.value >= 0) {
                    hasValidItem = true;
                }
            } else {
                // Simple mode: just name and quantity needed
                hasValidItem = true;
            }
        }
    });
    
    if (!hasValidItem) {
        errors.push('At least one valid item is required');
        isValid = false;
    }
    
    // In simple mode, validate that total amount is entered
    if (isSimpleMode) {
        const manualTotalInput = document.getElementById('manualTotalInput');
        if (!manualTotalInput.value || manualTotalInput.value <= 0) {
            manualTotalInput.classList.add('is-invalid');
            errors.push('Total amount is required in simple mode');
            isValid = false;
        } else {
            manualTotalInput.classList.remove('is-invalid');
            manualTotalInput.classList.add('is-valid');
        }
    }
    
    // Show validation errors
    if (!isValid) {
        event.preventDefault();
        
        // Show error alert
        const errorMessage = errors.join('<br>');
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
        alertDiv.innerHTML = `
            <i data-feather="alert-circle" class="me-2"></i>
            <strong>Please fix the following errors:</strong><br>
            ${errorMessage}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Insert alert before the form
        const form = document.getElementById('estimateForm');
        form.parentNode.insertBefore(alertDiv, form);
        
        // Refresh icons
        feather.replace();
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.classList.remove('show');
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 150);
            }
        }, 5000);
    } else {
        // Show loading state
        const submitBtn = event.target.querySelector('button[type="submit"]');
        const originalContent = submitBtn.innerHTML;
        submitBtn.innerHTML = `
            <div class="spinner-border spinner-border-sm me-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            Generating PDF...
        `;
        submitBtn.disabled = true;
        
        // Re-enable button after 3 seconds (in case of error)
        setTimeout(() => {
            submitBtn.innerHTML = originalContent;
            submitBtn.disabled = false;
        }, 3000);
    }
}

function updatePdfAction() {
    const hideAmountsToggle = document.getElementById('hideAmountsToggle');
    const form = document.getElementById('estimateForm');
    const generateBtn = document.getElementById('generatePdfBtn');
    const totalAmount = document.getElementById('totalAmount');
    const manualTotalInput = document.getElementById('manualTotalInput');
    const totalInput = document.getElementById('totalInput');
    
    if (hideAmountsToggle && form && generateBtn) {
        if (hideAmountsToggle.checked) {
            // Switch to simple PDF mode
            form.action = 'generate_pdf_simple.php';
            generateBtn.innerHTML = '<i data-feather="download" class="me-2"></i>Generate Simple PDF';
            
            // Show manual total input and hide calculated total
            totalAmount.style.display = 'none';
            manualTotalInput.style.display = 'block';
            manualTotalInput.focus();
            
            // Gray out price columns in item rows
            const priceInputs = document.querySelectorAll('.item-price');
            const priceLabels = document.querySelectorAll('.price-label');
            const subtotalElements = document.querySelectorAll('.item-subtotal');
            
            priceInputs.forEach(input => {
                input.style.opacity = '0.5';
                input.style.pointerEvents = 'none';
                input.style.backgroundColor = '#f8f9fa';
                input.setAttribute('disabled', 'true');
            });
            priceLabels.forEach(label => {
                label.style.opacity = '0.5';
            });
            subtotalElements.forEach(element => {
                element.style.opacity = '0.5';
                element.parentElement.style.opacity = '0.5';
            });
            
            // Update total input when manual input changes and sync the current calculated total
            const currentTotal = totalInput.value || 0;
            if (currentTotal > 0) {
                manualTotalInput.value = currentTotal;
            }
            
            manualTotalInput.addEventListener('input', function() {
                totalInput.value = this.value || 0;
            });
        } else {
            // Switch to normal PDF mode
            form.action = 'generate_pdf.php';
            generateBtn.innerHTML = '<i data-feather="download" class="me-2"></i>Generate PDF Estimate';
            
            // Show calculated total and hide manual input
            totalAmount.style.display = 'block';
            manualTotalInput.style.display = 'none';
            
            // Restore price columns in item rows
            const priceInputs = document.querySelectorAll('.item-price');
            const priceLabels = document.querySelectorAll('.price-label');
            const subtotalElements = document.querySelectorAll('.item-subtotal');
            
            priceInputs.forEach(input => {
                input.style.opacity = '1';
                input.style.pointerEvents = 'auto';
                input.style.backgroundColor = '';
                input.removeAttribute('disabled');
            });
            priceLabels.forEach(label => {
                label.style.opacity = '1';
            });
            subtotalElements.forEach(element => {
                element.style.opacity = '1';
                element.parentElement.style.opacity = '1';
            });
            
            // Recalculate total
            updateTotal();
        }
        
        // Refresh feather icons
        feather.replace();
    }
}

// Address lines management
let addressLineCounter = 0;

function initializeAddressLines() {
    // Clear any existing address lines first
    const container = document.getElementById('addressContainer');
    if (container) {
        container.innerHTML = '';
        addressLineCounter = 0;
    }
    
    const savedAddress = sessionStorage.getItem('shopAddress');
    if (savedAddress && savedAddress.trim()) {
        const lines = savedAddress.split('\n').filter(line => line.trim());
        if (lines.length > 0) {
            lines.forEach(line => addAddressLine(line.trim()));
        } else {
            // Add hardcoded default address lines
            addAddressLine('1st Floor, Global Village');
            addAddressLine('Bank Road');
            addAddressLine('Kannur-1');
        }
    } else {
        // Add hardcoded default address lines
        addAddressLine('1st Floor, Global Village');
        addAddressLine('Bank Road');
        addAddressLine('Kannur-1');
    }
}

function addAddressLine(value = '') {
    addressLineCounter++;
    const container = document.getElementById('addressContainer');
    
    const lineDiv = document.createElement('div');
    lineDiv.className = 'input-group mb-2';
    lineDiv.setAttribute('data-line-id', addressLineCounter);
    
    lineDiv.innerHTML = `
        <input type="text" class="form-control address-line" placeholder="Address line ${addressLineCounter}" value="${value}">
        <button type="button" class="btn btn-outline-danger remove-address-line" onclick="removeAddressLine(${addressLineCounter})" title="Remove line">
            <i data-feather="x" size="16"></i>
        </button>
    `;
    
    container.appendChild(lineDiv);
    
    // Add event listener for real-time update
    const input = lineDiv.querySelector('.address-line');
    input.addEventListener('input', updateAddressField);
    
    // Refresh Feather icons
    feather.replace();
    
    // Update the combined address field
    updateAddressField();
    
    // Focus on the new input
    input.focus();
}

function removeAddressLine(lineId) {
    const lineDiv = document.querySelector(`[data-line-id="${lineId}"]`);
    if (lineDiv) {
        // Add fade out animation
        lineDiv.style.animation = 'fadeOut 0.3s ease-out';
        setTimeout(() => {
            lineDiv.remove();
            updateAddressField();
        }, 300);
    }
}

function updateAddressField() {
    const addressLines = document.querySelectorAll('.address-line');
    const addressValues = Array.from(addressLines)
        .map(input => input.value.trim())
        .filter(value => value.length > 0);
    
    const combinedAddress = addressValues.join('\n');
    document.getElementById('shopAddress').value = combinedAddress;
    
    // Save to sessionStorage
    if (combinedAddress) {
        sessionStorage.setItem('shopAddress', combinedAddress);
    } else {
        sessionStorage.removeItem('shopAddress');
    }
}

// Add CSS animation for fade out
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-10px);
        }
    }
    .address-line:focus {
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    }
    .remove-address-line {
        border-left: 0;
    }
`;
document.head.appendChild(style);

// Handle responsive behavior
window.addEventListener('resize', function() {
    // Update layout for mobile/desktop transitions
    updateItemNumbers();
});

// Prevent form submission on Enter key in input fields (except submit button)
document.addEventListener('keypress', function(event) {
    if (event.key === 'Enter' && event.target.tagName !== 'BUTTON' && event.target.type !== 'submit') {
        event.preventDefault();
        
        // Move to next input field
        const inputs = Array.from(document.querySelectorAll('input, textarea, select'));
        const currentIndex = inputs.indexOf(event.target);
        if (currentIndex < inputs.length - 1) {
            inputs[currentIndex + 1].focus();
        }
    }
});

// Auto-save settings on input
const settingInputs = document.querySelectorAll('#settings input, #settings textarea');
settingInputs.forEach(input => {
    input.addEventListener('blur', function() {
        const key = this.id;
        const value = this.value;
        if (value) {
            sessionStorage.setItem(key, value);
        } else {
            sessionStorage.removeItem(key);
        }
    });
});

// Initialize save_settings.php handler
if (!document.querySelector('script[src*="save_settings.php"]')) {
    // Create save_settings.php functionality inline
    const saveSettingsScript = document.createElement('script');
    saveSettingsScript.textContent = `
        // This would normally be a separate PHP file
        // For now, we'll handle settings saving in sessionStorage
        window.saveSettingsToServer = function(formData) {
            return new Promise((resolve) => {
                setTimeout(() => {
                    resolve({ success: true });
                }, 100);
            });
        };
    `;
    document.head.appendChild(saveSettingsScript);
}

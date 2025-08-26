<?php
session_start();
ini_set('memory_limit', '256M');
require_once('vendor/autoload.php');
require_once('vendor/setasign/fpdf/fpdf.php');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// Get form data
$customerName = $_POST['customerName'] ?? '';
$customerEmail = $_POST['customerEmail'] ?? '';
$customerPhone = $_POST['customerPhone'] ?? '';
$customerAddress = $_POST['customerAddress'] ?? '';
$estimateNumber = $_POST['estimateNumber'] ?? '';
$estimateDate = $_POST['estimateDate'] ?? '';
$items = $_POST['items'] ?? [];
$quantities = $_POST['quantities'] ?? [];
$total = $_POST['total'] ?? 0;

// Get shop settings from POST data (form submission) with Computer Soft defaults
$shopName = $_POST['shopName'] ?? 'COMPUTER SOFT';
$shopAddress = $_POST['shopAddress'] ?? '1st Floor, Global Village, Bank Road, Kannur-1';
$shopPhone = $_POST['shopPhone'] ?? '0497 2767015 / 9142 927 321';
$shopEmail = $_POST['shopEmail'] ?? 'computersoftknr@gmail.com';
$shopWebsite = $_POST['shopWebsite'] ?? 'www.computersoft.in';
$shopGST = $_POST['shopGST'] ?? '';
$bankName = $_POST['bankName'] ?? '';
$accountNumber = $_POST['accountNumber'] ?? '';
$ifscCode = $_POST['ifscCode'] ?? '';
$accountHolder = $_POST['accountHolder'] ?? '';
$shopLogo = $_SESSION['shopLogo'] ?? null;

class SimpleEstimatePDF extends FPDF {
    private $shopName;
    private $shopAddress;
    private $shopPhone;
    private $shopEmail;
    private $shopWebsite;
    private $shopGST;
    private $logoPath;
    private $bankName;
    private $accountNumber;
    private $ifscCode;
    private $accountHolder;
    
    function __construct($shopName, $shopAddress, $shopPhone, $shopEmail, $shopWebsite, $shopGST, $logoPath, $bankName, $accountNumber, $ifscCode, $accountHolder) {
        parent::__construct();
        $this->shopName = $shopName;
        $this->shopAddress = $shopAddress;
        $this->shopPhone = $shopPhone;
        $this->shopEmail = $shopEmail;
        $this->shopWebsite = $shopWebsite;
        $this->shopGST = $shopGST;
        $this->logoPath = $logoPath;
        $this->bankName = $bankName;
        $this->accountNumber = $accountNumber;
        $this->ifscCode = $ifscCode;
        $this->accountHolder = $accountHolder;
    }
    
    function Header() {
        // Calculate positions for centered logo
        $headerHeight = 45; // Total header height before line
        $logoSize = 100;
        $logoY = ($headerHeight - ($logoSize * 0.6)) / 2 + 8; // Center logo vertically
        
        // Logo on the left center
        if ($this->logoPath && file_exists($this->logoPath)) {
            $this->Image($this->logoPath, 5, $logoY, $logoSize, 0); // Full left logo
        }
        
        // Shop details on the far right, centered vertically
        $this->SetFont('Arial','',10);
        $this->SetTextColor(51, 122, 183); // Blue color
        
        // Position at far right, centered between top and line
        $rightX = 155; // Far right position
        $startY = ($headerHeight / 2) - 15; // Position higher in header space
        
        // Shop address with tighter spacing
        $addressLines = explode("\n", $this->shopAddress);
        $yPos = $startY;
        foreach ($addressLines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $this->SetXY($rightX, $yPos);
                $this->Cell(0, 4, $line, 0, 1, 'L'); // Reduced height from 5 to 4
                $yPos += 4; // Tighter spacing
            }
        }
        
        $yPos += 2; // Reduced gap from 3 to 2
        
        // Phone number with label
        $this->SetXY($rightX, $yPos);
        $this->Cell(0, 4, 'Mobile No: ' . $this->shopPhone, 0, 1, 'L');
        $yPos += 5;
        
        // Email address with label
        $this->SetXY($rightX, $yPos);
        $this->Cell(0, 4, 'Email: ' . $this->shopEmail, 0, 1, 'L');
        $yPos += 5;
        
        // Website with label
        $this->SetXY($rightX, $yPos);
        $this->Cell(0, 4, 'Website: ' . $this->shopWebsite, 0, 1, 'L');
        $yPos += 5;
        
        // GST Number with label (if provided)
        if (!empty($this->shopGST)) {
            $this->SetXY($rightX, $yPos);
            $this->Cell(0, 4, 'GST No: ' . $this->shopGST, 0, 1, 'L');
        }
        
        // Reset text color to black
        $this->SetTextColor(0, 0, 0);
        
        // Fixed line position
        $this->SetY($headerHeight);
        $this->Line(10, $headerHeight, 200, $headerHeight);
        $this->Ln(10);
    }

    function Footer() {
        // Clean footer with just page number
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        // Center - page number only
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
    }
}

// Create PDF
$pdf = new SimpleEstimatePDF($shopName, $shopAddress, $shopPhone, $shopEmail, $shopWebsite, $shopGST, $shopLogo, $bankName, $accountNumber, $ifscCode, $accountHolder);
$pdf->AddPage();

// Use provided estimate number or generate one
if (empty($estimateNumber)) {
    $estimateNo = 'EST-' . date('Ymd') . '-' . substr(md5($customerName . time()), 0, 4);
} else {
    $estimateNo = $estimateNumber;
}

// Use provided date or current date
if (empty($estimateDate)) {
    $displayDate = date('d-m-Y');
} else {
    $displayDate = date('d-m-Y', strtotime($estimateDate));
}

// Customer Details Section
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,'ESTIMATE',0,1,'C');
$pdf->Ln(5);

$pdf->SetFont('Arial','',12);
$pdf->Cell(100,8,"Estimate No: $estimateNo",0,0);
$pdf->Cell(0,8,"Date: $displayDate",0,1);

$pdf->Cell(100,8,"Customer: $customerName",0,1);

if (!empty($customerPhone)) {
    $pdf->Cell(100,8,"Phone: $customerPhone",0,1);
}

if (!empty($customerEmail)) {
    $pdf->Cell(100,8,"Email: $customerEmail",0,1);
}

if (!empty($customerAddress)) {
    $pdf->Cell(20,8,"Address:",0,0);
    $pdf->MultiCell(80,8,$customerAddress,0,'L');
}

$pdf->Ln(10);

// Items Table Header (Simple version - no price columns)
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(230,230,230);
$pdf->Cell(20,10,'S.No',1,0,'C',true);
$pdf->Cell(120,10,'Item Description',1,0,'C',true);
$pdf->Cell(30,10,'Quantity',1,1,'C',true);

// Items
$pdf->SetFont('Arial','',10);
$serial = 1;

for ($i = 0; $i < count($items); $i++) {
    if (!empty($items[$i])) {
        $pdf->Cell(20,10,$serial,1,0,'C');
        $pdf->Cell(120,10,$items[$i],1,0,'L');
        $pdf->Cell(30,10,$quantities[$i] ?? '1',1,1,'C');
        $serial++;
    }
}

// Add some spacing before total
$pdf->Ln(10);

// Total Amount (simplified)
$pdf->SetFont('Arial','B',14);
$pdf->Cell(120,12,'',0,0); // Empty space
$pdf->Cell(50,12,'Total Amount: Rs. ' . number_format($total, 2),0,1,'R');

// Bank Details section below total
$pdf->Ln(10);
if (!empty($bankName) || !empty($accountNumber) || !empty($ifscCode) || !empty($accountHolder)) {
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(0,6,"Bank Details:",0,1,'L');
    
    $pdf->SetFont('Arial','',9);
    if (!empty($bankName)) {
        $pdf->Cell(0,5,"Bank Name: " . $bankName,0,1,'L');
    }
    if (!empty($accountNumber)) {
        $pdf->Cell(0,5,"Account Number: " . $accountNumber,0,1,'L');
    }
    if (!empty($ifscCode)) {
        $pdf->Cell(0,5,"IFSC Code: " . $ifscCode,0,1,'L');
    }
    if (!empty($accountHolder)) {
        $pdf->Cell(0,5,"Account Holder: " . $accountHolder,0,1,'L');
    }
}

$pdf->Ln(10);

// Generate filename
$filename = 'estimate_' . str_replace(' ', '_', $customerName) . '_' . date('Ymd') . '_simple_' . time() . '.pdf';
$filepath = 'attached_assets/' . $filename;

// Save PDF
$pdf->Output('F', $filepath);

// Output PDF for download
$pdf->Output('D', $filename);
?>
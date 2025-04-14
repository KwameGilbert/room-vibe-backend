<?php

require_once __DIR__ . '/../vendor/autoload.php'; 

use TCPDF as TCPDF;

$viewReceiptUrl = "http://roomvibe.rf.gd";
// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('RoomVibe');
$pdf->SetAuthor('RoomVibe');
$pdf->SetTitle('Booking Receipt');
$pdf->SetSubject('Booking Receipt #');
$pdf->SetKeywords('Receipt, Booking, RoomVibe');

// Remove header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(15, 15, 15);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 15);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Set font
$pdf->SetFont('helvetica', '', 10);

// Add a page
$pdf->AddPage();

// Get the current date
$currentDate = date('j F Y');

// Create the QR code
$qrStyle = [
    'border' => 2,
    'vpadding' => 'auto',
    'hpadding' => 'auto',
    'fgcolor' => [255,165,0],
    'bgcolor' => [0,0,0],
    'module_width' => 1,
    'module_height' => 1
];

// Generate QR code and get its position
$pdf->write2DBarcode($viewReceiptUrl, 'QRCODE,L', 150, 15, 40, 40, $qrStyle, 'N');

// Output the PDF
$pdf->Output('RoomVibe QR'. '.pdf', 'I'); // 'D' means download
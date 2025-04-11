<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: ./../splash.php");
    exit();
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../vendor/autoload.php'; 

use TCPDF as TCPDF;

// Check if booking ID is provided
if (!isset($_GET['id'])) {
    die("Booking ID not specified.");
}
$booking_id = $_GET['id'];

// Get the domain for QR code generation
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domain = $protocol . $_SERVER['HTTP_HOST'];
$viewReceiptUrl = $domain . "/room-vibe-backend/frontend/booking/view_receipt.php?id=" . $booking_id;

// Fetch booking details
$database = new Database();
$conn = $database->getConnection();

$stmt = $conn->prepare("
    SELECT b.*,
           h.hostel_name, h.location, h.rating, h.accomodation_status,
           r.room_number, r.price, r.specification,
           s.firstName, s.lastName, s.email, s.phone
    FROM booking b
    JOIN hostel h ON b.hostel_id = h.id
    JOIN room r ON b.room_id = r.id
    JOIN student s ON b.student_id = s.id
    WHERE b.id = ?
");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    die("Booking not found.");
}

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('RoomVibe');
$pdf->SetAuthor('RoomVibe');
$pdf->SetTitle('Booking Receipt');
$pdf->SetSubject('Booking Receipt #' . $booking_id);
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
    'fgcolor' => [0,0,0],
    'bgcolor' => [255,255,255],
    'module_width' => 1,
    'module_height' => 1
];

// Generate QR code and get its position
$pdf->write2DBarcode($viewReceiptUrl, 'QRCODE,L', 150, 15, 40, 40, $qrStyle, 'N');

// Logo
$logoPath = __DIR__ . '/../images/room.png';
if (file_exists($logoPath)) {
    $pdf->Image($logoPath, 15, 15, 30, 0, '', '', '', false, 300, '', false, false, 0, false, false, false);
}

// Receipt header
$pdf->SetFont('helvetica', 'B', 20);
$pdf->SetY(30);
$pdf->Cell(0, 10, 'BOOKING RECEIPT', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 5, 'Receipt #: ' . $booking_id, 0, 1, 'C');
$pdf->Cell(0, 5, 'Date: ' . $currentDate, 0, 1, 'C');

$pdf->Ln(10);

// Customer Information
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Customer Information', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 10);

// Table for customer info
$customerInfo = '<table cellpadding="5" cellspacing="0" border="0">
    <tr>
        <td width="25%"><b>Name:</b></td>
        <td>' . $booking['firstName'] . ' ' . $booking['lastName'] . '</td>
    </tr>
    <tr>
        <td><b>Email:</b></td>
        <td>' . $booking['email'] . '</td>
    </tr>
    <tr>
        <td><b>Phone:</b></td>
        <td>' . $booking['phone'] . '</td>
    </tr>
</table>';
$pdf->writeHTML($customerInfo, true, false, false, false, '');

$pdf->Ln(5);

// Booking Details
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Booking Details', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 10);

// Table for booking details
$paymentStatus = ($booking['paid'] == 1) ? '<span style="color: #22c55e">Paid</span>' : '<span style="color: #f97316">Pending</span>';
$bookingDate = date("j M Y, g:i a", strtotime($booking['created_at']));
$bookingInfo = '<table cellpadding="5" cellspacing="0" border="0">
    <tr>
        <td width="35%"><b>Booking Reference:</b></td>
        <td>' . ($booking['payment_reference'] ?? $booking['id']) . '</td>
    </tr>
    <tr>
        <td><b>Payment Status:</b></td>
        <td>' . $paymentStatus . '</td>
    </tr>
    <tr>
        <td><b>Booking Date:</b></td>
        <td>' . $bookingDate . '</td>
    </tr>
    <tr>
        <td><b>Amount:</b></td>
        <td>GHS ' . number_format($booking['amount'] ?? 0, 2) . '</td>
    </tr>
</table>';
$pdf->writeHTML($bookingInfo, true, false, false, false, '');

$pdf->Ln(5);

// Hostel Information
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Accommodation Information', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 10);

// Table for hostel info
$rating = isset($booking['rating']) ? floor($booking['rating']) : 0;
$ratingStars = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
$hostelInfo = '<table cellpadding="5" cellspacing="0" border="0">
    <tr>
        <td width="35%"><b>Hostel:</b></td>
        <td>' . $booking['hostel_name'] . '</td>
    </tr>
    <tr>
        <td><b>Location:</b></td>
        <td>' . $booking['location'] . '</td>
    </tr>
    <tr>
        <td><b>Room Number:</b></td>
        <td>' . $booking['room_number'] . '</td>
    </tr>
    <tr>
        <td><b>Room Type:</b></td>
        <td>' . ($booking['specification'] ?? 'Standard') . '</td>
    </tr>
    <tr>
        <td><b>Rating:</b></td>
        <td>' . $ratingStars . '</td>
    </tr>
</table>';
$pdf->writeHTML($hostelInfo, true, false, false, false, '');

$pdf->Ln(5);

// Terms & Conditions and footer
$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(0, 5, 'Thank you for choosing RoomVibe for your accommodation needs.', 0, 1, 'C');
$pdf->Cell(0, 5, 'For any questions or concerns, please contact our support team.', 0, 1, 'C');
$pdf->Cell(0, 5, 'Scan the QR code to view this receipt online.', 0, 1, 'C');

// Draw line
$pdf->Line(15, $pdf->GetY() + 5, 195, $pdf->GetY() + 5);

$pdf->SetY($pdf->GetY() + 10);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 5, 'RoomVibe - Making campus accommodation simple', 0, 1, 'C');

// Output the PDF
$pdf->Output('RoomVibe_Receipt_' . $booking_id . '.pdf', 'D'); // 'D' means download
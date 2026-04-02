<?php
session_start();

if ($_SESSION['user_type'] != 'student') {
    header('Location: index.php');
    exit;
}

require_once 'db_connect.php';
require_once 'vendor/autoload.php'; // Include the QR Code library

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Fetch student's RFID card number if not already in session
if (!isset($_SESSION['rfid_card_number'])) {
    $rfid_sql = "SELECT rfid_card_number FROM students WHERE id = ?";
    $stmt_rfid = $conn->prepare($rfid_sql);
    $stmt_rfid->bind_param("i", $_SESSION['id']);
    $stmt_rfid->execute();
    $stmt_rfid->bind_result($rfid_card_number);
    $stmt_rfid->fetch();
    $_SESSION['rfid_card_number'] = $rfid_card_number;
    $stmt_rfid->close();
}

// Generate QR code
$qrCode = QrCode::create($_SESSION['rfid_card_number'])
    ->setSize(200)
    ->setMargin(10);

$writer = new PngWriter();
$result = $writer->write($qrCode);
$imageData = $result->getString();

header('Content-Type: image/png');
header('Content-Disposition: attachment; filename="qr_code.png"');
echo $imageData;
?>

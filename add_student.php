<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['StudentID'], $_POST['StudentName'], $_POST['ClassID'], $_POST['RFIDCardNumber'], $_POST['Email'], $_POST['QRCode'])) {
        $studentID = $_POST['StudentID'];
        $studentName = $_POST['StudentName'];
        $classID = $_POST['ClassID'];
        $rfidCardNumber = $_POST['RFIDCardNumber'];
        $email = $_POST['Email'];
        $qrCode = $_POST['QRCode'];

        // Prepare the SQL statement to avoid SQL injection
        $stmt = $conn->prepare("INSERT INTO students (id, name, classID, rfid_card_number, email, qr_code) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $studentID, $studentName, $classID, $rfidCardNumber, $email, $qrCode);

        if ($stmt->execute()) {
            echo "Student added successfully.";
        } else {
            echo "Error adding student: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "All fields are required.";
    }
} else {
    echo "No data submitted.";
}

$conn->close();
?>

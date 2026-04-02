<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['StudentID'], $_POST['Date'])) {
        $studentID = $_POST['StudentID'];
        $date = $_POST['Date'];

        // Check if the record exists
        $checkStmt = $conn->prepare("SELECT * FROM Attendance WHERE StudentID = ? AND Date = ?");
        $checkStmt->bind_param("is", $studentID, $date);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            // Record exists, proceed to delete
            $stmt = $conn->prepare("DELETE FROM Attendance WHERE StudentID = ? AND Date = ?");
            $stmt->bind_param("is", $studentID, $date);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo "Record deleted successfully.";
                } else {
                    echo "Error: No record deleted.";
                }
            } else {
                echo "Error deleting record: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "No record found with the given Student ID and Date.";
        }

        $checkStmt->close();
    } else {
        echo "Student ID and Date are required.";
    }
} else {
    echo "No data submitted.";
}

$conn->close();
?>

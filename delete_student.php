<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['StudentID'])) {
        $studentID = $_POST['StudentID'];

        // Start transaction
        $conn->begin_transaction();

        try {
            // Prepare and execute the SQL statement to delete related attendance records
            $stmt = $conn->prepare("DELETE FROM attendance WHERE StudentID = ?");
            $stmt->bind_param("i", $studentID);
            $stmt->execute();
            $stmt->close();

            // Prepare and execute the SQL statement to delete the student
            $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
            $stmt->bind_param("i", $studentID);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "Student deleted successfully.";
                $conn->commit(); // Commit the transaction
            } else {
                echo "No student found with the given ID.";
                $conn->rollback(); // Rollback the transaction
            }

            $stmt->close();
        } catch (Exception $e) {
            $conn->rollback(); // Rollback the transaction in case of an error
            echo "Error deleting student: " . $e->getMessage();
        }
    } else {
        echo "Student ID is required.";
    }
} else {
    echo "No data submitted.";
}

$conn->close();
?>

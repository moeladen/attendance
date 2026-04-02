<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ensure all required fields are present
    if (isset($_POST['StudentID'], $_POST['ClassID'], $_POST['Date'], $_POST['Time'], $_POST['Status'], $_POST['TeacherID'])) {
        $studentID = $_POST['StudentID'];
        $classID = $_POST['ClassID'];
        $date = $_POST['Date'];
        $time = $_POST['Time'];
        $status = $_POST['Status'];
        $teacherID = $_POST['TeacherID'];
        $remarks = isset($_POST['Remarks']) ? $_POST['Remarks'] : null;

        // Prepare the SQL statement to avoid SQL injection
        $stmt = $conn->prepare("INSERT INTO Attendance (StudentID, ClassID, Date, Time, Status, TeacherID, Remarks) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssis", $studentID, $classID, $date, $time, $status, $teacherID, $remarks);

        if ($stmt->execute()) {
            echo "Record added successfully.";
        } else {
            echo "Error adding record: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "All fields are required.";
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Page</title>
    <link rel="stylesheet" href="t.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to Teacher Page</h1>

        <section class="teacher-info-section">
            <h2>Teacher's Info</h2>
            <p>Click the button below to access all your personal information:</p>
            <a href="teacher_info.php" class="btn">Teacher's Info</a>
        </section>

        <section class="attendance-management-section">
            <h2>Manage Attendance</h2>
            <p>Click the button below to alter attendance records:</p>
            <a href="alter_attendance.html" class="btn">Alter Attendance</a>
        </section>

        <section class="view-attendance-section">
            <h2>View Attendance</h2>
            <p>Click the button below to view attendance records:</p>
            <a href="view_attendance.html" class="btn">View Attendance</a>
        </section>
    </div>
</body>
</html>

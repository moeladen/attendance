<?php
session_start();
require_once 'db_connect.php';

// Fetch attendance records from the database
$sql = "SELECT Attendance.AttendanceID, Attendance.StudentID, Attendance.ClassID, Attendance.Date, Attendance.Time, Attendance.Status, Attendance.TeacherID, Attendance.Remarks FROM Attendance";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table class='table table-striped table-bordered'>
            <thead class='thead-dark'>
                <tr>
                    <th>Attendance ID</th>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Class ID</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Teacher ID</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>";

    while ($row = $result->fetch_assoc()) {
        $student_name = get_student_name($row['StudentID']);
        echo "<tr>
                <td>" . (isset($row['AttendanceID']) ? $row['AttendanceID'] : 'N/A') . "</td>
                <td>{$row['StudentID']}</td>
                <td>{$student_name}</td>
                <td>{$row['ClassID']}</td>
                <td>{$row['Date']}</td>
                <td>{$row['Time']}</td>
                <td>{$row['Status']}</td>
                <td>" . (isset($row['TeacherID']) ? $row['TeacherID'] : 'N/A') . "</td>
                <td>" . (isset($row['Remarks']) ? $row['Remarks'] : 'N/A') . "</td>
              </tr>";
    }

    echo "</tbody></table>";
} else {
    echo "<div class='alert alert-info'>No attendance records found.</div>";
}

$conn->close();
?>

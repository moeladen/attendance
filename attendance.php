<?php
session_start();

if ($_SESSION['user_type'] != 'student') {
    header('Location: index.php');
    exit;
}

require_once 'db_connect.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .message {
            text-align: center;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Attendance Records</h1>
        <?php
        if (isset($_SESSION['id'])) {
            $student_id = $_SESSION['id'];

            // Prepare the SQL statement to avoid SQL injection
            $stmt = $conn->prepare("SELECT * FROM attendance WHERE studentID = ?");
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo '<table>';
                echo '<tr><th>Date</th><th>Status</th></tr>';
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['Date']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['Status']) . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p class="message">No attendance records found.</p>';
            }

            $stmt->close();
        } else {
            echo '<p class="message">Error: Student ID not found in session.</p>';
        }

        $conn->close();
        ?>
    </div>
</body>
</html>

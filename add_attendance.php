<?php
session_start();
require_once 'db_connect.php';

$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['StudentID'], $_POST['ClassID'], $_POST['Date'], $_POST['Time'], $_POST['Status'], $_POST['TeacherID'], $_POST['Remarks'])) {
        $StudentID = $_POST['StudentID'];
        $ClassID = $_POST['ClassID'];
        $Date = $_POST['Date'];
        $Time = $_POST['Time'];
        $Status = $_POST['Status'];
        $TeacherID = $_POST['TeacherID'];
        $Remarks = $_POST['Remarks'];

        // Check if ClassID exists in the classes table
        $class_check_stmt = $conn->prepare("SELECT ClassID FROM classes WHERE ClassID = ?");
        $class_check_stmt->bind_param("s", $ClassID);
        $class_check_stmt->execute();
        $class_check_stmt->store_result();

        if ($class_check_stmt->num_rows > 0) {
            // Prepare the SQL statement to avoid SQL injection
            $stmt = $conn->prepare("INSERT INTO attendance (StudentID, ClassID, Date, Time, Status, TeacherID, Remarks) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $StudentID, $ClassID, $Date, $Time, $Status, $TeacherID, $Remarks);

            if ($stmt->execute()) {
                $message = "Attendance record added successfully.";
                $success = true;
            } else {
                $message = "Error adding attendance record: " . $stmt->error;
                $success = false;
            }

            $stmt->close();
        } else {
            $message = "Invalid ClassID. The class does not exist.";
            $success = false;
        }

        $class_check_stmt->close();
    } else {
        $message = "All fields are required.";
        $success = false;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .toast-success {
            background-color: #28a745 !important;
        }

        .toast-error {
            background-color: #dc3545 !important;
        }
    </style>
</head>
<body>
    <form method="post" action="">
        <input type="text" name="StudentID" placeholder="Student ID" required>
        <input type="text" name="ClassID" placeholder="Class ID" required>
        <input type="date" name="Date" required>
        <input type="time" name="Time" required>
        <input type="text" name="Status" placeholder="Status" required>
        <input type="text" name="TeacherID" placeholder="Teacher ID" required>
        <input type="text" name="Remarks" placeholder="Remarks" required>
        <button type="submit">Submit</button>
    </form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            <?php if (!empty($message)) { ?>
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "timeOut": "5000",
                    "onHidden": function() {
                        window.location.href = 'teacher.php';
                    }
                };
                <?php if ($success) { ?>
                    toastr.success("<?php echo $message; ?>", "Success");
                <?php } else { ?>
                    toastr.error("<?php echo $message; ?>", "Error");
                <?php } ?>
            <?php } ?>
        });
    </script>
</body>
</html>

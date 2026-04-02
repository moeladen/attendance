<?php
session_start();

if ($_SESSION['user_type'] != 'student') {
    header('Location: index.php');
    exit;
}

require_once 'db_connect.php';
require_once 'vendor/autoload.php'; // Ensure this path is correct
$_SESSION['id'] = 22030001;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Fetch student's RFID card number if not already in session
if (!isset($_SESSION['rfid_card_number'])) {
    $rfid_sql = "SELECT rfid_card_number FROM students WHERE id = ?";
    $stmt_rfid = $conn->prepare($rfid_sql);
    if (!$stmt_rfid) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $stmt_rfid->bind_param("i", $_SESSION['id']);
    $stmt_rfid->execute();
    $stmt_rfid->bind_result($rfid_card_number);
    if ($stmt_rfid->fetch()) {
        $_SESSION['rfid_card_number'] = $rfid_card_number;
    } else {
        die('Error: Student not found or RFID card number is null');
    }
    $stmt_rfid->close();
}

// Generate QR code
$rfid_card_number = $_SESSION['rfid_card_number'];
$qrCode = QrCode::create($rfid_card_number)
    ->setSize(200)
    ->setMargin(10);

$writer = new PngWriter();
$result = $writer->write($qrCode);
$qrCodeDataUri = $result->getDataUri();

// Fetch attendance records
$attendance_sql = "SELECT Time, status, date FROM attendance WHERE studentID = ? ORDER BY date DESC";
$stmt = $conn->prepare($attendance_sql);
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$attendance_result = $stmt->get_result();

// Count absences
$absence_sql = "SELECT COUNT(*) AS absences FROM attendance WHERE studentID = ? AND status = 'absent'";
$stmt_absence = $conn->prepare($absence_sql);
$stmt_absence->bind_param("i", $_SESSION['id']);
$stmt_absence->execute();
$absence_result = $stmt_absence->get_result();
$absences = $absence_result->fetch_assoc()['absences'];

// Calculate remaining absences
$max_absences = 10;
$remaining_absences = $max_absences - $absences;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Portal</title>
    <link rel="stylesheet" href="s.css">
</head>
<body>
    <header>
        <h1>Welcome to the Student Portal</h1>
    </header>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="attendance.php">Attendance</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container">
        <div class="header">
            <h2>Welcome, <?php echo $_SESSION['name']; ?></h2>
            <p>Student ID: <?php echo $_SESSION['id']; ?></p>
            <img src="<?php echo $qrCodeDataUri; ?>" alt="QR Code">
            <form action="download_qr.php" method="POST">
                <button type="submit">Download QR Code</button>
            </form>
        </div>

        <div class="announcements">
            <h3>Announcements</h3>
            <ul>
                <?php
                if ($announcements_result->num_rows > 0) {
                    while ($row = $announcements_result->fetch_assoc()) {
                        echo "<li><strong>" . $row['title'] . ":</strong> " . $row['content'] . "</li>";
                    }
                } else {
                    echo "<p>No announcements found.</p>";
                }
                ?>
            </ul>
        </div>

        <div class="events">
            <h3>Upcoming Events</h3>
            <ul>
                <?php
                if ($events_result->num_rows > 0) {
                    while ($row = $events_result->fetch_assoc()) {
                        echo "<li>" . $row['event_name'] . " - " . $row['event_date'] . "</li>";
                    }
                } else {
                    echo "<p>No upcoming events found.</p>";
                }
                ?>
            </ul>
        </div>

        <div class="courses">
            <h3>Your Courses</h3>
            <ul>
                <?php
                if ($courses_result->num_rows > 0) {
                    while ($row = $courses_result->fetch_assoc()) {
                        echo "<li>" . $row['course_name'] . " (Instructor: " . $row['instructor'] . ")</li>";
                    }
                } else {
                    echo "<p>No courses found.</p>";
                }
                ?>
            </ul>
        </div>

        <div class="grades">
            <h3>Your Grades</h3>
            <ul>
                <?php
                if ($grades_result->num_rows > 0) {
                    while ($row = $grades_result->fetch_assoc()) {
                        echo "<li>" . $row['course_name'] . ": " . $row['grade'] . "</li>";
                    }
                } else {
                    echo "<p>No grades found.</p>";
                }
                ?>
            </ul>
        </div>

        <div class="attendance-records">
            <h3>Attendance Records</h3>
            <?php
            if ($attendance_result->num_rows > 0) {
                echo "<ul>";
                while ($row = $attendance_result->fetch_assoc()) {
                    echo "<li>Date: " . $row['date'] . ", Status: " . $row['status'] . "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No attendance records found.</p>";
            }
            ?>
        </div>

        <div class="absence-count">
            <h3>Absence Count</h3>
            <p>Total Absences: <?php echo $absences; ?></p>
            <p>Remaining Absences: <?php echo $remaining_absences; ?></p>
        </div>

        <div class="feedback">
            <h3>Feedback</h3>
            <form action="submit_feedback.php" method="POST">
                <input type="text" name="subject" placeholder="Subject" required>
                <textarea name="message" placeholder="Your feedback" rows="4" required></textarea>
                <input type="submit" value="Submit">
            </form>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>

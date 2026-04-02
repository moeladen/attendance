<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php
    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check credentials and redirect based on user type
        if ($_POST['username'] == 'teacher' && $_POST['password'] == 'teacher_password') {
            $_SESSION['user_type'] = 'teacher';
            header('Location: teacher.php');
            exit();
        } elseif ($_POST['username'] == 'faculty' && $_POST['password'] == 'faculty_password') {
            $_SESSION['user_type'] = 'faculty';
            header('Location: faculty.php');
            exit();
        } elseif ($_POST['username'] == '22030001' && $_POST['password'] == 'pass') {
            $_SESSION['user_type'] = 'student';
            header('Location: student.php');
            exit();
        } else {
            echo "<p style='color: red; text-align: center;'>Invalid credentials</p>";
        }
    }
    ?>
    <div class="image-container">
        <img src="download.png" alt="Background Image">
    </div>
    <div class="login-container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <h1>Attendance Login</h1>
            <div class="input-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>

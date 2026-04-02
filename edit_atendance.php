<?php
session_start();

if ($_SESSION['user_type'] != 'faculty') {
    header('Location: index.php');
    exit;
}

// Your logic to edit attendance
?>

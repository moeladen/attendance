<?php
require_once 'db_connect.php';

$sql = "SELECT id FROM teachers WHERE rfid_card_number = 49 17 D0 83 OR 07 E6 99 52"; 
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo $row['id'];
} else {
    echo "";
}
?>
<?php
require_once 'db_connect.php';

// Use the IN operator for the correct SQL syntax when specifying multiple IDs
$sql = "SELECT rfid_card_number FROM teachers WHERE id IN (101, 100)"; 
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch and display all matching records
    while ($row = $result->fetch_assoc()) {
        echo $row['rfid_card_number'] . "";
    }
} else {
    echo "No results found.";
}
?>

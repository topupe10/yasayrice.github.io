<?php
// Include your database connection code
require_once 'db_connect.php';

// Get the variety from the request
$variety = $_GET['variety'];

// Fetch prices from the database based on the variety
$sql = "SELECT price_per_sack, price_per_kilo FROM milledgrains WHERE variety = ?";
$stmt = $connect->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $variety);
    $stmt->execute();
    $stmt->bind_result($pricePerSack, $pricePerKilo);
    $stmt->fetch();
    $stmt->close();

    // Return prices as JSON
    echo json_encode(array(
        'price_per_sack' => $pricePerSack,
        'price_per_kilo' => $pricePerKilo
    ));
} else {
    // Handle the case when the SQL statement couldn't be prepared
    echo json_encode(array('error' => 'Error preparing SQL statement'));
}

// Close the database connection
$connect->close();
?>

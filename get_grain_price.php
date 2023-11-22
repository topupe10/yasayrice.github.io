<?php
// Include your database connection
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grainType = $_POST['grainType'];

    // Fetch grain price from the database based on grain type
    $sql = "SELECT grain_price FROM grainprice WHERE grain_type = ?";
    $stmt = $connect->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $grainType);
        $stmt->execute();
        $stmt->bind_result($grainPrice);
        $stmt->fetch();
        $stmt->close();

        echo $grainPrice; // Return the grain price
    } else {
        echo "Error: " . $connect->error;
    }
}
?>
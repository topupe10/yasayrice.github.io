<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productType = $_POST['productType'];

    // Query the available_quantity for the selected grain_type
    $sql = "SELECT SUM(available_quantity) FROM grainsstock WHERE grain_type = ? GROUP BY grain_type";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("s", $productType);
    $stmt->execute();
    $stmt->bind_result($availableQuantity);
    $stmt->fetch();
    $stmt->close();

    echo $availableQuantity;
}
?>

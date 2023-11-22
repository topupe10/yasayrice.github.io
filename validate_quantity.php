<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grainType = $_POST['grainType'];
    $enteredQuantity = $_POST['quantity'];

    // Fetch the available quantity for the selected grain type from your database
    $sql = "SELECT available_quantity FROM grainsstock WHERE grain_type = ?";
    $stmt = $connect->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $grainType);
        $stmt->execute();
        $stmt->bind_result($availableQuantity);
        $stmt->fetch();
        $stmt->close();

        if ($enteredQuantity <= $availableQuantity) {
            echo 'valid';
        } else {
            echo 'invalid';
        }
    }
}
?>
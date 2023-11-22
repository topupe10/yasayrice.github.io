<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grainType = $_POST['grainType'];

    $sql = "SELECT milling_fee FROM millingfees WHERE grain_type = ?";
    $stmt = $connect->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $grainType);
        $stmt->execute();
        $stmt->bind_result($millingFee);
        $stmt->fetch();
        $stmt->close();

        echo $millingFee;
    } else {
        echo "Error: " . $connect->error;
    }
}
?>

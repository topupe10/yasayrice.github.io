<?php
// Assuming you have a database connection in db_connect.php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['productType']) && isset($_POST['millingVariety']) && isset($_POST['scaleType'])) {
    $productType = $_POST['productType'];
    $millingVariety = $_POST['millingVariety'];
    $scaleType = $_POST['scaleType'];

    if ($connect->connect_error) {
        die("Connection failed: " . $connect->connect_error);
    }

    // Choose the appropriate column based on the scale type
    $stockColumn = ($scaleType === 'Sack') ? 'available_stock_sack' : 'available_stock';

    // Query to get available stock based on productType, millingVariety, and scaleType
    $sql = "SELECT `$stockColumn` FROM `milledgrains` WHERE `grain_type` = ? AND `variety` = ?";
    $stmt = $connect->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ss", $productType, $millingVariety);
        $stmt->execute();
        $stmt->bind_result($availableStock);
        $stmt->fetch();
        $stmt->close();

        // Return the available stock as a response
        echo $availableStock;
    } else {
        // Handle the case when the SQL statement couldn't be prepared
        echo "Error: " . $connect->error;
    }

    $connect->close();
} else {
    // Handle invalid or missing parameters
    echo "Invalid parameters";
}
?>

<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'db_connect.php';

if (isset($_POST['transactionId']) && isset($_POST['newQuantity'])) {
    $transactionId = $_POST['transactionId'];
    $newQuantity = $_POST['newQuantity'];

    // Get the current date in the format "YYYY-MM-DD"
    $dateCompleted = date("Y-m-d");

    // Update the quantity, status, and date_completed in the database
    $sqlUpdateTransaction = "UPDATE millingtransactions SET quantity = ?, status = 'Completed', date_completed = ? WHERE transaction_id = ?";
    $stmtUpdateTransaction = $connect->prepare($sqlUpdateTransaction);

    if ($stmtUpdateTransaction) {
        $stmtUpdateTransaction->bind_param("iss", $newQuantity, $dateCompleted, $transactionId);
        $stmtUpdateTransaction->execute();
        $stmtUpdateTransaction->close();

        // Check if the status is "Completed" before updating milledgrains
        $sqlCheckStatus = "SELECT status, grain_type, milling_variety FROM millingtransactions WHERE transaction_id = ?";
        $stmtCheckStatus = $connect->prepare($sqlCheckStatus);

        if ($stmtCheckStatus) {
            $stmtCheckStatus->bind_param("i", $transactionId);
            $stmtCheckStatus->execute();
            $stmtCheckStatus->bind_result($status, $productType, $millingVariety);
            $stmtCheckStatus->fetch();
            $stmtCheckStatus->close();

            if ($status === "Completed") {
                // Update the available_stock in the "milledgrains" table
                $sqlUpdateMilledGrains = "UPDATE milledgrains SET available_stock = available_stock + ? WHERE grain_type = ? AND variety = ?";
                $stmtUpdateMilledGrains = $connect->prepare($sqlUpdateMilledGrains);

                if ($stmtUpdateMilledGrains) {
                    $stmtUpdateMilledGrains->bind_param("iss", $newQuantity, $productType, $millingVariety);
                    $stmtUpdateMilledGrains->execute();
                    $stmtUpdateMilledGrains->close();
                }
            }
        }
        echo "Update successful!";
    } else {
        echo "Update failed!";
    }
} else {
    echo "Invalid parameters!";
}
?>

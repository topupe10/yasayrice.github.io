<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'db_connect.php';

    $transactionId = $_POST['transaction_id'];
    $replacementReason = $_POST['reason'];

    // Perform the update query to set the status to 'Replaced' and store the replacement reason
    $sqlUpdateStatus = "UPDATE sellingtransactions SET replacement_status = 'Replaced', replacement_reason = ? WHERE transaction_id = ?";
    $stmtUpdateStatus = $connect->prepare($sqlUpdateStatus);

    if ($stmtUpdateStatus) {
        $stmtUpdateStatus->bind_param("si", $replacementReason, $transactionId);
        $stmtUpdateStatus->execute();
        $stmtUpdateStatus->close();

        // Query the database to fetch the transaction details based on $transactionId
        $sqlFetchTransaction = "SELECT grain_type, milling_variety, quantity, scale_type FROM sellingtransactions WHERE transaction_id = ?";
        
        $stmtFetchTransaction = $connect->prepare($sqlFetchTransaction);
        
        if ($stmtFetchTransaction) {
            $stmtFetchTransaction->bind_param("i", $transactionId);
            $stmtFetchTransaction->execute();
            $stmtFetchTransaction->bind_result($grainType, $millingVariety, $quantity, $scaleType);

            // Fetch the result
            $stmtFetchTransaction->fetch();

            // Close the fetch statement
            $stmtFetchTransaction->close();

            // Update milledgrains available_stock based on grain_type and variety
            $sqlUpdateStock = "UPDATE milledgrains SET available_stock = available_stock - ? WHERE grain_type = ? AND variety = ?";
            $stmtUpdateStock = $connect->prepare($sqlUpdateStock);

            if ($stmtUpdateStock) {
                $stmtUpdateStock->bind_param("iss", $quantity, $grainType, $millingVariety);
                $stmtUpdateStock->execute();
                $stmtUpdateStock->close();

                // Perform other necessary operations

                // Return success
                echo json_encode(['success' => true]);
            } else {
                // Return an error message
                echo json_encode(['error' => 'Error updating stock: ' . $connect->error]);
            }
        } else {
            // Return an error message
            echo json_encode(['error' => 'Error fetching transaction details: ' . $connect->error]);
        }
    } else {
        // Return an error message
        echo json_encode(['error' => 'Error updating status: ' . $connect->error]);
    }

    // Close the database connection
    $connect->close();
}
?>

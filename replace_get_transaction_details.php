<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'db_connect.php';

    $transactionId = $_POST['transaction_id'];

    // Query the database to fetch the transaction details based on $transactionId
    $sql = "SELECT grain_type, milling_variety, quantity, scale_type FROM sellingtransactions WHERE transaction_id = ?";
    
    $stmt = $connect->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $transactionId);
        $stmt->execute();
        $stmt->bind_result($grainType, $millingVariety, $quantity, $scaleType);

        // Fetch the result
        $stmt->fetch();

        // Return the details as JSON
        echo json_encode([
            'grain_type' => $grainType,
            'milling_variety' => $millingVariety,
            'quantity' => $quantity,
            'scale_type' => $scaleType,
        ]);

        $stmt->close();
    } else {
        // Return an error message
        echo json_encode(['error' => 'Error: ' . $connect->error]);
    }

    // Close the database connection
    $connect->close();
}
?>

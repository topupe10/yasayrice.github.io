<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure you have a database connection in this script
    require_once 'db_connect.php';

    // Get the transaction ID from the POST data
    $transactionId = $_POST['transactionId'];

    // Initialize an empty array to store the transaction details
    $details = [];

    // Query the database to fetch the transaction details based on $transactionId
    $sql = "SELECT transaction_id, customer_id, transaction_date, grain_type, milling_variety, scale_type, quantity, delivery_method, delivery_date, status
            FROM sellingtransactions
            WHERE transaction_id = ?";
    
    $stmt = $connect->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $transactionId);
        $stmt->execute();
        $stmt->bind_result($transaction_id, $customer_id, $transaction_date, $grain_type, $milling_variety, $scale_type, $quantity, $delivery_method, $delivery_date, $status);

        // Fetch the result
        $stmt->fetch();

        // Populate the details array
        $details = [
            'transaction_id' => $transaction_id,
            'customer_id' => $customer_id,
            'transaction_date' => $transaction_date,
            'grain_type' => $grain_type,
            'milling_variety' => $milling_variety,
            'scale_type' => $scale_type,
            'quantity' => $quantity,
            'delivery_method' => $delivery_method,
            'delivery_date' => $delivery_date,
            'status' => $status,
        ];

        $stmt->close();
    }

    // Close the database connection
    $connect->close();

    // Return the details as JSON
    echo json_encode($details);
}
?>

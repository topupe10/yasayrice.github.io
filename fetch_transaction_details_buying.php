<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure you have a database connection in this script
    require_once 'db_connect.php';

    // Get the transaction ID from the POST data
    $transactionId = $_POST['transactionId'];

    // Initialize an empty array to store the transaction details
    $details = [];

    // Query the database to fetch the transaction details based on $transactionId
    $sql = "SELECT invoice_number, transaction_id, customer_id, transaction_date, grain_type, quantity, total_cost
            FROM buyingtransactions
            WHERE transaction_id = ?";
    
    $stmt = $connect->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $transactionId);
        $stmt->execute();
        $stmt->bind_result($invoice_number, $transaction_id, $customer_id, $transaction_date, $grain_type,  $quantity, $total_cost);

        // Fetch the result
        $stmt->fetch();

        // Populate the details array
        $details = [
            'invoice_number' => $invoice_number,
            'transaction_id' => $transaction_id,
            'customer_id' => $customer_id,
            'transaction_date' => $transaction_date,
            'grain_type' => $grain_type,
            'quantity' => $quantity,
            'total_cost' => '₱' . number_format($total_cost, 2, '.', ',')
        ];

        $stmt->close();
    }

    // Close the database connection
    $connect->close();

    // Return the details as JSON
    echo json_encode($details);
}
?>

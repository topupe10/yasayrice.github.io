<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure you have a database connection in this script
    require_once 'db_connect.php';

    // Get the transaction ID from the POST data
    $transactionId = $_POST['transactionId'];

    // Initialize an empty array to store the transaction details
    $details = [];

    // Query the database to fetch the transaction details based on $transactionId
    $sql = "SELECT transaction_id, customer_id, transaction_date, grain_type, milling_variety, scale_type, quantity, total_price, delivery_method, delivery_date, status
            FROM sellingtransactions
            WHERE transaction_id = ?";
    
    $stmt = $connect->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $transactionId);
        $stmt->execute();
        $stmt->bind_result($transaction_id, $customer_id, $transaction_date, $grain_type, $milling_variety, $scale_type, $quantity, $total_price, $delivery_method, $delivery_date, $status);

        // Fetch the result
        $stmt->fetch();

        // Map the status value to the desired display value
        $displayStatus = ($status === 'Complete') ? 'Done' : (($status === 'Ongoing') ? 'Redry' : $status);

        // Populate the details array with the modified status
        $details = [
            'Transaction #' => $transaction_id,
            'Customer ID' => $customer_id,
            'Transaction Date' => $transaction_date,
            'Grain Type' => $grain_type,
            'Milling Variety' => $milling_variety,
            'Scale Type' => $scale_type,
            'Quantity' => $quantity,
            'Distribution' => $delivery_method,
            'Delivery Date' => $delivery_date,
            'Status' => $displayStatus, // Use the modified status here
            'Total Price' => '₱' . number_format($total_price, 2, '.', ',')
        ];
        

        $stmt->close();
    }

    // Close the database connection
    $connect->close();

    // Return the details as JSON
    echo json_encode($details);
}
?>

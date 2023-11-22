<?php
require_once 'db_connect.php';

if (isset($_POST['transactionId'])) {
    $transactionId = $_POST['transactionId'];

    // Fetch transaction details from both tables
    $sql = "SELECT * FROM (
                SELECT * FROM millingtransactions WHERE transaction_id = $transactionId) AS combined";

    $result = $connect->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Display transaction details in a table
        echo "<table class='table table-bordered'>";
        echo "<tr><th>Transaction #</th><td>" . $row['transaction_id'] . "</td></tr>";
        echo "<tr><th>Invoice #</th><td>" . $row['invoice_number'] . "</td></tr>";
        echo "<tr><th>Customer ID</th><td>" . ($row['customer_id'] ? $row['customer_id'] : 'N/A') . "</td></tr>";
        echo "<tr><th>Transaction Date</th><td>" . ($row['transaction_date'] ? $row['transaction_date'] : 'N/A') . "</td></tr>";
        echo "<tr><th>Grain Type</th><td>" . ($row['grain_type'] ? $row['grain_type'] : 'N/A') . "</td></tr>";
        echo "<tr><th>Milling Variety</th><td>" . ($row['milling_variety'] ? $row['milling_variety'] : 'N/A') . "</td></tr>";
        echo "<tr><th>Quantity</th><td>" . ($row['quantity'] ? $row['quantity'] : 'N/A') . "</td></tr>";
        echo "<tr><th>Delivery Method</th><td>" . ($row['delivery_method'] ? $row['delivery_method'] : 'N/A') . "</td></tr>";
        echo "<tr><th>Delivery Date</th><td>" . ($row['delivery_date'] ? $row['delivery_date'] : 'N/A') . "</td></tr>";
        echo "<tr><th>Date Completed</th><td>" . ($row['date_completed'] ? $row['date_completed'] : 'N/A') . "</td></tr>";
        echo "<tr><th>Status</th><td>" . ($row['status'] ? $row['status'] : 'N/A') . "</td></tr>";
        echo "<tr><th>Total Fee</th><td>" . ($row['total_fee'] ? '₱' . number_format($row['total_fee'], 2) : 'N/A') . "</td></tr>";
        echo "</table>";
    } else {
        echo "No data found for the given transaction ID.";
    }
} else {
    echo "Transaction ID not provided.";
}
?>

<?php
// Include your database connection here (e.g., db_connect.php)
require_once 'db_connect.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);


// Check if the POST request contains a 'transactionId' parameter
if (isset($_POST['transactionId'])) {
    $transactionId = $_POST['transactionId'];

    // Create an associative array to store the result
    $result = array();

    // Prepare the SQL statement using parameter binding
    $sql = "SELECT
    mt.transaction_id,
    mt.customer_id,
    mt.transaction_date,
    mt.delivery_method,
    mt.delivery_date,
    mt.status,
    g.grain_type,
    g.milling_variety,
    g.quantity
FROM millingtransactions AS mt
INNER JOIN grains AS g ON mt.transaction_id = g.transaction_id
WHERE mt.transaction_id = ?";


    // Use prepared statements to prevent SQL injection
    if ($stmt = $connect->prepare($sql)) {
        $stmt->bind_param("i", $transactionId);
        if ($stmt->execute()) {
            $queryResult = $stmt->get_result();
            if ($queryResult->num_rows > 0) {
                $result = $queryResult->fetch_assoc();
            } else {
                // Handle the case where no data is found
                $result['error'] = 'No data found for the given transaction ID';
            }
            $stmt->close();
        } else {
            $result['error'] = 'Error executing the query: ' . $stmt->error;
        }
    } else {
        $result['error'] = 'Error preparing the query: ' . $connect->error;
    }

    // Send the result as JSON
    echo json_encode($result);
} else {
    // Handle cases where 'transactionId' is not provided
    echo json_encode(array('error' => 'Transaction ID not provided.'));
}
?>


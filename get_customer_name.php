<?php
// Include your database connection file
require_once 'db_connect.php';

// Check if the request contains the customer_id parameter
if (isset($_GET['customer_id'])) {
    $customer_id = $_GET['customer_id'];

    // Query to retrieve the customer's name based on the customer_id
    $sql = "SELECT name FROM customers WHERE customer_id = ?";
    $stmt = $connect->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $stmt->bind_result($customer_name);

        // Fetch the result
        if ($stmt->fetch()) {
            // Output the customer's name
            echo $customer_name;
        } else {
            // Customer not found
            echo 'Customer not found';
        }

        $stmt->close();
    } else {
        // Error in preparing the statement
        echo 'Error preparing statement';
    }

    // Close the database connection
    $connect->close();
} else {
    // Parameter not provided
    echo 'Customer ID parameter not provided';
}
?>

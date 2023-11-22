<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['user_authenticated']) || !$_SESSION['user_authenticated']) {
    http_response_code(403);
    exit("Unauthorized");
}

require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transactionId = $_POST['transaction_id'];

    // Update the redry_status to 'redry'
    $sql = "UPDATE sellingtransactions SET redry_status = 'Ongoing', status = 'Ongoing' WHERE transaction_id = ?";
    $stmt = $connect->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $transactionId);
        $stmt->execute();
        $stmt->close();
        echo "success";
    } else {
        http_response_code(500);
        echo "Error: " . $connect->error;
    }

    // Close the database connection
    $connect->close();
} else {
    http_response_code(400);
    echo "Bad Request";
}
?>

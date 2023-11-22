<?php
// Include the database connection file
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the grain type, milling variety, and scale type from the AJAX request
    $grainType = $_POST['grainType'];
    $millingVariety = $_POST['millingVariety'];
    $scaleType = $_POST['scaleType'];

    // Fetch the price from the milledgrains table
    $sql = "SELECT ";
    
    // Determine the column to select based on the scale type
    if ($scaleType === 'Sack') {
        $sql .= "price_per_sack AS price ";
    } elseif ($scaleType === 'Kilo') {
        $sql .= "price_per_kilo AS price ";
    } else {
        // Handle the case where the scale type is not valid
        echo "Invalid scale type selected";
        exit();
    }

    $sql .= "FROM milledgrains WHERE grain_type = ? AND variety = ?";
    
    $stmt = $connect->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ss", $grainType, $millingVariety);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo $row['price'];
        } else {
            // Handle the case where no matching record is found
            echo "Price not found for the specified grain type and variety";
        }

        $stmt->close();
    } else {
        // Handle the case when the SQL statement couldn't be prepared
        echo "Error: " . $connect->error;
    }

    // Close the database connection
    $connect->close();
} else {
    // Handle the case where the request method is not POST
    echo "Invalid request method";
}
?>

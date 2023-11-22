<?php
require_once 'db_connect.php';
// Function to generate an invoice number with the current year and random numbers
function generateInvoiceNumber() {
    $currentYear = date('Y');
    $randomNumbers = mt_rand(100000, 999999); // You can adjust the range as needed
    return $currentYear . $randomNumbers;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $customerName = $_POST['customerName'];
    $contactNumber = $_POST['contactNumber'];

    // Insert data into the "Customers" table
    $sql = "INSERT INTO Customers (name, contact_number) VALUES (?, ?)";
    $stmt = $connect->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ss", $customerName, $contactNumber);
        $stmt->execute();
        $customerId = $stmt->insert_id; // Get the auto-generated customer ID
        $stmt->close();

        // Insert data into the "QuickMillingTransactions" table
        $productType = $_POST['productType'];
        $millingVariety = $_POST['millingVariety'];
        $quantity = $_POST['quantity'];
        $transactionDate = date('Y-m-d'); // Get the current date

        $status = "Ongoing"; // You can set the status to an initial value

        $invoiceNumber = generateInvoiceNumber();

        $sql = "INSERT INTO millingtransactions (invoice_number, customer_id, grain_type, milling_variety, quantity, transaction_date, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connect->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("iississ", $invoiceNumber, $customerId, $productType, $millingVariety, $quantity, $transactionDate, $status);
            $stmt->execute();
            $stmt->close();

            // Deduct the quantity from the grainsstock table based on the older date or distribute it to multiple records
            $sqlDeductStock = "UPDATE grainsstock
                SET available_quantity = 
                    CASE
                        WHEN (SELECT SUM(available_quantity) FROM grainsstock WHERE grain_type = ?) >= ? THEN
                            available_quantity - ?
                        ELSE
                            available_quantity - (SELECT SUM(available_quantity) FROM grainsstock WHERE grain_type = ?)
                    END
                WHERE grain_type = ?
                ORDER BY created_at ASC
                LIMIT 1;";

            $stmtDeductStock = $connect->prepare($sqlDeductStock);

            if ($stmtDeductStock) {
                $stmtDeductStock->bind_param("siiis", $productType, $quantity, $quantity, $productType, $productType);
                $stmtDeductStock->execute();
                $stmtDeductStock->close();
            }
        }

        // Close the database connection
        $connect->close();

        // Redirect to a success page or show a success message
        header("Location: milling.php");
    } else {
        // Handle the case when the SQL statement couldn't be prepared
        echo "Error: " . $connect->error;
    }
}
?>

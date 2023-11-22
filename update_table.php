<?php
require_once 'db_connect.php';

// Get the start and end dates from the GET parameters
$startDate = $_GET['start_date'];
$endDate = $_GET['end_date'];

// Use prepared statements to prevent SQL injection
$millingSql = "SELECT mt.transaction_id, c.name AS customer_name, c.contact_number, c.address, mt.transaction_date, mt.delivery_method, mt.delivery_date, mt.status
                FROM millingtransactions mt
                INNER JOIN customers c ON mt.customer_id = c.customer_id
                WHERE mt.transaction_date BETWEEN ? AND ?";

// Assuming you have a similar SQL query for quick milling transactions
$quickMillingSql = "SELECT qmt.transaction_id, c.name AS customer_name, c.contact_number, c.address, qmt.transaction_date, qmt.grain_type, qmt.milling_variety, qmt.quantity, qmt.delivery_method, qmt.delivery_date, qmt.status
                    FROM quick_milling_transactions qmt
                    INNER JOIN customers c ON qmt.customer_id = c.customer_id
                    WHERE qmt.transaction_date BETWEEN ? AND ?";

// Assuming you have a similar SQL query for selling transactions
$sellingSql = "SELECT st.transaction_id, c.name AS customer_name, c.contact_number, c.address, st.transaction_date, st.grain_type, st.milling_variety, st.scale_type, st.quantity, st.delivery_method, st.delivery_date, st.status
                FROM sellingtransactions st
                INNER JOIN customers c ON st.customer_id = c.customer_id
                WHERE st.transaction_date BETWEEN ? AND ?";

// Assuming you have a similar SQL query for buying transactions
$buyingSql = "SELECT bt.transaction_id, c.name AS customer_name, c.contact_number, c.address, bt.transaction_date, bt.grain_type, bt.quantity, bt.status
                FROM buyingtransactions bt
                INNER JOIN customers c ON bt.customer_id = c.customer_id
                WHERE bt.transaction_date BETWEEN ? AND ?";

// Prepare and bind parameters
$stmt = $connect->prepare($millingSql);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

// Generate the HTML for the updated milling table
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["transaction_id"] . "</td>";
        echo "<td>" . $row["customer_name"] . "</td>";
        echo "<td>" . $row["contact_number"] . "</td>";
        echo "<td>" . $row["address"] . "</td>";
        echo "<td>" . $row["transaction_date"] . "</td>";
        echo "<td>" . $row["delivery_method"] . "</td>";
        echo "<td>" . $row["delivery_date"] . "</td>";
        echo "<td>" . $row["status"] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>No milling transactions found.</td></tr>";
}

// Close the statement and database connection
$stmt->close();
$connect->close();
?>

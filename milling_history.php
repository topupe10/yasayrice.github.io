<?php
session_start();

if (!isset($_SESSION['user_authenticated']) || !$_SESSION['user_authenticated']) {
    // User is not authenticated, redirect to the login page
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';
$activePage = 'millinghistory'; // Set the active page to 'selling'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $customerName = $_POST['customerName'];
    $contactNumber = $_POST['contactNumber'];
    $address = $_POST['address'];
    $dispose = $_POST['dispose'];
    $deliveryDate = $_POST['deliveryDate'];

    if ($connect->connect_error) {
        die("Connection failed: " . $connect->connect_error);
    }

    $sql = "INSERT INTO Customers (name, contact_number, address) VALUES (?, ?, ?)";
    $stmt = $connect->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sss", $customerName, $contactNumber, $address);
        $stmt->execute();
        $customerId = $stmt->insert_id; // Get the auto-generated customer ID
        $stmt->close();

        // Insert data into the "millingtransactions" table
        $sql = "INSERT INTO millingtransactions (customer_id, transaction_date, delivery_method, delivery_date, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $connect->prepare($sql);

        if ($stmt) {
            $transactionDate = date('Y-m-d'); // Assuming the transaction date is the current date
            $status = "Ongoing"; // You can change the status as needed

            $stmt->bind_param("issss", $customerId, $transactionDate, $dispose, $deliveryDate, $status);
            $stmt->execute();
            $transactionId = $stmt->insert_id; // Get the auto-generated transaction ID
            $stmt->close();

            // Handle multiple grains
            $quantities = $_POST['quantity'];
            $productTypes = $_POST['productType'];
            $millingVarieties = $_POST['millingVariety'];

            // Insert data into the "grains" table for each grain
            $sql = "INSERT INTO grains (transaction_id, grain_type, milling_variety, quantity) VALUES (?, ?, ?, ?)";
            $stmt = $connect->prepare($sql);

            if ($stmt) {
                for ($i = 0; $i < count($quantities); $i++) {
                    $stmt->bind_param("isss", $transactionId, $productTypes[$i], $millingVarieties[$i], $quantities[$i]);
                    $stmt->execute();
                }

                $stmt->close();
            }

            // Close the database connection
            $connect->close();

            // Redirect to a success page or show a success message
            header("Location: milling.php");
            exit();
        } else {
            // Handle the case when the SQL statement couldn't be prepared
            echo "Error: " . $connect->error;
        }
    }
}

// Check if the form is submitted with date values
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['start_date']) && isset($_GET['end_date'])) {
    // Get the start and end date values from the form
    $startDate = $_GET['start_date'];
    $endDate = $_GET['end_date'];

    // Add a WHERE clause to filter transactions within the specified date range
    $sql = "SELECT transaction_id, Customers.name AS customer_name, transaction_date, delivery_method, 'Customer' AS user_type
            FROM millingtransactions
            JOIN Customers ON millingtransactions.customer_id = Customers.customer_id
            WHERE status = 'Completed'
            AND transaction_date BETWEEN '$startDate' AND '$endDate'";
} else {
    // If the form is not submitted or the date fields are empty, retrieve all transactions
    $sql = "SELECT transaction_id, Customers.name AS customer_name, transaction_date, delivery_method, 'Customer' AS user_type
            FROM millingtransactions
            JOIN Customers ON millingtransactions.customer_id = Customers.customer_id
            WHERE status = 'Completed'";
}

// Execute the SQL query
$result = $connect->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Milling Services | Yasay Rice & Corn Milling Management Information System</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="stylesheet/millingstyle.css">
    <link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
  integrity="sha384-..."
  crossorigin="anonymous"
/>
</head>
<body>
<header>
<div class="logo">
    <img src="logo.png" alt="Company Logo" class="logo-image">
    YASAY RICE & CORN MILL
</div>
    <nav>
        <ul>
            <li <?php if ($activePage == 'dashboard') echo 'class="active"'; ?>>
                <a href="stockman_dashboard.php">
                    <i class="fas fa-chart-bar"></i> Dashboard
                </a>
            </li>
            <li <?php if ($activePage == 'ongoingmilling') echo 'class="active"'; ?>>
                <a href="ongoing_milling.php">
                    <i class="fa-solid fa-wheat-awn"></i> Ongoing Milling
                </a>
            </li>
            <li <?php if ($activePage == 'millinghistory') echo 'class="active"'; ?>>
                <a href="milling_history.php">
                    <i class="fas fa-list-alt"></i> Milling History
                </a>
            </li>
            <li <?php if ($activePage == 'inventory') echo 'class="active"'; ?>>
                <a href="stockman_inventory.php">
                    <i class="fas fa-boxes"></i> Inventory
                </a>
            </li>
            <li>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </li>
        </ul>
    </nav>
</header>
    <div class="content">
        <ol class="breadcrumb">
            <li><a href="dashboard.php">Dashboard</a></li>
            /
            <li class="active">Milling Services</li>
        </ol>
<!-- Add a new modal for displaying transaction details -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" role="dialog" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailsModalLabel">Transaction Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detailsModalBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
        <div class="container-box">
        <form method="GET" class="date-range-form">
            <div class="form-group">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" required>
            </div>
            <div class="form-group">
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" required>
            </div>
            <button type="submit" class="btn btn-info">Apply Filters</button>
            <a href="report.php" class="btn btn-danger">Reset Filters</a>
        </form>
<?php
$sql = "SELECT transaction_id, Customers.name AS customer_name, transaction_date, delivery_method, 'Customer' AS user_type
        FROM millingtransactions
        JOIN Customers ON millingtransactions.customer_id = Customers.customer_id
        WHERE status = 'Completed'";
$result = $connect->query($sql);
?>
<div class="container-box1">
<h1>Milling History</h1>
<table class="table special-table1">
    <thead>
        <tr>
            <th>Name</th>
            <th>Mill Date</th>
            <th>User</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Check if there are rows in the result
        if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["customer_name"] . "</td>";
                echo "<td>" . $row["transaction_date"] . "</td>";
                echo "<td>" . $row["user_type"] . "</td>";
                if ($row["user_type"] == "Customer") {
                echo "<td><button type='button' class='btn btn-info view-details-btn' data-toggle='modal' data-target='#viewDetailsModal' data-transaction-id='" . $row["transaction_id"] . "'>View Details</button></td></td>";
                }
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No ongoing millings</td></tr>";
        }
        ?>
    </tbody>
</table>
</div>
</div>
<script>
    $(document).ready(function () {
        $('.view-details-btn').click(function () {
            var transactionId = $(this).data('transaction-id');

            // Send an AJAX request to fetch transaction details
            $.ajax({
                url: 'get_transaction_details.php', // Create a separate PHP file to handle the request
                type: 'POST',
                data: {
                    transactionId: transactionId
                },
                success: function (response) {
                    // Display the details in the modal body
                    $('#detailsModalBody').html(response);

                    // Show the modal
                    $('#viewDetailsModal').modal('show');
                },
                error: function (error) {
                    console.error(error);
                }
            });
        });
    });
</script>
    <!-- Bootstrap JavaScript and jQuery should be included after your content -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

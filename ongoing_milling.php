<?php
session_start();

// Include necessary files
require_once 'db_connect.php';

if (!isset($_SESSION['user_authenticated']) || !$_SESSION['user_authenticated']) {
    // User is not authenticated, redirect to the login page
    header("Location: login.php");
    exit();
}

$activePage = 'ongoingmilling'; // Set the active page to 'ongoingmilling'


?>

<!DOCTYPE html>
<html>
<head>
    <title>Milling Services | Yasay Rice & Corn Milling Management Information System</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="stylesheet/stockmanmillingstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha384-..." crossorigin="anonymous"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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
<div class="modal fade" id="updateQuantityModal" tabindex="-1" role="dialog" aria-labelledby="updateQuantityModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateQuantityModalLabel">Update Milled Grains Quantity</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Enter how many kilograms are milled:</strong></p>
                <input type="number" id="newQuantityInput" class="form-control" placeholder="Enter quantity" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="updateQuantityBtn">Update</button>
            </div>
        </div>
    </div>
</div>  
<div class="content">
    <ol class="breadcrumb">
        <li><a href="dashboard.php">Dashboard</a></li>
        /
        <li class="active">Milling Services</li>
    </ol>
    <div class="container-box">
        <h1>Milling Services</h1>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <table class="table table-condensed special-table">
                    <thead>
                    <tr style="text-align:center;">
                        <th>Grains Classification</th>
                        <th>Grains Queued for Milling</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
// Fetch data from the millingfees table
$sql = "SELECT grain_type FROM millingfees";
$result = $connect->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Sum the quantity for each grain type in both tables
        $sqlSumQuantity = "
            SELECT SUM(quantity) AS total_quantity FROM (
                SELECT quantity FROM millingtransactions WHERE grain_type = '{$row['grain_type']}' AND status = 'Ongoing') AS combined";

        $resultSumQuantity = $connect->query($sqlSumQuantity);
        $totalQuantity = ($resultSumQuantity->num_rows > 0) ? $resultSumQuantity->fetch_assoc()['total_quantity'] : 0;

        echo "<tr>";
        echo "<td style='text-align:center;'>" . $row['grain_type'] . "</td>";
        echo "<td style='text-align:center;'>" . $totalQuantity . " kg</td>";
        echo "</tr>";
    }
}
?>

                    </tbody>
                </table>
            </div>
        </div>
    <br>
<p>Today's ongoing millings:</p>
<?php
// Fetch data for ongoing milling transactions for today
$currentDate = date("Y-m-d"); // Get the current date in the format "YYYY-MM-DD"

$sqlToday = "
    SELECT transaction_id, Customers.name AS customer_name, transaction_date, status, delivery_method, 'Customer' AS user_type,
    grain_type, milling_variety, quantity
    FROM millingtransactions
    JOIN Customers ON millingtransactions.customer_id = Customers.customer_id
    WHERE status = 'Ongoing' AND DATE(transaction_date) = '$currentDate'";

$resultToday = $connect->query($sqlToday);
?>
<div class="container-box1">
    <table class="table special-table1">
        <thead>
            <tr>
                <th>Name</th>
                <th>Mill Date</th>
                <th>Grain</th>
                <th>Variety</th>
                <th>Quantity</th>
                <th>User</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Check if there are rows in the result for today
            if ($resultToday->num_rows > 0) {
                // Output data of each row
                $firstRow = true;

                while ($row = $resultToday->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["customer_name"] . "</td>";
                    echo "<td>" . $row["transaction_date"] . "</td>";
                    echo "<td>" . $row["grain_type"] . "</td>";
                    echo "<td>" . $row["milling_variety"] . "</td>";
                    echo "<td>" . $row["quantity"] . "</td>";
                    echo "<td>" . $row["user_type"] . "</td>";

                    // Display "Update Status" button only for the first row
                    if ($firstRow) {
                        echo "<td><button type='button' class='btn btn-success update-status-btn' data-toggle='modal' data-target='#updateQuantityModal' data-transaction-id='" . $row["transaction_id"] . "'>Update Status</button></td>";
                        $firstRow = false; // Set to false after displaying the button for the first row
                    } else {
                        echo "<td style='text-align:center; color:red;'>Queued</td>";
                    }

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No ongoing millings for today</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<br>

<p>Unfinished millings:</p>
<?php
// Fetch data for ongoing milling transactions for past dates
$sqlPast = "
    SELECT transaction_id, Customers.name AS customer_name, transaction_date, status, delivery_method, 'Customer' AS user_type,
    grain_type, milling_variety, quantity
    FROM millingtransactions
    JOIN Customers ON millingtransactions.customer_id = Customers.customer_id
    WHERE status = 'Ongoing' AND DATE(transaction_date) < '$currentDate'";

$resultPast = $connect->query($sqlPast);
?>

<div class="container-box1">
    <table class="table special-table1">
        <thead>
            <tr>
                <th>Name</th>
                <th>Mill Date</th>
                <th>Grain</th>
                <th>Variety</th>
                <th>Quantity</th>
                <th>User</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Check if there are rows in the result for past dates
        if ($resultPast->num_rows > 0) {
            // Output data of each row
            $firstRow = true; // Initialize $firstRow here
        
            while ($row = $resultPast->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["customer_name"] . "</td>";
                echo "<td>" . $row["transaction_date"] . "</td>";
                echo "<td>" . $row["grain_type"] . "</td>";
                echo "<td>" . $row["milling_variety"] . "</td>";
                echo "<td>" . $row["quantity"] . "</td>";
                echo "<td>" . $row["user_type"] . "</td>";
            
                // Display "Update Status" button only for the first row
                if ($firstRow) {
                    echo "<td><button type='button' class='btn btn-success update-status-btn' data-toggle='modal' data-target='#updateQuantityModal' data-transaction-id='" . $row["transaction_id"] . "'>Update Status</button></td>";
                    $firstRow = false; // Set to false after displaying the button for the first row
                } else {
                    echo "<td style='text-align:center; color:red;'>Queued</td>";
                }
            
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No ongoing millings from past dates</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>
<script>
$(document).ready(function () {
    $('.update-status-btn').click(function () {
        var transactionId = $(this).data('transaction-id');
        $('#updateQuantityBtn').data('transaction-id', transactionId);
    });

    $('#updateQuantityBtn').click(function () {
        var transactionId = $(this).data('transaction-id');
        var newQuantity = $('#newQuantityInput').val();

        // Check if the input is not empty before sending the AJAX request
        if (newQuantity.trim() === '') {
            // Show an alert or take appropriate action for empty input
            Swal.fire({
                icon: 'error',
                title: 'Empty Quantity',
                text: 'Please enter a quantity before updating.',
            });
            return;
        }

        // Send an AJAX request to update the quantity and status
        $.ajax({
            url: 'update_quantity.php', // Create a separate PHP file to handle the update
            type: 'POST',
            data: {
                transactionId: transactionId,
                newQuantity: newQuantity
            },
            success: function (response) {
                // Close the modal
                $('#updateQuantityModal').modal('hide');

                // Show a SweetAlert success message
                Swal.fire({
                    icon: 'success',
                    title: 'Update Successful!',
                    showConfirmButton: false,
                    text: 'Quantity and status have been updated successfully.',
                });

                // Delay the reload for 2 seconds (adjust as needed)
                setTimeout(function () {
                    // Reload the page to reflect the updated data
                    location.reload(true);
                }, 2000);
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
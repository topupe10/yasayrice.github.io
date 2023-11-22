<?php
session_start();

if (!isset($_SESSION['user_authenticated']) || !$_SESSION['user_authenticated']) {
    // User is not authenticated, redirect to the login page
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';
$activePage = 'buying';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $customerName = $_POST['customerName'];
    $contactNumber = $_POST['contactNumber'];
    $address = $_POST['address'];
    $quantity = $_POST['quantity'];
    $productType = $_POST['productType'];
    $grainType = $_POST['productType'];

    if ($connect->connect_error) {
        die("Connection failed: " . $connect->connect_error);
    }

    // Generate an invoice number with the current year and random numbers
    $invoiceNumber = generateInvoiceNumber();

    // Insert data into the "Customers" table
    $sql = "INSERT INTO Customers (name, contact_number, address) VALUES (?, ?, ?)";
    $stmt = $connect->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sss", $customerName, $contactNumber, $address);
        $stmt->execute();
        $customerId = $stmt->insert_id; // Get the auto-generated customer ID
        $stmt->close();

        // Insert data into the "buyingtransactions" table
        $sql = "INSERT INTO buyingtransactions (invoice_number, customer_id, transaction_date, grain_type, quantity, total_cost) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $connect->prepare($sql);

        if ($stmt) {
            $transactionDate = date('Y-m-d'); // Assuming the transaction date is the current date
            $status = "Ongoing"; // You can change the status as needed
            $totalCost = $quantity * $buyingPrice;
            $stmt->bind_param("sissii", $invoiceNumber, $customerId, $transactionDate, $grainType, $quantity, $_POST['totalCost']);
            $stmt->execute();
            $stmt->close();

            // Update or insert the "grainsstock" table
            $sql = "INSERT INTO grainsstock (grain_type, available_quantity, stock_in_quantity) VALUES (?, ?, ?)";
            $stmt = $connect->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("sii", $grainType, $quantity, $quantity);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    // Close the database connection
    $connect->close();

    // After successful form submission
    $redirectUrl = "buying.php?success=true";
    $redirectUrl .= "&transaction_date=" . urlencode($transactionDate); // Assuming $transactionDate holds the transaction date
    $redirectUrl .= "&customer_id=" . urlencode($customerId); // Assuming $customerId holds the customer ID
    $redirectUrl .= "&invoice_number=" . urlencode($invoiceNumber); // Assuming $invoiceNumber holds the invoice number
    $redirectUrl .= "&grainType=" . urlencode($grainType); // Assuming $grainType holds the grain type
    $redirectUrl .= "&quantity=" . urlencode($quantity); // Assuming $quantity holds the quantity
    $redirectUrl .= "&total_cost=" . urlencode($totalCost); // Assuming $totalCost holds the total cost
    
    header("Location: $redirectUrl");
    exit();

}
// Function to generate an invoice number with the current year and random numbers
function generateInvoiceNumber() {
    $currentYear = date('Y');
    $randomNumbers = mt_rand(100000, 999999); // You can adjust the range as needed
    return $currentYear . $randomNumbers;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Buying Services | Yasay Rice & Corn Milling Management Information System</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="stylesheet/buyingstyle.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha384-..." crossorigin="anonymous"/>
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
                <a href="dashboard.php">
                    <i class="fas fa-chart-bar"></i> Dashboard
                </a>
            </li>
            <li <?php if ($activePage == 'milling') echo 'class="active"'; ?>>
                <a href="milling.php">
                    <i class="fa-solid fa-wheat-awn"></i> Milling Services
                </a>
            </li>
            <li <?php if ($activePage == 'selling') echo 'class="active"'; ?>>
                <a href="selling.php">
                    <i class="fas fa-shopping-cart"></i> Selling Services
                </a>
            </li>
            <li <?php if ($activePage == 'buying') echo 'class="active"'; ?>>
                <a href="buying.php">
                    <i class="fas fa-shopping-basket"></i> Buying Services
                </a>
            </li>
            <li <?php if ($activePage == 'inventory') echo 'class="active"'; ?>>
                <a href="inventory.php">
                    <i class="fas fa-boxes"></i> Inventory
                </a>
            </li>
            <li <?php if ($activePage == 'reports') echo 'class="active"'; ?>>
                <a href="report.php">
                    <i class="fas fa-chart-line"></i> Reports
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

<!-- Modal for View Transaction Details -->
<div class="modal fade" id="transactionDetailsModal" tabindex="-1" role="dialog" aria-labelledby="transactionDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionDetailsModalLabel">Transaction Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Display transaction details in a table -->
                <table class="table">
                    <tbody>
                        <tr>
                            <th>Invoice #:</th>
                            <td><span id="invoiceNumber"></span></td>
                        </tr>
                        <tr>
                            <th>Transaction #:</th>
                            <td><span id="transactionId"></span></td>
                        </tr>
                        <tr>
                            <th>Customer ID:</th>
                            <td><span id="customerId"></span></td>
                        </tr>
                        <tr>
                            <th>Transaction Date:</th>
                            <td><span id="transactionDate"></span></td>
                        </tr>
                        <tr>
                            <th>Grain Type:</th>
                            <td><span id="grainType"></span></td>
                        </tr>
                        <tr>
                            <th>Quantity:</th>
                            <td><span id="buyingQuantity"></span></td>
                        </tr>
                        <tr>
                            <th>Total Cost:</th>
                            <td><span id="buyingTotalCost"></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

    <div class="content">
        <ol class="breadcrumb">
            <li><a href="dashboard.php">Dashboard</a></li>
            /
            <li class="active">Buying Services</li>
        </ol>
        <div class="container-box">
        <h1>Buying Services</h1>
        <?php
// Query to fetch grain_type, grain_price, and sum of available_quantity by grain_type
$sql = "SELECT gp.grain_type, gp.grain_price, IFNULL(SUM(stock.available_quantity), 0) AS total_available_quantity
        FROM grainprice gp
        LEFT JOIN grainsstock stock ON gp.grain_type = stock.grain_type
        GROUP BY gp.grain_type DESC";
$result = $connect->query($sql);
?>
<div class="row justify-content-center">
    <div class="col-md-12">
        <table class="table table-condensed special-table">
            <thead>
                <tr style="text-align:center;">
                    <th>Grain Type</th>
                    <th>Buying Price (Kilo)</th>
                    <th>Stocks Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Check if there are rows in the result
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td style=text-align:center;>" . $row["grain_type"] . "</td>";
                        echo "<td style=text-align:center;>₱" . number_format($row["grain_price"], 2) . "</td>"; // Format the price in peso currency
                        echo "<td style='text-align:center;'>" . number_format($row["total_available_quantity"]) . " kg</td>"; // Display total available_quantity by grain_type
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No data found in the grainprice table.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<br>
<button type="button" class="btn btn-info" data-toggle="modal" data-target="#sellGrainsModal">
    Buy Grains
</button>
         <!-- Modal for selling grains -->
         <div class="modal fade" id="sellGrainsModal" tabindex="-1" role="dialog" aria-labelledby="sellGrainsModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sellGrainsModalLabel">Buying Grains Form</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Add the form for selling grains here -->
                    <form id="sellGrainsForm" method="post">
                        <!-- Customer information fields -->
                        <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customerName"><i class="fa fa-user-circle"></i> Seller Name</label>
                                        <input type="text" class="form-control" id="customerName" name="customerName" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contactNumber"><i class="fa fa-id-badge"></i> Contact Number</label>
                                        <input type="tel" class="form-control" id="contactNumber" name="contactNumber" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group" id="deliveryAddressField">
                                        <label for="address"><i class="fa fa-map-marker"></i> Address</label>
                                        <input type="text" class="form-control" id="address" name="address">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="productType"><i class="fa fa-cubes"></i> Grain Type</label>
                                        <select class="form-control" id="productType" name="productType" required onchange="getGrainPrice(this.value)">
                                            <option value="" disabled selected>Select Grain Type</option>
                                            <option value="Rice">Rice</option>
                                            <option value="Corn">Corn</option>
                                        </select>
                                    </div>
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="quantity"><i class="fa fa-hashtag"></i> Quantity (Kilo)</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" required>
                                        <p id="quantityAlert" style="color: red;"></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="buyingPrice"><i class="fa fa-money"></i> Price</label>
                                        <input type="text" class="form-control" id="buyingPrice" name="buyingPrice" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="totalCost"><i class="fa fa-money"></i> Total Cost</label>
                                        <input type="text" class="form-control" id="totalCost" name="totalCost" readonly>
                                    </div>
                                </div>
                                <input type="hidden" id="hiddentotalCost" name="totalCost" value="">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <!-- Regular buttons for all contexts -->
                        <div id="customerButtons">
                            <button type="button" class="btn btn-danger" id="resetFieldsButtonCustomer">
                                <i class="fa fa-undo" aria-hidden="true"></i> Reset
                            </button>
                            <button type="submit" form="sellGrainsForm" name="submit" class="btn btn-success">
                                <i class="glyphicon glyphicon-ok"></i> Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br><br>
        <p>Today's purchased grains:</p>
            <!-- Table to show milling transactions -->
                <?php 
                    $sql = "SELECT invoice_number, transaction_id, Customers.name AS customer_name, transaction_date
                            FROM buyingtransactions 
                            JOIN Customers ON buyingtransactions .customer_id = Customers.customer_id
                            ORDER BY transaction_id DESC";
                    $result = $connect->query($sql);
                    ?>
                    <div class="container-box1">
                <table class="table special-table1">
            <thead>
                <tr>
                    <th>Trans. #</th>
                    <th>Invoice #</th>
                    <th>Name</th>
                    <th>Transaction Date</th>
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
                        echo "<td>" . $row["transaction_id"] . "</td>";
                        echo "<td>" . $row["invoice_number"] . "</td>";
                        echo "<td>" . $row["customer_name"] . "</td>";
                        echo "<td>" . $row["transaction_date"] . "</td>";
                        echo "<td><button class='btn btn-primary view-details' data-toggle='modal' data-target='#transactionDetailsModal' data-transaction-id='" . $row["transaction_id"] . "'>View Details</button>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No ongoing millings</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
    <script>
        // Get the modal
        var modal = document.getElementById("millingModal");

        // Get the button that opens the modal
        var openModalBtn = document.getElementById("openModalBtn");

        // Get the <span> element that closes the modal
        var closeModalBtn = document.getElementById("closeModalBtn");

        // When the user clicks the button, open the modal
        openModalBtn.onclick = function() {
            modal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal
        closeModalBtn.onclick = function() {
            modal.style display = "none";
        }

        // When the user clicks outside the modal, close it
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>
    <script>
    $(document).on("click", ".view-details", function () {
        var transactionId = $(this).data("transaction-id");
        $.ajax({
            type: 'POST',
            url: 'fetch_transaction_details_buying.php', // Replace with the actual URL for fetching transaction details
            data: { transactionId: transactionId },
            success: function (data) {
                var details = JSON.parse(data);
                $("#invoiceNumber").text(details.invoice_number);
                $("#transactionId").text(details.transaction_id);
                $("#customerId").text(details.customer_id);
                $("#transactionDate").text(details.transaction_date);
                $("#grainType").text(details.grain_type);
                $("#buyingQuantity").text(details.quantity);
                $("#buyingTotalCost").text(details.total_cost);
            }
        });
    });
</script>
<script>
    function getGrainPrice(grainType) {
    $.ajax({
        type: 'POST',
        url: 'get_grain_price.php', // Replace with the actual URL for fetching grain price
        data: {
            grainType: grainType,
        },
        success: function(data) {
            document.getElementById("buyingPrice").value = "₱" + parseFloat(data).toFixed(2);

            // Call calculateTotalPrice after setting the selling price
            calculateTotalPrice();
        },
    });
}
// Event binding for quantity input
$("#quantity").on("input", function() {
    calculateTotalCost();
});
function calculateTotalCost() {
    var buyingPriceString = document.getElementById("buyingPrice").value.replace("₱", "");
    var buyingPrice = parseFloat(buyingPriceString.replace(",", ""));
    var quantity = parseFloat(document.getElementById("quantity").value) || 0; // Use getElementById for simplicity
    var totalCost = buyingPrice * quantity;

    if (!isNaN(totalCost)) {
        // Format the totalCost using Number.toLocaleString to add commas
        document.getElementById("totalCost").value = "₱" + totalCost.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        // Set the calculated total cost in a hidden input field to be submitted with the form
        document.getElementById("hiddentotalCost").value = totalCost;
    }
}
</script>
<script>
    $(document).ready(function () {
        // Check if the URL contains the success parameter
        var urlParams = new URLSearchParams(window.location.search);
        var successParam = urlParams.get('success');

        if (successParam === 'true') {
            // Fetch other parameters
            var transaction_date = urlParams.get('transaction_date');
            var customer_id = urlParams.get('customer_id');
            var invoice_number = urlParams.get('invoice_number');
            var grainType = urlParams.get('grainType');
            var quantity = urlParams.get('quantity');
            var total_cost = urlParams.get('total_cost');

            // Fetch customer name using an AJAX request
            $.ajax({
                url: 'get_customer_name.php',
                method: 'GET',
                data: { customer_id: customer_id },
                success: function (customer_name) {
                    // Customize the content of the Swal alert using the fetched values
                    var swalContent = `
                            <h1 style="text-align: center;"><strong>YASAY RICE & CORN MILL</strong></h1>
                            <p style="text-align: center;">Igpit, Opol Misamis Oriental 9000</p>
                            <br><br><br>
                            <p style="text-align: right;"><b>Date:</b> ${transaction_date}</p>
                            <p style="text-align: left;"><b>Customer:</b> ${customer_name}</p>
                            <p style="text-align: left;"><b>Official Receipt #:</b> ${invoice_number}</p>
                            <hr>
                            <table style="width: 100%; border:none; text-align: left;">
                                <tr>
                                    <td><b>Grain Type:</b></td>
                                    <td>${grainType}</td>
                                </tr>
                                <tr>
                                    <td><b>Quantity:</b></td>
                                    <td>${quantity}kg</td>
                                </tr>
                                <tr>
                                    <td><b>Total Cost:</b></td>
                                    <td>₱${total_cost}</td>
                                </tr>
                            </table>
                            <hr>
                            <p style="text-align: center;">Thank you for your service! Please Come Again :)</p>
                            <button id="printButton" onclick="printSwalContent()" style="background-color: #258cd1; border: none; color: white; padding: 10px 20px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 5px;">
                            <i class="fas fa-print" style="margin-right: 5px;"></i> Print
                            </button>
                            `;
                    
                            Swal.fire({
                                html: swalContent,
                                showConfirmButton: false, // Hide the default "OK" button
                            });
                        },
                        error: function () {
                            console.log('Error fetching customer name.');
                        }
                    });
                }
            });
    
            // Function to print the Swal alert content
            function printSwalContent() {
                var printButton = document.getElementById('printButton');
                printButton.style.display = 'none'; // Hide the button before printing
                window.print();
                printButton.style.display = ''; // Restore the button after printing
            }
</script>
    <!-- Bootstrap JavaScript and jQuery should be included after your content -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

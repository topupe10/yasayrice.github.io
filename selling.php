<?php
// Function to generate an invoice number with the current year and random numbers
function generateInvoiceNumber() {
    $currentYear = date('Y');
    $randomNumbers = mt_rand(100000, 999999); // You can adjust the range as needed
    return $currentYear . $randomNumbers;
}

session_start();

if (!isset($_SESSION['user_authenticated']) || !$_SESSION['user_authenticated']) {
    // User is not authenticated, redirect to the login page
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';
$activePage = 'selling'; // Set the active page to 'selling'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $customerName = $_POST['customerName'];
    $contactNumber = $_POST['contactNumber'];
    $address = $_POST['address'];
    $dispose = $_POST['dispose'];
    $deliveryDate = $_POST['deliveryDate'];
    $quantity = $_POST['quantity'];
    $productType = $_POST['productType'];
    $millingVariety = $_POST['millingVariety'];
    $scaleType = $_POST['scaleType'];
    $grainType = $_POST['productType'];

    if ($connect->connect_error) {
        die("Connection failed: " . $connect->connect_error);
    }

    $invoiceNumber = generateInvoiceNumber();

    // Insert data into the "Customers" table
        $sql = "INSERT INTO customers (name, contact_number, address) VALUES (?, ?, ?)";
        $stmt = $connect->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("sss", $customerName, $contactNumber, $address);
            $stmt->execute();
            $customerId = $stmt->insert_id; // Get the auto-generated customer ID
            $stmt->close();  // Close the first prepared statement
        
            // Insert data into the "sellingtransactions" table
            $sql = "INSERT INTO sellingtransactions (invoice_number, customer_id, transaction_date, grain_type, milling_variety, scale_type, quantity, total_price, delivery_method, delivery_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $connect->prepare($sql);
        
            if ($stmt) {
            $transactionDate = date('Y-m-d'); // Assuming the transaction date is the current date
            $status = "Complete"; // You can change the status as needed
        
            // Calculate total price - make sure to set $sellingPrice correctly based on your AJAX response
            $totalPrice = $quantity * $sellingPrice;
        
            $stmt->bind_param("sisssssisss", $invoiceNumber, $customerId, $transactionDate, $grainType, $millingVariety, $scaleType, $quantity, $_POST['totalPrice'], $dispose, $deliveryDate, $status);
        
            // Check for errors
            if ($stmt->execute()) {
                // Success
            } else {
                echo "Error inserting into sellingtransactions: " . $stmt->error;
            }

            if ($scaleType === 'Sack' || $scaleType === 'Kilo') {
                // Deduct from available_stock
                $sql = "UPDATE milledgrains 
                        SET available_stock = available_stock - ?
                        WHERE grain_type = ? AND variety = ?";
                $stmt = $connect->prepare($sql);

                if ($stmt) {
                    $stmt->bind_param("dss", $quantity, $grainType, $millingVariety);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    // Handle the case when the SQL statement couldn't be prepared
                    echo "Error updating available stock: " . $connect->error;
                }
            } else {
                // Handle the case where the scale type is not valid
                echo "Invalid scale type selected";
            }            
        } else {
            echo "Error: " . $connect->error;
        }

        // Close the database connection
        $connect->close();

        // After successful form submission
        $redirectUrl = "selling.php?success=true";
        $redirectUrl .= "&transaction_date=" . urlencode($transactionDate); // Assuming $transactionDate holds the transaction date
        $redirectUrl .= "&customer_id=" . urlencode($customerId); // Assuming $customerId holds the customer ID
        $redirectUrl .= "&invoice_number=" . urlencode($invoiceNumber); // Assuming $invoiceNumber holds the invoice number
        $redirectUrl .= "&grainType=" . urlencode($grainType); // Assuming $grainType holds the grain type
        $redirectUrl .= "&milling_variety=" . urlencode($millingVariety);
        $redirectUrl .= "&quantity=" . urlencode($quantity); // Assuming $quantity holds the quantity
        $redirectUrl .= "&total_cost=" . urlencode($totalCost); // Assuming $totalCost holds the total cost
        
        header("Location: $redirectUrl");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Selling Services | Yasay Rice & Corn Milling Management Information System</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" type="text/css" href="stylesheet/sellingstyle.css">
    <link  rel="stylesheet"  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"  integrity="sha384-..."  crossorigin="anonymous"/>
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
<!-- Modal for View Details -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" role="dialog" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailsModalLabel">Transaction Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Display transaction details in a table -->
                <table class="table">
                    <tbody id="transactionDetailsTable"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Your modal structure -->
<div class="modal fade" id="replaceTransactionModal" tabindex="-1" role="dialog" aria-labelledby="replaceTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="replaceTransactionModalLabel">Replace Transaction</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Display basic transaction details in a table -->
                <table class="table">
                    <tbody>
                        <tr>
                            <td><strong>Grain Type:</strong></td>
                            <td><span id="replaceGrainType"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Milling Variety:</strong></td>
                            <td><span id="replaceMillingVariety"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Quantity:</strong></td>
                            <td><span id="replaceQuantity"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Scale Type:</strong></td>
                            <td><span id="replaceScaleType"></span></td>
                        </tr>
                    </tbody>
                </table>

                <!-- Additional fields for replacement reason -->
                <label for="replacementReason">Replacement Reason:</label>
                <textarea class="form-control" id="replacementReason" rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="confirmReplaceBtn">Replace</button>
            </div>
        </div>
    </div>
</div>
    <div class="content">
        <ol class="breadcrumb">
            <li><a href="dashboard.php">Dashboard</a></li>
            /
            <li class="active">Selling Services</li>
        </ol>
        <div class="container-box">
        <h1>Selling Services</h1>
        <?php
$sql = "SELECT `variety`, `price_per_kilo`, `available_stock`, available_stock_sack FROM `milledgrains`";
$result = $connect->query($sql);

if ($result->num_rows > 0) {
    echo '<div class="row justify-content-center">
            <div class="col-md-12">
                        <table class="table table-condensed special-table">
                            <thead>
                            <tr style="text-align:center;">
                                <th>Milled Grain Variety</th>
                                <th>Price</th>
                                <th class="quantity-column bg-quantity">Available Stocks</th>
                            </tr>
                            </thead>
                            <tbody>';

                            while ($row = $result->fetch_assoc()) {
                                // Format prices in peso currency
                                $price_per_kilo = '₱' . number_format($row['price_per_kilo'], 2);
                                // Output a row for each record
                                echo '<tr>';
                                echo '<td style="padding-left:20px;">' . $row['variety'] . '</td>';
                                echo '<td style="text-align:center;">' . $price_per_kilo . '</td>';
                                echo '<td style="text-align:center;">' . number_format($row['available_stock']) . ' kg</td>';
                                echo '</tr>';
                            }
            echo '</tbody>
        </table>
    </div>';
} else {
    echo 'No data found in the milledgrains table.';
}
?>
</div>
<br>

<button type="button" class="btn btn-info" data-toggle="modal" data-target="#sellGrainsModal">
    Sell Grains
</button>
        <br><br>
         <!-- Modal for selling grains -->
         <div class="modal fade" id="sellGrainsModal" tabindex="-1" role="dialog" aria-labelledby="sellGrainsModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sellGrainsModalLabel">Selling Grains Form</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Add the form for selling grains here -->
                    <form id="sellGrainsForm" method="post">
                        <!-- Customer information fields -->
                        <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="customerName"><i class="fa fa-user-circle"></i> Customer Name</label>
                                        <input type="text" class="form-control" id="customerName" name="customerName" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="contactNumber"><i class="fa fa-id-badge"></i> Contact Number</label>
                                        <input type="tel" class="form-control" id="contactNumber" name="contactNumber" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" id="deliveryAddressField">
                                        <label for="address"><i class="fa fa-map-marker"></i> Address</label>
                                        <input type="text" class="form-control" id="address" name="address">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="dispose"><i class="fa fa-truck"></i> Distribution</label>
                                        <select class="form-control" id="dispose" name="dispose" required>
                                            <option value="" disabled selected>Select Method</option>
                                            <option value="delivery">Delivery</option>
                                            <option value="pickup">Pickup</option>
                                            <option value="N/A" style="display: none">N/A</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="deliveryDate"><i class="fa fa-calendar"></i> Delivery Date</label>
                                        <input type="date" class="form-control" id="deliveryDate" name="deliveryDate" min="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="productType"><i class="fa fa-cubes"></i> Grain Type</label>
                                        <select class="form-control" id="productType" name="productType" required>
                                            <option value="" disabled selected>Select Grain Type</option>
                                            <option value="Rice">Rice</option>
                                            <option value="Corn">Corn</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="millingVariety"><i class="fa fa-cubes"></i> Variety</label>
                                        <select class="form-control" id="millingVariety" name="millingVariety" required>
                                            <option value="" disabled selected>Select Milling Variety</option>
                                            <option value="White Rice">White Rice</option>
                                            <option value="Red Rice">Red Rice</option>
                                            <option value="Cracked Corn">Cracked Corn</option>
                                            <option value="Yellow Grits">Yellow Grits</option>
                                            <option value="Yellow Corn Bran">Yellow Corn Bran</option>
                                            <option value="White Corn Bran">White Corn Bran</option>
                                            <option value="White Corn Grits #10">White Corn Grits #10</option>
                                            <option value="White Corn Grits #12">White Corn Grits #12</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="scaleType">Scale Type</label>
                                        <select class="form-control" id="scaleType" name="scaleType" required>
                                            <option value="" disabled selected>Select Scale Type</option>
                                            <option value="Sack">Sack</option>
                                            <option value="Kilo">Kilo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="quantity"><i class="fa fa-hashtag"></i> Quantity</label>
                                        <input type="text" class="form-control" id="quantity" name="quantity" required>
                                        <p id="quantityAlert" style="color: red;"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sellingPrice"><i class="fa fa-money"></i> Price</label>
                                        <input type="text" class="form-control" id="sellingPrice" name="sellingPrice" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="totalPrice"><i class="fa fa-money"></i> Total Price</label>
                                        <input type="text" class="form-control" id="totalPrice" name="totalPrice" readonly>
                                    </div>
                                </div>
                                <input type="hidden" id="hiddentotalPrice" name="totalPrice" value="">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <!-- Regular buttons for all contexts -->
                        <div id="customerButtons">
                            <button type="button" class="btn btn-danger" id="resetFieldsButtonCustomer"><i class="fa fa-undo" aria-hidden="true"></i> Reset</button>
                            <button type="submit" form="sellGrainsForm" id="submitBtn" name="submit" class="btn btn-success"><i class="glyphicon glyphicon-ok"></i> Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <p>Today's selling transactions:</p>
        <!-- Table to show milling transactions -->
        <?php 
                $sql = "SELECT invoice_number, transaction_id, Customers.name AS customer_name, transaction_date, status, delivery_method
                        FROM sellingtransactions
                        JOIN Customers ON sellingtransactions.customer_id = Customers.customer_id
                        WHERE status = 'Complete'  or status = 'Redried'
                        ORDER BY sellingtransactions.customer_id DESC";
                $result = $connect->query($sql);
                ?>
                <div class="container-box1">
        <table class="table special-table1">
    <thead>
        <tr>
            <th>Trans #</th>
            <th>Invoice #</th>
            <th>Customer</th>
            <th>Transaction Date</th>
            <th>Action</th>
            <th>Damage</th>
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
                echo "<td>
                          <button type='button' class='btn btn-info view-details-btn' data-toggle='modal' data-target='#viewDetailsModal' data-transaction-id='" . $row["transaction_id"] . "'>View Details</button>
                      </td>";
                echo "<td>
                          <button type='button' class='btn btn-danger redry-btn' data-transaction-id='" . $row["transaction_id"] . "'>Redry</button>
                          <button type='button' class='btn btn-danger replace-btn' data-transaction-id='" . $row["transaction_id"] . "' data-status='" . $row["status"] . "'>Replace</button>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No selling transactions today.</td></tr>";
        }
        ?>
    </tbody>
</table>
</div>
<p>Today's redry transactions:</p>
        <!-- Table to show milling transactions -->
        <?php 
                $sql = "SELECT transaction_id, Customers.name AS customer_name, transaction_date, status, delivery_method
                        FROM sellingtransactions
                        JOIN Customers ON sellingtransactions.customer_id = Customers.customer_id
                        WHERE redry_status = 'Ongoing'
                        ORDER BY sellingtransactions.customer_id DESC";
                $result = $connect->query($sql);
                ?>
                <div class="container-box1">
        <table class="table special-table1">
    <thead>
        <tr>
            <th>Transaction #</th>
            <th>Customer</th>
            <th>Transaction Date</th>
            <th colspan="2" style="text-align:center;">Action</th>
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
                echo "<td>" . $row["customer_name"] . "</td>";
                echo "<td>" . $row["transaction_date"] . "</td>";
                echo "<td>
                          <button type='button' class='btn btn-info view-details-btn' data-toggle='modal' data-target='#viewDetailsModal' data-transaction-id='" . $row["transaction_id"] . "'>View Details</button>
                      </td>";
                echo "<td>
                          <button type='button' class='btn btn-danger redistribute-btn' data-transaction-id='" . $row["transaction_id"] . "'>Redistribute</button>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No redry transactions today.</td></tr>";
        }
        ?>
    </tbody>
</table>
    </div>
    <!-- Include Bootstrap JavaScript and jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    // Get the modal
    var modal = document.getElementById("sellGrainsModal");

    // Get the button that opens the modal
    var openModalBtn = document.querySelector("[data-target='#sellGrainsModal']");

    // Get the <span> element that closes the modal
    var closeModalBtn = document.querySelector("#sellGrainsModal .close");

    // When the user clicks the button, open the modal
    openModalBtn.addEventListener("click", function() {
        modal.style.display = "block";
    });

    // When the user clicks on <span> (x), close the modal
    closeModalBtn.addEventListener("click", function() {
        modal.style.display = "none";
    });

    // When the user clicks outside the modal, close it
    window.addEventListener("click", function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
</script>
<script>
    $(document).ready(function () {
        // Handle click on "View Details" button
        $('.view-details-btn').click(function () {
            var transactionId = $(this).data('transaction-id');

            // Use AJAX to fetch details from the server-side script
            $.ajax({
                type: 'POST',
                url: 'selling_fetch_transaction_details.php', // Replace with the actual server-side script URL
                data: { transactionId: transactionId },
                dataType: 'json',
                success: function (data) {
                    // Populate the modal with fetched details in a table
                    var tableRows = '';
                    $.each(data, function (key, value) {
                        tableRows += '<tr><td><strong>' + key.replace('_', ' ') + '</strong></td><td>' + value + '</td></tr>';
                    });

                    // Update the table body with rows
                    $('#transactionDetailsTable').html(tableRows);

                    // Show the modal
                    $('#viewDetailsModal').modal('show');
                },
                error: function () {
                    alert('Error fetching transaction details');
                }
            });
        });
    });
</script>
<script>
    // Function to check the quantity against available_quantity
    function checkQuantity() {
    var productType = document.getElementById("productType").value;
    var quantity = document.getElementById("quantity").value;

    console.log("Product Type: " + productType); // Add this for debugging
    console.log("Quantity: " + quantity); // Add this for debugging

        // Send an AJAX request to the server to get available_quantity
        $.ajax({
            type: 'POST',
            url: 'check_quantity.php', // Create a PHP script to handle the database query
            data: {
                productType: productType,
                millingVariety: $("#millingVariety").val(), // Include millingVariety
                scaleType: $("#scaleType").val(),
            },
            success: function(data) {
                var availableQuantity = parseInt(data);
            
                if (quantity > availableQuantity) {
                    document.getElementById("quantityAlert").textContent = "*Quantity exceeds the available stocks.";
                    document.getElementById("submitBtn").disabled = true;
                } else {
                    document.getElementById("quantityAlert").textContent = "";
                    document.getElementById("submitBtn").disabled = false;
                }
            },
        });
    }
    // Attach the checkQuantity function to the change event of the quantity input field
    document.getElementById("quantity").addEventListener("change", checkQuantity);
</script>
<script>
    $(document).ready(function () {
        // Function to handle the "Redry" button click
        $('.redry-btn').click(function () {
            var transactionId = $(this).data('transaction-id');

            // Use SweetAlert for confirmation
            Swal.fire({
                title: 'Confirm Milled Grains Redry',
                text: 'Are you sure the damaged milled grains are suitable for redrying?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, redry it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // User clicked confirm, make an Ajax request to update the redry_status
                    $.ajax({
                        type: 'POST',
                        url: 'update_redry_status.php',
                        data: { transaction_id: transactionId },
                        success: function (response) {
                            if (response === 'success') {
                                // Use SweetAlert for success
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Update Successful!',
                                    text: 'Damaged milled grains accepted for redrying.',
                                }).then(function () {
                                    // Optionally, you can reload the page or update the UI as needed
                                    location.reload();
                                });
                            } else {
                                // Use SweetAlert for error
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error updating redry status:',
                                    text: response,
                                });
                            }
                        },
                        error: function () {
                            // Use SweetAlert for generic error
                            Swal.fire({
                                icon: 'error',
                                text: 'Error updating redry status. Please try again.',
                            });
                        }
                    });
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function () {
        // Function to handle the "Redistribute" button click
        $('.redistribute-btn').click(function () {
            var transactionId = $(this).data('transaction-id');

            // Make an Ajax request to update the redry_status
            $.ajax({
                type: 'POST',
                url: 'update_redistribute_status.php',
                data: { transaction_id: transactionId },
                success: function (response) {
                    if (response === 'success') {
                        // Use SweetAlert for success
                        Swal.fire({
                            icon: 'success',
                            title: 'Update Successful!',
                            text: 'Redried grains redistributed successfully.',
                        }).then(function () {
                            // Optionally, you can reload the page or update the UI as needed
                            location.reload();
                        });
                    } else {
                        // Use SweetAlert for error
                        Swal.fire({
                            icon: 'error',
                            title: 'Error updating redry status to complete:',
                            text: response,
                        });
                    }
                },
                error: function () {
                    // Use SweetAlert for generic error
                    Swal.fire({
                        icon: 'error',
                        text: 'Error updating redry status to complete. Please try again.',
                    });
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function () {
        // Function to handle the "Replace" button click
        $('.replace-btn').click(function () {
            var transactionId = $(this).data('transaction-id');

            // Fetch transaction details via Ajax
            $.ajax({
                type: 'POST',
                url: 'replace_get_transaction_details.php', // Create a new PHP file to handle this request
                data: { transaction_id: transactionId },
                dataType: 'json',
                success: function (data) {
                    if (data) {
                        // Update modal content with fetched transaction details
                        $('#replaceGrainType').text(data.grain_type);
                        $('#replaceMillingVariety').text(data.milling_variety);
                        $('#replaceQuantity').text(data.quantity);
                        $('#replaceScaleType').text(data.scale_type);

                        // Display the modal
                        $('#replaceTransactionModal').modal('show');
                    } else {
                        alert('Error fetching transaction details.');
                    }
                },
                error: function () {
                    alert('Error fetching transaction details. Please try again.');
                }
            });
        });

        // Handle the "Replace" button within the modal
        $('#confirmReplaceBtn').click(function () {
            // Your existing code for updating the transaction status goes here...
        });
    });
</script>
<script>
    $(document).ready(function () {
        // Function to handle the "Replace" button click
        $('.replace-btn').click(function () {
            var transactionId = $(this).data('transaction-id');
            var grainType = $(this).data('grain-type');
            var millingVariety = $(this).data('milling-variety');
            var quantity = $(this).data('quantity');
            var scaleType = $(this).data('scale-type');

            // Update modal content with basic transaction details
            $('#replaceGrainType').text(grainType);
            $('#replaceMillingVariety').text(millingVariety);
            $('#replaceQuantity').text(quantity);
            $('#replaceScaleType').text(scaleType);

            // Display the modal
            $('#replaceTransactionModal').modal('show');

            // Handle the "Replace" button within the modal
            $('#confirmReplaceBtn').click(function () {
                // Get the replacement reason entered by the user
                var replacementReason = $('#replacementReason').val();

                // Make a server-side request to handle the replacement
                $.post('handle_replace.php', {
                    transaction_id: transactionId,
                    grain_type: grainType,
                    milling_variety: millingVariety,
                    quantity: quantity,
                    scale_type: scaleType,
                    reason: replacementReason
                }, function (response) {
                    // Parse the JSON response
                    var data = JSON.parse(response);

                    if (data.success) {
                        // Display a success message using SweetAlert
                        Swal.fire({
                            icon: 'success',
                            title: 'Replacement Successful',
                            text: 'The damaged milled grains has been replaced successfully.',
                        }).then((result) => {
                            // Optionally, you can reload the page or update the UI as needed
                            location.reload();
                        });
                    } else {
                        // Display an error message using SweetAlert
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error replacing transaction: ' + data.error,
                        });
                    }
                });
            });
        });
    });
</script>
<script>
    function getMillingFeeAndPrice() {
    var grainType = $("#productType").val();
    var millingVariety = $("#millingVariety").val();
    var scaleType = $("#scaleType").val();

    // Check if both milling variety and scale type are selected
    if (millingVariety && scaleType) {
        $.ajax({
            type: 'POST',
            url: 'get_price_variety.php',
            data: {
                grainType: grainType,
                millingVariety: millingVariety,
                scaleType: scaleType
            },
            success: function(data) {
                var sellingPrice = parseFloat(data).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' });
                document.getElementById("sellingPrice").value = sellingPrice;

                // Call calculateTotalFee after setting the selling price
                calculateTotalFee();
            },
        });
    } else {
        // Display an appropriate message or handle the case when milling variety and scale type are not selected
        document.getElementById("sellingPrice").value = "";
    }
}

// Event binding outside the function for milling fee and price
    $("#productType, #millingVariety, #scaleType").on("change", function() {
        getMillingFeeAndPrice();
    });

    // Event binding for quantity input
    $("#quantity").on("input", function() {
        calculateTotalPrice();
    });

    // Function to calculate and update total fee (duplicate method)
    function calculateTotalPrice() {
        var sellingPriceString = document.getElementById("sellingPrice").value.replace("₱", "");
        var sellingPrice = parseFloat(sellingPriceString.replace(",", "")); // Remove commas for parsing as a number
        var quantity = parseFloat(document.getElementById("quantity").value) || 0;
        var totalPrice = sellingPrice * quantity;

        if (!isNaN(totalPrice)) {
            // Format the duplicateTotalFee using Number.toLocaleString to add commas
            document.getElementById("totalPrice").value = "₱" + totalPrice.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            // Set the calculated total fee in a hidden input field to be submitted with the form
            document.getElementById("hiddentotalPrice").value = totalPrice;
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
            var milling_variety = urlParams.get('milling_variety');
            var quantity = urlParams.get('quantity');
            var total_cost = urlParams.get('total_cost');
            var selling_price = urlParams.get('selling_price');

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
                                    <td><b>Variety:</b></td>
                                    <td>${milling_variety}</td>
                                </tr>
                                <tr>
                                    <td><b>Price per Unit:</b></td>
                                    <td>₱${total_cost}</td>
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
                            <p style="text-align: center;">Thank you for purchasing from Yasay Rice & Corn Milling Company!</p>
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
<script>
    // Add an event listener to the "Distribution" dropdown
    $(document).ready(function () {
        $('#dispose').on('change', function () {
            // Get the selected value
            var selectedValue = $(this).val();

            // Check if the selected value is "pickup"
            if (selectedValue === 'pickup') {
                // Set the "readonly" attribute on the "Delivery Date" input
                $('#deliveryDate').prop('readonly', true);
            } else {
                // Remove the "readonly" attribute if not "pickup"
                $('#deliveryDate').prop('readonly', false);
            }
        });
    });
</script>
</body>
</html>

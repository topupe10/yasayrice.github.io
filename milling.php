<?php
session_start();
// Function to generate an invoice number with the current year and random numbers
function generateInvoiceNumber() {
    $currentYear = date('Y');
    $randomNumbers = mt_rand(100000, 999999); // You can adjust the range as needed
    return $currentYear . $randomNumbers;
}

if (!isset($_SESSION['user_authenticated']) || !$_SESSION['user_authenticated']) {
    // User is not authenticated, redirect to the login page
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';
$activePage = 'milling'; // Set the active page to 'milling'

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

        $invoiceNumber = generateInvoiceNumber();

        // Insert data into the "millingtransactions" table
        $sql = "INSERT INTO millingtransactions (invoice_number, customer_id, transaction_date, delivery_method, delivery_date, status, grain_type, milling_variety, quantity) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connect->prepare($sql);

        if ($stmt) {
            $transactionDate = date('Y-m-d'); // Assuming the transaction date is the current date
            $status = "Ongoing"; // You can change the status as needed

            // Assuming you have only one grain in the form
            $grainType = $_POST['productType'][0];
            $millingVariety = $_POST['millingVariety'][0];
            $quantity = $_POST['quantity'][0];

            $stmt->bind_param("iissssssi", $invoiceNumber, $customerId, $transactionDate, $dispose, $deliveryDate, $status, $grainType, $millingVariety, $quantity);
            $stmt->execute();
            $transactionId = $stmt->insert_id; // Get the auto-generated transaction ID
            $stmt->close();

            // Close the database connection
            $connect->close();

            // After successful form submission
        $redirectUrl = "milling.php?success=true";
        $redirectUrl .= "&transaction_date=" . urlencode($transactionDate); // Assuming $transactionDate holds the transaction date
        $redirectUrl .= "&customer_id=" . urlencode($customerId); // Assuming $customerId holds the customer ID
        $redirectUrl .= "&invoice_number=" . urlencode($invoiceNumber); // Assuming $invoiceNumber holds the invoice number
        $redirectUrl .= "&grainType=" . urlencode($grainType); // Assuming $grainType holds the grain type
        $redirectUrl .= "&milling_variety=" . urlencode($millingVariety);
        $redirectUrl .= "&quantity=" . urlencode($quantity); // Assuming $quantity holds the quantity
        $redirectUrl .= "&total_fee=" . urlencode($totalFee); // Assuming $totalCost holds the total cost
        
        header("Location: $redirectUrl");
        exit();
        }
    }
}
?>
<!-- The rest of your HTML code remains unchanged -->
<!DOCTYPE html>
<html>
<head>
    <title>Milling Services | Yasay Rice & Corn Milling Management Information System</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="stylesheet/millingstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha384-..." crossorigin="anonymous"/>
</head>
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
                            <th>Grain Type</th>
                            <th>Milling Fee</th>
                            <th>Ongoing Milling</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch data from the millingfees table
                        $sql = "SELECT grain_type, milling_fee FROM millingfees";
                        $result = $connect->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                // Count the number of ongoing transactions for each grain type
                                $sqlCountOngoing = "SELECT COUNT(*) AS ongoing_count FROM millingtransactions WHERE grain_type = '{$row['grain_type']}' AND status = 'Ongoing'";
                                $resultCountOngoing = $connect->query($sqlCountOngoing);
                                $ongoingCount = ($resultCountOngoing->num_rows > 0) ? $resultCountOngoing->fetch_assoc()['ongoing_count'] : 0;

                                echo "<tr>";
                                echo "<td style=text-align:center;>" . $row['grain_type'] . "</td>";
                                echo "<td style=text-align:center;>₱" . number_format($row['milling_fee'], 2) . "</td>";
                                echo "<td style=text-align:center;>" . $ongoingCount . "</td>"; // Display the count of ongoing transactions for each grain type
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
<br>

        <!-- Button to open the milling form modal -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addServiceModal">
            Add Milling (Customer)
        </button>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#quickMillingModal">
            Quick Milling (Admin)
        </button>
<!-- Quick Milling Modal for Admin -->
<div class="modal fade" id="quickMillingModal" tabindex="-1" role="dialog" aria-labelledby="quickMillingModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addServiceModalLabel">Quick Milling (Admin)</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: red;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="quickMillingForm" method="post" action="process_quick_milling.php">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="display:none;">
                                <label for="customerName"><i class="fa fa-user-circle"></i> Customer Name</label>
                                <input type="text" class="form-control" id="customerName" name="customerName" value="Admin" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="display:none;">
                                <label for="contactNumber"><i class="fa fa-id-badge"></i> Contact Number</label>
                                <input type="tel" class="form-control" id="contactNumber" name="contactNumber" value="09772811758" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6" style="display:none;">
                            <div class="form-group" id="deliveryAddressField">
                                <label for="address"><i class="fa fa-map-marker"></i> Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="Igpit, Opol CDOC" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="display:none;">
                                <label for="dispose"><i class="fa fa-truck"></i> Distribution Method</label>
                                <select class="form-control" id="dispose" name="dispose" readonly>
                                    <option value="N/A" selected>N/A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="display:none;">
                                <label for="deliveryDate"><i class="fa fa-calendar"></i> Delivery Date</label>
                                <input type="date" class="form-control" id="deliveryDate" name="deliveryDate" value="" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="millingVariety"><i class="fa fa-cubes"></i> Milling Variety</label>
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
                                <label for="quantity"><i class="fa fa-hashtag"></i> Quantity (Kilo)</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" required>
                                <p id="quantityAlert" style="color: red;"></p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="resetFieldsButtonAdmin"><i class="fa fa-undo" aria-hidden="true"></i> Reset</button>
                <button type="submit" form="quickMillingForm" id="submitBtn" name="submit" class="btn btn-success" disabled><i class="glyphicon glyphicon-ok"></i> Submit</button>
            </div>
        </div>
    </div>
</div>
        <!-- Add Service Modal -->
        <div class="modal fade" id="addServiceModal" tabindex="-1" role="dialog" aria-labelledby="addServiceModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="addServiceModalLabel">Milling Grains Form (Customer)</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: red;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="addServiceForm" method="post">
                            <button type="button" class="btn btn-primary" id="addMillingEntry">
                                Multiple Grains
                            </button>
                            <br><br>
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="dispose"><i class="fa fa-truck"></i> Distribution Method</label>
                                        <select class="form-control" id="dispose" name="dispose" required>
                                            <option value="" disabled selected>Select Method</option>
                                            <option value="delivery">Delivery</option>
                                            <option value="pickup">Pickup</option>
                                            <option value="N/A" style="display: none">N/A</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="deliveryDate"><i class="fa fa-calendar"></i> Delivery Date</label>
                                        <input type="date" class="form-control" id="deliveryDate" name="deliveryDate" min="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="productType"><i class="fa fa-cubes"></i> Grain Type</label>
                                        <select class="form-control" name="productType[]" id="productType" onchange="getMillingFee(this.value)" required>
                                            <option value="" disabled selected>Select Grain Type</option>
                                            <option value="Rice">Rice</option>
                                            <option value="Corn">Corn</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="millingVariety"><i class="fa fa-cubes"></i> Milling Variety</label>
                                        <select class="form-control" name="millingVariety[]" required>
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
                                        <label for="quantity"><i class="fa fa-hashtag"></i> Quantity (Sacks)</label>
                                        <input type="number" class="form-control" name="quantity[]" required oninput="calculateTotalFee()" onchange="calculateTotalFee()">
                                    </div>
                                </div>
                            </div>
                            <div id="multipleGrainsFields">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="millingFee"><i class="fa fa-money"></i> Milling Fee</label>
                                        <input type="text" class="form-control" id="millingFee" name="millingFee" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="totalFee"><i class="fa fa-money"></i> Total Fee</label>
                                        <input type="text" class="form-control" id="totalFee" name="totalFee" readonly>
                                    </div>
                                </div>
                                <input type="hidden" id="hiddenTotalFee" name="totalFee" value="">  
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <!-- Regular buttons for all contexts -->
                        <div id="customerButtons">
                            <button type="button" class="btn btn-danger" id="resetFieldsButtonCustomer">
                                <i class="fa fa-undo" aria-hidden="true"></i> Reset
                            </button>
                            <button type="submit" form="addServiceForm" name="submit" class="btn btn-success" onclick="submitForm()">
                                <i class="glyphicon glyphicon-ok"></i> Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br><br>
        <p>Today's ongoing millings:</p>
<!-- Table to show milling transactions -->
<?php
$sql = "SELECT transaction_id, Customers.name AS customer_name, transaction_date, status, delivery_method, 'Customer' AS user_type
        FROM millingtransactions
        JOIN Customers ON millingtransactions.customer_id = Customers.customer_id
        ORDER by transaction_id DESC ";
$result = $connect->query($sql);
?>
<div class="container-box1">
<table class="table special-table1">
    <thead>
        <tr>
            <th>Name</th>
            <th>Transaction Date</th>
            <th>Status</th>
            <th>User Type</th>
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
                echo "<td>" . $row["status"] . "</td>";
                echo "<td>" . $row["user_type"] . "</td>";
                echo "<td><button type='button' class='btn btn-info view-details-btn' data-toggle='modal' data-target='#viewDetailsModal' data-transaction-id='" . $row["transaction_id"] . "'>View Details</button></td></td>";
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
            modal.style.display = "none";
        }

        // When the user clicks outside the modal, close it
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>
    <script>
document.getElementById('addMillingEntry').addEventListener('click', function() {
    var newRow = document.createElement('div');
    newRow.className = 'row';
    
    var quantityCol = document.createElement('div');
    quantityCol.className = 'col-md-4';
    
    var quantityGroup = document.createElement('div');
    quantityGroup.className = 'form-group';
    var quantityLabel = document.createElement('label');
    quantityLabel.innerHTML = '<i class="fa fa-hashtag"></i> Quantity (Sacks)';
    var quantityInput = document.createElement('input');
    quantityInput.type = 'number';
    quantityInput.className = 'form-control';
    quantityInput.name = 'quantity[]'; // Use an array to handle multiple grains
    
    var productTypeCol = document.createElement('div');
    productTypeCol.className = 'col-md-4';
    
    var productTypeGroup = document.createElement('div');
    productTypeGroup.className = 'form-group';
    var productTypeLabel = document.createElement('label');
    productTypeLabel.innerHTML = '<i class="fa fa-cubes"></i> Grain Type';
    var productTypeSelect = document.createElement('select');
    productTypeSelect.className = 'form-control';
    productTypeSelect.name = 'productType[]'; // Use an array to handle multiple grains
    var productTypeOptionDefault = document.createElement('option');
    productTypeOptionDefault.value = '';
    productTypeOptionDefault.disabled = true;
    productTypeOptionDefault.selected = true;
    productTypeOptionDefault.textContent = 'Select Grain Type';
    var productTypeOptionRice = document.createElement('option');
    productTypeOptionRice.value = 'Rice';
    productTypeOptionRice.textContent = 'Rice';
    var productTypeOptionCorn = document.createElement('option');
    productTypeOptionCorn.value = 'Corn';
    productTypeOptionCorn.textContent = 'Corn';
    
    // Milling Variety field
    var millingVarietyCol = document.createElement('div');
    millingVarietyCol.className = 'col-md-4';
    
    var millingVarietyGroup = document.createElement('div');
    millingVarietyGroup.className = 'form-group';
    var millingVarietyLabel = document.createElement('label');
    millingVarietyLabel.innerHTML = '<i class="fa fa-cubes"></i> Milling Variety';
    var millingVarietySelect = document.createElement('select');
    millingVarietySelect.className = 'form-control';
    millingVarietySelect.name = 'millingVariety[]'; // Use an array to handle multiple grains
    var millingVarietyOptionDefault = document.createElement('option');
    millingVarietyOptionDefault.value = '';
    millingVarietyOptionDefault.disabled = true;
    millingVarietyOptionDefault.selected = true;
    millingVarietyOptionDefault.textContent = 'Select Milling Variety';
    var millingVarietyOption1 = document.createElement('option');
    millingVarietyOption1.value = 'White Rice';
    millingVarietyOption1.textContent = 'White Rice';
    var millingVarietyOption2 = document.createElement('option');
    millingVarietyOption2.value = 'Red Rice';
    millingVarietyOption2.textContent = 'Red Rice';
    var millingVarietyOption3 = document.createElement('option');
    millingVarietyOption3.value = 'Cracked Corn';
    millingVarietyOption3.textContent = 'Cracked Corn';
    var millingVarietyOption4 = document.createElement('option');
    millingVarietyOption4.value = 'Yellow Grits';
    millingVarietyOption4.textContent = 'Yellow Grits';
    var millingVarietyOption5 = document.createElement('option');
    millingVarietyOption5.value = 'Yellow Corn Bran';
    millingVarietyOption5.textContent = 'Yellow Corn Bran';
    var millingVarietyOption6 = document.createElement('option');
    millingVarietyOption6.value = 'White Corn Bran';
    millingVarietyOption6.textContent = 'White Corn Bran';
    var millingVarietyOption7 = document.createElement('option');
    millingVarietyOption7.value = 'White Corn Grits #10';
    millingVarietyOption7.textContent = 'White Corn Grits #10';
    var millingVarietyOption8 = document.createElement('option');
    millingVarietyOption8.value = 'White Corn Grits #12';
    millingVarietyOption8.textContent = 'White Corn Grits #12';
    
    // Append all milling variety options
    millingVarietySelect.appendChild(millingVarietyOptionDefault);
    millingVarietySelect.appendChild(millingVarietyOption1);
    millingVarietySelect.appendChild(millingVarietyOption2);
    millingVarietySelect.appendChild(millingVarietyOption3);
    millingVarietySelect.appendChild(millingVarietyOption4);
    millingVarietySelect.appendChild(millingVarietyOption5);
    millingVarietySelect.appendChild(millingVarietyOption6);
    millingVarietySelect.appendChild(millingVarietyOption7);
    millingVarietySelect.appendChild(millingVarietyOption8);
    // Add more options as needed
    
    // Assemble the elements
    millingVarietyGroup.appendChild(millingVarietyLabel);
    millingVarietyGroup.appendChild(millingVarietySelect);
    
    millingVarietyCol.appendChild(millingVarietyGroup);
    newRow.appendChild(millingVarietyCol);
    
    // Append the new row to the multipleGrainsFields div
    document.getElementById('multipleGrainsFields').appendChild(newRow);

    
    // Assemble the elements
    productTypeGroup.appendChild(productTypeLabel);
    productTypeSelect.appendChild(productTypeOptionDefault);
    productTypeSelect.appendChild(productTypeOptionRice);
    productTypeSelect.appendChild(productTypeOptionCorn);
    productTypeGroup.appendChild(productTypeSelect);
    millingVarietyGroup.appendChild(millingVarietyLabel);
    millingVarietySelect.appendChild(millingVarietyOptionDefault);
    quantityGroup.appendChild(quantityLabel);
    quantityGroup.appendChild(quantityInput);
    
    millingVarietyGroup.appendChild(millingVarietySelect);

    productTypeCol.appendChild(productTypeGroup);
    millingVarietyCol.appendChild(millingVarietyGroup);
    quantityCol.appendChild(quantityGroup);

    newRow.appendChild(productTypeCol);
    newRow.appendChild(millingVarietyCol);
    newRow.appendChild(quantityCol);
    
    document.getElementById('multipleGrainsFields').appendChild(newRow);
});
</script>
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
<script>
    // Function to check the quantity against available_quantity
    function checkQuantity() {
        var productType = document.getElementById("productType").value;
        var quantity = document.getElementById("quantity").value;

        // Send an AJAX request to the server to get available_quantity
        $.ajax({
            type: 'POST',
            url: 'check_quantity_milling.php', // Create a PHP script to handle the database query
            data: {
                productType: productType,
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
function getMillingFee(grainType) {
    $.ajax({
        type: 'POST',
        url: 'get_milling_fee.php',
        data: {
            grainType: grainType,
        },
        success: function(data) {
            document.getElementById("millingFee").value = "₱" + parseFloat(data).toFixed(2);
            
            // Call calculateTotalFee after setting the milling fee
            calculateTotalFee();
        },
    });
}
</script>
<script>
function calculateTotalFee() {
    var millingFeeString = document.getElementById("millingFee").value.replace("₱", "");
    var millingFee = parseFloat(millingFeeString.replace(",", "")); // Remove commas for parsing as a number
    var quantity = parseFloat(document.querySelector('input[name="quantity[]"]').value);
    var totalFee = millingFee * quantity;

    if (!isNaN(totalFee)) {
        // Format the totalFee using Number.toLocaleString to add commas
        document.getElementById("totalFee").value = "₱" + totalFee.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        
        // Set the calculated total fee in a hidden input field to be submitted with the form
        document.getElementById("hiddenTotalFee").value = totalFee;
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
            var total_fee = urlParams.get('total_fee');

            // Fetch customer name using an AJAX request or any other method
            $.ajax({
                url: 'get_customer_name.php', // Replace with the actual URL to fetch customer name
                method: 'GET',
                data: { customer_id: customer_id },
                success: function (response) {
                    var customer_name = response;

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
                                <td>₱${total_fee}</td>
                            </tr>
                            <tr>
                                <td><b>Quantity:</b></td>
                                <td>${quantity}kg</td>
                            </tr>
                            <tr>
                                <td><b>Total Price:</b></td>
                                <td>₱${total_fee}</td>
                            </tr>
                        </table>
                        <hr>
                        <p style="text-align: center;">Thank you for availing milling service from Yasay Rice & Corn Milling Company!</p>
                        <br>
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

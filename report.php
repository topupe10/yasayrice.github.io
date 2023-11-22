<?php
session_start();

if (!isset($_SESSION['user_authenticated']) || !$_SESSION['user_authenticated']) {
    // User is not authenticated, redirect to the login page
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';
$activePage = 'reports'; // Set the active page to 'selling'

// Check if start_date and end_date are provided in the URL
if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    // Sanitize and store the user-provided start_date and end_date
    $startDate = $connect->real_escape_string($_GET['start_date']);
    $endDate = $connect->real_escape_string($_GET['end_date']);

    // Modify the SQL query to include date range filtering
    $quickMillingSql = "SELECT qmt.invoice_number, qmt.transaction_id, c.name AS customer_name, c.contact_number, c.address, qmt.transaction_date, qmt.grain_type, qmt.milling_variety, qmt.quantity, qmt.delivery_method, qmt.delivery_date, qmt.date_completed
            FROM millingtransactions qmt
            INNER JOIN customers c ON qmt.customer_id = c.customer_id
            WHERE qmt.transaction_date BETWEEN '$startDate' AND '$endDate'
            AND status = 'Completed'";

    $sellingSql = "SELECT st.transaction_id, c.name AS customer_name, c.contact_number, c.address, st.transaction_date, st.grain_type, st.milling_variety, st.scale_type, st.quantity, st.delivery_method, st.delivery_date, st.status
            FROM sellingtransactions st
            INNER JOIN customers c ON st.customer_id = c.customer_id
            WHERE st.transaction_date BETWEEN '$startDate' AND '$endDate'";

    $buyingSql = "SELECT bt.transaction_id, c.name AS customer_name, c.contact_number, c.address, bt.transaction_date, bt.grain_type, bt.quantity, bt.status
            FROM buyingtransactions bt
            INNER JOIN customers c ON bt.customer_id = c.customer_id
            WHERE bt.transaction_date BETWEEN '$startDate' AND '$endDate'";
    $sellingreplaceSql = "SELECT st.transaction_id, c.name AS customer_name, c.contact_number, c.address, st.transaction_date, st.grain_type, st.milling_variety, st.scale_type, st.quantity, st.delivery_method, st.delivery_date, st.status
            FROM sellingtransactions st
            INNER JOIN customers c ON st.customer_id = c.customer_id
            WHERE st.transaction_date BETWEEN '$startDate' AND '$endDate'
            AND replacement_status = 'Replaced'";
    $sellingredrySql = "SELECT st.transaction_id, c.name AS customer_name, c.contact_number, c.address, st.transaction_date, st.grain_type, st.milling_variety, st.scale_type, st.quantity, st.delivery_method, st.delivery_date, st.status
            FROM sellingtransactions st
            INNER JOIN customers c ON st.customer_id = c.customer_id
            WHERE st.transaction_date BETWEEN '$startDate' AND '$endDate'
            AND redry_status = 'Completed'";
} else {
    // If start_date and end_date are not provided, use the unfiltered query
    $quickMillingSql = "SELECT qmt.invoice_number, qmt.transaction_id, c.name AS customer_name, c.contact_number, c.address, qmt.transaction_date, qmt.grain_type, qmt.milling_variety, qmt.quantity, qmt.delivery_method, qmt.delivery_date, qmt.date_completed
            FROM millingtransactions qmt
            INNER JOIN customers c ON qmt.customer_id = c.customer_id
            AND status = 'Completed'";

    $sellingSql = "SELECT st.transaction_id, c.name AS customer_name, c.contact_number, c.address, st.transaction_date, st.grain_type, st.milling_variety, st.scale_type, st.quantity, st.delivery_method, st.delivery_date, st.status
            FROM sellingtransactions st
            INNER JOIN customers c ON st.customer_id = c.customer_id";
    $sellingreplaceSql = "SELECT st.transaction_id, c.name AS customer_name, c.contact_number, c.address, st.transaction_date, st.grain_type, st.milling_variety, st.scale_type, st.quantity, st.delivery_method, st.delivery_date, st.status
            FROM sellingtransactions st
            INNER JOIN customers c ON st.customer_id = c.customer_id
            AND replacement_status = 'Replaced'";
    $sellingredrySql = "SELECT st.transaction_id, c.name AS customer_name, c.contact_number, c.address, st.transaction_date, st.grain_type, st.milling_variety, st.scale_type, st.quantity, st.delivery_method, st.delivery_date, st.status
            FROM sellingtransactions st
            INNER JOIN customers c ON st.customer_id = c.customer_id
            AND redry_status = 'Completed'";
    $buyingSql = "SELECT bt.transaction_id, c.name AS customer_name, c.contact_number, c.address, bt.transaction_date, bt.grain_type, bt.quantity, bt.status
            FROM buyingtransactions bt
            INNER JOIN customers c ON bt.customer_id = c.customer_id";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Selling Services | Yasay Rice & Corn Milling Management Information System</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include your custom CSS file -->
    <link rel="stylesheet" type="text/css" href="stylesheet/reportsstyle.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
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
<div class="content">
    <ol class="breadcrumb">
            <li><a href="dashboard.php">Dashboard</a></li>
            /
            <li class="active">Reports</li>
        </ol>
        <div class="container-box">
        <div class="button-container">
            <button id="salesButton" type="button" class="btn btn-primary rpt-button">Overall Sales Reports</button>
            <button id="millingButton" type="button" class="btn btn-primary rpt-button">Milling Reports</button>
            <button id="sellingButton" type="button" class="btn btn-primary rpt-button">Selling Reports</button>
            <button id="buyingButton" type="button" class="btn btn-primary rpt-button">Buying Reports</button>
            <button id="returnButton" type="button" class="btn btn-primary rpt-button">Damage Reports</button>
        </div>
        <br>
        <!-- Create filters or date range selection for the report -->
        <form method="GET" action="report.php" class="date-range-form">
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
            <br>
        <!-- Quick Milling Transaction Report -->
        <div id="quickMillingReports">
            <div class="container-box1">
                <h1 id="heading2"><strong>Milling Transactions Report</strong></h1>
                <table class="table" id="adminMillingTable">
                    <thead>
                        <tr>
                            <th>Trans. #</th>
                            <th>Official Receipt #</th>
                            <th>Name</th>
                            <th>Mill Date</th>
                            <th>Type</th>
                            <th>Variety</th>
                            <th>Quantity</th>
                            <th>Completion Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch and display quick milling transaction data with customer details from the database
                        $quickMillingResult = $connect->query($quickMillingSql);
                    
                        if ($quickMillingResult->num_rows > 0) {
                            while ($row = $quickMillingResult->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["transaction_id"] . "</td>";
                                echo "<td>" . $row["invoice_number"] . "</td>";
                                echo "<td>" . $row["customer_name"] . "</td>";
                                echo "<td>" . $row["transaction_date"] . "</td>";
                                echo "<td>" . $row["grain_type"] . "</td>";
                                echo "<td>" . $row["milling_variety"] . "</td>";
                                echo "<td>" . $row["quantity"] . "</td>";
                                echo "<td>" . $row["date_completed"] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='12'>No quick milling transactions found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            <button id="printButton2" class="btn btn-info pull-right">Print PDF</button>
        </div>
    </div>
        <div id="sellingReports">
            <div class="container-box1">
                <h1 id="heading3"><strong>Selling Transactions Report</strong></h1>
                <table class="table" id="SellingTable">
                    <thead>
                        <tr>
                            <th>Trans. ID</th>
                            <th>Name</th>
                            <th>Contact No.</th>
                            <th>Address</th>
                            <th>Trans. Date</th>
                            <th>Grain</th>
                            <th>Variety</th>
                            <th>Scale</th>
                            <th>Quantity</th>
                            <th>Distribution</th>
                            <th>Distribution Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch and display selling transaction data with customer details from the database
                        $result = $connect->query($sellingSql);
                    
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["transaction_id"] . "</td>";
                                echo "<td>" . $row["customer_name"] . "</td>";
                                echo "<td>" . $row["contact_number"] . "</td>";
                                echo "<td>" . $row["address"] . "</td>";
                                echo "<td>" . $row["transaction_date"] . "</td>";
                                echo "<td>" . $row["grain_type"] . "</td>";
                                echo "<td>" . $row["milling_variety"] . "</td>";
                                echo "<td>" . $row["scale_type"] . "</td>";
                                echo "<td>" . $row["quantity"] . "</td>";
                                echo "<td>" . $row["delivery_method"] . "</td>";
                                echo "<td>" . $row["delivery_date"] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='12'>No selling transactions found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            <button id="printButton3" class="btn btn-info pull-right">Print PDF</button>
        </div>
    </div>
        <!-- Buying Transaction Report -->
        <div id="buyingReports">
            <div class="container-box1">
                <h1 id="heading4"><strong>Buying Transactions Report</strong></h1>
                <table class="table" id="BuyingTable">
                    <thead>
                        <tr>
                            <th>Trans. ID</th>
                            <th>Name</th>
                            <th>Contact No.</th>
                            <th>Address</th>
                            <th>Trans. Date</th>
                            <th>Grain Type</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch and display buying transaction data with customer details from the database
                        $buyingResult = $connect->query($buyingSql);
                    
                        if ($buyingResult->num_rows > 0) {
                            while ($row = $buyingResult->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["transaction_id"] . "</td>";
                                echo "<td>" . $row["customer_name"] . "</td>";
                                echo "<td>" . $row["contact_number"] . "</td>";
                                echo "<td>" . $row["address"] . "</td>";
                                echo "<td>" . $row["transaction_date"] . "</td>";
                                echo "<td>" . $row["grain_type"] . "</td>";
                                echo "<td>" . $row["quantity"] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>No buying transactions found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <button id="printButton4" class="btn btn-info pull-right">Print PDF</button>
            </div>
        </div>
    <!-- Damage Transaction Report -->
<div id="returnReports">
            <div class="container-box1">
                <h1 id="heading5"><strong>Replaced Milled Grains Reports</strong></h1>
                <table class="table" id="ReplaceTable">
                    <thead>
                        <tr>
                            <th>Trans. #</th>
                            <th>Name</th>
                            <th>Contact No.</th>
                            <th>Address</th>
                            <th>Trans. Date</th>
                            <th>Grain</th>
                            <th>Variety</th>
                            <th>Scale</th>
                            <th>Quantity</th>
                            <th>Distribution</th>
                            <th>Distribution Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        // Fetch and display selling transaction data with customer details from the database
                        $result = $connect->query($sellingreplaceSql);
                    
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["transaction_id"] . "</td>";
                                echo "<td>" . $row["customer_name"] . "</td>";
                                echo "<td>" . $row["contact_number"] . "</td>";
                                echo "<td>" . $row["address"] . "</td>";
                                echo "<td>" . $row["transaction_date"] . "</td>";
                                echo "<td>" . $row["grain_type"] . "</td>";
                                echo "<td>" . $row["milling_variety"] . "</td>";
                                echo "<td>" . $row["scale_type"] . "</td>";
                                echo "<td>" . $row["quantity"] . "</td>";
                                echo "<td>" . $row["delivery_method"] . "</td>";
                                echo "<td>" . $row["delivery_date"] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='12'>No returns transactions found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <button id="printButton5" class="btn btn-info pull-right">Print PDF</button>
                </div>
                <div class="container-box1">
                <h1 id="heading6"><strong>Redried Milled Grains Reports</strong></h1>
                    <table class="table" id="RedryTable">
                        <thead>
                            <tr>
                                <th>Trans. #</th>
                                <th>Name</th>
                                <th>Contact No.</th>
                                <th>Address</th>
                                <th>Trans. Date</th>
                                <th>Grain</th>
                                <th>Variety</th>
                                <th>Scale</th>
                                <th>Quantity</th>
                                <th>Distribution</th>
                                <th>Distribution Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            // Fetch and display selling transaction data with customer details from the database
                            $result = $connect->query($sellingredrySql);
                        
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row["transaction_id"] . "</td>";
                                    echo "<td>" . $row["customer_name"] . "</td>";
                                    echo "<td>" . $row["contact_number"] . "</td>";
                                    echo "<td>" . $row["address"] . "</td>";
                                    echo "<td>" . $row["transaction_date"] . "</td>";
                                    echo "<td>" . $row["grain_type"] . "</td>";
                                    echo "<td>" . $row["milling_variety"] . "</td>";
                                    echo "<td>" . $row["scale_type"] . "</td>";
                                    echo "<td>" . $row["quantity"] . "</td>";
                                    echo "<td>" . $row["delivery_method"] . "</td>";
                                    echo "<td>" . $row["delivery_date"] . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='12'>No returns transactions found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <button id="printButton6" class="btn btn-info pull-right">Print PDF</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Function to show Milling Reports
    function showMillingReports() {
        document.getElementById("quickMillingReports").style.display = "block";
        document.getElementById("sellingReports").style.display = "none";
        document.getElementById("buyingReports").style.display = "none";
        document.getElementById("returnReports").style.display = "none";

        // Set the active class for the Milling Reports button
        document.getElementById("millingButton").classList.add("active");
        // Remove the active class from other buttons
        document.getElementById("sellingButton").classList.remove("active");
        document.getElementById("buyingButton").classList.remove("active");
        document.getElementById("returnButton").classList.remove("active");
    }

    // Function to show Selling Reports
    function showSellingReports() {
        document.getElementById("quickMillingReports").style.display = "none";
        document.getElementById("sellingReports").style.display = "block";
        document.getElementById("buyingReports").style.display = "none";
        document.getElementById("returnReports").style.display = "none";

        // Set the active class for the Selling Reports button
        document.getElementById("sellingButton").classList.add("active");
        // Remove the active class from other buttons
        document.getElementById("millingButton").classList.remove("active");
        document.getElementById("buyingButton").classList.remove("active");
        document.getElementById("returnButton").classList.remove("active");
    }

    // Function to show Buying Reports
    function showBuyingReports() {
        document.getElementById("quickMillingReports").style.display = "none";
        document.getElementById("sellingReports").style.display = "none";
        document.getElementById("buyingReports").style.display = "block";
        document.getElementById("returnReports").style.display = "none";

        // Set the active class for the Buying Reports button
        document.getElementById("buyingButton").classList.add("active");
        // Remove the active class from other buttons
        document.getElementById("millingButton").classList.remove("active");
        document.getElementById("sellingButton").classList.remove("active");
        document.getElementById("returnButton").classList.remove("active");
        
    }

    // Function to show Buying Reports
    function showReturnReports() {
        document.getElementById("quickMillingReports").style.display = "none";
        document.getElementById("sellingReports").style.display = "none";
        document.getElementById("buyingReports").style.display = "none";
        document.getElementById("returnReports").style.display = "block";

        // Set the active class for the Buying Reports button
        document.getElementById("returnButton").classList.add("active");
        // Remove the active class from other buttons
        document.getElementById("millingButton").classList.remove("active");
        document.getElementById("sellingButton").classList.remove("active");
        document.getElementById("buyingButton").classList.remove("active");
    }

    // Attach click event listeners to the buttons
    document.getElementById("millingButton").addEventListener("click", showMillingReports);
    document.getElementById("sellingButton").addEventListener("click", showSellingReports);
    document.getElementById("buyingButton").addEventListener("click", showBuyingReports);
    document.getElementById("returnButton").addEventListener("click", showReturnReports);

    // Call the showMillingReports function to display Milling Reports by default
    showMillingReports();
});
</script>
<script>
    function printTable(tableId, headingId, companyName, companyAddress) {
        // Style for centering text
        var centerStyle = "text-align: center; margin: 0 auto;";

        var printHeading = "<h2 style='" + centerStyle + "'>" + companyName + "</h2><p style='" + centerStyle + "'>" + companyAddress + "</p>" + document.getElementById(headingId).outerHTML;
        var printContent = document.getElementById(tableId).outerHTML;
        var originalContent = document.body.innerHTML;

        // Combine the heading and table content
        var combinedContent = printHeading + printContent;

        document.body.innerHTML = combinedContent;
        window.print();
        document.body.innerHTML = originalContent;
    }

    // Attach click event handlers to the print buttons
    document.getElementById("printButton1").addEventListener("click", function () {
        printTable("customerMillingTable", "heading1", "Yasay Rice & Corn Mill", "Igpit, Opol, Misamis Oriental 9000");
    });
</script>
<script>
    function printTable(tableId, headingId, companyName, companyAddress) {
        // Style for centering text
        var centerStyle = "text-align: center; margin: 0 auto;";

        var printHeading = "<h2 style='" + centerStyle + "'>" + companyName + "</h2><p style='" + centerStyle + "'>" + companyAddress + "</p>" + document.getElementById(headingId).outerHTML;
        var printContent = document.getElementById(tableId).outerHTML;
        var originalContent = document.body.innerHTML;

        // Combine the heading and table content
        var combinedContent = printHeading + printContent;

        document.body.innerHTML = combinedContent;
        window.print();
        document.body.innerHTML = originalContent;
    }

    // Attach click event handlers to the print buttons
    document.getElementById("printButton2").addEventListener("click", function () {
        printTable("adminMillingTable", "heading2", "Yasay Rice & Corn Mill", "Igpit, Opol, Misamis Oriental 9000");
    });
</script>
<script>
    function printTable(tableId, headingId, companyName, companyAddress) {
        // Style for centering text
        var centerStyle = "text-align: center; margin: 0 auto;";

        var printHeading = "<h2 style='" + centerStyle + "'>" + companyName + "</h2><p style='" + centerStyle + "'>" + companyAddress + "</p>" + document.getElementById(headingId).outerHTML;
        var printContent = document.getElementById(tableId).outerHTML;
        var originalContent = document.body.innerHTML;

        // Combine the heading and table content
        var combinedContent = printHeading + printContent;

        document.body.innerHTML = combinedContent;
        window.print();
        document.body.innerHTML = originalContent;
    }

    // Attach click event handlers to the print buttons
    document.getElementById("printButton3").addEventListener("click", function () {
        printTable("SellingTable", "heading3", "Yasay Rice & Corn Mill", "Igpit, Opol, Misamis Oriental 9000");
    });
</script>
<script>
    function printTable(tableId, headingId, companyName, companyAddress) {
        // Style for centering text
        var centerStyle = "text-align: center; margin: 0 auto;";

        var printHeading = "<h2 style='" + centerStyle + "'>" + companyName + "</h2><p style='" + centerStyle + "'>" + companyAddress + "</p>" + document.getElementById(headingId).outerHTML;
        var printContent = document.getElementById(tableId).outerHTML;
        var originalContent = document.body.innerHTML;

        // Combine the heading and table content
        var combinedContent = printHeading + printContent;

        document.body.innerHTML = combinedContent;
        window.print();
        document.body.innerHTML = originalContent;
    }

    // Attach click event handlers to the print buttons
    document.getElementById("printButton4").addEventListener("click", function () {
        printTable("BuyingTable", "heading4", "Yasay Rice & Corn Mill", "Igpit, Opol, Misamis Oriental 9000");
    });
</script>
<script>
    function printTable(tableId, headingId, companyName, companyAddress) {
        // Style for centering text
        var centerStyle = "text-align: center; margin: 0 auto;";

        var printHeading = "<h2 style='" + centerStyle + "'>" + companyName + "</h2><p style='" + centerStyle + "'>" + companyAddress + "</p>" + document.getElementById(headingId).outerHTML;
        var printContent = document.getElementById(tableId).outerHTML;
        var originalContent = document.body.innerHTML;

        // Combine the heading and table content
        var combinedContent = printHeading + printContent;

        document.body.innerHTML = combinedContent;
        window.print();
        document.body.innerHTML = originalContent;
    }

    // Attach click event handlers to the print buttons
    document.getElementById("printButton5").addEventListener("click", function () {
        printTable("ReplaceTable", "heading5", "Yasay Rice & Corn Mill", "Igpit, Opol, Misamis Oriental 9000");
    });
</script>
<script>
    function printTable(tableId, headingId, companyName, companyAddress) {
        // Style for centering text
        var centerStyle = "text-align: center; margin: 0 auto;";

        var printHeading = "<h2 style='" + centerStyle + "'>" + companyName + "</h2><p style='" + centerStyle + "'>" + companyAddress + "</p>" + document.getElementById(headingId).outerHTML;
        var printContent = document.getElementById(tableId).outerHTML;
        var originalContent = document.body.innerHTML;

        // Combine the heading and table content
        var combinedContent = printHeading + printContent;

        document.body.innerHTML = combinedContent;
        window.print();
        document.body.innerHTML = originalContent;
    }

    // Attach click event handlers to the print buttons
    document.getElementById("printButton6").addEventListener("click", function () {
        printTable("RedryTable", "heading6", "Yasay Rice & Corn Mill", "Igpit, Opol, Misamis Oriental 9000");
    });
</script>
</body>
</html>

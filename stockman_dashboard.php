<?php
session_start();

require_once 'db_connect.php';

// Check if the user is authenticated and has the role of stockman
if (!isset($_SESSION['user_authenticated']) || !$_SESSION['user_authenticated'] || $_SESSION['role'] !== 'stockman') {
    // User is not authenticated or doesn't have the stockman role, redirect to the login page
    header("Location: login.php");
    exit();
}

$activePage = 'dashboard';

// Query to get the count for milling from both tables
$query = "SELECT COUNT(*) AS totalMillingCount 
        FROM (SELECT transaction_id, customer_id, transaction_date, delivery_method, delivery_date, status FROM millingtransactions) 
        AS combined_milling";

$result = $connect->query($query);

if ($result) {
    $row = $result->fetch_assoc();
    $millingCount = $row['totalMillingCount'];
} else {
    echo "Error in query: " . $connect->error;
}
// Query to get the count for selling from the sellingtransactions table
$queryMilling = "SELECT COUNT(*) AS totalOngoingMillingCount FROM millingtransactions WHERE status = 'Ongoing'";

$resultMilling = $connect->query($queryMilling);

if ($resultMilling) {
    $rowMilling = $resultMilling->fetch_assoc();
    $ongoingmillingCount = $rowMilling['totalOngoingMillingCount'];
} else {
    echo "Error in selling query: " . $connect->error;
}
// Query to get the count for buying from the buyingtransactions table
$queryBuying = "SELECT COUNT(*) AS totalBuyingCount FROM buyingtransactions";

$resultBuying = $connect->query($queryBuying);

if ($resultBuying) {
    $rowBuying = $resultBuying->fetch_assoc();
    $buyingCount = $rowBuying['totalBuyingCount'];
} else {
    echo "Error in buying query: " . $connect->error;
}
// Query your database to get the count for inventory
$queryAvailableStock = "SELECT SUM(available_stock) AS totalAvailableStock FROM milledgrains";
$queryAvailableQuantity = "SELECT SUM(available_quantity) AS totalAvailableQuantity FROM grainsstock";

$resultAvailableStock = $connect->query($queryAvailableStock);
$resultAvailableQuantity = $connect->query($queryAvailableQuantity);

if ($resultAvailableStock && $resultAvailableQuantity) {
    $rowAvailableStock = $resultAvailableStock->fetch_assoc();
    $rowAvailableQuantity = $resultAvailableQuantity->fetch_assoc();

    // Combine the counts
    $totalAvailableCount = $rowAvailableStock['totalAvailableStock'] + $rowAvailableQuantity['totalAvailableQuantity'];

    // Format the number
    $formattedCount = number_format($totalAvailableCount); // Adjust the decimal places as needed
} else {
    echo "Error in inventory queries: " . $connect->error;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard | Yasay Rice & Corn Milling Management Information System</title>
    <link rel="stylesheet" type="text/css" href="stylesheet/stockmandashboardstyle.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
<div class="dashboard-content">
    <p>Quicklinks</p>
    <div class="dashboard-quicklinks">
        <div class="quicklink">
            <?php
            // Query your database to get the count for selling
            echo "<h1>$ongoingmillingCount</h1>";
            ?>
            <h2><i class="fas fa-shopping-cart"></i> Ongoing Milling</h2>
            <a href="ongoing_milling.php">
                See More
            </a>
        </div>
        <div class="quicklink">
            <?php
            // Display the count for buying
            echo "<h1>$millingCount</h1>";
            ?>
            <h2><i class="fas fa-list-alt"></i> Milling History</h2>
            <a href="milling_history.php">
                See More
            </a>
        </div>
        <div class="quicklink">
            <?php
            // Display the count for totalinventory in kilos
            echo "<h1>{$formattedCount} kilos</h1>";
            ?>
            <h2><i class="fas fa-boxes"></i> Stocks Inventory</h2>
            <a href="stockman_inventory.php">
                See More
            </a>
        </div>
    </div>
    <br>
    <p>Statistical Graphs</p>
    <!-- Chart Section -->
    <div class="dashboard-charts">
    <!-- Inventory Bar Chart -->
    <div class="dashboard-chart">
        <div class="chart-container">
            <canvas id="inventoryChart" width="200" height="200"></canvas>
            <h3>Stocks Inventory Chart</h3>
        </div>
    </div>
</div>
<!-- Chart Scripts -->
<script>
    // Inventory Bar Chart
    var ctxInventory = document.getElementById('inventoryChart').getContext('2d');
    var totalAvailableStock = <?php echo $rowAvailableStock['totalAvailableStock']; ?>;
    var totalAvailableQuantity = <?php echo $rowAvailableQuantity['totalAvailableQuantity']; ?>;
    
    // Combine the counts
    var totalAvailableCount = totalAvailableStock + totalAvailableQuantity;
    
    // Set a ceiling of 9000
    var chartCeiling = 9000;
    var displayCount = Math.min(totalAvailableCount, chartCeiling);
    
    var wheatIcon = new Image();
    wheatIcon.src = 'path/to/wheat-icon.png';  // Replace with the actual path to your wheat icon
    
    var inventoryChart = new Chart(ctxInventory, {
        type: 'bar',
        data: {
            labels: ['Milled Grains Stock (Kilos)', 'Raw Grains (Pasi) Stock Quantity (Kilos)'],
            datasets: [{
                label: 'Key Metrics',
                data: [
                    totalAvailableStock,
                    totalAvailableQuantity
                ],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 99, 132, 0.2)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max: chartCeiling, // Set the y-axis max value
                    ticks: {
                        callback: function(value, index, values) {
                            return value + ' Kilos';
                        }
                    }
                }
            },
        }
    });
</script>
</div>
</body>
</html>

<?php
session_start();

if (!isset($_SESSION['user_authenticated']) || !$_SESSION['user_authenticated']) {
    // User is not authenticated, redirect to the login page
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';
$activePage = 'inventory'; // Set the active page to 'inventory'

// Fetch data for remaining grains inventory
$sqlGrains = "SELECT grain_type, SUM(stock_in_quantity) AS stock_in_quantity, SUM(available_quantity) AS available_quantity FROM grainsstock GROUP BY grain_type";
$resultGrains = $connect->query($sqlGrains);

// Fetch data for remaining milled products inventory
$sqlMilledGrains = "SELECT grain_type, variety, available_stock FROM milledgrains";
$resultMilledGrains = $connect->query($sqlMilledGrains);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory - Rice and Corn Milling Company</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include your custom CSS file -->
    <link rel="stylesheet" type="text/css" href="stylesheet/inventorystyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha384-..." crossorigin="anonymous">
</head>
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
<body>
<div class="content">
<ol class="breadcrumb">
    <li><a href="stockman_dashboard.php">Dashboard</a></li>
    /
    <li class="active">Inventory</li>
</ol>
<div class="container-box">
    <br>
        <button id="grainsButton" class="toggle-button active" onclick="toggleTable('grainsTable')">Show Grains Inventory</button>
        <button id="milledGrainsButton" class="toggle-button" onclick="toggleTable('milledGrainsTable')">Show Milled Grains Inventory</button>
    <br>
    <br>
    <div class="container-box1">
    <table id="grainsTable" class="table table-bordered special-table">
        <thead>
            <tr id="grains-variety-header">
                <th style="border: none;"></th>
                <th style="border: none;" colspan="2">Grains Stock Inventory</th>
                <th style="border: none;"></th>
            </tr>
            <tr id="grains-variety-header2">
                <th>Grain Type</th>
                <th>Stock-in Quantity</th>
                <th>Remaining Stocks</th>
                <th>Milled Grains</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalRemainingStocks = 0; // Initialize a variable to store the total remaining stocks
            $milledQuantity = 0;

            while ($row = $resultGrains->fetch_assoc()) {
                $stockInQuantity = $row["stock_in_quantity"];
                $availableQuantity = $row["available_quantity"];
                $remainingStock = $stockInQuantity - $availableQuantity;
                $milledQuantity += $availableQuantity;

                // Add the remaining stock to the total remaining stocks
                $totalRemainingStocks += $remainingStock;

                echo "<tr>";
                echo "<td>" . $row["grain_type"] . "</td>";
                echo "<td>" . number_format($stockInQuantity) . " kg</td>";
                echo "<td>" . number_format($remainingStock) . " kg</td>";
                echo "<td>" . number_format($availableQuantity) . " kg</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td style="border:none;"></td>
                <td style="border:none;"><b>Total Stocks Remaining:</b></td>
                <td style="border:none;"><b><?php echo number_format($totalRemainingStocks); ?> kg</b></td>
                <td style="border:none;"><b><?php echo number_format($milledQuantity); ?> kg</b></</td>
            </tr>
        </tfoot>
    </table>
    
    <table id="milledGrainsTable" class="table table-bordered special-table hidden-table">
        <thead>
            <tr id="grains-variety-header">
                <th style="border: none;"></th>
                <th style="border: none;" colspan="2">Milled Grains Stock Inventory</th>
            </tr>
            <tr id="grains-variety-header2">
                <th>Grain Type</th>
                <th>Variety</th>
                <th>Remaining Stock</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalRemainingStockSack = 0; // Initialize a variable to store the total remaining stock in sacks
            $totalRemainingStockKilo = 0; // Initialize a variable to store the total remaining stock in kilos

            while ($row = $resultMilledGrains->fetch_assoc()) {

                // Calculate the kilos based on your logic (45 available_stock = 1 sack)
                $available_stock = $row['available_stock'];

                // Add the remaining stock to the total remaining stock in both kilos and sacks
                $totalRemainingStockSack += $available_stock;

                echo "<tr>";
                echo "<td>" . $row["grain_type"] . "</td>";
                echo "<td>" . $row["variety"] . "</td>";
                echo "<td>" . number_format($available_stock) . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td style="border:none;"></td>
                <td style="border:none;"><b>Total Stocks Remaining:</b></td>
                <td style="border:none;"><b><?php echo number_format($totalRemainingStockSack); ?> sacks</b></td>
            </tr>
        </tfoot>
    </table>
</div>
</div>
</div>
<script>
        function toggleTable(tableId) {
    var table = document.getElementById(tableId);
    var otherTableId = tableId === 'grainsTable' ? 'milledGrainsTable' : 'grainsTable';
    var otherTable = document.getElementById(otherTableId);
    var activeButton = tableId === 'grainsTable' ? 'grainsButton' : 'milledGrainsButton';
    var otherButton = tableId === 'grainsTable' ? 'milledGrainsButton' : 'grainsButton';

    if (!table.classList.contains('active')) {
        table.classList.add('active');
        otherTable.classList.remove('active');
        table.style.display = 'table';
        otherTable.style.display = 'none';

        document.getElementById(activeButton).classList.add('active');
        document.getElementById(otherButton).classList.remove('active');
    }
}
    </script>
</body>
</html>

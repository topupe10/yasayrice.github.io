<?php
require_once 'db_connect.php';

// Check the connection
if ($connect->connect_errno) {
    die("Connection failed: " . $connect->connect_error);
}

// Rest of your code to fetch data and create the chart
$query = "SELECT grain_type, COUNT(*) AS count FROM millingtransactions GROUP BY grain_type";
$result = $connect->query($query);

$labels = [];
$data = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['grain_type'];
    $data[] = $row['count'];
}
?>

<script>
    // JavaScript code to create the pie chart
    var pieChartData = {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            data: <?php echo json_encode($data); ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.5)',
                'rgba(255, 206, 86, 0.5)',
                'rgba(75, 192, 192, 0.5)',
                'rgba(54, 162, 235, 0.5)',
            ]
        }]
    };

    var millingPieChart = document.getElementById('millingPieChart').getContext('2d');

    var myPieChart = new Chart(millingPieChart, {
        type: 'pie',
        data: pieChartData
    });
</script>

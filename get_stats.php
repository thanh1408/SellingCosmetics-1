<?php
session_start();
require_once "connect.php";

if ($conn->connect_error) {
    die(json_encode(['error' => 'Kết nối thất bại: ' . $conn->connect_error]));
}

$monthlyRevenue = [];
$resultRevenue = $conn->query("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') AS month,
        SUM(final_total) AS revenue
    FROM orders
    WHERE YEAR(created_at) = YEAR(CURDATE())
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month ASC
");
if ($resultRevenue) {
    while ($row = $resultRevenue->fetch_assoc()) {
        $monthlyRevenue[] = [
            'month' => $row['month'],
            'revenue' => (float)$row['revenue']
        ];
    }
}

header('Content-Type: application/json');
echo json_encode([
    'monthlyRevenue' => $monthlyRevenue
]);

$conn->close();
?>

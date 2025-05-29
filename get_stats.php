<?php
session_start();
require_once "connect.php";

if ($conn->connect_error) {
    die(json_encode(['error' => 'Kết nối thất bại: ' . $conn->connect_error]));
}

$totalOrders = 0;
$itemsSold = 0;
$itemsCommented = 0;

$resultOrders = $conn->query("SELECT COUNT(*) as total FROM orders");
if ($resultOrders) $totalOrders = $resultOrders->fetch_assoc()['total'];

$resultProducts = $conn->query("SELECT SUM(quantity) as sold FROM order_items");
if ($resultProducts) $itemsSold = $resultProducts->fetch_assoc()['sold'] ?? 0;

$resultReviews = $conn->query("SELECT COUNT(*) as reviewed FROM product_reviews");
if ($resultReviews) $itemsCommented = $resultReviews->fetch_assoc()['reviewed'];

header('Content-Type: application/json');
echo json_encode([
    'totalOrders' => $totalOrders,
    'itemsSold' => $itemsSold,
    'itemsCommented' => $itemsCommented
]);

$conn->close();
?>
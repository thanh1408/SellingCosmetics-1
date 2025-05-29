<?php
session_start();
require_once "connect.php";

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Kiểm tra order_id
if (!isset($_POST['order_id'])) {
    echo "<h3 style='color:red;text-align:center;'>Lỗi: Không tìm thấy đơn hàng.</h3>";
    exit();
}

$order_id = $_POST['order_id'];
$bank = $_POST['bank'] ?? ''; // Ngân hàng được chọn (nếu có)

// Kiểm tra đơn hàng
$sql = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo "<h3 style='color:red;text-align:center;'>Lỗi: Đơn hàng không tồn tại hoặc không thuộc về bạn.</h3>";
    exit();
}

// Kiểm tra voucher_id trong đơn hàng
if ($order['voucher_id']) {
    $sql = "SELECT id FROM vouchers WHERE id = ? AND is_active = 1 AND expires_at > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order['voucher_id']);
    $stmt->execute();
    $voucher = $stmt->get_result()->fetch_assoc();
    if (!$voucher) {
        // Nếu voucher không hợp lệ, đặt voucher_id thành NULL
        $sql = "UPDATE orders SET voucher_id = NULL WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
    }
}

// Giả lập logic xử lý thanh toán (thay bằng API thanh toán thực tế)
$payment_success = true; // Thay bằng kiểm tra thực tế từ cổng thanh toán

if ($payment_success) {
    // Cập nhật trạng thái đơn hàng
    $sql = "UPDATE orders SET status = 'Đã thanh toán', payment_method = 'Online' WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
    $stmt->execute();

    // Xóa các mục trong cart_items dựa trên session_id
    $session_id = session_id();
    $sql = "DELETE FROM cart_items WHERE session_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $session_id);
    $stmt->execute();

    // Xóa session sau khi thanh toán thành công
    unset($_SESSION['checkout_items']);
    unset($_SESSION['order_temp']);

    // Chuyển hướng đến trang xác nhận thanh toán thành công
    header("Location: payment_success.php?order_id=$order_id");
    exit();
} else {
    // Chuyển hướng đến trang lỗi thanh toán
    header("Location: payment_failed.php?order_id=$order_id");
    exit();
}

$stmt->close();
$conn->close();
?>
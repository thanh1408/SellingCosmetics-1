<?php
session_start();
require_once "connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Lấy danh sách voucher
$user_id = $_SESSION['user_id'];
$sql = "SELECT v.id, v.code, v.discount, v.discount_type, v.min_order_value
        FROM user_vouchers uv
        JOIN vouchers v ON uv.voucher_id = v.id
        WHERE uv.user_id = ? AND v.is_active = 1 AND v.expires_at > NOW()";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$vouchers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Lấy địa chỉ (không cần thiết ngay tại đây vì sẽ xử lý ở delivery.php)
$sql = "SELECT * FROM dia_chi WHERE user_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$address = $stmt->fetch();

$productVariants = [
    // skicare
    1 => [
        ['name' => '130g trắng', 'img' => 'assets/images/p11.jpg', 'price' => 40000],
        ['name' => '130g xanh', 'img' => 'assets/images/p12.jpg', 'price' => 40000]
    ],
    2 => [
        ['name' => '88ml', 'img' => 'assets/images/p23.jpg', 'price' => 88000],
        ['name' => '236ml', 'img' => 'assets/images/p24.jpg', 'price' => 284000]
    ],
    3 => [
        ['name' => 'Vàng', 'img' => 'assets/images/p34.jpg', 'price' => 295000],
        ['name' => 'Đen', 'img' => 'assets/images/p35.jpg', 'price' => 295000],
        ['name' => 'Trắng', 'img' => 'assets/images/p36.jpg', 'price' => 295000]
    ],
    4 => [
        ['name' => 'A01#', 'img' => 'assets/images/p41.jpg', 'price' => 36300],
        ['name' => 'A01#', 'img' => 'assets/images/p42.jpg', 'price' => 36300]
    ],
    5 => [
        ['name' => '10ml', 'img' => 'assets/images/p5.jpg', 'price' => 311000],
        ['name' => '30ml', 'img' => 'assets/images/p5.jpg', 'price' => 651000]
    ],
    6 => [
        ['name' => 'JUICY 20 + Glas 16', 'img' => 'assets/images/p61.jpg', 'price' => 269000],
        ['name' => 'JUICY 23 + Glas 16', 'img' => 'assets/images/p62.jpg', 'price' => 269000]
    ],
    7 => [
        ['name' => '60ml', 'img' => 'assets/images/p7.jpg', 'price' => 65600]
    ],
    8 => [
        ['name' => 'Sạch da giảm nhờn', 'img' => 'assets/images/p81.jpg', 'price' => 254000],
        ['name' => 'Dịu nhẹ da nhạy', 'img' => 'assets/images/p82.jpg', 'price' => 254000],
        ['name' => 'Dành cho da sạm', 'img' => 'assets/images/p83.jpg', 'price' => 254000]
    ],
    9 => [
        ['name' => 'NTT + SRM', 'img' => 'assets/images/p91.jpg', 'price' => 165000],
        ['name' => 'NTT + KD', 'img' => 'assets/images/p92.jpg', 'price' => 120000],
        ['name' => 'SRM + KD', 'img' => 'assets/images/p93.jpg', 'price' => 139000]
    ],
    10 => [
        ['name' => 'Taupe', 'img' => 'assets/images/p101.jpg', 'price' => 264000],
        ['name' => 'Best ever', 'img' => 'assets/images/p102.jpg', 'price' => 264000]
    ],
    // haircare
    31 => [
        ['name' => 'XBUOI_SVANG', 'img' => 'assets/images/p312.jpg', 'price' => 255150],
        ['name' => 'XBUOI_SHONG', 'img' => 'assets/images/p313.jpg', 'price' => 267300]
    ],
    32 => [
        ['name' => 'Lẻ 1 chai', 'img' => 'assets/images/p322.jpg', 'price' => 314000],
        ['name' => 'Combo 2 chai', 'img' => 'assets/images/p323.jpg', 'price' => 589000]
    ],
    // perfume
    41 => [
        ['name' => '10ml', 'img' => 'assets/images/p411.jpg', 'price' => 369000],
        ['name' => '30ml', 'img' => 'assets/images/p412.jpg', 'price' => 507340]
    ],
    42 => [
        ['name' => '30ml', 'img' => 'assets/images/p421.jpg', 'price' => 960000],
        ['name' => '50ml', 'img' => 'assets/images/p422.jpg', 'price' => 576000]
    ],
    43 => [
        ['name' => '50ml', 'img' => 'assets/images/p431.jpg', 'price' => 1480000],
        ['name' => '100ml', 'img' => 'assets/images/p432.jpg', 'price' => 740000]
    ],
    44 => [
        ['name' => '30ml', 'img' => 'assets/images/p441.jpg', 'price' => 350000],
        ['name' => '60ml', 'img' => 'assets/images/p442.jpg', 'price' => 700000]
    ],
    45 => [
        ['name' => '40ml', 'img' => 'assets/images/p451.jpg', 'price' => 560000],
        ['name' => '70ml', 'img' => 'assets/images/p452.jpg', 'price' => 980000]
    ],
    46 => [
        ['name' => '30ml', 'img' => 'assets/images/p461.jpg', 'price' => 774630],
        ['name' => '50ml', 'img' => 'assets/images/p462.jpg', 'price' => 1291050]
    ],
    47 => [
        ['name' => '60ml', 'img' => 'assets/images/p471.jpg', 'price' => 82650],
        ['name' => '100ml', 'img' => 'assets/images/p472.jpg', 'price' => 137750]
    ],
    48 => [
        ['name' => '30ml', 'img' => 'assets/images/p481.jpg', 'price' => 1800000],
        ['name' => '60ml', 'img' => 'assets/images/p482.jpg', 'price' => 3600000]
    ],
    49 => [
        ['name' => '30ml', 'img' => 'assets/images/p491.jpg', 'price' => 240000],
        ['name' => '50ml', 'img' => 'assets/images/p492.jpg', 'price' => 400000]
    ],
    50 => [
        ['name' => '20ml', 'img' => 'assets/images/p501.jpg', 'price' => 270000],
        ['name' => '50ml', 'img' => 'assets/images/p502.jpg', 'price' => 675000]
    ]
];

// Khởi tạo mảng cart_items
$cart_items = $_SESSION['checkout_items'] ?? [];

// Xử lý dữ liệu từ "Mua ngay" hoặc "Thêm vào giỏ"
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['items']) && !empty($_POST['items'])) {
        $cart_items = [];
        foreach ($_POST['items'] as $index => $item) {
            $product_id = isset($item['product_id']) ? (int)$item['product_id'] : 0;
            $product_name = $item['product_name'] ?? '';
            $product_price = isset($item['product_price']) ? (float)$item['product_price'] : 0;
            $product_option = $item['product_option'] ?? '';
            $product_img = $item['product_img'] ?? '';
            $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 1;

            // Cập nhật giá dựa trên productVariants nếu có
            if (isset($productVariants[$product_id])) {
                foreach ($productVariants[$product_id] as $variant) {
                    if ($variant['name'] === $product_option) {
                        $product_price = $variant['price'];
                        $product_img = $variant['img'];
                        break;
                    }
                }
            }

            $cart_items[$index] = [
                'product_id' => $product_id,
                'product_name' => $product_name,
                'product_price' => $product_price,
                'product_option' => $product_option,
                'product_img' => $product_img,
                'quantity' => $quantity
            ];
        }

        $voucher_id = $_POST['voucher_id'] ?? 0;
        $_SESSION['selected_voucher_id'] = $voucher_id;

        $_SESSION['checkout_items'] = $cart_items;
        $_SESSION['order_temp'] = $cart_items;

        if (empty($cart_items)) {
            header("Location: checkout.php?error=missing_items");
            exit();
        }

        // Kiểm tra phân loại
        foreach ($cart_items as $item) {
            if (empty($item['product_option']) && isset($productVariants[$item['product_id']]) && count($productVariants[$item['product_id']]) > 0) {
                header("Location: checkout.php?error=missing_option");
                exit();
            }
        }

        // Nếu là "Xác nhận mua" (từ giỏ hàng hoặc sau khi chọn voucher)
        if (isset($_POST['confirm_checkout'])) {
            header("Location: delivery.php");
            exit();
        }
    }
}

// Tính tổng tiền
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['product_price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Thanh Toán - Luna Beauty</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #fff5f7 0%, #f8e9ec 100%);
        color: #333;
        min-height: 100vh;
        padding: 40px 20px;
    }

    .checkout-container {
        background: #fff;
        max-width: 800px;
        margin: 0 auto;
        padding: 40px;
        border-radius: 24px;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .checkout-container:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
    }

    .product-header {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 32px;
        margin-bottom: 32px;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 24px;
    }

    .product-header img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .product-details h2 {
        font-size: 28px;
        color: #333;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .product-details p {
        font-size: 16px;
        color: #555;
        margin: 8px 0;
    }

    .option-group {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin: 16px 0;
    }

    .option-group button {
        padding: 12px 20px;
        border: 2px solid #e0e0e0;
        background: #fff;
        border-radius: 12px;
        font-size: 15px;
        color: #333;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .option-group button:hover,
    .option-group button.active {
        background: #ff6b81;
        color: #fff;
        border-color: #ff6b81;
        box-shadow: 0 4px 12px rgba(255, 107, 129, 0.3);
    }

    .qty-controls {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 16px 0;
    }

    .qty-controls button {
        width: 40px;
        height: 40px;
        border: 2px solid #e0e0e0;
        background: #fff;
        font-size: 20px;
        border-radius: 50%;
        color: #ff6b81;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .qty-controls button:hover {
        background: #ff6b81;
        color: #fff;
        border-color: #ff6b81;
    }

    .qty-controls input {
        width: 60px;
        text-align: center;
        font-size: 16px;
        border: 2px solid #e0e0e0;
        height: 40px;
        border-radius: 12px;
        background: #fff;
        color: #333;
    }

    .voucher-group {
        margin: 16px 0;
    }

    .voucher-group label {
        font-size: 16px;
        color: #333;
        font-weight: 500;
        margin-bottom: 8px;
        display: block;
    }

    .voucher-group select {
        width: 100%;
        padding: 12px;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        font-size: 15px;
        color: #333;
        background: #fff;
        cursor: pointer;
        transition: border-color 0.3s ease;
    }

    .voucher-group select:focus {
        border-color: #ff6b81;
        outline: none;
    }

    .total {
        font-size: 20px;
        font-weight: 700;
        color: #ff0000;
        margin-top: 24px;
    }

    .checkout-btn {
        width: 100%;
        padding: 16px;
        background: linear-gradient(90deg, #ff6b81 0%, #ff8e53 100%);
        color: #fff;
        border: none;
        border-radius: 50px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        margin-top: 32px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .checkout-btn:hover {
        background: linear-gradient(90deg, #ff4d68 0%, #ff7036 100%);
        box-shadow: 0 8px 24px rgba(255, 107, 129, 0.5);
        transform: translateY(-3px);
    }

    @media (max-width: 1024px) {
        .checkout-container {
            max-width: 700px;
            padding: 32px;
        }

        .product-header {
            grid-template-columns: 180px 1fr;
            gap: 24px;
        }

        .product-header img {
            height: 180px;
        }
    }

    @media (max-width: 768px) {
        body {
            padding: 24px 16px;
        }

        .checkout-container {
            padding: 24px;
        }

        .product-header {
            grid-template-columns: 150px 1fr;
            gap: 20px;
        }

        .product-header img {
            height: 150px;
        }

        .product-details h2 {
            font-size: 24px;
        }

        .product-details p {
            font-size: 15px;
        }

        .option-group button {
            padding: 10px 16px;
            font-size: 14px;
        }

        .qty-controls button {
            width: 36px;
            height: 36px;
            font-size: 18px;
        }

        .qty-controls input {
            width: 50px;
            height: 36px;
            font-size: 15px;
        }

        .total {
            font-size: 18px;
        }

        .checkout-btn {
            padding: 14px;
            font-size: 15px;
        }
    }

    @media (max-width: 600px) {
        body {
            padding: 16px;
        }

        .checkout-container {
            padding: 16px;
        }

        .product-header {
            grid-template-columns: 1fr;
            gap: 16px;
            text-align: center;
        }

        .product-header img {
            max-width: 200px;
            height: 140px;
            margin: 0 auto;
        }

        .product-details h2 {
            font-size: 20px;
        }

        .product-details p {
            font-size: 14px;
        }

        .option-group {
            justify-content: center;
        }

        .option-group button {
            padding: 8px 12px;
            font-size: 13px;
        }

        .qty-controls button {
            width: 32px;
            height: 32px;
            font-size: 16px;
        }

        .qty-controls input {
            width: 45px;
            height: 32px;
            font-size: 14px;
        }

        .total {
            font-size: 16px;
        }

        .checkout-btn {
            padding: 12px;
            font-size: 14px;
        }
    }
</style>

<body>
    <div class="checkout-container">
        <form method="POST">
            <?php if (!empty($cart_items)): ?>
                <h2>Thông tin đơn hàng</h2>
                <?php foreach ($cart_items as $id => $item): ?>
                    <div class="product-header">
                        <img src="<?= htmlspecialchars($item['product_img']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>">
                        <div class="product-details">
                            <h2><?= htmlspecialchars($item['product_name']) ?></h2>
                            <p>Giá: <span><?= number_format($item['product_price']) ?></span>đ</p>
                            <p><strong>Phân loại:</strong> <?= htmlspecialchars($item['product_option']) ?></p>
                            <p><strong>Số lượng:</strong> <?= $item['quantity'] ?></p>
                        </div>
                    </div>
                    <input type="hidden" name="items[<?= $id ?>][product_id]" value="<?= htmlspecialchars($item['product_id'] ?? '') ?>">
                    <input type="hidden" name="items[<?= $id ?>][product_name]" value="<?= htmlspecialchars($item['product_name']) ?>">
                    <input type="hidden" name="items[<?= $id ?>][product_price]" value="<?= $item['product_price'] ?>">
                    <input type="hidden" name="items[<?= $id ?>][product_img]" value="<?= htmlspecialchars($item['product_img']) ?>">
                    <input type="hidden" name="items[<?= $id ?>][product_option]" value="<?= htmlspecialchars($item['product_option']) ?>">
                    <input type="hidden" name="items[<?= $id ?>][quantity]" value="<?= $item['quantity'] ?>">
                <?php endforeach; ?>
            <?php else: ?>
                <p>Không có sản phẩm nào trong đơn hàng.</p>
            <?php endif; ?>
            <div class="voucher-group">
                <label for="voucher_id">Áp dụng voucher giảm giá</label>
                <select id="voucher_id" name="voucher_id" onchange="updateTotal()">
                    <option value="0">Không sử dụng voucher</option>
                    <?php foreach ($vouchers as $voucher): ?>
                        <option value="<?= $voucher['id'] ?>"
                            data-discount="<?= $voucher['discount'] ?>"
                            data-type="<?= $voucher['discount_type'] ?>"
                            data-min="<?= $voucher['min_order_value'] ?>">
                            <?= htmlspecialchars($voucher['code']) ?> - Giảm
                            <?= $voucher['discount_type'] === 'percentage' ? ($voucher['discount'] * 100) . '%' : number_format($voucher['discount']) . 'đ' ?>
                            (Đơn tối thiểu: <?= number_format($voucher['min_order_value']) ?>đ)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <p class="total">Tổng cộng: <span id="total"><?= number_format($total) ?></span>đ</p>
            <input type="hidden" name="confirm_checkout" value="1">
            <button type="submit" class="checkout-btn"><i class="fas fa-check-circle"></i> Xác nhận mua</button>
        </form>
    </div>

    <script>
        const productVariants = <?= json_encode($productVariants) ?>;
        function updateTotal() {
            let subtotal = <?= $total ?>;
            let voucherId = document.getElementById("voucher_id").value;
            let discount = 0;

            if (voucherId !== "0") {
                let selectedOption = document.querySelector(`#voucher_id option[value="${voucherId}"]`);
                let discountValue = parseFloat(selectedOption.getAttribute("data-discount"));
                let discountType = selectedOption.getAttribute("data-type");
                let minOrder = parseFloat(selectedOption.getAttribute("data-min"));

                if (subtotal >= minOrder) {
                    if (discountType === "percentage") {
                        discount = subtotal * discountValue;
                    } else {
                        discount = discountValue;
                    }
                }
            }

            let total = subtotal - discount;
            document.getElementById("total").innerText = total.toLocaleString('vi-VN');
        }

        window.onload = () => {
            updateTotal();
        };
    </script>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
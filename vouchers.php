
<?php
session_start();
require_once 'connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=vouchers.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Lấy danh sách voucher
$sql = "SELECT v.id, v.code, v.discount, v.discount_type, v.min_order_value, v.expires_at, 
        uv.id AS user_voucher_id
        FROM vouchers v
        LEFT JOIN user_vouchers uv ON v.id = uv.voucher_id AND uv.user_id = ?
        WHERE v.is_active = 1 AND v.expires_at > NOW()";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$vouchers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1200">
    <title>Thu Thập Voucher - Luna Beauty</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="./assets/fonts/fontawesome-free-6.4.0-web/fontawesome-free-6.4.0-web/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'custom-pink': '#e84a70',
                    }
                }
            }
        }
    </script>
    <style>
        body { 
            width: 1200px; 
            margin: 0 auto; 
            font-family: Arial, Helvetica, sans-serif; 
            background: #f9fafb;
        }
       
        .container { 
            padding: 160px 0 40px; /* Thêm padding-top để tránh header */
            min-height: 500px;
            width: 1200px;
            margin: 0 auto;
        }
        .voucher-grid { 
            display: flex; 
            flex-wrap: wrap; 
            gap: 20px; 
            
        }
        .voucher-card { 
            width: 380px; 
            background: #ffffff; 
            border: 1px solid #e5e7eb; 
            border-radius: 8px; 
            padding: 20px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
            box-sizing: border-box;
        }
        .voucher-header { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            margin-bottom: 12px;
        }
        .voucher-icon { 
            color: #e84a70; 
        }
        .voucher-title { 
            font-size: 18px; 
            font-weight: bold; 
            color: #1f2937;
        }
        .voucher-details { 
            font-size: 14px; 
            color: #4b5563; 
            margin-bottom: 12px;
            line-height: 1.5;
        }
        .voucher-button { 
            width: 100%; 
            padding: 10px; 
            background: #e84a70; 
            color: #ffffff; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 14px;
            transition: background 0.2s;
        }
        .voucher-button:hover:not(:disabled) {
            background: #d43a60;
        }
        .voucher-button:disabled { 
            background: #d1d5db; 
            cursor: not-allowed;
        }
        .voucher-message { 
            margin-top: 20px; 
            font-size: 14px; 
            text-align: center;
            min-height: 20px;
        }
        
        
    </style>
</head>
<body>
    <?php
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $session_id = session_id();
    $cart_count = 0;

    // Truy vấn tổng số lượng sản phẩm trong giỏ hàng
    $sql = "SELECT SUM(quantity) AS total_quantity FROM cart_items WHERE session_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $cart_count = $row['total_quantity'] ?? 0;
    }
    ?>
    <!-- Header -->
    <header>
        <!-- Top info bar -->
        <div class="top-info">
            <div class="left"></div>
            <div class="right">
                <?php
                if (isset($_SESSION['username'])) {
                    echo "<span>Xin chào <strong>{$_SESSION['username']}</strong></span>";
                } else {
                    echo '<a href="login.php">Bạn chưa đăng nhập</a>';
                }
                ?>
            </div>
        </div>

        <!-- Logo + search bar + cart -->
        <div class="topbar">
            <a href="home.php" class="logo">
                <img src="assets/images/logo.jpg" alt="Mỹ Phẩm 563" style="height: 90px;">
            </a>
            <form class="search-box" method="GET" action="search.php">
                <input type="text" name="query" placeholder="Tìm kiếm sản phẩm..." required>
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>

            <div class="icon-container">
                <a href="cart.php" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count"><?php echo $cart_count; ?></span>
                </a>


                <a href="javascript:void(0)" class="setting-icon" onclick="toggleSettings()">
                    <i class="fa-solid fa-gear"></i>
                </a>
            </div>
            <div class="settings-page">
                <div class="settings-header">
                    <i class="fa-solid fa-arrow-left" onclick="closeSettings()"></i>
                    <h2>Thiết lập tài khoản</h2>
                </div>

                <div class="settings-section">
                    <div class="settings-title">Tài khoản của tôi</div>
                    <a href="account.php" class="settings-item">Tài khoản & Bảo mật</a>
                    <a href="change_address.php" class="settings-item">Địa Chỉ</a>
                    <a href="bank.php" class="settings-item">Tài khoản / Thẻ ngân hàng</a>
                </div>


                <div class="settings-section">
                    <div class="settings-title">Cài đặt</div>
                    <div class="settings-item">Cài đặt Chat</div>
                    <a href="noti.php" class="settings-item">Cài đặt thông báo</a>
                    <div class="settings-item">Cài đặt riêng tư</div>
                    <div class="settings-item">
                        Ngôn ngữ / Language
                        <div class="subtext">Tiếng Việt</div>
                    </div>
                </div>

                <div class="settings-logout">
                    <a href="logout.php">
                        <button>Đăng xuất</button>
                    </a>
                </div>

            </div>

        </div>

        </div>
        <!-- Navbar -->
        <nav class="navbar">
            <a href="home.php"><i class="fa-solid fa-house"></i></a>
            <a href="#" onclick="openGioiThieu()">Giới thiệu</a>
            <a href="#" onclick="openDichVu()">Dịch vụ</a>
            <a href="register.php">Đăng ký</a>
            <a href="login.php">Đăng nhập</a>
            <a href="vouchers.php">Voucher</a>
            <a href="contact.php">Liên hệ</a>
        </nav>
        <!-- Khung giới thiệu -->
        <div id="gioiThieuBox" style="display: none; background:rgb(255, 240, 245); padding: 20px; color: black; border-radius: 4px; position: relative; margin-top : 16px">
            <!-- Nút đóng -->
            <span onclick="closeGioiThieu()" style="position: absolute; top: 10px; right: 20px; font-size: 24px; cursor: pointer;">&times;</span>

            <h2>🌸 Giới thiệu về <strong>Luna Beauty</strong></h2>
            <p>Chào bạn đến với <strong>Luna Beauty</strong> – thế giới mỹ phẩm nơi vẻ đẹp tự nhiên được tôn vinh mỗi ngày!</p>
            <p><strong>Luna Beauty</strong> được thành lập với mong muốn mang đến cho bạn những sản phẩm chăm sóc da chính hãng, an toàn và hiệu quả...</p>
            <ul>
                <li>Sản phẩm 100% chính hãng, có đầy đủ hóa đơn – nguồn gốc rõ ràng.</li>
                <li>Tư vấn chăm sóc da chuyên sâu, phù hợp với từng loại da.</li>
                <li>Chính sách đổi trả minh bạch.</li>
                <li>Giao hàng toàn quốc.</li>
            </ul>
            <p><strong>Sứ mệnh:</strong> Chúng tôi tin rằng đẹp là khi bạn tự tin là chính mình.</p>
        </div>
        <!-- khung dịch vụ -->
        <div id="dichVuBox" style="background-color: #fff0f5; padding: 30px; border-radius: 4px; display: none; margin-top: 16px; position: relative;">
            <span onclick="closeDichVu()" style="position: absolute; top: 10px; right: 20px; font-size: 24px; cursor: pointer;">&times;</span>
            <h2 style="color: #e84a70;">
                <i class="fas fa-concierge-bell"></i> Dịch vụ của Luna Beauty
            </h2>
            <ul style="line-height: 1.8; font-size: 16px; list-style: none; padding-left: 0;">
                <li><i class="fas fa-comments"></i> <strong>Tư vấn chăm sóc da miễn phí</strong> theo từng loại da & tình trạng da.</li>
                <li><i class="fas fa-shipping-fast"></i> <strong>Giao hàng nhanh toàn quốc</strong>, hỗ trợ kiểm tra trước khi nhận.</li>
                <li><i class="fas fa-exchange-alt"></i> <strong>Đổi/trả hàng dễ dàng</strong> trong vòng 7 ngày nếu có lỗi.</li>
                <li><i class="fas fa-gift"></i> <strong>Gói quà miễn phí</strong> – gửi lời chúc yêu thương đến người nhận.</li>
                <li><i class="fas fa-gem"></i> <strong>Ưu đãi khách hàng thân thiết</strong> – tích điểm & nhận voucher giảm giá.</li>
            </ul>
        </div>
    </header>
    <div class="container">
        <h2 style="font-size: 28px; font-weight: bold; color: #1f2937; margin-bottom: 24px; text-align: center;">Thu Thập Voucher</h2>
        <div class="voucher-grid">
            <?php foreach ($vouchers as $voucher): ?>
                <div class="voucher-card">
                    <div class="voucher-header">
                        <i class="fas fa-ticket-alt fa-2x voucher-icon"></i>
                        <div class="voucher-title"><?php echo htmlspecialchars($voucher['code']); ?></div>
                    </div>
                    <div class="voucher-details">
                        <p>Giảm: <?php echo $voucher['discount_type'] === 'percentage' ? ($voucher['discount'] * 100) . '%' : number_format($voucher['discount']) . 'đ'; ?></p>
                        <p>Đơn tối thiểu: <?php echo number_format($voucher['min_order_value']); ?>đ</p>
                        <p>Hết hạn: <?php echo date('d/m/Y', strtotime($voucher['expires_at'])); ?></p>
                    </div>
                    <button class="voucher-button" 
                            onclick="collectVoucher(<?php echo $voucher['id']; ?>, '<?php echo htmlspecialchars($voucher['code']); ?>')"
                            <?php echo $voucher['user_voucher_id'] ? 'disabled' : ''; ?>>
                        <?php echo $voucher['user_voucher_id'] ? 'Đã thu thập' : 'Thu thập ngay'; ?>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
        <p id="voucherMessage" class="voucher-message"></p>
    </div>
    <footer class="footer">
    <div class="footer-container">
        <div class="footer-column">
            <h4>CHĂM SÓC KHÁCH HÀNG</h4>
            <ul>
                <li><a href="#">Trung tâm trợ giúp</a></li>
                <li><a href="#">Hướng dẫn mua hàng</a></li>
                <li><a href="#">Chính sách đổi trả</a></li>
                <li><a href="#">Hướng dẫn thanh toán</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h4>VỀ CHÚNG TÔI</h4>
            <ul>
                <li><a href="#">Giới thiệu</a></li>
                <li><a href="#">Tuyển dụng</a></li>
                <li><a href="#">Điều khoản</a></li>
                <li><a href="#">Bảo mật</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h4>THEO DÕI CHÚNG TÔI</h4>
            <ul>
                <li><a href="#"><i class="fab fa-facebook"></i> Facebook</a></li>
                <li><a href="#"><i class="fab fa-instagram"></i> Instagram</a></li>
                <li><a href="#"><i class="fab fa-youtube"></i> YouTube</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h4>PHƯƠNG THỨC THANH TOÁN</h4>
            <div class="payment-icons">
                <img src="assets/images/payment/visa.png" alt="Visa">
                <img src="assets/images/payment/mastercard.png" alt="MasterCard">
                <img src="assets/images/payment/cod.png" alt="COD">
                <img src="assets/images/payment/momo.png" alt="MoMo">
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; 2025 Mỹ Phẩm 563. Địa chỉ: 123 Trần Duy Hưng, Hà Nội. ĐKKD: 0123456789.</p>
    </div>
</footer>
    <script>
        function toggleSettings() {
            alert('Cài đặt chưa được triển khai!');
        }
        function openGioiThieu() {
            document.getElementById('gioiThieuBox').style.display = 'block';
            document.getElementById('dichVuBox').style.display = 'none';
        }
        function closeGioiThieu() {
            document.getElementById('gioiThieuBox').style.display = 'none';
        }
        function openDichVu() {
            document.getElementById('dichVuBox').style.display = 'block';
            document.getElementById('gioiThieuBox').style.display = 'none';
        }
        function closeDichVu() {
            document.getElementById('dichVuBox').style.display = 'none';
        }
        function collectVoucher(voucherId, voucherCode) {
            fetch('collect_voucher.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `voucher_id=${voucherId}&voucher_code=${encodeURIComponent(voucherCode)}`
            })
            .then(response => response.json())
            .then(data => {
                const messageEl = document.getElementById('voucherMessage');
                messageEl.textContent = data.message;
                messageEl.style.color = data.success ? '#16a34a' : '#dc2626';
                if (data.success) {
                    const button = document.querySelector(`button[onclick="collectVoucher(${voucherId}, '${voucherCode}')"]`);
                    button.textContent = 'Đã thu thập';
                    button.disabled = true;
                }
            })
            .catch(error => {
                document.getElementById('voucherMessage').textContent = 'Đã có lỗi xảy ra, vui lòng thử lại!';
                document.getElementById('voucherMessage').style.color = '#dc2626';
            });
        }
    </script>
</body>
</html>

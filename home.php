<?php
session_start();
require_once 'connect.php';

$sliders = [];
$resultSliders = $conn->query("SELECT image, link FROM sliders ORDER BY `order` ASC");
if ($resultSliders) {
    while ($row = $resultSliders->fetch_assoc()) {
        $sliders[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chi tiết sản phẩm - Mỹ phẩm</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="./assets/fonts/fontawesome-free-6.4.0-web/fontawesome-free-6.4.0-web/css/all.min.css">
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
                <img src="assets/images/logo1.png" alt="Mỹ Phẩm 563" style="height: 140px;">
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
                    <div class="settings-title">Quản lí</div>
                    <?php
                    // Giả sử username được lưu trong session
                    $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
                    // Kiểm tra nếu username chứa 'admin' (không phân biệt hoa thường)
                    $isAdmin = stripos($username, 'admin') !== false;

                    // Hiển thị thẻ <a> với điều kiện
                    ?>
                    <a href="<?php echo $isAdmin ? 'admin.php' : '#'; ?>"
                        class="settings-item"
                        <?php echo !$isAdmin ? 'style="pointer-events: none; opacity: 0.5;"' : ''; ?>>
                        Quản lý trang
                    </a>
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
    <div class="main-content">
        <nav class="category">
            <h3 class="category__heading">
                <i class="category__heading_icon fa-solid fa-list"></i>
                DANH MỤC
            </h3>
            <ul class="category-list">
                <li class="category-item ">
                    <a href="skincare.php" class="category-item__link">Skincare</a>
                </li>

                <li class="category-item">
                    <a href="makeup.php" class="category-item__link">Makeup</a>
                </li>

                <li class="category-item">
                    <a href="haircare.php" class="category-item__link">Haircare</a>
                </li>
                <li class="category-item">
                    <a href="bodycare.php" class="category-item__link">Bodycare</a>
                </li>
                <li class="category-item">
                    <a href="perfume.php" class="category-item__link">Perfume</a>
                </li>
            </ul>
        </nav>
        <!-- Product List -->
        <div class="product-list">
            <div class="slider-container">
                <div class="slider">
                    <?php if (empty($sliders)): ?>
                        <p>Không có slider nào.</p>
                    <?php else: ?>
                        <?php $first = true; ?>
                        <?php foreach ($sliders as $slider): ?>
                            <!-- Thêm liên kết cho mỗi slide -->
                            <a href="<?php echo htmlspecialchars($slider['link']); ?>" class="slide <?php echo $first ? 'active' : ''; ?>">
                                <img src="assets/<?php echo htmlspecialchars($slider['image']); ?>" alt="Slider">
                            </a>
                            <?php $first = false; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="slider-buttons">
                    <button class="prev">❮</button>
                    <button class="next">❯</button>
                </div>
            </div>
            <div class="featured-title">Sản Phẩm Nổi Bật</div>


            <div class="product-card" data-id="1">
                <div class="product-img">
                    <img src="https://down-vn.img.susercontent.com/file/a740cc999ebc78acde421864a7258777.webp" alt="Son MAC chính hãng">

                    <span class="badge discount">-33%</span>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Sữa Rửa Mặt Ý Dĩ Hatomugi nội địa Nhật Bản 130g giúp da trắng sáng</h3>
                    <div class="price">
                        <span class="old-price">60.000đ</span>
                        <span class="new-price">40.000đ</span>
                    </div>
                    <div class="extra-info">
                        <span class="rating">⭐ 4.9 | Đã bán 55.2k</span>
                        <span class="location">Bắc Giang</span>
                    </div>
                    <div class="product-actions">
                        <a href="product_detail.php?id=1" class="view-detail">
                            <i class="fas fa-eye"></i> Xem chi tiết
                        </a>
                        <form method="POST" action="checkout.php">
                            <input type="hidden" name="product_name" value="Sữa Rửa Mặt Ý Dĩ Hatomugi nội địa Nhật Bản 130g 170g giúp da trắng sáng">
                            <input type="hidden" name="product_price" value="185000">
                            <input type="hidden" name="product_option" value="130 trắng">
                            <input type="hidden" name="product_qty" value="1" min="1">
                            <input type="hidden" name="product_img" value="https://down-vn.img.susercontent.com/file/a740cc999ebc78acde421864a7258777.webp">

                        </form>
                    </div>
                </div>
            </div>



            <div class="product-card" data-id="2">
                <div class="product-img">
                    <img src="https://down-vn.img.susercontent.com/file/vn-11134207-7qukw-lfxjx5kitxx37b" alt="Sản phẩm dưỡng da">
                    <span class="badge discount">-20%</span>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Sữa rửa mặt Cerave 236ml làm sạch sâu dưỡng ẩm cho da dầu mụn, da thường, da khô</h3>
                    <div class="price">
                        <span class="old-price">110.000đ</span>
                        <span class="new-price">88.000đ</span>
                    </div>
                    <div class="extra-info">
                        <span class="rating">⭐ 4.9 | Đã bán 10.3k</span>
                        <span class="location">Hà Nội</span>
                    </div>
                    <div class="product-actions">
                        <a href="product_detail.php?id=2" class="view-detail">
                            <i class="fas fa-eye"></i> Xem chi tiết
                        </a>
                        <form method="POST" action="checkout.php">
                            <input type="hidden" name="product_name" value="Sữa rửa mặt Cerave 236ml làm sạch sâu dưỡng ẩm cho da dầu mụn, da thường, da khô">
                            <input type="hidden" name="product_price" value="185000">
                            <input type="hidden" name="product_option" value="130 trắng">
                            <input type="hidden" name="product_qty" value="1" min="1">
                            <input type="hidden" name="product_img" value="https://down-vn.img.susercontent.com/file/vn-11134207-7qukw-lfxjx5kitxx37b">

                        </form>
                    </div>
                </div>
            </div>



            <div class="product-card" data-id="3">
                <div class="product-img">
                    <img src="https://down-vn.img.susercontent.com/file/vn-11134207-7ra0g-m8iqk98jisg725" alt="Sản phẩm dưỡng da">
                    <span class="badge discount">-47%</span>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Serum Phục Hồi Da Sáng Khỏe Sau Mụn Tia'M Vita B3 Source 40Ml </h3>
                    <div class="price">
                        <span class="old-price">559.000đ</span>
                        <span class="new-price">295.000đ</span>
                    </div>
                    <div class="extra-info">
                        <span class="rating">⭐ 4.8 | Đã bán 2.3k</span>
                        <span class="location">Hồ Chí Minh</span>
                    </div>
                    <div class="product-actions">
                        <a href="product_detail.php?id=3" class="view-detail">
                            <i class="fas fa-eye"></i> Xem chi tiết
                        </a>
                        <form method="POST" action="checkout.php">
                            <input type="hidden" name="product_name" value="Serum Phục Hồi Da Sáng Khỏe Sau Mụn Tia'M Vita B3 Source 40Ml ">
                            <input type="hidden" name="product_price" value="185000">
                            <input type="hidden" name="product_option" value="130 trắng">
                            <input type="hidden" name="product_qty" value="1" min="1">
                            <input type="hidden" name="product_img" value="https://down-vn.img.susercontent.com/file/vn-11134207-7ra0g-m8iqk98jisg725">

                        </form>
                    </div>
                </div>
            </div>



            <div class="product-card" data-id="4">
                <div class="product-img">
                    <img src=https://down-vn.img.susercontent.com/file/sg-11134201-7rfhg-m3kh553myq7bc8 alt="Sản phẩm dưỡng da">
                    <span class="badge discount">-45%</span>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Bảng phấn mắt nhũ tương 8 hình trái tim có điểm nổi bật nhũ tương, Bảng phấn mắt 3in1 với má hồng</h3>
                    <div class="price">
                        <span class="old-price">66.000</span>
                        <span class="new-price">36.300đ</span>
                    </div>
                    <div class="extra-info">
                        <span class="rating">⭐ 4.8 | Đã bán 2.3k</span>
                        <span class="location">Hồ Chí Minh</span>
                    </div>
                    <div class="product-actions">
                        <a href="product_detail.php?id=4" class="view-detail">
                            <i class="fas fa-eye"></i> Xem chi tiết
                        </a>
                        <form method="POST" action="checkout.php">
                            <input type="hidden" name="product_name" value="Bảng phấn mắt nhũ tương 8 hình trái tim có điểm nổi bật nhũ tương, Bảng phấn mắt 3in1 với má hồng">
                            <input type="hidden" name="product_price" value="185000">
                            <input type="hidden" name="product_option" value="130 trắng">
                            <input type="hidden" name="product_qty" value="1" min="1">
                            <input type="hidden" name="product_img" value="https://down-vn.img.susercontent.com/file/sg-11134201-7rfhg-m3kh553myq7bc8">

                        </form>
                    </div>
                </div>
            </div>

            <div class="product-card" data-id="5">
                <div class="product-img">
                    <img src=https://down-vn.img.susercontent.com/file/vn-11134207-7ras8-m27i0toqrc8id9 alt="Sản phẩm dưỡng da">
                    <span class="badge discount">-4%</span>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Tinh Chất oh!oh! Skin Health Serum (with 20% Niacinamide & 2% Acetyl Glucosamine) (10ml - 30ml)</h3>
                    <div class="price">
                        <span class="old-price">325.000đ</span>
                        <span class="new-price">311.000đ</span>
                    </div>
                    <div class="extra-info">
                        <span class="rating">⭐ 4.8 | Đã bán 12.3k</span>
                        <span class="location">Hồ Chí Minh</span>
                    </div>
                    <div class="product-actions">
                        <a href="product_detail.php?id=5" class="view-detail">
                            <i class="fas fa-eye"></i> Xem chi tiết
                        </a>
                        <form method="POST" action="checkout.php">
                            <input type="hidden" name="product_name" value="Tinh Chất oh!oh! Skin Health Serum (with 20% Niacinamide & 2% Acetyl Glucosamine) (10ml - 30ml)">
                            <input type="hidden" name="product_price" value="185000">
                            <input type="hidden" name="product_option" value="130 trắng">
                            <input type="hidden" name="product_qty" value="1" min="1">
                            <input type="hidden" name="product_img" value="https://down-vn.img.susercontent.com/file/vn-11134207-7ras8-m27i0toqrc8id9">

                        </form>
                    </div>
                </div>
            </div>

            <div class="product-card" data-id="6">
                <div class="product-img">
                    <img src=https://down-vn.img.susercontent.com/file/vn-11134207-7ra0g-m8lkwxcx3ix3fe alt="Sản phẩm dưỡng da">
                    <span class="badge discount">-60%</span>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Son Tint lì cho môi căng mọng Hàn Quốc Romand Juicy Lasting Tint 5.5g</h3>
                    <div class="price">
                        <span class="old-price">419.000đ</span>
                        <span class="new-price">269.000đ</span>
                    </div>
                    <div class="extra-info">
                        <span class="rating">⭐ 4.8 | Đã bán 2.3k</span>
                        <span class="location">Hồ Chí Minh</span>
                    </div>
                    <div class="product-actions">
                        <a href="product_detail.php?id=6" class="view-detail">
                            <i class="fas fa-eye"></i> Xem chi tiết
                        </a>
                        <form method="POST" action="checkout.php">
                            <input type="hidden" name="product_name" value="Son bóng dưỡng môi bắt sáng 3CE Shine Reflector 1.7g">
                            <input type="hidden" name="product_price" value="185000">
                            <input type="hidden" name="product_option" value="130 trắng">
                            <input type="hidden" name="product_qty" value="1" min="1">
                            <input type="hidden" name="product_img" value="https://down-vn.img.susercontent.com/file/vn-11134207-7ra0g-m8lkwxcx3ix3fe">

                        </form>
                    </div>
                </div>
            </div>

            <div class="product-card" data-id="7">
                <div class="product-img">
                    <img src=https://down-vn.img.susercontent.com/file/vn-11134201-7ra0g-m8cwtleitrgkb7 alt="Sản phẩm dưỡng da">
                    <span class="badge discount">-20%</span>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Gel Mờ Sẹo Và Vết Thâm Scar Care Acnes 12Gr</h3>
                    <div class="price">
                        <span class="old-price">82.000đ</span>
                        <span class="new-price">65.600đ</span>
                    </div>
                    <div class="extra-info">
                        <span class="rating">⭐ 4.8 | Đã bán 8.3k</span>
                        <span class="location">Phú Thọ 2</span>
                    </div>
                    <div class="product-actions">
                        <a href="product_detail.php?id=7" class="view-detail">
                            <i class="fas fa-eye"></i> Xem chi tiết
                        </a>
                        <form method="POST" action="checkout.php">
                            <input type="hidden" name="product_name" value="Gel Mờ Sẹo Và Vết Thâm Scar Care Acnes 12Gr">
                            <input type="hidden" name="product_price" value="185000">
                            <input type="hidden" name="product_option" value="130 trắng">
                            <input type="hidden" name="product_qty" value="1" min="1">
                            <input type="hidden" name="product_img" value="https://down-vn.img.susercontent.com/file/vn-11134201-7ra0g-m8cwtleitrgkb7">

                        </form>
                    </div>
                </div>
            </div>

            <div class="product-card" data-id="8">
                <div class="product-img">
                    <img src=https://down-vn.img.susercontent.com/file/vn-11134201-7ra0g-m8y6psofy1kyad alt="Sản phẩm dưỡng da">
                    <span class="badge discount">-46%</span>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Nước Tẩy Trang làm sạch sâu dịu nhẹ cho mọi loại da - Garnier Micellar Cleansing Water 400ml</h3>
                    <div class="price">
                        <span class="old-price">398.000</span>
                        <span class="new-price">254.000đ</span>
                    </div>
                    <div class="extra-info">
                        <span class="rating">⭐ 4.8 | Đã bán 6.3k</span>
                        <span class="location">Hồ Chí Minh</span>
                    </div>
                    <div class="product-actions">
                        <a href="product_detail.php?id=8" class="view-detail">
                            <i class="fas fa-eye"></i> Xem chi tiết
                        </a>
                        <form method="POST" action="checkout.php">
                            <input type="hidden" name="product_name" value="Nước Tẩy Trang làm sạch sâu dịu nhẹ cho mọi loại da - Garnier Micellar Cleansing Water 400ml">
                            <input type="hidden" name="product_price" value="185000">
                            <input type="hidden" name="product_option" value="130 trắng">
                            <input type="hidden" name="product_qty" value="1" min="1">
                            <input type="hidden" name="product_img" value="https://down-vn.img.susercontent.com/file/vn-11134201-7ra0g-m8y6psofy1kyad">

                        </form>
                    </div>
                </div>
            </div>

            <div class="product-card" data-id="9">
                <div class="product-img">
                    <img src=https://down-vn.img.susercontent.com/file/vn-11134207-7ras8-m26fczqyqdgydf alt="Sản phẩm dưỡng da">
                    <span class="badge discount">-46%</span>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Combo Simple Cho Da Nhạy Cảm - Nước Tẩy Trang (NTT), Sữa Rửa Mặt (SRM), Toner, Kem Dưỡng (KD)</h3>
                    <div class="price">
                        <span class="old-price">240.000</span>
                        <span class="new-price">120.000đ</span>
                    </div>
                    <div class="extra-info">
                        <span class="rating">⭐ 4.8 | Đã bán 2.0k</span>
                        <span class="location">Hồ Chí Minh</span>
                    </div>
                    <div class="product-actions">
                        <a href="product_detail.php?id=9" class="view-detail">
                            <i class="fas fa-eye"></i> Xem chi tiết
                        </a>
                        <form method="POST" action="checkout.php">
                            <input type="hidden" name="product_name" value="Combo Simple Cho Da Nhạy Cảm - Nước Tẩy Trang (NTT), Sữa Rửa Mặt (SRM), Toner, Kem Dưỡng (KD)">
                            <input type="hidden" name="product_price" value="185000">
                            <input type="hidden" name="product_option" value="130 trắng">
                            <input type="hidden" name="product_qty" value="1" min="1">
                            <input type="hidden" name="product_img" value="https://down-vn.img.susercontent.com/file/vn-11134207-7ras8-m26fczqyqdgydf">

                        </form>
                    </div>
                </div>
            </div>

            <div class="product-card" data-id="10">
                <div class="product-img">
                    <a href="product_detail.php?id=10">
                        <img src="https://down-vn.img.susercontent.com/file/vn-11134207-7r98o-lstqguefuimcd2" alt="Sản phẩm dưỡng da">
                        <span class="badge discount">-34%</span>
                    </a>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Son Kem 3CE Velvet Lip Tint Taupe Speak Up Daffodil Bitter Hour Child Like 4g - Mibebe</h3>
                    <div class="price">
                        <span class="old-price">400.000</span>
                        <span class="new-price">264.000đ</span>
                    </div>
                    <div class="extra-info">
                        <span class="rating">⭐ 5.0 | Đã bán 9.3k</span>
                        <span class="location">Hồ Chí Minh</span>
                    </div>
                    <div class="product-actions">
                        <a href="product_detail.php?id=10" class="view-detail">
                            <i class="fas fa-eye"></i> Xem chi tiết
                        </a>
                        <form method="POST" action="checkout.php">
                            <input type="hidden" name="product_name" value="Son Kem 3CE Velvet Lip Tint Taupe Speak Up Daffodil Bitter Hour Child Like 4g - Mibebe">
                            <input type="hidden" name="product_price" value="185000">
                            <input type="hidden" name="product_option" value="130 trắng">
                            <input type="hidden" name="product_qty" value="1" min="1">
                            <input type="hidden" name="product_img" value="https://down-vn.img.susercontent.com/file/vn-11134207-7r98o-lstqguefuimcd2">
                        </form>
                    </div>
                </div>
            </div>

            <div class="product-card" data-id="47">
                <div class="product-img">
                    <a href="product_detail.php?id=47"></a>
                    <img src=https://down-vn.img.susercontent.com/file/vn-11134201-7ra0g-m8uwinbm5w1z01.webp alt="Sản phẩm dưỡng da">
                    <span class="badge discount">-30%</span>
                    </a>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Nước hoa nữ Cindy Bloom Aroma Flower hương ngọt ngào nữ tính 30ml</h3>
                    <div class="price">
                        <span class="old-price">180.000</span>
                        <span class="new-price">137.750đ</span>
                    </div>
                    <div class="extra-info">
                        <span class="rating">⭐ 4.8 | Đã bán 2.3k</span>
                        <span class="location">Hồ Chí Minh</span>
                    </div>
                    <div class="product-actions">
                        <a href="product_detail.php?id=47" class="view-detail">
                            <i class="fas fa-eye"></i> Xem chi tiết
                        </a>
                        <form method="POST" action="checkout.php">
                            <input type="hidden" name="product_name" value="Son bóng dưỡng môi bắt sáng 3CE Shine Reflector 1.7g">
                            <input type="hidden" name="product_price" value="185000">
                            <input type="hidden" name="product_option" value="130 trắng">
                            <input type="hidden" name="product_qty" value="1" min="1">
                            <input type="hidden" name="product_img" value="https://down-vn.img.susercontent.com/file/vn-11134207-7ra0g-m8lkwxcx3ix3fe">
                        </form>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <script src="script.js"></script>
    <script>
        function toggleSettings() {
            const panel = document.querySelector(".settings-page");
            panel.classList.toggle("open"); // dùng class thay vì style
        }

        function closeSettings() {
            document.querySelector(".settings-page").classList.remove("open");
        }

        // Ẩn khi click bên ngoài
        document.addEventListener("click", function(event) {
            const settingsPage = document.querySelector(".settings-page");
            const settingsIcon = document.querySelector(".setting-icon");

            if (!settingsPage.contains(event.target) && !settingsIcon.contains(event.target)) {
                settingsPage.classList.remove("open");
            }
        });
    </script>

</body>

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

</html>
<?php
session_start();
require_once 'connect.php';

// Fetch sliders
$sliders = [];
$resultSliders = $conn->query("SELECT image, link FROM sliders ORDER BY `order` ASC");
if ($resultSliders) {
    while ($row = $resultSliders->fetch_assoc()) {
        $sliders[] = $row;
    }
}

// Product variants array
$productVariants = [
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
    41 => [
        ['name' => '30ml', 'img' => 'assets/images/p411.jpg', 'price' => 588000],
        ['name' => '50ml', 'img' => 'assets/images/p412.jpg', 'price' => 980000]
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

// Handle sorting
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'popular';
$orderBy = '';
switch ($sort) {
    case 'newest':
        $orderBy = 'created_at DESC';
        break;
    case 'price_low':
        $orderBy = 'price ASC';
        break;
    case 'price_high':
        $orderBy = 'price DESC';
        break;
    case 'popular':
    default:
        $orderBy = 'stock ASC'; // Lower stock implies higher popularity
        break;
}

// Handle pagination
$productsPerPage = 10;
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) $currentPage = 1;
$offset = ($currentPage - 1) * $productsPerPage;

// Count total products
$countSql = "SELECT COUNT(*) as total FROM products WHERE category = 'perfume'";
$countResult = $conn->query($countSql);
$totalProducts = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalProducts / $productsPerPage);

// Fetch products from database
$sql = "SELECT id, name, price, stock, product_image, description, created_at 
        FROM products 
        WHERE category = 'perfume' 
        ORDER BY $orderBy 
        LIMIT $productsPerPage OFFSET $offset";
$result = $conn->query($sql);
$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
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
    <style>
        .pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }

        .page-number {
            display: inline-block;
            padding: 8px 12px;
            text-decoration: none;
            color: #333;
            border: 1px solid #ddd;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .page-number:hover {
            background-color: #e84a70;
            color: white;
            border-color: #e84a70;
        }

        .page-number.active {
            background-color: #e84a70;
            color: white;
            border-color: #e84a70;
            font-weight: bold;
        }

        .page-ellipsis {
            padding: 8px 12px;
            color: #333;
        }

        .page-btn {
            background: none;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .page-btn:hover:not(:disabled) {
            background-color: #e84a70;
            color: white;
            border-color: #e84a70;
        }

        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .page-status {
            font-size: 16px;
            color: #333;
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

    // Query total quantity in cart
    $sql = "SELECT SUM(quantity) AS total_quantity FROM cart_items WHERE session_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->fetch_assoc()) {
        $row = $result->fetch_assoc();
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
                    echo '<a href=\"login.php\">Bạn chưa đăng nhập</a>';
                }
                ?>
            </div>
        </div>

        <!-- Logo + search bar + cart -->
        <div class="topbar">
            <a href="http://home.php" class="logo">
                <img src="assets/images/logo1.png" alt="Mỹ Phẩm" style="height: 140px;">
            </a>
            <form class="search-box" method="search.php" action="GET">
                <input type="text" name="query" placeholder="Tìm kiếm sản phẩm..." required>
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>

            <div class="icon-container">
                <a href="cart.php" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count"><?php echo $cart_count; ?></span>
                </a>
                <a href="javascript:void(0);" class="setting-icon" onclick="toggleSettings()">
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
                    <a href="https://account.php" class="settings-item">Tài khoản & Bảo mật</a>
                    <a href="change_address.php" class="settings-item">Địa Chỉ</a>
                    <a href="bank.php" class="settings-item">Tài khoản / Thẻ ngân hàng</a>
                </div>

                <div class="settings-section">
                    <div class="settings-title">Quản lý</div>
                    <?php
                    $username = isset($_SESSION['username'])? $_SESSION['username'] : '';
                    $isAdmin = stripos($username, 'admin') !== false;
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

        <!-- Navbar -->
        <nav class="navbar">
            <a href="home.php"><i class="fa-solid fa-home"></i></a>
            <a href="#" onclick="openGioiThieu()">Giới thiệu</a>
            <a href="#" onclick="openDichVu()">Dịch vụ</a>
            <a href="register.php">Đăng ký</a>
            <a href="login.php">Đăng nhập</a>
            <a href="vouchers.php">Voucher</a>
            <a href="contact.php">Liên hệ</a>
        </nav>
        <!-- Khung chỉnh sửa thông tin -->
        <div id="gioiThieuBox" style="display: none; background:rgb(255, 240, 245); padding: 20px; color: black; border-radius: 4px; position: relative; margin-top: 16px;">
            <span onclick="closeGioiThieu()" style="position: absolute; top: 10px; right: 20px; font-size: 24px; cursor: pointer;">×</span>
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
        <!-- Khung dịch vụ -->
        <div id="dichVuBox" style="background-color: #fff0f5; padding: 30px; border-radius: 4px; display: none; margin-top: 16px; position: relative;">
            <span onclick="closeDichVu()" style="position: absolute; top: 10px; right: 20px; font-size: 24px; cursor: pointer;">×</span>
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
                <li class="category-item">
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
            <div class="sort-bar">
                <div class="sort-left">
                    <span class="sort-label">Sắp xếp theo</span>
                    <span class="sort-item <?php echo $sort == 'popular' ? 'active' : ''; ?>" onclick="window.location.href='?sort=popular&page=1'">Phổ biến</span>
                    <span class="sort-item <?php echo $sort == 'newest' ? 'active' : ''; ?>" onclick="window.location.href='?sort=newest&page=1'">Mới nhất</span>
                    <div class="sort-price">
                        Giá <i class="fas fa-chevron-down"></i>
                        <div class="sort-price-dropdown">
                            <div class="sort-price-option" onclick="window.location.href='?sort=price_low&page=1'">Giá thấp đến cao</div>
                            <div class="sort-price-option" onclick="window.location.href='?sort=price_high&page=1'">Giá cao đến thấp</div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (empty($products)): ?>
                <p>Không có sản phẩm nào.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <?php
                    // Get the first variant for the product to use in the form
                    $variant = isset($productVariants[$product['id']][0]) ? $productVariants[$product['id']][0] : [
                        'name' => 'Default',
                        'price' => $product['price'],
                        'img' => $product['product_image']
                    ];
                    // Calculate discount if variant price is lower than product price
                    $new_price = min(array_column($productVariants[$product['id']] ?? [], 'price') ?: [$product['price']]);
                    $discount = $product['price'] > $new_price ? round(($product['price'] - $new_price) / $product['price'] * 100) : 0;
                    ?>
                    <div class="product-card" data-id="<?php echo $product['id']; ?>">
                        <div class="product-img">
                            <a href="product_detail.php?id=<?php echo $product['id']; ?>">
                                <img src="<?php echo htmlspecialchars($product['product_image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <span class="badge discount"><?php echo "-$discount%"; ?></span>
                            </a>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="price">
                                <?php if ($discount > 0): ?>
                                    <span class="old-price"><?php echo number_format($product['price'], 0, '.', '.') . 'đ'; ?></span>
                                <?php endif; ?>
                                <span class="new-price"><?php echo number_format($new_price, 0, '.', '.') . 'đ'; ?></span>
                            </div>
                            <div class="extra-info">
                                <span class="stock">Còn <?php echo $product['stock']; ?> sản phẩm</span>
                                <span class="location">Hồ Chí Minh</span>
                            </div>
                            <div class="product-actions">
                                <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="view-detail">
                                    <i class="fas fa-eye"></i> Xem chi tiết
                                </a>
                                <form method="POST" action="checkout.php">
                                    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                                    <input type="hidden" name="product_price" value="<?php echo $variant['price']; ?>">
                                    <input type="hidden" name="product_option" value="<?php echo htmlspecialchars($variant['name']); ?>">
                                    <input type="hidden" name="product_qty" value="1" min="1">
                                    <input type="hidden" name="product_img" value="<?php echo htmlspecialchars($variant['img']); ?>">
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <span class="page-status"><?php echo $currentPage . '/' . $totalPages; ?></span>
        
        <!-- Previous Page Button -->
        <button class="page-btn" <?php echo $currentPage <= 1 ? 'disabled' : ''; ?> 
                onclick="window.location.href='?sort=<?php echo $sort; ?>&page=<?php echo $currentPage - 1; ?>'">
            <i class="fas fa-chevron-left" style="color:black;"></i>
        </button>

        <!-- Page Numbers -->
        <?php
        // Maximum number of pages to show
        $maxPagesToShow = 5;
        $halfMaxPages = floor($maxPagesToShow / 2);

        // Calculate start and end page
        $startPage = max(1, $currentPage - $halfMaxPages);
        $endPage = min($totalPages, $startPage + $maxPagesToShow - 1);

        // Adjust if the number of pages shown is less than maxPagesToShow
        if ($endPage - $startPage + 1 < $maxPagesToShow) {
            $startPage = max(1, $endPage - $maxPagesToShow + 1);
        }

        // Show "1" and ellipsis if startPage is greater than 1
        if ($startPage > 1) {
            echo '<a class="page-number" href="?sort=' . $sort . '&page=1">1</a>';
            if ($startPage > 2) {
                echo '<span class="page-ellipsis">...</span>';
            }
        }

        // Display page numbers
        for ($i = $startPage; $i <= $endPage; $i++) {
            $activeClass = ($i == $currentPage) ? 'active' : '';
            echo '<a class="page-number ' . $activeClass . '" href="?sort=' . $sort . '&page=' . $i . '">' . $i . '</a>';
        }

        // Show ellipsis and last page if endPage is less than totalPages
        if ($endPage < $totalPages) {
            if ($endPage < $totalPages - 1) {
                echo '<span class="page-ellipsis">...</span>';
            }
            echo '<a class="page-number" href="?sort=' . $sort . '&page=' . $totalPages . '">' . $totalPages . '</a>';
        }
        ?>

        <!-- Next Page Button -->
        <button class="page-btn" <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?> 
                onclick="window.location.href='?sort=<?php echo $sort; ?>&page=<?php echo $currentPage + 1; ?>'">
            <i class="fas fa-chevron-right" style="color:black;"></i>
        </button>
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
            <p>© 2025 Mỹ Phẩm 563. Địa chỉ: 123 Trần Duy Hưng, Hà Nội. ĐKKD: 0123456789.</p>
        </div>
    </footer>

    <script src="script.js"></script>
    <script>
        function toggleSettings() {
            const panel = document.querySelector(".settings-page");
            panel.classList.toggle("open");
        }

        function closeSettings() {
            document.querySelector(".settings-page").classList.remove("open");
        }

        document.addEventListener("click", function(event) {
            const settingsPage = document.querySelector(".settings-page");
            const settingsIcon = document.querySelector(".setting-icon");

            if (!settingsPage.contains(event.target) && !settingsIcon.contains(event.target)) {
                settingsPage.classList.remove("open");
            }
        });
    </script>
</body>
</html>
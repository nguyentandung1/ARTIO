<?php
ob_start();
require 'admin/connect.php';
$func = "includes/functions/";
require $func . 'controller.php';
session_start();

$unSeenFlag = false;
$_SESSION['noOfNewNotification'] = 0;
$_SESSION['noOfOldNotification'] = 0;

if (isset($_SESSION["typeOfUser"]) && $_SESSION["typeOfUser"] === "buyer") {
    $User = getBuyer($db, $_SESSION["username"]);
    if (isset($User[0])) {
        $Notifications = getNotificationsForBuyer($db, $User[0]['ID']);
        foreach ($Notifications as $noti) {
            if ($noti['seen'] == 0) {
                $unSeenFlag = true;
                break;
            }
        }
    }
} elseif (isset($_SESSION["typeOfUser"]) && $_SESSION["typeOfUser"] === "seller") {
    $User = getSeller($db, $_SESSION["username"]);
    if (isset($User[0])) {
        $Notifications = getNotificationsForSeller($db, $User[0]['ID']);
        foreach ($Notifications as $noti) {
            if ($noti['seen'] == 0) {
                $unSeenFlag = true;
                break;
            }
        }
    }
}
?>

<style>
    .artio-header {
        width: 100%;
        background: linear-gradient(135deg, #003b95 0%, #0069d9 48%, #19b5fe 100%);
        padding: 10px 0;
        box-shadow: 0 6px 22px rgba(0, 70, 160, 0.22);
    }   

    .artio-nav {
        width: 92%;
        max-width: 1320px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        gap: 26px;
    }

    .artio-brand {
        min-width: 180px;
    }

    .artio-brand a {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .artio-logo-mark {
        width: 46px;
        height: 46px;
        border-radius: 15px;
        background: linear-gradient(135deg, #ffb000, #ff6b00);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 24px;
        font-weight: 900;
        box-shadow: 0 6px 16px rgba(255, 132, 0, 0.35);
    }

    .artio-brand-name {
        color: #fff;
        font-size: 31px;
        font-weight: 800;
        letter-spacing: 2px;
        font-family: Arial, Helvetica, sans-serif;
    }

    .artio-category-btn {
        height: 48px;
        padding: 0 20px;
        border-radius: 28px;
        background: rgba(255, 255, 255, 0.13);
        color: #fff;
        display: flex;
        align-items: center;
        gap: 9px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 700;
        border: 1px solid rgba(255, 255, 255, 0.18);
        transition: 0.25s;
        white-space: nowrap;
    }

    .artio-category-btn:hover {
        background: rgba(255, 255, 255, 0.23);
        color: #fff;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .artio-search {
        flex: 1;
        max-width: 520px;
        min-width: 280px;
    }

    .artio-search form {
        width: 100%;
        position: relative;
        margin: 0;
    }

    .artio-search input {
        width: 100%;
        height: 52px;
        border: none;
        outline: none;
        border-radius: 40px;
        padding: 0 62px 0 22px;
        background: #fff;
        color: #333;
        font-size: 14px;
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.14);
    }

    .artio-search input::placeholder {
        color: #8d98aa;
    }

    .artio-search button {
        position: absolute;
        right: 6px;
        top: 6px;
        width: 40px;
        height: 40px;
        border: none;
        border-radius: 50%;
        background: #0d6efd;
        color: #fff;
        cursor: pointer;
        font-size: 16px;
        transition: 0.25s;
    }

    .artio-search button:hover {
        background: #004fc4;
        transform: scale(1.04);
    }

    .artio-menu {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 20px;
        white-space: nowrap;
    }

    .artio-menu-link {
        color: #fff;
        text-decoration: none;
        font-size: 15px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: 0.25s;
    }

    .artio-menu-link:hover {
        color: #fff;
        text-decoration: none;
        transform: translateY(-1px);
        opacity: 0.92;
    }

    .artio-menu-link .nav-symbol {
        font-size: 21px;
        line-height: 1;
    }

    .artio-auction {
        background: linear-gradient(135deg, #ff9d00, #ff6a00);
        color: #fff !important;
        padding: 12px 20px;
        border-radius: 28px;
        box-shadow: 0 8px 18px rgba(255, 120, 0, 0.35);
    }

    .artio-icon-btn {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.16);
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        border: 1px solid rgba(255, 255, 255, 0.18);
        transition: 0.25s;
        position: relative;
        cursor: pointer;
    }

    .artio-icon-btn:hover {
        background: rgba(255, 255, 255, 0.26);
        transform: translateY(-2px);
    }

    .artio-icon-btn img {
        width: 25px;
        height: 25px;
        object-fit: contain;
    }

    .artio-noti-dot {
        position: absolute;
        top: 3px;
        right: 4px;
        width: 10px;
        height: 10px;
        background: #ff3355;
        border: 2px solid #fff;
        border-radius: 50%;
    }

    .artio-user {
        position: relative;
    }

    .artio-avatar {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: #eef5ff;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid rgba(255,255,255,0.55);
        cursor: pointer;
        transition: 0.25s;
    }

    .artio-avatar:hover {
        transform: translateY(-2px);
    }

    .artio-avatar img {
        width: 27px;
        height: 27px;
        object-fit: contain;
    }

.artio-user{
    position:relative;
}

    .artio-dropdown {
        position: absolute;
        top: 52px;
        right: 0;
        width: 220px;
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 14px 35px rgba(0,0,0,0.18);
        padding: 10px;
        display: none;
        z-index: 99999;
    }

    .artio-user:hover .artio-dropdown {
        display: block;
    }

    .artio-dropdown::before {
        content: "";
        position: absolute;
        top: -8px;
        right: 18px;
        width: 16px;
        height: 16px;
        background: #fff;
        transform: rotate(45deg);
    }

    .artio-dropdown ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .artio-dropdown ul li a {
        display: block;
        padding: 12px 14px;
        color: #2d3445;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        border-radius: 12px;
        transition: 0.2s;
    }

    .artio-dropdown ul li a:hover {
        background: #eef5ff;
        color: #0d6efd;
    }

    .artio-dropdown .logout-link {
        color: #e60023 !important;
    }

    .artio-category-wrapper{
    position:relative;
}

.artio-category-dropdown{
    position:absolute;
    top:58px;
    left:0;
    width:240px;
    background:white;
    border-radius:18px;
    padding:10px;
    box-shadow:0 14px 35px rgba(0,0,0,0.18);

    opacity:0;
    visibility:hidden;
    transform:translateY(10px);

    transition:0.25s;

    z-index:999999;
}

.artio-category-wrapper:hover .artio-category-dropdown{
    opacity:1;
    visibility:visible;
    transform:translateY(0);
}

.artio-category-dropdown a{
    display:block;
    padding:12px 15px;
    text-decoration:none;
    color:#333;
    font-size:14px;
    font-weight:600;
    border-radius:12px;
    transition:0.2s;
}

.artio-category-dropdown a:hover{
    background:#eef5ff;
    color:#0d6efd;
    padding-left:20px;
}
    
    @media (max-width: 1100px) {
        .artio-nav {
            gap: 14px;
        }

        .artio-category-btn {
            display: none;
        }

        .artio-menu-link span.link-text {
            display: none;
        }

        .artio-auction span.link-text {
            display: inline;
        }
    }

    @media (max-width: 850px) {
        .artio-nav {
            flex-wrap: wrap;
        }

        .artio-brand {
            min-width: auto;
        }

        .artio-search {
            order: 3;
            max-width: 100%;
            width: 100%;
        }

        .artio-menu {
            gap: 12px;
        }
    }
</style>

<header class="artio-header">
    <div class="artio-nav">

        <!-- LOGO -->
        <div class="artio-brand">
            <a href="index.php">
                <div class="artio-logo-mark">🎨</div>
                <span class="artio-brand-name">ARTIO</span>
            </a>
        </div>

        <!-- CATEGORY BUTTON -->
      <div class="artio-category-wrapper">

    <div class="artio-category-btn">
        <span>▦</span>
        <span>Danh mục</span>
        <span>⌄</span>
    </div>

    <div class="artio-category-dropdown">

        <?php
            $navCategories = getCategories($db);
            $navCategories = array_reverse($navCategories);
        ?>

        <?php foreach($navCategories as $cat): ?>
            <a href="childcat.php?cat=<?php echo urlencode($cat['categoryId']); ?>">
                <?php echo htmlspecialchars($cat['categoryName']); ?>
            </a>
        <?php endforeach; ?>

    </div>

</div>

        <!-- SEARCH -->
        <div class="artio-search">
            <form action="searchItem.php" method="GET">
                <input
                    type="text"
                    name="keyword"
                    placeholder="Tìm kiếm tác phẩm nghệ thuật..."
                >
                <button type="submit">⌕</button>
            </form>
        </div>

        <!-- RIGHT MENU -->
        <div class="artio-menu">

            <a href="auction.php" class="artio-menu-link artio-auction">
                <span class="nav-symbol">⚒</span>
                <span class="link-text">Đấu giá</span>
            </a>

            <?php if (isset($_SESSION["username"])): ?>

                <?php if ($_SESSION["typeOfUser"] === "buyer"): ?>
                    <a href="cart.php?username=<?php echo $User[0]['userName']; ?>" class="artio-menu-link">
                        <span class="nav-symbol">🛒</span>
                        <span class="link-text">Giỏ hàng</span>
                    </a>

                    <a href="myAuction.php" class="artio-menu-link">
                        <span class="nav-symbol">🏆</span>
                        <span class="link-text">Đấu giá của tôi</span>
                    </a>
                <?php else: ?>
                    <a href="sellerAuction.php" class="artio-menu-link">
                        <span class="nav-symbol">🏆</span>
                        <span class="link-text">Đấu giá của tôi</span>
                    </a>

                    <a href="history.php" class="artio-menu-link">
                        <span class="nav-symbol">↺</span>
                        <span class="link-text">Lịch sử</span>
                    </a>
                <?php endif; ?>

                <a href="notification.php" class="artio-icon-btn">
                    <?php if ($unSeenFlag): ?>
                        <span class="artio-noti-dot"></span>
                        <img src="img/icons/heart_fill.png">
                    <?php else: ?>
                        <img src="img/icons/heart.png">
                    <?php endif; ?>
                </a>

                <div class="artio-user">
                    <div class="artio-avatar">
                        <img src="img/icons/account.png">
                    </div>

                    <div class="artio-dropdown">
                        <ul>
                            <?php if ($_SESSION["typeOfUser"] === "buyer"): ?>
                                <li><a href="profileBuyer.php">Thông tin tài khoản</a></li>
                                <li><a href="cart.php?username=<?php echo $User[0]['userName']; ?>">Giỏ hàng</a></li>
                                <li><a href="myAuction.php">Đấu giá của tôi</a></li>
                            <?php else: ?>
                                <li><a href="profileSeller.php">Thông tin tài khoản</a></li>
                                <li><a href="sellerAuction.php">Đấu giá của tôi</a></li>
                                <li><a href="history.php">Lịch sử giao dịch</a></li>
                            <?php endif; ?>

                            <li><a href="logout.php" class="logout-link">Đăng xuất</a></li>
                        </ul>
                    </div>
                </div>

            <?php else: ?>

                <a href="signin.php" class="artio-menu-link">
                    <span class="nav-symbol">➜</span>
                    <span class="link-text">Đăng nhập</span>
                </a>

                <a href="signup.php" class="artio-menu-link">
                    <span class="nav-symbol">👥</span>
                    <span class="link-text">Đăng ký</span>
                </a>

                <a href="about.php" class="artio-icon-btn">
                    <img src="img/icons/aboutus.png">
                </a>

            <?php endif; ?>

        </div>
    </div>
</header>

<?php ob_end_flush(); ?>
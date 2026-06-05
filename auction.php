<?php
ob_start();
$pageTitle = 'Auction';
require "init.php";

$now = date('Y-m-d H:i:s');

/* AUTO END AUCTION + CHỌN WINNER */
$endedStmt = $db->prepare("
    SELECT *
    FROM auction
    WHERE endTime < NOW()
    AND status IN ('approved','active','ended')
");
$endedStmt->execute();
$endedAuctions = $endedStmt->fetchAll();

foreach($endedAuctions as $endedAuction){

    $auctionId = $endedAuction['auctionId'];

    $winnerStmt = $db->prepare("
        SELECT buyerId, bidAmount
        FROM bids
        WHERE auctionId = ?
        ORDER BY bidAmount DESC
        LIMIT 1
    ");
    $winnerStmt->execute([$auctionId]);
    $winner = $winnerStmt->fetch();

    if($winner && empty($endedAuction['winnerId'])){
        $updateStmt = $db->prepare("
            UPDATE auction
            SET status = 'ended',
                winnerId = ?,
                finalPrice = ?,
                paymentDeadline = DATE_ADD(NOW(), INTERVAL 1 HOUR)
            WHERE auctionId = ?
        ");
        $updateStmt->execute([
            $winner['buyerId'],
            $winner['bidAmount'],
            $auctionId
        ]);
    } else {
        $updateStmt = $db->prepare("
            UPDATE auction
            SET status = 'expired'
            WHERE auctionId = ?
        ");
        $updateStmt->execute([$auctionId]);
    }
}

/* Tự động hết hạn thanh toán */
$expiredStmt = $db->prepare("
    UPDATE auction
    SET status = 'expired'
    WHERE status = 'ended'
    AND paymentDeadline IS NOT NULL
    AND paymentDeadline < NOW()
");
$expiredStmt->execute();

/* Lấy danh sách auction */
$stmt = $db->prepare("
    SELECT *,
        CASE
            WHEN status IN ('ended','paid','expired') OR endTime < NOW() THEN 'ended_group'
            WHEN startTime <= NOW() AND endTime >= NOW() THEN 'active_group'
            ELSE 'upcoming_group'
        END AS timeGroup
    FROM auction
    ORDER BY startTime ASC
");
$stmt->execute();
$auctions = $stmt->fetchAll();

$activeAuctions = [];
$upcomingAuctions = [];
$endedAuctionsList = [];

foreach ($auctions as $auction) {
    if ($auction['timeGroup'] == 'active_group') {
        $activeAuctions[] = $auction;
    } elseif ($auction['timeGroup'] == 'upcoming_group') {
        $upcomingAuctions[] = $auction;
    } else {
        $endedAuctionsList[] = $auction;
    }
}
?>

<style>
.auction-card{
    min-height:760px;
    height:auto;
    box-shadow:rgba(0,0,0,0.18) 0px 8px 25px;
    border-radius:18px;
    padding:22px;
    margin-bottom:30px;
    background:#fff;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    border:1px solid #eee;
    transition:all 0.25s ease;
}

.auction-card:hover{
    transform:translateY(-4px);
    box-shadow:rgba(0,0,0,0.25) 0px 12px 30px;
}

.auction-waiting{opacity:0.55;filter:grayscale(100%);}
.auction-active{opacity:1;filter:none;}
.auction-ended{opacity:0.6;filter:grayscale(100%);}
.auction-expired{opacity:0.55;filter:grayscale(100%);}

.section-title{
    font-family:candara;
    font-weight:bold;
    padding:12px 18px;
    background:#f8f9fa;
    border-left:6px solid #ff9800;
    border-radius:10px;
}

.auction-title{
    height:34px;
    overflow:hidden;
    font-size:22px;
    font-weight:bold;
    color:#111;
    margin-bottom:16px;
    font-family:candara;
}

.auction-desc{
    height:70px;
    overflow:hidden;
    background:#f8f9fa;
    padding:12px 14px;
    border-radius:12px;
    color:#444;
    line-height:1.6;
    margin-bottom:12px;
}

.auction-info{
    background:#f8f9fa;
    padding:10px 12px;
    border-radius:10px;
    margin-bottom:10px;
    font-size:15px;
}

.auction-price{background:#fff3cd;color:#8a5a00;font-weight:bold;}
.auction-time{background:#eef7ff;color:#064b75;}
.auction-countdown{background:#fff0f0;color:#b30000;font-weight:bold;}
.auction-status{background:#e8f5e9;color:#1b5e20;font-weight:bold;}

.auction-img{
    height:250px;
    flex-shrink:0;
    display:flex;
    justify-content:center;
    align-items:center;
    background:#fafafa;
    border-radius:14px;
    margin:16px 0;
    border:1px solid #eee;
}

.auction-img img{
    max-width:220px;
    max-height:220px;
    object-fit:contain;
    border-radius:10px;
}

.auction-btn{
    display:block;
    text-align:center;
    padding:14px;
    border-radius:12px;
    text-decoration:none;
    font-weight:bold;
    background:linear-gradient(45deg,#ff9800,#ff6f00);
    color:white;
    box-shadow:0 4px 10px rgba(255,152,0,0.35);
    margin-top:auto;
}

.auction-btn:hover{
    color:white;
    text-decoration:none;
}

.auction-btn.disabled{
    background:#aaa;
    box-shadow:none;
    pointer-events:none;
}

.winner-box{
    background:#d4edda;
    color:#155724;
    border:1px solid #c3e6cb;
    padding:12px;
    border-radius:10px;
    margin-top:12px;
    font-weight:bold;
}

.expired-box{
    background:#f8d7da;
    color:#721c24;
    border:1px solid #f5c6cb;
    padding:12px;
    border-radius:10px;
    margin-top:12px;
    font-weight:bold;
}

.container.mt-5{
    padding-bottom:80px;
}
</style>

<?php
function renderAuctionCard($auction, $db) {
    $stmtItem = $db->prepare("SELECT * FROM item WHERE itemId = ?");
    $stmtItem->execute([$auction['itemId']]);
    $item = $stmtItem->fetch();

    if(!$item) return;

    if($auction['status'] == 'expired'){
        $cardClass = "auction-expired";
        $statusText = "Hết hạn thanh toán";
        $buttonClass = "disabled";
        $buttonText = "Đã hết hạn";
    } elseif($auction['status'] == 'paid'){
        $cardClass = "auction-ended";
        $statusText = "Đã thanh toán";
        $buttonClass = "disabled";
        $buttonText = "Hoàn tất";
    } elseif($auction['status'] == 'ended'){
        $cardClass = "auction-ended";
        $statusText = "Đã kết thúc";
        $buttonClass = "disabled";
        $buttonText = "Đã kết thúc";
    } elseif(isset($auction['timeGroup']) && $auction['timeGroup'] == 'active_group'){
        $cardClass = "auction-active";
        $statusText = "Đang đấu giá";
        $buttonClass = "";
        $buttonText = "Đấu giá";
    } elseif(isset($auction['timeGroup']) && $auction['timeGroup'] == 'upcoming_group'){
        $cardClass = "auction-waiting";
        $statusText = "Sắp diễn ra";
        $buttonClass = "disabled";
        $buttonText = "Chưa bắt đầu";
    } else {
        $cardClass = "auction-ended";
        $statusText = "Đã kết thúc";
        $buttonClass = "disabled";
        $buttonText = "Đã kết thúc";
    }

    $imageStmt = $db->prepare("
        SELECT *
        FROM itemimage
        WHERE itemId = ?
        LIMIT 1
    ");
    $imageStmt->execute([$auction['itemId']]);
    $image = $imageStmt->fetch();

    if($image){
        $imagePath = "data/uploads/items/" . $image['image'];
    } else {
        $imagePath = "data/uploads/items/default.png";
    }

    $winnerName = "";
    if(!empty($auction['winnerId']) && $auction['status'] != 'expired'){
        $winnerStmt = $db->prepare("
            SELECT fName, lName
            FROM buyer
            WHERE ID = ?
        ");
        $winnerStmt->execute([$auction['winnerId']]);
        $winnerInfo = $winnerStmt->fetch();

        if($winnerInfo){
            $winnerName = $winnerInfo['fName'] . ' ' . $winnerInfo['lName'];
        }
    }
?>

<div class="col-lg-4 col-md-6 col-12">
    <div class="auction-card <?php echo $cardClass; ?>">

        <div>
            <h4 class="auction-title"><?php echo $item['title']; ?></h4>

            <p class="auction-desc"><?php echo $item['description']; ?></p>

            <p class="auction-info auction-price">
                <b>Giá tối thiểu:</b>
                <?php echo $auction['minPrice']; ?> $
            </p>

            <?php if(!empty($auction['finalPrice'])): ?>
                <p class="auction-info auction-price">
                    <b>Giá thắng:</b>
                    <?php echo $auction['finalPrice']; ?> $
                </p>
            <?php endif; ?>

            <p class="auction-info auction-time">
                <b>Bắt đầu:</b>
                <?php echo $auction['startTime']; ?>
            </p>

            <p class="auction-info auction-time">
                <b>Kết thúc:</b>
                <?php echo $auction['endTime']; ?>
            </p>

            <p class="auction-info auction-countdown">
                <b>Thời gian còn lại:</b>
                <span class="countdown"
                      data-start="<?php echo $auction['startTime']; ?>"
                      data-end="<?php echo $auction['endTime']; ?>">
                    Đang tính...
                </span>
            </p>

            <p class="auction-info auction-status">
                <b>Trạng thái:</b>
                <?php echo $statusText; ?>
            </p>

            <?php

            $currentWinner = "";

            $winnerStmt2 = $db->prepare("
                SELECT buyer.fName, buyer.lName, bids.bidAmount
                FROM bids
                INNER JOIN buyer
                    ON bids.buyerId = buyer.ID
                WHERE bids.auctionId = ?
                ORDER BY bids.bidAmount DESC
                LIMIT 1
            ");

            $winnerStmt2->execute([$auction['auctionId']]);

            $currentWinnerData = $winnerStmt2->fetch();

            if($currentWinnerData){

                $currentWinner =
                    $currentWinnerData['fName'] . ' ' .
                    $currentWinnerData['lName'] .
                    ' (' . $currentWinnerData['bidAmount'] . ' $)';
            }

            ?>

            <?php if($currentWinner != ""): ?>

                <p class="auction-info"
                style="background:#fff3cd;color:#8a5a00;font-weight:bold;">

                    Người đang dẫn đầu:
                    <?php echo $currentWinner; ?>

                </p>

            <?php endif; ?>

            <?php if($winnerName != ""): ?>
                <div class="winner-box">
                    Winner: <?php echo $winnerName; ?>
                </div>
            <?php endif; ?>

            <?php if($auction['status'] == 'expired'): ?>
                <div class="expired-box">
                    Người thắng đã quá hạn thanh toán hoặc phiên không có người ra giá.
                </div>
            <?php endif; ?>
        </div>

        <div class="auction-img">
            <img src="<?php echo $imagePath; ?>">
        </div>

        <?php if(
            isset($_SESSION['typeOfUser']) &&
            $_SESSION['typeOfUser'] == 'buyer' &&
            $auction['status'] == 'ended' &&
            $auction['winnerId'] == $_SESSION['id']
        ): ?>

            <a class="auction-btn"
               href="payAuction.php?auctionId=<?php echo $auction['auctionId']; ?>">
                Thanh toán
            </a>

        <?php elseif($buttonClass == ""): ?>

            <a class="auction-btn"
               href="bid.php?auctionId=<?php echo $auction['auctionId']; ?>">
                <?php echo $buttonText; ?>
            </a>

        <?php else: ?>

            <a class="auction-btn disabled" href="#">
                <?php echo $buttonText; ?>
            </a>

        <?php endif; ?>

    </div>
</div>

<?php } ?>

<div class="container mt-5">
    <h1 class="text-center mb-4" style="font-family:candara;font-weight:bold;">
        ĐẤU GIÁ TRANH
    </h1>

    <?php if(empty($auctions)): ?>
        <p class="text-center">Hiện chưa có phiên đấu giá nào.</p>
    <?php endif; ?>

    <h3 class="mb-4 mt-4 section-title">Đang trong phiên đấu giá</h3>
    <div class="row">
        <?php if(empty($activeAuctions)): ?>
            <p class="text-muted ms-3">Không có sản phẩm nào đang đấu giá.</p>
        <?php endif; ?>

        <?php foreach($activeAuctions as $auction): ?>
            <?php renderAuctionCard($auction, $db); ?>
        <?php endforeach; ?>
    </div>

    <h3 class="mb-4 mt-5 section-title">Chưa mở phiên</h3>
    <div class="row">
        <?php if(empty($upcomingAuctions)): ?>
            <p class="text-muted ms-3">Không có sản phẩm nào sắp mở phiên.</p>
        <?php endif; ?>

        <?php foreach($upcomingAuctions as $auction): ?>
            <?php renderAuctionCard($auction, $db); ?>
        <?php endforeach; ?>
    </div>

    <h3 class="mb-4 mt-5 section-title">Đã kết thúc</h3>
    <div class="row">
        <?php if(empty($endedAuctionsList)): ?>
            <p class="text-muted ms-3">Chưa có sản phẩm nào kết thúc.</p>
        <?php endif; ?>

        <?php foreach($endedAuctionsList as $auction): ?>
            <?php renderAuctionCard($auction, $db); ?>
        <?php endforeach; ?>
    </div>
</div>

<script>
function updateCountdowns() {
    const countdowns = document.querySelectorAll('.countdown');

    countdowns.forEach(function(el) {
        const startTime = new Date(el.dataset.start).getTime();
        const endTime = new Date(el.dataset.end).getTime();
        const now = new Date().getTime();

        let distance;
        let prefix = "";

        if (now < startTime) {
            distance = startTime - now;
            prefix = "Mở sau: ";
        } else if (now <= endTime) {
            distance = endTime - now;
            prefix = "";
        } else {
            el.innerHTML = "Đã kết thúc";
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance / (1000 * 60 * 60)) % 24);
        const minutes = Math.floor((distance / (1000 * 60)) % 60);
        const seconds = Math.floor((distance / 1000) % 60);

        el.innerHTML = prefix + days + " ngày " + hours + " giờ " + minutes + " phút " + seconds + " giây";
    });
}

updateCountdowns();
setInterval(updateCountdowns, 1000);
</script>

<?php
include $tpl . "footer.php";
ob_end_flush();
?>
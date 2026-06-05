<?php
ob_start();
$pageTitle = "Place Bid";
require "init.php";

if (!isset($_SESSION['username'])) {
    header("Location: signin.php");
    exit();
}

if (!isset($_SESSION['typeOfUser']) || $_SESSION['typeOfUser'] != 'buyer') {
    header("Location: auction.php");
    exit();
}

if (!isset($_GET['auctionId'])) {
    header("Location: auction.php");
    exit();
}

$auctionId = intval($_GET['auctionId']);
$buyerId = $_SESSION['id'];
$error = "";
$success = "";

$stmt = $db->prepare("
    SELECT *
    FROM auction
    WHERE auctionId = ?
");
$stmt->execute([$auctionId]);
$auction = $stmt->fetch();

if (!$auction) {
    header("Location: auction.php");
    exit();
}

$now = date('Y-m-d H:i:s');

$error = "";

/*
    Nếu quá thời gian kết thúc thì tự chuyển sang ended
*/
if ($now > $auction['endTime']) {

    if ($auction['status'] != 'ended' && $auction['status'] != 'paid' && $auction['status'] != 'expired') {

        $winnerStmt = $db->prepare("
            SELECT buyerId, bidAmount
            FROM bids
            WHERE auctionId = ?
            ORDER BY bidAmount DESC
            LIMIT 1
        ");
        $winnerStmt->execute([$auctionId]);
        $winner = $winnerStmt->fetch();

        if ($winner) {
            $updateAuction = $db->prepare("
                UPDATE auction
                SET status = 'ended',
                    winnerId = ?,
                    finalPrice = ?,
                    paymentDeadline = DATE_ADD(NOW(), INTERVAL 1 HOUR)
                WHERE auctionId = ?
            ");
            $updateAuction->execute([
                $winner['buyerId'],
                $winner['bidAmount'],
                $auctionId
            ]);

            $auction['status'] = 'ended';
            $auction['winnerId'] = $winner['buyerId'];
            $auction['finalPrice'] = $winner['bidAmount'];
        } else {
            $updateAuction = $db->prepare("
                UPDATE auction
                SET status = 'expired'
                WHERE auctionId = ?
            ");
            $updateAuction->execute([$auctionId]);

            $auction['status'] = 'expired';
        }
    }

    $error = "Phiên đấu giá đã kết thúc.";
}

$countStmt = $db->prepare("
    SELECT COUNT(*) AS bidCount
    FROM bids
    WHERE auctionId = ? AND buyerId = ?
");
$countStmt->execute([$auctionId, $buyerId]);
$bidCount = $countStmt->fetch()['bidCount'];

$highestStmt = $db->prepare("
    SELECT MAX(bidAmount) AS highestBid
    FROM bids
    WHERE auctionId = ?
");
$highestStmt->execute([$auctionId]);
$highestBid = $highestStmt->fetch()['highestBid'];

if (!$highestBid) {
    $highestBid = $auction['minPrice'];
}

$itemStmt = $db->prepare("
    SELECT *
    FROM item
    WHERE itemId = ?
");
$itemStmt->execute([$auction['itemId']]);
$item = $itemStmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bidAmount'])) {

    $bidAmount = floatval($_POST['bidAmount']);

    if ($error != "") {
        // giữ nguyên error
    } elseif ($bidCount >= 3) {
        $error = "Bạn đã dùng hết 3 lượt ra giá cho phiên này.";
    } elseif ($bidAmount < $auction['minPrice']) {
        $error = "Giá đặt không được nhỏ hơn giá tối thiểu.";
    } elseif ($bidAmount <= $highestBid) {
        $error = "Giá đặt phải cao hơn giá hiện tại.";
    } else {
        $insert = $db->prepare("
            INSERT INTO bids
            (auctionId, buyerId, bidAmount)
            VALUES (?, ?, ?)
        ");
        $insert->execute([$auctionId, $buyerId, $bidAmount]);
        

        $success = "Đặt giá thành công.";

        $bidCount++;
        $highestBid = $bidAmount;
    }
}

$rankStmt = $db->prepare("
    SELECT 
        buyer.ID,
        buyer.fName,
        buyer.lName,
        MAX(bids.bidAmount) AS maxBid
    FROM bids
    INNER JOIN buyer
        ON bids.buyerId = buyer.ID
    WHERE bids.auctionId = ?
    GROUP BY bids.buyerId
    ORDER BY maxBid DESC
");
$rankStmt->execute([$auctionId]);
$rankings = $rankStmt->fetchAll();
?>

<style>
.bid-page{
    margin-top:40px;
    margin-bottom:50px;
}

.bid-title{
    font-family:candara;
    font-weight:bold;
    margin-bottom:28px;
}

.bid-card{
    background:white;
    border-radius:18px;
    box-shadow:rgba(0,0,0,0.18) 0px 8px 25px;
    padding:26px;
    border:1px solid #eee;
}

.bid-card h3{
    font-family:candara;
    font-weight:bold;
    margin-bottom:18px;
}

.bid-info{
    background:#f8f9fa;
    padding:12px 14px;
    border-radius:10px;
    margin-bottom:10px;
}

.bid-info b{
    color:#222;
}

.bid-min{
    background:#fff3cd;
    color:#8a5a00;
    font-weight:bold;
}

.bid-current{
    background:#e8f5e9;
    color:#1b5e20;
    font-weight:bold;
}

.bid-count{
    background:#eef7ff;
    color:#064b75;
    font-weight:bold;
}

.bid-time{
    background:#f8f9fa;
    color:#333;
}

.bid-form-box{
    margin-top:20px;
    padding:18px;
    border-radius:14px;
    background:#fafafa;
    border:1px solid #eee;
}

.bid-input{
    padding:12px 14px;
    border-radius:10px;
}

.bid-submit{
    background:linear-gradient(45deg,#ff9800,#ff6f00);
    color:white;
    font-weight:bold;
    border:none;
    border-radius:10px;
    padding:11px 20px;
}

.bid-submit:hover{
    color:white;
    background:linear-gradient(45deg,#ff8c00,#e65c00);
}

.rank-card{
    background:white;
    border-radius:18px;
    box-shadow:rgba(0,0,0,0.15) 0px 6px 20px;
    padding:24px;
    border:1px solid #eee;
}

.rank-title{
    font-family:candara;
    font-weight:bold;
    margin-bottom:8px;
}

.rank-table th{
    background:#212529;
    color:white;
}

.rank-number{
    font-weight:bold;
    color:#ff6f00;
}
</style>

<div class="container bid-page">

    <h1 class="text-center bid-title">ĐẶT GIÁ ĐẤU GIÁ</h1>

    <div class="bid-card">

        <h3><?php echo $item ? $item['title'] : 'Auction Item'; ?></h3>

        <p class="bid-info bid-min">
            <b>Giá tối thiểu:</b> <?php echo $auction['minPrice']; ?> $
        </p>

        <p class="bid-info bid-current">
            <b>Giá hiện tại:</b> <?php echo $highestBid; ?> $
        </p>

        <p class="bid-info bid-count">
            <b>Lượt ra giá còn lại:</b> <?php echo max(0, 3 - $bidCount); ?>/3
        </p>

        <p class="bid-info bid-time">
            <b>Bắt đầu:</b> <?php echo $auction['startTime']; ?>
        </p>

        <p class="bid-info bid-time">
            <b>Kết thúc:</b> <?php echo $auction['endTime']; ?>
        </p>

        <?php if($error != ""): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if($success != ""): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <div class="bid-form-box">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">
                        Nhập giá bạn muốn đặt
                    </label>

                    <input 
                        type="number" 
                        name="bidAmount" 
                        step="0.01" 
                        min="<?php echo $highestBid + 0.01; ?>"
                        class="form-control bid-input" 
                        placeholder="Nhập giá cao hơn <?php echo $highestBid; ?> $"
                        required
                    >
                </div>

                <?php if($error == ""): ?>
                    <button type="submit" class="btn bid-submit">
                        Gửi giá
                    </button>
                <?php else: ?>
                    <button type="button" class="btn bid-submit" disabled>
                        Không thể ra giá
                    </button>
                <?php endif; ?>

                <a href="auction.php" class="btn btn-secondary">
                    Quay lại
                </a>
            </form>
        </div>

    </div>

    <div class="rank-card mt-4">
        <h4 class="rank-title">Bảng xếp hạng người ra giá</h4>
        <p class="text-muted">Giá được ẩn, chỉ hiển thị thứ hạng và tên buyer.</p>

        <table class="table table-bordered text-center rank-table">
            <thead>
                <tr>
                    <th>Thứ hạng</th>
                    <th>Buyer</th>
                </tr>
            </thead>

            <tbody>
                <?php if(empty($rankings)): ?>
                    <tr>
                        <td colspan="2">Chưa có ai ra giá.</td>
                    </tr>
                <?php else: ?>
                    <?php $rank = 1; ?>
                    <?php foreach($rankings as $row): ?>
                        <tr>
                            <td class="rank-number"><?php echo $rank++; ?></td>
                            <td>
                                <?php echo $row['fName'] . ' ' . $row['lName']; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php
include $tpl . "footer.php";
ob_end_flush();
?>
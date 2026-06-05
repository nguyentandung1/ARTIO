<?php
ob_start();
$pageTitle = "Seller Auctions";
require "init.php";

if (!isset($_SESSION['username'])) {
    header("Location: signin.php");
    exit();
}

if (!isset($_SESSION['typeOfUser']) || $_SESSION['typeOfUser'] != 'seller') {
    header("Location: index.php");
    exit();
}

$sellerId = $_SESSION['id'];

$stmt = $db->prepare("
    SELECT auction.*, item.title
    FROM auction
    INNER JOIN item
        ON auction.itemId = item.itemId
    WHERE auction.sellerId = ?
    ORDER BY auction.startTime DESC
");
$stmt->execute([$sellerId]);
$auctions = $stmt->fetchAll();
?>

<div class="container mt-5 mb-5">
    <h1 class="text-center mb-4">Phiên đấu giá của tôi</h1>

    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>Item</th>
                <th>Min Price</th>
                <th>Final Price</th>
                <th>Start</th>
                <th>End</th>
                <th>Payment Deadline</th>
                <th>Paid At</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            <?php if(empty($auctions)): ?>
                <tr>
                    <td colspan="8">Bạn chưa có sản phẩm đấu giá nào.</td>
                </tr>
            <?php else: ?>
                <?php foreach($auctions as $auction): ?>
                    <tr>
                        <td><?php echo $auction['title']; ?></td>
                        <td><?php echo $auction['minPrice']; ?> $</td>
                        <td><?php echo $auction['finalPrice'] ? $auction['finalPrice'] . ' $' : '---'; ?></td>
                        <td><?php echo $auction['startTime']; ?></td>
                        <td><?php echo $auction['endTime']; ?></td>
                        <td><?php echo $auction['paymentDeadline'] ? $auction['paymentDeadline'] : '---'; ?></td>
                        <td><?php echo $auction['paidAt'] ? $auction['paidAt'] : '---'; ?></td>
                        <td><?php echo $auction['status']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="text-center">
        <a href="profileSeller.php" class="btn btn-secondary">Quay lại profile</a>
    </div>
</div>

<?php
include $tpl . "footer.php";
ob_end_flush();
?>
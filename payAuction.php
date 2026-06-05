<?php
ob_start();
$pageTitle = "Pay Auction";
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
    header("Location: myAuction.php");
    exit();
}

$auctionId = intval($_GET['auctionId']);
$buyerId = $_SESSION['id'];

$stmt = $db->prepare("
    SELECT *
    FROM auction
    WHERE auctionId = ?
    AND winnerId = ?
");
$stmt->execute([$auctionId, $buyerId]);
$auction = $stmt->fetch();

if (!$auction) {
    header("Location: myAuction.php");
    exit();
}

if ($auction['status'] != 'ended') {
    header("Location: myAuction.php");
    exit();
}

$now = date('Y-m-d H:i:s');

if ($auction['paymentDeadline'] != null && $now > $auction['paymentDeadline']) {
    $expire = $db->prepare("
        UPDATE auction
        SET status = 'expired'
        WHERE auctionId = ?
    ");
    $expire->execute([$auctionId]);

    header("Location: myAuction.php");
    exit();
}

$update = $db->prepare("
    UPDATE auction
    SET status = 'paid',
        paidAt = NOW()
    WHERE auctionId = ?
");

$update->execute([$auctionId]);

header("Location: myAuction.php");
exit();
?>
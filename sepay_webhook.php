<?php
require "admin/connect.php";

$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

if (!$data) {
    http_response_code(400);
    echo "Invalid data";
    exit();
}

/*
    Tùy payload SePay, nội dung chuyển khoản có thể nằm ở:
    - content
    - transfer_content
    - description
*/
$content = $data['content'] 
    ?? $data['transfer_content'] 
    ?? $data['description'] 
    ?? '';

$amount = $data['transferAmount'] 
    ?? $data['amount'] 
    ?? 0;

$transactionId = $data['id'] 
    ?? $data['transaction_id'] 
    ?? uniqid("sepay_");

/*
    Nội dung chuẩn:
    ARTIOA12B3
    A12 = auctionId 12
    B3 = buyerId 3
*/
if (!preg_match('/ARTIOA(\d+)B(\d+)/', $content, $matches)) {
    http_response_code(200);
    echo "No matching auction content";
    exit();
}

$auctionId = intval($matches[1]);
$buyerId = intval($matches[2]);

$stmt = $db->prepare("
    SELECT *
    FROM auction
    WHERE auctionId = ?
    AND winnerId = ?
    AND status = 'ended'
");
$stmt->execute([$auctionId, $buyerId]);
$auction = $stmt->fetch();

if (!$auction) {
    http_response_code(200);
    echo "Auction not found or already processed";
    exit();
}

$finalPrice = floatval($auction['finalPrice']);

if (floatval($amount) < $finalPrice) {
    http_response_code(200);
    echo "Amount not enough";
    exit();
}

$insert = $db->prepare("
    INSERT INTO auction_payments(
        auctionId,
        buyerId,
        amount,
        transferContent,
        method,
        status,
        transactionId,
        rawData,
        paidAt
    )
    VALUES (?, ?, ?, ?, 'sepay', 'paid', ?, ?, NOW())
");
$insert->execute([
    $auctionId,
    $buyerId,
    $amount,
    $content,
    $transactionId,
    $rawData
]);

$update = $db->prepare("
    UPDATE auction
    SET status = 'paid',
        paidAt = NOW()
    WHERE auctionId = ?
");
$update->execute([$auctionId]);

http_response_code(200);
echo "OK";
?>
<?php
$pageTitle = "Auction Bids";
include 'init.php';

if(!isset($_SESSION['typeOfUser']))
    header("Location: ../signin.php");

if (isset($_SESSION['typeOfUser']) && $_SESSION['typeOfUser'] != "admin") {
    header("Location: ../signin.php");
}

if(!isset($_GET['auctionId'])){
    header("Location: items.php");
    exit();
}

$auctionId = intval($_GET['auctionId']);

$stmt = $db->prepare("
    SELECT 
        bids.*,
        buyer.fName,
        buyer.lName
    FROM bids
    INNER JOIN buyer
        ON bids.buyerId = buyer.ID
    WHERE bids.auctionId = ?
    ORDER BY bids.bidAmount DESC
");
$stmt->execute([$auctionId]);
$bids = $stmt->fetchAll();
?>

<div class="container items">
    <h1 class="text-center">Auction Bids</h1>

    <div class="mb-3 text-center">
        <a href="items.php?do=ExpiredAuctions" class="btn btn-primary">
            Back
        </a>
    </div>

    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Buyer</th>
                <th>Bid Amount</th>
                <th>Bid Time</th>
            </tr>
        </thead>

        <tbody>
            <?php if(empty($bids)): ?>
                <tr>
                    <td colspan="4">No bids found.</td>
                </tr>
            <?php else: ?>
                <?php $rank = 1; ?>
                <?php foreach($bids as $bid): ?>
                    <tr>
                        <td><?php echo $rank++; ?></td>
                        <td><?php echo $bid['fName'] . ' ' . $bid['lName']; ?></td>
                        <td><?php echo $bid['bidAmount']; ?> $</td>
                        <td>---</td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
include $tpl . 'footer.php';
?>
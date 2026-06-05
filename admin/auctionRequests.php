<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

$pageTitle = "Auction Requests";

include "init.php";

// APPROVE
if(isset($_GET['approve'])){

    $requestId = $_GET['approve'];

    // lấy request
    $stmt = $db->prepare("
        SELECT *
        FROM auction_requests
        WHERE requestId=?
    ");

    $stmt->execute([$requestId]);

    $request = $stmt->fetch();

    if($request){

        // tạo auction
        $stmt = $db->prepare("
            INSERT INTO auction
            (
                itemId,
                sellerId,
                minPrice,
                startTime,
                endTime,
                status
            )
            VALUES
            (?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 3 DAY), 'active')
        ");

        $stmt->execute([
            $request['itemId'],
            $request['sellerId'],
            10
        ]);

        // update request
        $stmt = $db->prepare("
            UPDATE auction_requests
            SET status='approved'
            WHERE requestId=?
        ");

        $stmt->execute([$requestId]);
    }

    header("Location: auctionRequests.php");
    exit();
}

// REJECT
if(isset($_GET['reject'])){

    $stmt = $db->prepare("
        UPDATE auction_requests
        SET status='rejected'
        WHERE requestId=?
    ");

    $stmt->execute([$_GET['reject']]);

    header("Location: auctionRequests.php");
    exit();
}

// LIST REQUESTS
$stmt = $db->prepare("
    SELECT *
    FROM auction_requests
    ORDER BY createdAt DESC
");

$stmt->execute();

$requests = $stmt->fetchAll();

?>

<div class="container mt-5">

    <h1 class="mb-4">Auction Requests</h1>

    <table class="table table-bordered">

        <thead>
            <tr>
                <th>ID</th>
                <th>Item ID</th>
                <th>Seller ID</th>
                <th>Status</th>
                <th>Created</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

        <?php foreach($requests as $req): ?>

            <tr>

                <td><?= $req['requestId'] ?></td>

                <td><?= $req['itemId'] ?></td>

                <td><?= $req['sellerId'] ?></td>

                <td><?= $req['status'] ?></td>

                <td><?= $req['createdAt'] ?></td>

                <td>

                    <?php if($req['status'] == 'pending'): ?>

                        <a href="?approve=<?= $req['requestId'] ?>"
                           class="btn btn-success btn-sm">

                            Approve

                        </a>

                        <a href="?reject=<?= $req['requestId'] ?>"
                           class="btn btn-danger btn-sm">

                            Reject

                        </a>

                    <?php else: ?>

                        Done

                    <?php endif; ?>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>

<?php include $tpl . "footer.php"; ?>
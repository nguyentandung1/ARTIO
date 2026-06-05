<?php
$pageTitle = "Items";
include 'init.php';

if(!isset($_SESSION['typeOfUser']))
    header("Location: ../signin.php");

if (isset($_SESSION['typeOfUser']) && $_SESSION['typeOfUser'] != "admin") {
    header("Location: ../signin.php");
}

$do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

if($do == 'Manage') {

    $items = GetItems($db);

    $name = $id = '';
    $idErr = '';

    if (isset($_POST['search'])) {
        $name = input_data($_POST['name']);
        $id = input_data($_POST['id']);
        $idErr = validateNumber($id);

        if ($id != '' && $idErr == '') {
            $items = GetItemBySellerID($id, $db);
        } elseif ($name != "") {
            $items = GetItemBySellerUserName($name, $db);
        } else {
            $items = array();
        }
    }

    if (isset($_POST['showall'])) {
        $items = GetItems($db);
    }
?>

<div class="searching-area container items">
    <h1 class="text-center">Manage Items</h1>

    <div class="mb-3 text-center">
        <a href="?do=Pending" class="btn btn-warning">
            Pending Auction Requests
        </a>

        <a href="items.php?do=ExpiredAuctions" class="btn btn-danger">
            Expired Payments
        </a>
    </div>

    <form action="?do=Manage" method="POST" class="search-form">
        <div class="name">
            <div>Seller Username:</div>
            <input type="text" name="name" class="form-control">
        </div>

        <div class="id">
            <div>ID:</div>
            <input type="text" name="id" class="form-control">
        </div>

        <div class="search-btns">
            <input type="submit" name="search" value="Search" class="btn btn-primary me-1 ms-1">
            <input type="submit" name="showall" value="Show All" class="btn btn-primary">
        </div>
    </form>
</div>

<div class="container items">
    <div class="table-responsive">
        <table class="table table-bordered text-center">
            <thead class="thead-dark">
                <tr>
                    <th scope="col" class="table-dark">#ID</th>
                    <th scope="col" class="table-dark">Item Name</th>
                    <th scope="col" class="table-dark">Child Category</th>
                    <th scope="col" class="table-dark">Owner's Name</th>
                    <th scope="col" class="table-dark">Price</th>
                    <th scope="col" class="table-dark">Quantity</th>
                    <th scope="col" class="table-dark">Control</th>
                </tr>
            </thead>

            <tbody>
            <?php
            if (empty($items)) {
                echo '<tr>';
                echo '<td scope="row" colspan="7" style="font-size: 25px; color: #c13131;">No Result Found</td>';
                echo '</tr>';
            } else {
                foreach ($items as $item) {
                    echo '<tr>';
                    echo '<th scope="row">' . $item['itemId'] . '</th>';
                    echo '<td>' . $item['title'] . '</td>';
                    echo '<td>' . $item['childcategoryName'] . '</td>';
                    echo '<td>' . $item['fName'] . ' ' . $item['lName'] . '</td>';
                    echo '<td>' . $item['price'] . ' $</td>';
                    echo '<td>' . $item['quantity'] . '</td>';
                    echo '<td>
                            <a href="?do=View&itemId=' . $item['itemId'] . '" class="btn btn-primary">
                                <i class="fas fa-eye"></i> View
                            </a>

                            <a href="?do=Delete&itemId=' . $item['itemId'] . '" class="btn btn-danger">
                                <i class="fas fa-user-minus"></i> Delete
                            </a>
                          </td>';
                    echo '</tr>';
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<?php
} elseif ($do == 'Pending') {

    if (isset($_GET['action']) && isset($_GET['requestId'])) {
        $requestId = intval($_GET['requestId']);

        if ($_GET['action'] == 'approve') {
            $minPrice = $_POST['minPrice'];
            $startTime = $_POST['startTime'];
            $endTime = $_POST['endTime'];

            ApproveSellerRequest($requestId, $minPrice, $startTime, $endTime, $db);
        }

        if ($_GET['action'] == 'reject') {
            RejectSellerRequest($requestId, $db);
        }

        header("Location: items.php?do=Pending");
        exit();
    }

    $requests = GetPendingRequests($db);
?>

<div class="container items">
    <h1 class="text-center">Pending Auction Requests</h1>

    <div class="mb-3 text-center">
        <a href="items.php" class="btn btn-primary">Back to Items</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered text-center">
            <thead class="thead-dark">
                <tr>
                    <th class="table-dark">Request ID</th>
                    <th class="table-dark">Item ID</th>
                    <th class="table-dark">Seller ID</th>
                    <th class="table-dark">Status</th>
                    <th class="table-dark">Created At</th>
                    <th class="table-dark">Approve Settings</th>
                    <th class="table-dark">Control</th>
                </tr>
            </thead>

            <tbody>
            <?php
            if (empty($requests)) {
                echo '<tr>';
                echo '<td colspan="7">No Pending Auction Requests</td>';
                echo '</tr>';
            } else {
                foreach($requests as $req) {
                    echo '<tr>';
                    echo '<td>' . $req["requestId"] . '</td>';
                    echo '<td>' . $req["itemId"] . '</td>';
                    echo '<td>' . $req["sellerId"] . '</td>';
                    echo '<td>' . $req["status"] . '</td>';
                    echo '<td>' . $req["createdAt"] . '</td>';

                    echo '<td>
                            <form action="items.php?do=Pending&action=approve&requestId=' . $req["requestId"] . '" method="POST">
                                <input type="number" name="minPrice" placeholder="Min price" required class="form-control mb-2">
                                <input type="datetime-local" name="startTime" required class="form-control mb-2">
                                <input type="datetime-local" name="endTime" required class="form-control mb-2">

                                <button type="submit" class="btn btn-success">
                                    Approve
                                </button>
                            </form>
                          </td>';

                    echo '<td>
                            <a href="?do=Pending&action=reject&requestId=' . $req["requestId"] . '" 
                               class="btn btn-danger">
                               Reject
                            </a>
                          </td>';

                    echo '</tr>';
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<?php
} elseif ($do == 'ExpiredAuctions') {

    $stmt = $db->prepare("
        SELECT *
        FROM auction
        WHERE status = 'expired'
        ORDER BY paymentDeadline DESC
    ");
    $stmt->execute();
    $expiredAuctions = $stmt->fetchAll();
?>

<div class="container items">
    <h1 class="text-center">Expired Auction Payments</h1>

    <div class="mb-3 text-center">
        <a href="items.php" class="btn btn-primary">Back to Items</a>
    </div>

    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>Auction ID</th>
                <th>Item ID</th>
                <th>Seller ID</th>
                <th>Payment Deadline</th>
                <th>Status</th>
                <th>Note</th>
                <th>Control</th>
            </tr>
        </thead>

        <tbody>
            <?php if(empty($expiredAuctions)): ?>
                <tr>
                    <td colspan="7">No expired auction payments.</td>
                </tr>
            <?php else: ?>
                <?php foreach($expiredAuctions as $auction): ?>
                    <tr>
                        <td><?php echo $auction['auctionId']; ?></td>
                        <td><?php echo $auction['itemId']; ?></td>
                        <td><?php echo $auction['sellerId']; ?></td>
                        <td><?php echo $auction['paymentDeadline']; ?></td>
                        <td><?php echo $auction['status']; ?></td>
                        <td style="color:red;font-weight:bold;">
                            Buyer quá hạn thanh toán, cần kiểm tra dấu hiệu gian lận
                        </td>
                        <td>
                            <a href="auctionBids.php?auctionId=<?php echo $auction['auctionId']; ?>" class="btn btn-primary">
                                View Bids
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
} elseif ($do == 'Delete') {

    $itemId = isset($_GET['itemId']) && is_numeric($_GET['itemId']) ? intval($_GET['itemId']) : 0;

    if (!$itemId) {
        header("Location: items.php");
    } else {
        $item = GetItemByID($itemId, $db);

        if(isset($_POST['submit'])) {
            DeleteItemByID($itemId, $db);
            header("Location: items.php");
        }
    }
?>

<div class="ItemsForm container mb-5">
    <h1 class="text-center">Delete Item</h1>

    <div class="delete-box shadow">
        <h3 class="text-center">
            Are you Sure You Want To Delete 
            <b><?php echo $item[0]['title'] ?></b>
        </h3>

        <form action="?do=Delete&itemId=<?php echo $itemId; ?>" method="POST" class="text-center">
            <button type="submit" name="submit" class="btn btn-danger">Yes</button>
            <a class="btn btn-success" href="?do=Manage">No</a>
        </form>
    </div>
</div>

<?php
} elseif ($do == 'View') {

    $itemId = isset($_GET['itemId']) && is_numeric($_GET['itemId']) ? intval($_GET['itemId']) : 0;

    if (!$itemId) {
        header("Location: items.php");
    } else {
        $item = GetItemViewByID($itemId, $db);
        $images = GetImagesByID($itemId, $db);
        $dataimages = "../data/uploads/items/";
?>

<div class="ItemsForm container mb-5 shadow">
    <section class="review-item">
        <div class="gallery">
            <div id="screen">
                <?php
                if (!empty($images)) {
                    echo '<img src="' . $dataimages . $images[0]['image'] . '" alt="primary">';
                } else {
                    echo '<img src="' . $dataimages . 'default.png" alt="primary">';
                }
                ?>
            </div>

            <div class="thumbnails">
                <?php
                for($i = 0; $i < count($images); $i++) {
                    echo '<img src="' . $dataimages . $images[$i]['image'] . '" alt="primary">';
                }
                ?>
            </div>
        </div>

        <div class="product">
            <a href="?do=Manage" class="seller-name">
                <?php echo $item[0]['fName'] . ' ' . $item[0]['lName']; ?>
            </a>

            <hr>

            <span class="date-of-item">
                <?php echo $item[0]['addDate']; ?>
            </span>

            <p class="item-name">
                <?php echo $item[0]['title']; ?>
            </p>

            <p class="description">
                <?php echo $item[0]['description']; ?>
            </p>

            <div class="price">
                <?php
                if ($item[0]['discount'] == 0) {
                    echo '<div class="new-price">';
                    echo $item[0]['price'] . " $";
                    echo '</div>';
                } else {
                    echo '<div class="new-price">';
                    echo $item[0]['price'] - ($item[0]['price'] * ($item[0]['discount']/100)) . " $";
                    echo '</div>';

                    echo '<div class="discount">';
                    echo $item[0]['discount'] . "%";
                    echo '</div>';

                    echo '<div class="old-price">';
                    echo $item[0]['price'] . " $";
                    echo '</div>';
                }
                ?>
            </div>

            <p class="loctaion">
                Location:
                <?php
                echo $item[0]['homeNumber'] . ', ' .
                     $item[0]['street'] . ' ' .
                     $item[0]['city'] . ' ' .
                     $item[0]['country'];
                ?>
            </p>
        </div>
    </section>
</div>

<?php
    }

} else {
    header("Location: items.php");
}

include $tpl . 'footer.php';
?>
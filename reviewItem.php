<?php
ob_start();
//get the name of the item to update the header
$itemName = isset($_GET['itemName']) ? $_GET['itemName'] : 0;
if (!$itemName) {
  header("Location: index.php");
}
$pageTitle = $itemName;
include 'init.php';

if (isset($_SESSION['typeOfUser']) && $_SESSION['typeOfUser'] == "admin") {
  header("Location: admin/index.php");
}

$itemId = isset($_GET['itemId']) && is_numeric($_GET['itemId']) ? intval($_GET['itemId']) : 0;
if (!$itemId) {
  header("Location: index.php");
}

$do = isset($_GET['do'])? $_GET['do'] : 'Manage';

$item = GetItemByID($itemId, $db)[0];
$images = GetImagesByID($itemId, $db);

if ($do == 'Manage') {
?>

<style>

.review-page{
    margin:60px auto;
}

.review-card{
    background:#ffffff;

    border-radius:28px;

    padding:45px;

    box-shadow:0 12px 45px rgba(0,0,0,0.08);

    display:flex;
    gap:50px;

    align-items:flex-start;
}

/* LEFT SIDE */

.review-gallery{
    width:52%;
}

.main-image{
    width:100%;
    height:620px;

    border-radius:24px;

    overflow:hidden;

    background:#f1f5f9;
}

.main-image img{
    width:100%;
    height:100%;
    object-fit:cover;

    transition:0.3s;
}

.main-image img:hover{
    transform:scale(1.02);
}

.thumbnail-wrapper{
    display:flex;
    gap:16px;

    margin-top:18px;
}

.thumbnail-wrapper img{
    width:95px;
    height:95px;

    object-fit:cover;

    border-radius:16px;

    cursor:pointer;

    border:3px solid transparent;

    transition:0.25s;
}

.thumbnail-wrapper img:hover{
    border-color:#0d6efd;
    transform:translateY(-4px);
}

/* RIGHT SIDE */

.review-info{
    flex:1;
    padding-top:10px;
}

.seller-link{
    display:inline-block;

    text-decoration:none;

    color:#0d6efd;

    font-size:15px;
    font-weight:700;

    margin-bottom:15px;
}

.item-date{
    color:#94a3b8;

    font-size:14px;

    margin-bottom:14px;
}

.review-title{
    font-size:46px;
    font-weight:900;

    color:#0f172a;

    line-height:1.1;

    margin-bottom:22px;
}

.review-desc{
    color:#475569;

    font-size:17px;

    line-height:1.9;

    margin-bottom:34px;
}

.price-box{
    margin-bottom:35px;
}

.final-price{
    display:flex;
    align-items:center;
    gap:14px;
}

.final-price .price{
    font-size:44px;
    font-weight:900;

    color:#0d6efd;
}

.discount-badge{
    background:#eff6ff;

    color:#0d6efd;

    padding:7px 12px;

    border-radius:10px;

    font-size:14px;
    font-weight:800;
}

.old-price{
    color:#94a3b8;

    font-size:22px;

    text-decoration:line-through;

    margin-top:8px;
}

.quantity-box{
    display:flex;
    align-items:center;

    gap:15px;

    margin-bottom:35px;
}

.qty-btn{
    width:48px;
    height:48px;

    border:none;

    border-radius:50%;

    background:#eff6ff;

    color:#0d6efd;

    font-size:22px;
    font-weight:800;

    transition:0.25s;
}

.qty-btn:hover{
    background:#0d6efd;
    color:#fff;
}

.qty-input{
    width:80px;
    height:48px;

    border-radius:14px;

    border:1px solid #dbe4f0;

    text-align:center;

    font-size:18px;
    font-weight:800;
}

.cart-btn{
    display:inline-flex;
    align-items:center;
    gap:12px;

    background:linear-gradient(135deg,#0d6efd,#009dff);

    color:#fff !important;

    text-decoration:none;

    border:none;

    padding:18px 36px;

    border-radius:16px;

    font-size:17px;
    font-weight:800;

    transition:0.3s;
}

.cart-btn:hover{
    transform:translateY(-3px);

    box-shadow:0 12px 24px rgba(13,110,253,0.3);
}

.sold-out{
    display:inline-block;

    background:#fee2e2;
    color:#dc2626;

    padding:10px 18px;

    border-radius:12px;

    font-size:14px;
    font-weight:800;

    margin-bottom:20px;
}

/* MOBILE */

@media(max-width:992px){

    .review-card{
        flex-direction:column;
        padding:24px;
    }

    .review-gallery{
        width:100%;
    }

    .main-image{
        height:420px;
    }

    .review-title{
        font-size:34px;
    }

}

</style>

<div class="container review-page">

<div class="review-card">

    <!-- LEFT -->

    <div class="review-gallery">

        <div class="main-image">

            <?php 
                if (!empty($images)) {
                    echo '<img id="mainProductImage" src="' . $dataimages . $images[0]['image'] . '">';
                } else {
                    echo '<img id="mainProductImage" src="' . $dataimages . 'default.png">';
                }
            ?>

        </div>

        <div class="thumbnail-wrapper">

            <?php 
                for($i = 0; $i < count($images); $i++) {

                    echo '
                    <img 
                        onclick="changeImage(this)"
                        src="' . $dataimages . $images[$i]['image'] . '">
                    ';
                }
            ?>

        </div>

    </div>

    <!-- RIGHT -->

    <div class="review-info">

        <a class="seller-link"
        href="seller.php?id=<?php echo $item['sellerId'] ?>">
            By:
            <?php echo $item['fName'] . ' ' . $item['lName']; ?>
        </a>

        <div class="item-date">
            Added in :
            <?php echo $item['addDate']; ?>
        </div>

        <?php 
            if ($item['quantity'] == 0) {
                echo '<span class="sold-out">Sold Out</span>';
            }
        ?>

        <h1 class="review-title">
            <?php echo $itemName; ?>
        </h1>

        <div class="review-desc">
            <?php echo $item['description']; ?>
        </div>

        <div class="price-box">

            <?php 
                if ($item['discount'] == 0) {

                    echo '
                    <div class="final-price">
                        <div class="price">
                            '.$item['price'].' $
                        </div>
                    </div>
                    ';

                } else {

                    echo '
                    <div class="final-price">

                        <div class="price">
                            '.($item['price'] - ($item['price'] * ($item['discount']/100))).' $
                        </div>

                        <div class="discount-badge">
                            -'.$item['discount'].'%
                        </div>

                    </div>

                    <div class="old-price">
                        '.$item['price'].' $
                    </div>
                    ';
                }
            ?>

        </div>

        <?php
        if (isset($_SESSION['typeOfUser']) 
            && $_SESSION['typeOfUser'] == 'buyer' 
            && $item['quantity'] != 0) {

            $cartID = GetCartIDFromBuyer($_SESSION['id'], $db)[0]['cartId'];

            if (CheckBuyerAndItem($cartID, $itemId, $db)) {

                echo '
                <form action="?do=Update&itemId='.$itemId.'&itemName='.$itemName.'" method="POST">

                    <div class="quantity-box">

                        <button type="button" class="qty-btn" onclick="decreaseQty()">
                            -
                        </button>

                        <input type="number"
                        id="qtyInput"
                        class="qty-input"
                        min="1"
                        name="quan"
                        value="1">

                        <button type="button"
                        class="qty-btn"
                        onclick="increaseQty('.$item['quantity'].')">
                            +
                        </button>

                    </div>

                    <button class="cart-btn">
                        🛒 Oder Cart
                    </button>

                </form>
                ';

            } else {
        ?>

        <form action="?do=Confirm&itemId=<?php echo $itemId ?>&itemName=<?php echo $itemName; ?>"
        method="POST">

            <div class="quantity-box">

                <button type="button"
                class="qty-btn"
                onclick="decreaseQty()">
                    -
                </button>

                <input type="number"
                id="qtyInput"
                class="qty-input"
                min="1"
                name="quan"
                value="1">

                <button type="button"
                class="qty-btn"
                onclick="increaseQty(<?php echo $item['quantity']; ?>)">
                    +
                </button>

            </div>

            <button class="cart-btn">
                🛒 Add to cart
            </button>

        </form>

        <?php } } ?>

    </div>

</div>

</div>

<script>

function changeImage(img){

    document.getElementById("mainProductImage").src = img.src;
}

function increaseQty(max){

    let input = document.getElementById("qtyInput");

    let current = parseInt(input.value);

    if(current < max){
        input.value = current + 1;
    }
}

function decreaseQty(){

    let input = document.getElementById("qtyInput");

    let current = parseInt(input.value);

    if(current > 1){
        input.value = current - 1;
    }
}

</script>

<?php
} elseif ($do == 'Confirm' && isset($_SESSION['id']) && $_SESSION['typeOfUser'] == "buyer") {
  $num = $_POST['quan'];
  $trueQuantity = QuantityOfItem($item['itemId'], $db)[0]['quantity'];
  if ($num > $trueQuantity) {
    $_SESSION['trueQuantity'] = $trueQuantity;
    header("Location: ?do=Manage&itemId=" . $_GET['itemId'] . "&itemName=" . $_GET['itemName'] . "");
  }
  if (isset($_POST['submit'])) {
    //add this item to the cart of that buyer(session_id)
    $num = $_GET['quantity'];
    //get the cart id from the buyer info
    $cartID = GetCartIDFromBuyer($_SESSION['id'], $db)[0]['cartId'];
    //update the itemCount and payment in cart
    $price = $num * ($item['price'] - ($item['price'] * ($item['discount']/100)));
    UpdateItemCount($cartID, $price, $item['itemId'], $db);
    //insert into the cart item table
    InsertCartItem($cartID, $item['itemId'], $num, $db);
    header("Location: cart.php");
  } else {
  $num = $_POST['quan'];
  if ($num <= 0) {
    header("Location: reviewitem.php?do=Manage&itemId=" . $itemId . "&itemName=" . $itemName);
  } else {
?>

<div class="container shadow add-to-cart" style="line-height: 2.5;">
  <h1 class="text-center">Add to Cart</h1>
  <div class="info-section" >
    <div class="item-name" style="font-size: 20px"><b style="font-size: 20px">Item:</b> <?php echo $item['title']; ?></div>
    <div class="final-price" style="font-size: 20px"><b style="font-size: 20px">Price:</b> <?php echo $item['price'] - ($item['price'] * ($item['discount']/100)); ?> $
    </div>
    
    <!-- <div class="quantity"><b>Quantity:</b> <//?php echo $num; ?></div> -->
    <div class="quantity" style="font-size: 20px"><b style="font-size: 20px">Quantity:</b> <?php echo $num; ?></div>
    <div class="total-price" style="font-size: 20px"><b style="font-size: 20px">Total Price:</b>
      <?php echo $num * ($item['price'] - ($item['price'] * ($item['discount']/100))); ?> $</div>
    <div class="location" style="font-size: 20px"><b style="font-size: 20px">Location:</b> <?php echo $item['homeNumber'] . ', ' .
                                  $item['street'] . ' ' . $item['city'] . ' ' . $item['country'];?></div>
  </div>
  <form action="?do=Confirm&itemId=<?php echo $itemId ?>&itemName=<?php echo $itemName; ?>&quantity=<?php echo $num; ?>"
    method="POST" class="text-center" method="POST">
    <button type="submit" name="submit" class="btn btn-success" style = "font-family:candara;letter-spacing: 0.05em;">Confirm</button>
    <a class="btn btn-danger" href="<?php echo '?do=Manage&itemId=' . $itemId . '&itemName=' . $itemName; ?>" style = "font-family:candara;letter-spacing: 0.05em;">Go Back</a>
  </form>
</div>

<?php
    }
  }
} elseif ($do == 'Update' && isset($_SESSION['id']) && $_SESSION['typeOfUser'] == "buyer") {
  if (isset($_POST['submit'])) {
    $cartID = GetCartIDFromBuyer($_SESSION['id'], $db)[0]['cartId'];
    //we need to delete the tuple with cartID and itemID
    $num = $_GET['quantity'];
    $oldnum = SelectQuantityOfItem($cartID, $itemId, $db)[0]['quantity'];
    $newPrice = $num * ($item['price'] - ($item['price'] * ($item['discount']/100)));
    $oldPrice = $oldnum * ($item['price'] - ($item['price'] * ($item['discount']/100)));
    UpdateItemCountPrice($cartID, $oldPrice, $newPrice, $db);
    UpdateCartItem($cartID, $itemId, $num, $db);
    header("Location: cart.php");
  } else {
  $num = $_POST['quan'];
  if ($num <= 0) {
    header("Location: reviewitem.php?do=Manage&itemId=" . $itemId . "&itemName=" . $itemName);
  } else {
?>

<div class="container shadow add-to-cart" style="line-height: 2.5;">
  <h1 class="text-center">Order The Cart</h1>
  <div class="info-section">
    <div class="item-name" style="font-size: 20px"><b style="font-size: 20px">Item:</b> <?php echo $item['title']; ?></div>
    <div class="final-price"style="font-size: 20px"><b style="font-size: 20px">Price:</b> <?php echo $item['price'] - ($item['price'] * ($item['discount']/100)); ?> $
    </div>
    <div class="quantity"style="font-size: 20px"><b style="font-size: 20px">Quantity:</b> <?php echo $num; ?></div>
    <div class="total-price"style="font-size: 20px"><b style="font-size: 20px">Total Price:</b>
      <?php echo $num * ($item['price'] - ($item['price'] * ($item['discount']/100))); ?> $</div>
    <div class="location"style="font-size: 20px"><b style="font-size: 20px">Location:</b> <?php echo $item['homeNumber'] . ', ' .
                                  $item['street'] . ' ' . $item['city'] . ' ' . $item['country'];?></div>
  </div>
  <form action="?do=Update&itemId=<?php echo $itemId ?>&itemName=<?php echo $itemName; ?>&quantity=<?php echo $num; ?>"
    method="POST" class="text-center" method="POST">
    <button type="submit" name="submit" class="btn btn-success" style = "font-family:candara;letter-spacing: 0.05em;">Confirm</button>
    <a class="btn btn-danger" href="<?php echo '?do=Manage&itemId=' . $itemId . '&itemName=' . $itemName; ?>" style = "font-family:candara;letter-spacing: 0.05em;">Go
      Back</a>
  </form>
</div>

<?php
    }
  }
}

include $tpl . 'footer.php';
ob_end_flush();
?>
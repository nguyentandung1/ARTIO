<?php
global $dataimages;
ob_start();

$pageTitle = 'Art presents';
require "init.php";

$items = getItems($db);
$childcategories = getChildCategories($db);
$itemImages = getItemsImages($db);

$childcategories = array_reverse($childcategories);

$noItems = false;

if (isset($_GET['childcat'])) {

    $items = getItemsByChildCategory($db, $_GET['childcat']);

    if (count($items) == 0) {
        $noItems = true;
    }
}

$inputSearchError = false;
$noItemsSearch = false;

if (isset($_GET['keyword'])) {

    $_GET['keyword'] = htmlspecialchars($_GET['keyword']);

    if ($_GET['keyword'] == "") {

        $inputSearchError = true;

    } else {

        $items = searchForItems($db, $_GET['keyword']);

        if (count($items) == 0) {
            $noItemsSearch = true;
        }
    }
}

/* LINK CATEGORY */

for ($i = 0; $i < count($childcategories); ++$i) {

    for ($k = 0; $k < count($items); ++$k) {

        if ($items[$k]['childcategoryId'] == $childcategories[$i]['childcategoryId']) {

            $items[$k]["childcategoryName"] =
                $childcategories[$i]['childcategoryName'];
        }
    }
}

/* LINK IMAGE */

for ($i = 0; $i < count($itemImages); ++$i) {

    for ($k = 0; $k < count($items); ++$k) {

        if ($items[$k]['itemId'] == $itemImages[$i]['itemId']) {

            $items[$k]["image"] =
                $itemImages[$i]['image'];
        }
    }
}
?>

<style>

.acc-page{

    background:#f6f9ff;

    min-height:700px;

    padding:50px 0 70px;
}

.acc-wrapper{

    width:94%;

    max-width:1400px;

    margin:auto;
}

/* TITLE */

.acc-title{

    margin-bottom:35px;
}

.acc-title h2{

    font-size:34px;

    font-weight:900;

    color:#0f172a;

    margin-bottom:8px;
}

.acc-title p{

    color:#64748b;

    font-size:15px;
}

/* GRID */

.acc-grid{

    display:grid;

    grid-template-columns:repeat(4,1fr);

    gap:30px;
}

/* CARD */

.product-card{

    background:#fff;

    border-radius:26px;

    overflow:hidden;

    box-shadow:0 10px 30px rgba(0,0,0,0.07);

    transition:0.3s;

    border:1px solid #edf2f7;

    position:relative;
}

.product-card:hover{

    transform:translateY(-8px);

    box-shadow:0 18px 45px rgba(0,0,0,0.12);
}

/* IMAGE */

.product-image{

    width:100%;

    height:280px;

    overflow:hidden;

    position:relative;

    background:#eef4ff;
}

.product-image img{

    width:100%;

    height:100%;

    object-fit:cover;

    transition:0.35s;
}

.product-card:hover .product-image img{

    transform:scale(1.07);
}

/* DISCOUNT */

.discount-badge{

    position:absolute;

    top:14px;
    right:14px;

    background:#ffedd5;

    color:#ea580c;

    padding:7px 12px;

    border-radius:999px;

    font-size:12px;

    font-weight:800;
}

/* SOLD OUT */

.sold-badge{

    position:absolute;

    top:14px;
    left:14px;

    background:#fee2e2;

    color:#dc2626;

    padding:7px 12px;

    border-radius:999px;

    font-size:12px;

    font-weight:800;
}

/* INFO */

.product-info{

    padding:22px;
}

.product-name{

    color:#0f172a;

    font-size:20px;

    font-weight:900;

    margin-bottom:8px;

    white-space:nowrap;

    overflow:hidden;

    text-overflow:ellipsis;
}

.product-category{

    color:#0d6efd;

    font-size:12px;

    font-weight:800;

    letter-spacing:1px;

    text-transform:uppercase;

    margin-bottom:14px;
}

.product-desc{

    color:#64748b;

    font-size:14px;

    line-height:1.7;

    height:70px;

    overflow:hidden;

    margin-bottom:18px;
}

/* PRICE */

.price-row{

    display:flex;

    align-items:center;

    gap:12px;

    margin-bottom:20px;
}

.new-price{

    color:#0d6efd;

    font-size:24px;

    font-weight:900;
}

.old-price{

    color:#94a3b8;

    font-size:15px;

    text-decoration:line-through;
}

/* BUTTON */

.review-btn{

    display:block;

    width:100%;

    text-align:center;

    background:linear-gradient(135deg,#0d6efd,#1da1ff);

    color:#fff !important;

    text-decoration:none;

    padding:13px 18px;

    border-radius:16px;

    font-size:14px;

    font-weight:900;

    transition:0.25s;
}

.review-btn:hover{

    transform:translateY(-2px);

    box-shadow:0 12px 25px rgba(13,110,253,0.25);
}

/* EMPTY */

.empty-box{

    background:#fff;

    border-radius:22px;

    padding:45px;

    text-align:center;

    color:#64748b;

    font-size:16px;

    box-shadow:0 10px 30px rgba(0,0,0,0.07);
}

/* RESPONSIVE */

@media(max-width:1200px){

    .acc-grid{

        grid-template-columns:repeat(3,1fr);
    }
}

@media(max-width:900px){

    .acc-grid{

        grid-template-columns:repeat(2,1fr);
    }

    .product-image{

        height:240px;
    }
}

@media(max-width:560px){

    .acc-page{

        padding:30px 0 50px;
    }

    .acc-grid{

        grid-template-columns:1fr;

        gap:22px;
    }

    .product-image{

        height:260px;
    }

    .acc-title h2{

        font-size:28px;
    }
}

</style>

<div class="acc-page">

    <div class="acc-wrapper">

        <div class="acc-title">

            <h2>Tác phẩm nghệ thuật</h2>

            <p>
                Khám phá những tác phẩm nghệ thuật nổi bật trên ARTIO
            </p>

        </div>

        <?php if(count($items) == 0): ?>

            <div class="empty-box">
                Không có sản phẩm nào trong danh mục này.
            </div>

        <?php else: ?>

            <div class="acc-grid">

                <?php foreach($items as $ite): ?>

                    <?php if($ite['isDeleted'] == 0): ?>

                        <?php

                        $imageSrc = $dataimages . "default.png";

                        if(isset($ite['image']) && $ite['image'] != ""){

                            $imageSrc =
                                $dataimages . $ite['image'];
                        }

                        $reviewLink =
                            "reviewitem.php?do=Manage&itemId="
                            . urlencode($ite['itemId'])
                            . "&itemName="
                            . urlencode($ite['title']);

                        $finalPrice = $ite['price'];

                        if($ite['discount'] > 0){

                            $finalPrice =
                                $ite['price']
                                -
                                ($ite['price']
                                *
                                ($ite['discount']/100));
                        }

                        ?>

                        <div class="product-card">

                            <a href="<?php echo $reviewLink; ?>"
                            style="text-decoration:none;">

                                <div class="product-image">

                                    <img src="<?php echo $imageSrc; ?>">

                                    <?php if($ite['discount'] > 0): ?>

                                        <span class="discount-badge">

                                            -<?php echo $ite['discount']; ?>%

                                        </span>

                                    <?php endif; ?>

                                    <?php if($ite['quantity'] == 0): ?>

                                        <span class="sold-badge">

                                            SOLD OUT

                                        </span>

                                    <?php endif; ?>

                                </div>

                            </a>

                            <div class="product-info">

                                <div class="product-name">

                                    <?php
                                    echo htmlspecialchars($ite['title']);
                                    ?>

                                </div>

                                <div class="product-category">

                                    <?php
                                    echo htmlspecialchars(
                                        $ite['childcategoryName']
                                    );
                                    ?>

                                </div>

                                <div class="product-desc">

                                    <?php
                                    echo htmlspecialchars(
                                        $ite['description']
                                    );
                                    ?>

                                </div>

                                <div class="price-row">

                                    <div class="new-price">

                                        <?php
                                        echo number_format(
                                            $finalPrice,
                                            0,
                                            ',',
                                            '.'
                                        );
                                        ?>$

                                    </div>

                                    <?php if($ite['discount'] > 0): ?>

                                        <div class="old-price">

                                            <?php
                                            echo number_format(
                                                $ite['price'],
                                                0,
                                                ',',
                                                '.'
                                            );
                                            ?>$

                                        </div>

                                    <?php endif; ?>

                                </div>

                                <?php if($ite['quantity'] == 0): ?>

                                    <span class="review-btn"
                                    style="background:#94a3b8;">

                                        Đã bán hết

                                    </span>

                                <?php else: ?>

                                    <a href="<?php echo $reviewLink; ?>"
                                    class="review-btn">

                                        Xem chi tiết

                                    </a>

                                <?php endif; ?>

                            </div>

                        </div>

                    <?php endif; ?>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</div>

<?php
include $tpl . "footer.php";
ob_end_flush();
?>
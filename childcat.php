<?php
ob_start();
require "init.php";

$items = getItems($db);
$childcategories = getChildCategories($db);

if (isset($_GET['cat'])) {
    $childcategories = getChildByCategory($db, $_GET['cat']);
}

$childcategories = array_reverse($childcategories);
?>

<style>
.childcat-page {
    background: #f8fbff;
    padding: 45px 0 60px;
    min-height: 650px;
}

.childcat-wrapper {
    width: 92%;
    max-width: 1280px;
    margin: auto;
}

.childcat-heading {
    margin-bottom: 30px;
}

.childcat-heading h2 {
    color: #0f172a;
    font-size: 30px;
    font-weight: 900;
    margin-bottom: 8px;
}

.childcat-heading p {
    color: #64748b;
    font-size: 15px;
}

.childcat-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 28px;
}

.childcat-card {
    background: #fff;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    transition: 0.25s;
    border: 1px solid #edf2f7;
}

.childcat-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 38px rgba(15, 23, 42, 0.13);
}

.childcat-image {
    width: 100%;
    height: 260px;
    background: #eef5ff;
    overflow: hidden;
}

.childcat-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: 0.3s;
}

.childcat-card:hover .childcat-image img {
    transform: scale(1.05);
}

.childcat-info {
    padding: 22px;
}

.childcat-name {
    color: #0f172a;
    font-size: 21px;
    font-weight: 900;
    margin-bottom: 14px;
    text-transform: uppercase;
}

.childcat-stats {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.stat-box {
    flex: 1;
    min-width: 110px;
    background: #f8fafc;
    border-radius: 14px;
    padding: 12px;
}

.stat-label {
    color: #64748b;
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 4px;
}

.stat-value {
    color: #0d6efd;
    font-size: 18px;
    font-weight: 900;
}

.view-btn {
    display: block;
    width: 100%;
    text-align: center;
    background: linear-gradient(135deg, #0d6efd, #10a8ff);
    color: #fff !important;
    text-decoration: none;
    padding: 13px 18px;
    border-radius: 14px;
    font-size: 14px;
    font-weight: 800;
    transition: 0.25s;
}

.view-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(13, 110, 253, 0.25);
}

.empty-childcat {
    background: #fff;
    padding: 40px;
    border-radius: 18px;
    text-align: center;
    color: #64748b;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
}

@media (max-width: 992px) {
    .childcat-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .childcat-image {
        height: 230px;
    }
}

@media (max-width: 576px) {
    .childcat-page {
        padding: 25px 0 40px;
    }

    .childcat-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .childcat-heading h2 {
        font-size: 24px;
    }

    .childcat-image {
        height: 220px;
    }
}
</style>

<?php if(isset($_GET['keyword'])): ?>
    <?php if ($inputSearchError): ?>
        <p class="alert-danger ms-auto me-auto pt-5 pb-5" style="width:50%">
            Enter a valid value!
        </p>
    <?php elseif($noItemsSearch): ?>
        <p class="alert-danger ms-auto me-auto pt-5 pb-5" style="width:50%">
            No items match this word <?php echo htmlspecialchars($_GET['keyword']); ?>
        </p>
    <?php elseif($noItems): ?>
        <p class="alert-danger ms-auto me-auto pt-5 pb-5" style="width:50%">
            No items in this Category
        </p>
    <?php else: ?>
        <?php header("Location: searchItem.php?keyword=".$_GET['keyword']); ?>
    <?php endif; ?>
<?php endif; ?>

<div class="childcat-page">
    <div class="childcat-wrapper">

        <div class="childcat-heading">
            <h2>Danh mục tác phẩm</h2>
            <p>Khám phá các nhóm tác phẩm nghệ thuật trong ARTIO</p>
        </div>

        <?php if(empty($childcategories)): ?>

            <div class="empty-childcat">
                Hiện chưa có danh mục con nào.
            </div>

        <?php else: ?>

            <div class="childcat-grid">

                <?php foreach($childcategories as $childcat): ?>

                    <?php
                        $soldCount = 0;

                        foreach($items as $item) {
                            if($item['childcategoryId'] == $childcat['childcategoryId']) {
                                $soldCount += getQuantityFromOrdersAndItems($item['itemId'], $db);
                            }
                        }

                        $imagePath = 'layout/images/' . $childcat['childcategoryName'] . '.jpg';
                    ?>

                    <div class="childcat-card">

                        <div class="childcat-image">
                            <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($childcat['childcategoryName']); ?>">
                        </div>

                        <div class="childcat-info">

                            <h3 class="childcat-name">
                                <?php echo htmlspecialchars($childcat['childcategoryName']); ?>
                            </h3>

                            <div class="childcat-stats">

                                <div class="stat-box">
                                    <div class="stat-label">Số lượng</div>
                                    <div class="stat-value">
                                        <?php echo htmlspecialchars($childcat['totalItems']); ?>
                                    </div>
                                </div>

                                <div class="stat-box">
                                    <div class="stat-label">Đã bán</div>
                                    <div class="stat-value">
                                        <?php echo $soldCount; ?>
                                    </div>
                                </div>

                            </div>

                            <a class="view-btn" href="<?php echo 'acc.php?childcat=' . urlencode($childcat['childcategoryId']); ?>">
                                Xem tất cả
                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>
</div>

<?php
include $tpl . 'footer.php';
ob_end_flush();
?>

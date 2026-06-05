<?php
  ob_start();
  $pageTitle = 'Home Page';
  require "init.php";

  $categories = getCategories($db);
  $itemImages = getItemsImages($db);

  $featuredItems = getFeaturedItems($db, 10);
  $latestItems = getLatestItems($db, 10);

  $categories = array_reverse($categories);

  $noItems = false;
  if(isset($_GET['cat'])){
    $items = getItemsByCategory($db,$_GET['cat']);
    if(count($items)==0)
      $noItems = true;
  }

  $inputSearchError = false;
  $noItemsSearch = false;

  if(isset($_GET['keyword'])){
    $_GET['keyword'] = htmlspecialchars($_GET['keyword']);
    if($_GET['keyword'] == "")
      $inputSearchError = true;
    else {
      $items = searchForItems($db,$_GET['keyword']);
      if(count($items)==0)
        $noItemsSearch = true;
    }
  }
?>

<div class="container">
  <main class="home-main">
    <div class="text-center">

      <?php if(isset($_GET['keyword'])): ?>

        <?php if ($inputSearchError) :?>
          <p class="alert-danger ms-auto me-auto pt-5 pb-5" style="width:50%;">Enter a valid value!</p>

        <?php elseif(($noItemsSearch)): ?>
          <p class="alert-danger ms-auto me-auto pt-5 pb-5" style="width:50%;">
            No items match <?php echo $_GET['keyword']; ?>
          </p>

        <?php elseif($noItems): ?>
          <p class="alert-danger ms-auto me-auto pt-5 pb-5" style="width:50%;">
            No items in this Category
          </p>

        <?php else: ?>
          <?php header("Location: searchItem.php?keyword=".$_GET['keyword']); ?>
        <?php endif ?>

      <?php else : ?>

      <div class="breadcrumb"></div>

      <div class="home-layout">

        <!-- SIDEBAR -->
        <div class="sidebar home-sidebar">
          <div class="sidebar-widget">
            <h3>Category</h3>
            <ul>
              <?php foreach($categories as $cat): ?>
                <li>
                  <a href="<?php echo "childcat.php?cat=".urlencode($cat['categoryId']); ?>">
                    <?php echo htmlspecialchars($cat['categoryName']); ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>

            <div class="artist-box">
                <div class="artist-box-icon">🖼️</div>

                <h4>Không gian nghệ thuật</h4>

                <p>
                    Khám phá, trưng bày và mua bán các tác phẩm nghệ thuật độc đáo trên ARTIO.
                </p>
            </div>

          </div>
        </div>

        <!-- RIGHT CONTENT -->
        <div class="home-content">

          <!-- CATEGORY GRID CŨ -->
          <div class="product-content">

            <?php foreach($categories as $cat): ?>

              <div class="product">

                <a href="<?php echo "childcat.php?cat=".urlencode($cat['categoryId']); ?>">

                  <img src="<?php echo "layout/images/" . $cat['categoryName'] . ".png"; ?>">

                </a>

                <div class="product-detail">
                  <h3><?php echo htmlspecialchars($cat['categoryName']); ?></h3>

                  <a href="<?php echo "childcat.php?cat=".urlencode($cat['categoryId']); ?>">
                    VIEW MORE
                  </a>
                </div>

              </div>

            <?php endforeach; ?>

          </div>

          <!-- SLIDER 1 -->
          <section class="product-slider-box">
            <div class="slider-header">
              <h3>☆ Sản phẩm nổi bật</h3>
              <a href="shop.php">Xem tất cả ›</a>
            </div>

            <div class="slider-wrapper">
              <button class="slider-btn prev-btn" type="button" onclick="slideProducts('featuredSlider', -1)">‹</button>

              <div class="slider-window">
                <div class="slider-track" id="featuredSlider">

                  <?php foreach($featuredItems as $item): ?>
                    <?php
                      $imgSrc = "layout/images/default.png";

                      if (!empty($item['image'])) {
                        $imgSrc = "data/uploads/items/" . $item['image'];
                      }

                      $reviewUrl = "reviewitem.php?do=Manage&itemId="
                        . urlencode($item['itemId'])
                        . "&itemName="
                        . urlencode($item['title']);
                    ?>

                    <a class="product-card" href="<?php echo $reviewUrl; ?>">
                      <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">

                      <h4><?php echo htmlspecialchars($item['title']); ?></h4>

                      <p><?php echo htmlspecialchars($item['sellerName']); ?></p>

                      <strong>
                        <?php echo number_format($item['price'], 0, ',', '.'); ?>đ
                      </strong>
                    </a>

                  <?php endforeach; ?>

                </div>
              </div>

              <button class="slider-btn next-btn" type="button" onclick="slideProducts('featuredSlider', 1)">›</button>
            </div>
          </section>

          <!-- SLIDER 2 -->
          <section class="product-slider-box">
            <div class="slider-header">
              <h3>◷ Sản phẩm mới nhất</h3>
              <a href="shop.php">Xem tất cả ›</a>
            </div>

            <div class="slider-wrapper">
              <button class="slider-btn prev-btn" type="button" onclick="slideProducts('latestSlider', -1)">‹</button>

              <div class="slider-window">
                <div class="slider-track" id="latestSlider">

                  <?php foreach($latestItems as $item): ?>
                    <?php
                      $imgSrc = "layout/images/default.png";

                      if (!empty($item['image'])) {
                        $imgSrc = "data/uploads/items/" . $item['image'];
                      }

                      $reviewUrl = "reviewitem.php?do=Manage&itemId="
                        . urlencode($item['itemId'])
                        . "&itemName="
                        . urlencode($item['title']);
                    ?>

                    <a class="product-card small-card" href="<?php echo $reviewUrl; ?>">
                      <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">

                      <h4><?php echo htmlspecialchars($item['title']); ?></h4>

                      <strong>
                        <?php echo number_format($item['price'], 0, ',', '.'); ?>đ
                      </strong>
                    </a>

                  <?php endforeach; ?>

                </div>
              </div>

              <button class="slider-btn next-btn" type="button" onclick="slideProducts('latestSlider', 1)">›</button>
            </div>
          </section>

        </div>
      </div>

      <?php endif ?>

    </div>
  </main>
</div>

<style>

.home-main,
.home-main * {
    font-family: Arial, Helvetica, sans-serif;
}

.home-main {
    box-shadow: rgba(0, 0, 0, 0.25) 0px 0px 12px;
    background: #fff;
}

.breadcrumb {
    background-color: #FFBE98;
    height: 18px;
    margin: 28px 28px 10px;
}

.home-layout {
    display: flex;
    align-items: flex-start;
    gap: 18px;
    padding: 20px 12px 25px;
}

.home-sidebar {
    width: 210px;
    min-width: 210px;
    flex: 0 0 210px;
}

.sidebar-widget {
    border-right: 1px solid #eee;
}

.sidebar-widget h3 {
    color: #c44b0b;
    font-size: 16px;
    font-weight: 800;
    text-transform: uppercase;
    margin-bottom: 18px;
    padding-bottom: 12px;
    border-bottom: 1px solid #ddd;
}

.sidebar-widget ul {
    padding: 0;
    margin: 0;
    list-style: none;
}

.sidebar-widget ul li {
    text-align: left;
    padding: 0 0 14px 25px;
}

.sidebar-widget ul li a {
    color: #20bf55;
    text-decoration: none;
    font-size: 14px;
    text-transform: uppercase;
}

.sidebar-widget ul li a:hover {
    color: #0969e8;
}

.home-content {
    flex: 1;
    min-width: 0;
    width: 100%;
}

.product-slider-box {
    width: 100%;
    background: #fff;
    border-radius: 16px;
    padding: 24px 28px 30px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.08);
    margin-bottom: 28px;
}

.slider-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 22px;
}

.slider-header h3 {
    margin: 0;
    color: #0969e8;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 21px;
    font-weight: 700;
    line-height: 1.3;
    letter-spacing: 0;
}

.slider-header a {
    color: #0969e8;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
}

.slider-wrapper {
    position: relative;
}

.slider-window {
    width: 100%;
    overflow: hidden;
}

.slider-track {
    display: flex;
    gap: 22px;
    transition: transform 0.5s ease;
}

.product-card {
    flex: 0 0 calc((100% - 88px) / 5);
    color: #222;
    text-decoration: none;
    text-align: left;
}

.product-card img {
    width: 100%;
    height: 240px;
    object-fit: cover;
    border-radius: 10px;
    display: block;
}

.product-card h4 {
    margin: 13px 0 6px;
    color: #263044;
    font-size: 15px;
    font-weight: 800;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.product-card p {
    margin: 0 0 8px;
    color: #8b95a7;
    font-size: 13px;
}

.product-card strong {
    color: #0969e8;
    font-size: 16px;
    font-weight: 800;
}

.small-card img {
    height: 155px;
}

.slider-btn {
    position: absolute;
    top: 38%;
    transform: translateY(-50%);
    width: 44px;
    height: 44px;
    border: none;
    border-radius: 50%;
    background: #fff;
    color: #1f2937;
    font-size: 32px;
    line-height: 1;
    box-shadow: 0 4px 14px rgba(0,0,0,0.18);
    cursor: pointer;
    z-index: 5;
}

.prev-btn {
    left: -18px;
}

.next-btn {
    right: -18px;
}

.slider-btn:hover {
    color: #0969e8;
}

/* CATEGORY GRID GIỮ KIỂU BAN ĐẦU */
.product-content {
    margin-top: 0;
    flex: 1;
    display: flex;
    flex-wrap: wrap;
    gap: 50px;
    justify-content: flex-start;
}

.product-content .product {
    width: 100%;
    flex: 0 0 calc(33.33% - 40px);
    max-width: calc(33.33% - 40px);
    background: #fff;
    border-radius: 28px;
    padding: 24px 10px 20px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.08);
    margin-bottom: 10px;
    text-align: center;
    transition: 0.25s;
}

.product-content .product img {
    width: 70%;
    aspect-ratio: 1/1;
    object-fit: cover;
    border-radius: 50%;
    display: block;
    margin: auto;
}

.product-detail {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 10px;
}

.product-detail h3 {
    font-size: 1.2em;
    font-weight: bold;
    color: #22c55e;
}

.product-detail a {
    color: #c94b0b;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    border: 1px solid #c94b0b;
    padding: 7px 13px;
    border-radius: 20px;
}

/* RESPONSIVE */
@media (max-width: 992px) {
    .home-layout {
        flex-direction: column;
        padding: 25px 18px;
    }

    .home-sidebar {
        width: 100%;
        min-width: 100%;
        flex: 0 0 auto;
    }

    .sidebar-widget {
        border-right: none;
    }

    .sidebar-widget ul {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
    }

    .sidebar-widget ul li {
        padding: 0;
    }

    .product-card {
        flex: 0 0 calc((100% - 44px) / 3);
    }

    .product-card img {
        height: 210px;
    }

    .small-card img {
        height: 140px;
    }

    .product-content .product {
        flex: 0 0 calc(50% - 30px);
        max-width: calc(50% - 30px);
    }
}

@media (max-width: 576px) {
    .home-layout {
        padding: 18px 10px;
    }

    .breadcrumb {
        margin: 18px 14px 5px;
    }

    .product-slider-box {
        padding: 18px 16px 24px;
    }

    .slider-header h3 {
        font-size: 18px;
    }

    .product-card {
        flex: 0 0 calc((100% - 22px) / 2);
    }

    .product-card img {
        height: 165px;
    }

    .small-card img {
        height: 125px;
    }

    .slider-btn {
        width: 36px;
        height: 36px;
        font-size: 26px;
    }

    .prev-btn {
        left: -10px;
    }

    .next-btn {
        right: -10px;
    }

    .product-content {
        gap: 20px;
        justify-content: center;
    }

    .product-content .product {
        flex: 0 0 calc(50% - 15px);
        max-width: calc(50% - 15px);
    }
}

/* ===== SIDEBAR CLEAN MODERN ===== */

.home-sidebar{
    background:#fff;
    border-radius:18px;
    padding:22px 18px;
    box-shadow:0 6px 24px rgba(0,0,0,0.06);
    border:1px solid #edf2f7;
}

.sidebar-widget{
    border-right:none !important;
}

.sidebar-widget h3{
    color:#0f172a !important;
    font-size:18px !important;
    font-weight:800 !important;
    margin-bottom:18px !important;
    padding-bottom:14px !important;
    border-bottom:1px solid #e5e7eb !important;
    letter-spacing:0.5px;
}

.sidebar-widget h3::before{
    content:"🎨";
    margin-right:10px;
}

.sidebar-widget ul{
    padding:0;
    margin:0;
    list-style:none;
}

.sidebar-widget ul li{
    margin-bottom:6px;
    padding:0 !important;
}

.sidebar-widget ul li a{
    display:flex;
    align-items:center;
    padding:11px 12px;
    border-radius:10px;
    color:green !important;
    text-decoration:none;
    font-size:14px !important;
    font-weight:700;
    transition:0.2s;
}

.sidebar-widget ul li a::before{
    display:none;
}

.sidebar-widget ul li a:hover{
    background:#f1f5f9;
    color:#2563eb !important;
    transform:translateX(3px);
}

.sidebar-widget ul li a:hover::before{
    background:#2563eb;
}

/* CTA BOX */

.artist-box{
    margin-top:22px;
    background:linear-gradient(135deg,#eff6ff,#dbeafe);
    border-radius:16px;
    padding:24px 18px;
    text-align:center;
    width:100%;
}

.artist-box-icon{
    font-size:42px;
    margin-bottom:10px;
}

.artist-box h4{
    color:#0f172a;
    font-family: Arial, Helvetica, sans-serif !important;
    font-size:16px;
    font-weight:800;
    line-height:1.35;
    margin-bottom:10px;
}

.artist-box p{
    color:#475569;
    font-family: Arial, Helvetica, sans-serif !important;
    font-size:13px;
    line-height:1.6;
    margin-bottom:0;
}

.artist-box a{
    display:inline-block;

    background:#2563eb;
    color:#fff;

    text-decoration:none;

    padding:10px 18px;

    border-radius:10px;

    font-size:13px;
    font-weight:700;

    transition:0.2s;
}

.artist-box a:hover{
    background:#1d4ed8;
    transform:translateY(-2px);
}

</style>

<script>
let sliderStates = {
    featuredSlider: 0,
    latestSlider: 0
};

function getVisibleItems() {
    if (window.innerWidth <= 576) return 2;
    if (window.innerWidth <= 992) return 3;
    return 5;
}

function slideProducts(sliderId, direction) {
    const slider = document.getElementById(sliderId);
    const cards = document.querySelectorAll('#' + sliderId + ' .product-card');

    if (!slider || cards.length === 0) return;

    const visibleItems = getVisibleItems();
    const totalItems = cards.length;
    const maxSlide = Math.max(totalItems - visibleItems, 0);

    sliderStates[sliderId] += direction;

    if (sliderStates[sliderId] > maxSlide) {
        sliderStates[sliderId] = 0;
    }

    if (sliderStates[sliderId] < 0) {
        sliderStates[sliderId] = maxSlide;
    }

    const gap = 22;
    const cardWidth = cards[0].offsetWidth + gap;

    slider.style.transform = `translateX(-${sliderStates[sliderId] * cardWidth}px)`;
}

setInterval(function() {
    slideProducts('featuredSlider', 1);
    slideProducts('latestSlider', 1);
}, 5000);

window.addEventListener('resize', function() {
    sliderStates.featuredSlider = 0;
    sliderStates.latestSlider = 0;

    const featured = document.getElementById('featuredSlider');
    const latest = document.getElementById('latestSlider');

    if (featured) featured.style.transform = 'translateX(0)';
    if (latest) latest.style.transform = 'translateX(0)';
});
</script>

<?php
include $tpl . "footer.php";
ob_end_flush();
?>
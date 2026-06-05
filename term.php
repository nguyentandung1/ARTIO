```php
<?php
  ob_start();
  $pageTitle = 'Terms and Conditions';
  require "init.php";
?>

<?php if(isset($_GET['keyword'])): ?>
    <?php if ($inputSearchError) :?>
        <p class="alert-danger ms-auto me-auto pt-5 pb-5" style="width:50%">Enter a valid value!</p>
    <?php elseif(($noItemsSearch)): ?>
        <p class="alert-danger ms-auto me-auto pt-5 pb-5" style="width:50%">
            No items match this word <?php echo " " . $_GET['keyword']; ?>
        </p>
    <?php elseif($noItems): ?>
        <p class="alert-danger ms-auto me-auto pt-5 pb-5" style="width:50%">No items in this Category</p>
    <?php else: ?>
        <?php header("Location: searchItem.php?keyword=".$_GET['keyword']); ?>
    <?php endif ?>
<?php endif ?>


<div class="container">
    <main>
        <div class="about">
            <h2 class="heading">Terms and Conditions</h2>
            <br>

            <h3 class="heading" style="font-size:1.3em; padding-left:6px">
                License and Access
            </h3>

            <p style="text-align: justify;font-family:candara;letter-spacing:0.05em;">
                ARTIO is an online platform developed to support the display, buying, selling, and auctioning of artwork. By accessing and using ARTIO, users agree to comply with these Terms and Conditions as well as all applicable laws and regulations.
                Users are granted a limited, non-exclusive, and non-transferable right to access and use the ARTIO platform for personal and lawful purposes only.
                This permission does not include copying, modifying, distributing, reselling, or commercially exploiting any part of the website, including its content, images, artwork information, interface design, source code, product listings, descriptions, or prices without proper authorization.
                Users are not allowed to use data mining tools, automated bots, or any other methods that may interfere with the normal operation of the website.
                ARTIO reserves the right to suspend or terminate user access if any activity is found to violate these terms, affect system security, damage website operation, or negatively impact other users.
                All rights not expressly granted in these Terms and Conditions remain the property of ARTIO and the respective content owners.
            </p>

            <br>

            <h3 class="heading" style="font-size:1.3em; padding-left:6px;">
                Reviews, Comments, Communications, and Other Content
            </h3>

            <p style="text-align: justify;font-family:candara;letter-spacing:0.05em;">
                Users may submit reviews, comments, messages, artwork descriptions, images, and other content through the ARTIO platform. All submitted content must be lawful, respectful, accurate, and must not infringe upon the rights of any third party.
                Users are not allowed to post content that is illegal, offensive, threatening, defamatory, misleading, harmful, invasive of privacy, or related to spam, viruses, malicious code, political campaigning, or unauthorized commercial solicitation.
                ARTIO reserves the right, but is not obligated, to review, edit, reject, or remove any content that is considered inappropriate, harmful, inaccurate, or in violation of these Terms and Conditions.
                By submitting content to ARTIO, users confirm that they own the rights to such content or have obtained the necessary permissions to publish it.
                Users remain responsible for the information and materials they provide on the platform.
                ARTIO does not take responsibility for user-generated content posted by users or third parties, but may take necessary action when violations are reported or detected.
            </p>
        </div>
    </main>
</div>

<?php
include $tpl . "footer.php";
ob_end_flush();
?>
</html>
```

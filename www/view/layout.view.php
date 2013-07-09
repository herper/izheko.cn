<!doctype html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>爱折扣 - 精选优质折扣商品</title>
        <meta name="keywords" content="爱折扣,九块九,九块九包邮,9.9包邮,优质折扣" />
        <meta name="description" content="爱折扣为您精选了淘宝、 天猫的优质折扣商品， 让您轻松找到物美价廉、 称心如意的宝贝。" />
        <link rel="shortcut icon" href="<?= App::static_server() ?>/img/favicon.ico">
        <link charset="utf-8" rel="stylesheet" type="text/css" href="<?= App::static_server() ?>/main.css" />
        <script src="<?= App::static_server() ?>/jquery.js"></script>
        <script src="<?= App::static_server() ?>/main.js"></script>
        <script src="http://l.tbcdn.cn/apps/top/x/sdk.js?appkey=21567955"></script>
    </head>
    <body>
<?php
    require APP_ROOT . "/view/component/topbar.view.php";
    require APP_ROOT . "/view/component/header.view.php";
    require APP_ROOT . "/view/component/navbar.view.php";

    echo '<div id="content">';
    if (isset($target_view)) {
        require APP_ROOT . "/view/$target_view.view.php";
    }
    else echo '<img id="error_content" src="' . App::static_server() . '/img/404.png" />';
    echo '</div>';

    require APP_ROOT . "/view/component/footer.view.php";
?>
    </body>
</html>

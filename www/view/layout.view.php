<!doctype html>
<html lang="zh-CN">
    <head>
        <meta charset="utf-8">
        <meta name="layoutmode" content="standard" />
        <meta name="viewport"   content="width=device-width,initial-scale=1.0" />
        <?php require APP_ROOT . '/view/component/seo.view.php'; ?>
        <link rel="shortcut icon" href="<?= App::static_server() ?>/img/favicon.ico?v=20130817" />
        <link charset="utf-8" rel="stylesheet" type="text/css" href="<?= App::static_server() ?>/css/main.css?v=20131010.8" />
        <script src="<?= App::static_server() ?>/js/jquery.min.js"></script>
        <script src="<?= App::static_server() ?>/js/main.js?v=20131010.0"></script>
    </head>
    <body>
        <?php require APP_ROOT . '/view/module/header.view.php'; ?>
        <script> Izheko.taodianjin_init(); </script>
        <?php require APP_ROOT . '/view/module/sidebar.view.php'; ?>
        <div id="content">
            <?php
                if (isset($target_view)) require APP_ROOT . "/view/$target_view.view.php";
                else echo '<img id="error_content" src="' . App::static_server() . '/img3/404.png" />';
            ?>
        </div>
        <?php require APP_ROOT . '/view/module/footer.view.php'; ?>
        <script src="http://l.tbcdn.cn/apps/top/x/sdk.js?appkey=21567955"></script>
    </body>
</html>

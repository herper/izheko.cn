<?php
if (empty($items))
{
?>
        <div class="no_items">很抱歉，没有符合条件的宝贝。</div>
<?php
} else {
?>
        <div class="item_list"><!--
<?php
require_once APP_ROOT . '/../common/helper/price.helper.php';
foreach($items as $item) { 
    list($discount_price, $vip, $original_price, $risen_price, $status) = $item->get_price_and_status();
    if ($risen_price) $risen_price = format_price($risen_price);
    $status_data = array(
        '未开始'   => array( 'green', '折扣活动还没开始哟。'),
        '去抢购'   => array( 'yellow',   '快去抢购吧！'),
        '已涨价'   => array( 'gray2', "宝贝已经涨价为 ￥$risen_price 啦。"),
        '折扣结束' => array( 'gray',  '折扣活动已经结束啦。'),
        '已抢光'   => array( 'gray',  '宝贝被抢光，已经下架啦。'),
    );
    list($style, $title) = $status_data[$status];
    list($discount_price_yuan, $discount_price_fen) = split_price($discount_price);
?>
         --><div class="item">
                <div class="title">
                    <b><?= $item->get_type_tag() ?></b>
                    <a target="_blank" href="<?= $item->jump_url() ?>">
                        <?= $item->get_title() ?>
                    </a>
                </div>
                <a target="_blank" href="<?= $item->jump_url() ?>">
                    <img src="<?= $item->get_pic_url() ?>" />
                </a>
                <div class="buy">
                    <a class="action <?= $style ?>" title="<?= $title ?>" target="_blank" href="<?= $item->jump_url() ?>">
                        <?= $status ?>
                        <?= $risen_price ? "<div>￥$risen_price</div>" : null ?>
                    </a>
                    <span title="折扣价 ￥<?= format_price($discount_price) ?>">￥<big><?=
                            $discount_price_yuan ?></big>.<?= $discount_price_fen ?></span>
                    <?php if ($original_price > $discount_price) { ?>
                    <small title="原价 ￥<?= format_price($original_price) ?>">￥<?= format_price($original_price) ?></small>
                    <?php } ?>
                </div>
                <div class="flags">
                    <?= $item->postage_free() ? '<span class="post">包邮</span> ' : null ?>
                    <?= $vip ? '<span class="vip" title="淘宝VIP用户价哟。">VIP价</span>' : null ?>
                </div>
            </div><!--
<?php } ?>
     --></div>
        <div class="page">
<?php
    require_once APP_ROOT . '/../common/helper/page.helper.php';
    echo paginate($page_url, '.html', $page, $total_count, $page_size);
?>
        </div>
        <div class="sidebar">
            <div class="go_top"><a href="#"><span>回到顶部</span><b></b></a></div>
<?php
    if($page >= 2) {
        $prev_page_url = $page_url . ($page - 1) . '.html' 
?>
            <div class="prev_page"><a href="<?= $prev_page_url ?>"><span>上一页</span><b></b></a></div>
<?php 
    } 
    if(($total_count / $page_size) > $page) {
        $next_page_url = $page_url . ($page + 1) . '.html' 
?>
            <div class="next_page"><a href="<?= $next_page_url ?>"><span>下一页</span><b></b></a></div>
        </div>
<?php
    }
}
?>
<?php
require_once APP_ROOT . '/helper/curl.helper.php';
require_once 'taobao_item/click_url_to_item_id.model.php';
require_once APP_ROOT . '/../common/helper/price.helper.php';
require_once APP_ROOT . '/../common/model/item_base.model.php';

class ItemGrab
{
    static function start()
    {
        list($target, $refer) = static::get_target();
        self::grab($target, $refer);
        system('cd ' . APP_ROOT . '; php run command/item_update.cmd.php');
    }

    static function grab($target, $refer)
    {
        echo strtotime("%F %T\n");
        $curl = new Curl();
        if($refer && $refer !== $target)
            $curl->get($refer);
        else $refer = null;
        $page = $curl->get($target, $refer);

        do {
            self::get_all_item($page);
        } while ($page = $page->get(static::next_page_xpath));
        echo "\n";
    }

    static function get_all_item($page)
    {
        $items = array();
        $node_list = $page->query(static::item_node_xpath);
        foreach ($node_list as $item_node)
        {
            self::get_one_item($item_node, $page, $items);
        }
        if ($items) self::save_items($items);
        else echo "no new item\n";
        flush();
    }

    static function get_one_item($item_node, $page, &$items)
    {
        static $ordinal;
        if ($ordinal === null) $ordinal = 1;
        else $ordinal ++;

        $ref_iid = static::get_ref_iid($item_node, $page);
        if (DB::affected_rows(
            "update items set ref_ordinal=$ordinal, ref_update_time=now() where ref_iid='$ref_iid'"
        )) return;
        
        $num_iid = self::get_num_iid($item_node, $page);
        if ($num_iid && preg_match('/^\d+$/', $num_iid))
        {
            $ref_price = $page->query(static::item_price_xpath, $item_node)->item(0)->nodeValue;
            $ref_price = preg_match('/\d+(\.\d+)?/', $ref_price, $matches) ? $matches[0] : null;
            $pic_node = $page->query(static::item_pic_xpath, $item_node)->item(0);
            self::fetch_pic($num_iid, $pic_node, $page);
            $ref_tip = static::get_tip_text($item_node, $page);
            $ref_end_time = static::get_ref_end_time($item_node, $page);
            $items[$num_iid] = array($ref_iid, $ordinal, $ref_price, $ref_tip, $ref_end_time);
        }
    }

    static function get_num_iid($item_node, $page)
    {
        list($url, $refer) = static::get_click_url($item_node, $page);
        if($url === null) return;
            
        if(preg_match('{http://s.click.taobao.com/}i', $url))
            return ClickUrlToItemId::fetch($url, $refer);
        else if(preg_match('{\.com/item\.htm\?(?:.*&)?id=(\d+)}i', $url, $m))
            return $m[1];
        else error_log("unexpected click url: $url\n");
    }

    static function fetch_pic($num_iid, $img, $page)
    {
        $path = APP_ROOT . '/../static/public/' . ItemBase::pic_path($num_iid);
        if(file_exists($path)) return;
        
        $pic = $img->getAttribute('data-original');
        if($pic === '') $pic = $img->getAttribute('src');
        if($pic === '') {
            echo "empty pic\n";
            return;
        }
        $response = $page->get_by_url($pic);

        if(!is_dir($dir = dirname($path))) mkdir($dir, 0755, true);
        file_put_contents($path, $response->body);
        if(!file_exists($path)) {
            echo "get pic $pic failed\n";
        }
    }

    static function save_items($items)
    {
        if (! $items) return;
        $now = strftime('%F %T');
        $values = '';
        foreach ($items as $num_iid => $tmp)
        {
            list($ref_iid, $ref_ordinal, $ref_price, $ref_tip, $ref_end_time) = $tmp;

            $ref_price = $ref_price ? parse_price($ref_price) : 'null';
            $ref_tip = DB::escape($ref_tip);
            $ref_end_time = $ref_end_time ? "'$ref_end_time'" : 'null';
            $values .= ",($num_iid, '$ref_iid', '$now', '$ref_ordinal', '$now', $ref_price, $ref_end_time, '$ref_tip')";
        }
        $values = substr($values, 1);
        $sql = 'insert ignore into items 
            (`num_iid`, `ref_iid`, `create_time`, `ref_ordinal`, `ref_update_time`, `ref_price`, `ref_end_time`, `ref_tip`)
            values ' . $values;
        $count = count($items);
        $affected = DB::affected_rows($sql);
        $now = strftime('%F %T');
        if($affected === false) error_log("$now insert failed: $count");
        else echo "$now insert success: $count, {$affected}\n";
    }
}



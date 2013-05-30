<?php
require_once APP_ROOT . '/../common/db.php';
require_once APP_ROOT . '/../common/model/taobao_api.model.php';

class Category
{
    static function get_type_id($cid)
    { 
        while( ($category = self::get($cid)) &&
            (!isset($category['type_id']) || $category['type_id'] <= 0) &&
            ($parent_cid = $category['parent_cid']) > 0 
        ) $cid = $parent_cid;
        if(isset($category['type_id'])) return $category['type_id'];
    }

    static function get($cid)
    {
        $category = self::get_from_db($cid);
        if (!$category && self::fetch($cid)) {
            $category = self::get_from_db($cid);
        }
        return $category;
    }

    static function get_from_db($cid)
    {
        $sql = "select * from categories where cid=$cid limit 1";
        return DB::get_row($sql);
    }

    static function fetch($cid)
    {
        $response = TaobaoApi::itemcats_get($cid);
        if (!isset($response['itemcats_get_response']['item_cats']['item_cat'][0]) ||
            !is_array($category = $response['itemcats_get_response']['item_cats']['item_cat'][0])
        )
        {
            fputs(STDERR, "fetch category $cid failed\n");
            return;
        }
        return self::save($category);
    }

    static function save($category)
    {
        $name = DB::escape($category['name']);
        $is_parent = $category['is_parent'] ? '1' : '0';
        $create_time = strftime('%F %T');
        $sql = "insert into categories (cid, name, parent_cid, is_parent, create_time)
            values ({$category['cid']}, '$name', '{$category['parent_cid']}', $is_parent, '$create_time')";
        return DB::query($sql);
    }

    static function exist($cid)
    {
        $sql = "select 1 from categories where cid = '$cid' limit 1";
        return DB::query($sql)->num_rows > 0;
    }
        
    static function get_children_from_api($cid)
    {
        $response = TaobaoApi::itemcats_children_get($cid);
        if (isset($response['itemcats_get_response']['item_cats']['item_cat']) &&
            is_array($categories = $response['itemcats_get_response']['item_cats']['item_cat'])
        )
        {
            return $categories;
        }
    }
    static function fetch_children($cid)
    {
        $categories = Category::get_children_from_api($cid);
        if(!is_array($categories))return;
        foreach ($categories as $category)
        {
            if(!self::exist($category['cid']))
                self::save($category);
        }
    }
}
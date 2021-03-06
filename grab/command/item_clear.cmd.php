<?php
require_once APP_ROOT . '/../common/helper/db.helper.php';
require_once APP_ROOT . '/../common/model/item_base.model.php';
require_once APP_ROOT . '/model/cache.model.php';

class ItemClear
{
    static function start()
    {
        self::clear();
    }

    static function clear()
    {
        #promotion ended or deleted
        $common = '
            from items where ref_update_time < "' . strftime('%F %T', time() - 7200) .
            '" or end_time < now() or (flags & ' .  ItemBase::FLAGS_MASK_ITEM_DELETED . ')'
            ;
        $type_ids = DB::get_values('select distinct type_id ' . $common);
        $sql    = 'replace into items_history select * ' . $common;
        $count1 = DB::affected_rows($sql);
        $sql    = 'delete ' . $common;
        $count2 = DB::affected_rows($sql);
        $now = strftime('%F %T');
        echo "$now cleared $count2 => $count1 \n";
        Cache::clear($type_ids);
    }
}

ItemClear::start();


<?php
/**
 * Created by PhpStorm.
 * User: xiaokang
 * Date: 2016/5/1
 * Time: 4:18
 */

/**
 *    实体商品
 *
 * @author    Garbin
 * @usage    none
 */
class OfflineGoods extends BaseGoods
{
    function __construct($param)
    {
        $this->OfflineGoods($param);
    }

    function OfflineGoods($param)
    {
        /* 初始化 */
        $param['_is_material'] = true;
        $param['_name'] = 'offline';
        $param['_order_type'] = 'offline';

        parent::__construct($param);
    }
}

?>
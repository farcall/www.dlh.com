<?php

//测试APP
class DongfixApp extends BackendApp {

    var $mod_region;
    var $mod_proxy;
    var $mod_store;
    var $mod_order;
    function __construct() {
        header("Content-Type: text/html; charset=UTF-8");
        $this->DongfixApp();
    }

    function DongfixApp() {
        parent::BackendApp();
        $this->mod_region = &m('region');
        $this->mod_proxy  = &m('proxy');
        $this->mod_store  = &m('store');
        $this->mod_order  = &m('order');
    }



    /**
     * proxy表添加父节点
     */
    function index() {
        return;
        $proxy_all = $this->mod_proxy->findAll();
        foreach ($proxy_all as $key=> $proxy){

            $region_id = $proxy['region_id'];
            echo $region_id.'<br>';
            $region = $this->mod_region->get($region_id);


            $proxy = $this->mod_proxy->edit($proxy['id'],array(
                'parent_id'=>$region['parent_id'],
            ));

            echo '代理区域:'.$proxy['region_name'].'--地区节点:'.$region_id.'--父节点:'.$region['parent_id'].'<br>';

        }
    }


    function shandong(){
        set_time_limit(0);

        $storeCount = $this->mod_store->getOne("SELECT COUNT(*) FROM `ecm_store`  where  `region_name` like'%山东省%'");
        echo '山东省商铺数量为:'.$storeCount."个<br>";

        $stores = $this->mod_store->getAll("SELECT * FROM `ecm_store`  where  `region_name` like'%山东省%'");


        $conditions = '';
        foreach ($stores as $k=>$store){
            $store_id = $store['store_id'];
            $conditions .= strval($store_id).',';
        }

        $conditions = substr($conditions,0,strlen($conditions)-1);
        echo ($conditions).'<br>';

        $order_count = $this->mod_store->getOne("select count(*) from `ecm_order` WHERE  `seller_id` IN (".$conditions.")");
        echo '山东省订单数:'.$order_count.'<br>';
        $order_amount = $this->mod_store->getOne("select sum(goods_amount) from `ecm_order` WHERE  `seller_id` IN (".$conditions.")");
        echo '山东省总销售额:'.$order_amount.'<br>';

        $orders = $this->mod_store->getAll("select distinct(buyer_name) from `ecm_order` WHERE  `seller_id` IN (".$conditions.")");

        echo '山东省内参与消费共:'.sizeof($orders).'人<br>';
        return;
    }
}

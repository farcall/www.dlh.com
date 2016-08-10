<?php

class DataApp extends BackendApp {

    var $mod_store;
    var $mod_member;
    var $mod_goods;
    var $mod_order;
    function __construct() {
        $this->DataApp();
    }

    function DataApp() {
        parent::BackendApp();
        $this->mod_store = &m('store');
        $this->mod_member = &m('member');
        $this->mod_goods = &m('goods');
        $this->mod_order = &m('order');
    }

    function index() {
        $begin_time = gmstr2time($_GET['add_time_from']);
        $end_time = gmstr2time($_GET['add_time_to']);

        $begin_time = $begin_time?$begin_time:0;
        $end_time = $end_time?$end_time:gmtime();

        if ($begin_time >= $end_time){
            $this->show_warning('开始时间不能大于结束时间');
            return;
        }

        $user_mod =& m('member');
        $news_info  = array(
            'new_user_qty'  => $user_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "member WHERE reg_time >= '$begin_time' and reg_time < '$end_time'"),
            'new_store_qty' => $user_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "store WHERE add_time >= '$begin_time' and add_time < '$end_time' AND state = 1"),
            'new_apply_qty' => $user_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "store WHERE add_time >= '$begin_time' AND add_time < '$end_time' and state = 0"),
            'new_goods_qty' => $user_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "goods WHERE add_time >= '$begin_time' and add_time < '$end_time' AND if_show = 1 AND closed = 0"),
            'new_order_qty' => $user_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "order WHERE finished_time >= '$begin_time' and finished_time < '$end_time' AND status = '" . ORDER_FINISHED . "'"),
            'new_adminchongzhi_qty' => $user_mod->getOne("SELECT sum(money) FROM " . DB_PREFIX . "epaylog WHERE type=10 and states=40 and complete = 1 and money_flow = 'income' and add_time>= '$begin_time' and add_time < '$end_time'"),
            'new_adminjianshao_qty' => $user_mod->getOne("SELECT sum(money) FROM " . DB_PREFIX . "epaylog WHERE type=10 and states=40 and complete = 1 and money_flow = 'outlay' and add_time>= '$begin_time' and add_time < '$end_time'"),
        );

        $this->assign('news_info',$news_info);


        $all_info = array(
            'user_qty'  => $user_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "member"),
            'store_qty' => $user_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "store WHERE state = 1"),
            'apply_qty' => $user_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "store WHERE state = 0"),
            'goods_qty' => $user_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "goods WHERE if_show = 1 AND closed = 0"),
            'order_qty' => $user_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "order WHERE status = '" . ORDER_FINISHED . "'"),
            'order_amount' => $user_mod->getOne("SELECT SUM(order_amount) FROM " . DB_PREFIX . "order WHERE status = '" . ORDER_FINISHED . "'"),
            'admin_email' => $user_mod->getOne("SELECT email FROM " . DB_PREFIX . "member WHERE user_id = '1'"),
            'vip_user_qty' => $user_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "member where vip = '1'"),
            'vip_store_qty' => $user_mod->getOne("SELECT count(*) FROM `ecm_member`  member ,`ecm_store`   store WHERE member.`user_id` = store.`store_id`  and member.`vip` =1"),
        );


        $this->assign('all_info',$all_info);


        //提现记录
        //成交金额
        //退款数
        //退款金额
        //会员数量
        //商家数量
        //VIP会员数量
        //VIP商家数量


        $this->import_resource(array('script' => 'inline_edit.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
            'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));

        $this->display('data.index.html');
    }

    /**
     * @param $begin 起始计算时间
     * @param $end   结束计算时间
     */
    function _store_count($begin,$end){
        
    }
}

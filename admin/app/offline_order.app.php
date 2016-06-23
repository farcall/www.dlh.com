<?php

/**
 * Created by PhpStorm.
 * User: xiaokang
 * Date: 2016/5/3
 * Time: 23:03
 */
class Offline_orderApp extends BackendApp
{
    var $_order_offline_mod;
    var $_order_mod;
    var $_epay_mod;
    var $_epaylog_mod;
    var $_integral_log_mod;

    function __construct()
    {
        $this->Offline_order();
    }

    function Offline_order()
    {
        parent::__construct();
        $this->_order_offline_mod = &m('order_offline');
        $this->_order_mod = &m('order');
        $this->_epay_mod = &m('epay');
        $this->_epaylog_mod = &m('epaylog');
        $this->_integral_log_mod = &m('integral_log');
    }

    function index()
    {
        $status = $_GET['status'];
        $page = $this->_get_page(20);


        if (!isset($status)) {
            $conditions = '1=1';
        } else {
            $conditions = "1=1 and status={$status}";
        }

        $orders = $this->_order_offline_mod->findAll(array(
            'conditions' => "{$conditions}",
            'count' => true,
            'limit' => $page['limit'],
            'order' => 'add_time asc',
        ));

        if (empty($orders)) {
            $this->display('paycenter/offline_order.html');
            return;
        }

        $page['item_count'] = $this->_order_offline_mod->getOne("select count(*) from ecm_order_offline where {$conditions}");
        $this->_format_page($page);
        $this->assign('page_info', $page);

        $this->assign('orders', $orders);

        $this->display('paycenter/offline_order.html');
    }

    /**
     * 作用:查看线下订单
     * Created by QQ:710932
     */
    function view()
    {
        $id = $_GET['id'];

        if (empty($id)) {
            $page = $this->_get_page(20);

            $orders = $this->_order_offline_mod->findAll(array(
                'conditions' => "seller_userid=" . $this->visitor->get('manage_store'),
                'count' => true,
                'limit' => $page['limit'],
                'order' => 'add_time DESC',
            ));

            if (empty($orders)) {
                $this->show_warning('请不要非法提交');
                return;
            }
            $page['item_count'] = $this->_order_offline_mod->getOne("select count(*) from ecm_order_offline where seller_userid=" . $this->visitor->get('manage_store'));
            $this->_format_page($page);
            $this->assign('page_info', $page);


            $this->assign('orders', $orders);
            $this->display('paycenter/offline_order_log.html');
            return;
        }


        $order = $this->_order_offline_mod->get(array(
            'conditions' => "order_id=" . $id,
        ));


        $this->assign('order', $order);
        $this->display('paycenter/offline_order_detail.html');
    }

    /**
     * 作用:订单审核
     * Created by QQ:710932
     */
    function check()
    {
        $status = $_POST['status'];
        $remark = $_POST['remark'];
        $order_id = $_POST['order_id'];
        if (!is_numeric($order_id) or $order_id <= 0) {
            $this->show_warning("非法订单");
            return;
        }

        if ($status != '0' and $status != '40') {
            $this->show_warning("请不要非法提交");
            return;
        }

        if ($status == 0 and strlen($remark) < 22) {
            $this->show_warning("拒绝必须填写理由,且不能少于10个字符");
            return;
        }

        //判断这笔订单是否已经处理
        $order_data = $this->_order_mod->get(array(
            'conditions' => "order_id='$order_id'",
        ));

        if($order_data['status'] == '0' or $order_data['status'] == '40'){
            $this->show_warning("请不要重复提交");
            return;
        }


        //线下做单-管理员审核拒绝
        if ($status == 0 and strlen($remark) >= 22) {
            $this->_cancel_order($order_id, $remark);
            return;
        }

        //线下做单-管理员审核通过
        if ($status == 40) {
            $this->_confirm_order($order_id, $remark);
            return;
        }
    }

    /**
     * 作用:审核通过
     * Created by QQ:710932
     */
    function _confirm_order($order_id, $remark)
    {
        $order_data = $this->_order_mod->get(array(
            'conditions' => "order_id='$order_id'",
        ));

        //order表修改
        $order_result = $this->_order_mod->edit($order_id, array(
            'finished_time' => gmtime(),
            'postscript' => $remark,
            'status' => ORDER_FINISHED,
        ));


        //order_offline表修改
        $order_offline_result = $this->_order_offline_mod->edit('order_id=' . $order_id, array(
            'check_admin' => $this->visitor->_get_detail('user_name'),
            'check_time' => gmtime(),
            'status' => ORDER_SHENHE_FINISHED,
        ));
        $order_offline_data = $this->_order_offline_mod->get(array(
            'conditions' => 'order_id=' . $order_id,
        ));

        //冻结资金扣除 epay  epay_log
        $epay_data = $this->_epay_mod->get(array(
            'conditions' => 'user_id=' . $order_offline_data['seller_userid'],
        ));

        $money_dj = $epay_data['money_dj'] - $order_offline_data['yongjin'];
        $this->_epay_mod->edit('user_id=' . $order_offline_data['seller_userid'], array(
            'money_dj' => $money_dj,
        ));

        //资金变动日志
        $order_sn = EPAY_TRADE_CHARGES . date('YmdHis', gmtime() + 8 * 3600) . rand(1000, 9999);
        $this->_epaylog_mod->add(array(
            'user_id' => $order_offline_data['seller_userid'],
            'user_name' => $order_offline_data['seller_username'],
            'order_id' => $order_offline_data['order_id'],
            'order_sn' => $order_sn,
            'type' => EPAY_TRADE_CHARGES,
            'states' => EPAY_TRADE_CHARGES,
            'money' => $order_offline_data['yongjin'],
            'money_flow' => 'outlay',
            'complete' => 1,
            'log_text' => '扣除线下交易佣金:买家(' . $order_offline_data['buyer_name'] . '),价格(' . $order_offline_data['money'] . ')',
            'add_time' => gmtime(),
        ));


        /*用户确认收货后 获得积分*/
        import('integral.lib');
        $integral = new Integral();
        $integral->change_integral_buy($order_data['buyer_id'], $order_data['goods_amount']);

        $integral->change_integral_seller($order_data['seller_id'], $order_data['goods_amount'] / 10);
        /*交易成功后,推荐者可以获得佣金  BEGIN*/
        import('tuijian.lib');
        $tuijian = new tuijian();
        $tuijian->do_tuijian($order_data);
        $this->show_message('OK');
        /*交易成功后,推荐者可以获得佣金  END*/
    }


    /**
     * 作用:审核拒绝
     * Created by QQ:710932
     */
    function _cancel_order($order_id, $remark)
    {
        //todo 审核拒绝
        $order_data = $this->_order_mod->get(array(
            'conditions' => "order_id='$order_id'",
        ));

        //order表修改
        $order_result = $this->_order_mod->edit($order_id, array(
            'finished_time' => gmtime(),
            'postscript' => $remark,
            'status' => ORDER_CANCELED,
        ));


        //order_offline表修改
        $order_offline_result = $this->_order_offline_mod->edit('order_id=' . $order_id, array(
            'check_admin' => $this->visitor->_get_detail('user_name'),
            'check_time' => gmtime(),
            'status' => ORDER_SHENHE_CANCELED,
        ));
        $order_offline_data = $this->_order_offline_mod->get(array(
            'conditions' => 'order_id=' . $order_id,
        ));

        //冻结资金扣除 epay  epay_log
        $epay_data = $this->_epay_mod->get(array(
            'conditions' => 'user_id=' . $order_offline_data['seller_userid'],
        ));

        $money_dj = $epay_data['money_dj'] - $order_offline_data['yongjin'];
        $money = $epay_data['money'] + $order_offline_data['yongjin'];
        $this->_epay_mod->edit('user_id=' . $order_offline_data['seller_userid'], array(
            'money_dj' => $money_dj,
            'money' => $money,
        ));

        //资金变动日志
        $order_sn = EPAY_TRADE_CHARGES . date('YmdHis', gmtime() + 8 * 3600) . rand(1000, 9999);
        $this->_epaylog_mod->add(array(
            'user_id' => $order_offline_data['seller_userid'],
            'user_name' => $order_offline_data['seller_username'],
            'order_id' => $order_offline_data['order_id'],
            'order_sn' => $order_sn,
            'type' => EPAY_TRADE_REFUSE,
            'states' => EPAY_TRADE_REFUSE,
            'money' => $order_offline_data['yongjin'],
            'money_flow' => 'income',
            'complete' => 1,
            'log_text' => '线下做单被拒绝，返还冻结资金:(' . $order_offline_data['money'] . ')',
            'add_time' => gmtime(),
        ));

        $this->show_message('OK');
        /*交易成功后,推荐者可以获得佣金  END*/
    }
}
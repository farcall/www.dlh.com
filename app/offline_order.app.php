<?php
/**
 * Created by PhpStorm.
 * User: xiaokang
 * Date: 2016/5/1
 * Time: 4:23
 */

require(ROOT_PATH . '/app/paycenterbase.app.php');

class Offline_orderApp extends PaycenterbaseApp
{
    var $_msg_mod;
    var $_store_mod;
    var $_epay_mod;
    var $_msglog_mod;
    var $_member_mod;
    var $_order_offline_mod;
    var $_integral_log_mod;

    function __construct() {
        parent::__construct();
        $referer = $_SERVER['HTTP_REFERER'];
        if (strpos($referer, 'act=login') === false) {
            $ret_url = $_SERVER['HTTP_REFERER'];
            $ret_text = 'go_back';
        } else {
            $ret_url = SITE_URL . '/index.php';
            $ret_text = 'back_index';
        }

        //是否是商家
        if (!$this->visitor->get('has_store')) {
            $this->show_warning('只有签约商家才可以做单!');
            die;
        }

        /* 检查店铺开启状态 */
        $state = $this->visitor->get('state');
        if ($state == 0) {
            $this->show_warning('apply_not_agree', $ret_text, $ret_url);
            die;
            return;
        } elseif ($state == 2) {
            $this->show_warning('store_is_closed', $ret_text, $ret_url);
            die;
            return;
        }
        $this->Offline_order();
    }

    function Offline_order() {
        parent::__construct();
        $this->_msg_mod = & m('msg');
        $this->_msglog_mod = & m('msglog');
        $this->_member_mod = &m('member');
        $this->_epay_mod = &m('epay');
        $this->_store_mod = &m('store');
        $this->_order_offline_mod = &m('order_offline');
        $this->_integral_log_mod = &m('integral_log');
    }


    function index(){

        if(IS_POST){
            if (empty($_POST['goods_name'])) {
                $this->show_warning('商品名称不能为空');
                returnl;
            }

            if (empty($_POST['money'])) {
                $this->show_warning('商品价格不能为空');
                return;
            }
            //检查是否存在凭证
            $offline_image = $_POST['offline_image'];
            if(empty($offline_image)){
                $this->show_warning('请确认交易凭证上传是否成功');
                return;
            }

            //会员检查
            $buyer_name = $_POST['buyer_name'];
            if ($buyer_name == $this->visitor->get('user_name')) {
                $this->show_warning('买家不能与卖家账户相同');
                return;
            }


            $buyer_member = $this->_member_mod->get(array(
                'conditions' => "user_name = '{$buyer_name}'",
            ));
            if(empty($buyer_member)){
                $this->show_warning('会员不存在');
                return;
            }

            //金额检查
            $money = $_POST['money'];
            if($money<=0 or !is_numeric($money)){
                $this->show_warning("请输入正确的商品金额");
                return;
            }

            $model_setting = &af('settings');
            $setting = $model_setting->getAll(); //载入系统设置数据
            $yongjin = $money*$setting['epay_trade_charges_ratio'];


            $store_epay = $this->_epay_mod->get(array(
                'conditions' => "user_id = '{$this->visitor->get('user_id')}'",
            ));

            if ($store_epay['money'] < $yongjin) {
                $this->show_warning('资金不足：当前交易需要佣金' . $yongjin . ',账户余额为' . $store_epay['money'], '立即充值', 'index.php?app=epay&act=czlist');
                return;
            }

            $buyer_id = $this->visitor->get('user_id');
            $seller_id = intval($this->visitor->get('manage_store'));
            $store_id = $this->visitor->get('manage_store');
            $seller_member = $this->_member_mod->get($seller_id);
            $store = $this->_store_mod->get($store_id);
            //构造订单数据
            $order = array(
                'buyer_id'=>$buyer_member['user_id'],
                'buyer_name'=>$buyer_member['user_name'],
                'buyer_mobile'=>$buyer_member['phone_mob'],
                'goods_name'=>$_POST['goods_name'],
                'money'=>$money,
                'yongjin'=>$yongjin,
                'seller_userid'=> $this->visitor->get('user_id'),
                'seller_username'=>$seller_member['user_name'],
                'seller_storeid'=>$store['store_id'],
                'seller_storename'=>$store['store_name'],
                'seller_mobile'=>$seller_member['phone_mob'],
            );


            $goods_type = & gt('offline');
            $order_type = & ot('offline');
            $order_id = $order_type->submit_order($order);
            if(empty($order_id)){
                $this->show_warning('订单创建失败');
            }

            //线下订单备份表——交易凭证在此表
            $offline_order_data = array(
                'order_id' => $order_id,
                'buyer_userid' => $buyer_member['user_id'],
                'buyer_username' => $buyer_member['user_name'],
                'buyer_mobile' => $buyer_member['phone_mob'],
                'seller_userid' => $this->visitor->get('user_id'),
                'seller_username' => $seller_member['user_name'],
                'seller_storeid' => $store['store_id'],
                'seller_storename' => $store['store_name'],
                'seller_mobile' => $seller_member['phone_mob'],
                'money' => $money,
                'yongjin' => $yongjin,
                'goods_name' => $_POST['goods_name'],
                'offline_image' => $offline_image,
                'status' => ORDER_SHENHE_ING,
                'add_time' => gmtime(),
            );


            $this->_order_offline_mod->add($offline_order_data);


            /* 记录订单操作日志 */
            $order_log =& m('orderlog');
            $order_log->add(array(
                'order_id' => $order_id,
                'operator' => addslashes($this->visitor->get('user_name')),
                'order_status' => '',
                'changed_status' => '下订单，审核中',
                'remark' => '买家提货，卖家做单',
                'log_time' => gmtime(),
                'operator_type' => 'buyer'
            ));

            $this->show_message("做单成功");
            return;
        }
        else{
            $this->display('paycenter/offline_order.html');
        }
    }

    /**
     * 作用:线下做单明细
     * Created by QQ:710932
     */
    function log()
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
    function uploader(){
        $file = $_FILES['file'];
        if ($file['error'] == UPLOAD_ERR_NO_FILE) { // 没有文件被上传
            die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "文件没有上传"}, "id" : "id"}');
        }
        import('uploader.lib');             //导入上传类
        $uploader = new Uploader();
        $uploader->allowed_type(IMAGE_FILE_TYPE); //限制文件类型
        $uploader->addFile($file); //上传logo
        if (!$uploader->file_info()) {
            //$this->show_warning($uploader->get_error(), 'go_back', 'index.php?app=ad&amp;act=edit&amp;id=' . $ad_id);
            $this->show_warning($uploader->get_error());
            return false;
        }
        /* 指定保存位置的根目录 */
        $uploader->root_dir(ROOT_PATH);

        /* 上传 */
        if ($file_path = $uploader->save('data/files/offline', $uploader->random_filename())) {   //保存到指定目录，并以指定文件名$ad_id存储
            header('Content-type:text/json');
            $jsondata = '{"jsonrpc" : "2.0", "result" : "'.$file_path.'", "id" : "id"}';
            echo $jsondata;
        } else {
            die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "图片上传失败"}, "id" : "id"}');
        }
    }
}
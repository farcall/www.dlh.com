<?php
/**
 * Created by PhpStorm.
 * User: xiaokang
 * Date: 2016/5/1
 * Time: 4:23
 */

class Offline_orderApp extends StoreadminbaseApp{
    var $_msg_mod;
    var $_store_mod;
    var $_epay_mod;
    var $_msglog_mod;
    var $_member_mod;
    function __construct() {
        $this->Offline_order();
    }

    function Offline_order() {
        parent::__construct();
        $this->_msg_mod = & m('msg');
        $this->_msglog_mod = & m('msglog');
        $this->_member_mod = &m('member');
        $this->_epay_mod = &m('epay');
        $this->_store_mod = &m('store');
    }


    function index(){
        if(IS_POST){
            //检查是否存在凭证
            $offline_image = $_POST['offline_image'];
            if(empty($offline_image)){
                $this->show_warning('请确认交易凭证上传是否成功');
                return;
            }

            //会员检查
            $buyer_name = $_POST['buyer_name'];
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

//            if($store_epay['money'] < $yongjin){
//                $this->show_warning('资金不足：当前交易需要佣金'.$yongjin.',账户余额为'.$store_epay['money'],'立即充值','index.php?app=epay&act=czlist');
//                return;
//            }

            $buyer_id = $this->visitor->get('user_id');
            $seller_id = intval($this->visitor->get('manage_store'));
            $store_id = $this->visitor->get('manage_store');
            $seller_member = $this->_member_mod->get($seller_id);
            $store = $this->_store_mod->get($store_id);
            //构造订单数据
            $offline_order_data = array(
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
            $order_id = $order_type->submit_order($offline_order_data);
            if(empty($order_id)){
                $this->show_warning('订单创建失败');
            }



            return;
        }
        else{
            $this->display('paycenter/offline_order.html');
        }
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
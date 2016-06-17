<?php
/**
 * Created by PhpStorm.
 * User: xiaokang
 * Date: 2016/4/23
 * Time: 21:31
 */
require(ROOT_PATH . '/app/paycenterbase.app.php');

class PaycenterApp extends PaycenterbaseApp{
    function __construct(){
        parent::__construct();
        $this->PaycenterApp();
    }

    function  PaycenterApp(){
    }

    /**
     * 作用:账户预览
     * Created by QQ:710932
     */
    function index(){
        //会员名称
        $user = $this->visitor->get();
        $member_mod = & m('member');
        $member_data = $member_mod->get_info($user['user_id']);

        $epay_mod = &m('epay');
        $epay_data = $epay_mod->get(array(
            'conditions' => "user_id=" . $this->member['user_id'],
        ));

        $data['user_name'] = $member_data['user_name'];
        //会员级别
        $data['vip'] = $member_data['vip'];
        //余额:  0.00
        $data['money'] = $epay_data['money'];
        //冻结金额:  0.00
        $data['money_dj'] = $epay_data['money_dj'];
        //税费资金:  0.00
        $data['money_tax'] = $epay_data['money_tax'];
        //积分赠送全:  0
        $data['integral_power'] = intval($epay_data['integral_power'] / 100000);
        //白积分:  0
        $data['integral_white'] = $member_data['integral'];
        //红积分:  0
        $data['integral_red'] = $epay_data['integral_red'];
        
        $this->assign('member',$data);
        $this->display('paycenter/index.html');
    }
}
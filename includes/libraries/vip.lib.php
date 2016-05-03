<?php
/**
 * Created by PhpStorm.
 * User: xiaokang
 * Date: 2016/4/25
 * Time: 19:00
 */
class Vip{
    var $_member_mod;
    var $_epay_mod;
    var $_epayintegrallog_mod;
    var $_epaylog_mod;

    function __construct(){
        $this->_member_mod = &m('member');
        $this->_epay_mod = &m('epay');
        $this->_epayintegrallog_mod = &m('epay_integral_log');
        $this->_epaylog_mod = &m('epaylog');
    }

    /**
     * @param $user_id
     * 作用:设置ID为user_id的成员为VIP会员
     * Created by QQ:710932
     */
    function setVip($user_id){
        $data['vip'] = 1;

        //修改VIP标志
        $this->_member_mod->edit($user_id, $data);

        //积分赠送权+10
        $this->change_integralpower_vip($user_id);
        //白积分加1000*100
        $this->change_integralwhite_vip($user_id);
        //金额减1000
        $this->change_money_vip($user_id);
    }

    /**
     * @param $user_id
     *
     * @return bool
     * 作用:不是VIP返回flase  否则返回true
     * Created by QQ:710932
     */
    function isVip($user_id){
        $member = $this->_member_mod->get_info($user_id);
        if(empty($member)){
            return false;
        }

        if($member['vip'] == 0){
            return false;
        }

        return true;
    }


    /**
     * @param $user_id
     * 作用:成为VIP后赠送积分赠送权
     * Created by QQ:710932
     */
    function change_integralpower_vip($user_id){
        if(!intval($user_id)){
            return;
        }

        $epay = $this->_epay_mod->get( array(
            'conditions' => 'user_id=' . $user_id,
        ));
        if(empty($epay)){
            return;
        }


        $data['integral_power'] = $epay['integral_power'] + 1000/100;
        //插入记录
        $this->_epay_mod->edit($epay['id'], $data);

        //操作记录入积分记录
        $integralpower_log = array(
            'user_id' => $user_id,
            'user_name' => $epay['user_name'],
            'amount' => 1000/100,
            'total_amount'=>$epay['integral_power'] + 1000/100,
            'type'=>EPAY_INTEGRAL_POWER,
            'flow'=>'income',
            'add_time' => gmtime(),
            'remark' => '购买会员赠送10个积分赠送权',
        );
        $this->_epayintegrallog_mod->add($integralpower_log);
    }

    function change_integralwhite_vip($user_id){
        if(!intval($user_id)){
            return;
        }

        $epay = $this->_epay_mod->get( array(
            'conditions' => 'user_id=' . $user_id,
        ));
        if(empty($epay)){
            return;
        }


        //获取当前用户的信息
        $member = $this->_member_mod->get(intval($user_id));
        if(empty($member)){
            return;
        }

        $data['integral_white'] = $epay['integral_white'] + 1000*100;
        //插入记录
        $this->_epay_mod->edit(array(
            'conditions' => 'user_id=' . $user_id,
        ), $data);

        //操作记录入积分记录
        $integralpower_log = array(
            'user_id' => $user_id,
            'user_name' => $epay['user_name'],
            'amount' => 1000*100,
            'total_amount'=>$epay['integral_white'] + 1000*100,
            'type'=>EPAY_INTEGRAL_WHITE,
            'flow'=>'income',
            'add_time' => gmtime(),
            'remark' => '购买会员获得100000白积分',
        );
        $this->_epayintegrallog_mod->add($integralpower_log);
    }

    function change_money_vip($user_id){
        $order_sn = EPAY_BUY . date('YmdHis',gmtime()+8*3600).rand(1000,9999);
        //用户信息
        $epay = $this->_epay_mod->get($user_id);
        if(empty($epay)){
            return;
        }


        $new_epay = array(
            'money' => $epay['money'] - 1000,
        );
        $this->_epay_mod->edit('user_id=' . $user_id, $new_epay);

        $add_epaylog = array(
            'user_id' => $epay['user_id'],
            'user_name' => $epay['user_name'],
            'order_sn' => $order_sn,
            'add_time' => gmtime(),
            'type' => EPAY_BUY,
            'money_flow' => 'outlay',//转出
            'money' => 1000,
            'complete' => 1,
            'log_text' => "购买会员扣除1000元",
            'states' => EPAY_BUY,
        );
        $this->_epaylog_mod->add($add_epaylog);
    }
}
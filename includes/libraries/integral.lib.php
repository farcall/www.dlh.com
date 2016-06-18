<?php
//白积分管理
class Integral {

    var $_member_mod;
    var $_integral_log_mod;
    var $_epay_mod;
    function __construct() {
        $this->_member_mod = &m('member');
        $this->_integral_log_mod = & m('integral_log');
        $this->_epay_mod = &m('epay');

        //判断积分操作是否开启 未开启直接返回
        if (!Conf::get('integral_enabled')) {
            return;
        }
    }





    // 用户确认收货  获得积分计算
    function change_integral_buy($user_id, $amount) {
        if(!intval($user_id)||!$amount){
            return;
        }

        //确认收货 获得的积分为  比例×额度
        $integral_buy = intval(100 * $amount);

        //获取当前用户的信息
        $member = $this->_member_mod->get(intval($user_id));
        //用户未存在 则返回
        if (empty($member)) {
            return;
        }

        $epay = $this->_epay_mod->get('user_id='.intval($user_id));
        if (empty($epay)){
            return;
        }

        $data['total_white'] = $integral_buy + $epay['total_white']; #当前总共积分
        $this->_epay_mod->edit('user_id='.intval($user_id), $data);

        //操作记录入积分记录
        $integral_log = array(
            'user_id' => $user_id,
            'user_name' => $member['user_name'],
            'point' => $integral_buy,
            'add_time' => gmtime(),
            'remark' => '购买赠送积分' . $integral_buy,
            'integral_type' => INTEGRAL_BUY,
        );
        $this->_integral_log_mod->add($integral_log);
    }


    function change_integral_seller($user_id, $yongjin)
    {

        if (!intval($user_id) || !$yongjin) {
            return;
        }

        $integral_seller = intval(100 * $yongjin);

        //获取当前用户的信息
        $member = $this->_member_mod->get(intval($user_id));
        //用户未存在 则返回
        if (empty($member)) {
            return;
        }

        if (!$member['vip']) {
            return;
        }

        $epay = $this->_epay_mod->get('user_id='.intval($user_id));
        $data['total_white'] = $integral_seller + $epay['total_white']; #当前总共积分
        $this->_epay_mod->edit('user_id='.intval($user_id), $data);

        //操作记录入积分记录
        $integral_log = array(
            'user_id' => $user_id,
            'user_name' => $member['user_name'],
            'point' => $integral_seller,
            'add_time' => gmtime(),
            'remark' => '销售赠送积分' . $integral_seller,
            'integral_type' => INTEGRAL_SELLER,
        );
        $this->_integral_log_mod->add($integral_log);

    }



    /**
     * @param $data
     * 作用:白积分
     * Created by QQ:710932
     */
    function change_integral($data)
    {
        $epay = $this->_epay_mod->get('user_id=' . $data['user_id']);
        if ($data['integral_flow'] == 'income') {
            $new_epay = array(
                'total_white' => $epay['total_white'] + $data['integral'],
            );
        } else {
            $new_epay = array(
                'total_white' => $epay['total_white'] - $data['integral'],
            );
        }
        $this->_epay_mod->edit('user_id=' . $data['user_id'], $new_epay);

        $this->_integral_log_mod->add(array(
            'user_id' => $data['user_id'],
            'user_name' => $data['user_name'],
            'point' => $data['integral'],
            'add_time' => gmtime(),
            'remark' => $data['log_text'],
            'integral_type' => $data['type'],
        ));

    }

}

?>
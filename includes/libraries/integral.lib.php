<?php
//积分管理
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

    //用户注册 积分操作
    function change_integral_reg($user_id) {
        if(!intval($user_id)){
            return;
        }
        // 获取后台设置的  注册赠送积分
        $integral_reg = Conf::get('integral_reg');
        //未设置 或者小于 0 不进行操作
        if ($integral_reg <= 0) {
            return;
        }
        //获取当前用户的信息
        $member = $this->_member_mod->get(intval($user_id));
        //用户未存在 则返回
        if (empty($member)) {
            return;
        }
        $data['integral'] = $integral_reg + $member['integral']; #当前可用积分
        $data['total_integral'] = $integral_reg + $member['total_integral']; #当前总共积分
        $this->_member_mod->edit($user_id, $data);

        //操作记录入积分记录
        $integral_log = array(
            'user_id' => $user_id,
            'user_name' => $member['user_name'],
            'point' => $integral_reg,
            'add_time' => gmtime(),
            'remark' => '注册赠送积分' . $integral_reg,
            'integral_type' => EPAY_INTEGRAL_WHITE_BUY,
        );
        $this->_integral_log_mod->add($integral_log);
    }

    //用户登录 积分操作
    function change_integral_login($user_id) {
        if(!intval($user_id)){
            return;
        }
        
        // 获取后台设置的  注册赠送积分
        $integral_login = Conf::get('integral_login');
        //未设置 或者小于 0 不进行操作
        if ($integral_login <= 0) {
            return;
        }
        //获取当前用户的信息
        $member = $this->_member_mod->get(intval($user_id));
        //用户未存在 则返回
        if (empty($member)) {
            return;
        }


        //判断今日是否登录过赠送积分
        $begin_time = mktime(0, 0, 0, date('m'), date("d"), date('Y'));
        $end_time = mktime(0, 0, 0, date('m'), date("d") + 1, date('Y'));
        $info = $this->_integral_log_mod->get("integral_type=" . INTEGRAL_LOGIN . " AND user_id=" . $user_id . " AND add_time >" . $begin_time . " AND add_time <" . $end_time);
        if ($info) {
            return;
        }


        $data['integral'] = $integral_login + $member['integral']; #当前可用积分
        $data['total_integral'] = $integral_login + $member['total_integral']; #当前总共积分
        $this->_member_mod->edit($user_id, $data);

        //操作记录入积分记录
        $integral_log = array(
            'user_id' => $user_id,
            'user_name' => $member['user_name'],
            'point' => $integral_login,
            'add_time' => gmtime(),
            'remark' => '登录赠送积分' . $integral_login,
            'integral_type' => INTEGRAL_LOGIN,
        );
        $this->_integral_log_mod->add($integral_log);
    }

    //  推荐用户 获得积分计算  $user_id 为推荐者的用户ID
    function change_integral_recom($user_id) {
        if(!intval($user_id)){
            return;
        }
        // 获取后台设置的  推荐赠送积分
        $integral_recom = Conf::get('integral_recom');
        //未设置 或者小于 0 不进行操作
        if ($integral_recom <= 0) {
            return;
        }
        //获取当前用户的信息
        $member = $this->_member_mod->get(intval($user_id));
        //用户未存在 则返回
        if (empty($member)) {
            return;
        }
        $data['integral'] = $integral_recom + $member['integral']; #当前可用积分
        $data['total_integral'] = $integral_recom + $member['total_integral']; #当前总共积分
        $this->_member_mod->edit($user_id, $data);

        //操作记录入积分记录
        $integral_log = array(
            'user_id' => $user_id,
            'user_name' => $member['user_name'],
            'point' => $integral_recom,
            'add_time' => gmtime(),
            'remark' => '推荐赠送积分' . $integral_recom,
            'integral_type' => INTEGRAL_RECOM,
        );
        $this->_integral_log_mod->add($integral_log);
    }

    // 用户确认收货  获得积分计算
    function change_integral_buy($user_id, $amount) {
        if(!intval($user_id)||!$amount){
            return;
        }
        // 获取后台设置的  购买获得积分计算
//        $integral_buy = Conf::get('integral_buy');
//        //未设置 或者小于 0 不进行操作
//        if ($integral_buy <= 0) {
//            return;
//        }
        //确认收货 获得的积分为  比例×额度
        $integral_buy = intval(100 * $amount);

        //获取当前用户的信息
        $member = $this->_member_mod->get(intval($user_id));
        //用户未存在 则返回
        if (empty($member)) {
            return;
        }
        $data['integral'] = $integral_buy + $member['integral']; #当前可用积分
        $data['total_integral'] = $integral_buy + $member['total_integral']; #当前总共积分
        $this->_member_mod->edit($user_id, $data);

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


        //积分赠送权
        $epay_data = $this->_epay_mod->get(array(
            'conditions' => 'user_id=' . $user_id,
        ));

        $epay_data['integral_power'] = $epay_data['integral_power'] + $integral_buy;
        $this->_epay_mod->edit('user_id=' . $user_id, $epay_data);

        //todo 积分赠送权日志

    }

    //此处是  推荐成为卖家， 卖家卖出产品后，他的推荐者可以获得积分
    function change_integral_tuijianseller($order){
        //todo
        $add_time = gmtime();

        //判断是否开启 推荐会员成为卖家 ， 卖家卖出产品  推荐者会获取提成
        if (!Conf::get('tuijian_seller_status')) {
            return;
        }

        /* 第1级 佣金操作  查看卖家是否有推荐人  BEGIN */
        if (!Conf::get('tuijian_seller_ratio1')) {
            return;
        }
        $tuijian_seller_ratio1 = round(Conf::get('tuijian_seller_ratio1') / 100, 3);

        //卖家相关信息
        $seller_id = $order['seller_id'];
        $seller_info = $this->_member_mod->get($seller_id);


        //1级推荐人 不存在卖家的推荐人则返回
        if (!$seller_info['referid']) {
            return;
        }
        $referid_1 = $seller_info['referid'];
        //查看推荐人是否存在 不存在则不操作
        $referinfo_1 = $this->_member_mod->get($referid_1);
        if (empty($referinfo_1)) {
            return;
        }

        //1级推荐人获得佣金
        $this->change_integral(
            array(
                'user_id' => $referinfo_1['user_id'],
                'add_time'=>$add_time,
                'user_name' => $referinfo_1['user_name'],
                'order_sn' => $order['order_sn'],
                'type' => EPAY_TUIJIAN_SELLER,
                'integral' => round($order['goods_amount'] * $tuijian_seller_ratio1, 3) * 100, #一级推荐人应该获取的佣金
                'integral_flow' => 'income', #流入积分
                'complete' => '1',
                'log_text' => '恭喜你获得' . round($order['goods_amount'] * $tuijian_seller_ratio1, 3) * 100 . '个积分,订单金额为' . $order['goods_amount'] . ',1级佣金比例为' . $tuijian_seller_ratio1
                    . ',推荐关系为:' . $seller_info['user_name'] . '<<--' . $referinfo_1['user_name'],
            )
        );
        /* 第1级 佣金操作  查看卖家是否有推荐人  END */



        /* 第2级 佣金操作  查看卖家是否有推荐人  BEGIN */
        if (!Conf::get('tuijian_seller_ratio2')) {
            return;
        }
        $tuijian_seller_ratio2 = round(Conf::get('tuijian_seller_ratio2') / 100, 3);
        //2级推荐人 不存在卖家的推荐人则返回
        if (!$referinfo_1['referid']) {
            return;
        }
        $referid_2 = $referinfo_1['referid'];
        //查看推荐人是否存在 不存在则不操作
        $referinfo_2 = $this->_member_mod->get($referid_2);
        if (empty($referinfo_2)) {
            return;
        }
        //2级推荐人获得佣金
        $this->change_epay(
            array(
                'user_id' => $referinfo_2['user_id'],
                'add_time'=>$add_time,
                'user_name' => $referinfo_2['user_name'],
                'order_sn' => $order['order_sn'],
                'type' => EPAY_TUIJIAN_SELLER,
                'money' => round($order['goods_amount'] * $tuijian_seller_ratio2, 3), #2级推荐人应该获取的佣金
                'money_flow' => 'income', #流入佣金
                'complete' => '1',
                'log_text' => '恭喜你获得' . round($order['goods_amount'] * $tuijian_seller_ratio2, 3) * 100 . '个积分,2级佣金比例为' . $tuijian_seller_ratio2
                    . ',推荐关系为:' . $seller_info['user_name'] . '<<--' . $referinfo_1['user_name'] . '<<--' . $referinfo_2['user_name'],
            )
        );
        /* 第2级 佣金操作  查看卖家是否有推荐人  END */


        /* 第3级 佣金操作  查看卖家是否有推荐人  BEGIN */
        if (!Conf::get('tuijian_seller_ratio3')) {
            return;
        }
        $tuijian_seller_ratio3 = round(Conf::get('tuijian_seller_ratio3') / 100, 3);
        //2级推荐人 不存在卖家的推荐人则返回
        if (!$referinfo_2['referid']) {
            return;
        }
        $referid_3 = $referinfo_2['referid'];
        //查看推荐人是否存在 不存在则不操作
        $referinfo_3 = $this->_member_mod->get($referid_3);
        if (empty($referinfo_3)) {
            return;
        }

        //3级推荐人获得佣金
        $this->change_epay(
            array(
                'user_id' => $referinfo_3['user_id'],
                'add_time'=>$add_time,
                'user_name' => $referinfo_3['user_name'],
                'order_sn' => $order['order_sn'],
                'type' => EPAY_TUIJIAN_SELLER,
                'money' => round($order['goods_amount'] * $tuijian_seller_ratio3, 2), #3级推荐人应该获取的佣金
                'money_flow' => 'income', #流入佣金
                'complete' => '1',
                'log_text' => '恭喜你获得' . round($order['goods_amount'] * $tuijian_seller_ratio3, 3) * 100 . '个积分,订单金额为' . $order['goods_amount'] . ',3级佣金比例为' . $tuijian_seller_ratio3
                    . ',推荐关系为:' . $seller_info['user_name'] . '<<--' . $referinfo_1['user_name'] . '<<--' . $referinfo_2['user_name'] . '<<--' . $referinfo_3['user_name'],
            )
        );
        /* 第3级 佣金操作  查看卖家是否有推荐人  END */
    }


    function change_integral_seller($user_id, $yongjin)
    {

        if (!intval($user_id) || !$yongjin) {
            return;
        }
        // 获取后台设置的  购买获得积分计算
//        $integral_seller = Conf::get('integral_seller');
//        //未设置 或者小于 0 不进行操作
//        if ($integral_seller <= 0) {
//            return;
//        }
        //确认收货 获得的积分为  比例×额度
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

        $data['integral'] = $integral_seller + $member['integral']; #当前可用积分
        $data['total_integral'] = $integral_seller + $member['total_integral']; #当前总共积分
        $this->_member_mod->edit($user_id, $data);

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


        //积分赠送权
        $epay_data = $this->_epay_mod->get(array(
            'conditions' => 'user_id=' . $user_id,
        ));

        $epay_data['integral_power'] = $epay_data['integral_power'] + $integral_seller;
        $this->_epay_mod->edit('user_id=' . $user_id, $epay_data);
        //todo 积分赠送权日志
    }

    /**
     *  此处相关逻辑代码  直接 在app/egg.app.php 写入了  
     * @param type $user_id
     * @param type $point
     */
    function change_integral_egg($user_id, $point) {
        
    }

    /**
     * 用户兑换产品扣除积分
     */
    function change_integral_goods($user_id, $point, $goods_id) {

    }


    /**
     * @param $data
     * 作用:白积分
     * Created by QQ:710932
     */
    function change_integral($data)
    {
        $member = $this->_member_mod->get('user_id=' . $data['user_id']);
        if ($data['money_flow'] == 'income') {
            $new_member = array(
                'integral' => $member['integral'] + $data['integral'],
            );
        } else {
            $new_member = array(
                'money' => $member['integral'] - $data['integral'],
            );
        }
        $this->_member_mod->edit('user_id=' . $data['user_id'], $new_member);

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
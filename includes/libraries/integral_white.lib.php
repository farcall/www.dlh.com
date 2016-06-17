<?php

//白积分管理
class Integral_white
{

    var $_member_mod;
    var $_integral_log_mod;
    var $_epay_mod;

    function __construct()
    {
        $this->_member_mod = &m('member');
        $this->_integral_log_mod = &m('integral_log');
        $this->_epay_mod = &m('epay');
    }


    // 用户确认收货  获得积分计算
    function change_integral_buy($user_id, $amount)
    {
        if (!intval($user_id) || !$amount) {
            return;
        }

        //确认收货 获得的积分为  比例×额度
        $integral_buy = intval(100 * $amount);

        $this->_epay_mod->


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
            'remark' => '购买赠送白积分' . $integral_buy.'赠送前白积分'.$member['integral'].'赠送后白积分:'.$data['integral'],
            'integral_type' => INTEGRAL_BUY,
        );
        $this->_integral_log_mod->add($integral_log);
    }

    //此处是  推荐成为卖家， 卖家卖出产品后，他的推荐者可以获得积分
    function change_integral_tuijianseller($order)
    {
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
        $tuijian_seller_ratio1 = round(Conf::get('tuijian_seller_ratio1') / 100, 2);

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
        $this->change_epay(
            array(
                'user_id' => $referinfo_1['user_id'],
                'add_time' => $add_time,
                'user_name' => $referinfo_1['user_name'],
                'order_sn' => $order['order_sn'],
                'type' => EPAY_TUIJIAN_SELLER,
                'integral' => round($order['goods_amount'] * $tuijian_seller_ratio1, 2) * 100, #一级推荐人应该获取的佣金
                'integral_flow' => 'income', #流入积分
                'complete' => '1',
                'log_text' => '恭喜你获得' . round($order['goods_amount'] * $tuijian_seller_ratio1, 2) * 100 . '个积分,订单金额为' . $order['goods_amount'] . ',1级佣金比例为' . $tuijian_seller_ratio1
                    . ',推荐关系为:' . $seller_info['user_name'] . '<<--' . $referinfo_1['user_name'],
            )
        );
        /* 第1级 佣金操作  查看卖家是否有推荐人  END */


        /* 第2级 佣金操作  查看卖家是否有推荐人  BEGIN */
        if (!Conf::get('tuijian_seller_ratio2')) {
            return;
        }
        $tuijian_seller_ratio2 = round(Conf::get('tuijian_seller_ratio2') / 100, 2);
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
                'add_time' => $add_time,
                'user_name' => $referinfo_2['user_name'],
                'order_sn' => $order['order_sn'],
                'type' => EPAY_TUIJIAN_SELLER,
                'money' => round($order['goods_amount'] * $tuijian_seller_ratio2, 2), #2级推荐人应该获取的佣金
                'money_flow' => 'income', #流入佣金
                'complete' => '1',
                'log_text' => '恭喜你获得' . round($order['goods_amount'] * $tuijian_seller_ratio2, 2) * 100 . '个积分,2级佣金比例为' . $tuijian_seller_ratio2
                    . ',推荐关系为:' . $seller_info['user_name'] . '<<--' . $referinfo_1['user_name'] . '<<--' . $referinfo_2['user_name'],
            )
        );
        /* 第2级 佣金操作  查看卖家是否有推荐人  END */


        /* 第3级 佣金操作  查看卖家是否有推荐人  BEGIN */
        if (!Conf::get('tuijian_seller_ratio3')) {
            return;
        }
        $tuijian_seller_ratio3 = round(Conf::get('tuijian_seller_ratio3') / 100, 2);
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
                'add_time' => $add_time,
                'user_name' => $referinfo_3['user_name'],
                'order_sn' => $order['order_sn'],
                'type' => EPAY_TUIJIAN_SELLER,
                'money' => round($order['goods_amount'] * $tuijian_seller_ratio3, 2), #3级推荐人应该获取的佣金
                'money_flow' => 'income', #流入佣金
                'complete' => '1',
                'log_text' => '恭喜你获得' . round($order['goods_amount'] * $tuijian_seller_ratio3, 2) * 100 . '个积分,订单金额为' . $order['goods_amount'] . ',3级佣金比例为' . $tuijian_seller_ratio3
                    . ',推荐关系为:' . $seller_info['user_name'] . '<<--' . $referinfo_1['user_name'] . '<<--' . $referinfo_2['user_name'] . '<<--' . $referinfo_3['user_name'],
            )
        );
        /* 第3级 佣金操作  查看卖家是否有推荐人  END */
    }

    //此处是  推荐成为买家， 买家购买产品后，他的推荐者可以获得积分
    function change_integral_tuijianbuyer($order)
    {
        //todo
    }

    //修改积分——qq435795
    function change_integra($data)
    {
        //$user_id,$order_sn,$type,$money,$money_flow,$complete,$log_text
        //获取当前用户的信息
        $member = $this->_member_mod->get($data['user_id']);
        //用户未存在 则返回
        if (empty($member)) {
            return;
        }
        $data['integral'] = $integral_reg + $member['integral']; #当前可用积分
        $data['total_integral'] = $integral_reg + $member['total_integral']; #当前总共积分
        $this->_member_mod->edit($data['user_id'], $data);

        //操作记录入积分记录
        $integral_log = array(
            'user_id' => $data['user_id'],
            'user_name' => $member['user_name'],
            'point' => $integral_reg,
            'add_time' => gmtime(),
            'remark' => '注册赠送积分' . $integral_reg,
            'integral_type' => INTEGRAL_REG,
        );
        $this->_integral_log_mod->add($integral_log);
    }


}

?>
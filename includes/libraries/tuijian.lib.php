<?php

class Tuijian {

    var $_epay_mod;
    var $_epaylog_mod;
    var $_member_mod;
    var $_integral_log_mod;

    function __construct() {
        $this->_epay_mod = & m('epay');
        $this->_epaylog_mod = & m('epaylog');
        $this->_member_mod = & m('member');
        $this->_integral_log_mod = &m('integral_log');
    }

    function do_tuijian($order) {
        $this->tuijian_seller($order);
        $this->tuijian_buyer($order);
    }

    /**
     * 此处是  推荐成为卖家， 卖家卖出产品后，他的推荐者可以获得佣金
     */
    function tuijian_seller($order) {
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
        //查看推荐人是否存在 不存在则不操作
        $referinfo_1 = $this->get_referid_member($seller_info);
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
                    'integral_flow' => 'income', #流入佣金
                    'complete' => '1',
                    'log_text' => '恭喜你获得' . round($order['goods_amount'] * $tuijian_seller_ratio1, 2) * 100 . '白积分,1级佣金比例为' . $tuijian_seller_ratio1
                    . ',推荐关系为:' . $seller_info['user_name'] . '<<--' . $referinfo_1['user_name'],
                )
        );
        $this->change_integral_power(array(
            'user_id' => $referinfo_1['user_id'],
            'add_time' => $add_time,
            'user_name' => $referinfo_1['user_name'],
            'order_sn' => $order['order_sn'],
            'type' => EPAY_TUIJIAN_SELLER,
            'integral' => round($order['goods_amount'] * $tuijian_seller_ratio1, 3) * 100, #一级推荐人应该获取的佣金
            'integral_flow' => 'income', #流入佣金
            'complete' => '1',
            'log_text' => '恭喜你获得' . round($order['goods_amount'] * $tuijian_seller_ratio1, 2) * 100 . '白积分,1级佣金比例为' . $tuijian_seller_ratio1
                . ',推荐关系为:' . $seller_info['user_name'] . '<<--' . $referinfo_1['user_name'],
        ));
        /* 第1级 佣金操作  查看卖家是否有推荐人  END */


        /* 第2级 佣金操作  查看卖家是否有推荐人  BEGIN */
        if (!Conf::get('tuijian_seller_ratio2')) {
            return;
        }
        $tuijian_seller_ratio2 = round(Conf::get('tuijian_seller_ratio2') / 100, 3);
        //2级推荐人 不存在卖家的推荐人则返回
        //查看推荐人是否存在 不存在则不操作
        $referinfo_2 = $this->get_referid_member($referinfo_1);
        if (empty($referinfo_2)) {
            return;
        }
        //2级推荐人获得佣金
        $this->change_integral(
                array(
                    'user_id' => $referinfo_2['user_id'],
                    'add_time'=>$add_time,
                    'user_name' => $referinfo_2['user_name'],
                    'order_sn' => $order['order_sn'],
                    'type' => EPAY_TUIJIAN_SELLER,
                    'integral' => round($order['goods_amount'] * $tuijian_seller_ratio2, 3) * 100, #2级推荐人应该获取的佣金
                    'integral_flow' => 'income', #流入佣金
                    'complete' => '1',
                    'log_text' => '恭喜你获得' . round($order['goods_amount'] * $tuijian_seller_ratio2, 2) * 100 . '白积分,2级佣金比例为' . $tuijian_seller_ratio2
                    . ',推荐关系为:' . $seller_info['user_name'] . '<<--' . $referinfo_1['user_name'] . '<<--' . $referinfo_2['user_name'],
                )
        );
        $this->change_integral_power(array(
            'user_id' => $referinfo_2['user_id'],
            'add_time' => $add_time,
            'user_name' => $referinfo_2['user_name'],
            'order_sn' => $order['order_sn'],
            'type' => EPAY_TUIJIAN_SELLER,
            'integral' => round($order['goods_amount'] * $tuijian_seller_ratio2, 3) * 100, #2级推荐人应该获取的佣金
            'integral_flow' => 'income', #流入佣金
            'complete' => '1',
            'log_text' => '恭喜你获得' . round($order['goods_amount'] * $tuijian_seller_ratio2, 2) * 100 . '白积分,2级佣金比例为' . $tuijian_seller_ratio2
                . ',推荐关系为:' . $seller_info['user_name'] . '<<--' . $referinfo_1['user_name'] . '<<--' . $referinfo_2['user_name'],
        ));
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
        $referinfo_3 = $this->get_referid_member($referinfo_2);
        if (empty($referinfo_3)) {
            return;
        }
        //3级推荐人获得佣金
        $this->change_integral(
                array(
                    'user_id' => $referinfo_3['user_id'],
                    'add_time'=>$add_time,
                    'user_name' => $referinfo_3['user_name'],
                    'order_sn' => $order['order_sn'],
                    'type' => EPAY_TUIJIAN_SELLER,
                    'integral' => round($order['goods_amount'] * $tuijian_seller_ratio3, 3) * 100, #3级推荐人应该获取的佣金
                    'integral_flow' => 'income', #流入佣金
                    'complete' => '1',
                    'log_text' => '恭喜你获得' . round($order['goods_amount'] * $tuijian_seller_ratio3, 2) * 100 . '白积分,3级佣金比例为' . $tuijian_seller_ratio3
                    . ',推荐关系为:' . $seller_info['user_name'] . '<<--' . $referinfo_1['user_name'] . '<<--' . $referinfo_2['user_name'] . '<<--' . $referinfo_3['user_name'],
                )
        );
        $this->change_integral_power(array(
            'user_id' => $referinfo_3['user_id'],
            'add_time' => $add_time,
            'user_name' => $referinfo_3['user_name'],
            'order_sn' => $order['order_sn'],
            'type' => EPAY_TUIJIAN_SELLER,
            'integral' => round($order['goods_amount'] * $tuijian_seller_ratio3, 3) * 100, #3级推荐人应该获取的佣金
            'integral_flow' => 'income', #流入佣金
            'complete' => '1',
            'log_text' => '恭喜你获得' . round($order['goods_amount'] * $tuijian_seller_ratio3, 2) * 100 . '白积分,3级佣金比例为' . $tuijian_seller_ratio3
                . ',推荐关系为:' . $seller_info['user_name'] . '<<--' . $referinfo_1['user_name'] . '<<--' . $referinfo_2['user_name'] . '<<--' . $referinfo_3['user_name'],
        ));
        /* 第3级 佣金操作  查看卖家是否有推荐人  END */
    }

    /**
     * 此处是  推荐成为买家， 买家购买产品后，他的推荐者可以获得佣金，佣金来源于卖家
     */
    function tuijian_buyer($order) {
        $add_time = gmtime();
        //判断是否开启 推荐会员成为买家 ， 买家购买产品  推荐者会获取提成，佣金来源于卖家
        if (!Conf::get('tuijian_buyer_status')) {
            return;
        }
        /* 第1级 佣金操作  查看卖家是否有推荐人  BEGIN */
        if (!Conf::get('tuijian_buyer_ratio1')) {
            return;
        }
        $tuijian_buyer_ratio1 = round(Conf::get('tuijian_buyer_ratio1') / 100, 3);

        //卖家相关信息
        $seller_id = $order['seller_id'];
        $seller_info = $this->_member_mod->get($seller_id);

        //买家相关信息
        $buyer_id = $order['buyer_id'];
        $buyer_info = $this->_member_mod->get($buyer_id);



        //查看推荐人是否存在 不存在则不操作
        $referinfo_1 = $this->get_referid_member($buyer_info);
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
                    'type' => EPAY_TUIJIAN_BUYER,
                    'integral' => round($order['goods_amount'] * $tuijian_buyer_ratio1, 2) * 100, #一级推荐人应该获取的佣金
                    'integral_flow' => 'income', #流入佣金
                    'complete' => '1',
                    'log_text' => '恭喜你获得' . round($order['goods_amount'] * $tuijian_buyer_ratio1, 2) * 100 . '白积分,1级佣金比例为' . $tuijian_buyer_ratio1
                    . ',推荐关系为:' . $buyer_info['user_name'] . '<<--' . $referinfo_1['user_name'],
                )
        );

        $this->change_integral_power(
            array(
                'user_id' => $referinfo_1['user_id'],
                'add_time' => $add_time,
                'user_name' => $referinfo_1['user_name'],
                'order_sn' => $order['order_sn'],
                'type' => EPAY_TUIJIAN_BUYER,
                'integral' => round($order['goods_amount'] * $tuijian_buyer_ratio1, 2) * 100, #一级推荐人应该获取的佣金
                'integral_flow' => 'income', #流入佣金
                'complete' => '1',
                'log_text' => '恭喜你获得' . round($order['goods_amount'] * $tuijian_buyer_ratio1, 2) * 100 . '白积分,1级佣金比例为' . $tuijian_buyer_ratio1
                    . ',推荐关系为:' . $buyer_info['user_name'] . '<<--' . $referinfo_1['user_name'],
            )
        );
        /* 第1级 佣金操作  查看卖家是否有推荐人  END */



        /* 第2级 佣金操作  查看买家是否有推荐人  BEGIN */
        if (!Conf::get('tuijian_buyer_ratio2')) {
            return;
        }
        $tuijian_buyer_ratio2 = round(Conf::get('tuijian_buyer_ratio2') / 100, 2);
        //2级推荐人 不存在买家的推荐人则返回
        //查看推荐人是否存在 不存在则不操作
        $referinfo_2 = $this->get_referid_member($referinfo_1);
        if (empty($referinfo_2)) {
            return;
        }

        //2级推荐人获得佣金
        $this->change_integral(
                array(
                    'user_id' => $referinfo_2['user_id'],
                    'add_time'=>$add_time,
                    'user_name' => $referinfo_2['user_name'],
                    'order_sn' => $order['order_sn'],
                    'type' => EPAY_TUIJIAN_BUYER,
                    'integral' => round($order['goods_amount'] * $tuijian_buyer_ratio2, 2) * 100, #2级推荐人应该获取的佣金
                    'integral_flow' => 'income', #流入佣金
                    'complete' => '1',
                    'log_text' => '恭喜你获得' . round($order['goods_amount'] * $tuijian_buyer_ratio2, 2) * 100 . '白积分,2级佣金比例为' . $tuijian_buyer_ratio2
                    . ',推荐关系为:' . $buyer_info['user_name'] . '<<--' . $referinfo_1['user_name'] . '<<--' . $referinfo_2['user_name'],
                )
        );

        $this->change_integral_power(
            array(
                'user_id' => $referinfo_2['user_id'],
                'add_time' => $add_time,
                'user_name' => $referinfo_2['user_name'],
                'order_sn' => $order['order_sn'],
                'type' => EPAY_TUIJIAN_BUYER,
                'integral' => round($order['goods_amount'] * $tuijian_buyer_ratio2, 2) * 100, #2级推荐人应该获取的佣金
                'integral_flow' => 'income', #流入佣金
                'complete' => '1',
                'log_text' => '恭喜你获得' . round($order['goods_amount'] * $tuijian_buyer_ratio2, 2) * 100 . '白积分,2级佣金比例为' . $tuijian_buyer_ratio2
                    . ',推荐关系为:' . $buyer_info['user_name'] . '<<--' . $referinfo_1['user_name'] . '<<--' . $referinfo_2['user_name'],
            )
        );
        /* 第2级 佣金操作  查看卖家是否有推荐人  END */


        /* 第3级 佣金操作  查看卖家是否有推荐人  BEGIN */
        if (!Conf::get('tuijian_buyer_ratio3')) {
            return;
        }
        $tuijian_buyer_ratio3 = round(Conf::get('tuijian_buyer_ratio3') / 100, 2);
        //2级推荐人 不存在卖家的推荐人则返回
        if (!$referinfo_2['referid']) {
            return;
        }
        $referid_3 = $referinfo_2['referid'];
        //查看推荐人是否存在 不存在则不操作
        $referinfo_3 = $this->get_referid_member($referinfo_2);
        if (empty($referinfo_3)) {
            return;
        }

        //3级推荐人获得佣金
        $this->change_integral(
                array(
                    'user_id' => $referinfo_3['user_id'],
                    'add_time'=>$add_time,
                    'user_name' => $referinfo_3['user_name'],
                    'order_sn' => $order['order_sn'],
                    'type' => EPAY_TUIJIAN_BUYER,
                    'integral' => round($order['goods_amount'] * $tuijian_buyer_ratio3, 2) * 100, #3级推荐人应该获取的佣金
                    'integral_flow' => 'income', #流入佣金
                    'complete' => '1',
                    'log_text' => '恭喜你获得' . round($order['goods_amount'] * $tuijian_buyer_ratio3, 2) * 100 . '白积分，3级佣金比例为' . $tuijian_buyer_ratio3
                    . ',推荐关系为:' . $buyer_info['user_name'] . '<<--' . $referinfo_1['user_name'] . '<<--' . $referinfo_2['user_name'] . '<<--' . $referinfo_3['user_name'],
                )
        );

        $this->change_integral_power(array(
            'user_id' => $referinfo_3['user_id'],
            'add_time' => $add_time,
            'user_name' => $referinfo_3['user_name'],
            'order_sn' => $order['order_sn'],
            'type' => EPAY_TUIJIAN_BUYER,
            'integral' => round($order['goods_amount'] * $tuijian_buyer_ratio3, 2) * 100, #3级推荐人应该获取的佣金
            'integral_flow' => 'income', #流入佣金
            'complete' => '1',
            'log_text' => '恭喜你获得' . round($order['goods_amount'] * $tuijian_buyer_ratio3, 2) * 100 . '白积分，3级佣金比例为' . $tuijian_buyer_ratio3
                . ',推荐关系为:' . $buyer_info['user_name'] . '<<--' . $referinfo_1['user_name'] . '<<--' . $referinfo_2['user_name'] . '<<--' . $referinfo_3['user_name'],
        ));
        /* 第3级 佣金操作  查看卖家是否有推荐人  END */
    }

    /**
     * 作用:得到上级推荐人（推荐人网络中的第一个VIP）
     * 如果上级推荐人中没有VIP
     * Created by QQ:710932
     */
    function get_referid_member($member)
    {
        //上级推荐ID
        $referid_id = $member['referid'];

        //推荐网络停止
        if ($referid_id <= 0) {
            return;
        }

        $referid_member = $this->_member_mod->get($referid_id);


        if ($referid_member['vip']) {
            return $referid_member;
        } else {
            return $this->get_referid_member($referid_member);
        }
    }


    /**
     * @param $data
     * 作用:积分赠送权
     * Created by QQ:710932
     */
    function change_integral_power($data)
    {

        $member = $this->_member_mod->get('user_id=' . $data['user_id']);
        $epay = $this->_epay_mod->get('user_id=' . $data['user_id']);

        if ($data['integral_flow'] == 'income') {
            $epay['integral_power'] = $epay['integral_power'] + $data['integral'];


        } else {
            $epay['integral_power'] = $epay['integral_power'] - $data['integral'];
        }


        $this->_epay_mod->edit('user_id=' . $data['user_id'], $epay);

        //todo 积分赠送权日志
    }

    /**
     * @param $data
     * 作用:白积分
     * Created by QQ:710932
     */
    function change_integral($data)
    {
        $member = $this->_member_mod->get('user_id=' . $data['user_id']);
        if ($data['integral_flow'] == 'income') {
            $new_member = array(
                'integral' => $member['integral'] + $data['integral'],
            );
        } else {
            $new_member = array(
                'integral' => $member['integral'] - $data['integral'],
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

<?php

/**
 * Created by PhpStorm.
 * User: xiaokang
 * Date: 2016/5/4
 * Time: 11:30
 */
class Integral_redApp extends MallbaseApp
{
    var $_epay_mod;
    var $_eapylog_mod;
    var $_integral_goods_mod;

    function __construct()
    {
        $this->Integral_redApp();
    }

    function Integral_redApp()
    {
        parent::__construct();
        $this->_epay_mod = &m('epay');
        $this->_eapylog_mod = &m('epaylog');
        $this->_integral_log_mod = &m('integral_log');
    }

    /**
     * 作用:红积分兑换为现金
     * Created by QQ:710932
     */
    function exchange()
    {
        if ($_POST) {
            //用于兑换的红积分数量
            $red = $_POST['integral_red'];

            if ($red <= 0) {
                $this->show_warning('红积分数量不能小于0');
                return;
            }

            $epay_data = $this->_epay_mod->get(array(
                'conditions' => "user_id = '{$this->visitor->get('user_id')}'",
            ));

            if ($red > $epay_data['integral_red']) {
                $this->show_warning('对不起，您的红积分不足！');
                return;
            }

            //红积分减少
            //税费资金增加=红积分*10%
            //现金余额增加=红积分*90%
            $data = array(
                'integral_red' => $epay_data['integral_red'] - $red,
                'money_tax' => $epay_data['money_tax'] + $red * 0.05,
                'money' => $epay_data['money'] + $red * 0.95,
            );

            $this->_epay_mod->edit($epay_data['id'], $data);


            $this->_integral_log_mod->add(array(
                'user_id' => $this->visitor->get('user_id'),
                'user_name' => $this->visitor->get('user_name'),
                'point' => $data['money_tax'],
                'add_time' => gmtime(),
                'remark' => '红积分兑换支出' . $data['integral_red'],
                'integral_type' => EPAY_INTEGRAL_RED_EXCHANGE_RED,
            ));

            $this->_integral_log_mod->add(array(
                'user_id' => $this->visitor->get('user_id'),
                'user_name' => $this->visitor->get('user_name'),
                'point' => $data['integral_red'],
                'add_time' => gmtime(),
                'remark' => '红积分兑换缴纳' . $data['money_tax'] . '元税费',
                'integral_type' => EPAY_INTEGRAL_RED_EXCHANGE_MONEYTAX,
            ));

            $this->_integral_log_mod->add(array(
                'user_id' => $this->visitor->get('user_id'),
                'user_name' => $this->visitor->get('user_name'),
                'point' => $data['money'],
                'add_time' => gmtime(),
                'remark' => '红积分兑换得到' . $data['money'] . '元',
                'integral_type' => EPAY_INTEGRAL_RED_EXCHANGE_MONEY,
            ));

            $this->show_message('恭喜，兑换完成');

        }
    }
}
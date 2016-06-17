<?php
/**
 * Created by PhpStorm.
 * User: Dong
 * Date: 16/6/12
 * Time: 下午11:19
 */

//白积分的运算类
class white{
    var $mod_integral_log;
    var $mod_order;

    var $total_white;
    var $used_white;

    var $user_name;

    var $vip_time;      //成为VIP的时间点

    function __construct($user_name)
    {
        $this->user_name = $user_name;

        $this->mod_integral_log = &m('integral_log');
        $this->mod_order = &m('order');

        //给VIP时间赋值,便于运算
        $this->_vip_time();


        $this->total_white = $this->_vip() + $this->_tuijian()+$this->_xiaofei()+$this->_xiaoshou();
        $this->used_white = $this->setUsedWhite();
    }


    /**
     * @return mixed
     */public function getAllWhite()
    {
        return $this->total_white;
    }

     /**
     * @return mixed
     */
    public function getUsedWhite()
    {
        return $this->used_white;
    }

    /**
     * @param mixed $used_white
     * 设置可用白积分
     */
    public function setUsedWhite()
    {
        return $this->mod_integral_log->getOne("select sum(point) from ecm_integral_log WHERE user_name='{$this->user_name}' and integral_type = 10");
    }


    function _vip_time(){
        $remark = '购买会员获得100000白积分';
        $vip = $this->mod_integral_log->get(array(
            'conditions' => "remark = '{$remark}' and user_name='{$this->user_name}'",
        ));

        $this->vip_time = empty($vip) ? 0 : $vip['add_time'];
    }

    function _vip(){
        return $this->mod_integral_log->getOne("select sum(point) from ecm_integral_log WHERE user_name='{$this->user_name}' and integral_type = 11");
    }
    //推荐白积分-从日志记录提取
    function _tuijian(){
        //100->推荐者购买
        return $this->mod_integral_log->getOne("select sum(point) from ecm_integral_log WHERE user_name='{$this->user_name}' and integral_type = 100");
    }

    //销售得到的白积分
    function _xiaoshou(){
        if (!$this->vip_time){
            return 0;
        }

        $xiaoshou_amount = $this->mod_order->getOne("select sum(order_amount) from ecm_order WHERE status=40 and seller_name = '{$this->user_name}' and add_time>={$this->vip_time}");
        return $xiaoshou_amount / 10 * 100;
    }

    //从订单提取包含购买VIP获得的白积分
    function _xiaofei(){
        $xiaofei_amount = $this->mod_order->getOne("select sum(order_amount) from ecm_order WHERE status=40 and buyer_name = '{$this->user_name}'");
        return $xiaofei_amount * 100;
    }
}




class AutocheckApp extends BackendApp
{
     var $mod_member;
    function __construct()
    {
        $this->AutocheckApp();
    }

    function AutocheckApp()
    {
        parent::BackendApp();

        header("Content-Type: text/html;charset=utf-8");
        $this->mod_member = &m('member');
    }


    //循环处理所有账户
    function loop()
    {
        $members = $this->mod_member->findAll();
        echo "共" . count($members) . "个账户<br>";

        foreach ($members as $k => $v) {
            $this->parse($v);
        }
    }

    //针对某一个账户进行处理
    function parse($member)
    {
        $white = new white($member['user_name'],gmtime());

        echo "用户:".$member['user_name']."---全部白积分:".$white->total_white."---消耗白积分:".$white->used_white."---白积分资金差:".($white->total_white-$white->used_white-$member['integral'])."<br>";
    }





}
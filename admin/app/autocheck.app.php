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
        echo '购买vip:'.$this->_vip().'<br>';
        echo '推荐他人:'.$this->_tuijian().'<br>';
        echo '销售返佣:'.$this->_xiaoshou().'<br>';
        echo '消费返现:'.$this->_xiaofei().'<br>;';
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
     var $mod_epay2;
     var $mod_epay;

    var $mod_epaylog;
    var $mod_order;
    var $mod_integral_log;
    function __construct()
    {
        $this->AutocheckApp();
    }

    function AutocheckApp()
    {
        parent::BackendApp();

        header("Content-Type: text/html;charset=utf-8");
        $this->mod_member = &m('member');
        $this->mod_epay2 = &m('epay2');
        $this->mod_epay  = &m('epay');
        $this->mod_order = &m('order');
        $this->mod_epaylog = &m('epaylog');
        $this->mod_integral_log = &m('integral_log');
    }

    function info()
    {
        $user_name = $_GET['user_name'];
        $white = new white($user_name,gmtime());
        echo "用户:".$user_name."---全部白积分:".$white->total_white."---消耗白积分:".$white->used_white."---白积分资金差:".($white->total_white-$white->used_white-$member['integral'])."<br>";


        echo "--------资金流入-----<br>";
        //管理员充值
        echo "管理员充值:".$this->mod_epaylog->getOne("select sum(money) from ecm_epaylog where user_name='{$user_name}' and complete=1 AND type=10")."<br>";
        //其他账户转入
        echo "转入资金:".$this->mod_epaylog->getOne("select sum(money) from ecm_epaylog where user_name='{$user_name}' and complete=1 AND type=40")."<br>";
        //系统奖励
        echo "系统奖励:".$this->mod_epaylog->getOne("select sum(used_white) from ecm_epay WHERE user_name='{$user_name}'")/100*0.95.'<br>';
        echo "--------资金收入-----<br>";
        //资金转出
        echo "转出资金:".$this->mod_epaylog->getOne("select sum(money) from ecm_epaylog where user_name='{$user_name}' and complete=1 AND type=50")."<br>";
        //已提现
        echo "已提现:".$this->mod_epaylog->getOne("select sum(money) from ecm_epaylog where user_name='{$user_name}' and complete=1 AND type=70 AND ecm_epaylog.states=71")."<br>";
        //未体现
        echo "账户余额:".$this->mod_epay->getOne("select money from ecm_epay WHERE user_name='{$user_name}'").'<br>';
        echo "冻结资金:".$this->mod_epay->getOne("select money_dj from ecm_epay WHERE user_name='{$user_name}'").'<br>';
        //做单
        echo "做单资金:". $this->mod_order->getOne("select sum(order_amount) from ecm_order WHERE status=40 and seller_name = '{$user_name}'")/10;
    }



    function migrate(){
        $epay2 = $this->mod_epay2->findAll();

        foreach ($epay2 as $k => $v) {
            echo $v['id'].'<br>';
            if ($v['id']<4300)
            {
                continue;
            }
//            $info = $this->mod_epay->get('user_id='.$v['user_id']);
//            var_dump($info);
            $this->mod_epay->edit('user_id='.$v['user_id'],array(
                'total_white'=> $v['total_white'],
                'used_white' => $v['used_white'],
            ));
        }
    }


    //循环处理所有账户
    function loop()
    {
        $members = $this->mod_member->findAll();
        $members2 = $this->mod_epay2->findAll();


        echo "共" . count($members) . "个账户<br>";
        echo "处理".count($members2)."个账户<br>";

        foreach ($members as $k => $v) {

            $user_name = $v['user_name'];
            $epay2 = $this->mod_epay2->get(array(
                'conditions'=>"user_name='{$user_name}'",
            ));

            if (empty($epay2)){
                $this->parse($v);
            }
        }
    }

    //针对某一个账户进行处理
    function parse($member)
    {
        $white = new white($member['user_name'],gmtime());

        $user_name = $member['user_name'];
        $epay2 = $this->mod_epay2->get(array(
            'conditions'=>"user_name='{$user_name}'",
        ));

        if (empty($epay2)){
            $data = array(
              'user_id' => $member['user_id'],
                'user_name'=>$member['user_name'],
                'total_white'=>$white->total_white,
                'used_white'=>$white->used_white,
                'add_time'=>gmtime(),
            );

            $this->mod_epay2->add($data);
        }

        echo "用户:".$member['user_name']."---全部白积分:".$white->total_white."---消耗白积分:".$white->used_white."---白积分资金差:".($white->total_white-$white->used_white-$member['integral'])."<br>";
    }




}
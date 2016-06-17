<?php
/**
 * Created by PhpStorm.
 * User: Dong
 * Date: 16/6/12
 * Time: 下午11:19
 */

class red{
    var $user_name;
    
    var $total_red;
    
    function __construct($user_name)
    {
        $this->user_name = $user_name;
    }

    /**
     * @param mixed $total_red
     */
    public function TotalRed($total_red)
    {
        $this->total_red = $total_red;
    } 
}

//白积分的运算类
class white{
    var $mod_integral_log;
    var $mod_order;

    var $all_white;

    var $user_name;

    var $calc_time;     //计算的时间点
    var $vip_time;      //成为VIP的时间点

    function __construct($user_name, $calc_time)
    {
        $this->user_name = $user_name;
        $this->calc_time = $calc_time;

        $this->mod_integral_log = &m('integral_log');
        $this->mod_order = &m('order');

        //给VIP时间赋值,便于运算
        $this->_vip_time();


        $this->all_white = $this->_tuijian()+$this->_xiaofei()+$this->_xiaoshou();
    }

    /**
     * @return mixed
     */public function getAllWhite()
    {
        return $this->all_white;
    }

    function _vip_time(){
        $remark = '购买会员获得100000白积分';
        $vip = $this->mod_integral_log->get(array(
            'conditions' => "remark = '{$remark}' and user_name='{$this->user_name}'",
        ));

        $this->vip_time = empty($vip) ? 0 : $vip['add_time'];
    }
    //推荐白积分-从日志记录提取
    function _tuijian(){
        //100->推荐者购买
        return $this->mod_integral_log->getOne("select sum(point) from ecm_integral_log WHERE user_name='{$this->user_name}' and add_time<{$this->calc_time} and integral_type = 100");
    }

    //销售得到的白积分
    function _xiaoshou(){
        if (!$this->vip_time){
            return 0;
        }

        $xiaoshou_amount = $this->mod_order->getOne("select sum(order_amount) from ecm_order WHERE status=40 and seller_name = '{$this->user_name}' and add_time>={$this->vip_time} and add_time<{$this->calc_time}");
        return $xiaoshou_amount / 10 * 100;
    }

    //从订单提取包含购买VIP获得的白积分
    function _xiaofei(){
        $xiaofei_amount = $this->mod_order->getOne("select sum(order_amount) from ecm_order WHERE status=40 and buyer_name = '{$this->user_name}' and add_time<{$this->calc_time}");
        return $xiaofei_amount * 100;
    }
}


class handle{
    var $total_white;
    var $used_white;
    var $total_red;


    var $user_name;
    var $calc_time;


    function __construct($user_name,$calc_time)
    {
        $this->user_name = $user_name;
        $this->calc_time = $calc_time;
    }
    
}


class AutocheckApp extends BackendApp
{

    var $mod_order;
    var $mod_integral_log;
    var $mod_operate_log;
    var $mod_member;

    var $user_name;

    var $all_white;
    var $sub_white;
    var $sum_red;

    var $isVip;      //是否是VIP
    var $vipTime;    //成为VIP的时间

    function __construct()
    {
        $this->AutocheckApp();
    }

    function AutocheckApp()
    {
        parent::BackendApp();

        header("Content-Type: text/html;charset=utf-8");

        $this->mod_order = &m('order');
        $this->mod_integral_log = &m('integral_log');
        $this->mod_operate_log = &m('operate_log');
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

        echo "用户:".$member['user_name']."---白积分:".$white->all_white."---实际白积分:".$member['integral']."<br>";
    }




    //某个账户所有的白积分
    function get_all_white($member)
    {
        return $this->mod_integral_log->getOne("select sum(point) from ecm_integral_log WHERE user_name='{$member['user_name']}' and integral_type in ('4','5','11','100')");
    }


    function _change($operate)
    {
        $end = $operate['add_time'];
        $this->all_white = $this->_all_white(0, $end);

        //今天得到的红积分

        echo "时间:" . local_date('Y-m-d', $operate['add_time']) . "  返利前<br>";

        echo "全部白积分:" . $this->all_white . "<br>";
        echo "剩余白积分:" . ($this->all_white - $this->sub_white) . "<br>";
        echo "全部红积分:" . $this->sum_red . "<br>";

        $power = floor($this->all_white / 100000) - floor($this->sub_white / 100000);
        $red = round($power * $operate['power_rate'], 2);
        echo "积分赠送权:" . floor($power) . "<br>";

        echo "新增红积分:" . $red . "<br>";
        echo "减去白积分:" . floor($red * 100) . "<br>";

        $this->sub_white += $red * 100;
        $this->sum_red += $red;
        echo "------------------<br>";
    }


    //判断成为VIP的时间
    function _vipTime()
    {
        $remark = '购买会员获得100000白积分';
        $vip = $this->mod_integral_log->get(array(
            'conditions' => "remark = '{$remark}' and user_name=" . $this->user_name,
        ));

        $this->vipTime = empty($vip) ? 0 : $vip['add_time'];
    }

    function index()
    {
        $this->user_name = $_GET['user_name'];
        $total_xiaoshou = $this->mod_order->getOne("select sum(order_amount) from ecm_order WHERE status=40 and seller_name = {$this->user_name}");
        echo "销售额:" . $total_xiaoshou . '元<br>';

        $white_xiaoshou = $this->mod_integral_log->getOne("select sum(point) from ecm_integral_log WHERE user_name = {$this->user_name}");
        echo "销售白积分:" . $white_xiaoshou . '<br>';
        // return;


        //每日运营数据表
        $operates = $this->mod_operate_log->findAll();

        //判断是否是vip
        //如果是VIP则计算成为VIP的时间戳
        $this->vipTime = $this->_vipTime();

        echo '成为VIP的时间:' . local_date('Y-m-d', $this->vipTime) . '<br>';
        //开始计算的时间
        echo "首次获得白积分的时间:" . local_date('Y-m-d', $this->_FirstGetWhiteIntegralTime()) . "<br>";

        $theTime = $this->_FirstGetWhiteIntegralTime();
        foreach ($operates as $k => $v) {
            $zeroTime = strtotime(date('Y-m-d', $operates[$k]['add_time']));
            if ($theTime < $operates[$k]['add_time'] and $theTime >= $zeroTime) {

                //某天的积分变动情况
                $last = $this->_change($operates[$k]);

                $theTime = $operates[$k]['add_time'] + 18 * 3600;
            }
        }


        //今天的积分情况
        $end_operate = end($operates);


        $end = gmtime();
        $this->all_white = $this->_all_white(0, $end);

        //今天得到的红积分

        echo "时间:" . local_date('Y-m-d', gmtime()) . "  返利前<br>";

        echo "全部白积分:" . $this->all_white . "<br>";
        echo "剩余白积分:" . ($this->all_white - $this->sub_white) . "<br>";
        echo "全部红积分:" . $this->sum_red . "<br>";

        $power = floor($this->all_white / 100000) - floor($this->sub_white / 100000);
        echo "积分赠送权:" . floor($power) . "<br>";
    }

    function _all_white($begin, $end)
    {
        //100->推荐者购买
        //11->购买会员
        //4->购买赠送
        //5->销售赠送

        return $this->mod_integral_log->getOne("select sum(point) from ecm_integral_log WHERE user_name={$this->user_name} and add_time>={$begin} and add_time<{$end} and integral_type in ('4','5','11','100')");
//        $tuijian = $this->mod_integral_log->getOne("select sum(point) from ecm_integral_log WHERE user_name={$this->user_name} and add_time>={$begin} and add_time<{$end} and integral_type=100");
//
//        $xiaofei = $this->_xiaofeiWhite($begin,$end);
//
//        $xiaoshou = 0;
//
//        if ($this->vipTime > $begin){
//            $begin = $begin>$this->vipTime;
//            $xiaoshou = $this->_xiaoshou($begin,$end);
//        }
//
//        $total_white = $tuijian+$xiaofei+$xiaoshou;
//        return $total_white;
    }

    //销售额
    function _xiaoshou($begin, $end)
    {
        $total_amount = $this->mod_order->getOne("select sum(order_amount) from ecm_order WHERE status=40 and seller_name = {$this->user_name} and add_time>={$begin} and add_time<{$end}");
        return $total_amount / 10 * 100;
    }


    //消费额
    function _xiaofeiWhite($begin, $end)
    {
        $total_amount = $this->mod_order->getOne("select sum(order_amount) from ecm_order WHERE status=40 and buyer_name = {$this->user_name} and add_time>={$begin} and add_time<{$end}");
        return $total_amount * 100;
    }


    //白积分转积分赠送权
    function power($white)
    {
        return floor($white / 100000);
    }

    //首次获得白积分的时间
    function _FirstGetWhiteIntegralTime()
    {
        $log = $this->mod_integral_log->get(array(
            'conditions' => 'user_name=' . $this->user_name,
            'order' => 'add_time asc',
            'limit' => 1,
        ));

        if (!empty($log)) {
            return $firsttime = $log['add_time'];
        }

        return false;
    }
}
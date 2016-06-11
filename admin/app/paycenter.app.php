<?php
/**
 * Created by PhpStorm.
 * User: xiaokang
 * Date: 2016/5/3
 * Time: 22:55
 */


/**
 *    财富中心
 *
 * @author    Garbin
 * @usage    none
 */
class PaycenterApp extends BackendApp
{
    var $_member_mod;
    var $_epay_mod;
    var $_operate_log_mod;
    var $_operate_change_log_mod;
    var $mobile_msg;


    function __construct()
    {
        $this->PaycenterApp();
    }

    function PaycenterApp()
    {
        parent::BackendApp();

        $this->_member_mod = &m('member');
        $this->_epay_mod = &m('epay');
        $this->_operate_log_mod = &m('operate_log');
        $this->_operate_change_log_mod = &m('operate_change_log');
        import('mobile_msg.lib');
        $this->mobile_msg = new Mobile_msg();
    }

    function index()
    {
        $page = $this->_get_page(20);
        $epay_members = $this->_epay_mod->find(array(
            'conditions' => 'integral_power>=100000',
            'count' => true,
            'limit' => $page['limit'],
            'order' => 'integral_power DESC',
        ));


        foreach ($epay_members as $key => $member) {
            $epay_members[$key]['integral_power'] = intval($epay_members[$key]['integral_power'] / 100000);
        }

        $page['item_count'] = $this->_epay_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);


        $allmoneys = $this->_get_today_moneys();
        $allyongjins = $allmoneys / 10;


        $this->assign('allmoney', $allmoneys);
        $this->assign('yongjins', $allyongjins);
        $this->assign('integral_power_count', $this->_get_integral_power_count());
        $this->assign('members', $epay_members);
        $this->display('paycenter/index.html');
    }

    /**
     *
     */
    function findusers()
    {
        return;
        $epay_used_members30 = $this->_epay_mod->getAll("SELECT *  FROM `ecm_operate_change_log` WHERE `operate_id` =30 and Id>12094");
        echo sizeof($epay_used_members30);

        foreach ($epay_used_members30 as $key => $epay30) {
            echo '<br>';
            //手动拒绝提现
            $this->step1($epay30);
            $id = $epay30['Id'];
            $this->_operate_change_log_mod->drop("$id");

//            $integral_log_mod = &m('integral_log');
//            $log = $integral_log_mod->find("user_name=".$epay30['user_name']." add_time desc");
//            var_dump($log);
        }

        return;
    }

    function step1($epay30){
        return;
        //查找
        $user_name = $epay30['user_name'];
        $epay31 = $this->_epay_mod->getAll("select * from ecm_operate_change_log WHERE operate_id=31 and user_name = $user_name");

        $zhanghu = $this->_epay_mod->get("user_name=$user_name");
        echo "用户名:".$zhanghu['user_name']."冻结资金:".$zhanghu['money_dj']."---账户余额:".$zhanghu['money']."---税费:".$zhanghu['money_tax']."---红积分:".$zhanghu['integral_red']."---积分赠送权:".$zhanghu['integral_power'];

        if ($epay30['pre_integral_white'] > $epay31[0]['pre_integral_white'])
        {
            $epay_pre = $epay30;
            $epay_last = $epay31[0];
        }else{
            $epay_pre = $epay31[0];
            $epay_last = $epay30;
        }


        $xxx = $this->_epay_mod->edit($zhanghu['id'],array(
            'money'=>'0',
            'money_tax'=>'0',
            'integral_red'=>$epay_pre['change_integral_red'],
            'integral_power'=>$epay_pre['pre_integral_power'],
        ));


        $yyy = $this->_member_mod->edit('user_name='.$user_name,array(
            'integral'=>$epay_last['pre_integral_white'],
            'total_integral'=>$epay_last['pre_integral_white'],
        ));

    }


    function todayfanli()
    {
        echo '2016年06月11日18:02:25';
        return;

        //金币汇率

        $power_rate = 1.4;

        $epay_members = $this->_epay_mod->find(array(
            // and user_name  not in  ('18265180291','13305393569','13475391355')
            'conditions' => "integral_power>=100000",
           // 'conditions' => "integral_power>=100000 and integral_power<11000000",
            'count' => true,
            'order' => 'integral_power DESC',
        ));

        $epay_used_members = $this->_epay_mod->getAll("SELECT *  FROM `ecm_operate_change_log` WHERE `operate_id` =  38");

        foreach($epay_members as $key=>$epay){
            foreach($epay_used_members as $key2=>$epay2)
            {
                if($epay['user_name']==$epay2['user_name']){
                    unset($epay_members[$key]);
                }
            }

        }

//        echo '------';
//        echo var_export($epay_members);
//
//        return;
        //今日操作日志
        foreach ($epay_members as $key => $epay) {
            $this->_member_fanli($epay, $power_rate, 38);
        }

        echo '今日奖励工作已全部完成';
        return;
    }

    /**
     * 作用:通过汇率进行分配
     * Created by QQ:710932
     */
    function fanli_ratio(){
        $power_rate = $_GET['ratio'];

        if (!is_numeric($power_rate) or $power_rate <= 0) {
            $this->show_warning('请输入合法汇率');
            return;
        }


        $power_count = $this->_get_integral_power_count();

        $fanli_money = $power_count*$power_rate;


        $epay_members = $this->_epay_mod->find(array(
            'conditions' => 'integral_power>=100000',
            'count' => true,
            'order' => 'integral_power DESC',
        ));


        //今日操作日志
        $operate_id = $this->_operate_log_mod->add(array(
            'money' => $fanli_money,
            'yongjin' => $this->_get_today_moneys() / 10,
            'integral_power' => $power_count,
            'power_rate' => $power_rate,
            'add_time' => gmtime(),
        ));

        if (empty($operate_id)) {
            $this->show_warning('操作失败，请联系网络管理员');
            return;
        }

        foreach ($epay_members as $key => $epay) {
            $this->_member_fanli($epay, $power_rate, $operate_id);
        }

        $this->show_message('今日奖励工作已全部完成');
        return;
    }

    /**
     * 作用:通过返的总金额进行分配
     * Created by QQ:710932
     */
    function fanli()
    {
        $power_count = $this->_get_integral_power_count();

        $fanli_money = $_GET['fanli_money'];
        if (!is_numeric($fanli_money) or $fanli_money <= 0) {
            $this->show_warning('请输入合法返利金额');
            return;
        }

        //金币汇率
        $power_rate = $fanli_money / $power_count;

        $epay_members = $this->_epay_mod->find(array(
            'conditions' => 'integral_power>=100000',
            'count' => true,
            'order' => 'integral_power DESC',
        ));


        //今日操作日志
        $operate_id = $this->_operate_log_mod->add(array(
            'money' => $fanli_money,        //奖励资金
            'yongjin' => $this->_get_today_moneys() / 10,       //平台收取佣金
            'integral_power' => $power_count,
            'power_rate' => $power_rate,
            'add_time' => gmtime(),
        ));

        if (empty($operate_id)) {
            $this->show_warning('操作失败，请联系网络管理员');
            return;
        }

        foreach ($epay_members as $key => $epay) {
            $this->_member_fanli($epay, $power_rate, $operate_id);
        }

        $this->show_message('今日奖励工作已全部完成');
        return;
    }


    //给每个会员分配资金
    function _member_fanli($epay, $power_rate, $operate_id)
    {

        $member = $this->_member_mod->get(array(
            'conditions' => "user_id=" . $epay['user_id'],
        ));

        //积分赠送权的实际数量
        $power = intval($epay['integral_power'] / 100000);


        $red = floor($power * $power_rate * 100) / 100;

        //红积分增加
        $per_integral_red = $epay['integral_red'];
        $epay['integral_red'] = $per_integral_red + $red;

        //白积分减少
        $pre_integral_white = $member['integral'];
        $member['integral'] = $pre_integral_white - $red * 100;
        $member['total_integral'] = $member['total_integral'] - $red * 100;
        $this->_member_mod->edit($epay['user_id'], $member);


        //白积分减少记录
        //操作记录入积分记录
        $integral_log_mod = &m('integral_log');
        $integral_log = array(
            'user_id' => $member['user_id'],
            'user_name' => $member['user_name'],
            'point' => $red * 100,
            'add_time' => gmtime(),
            'remark' => '当天奖励扣除白积分' . $red * 100,
            'integral_type' => INTEGRAL_FANLI,
        );
        $integral_log_mod->add($integral_log);

        //积分赠送权减少
        $change_integral_power = intval($epay['integral_red'] / 1000) - intval($per_integral_red / 1000);
        $new_integral_power = $epay['integral_power'] - $change_integral_power * 100000;
        $epay['integral_power'] = $new_integral_power > 0 ? $new_integral_power : 0;
        $this->_epay_mod->edit($epay['id'], $epay);

        //写入日志表
        $operate_change_log = array(
            'operate_id' => $operate_id,
            'user_id' => $epay['user_id'],
            'user_name' => $epay['user_name'],
            'change_integral_white' => $red * 100,  //减少
            'change_integral_red' => $red,   //增加
            'change_integral_power' => $change_integral_power,  //减少
            'pre_integral_white' => $pre_integral_white,
            'pre_integral_red' => $epay['integral_red'],
            'pre_integral_power' => $epay['integral_power'],
        );

        $operate_change_log_mod = &m('operate_change_log');
        $operate_change_log_mod->add($operate_change_log);

        //todo 短信提醒
        $msgtext = '今日赠送的红积分数量为：' . $red . '，请登录平台查看！';
        $to_mobile = trim($member['phone_mob']);
        if ($this->mobile_msg->isMobile($to_mobile)) {
            $this->mobile_msg->send_msg(0, 'admin', $to_mobile, $msgtext);
        }
    }

    /**
     * 作用:平台今日流水（距离上次返利时间截至）
     * Created by QQ:710932
     */
    function _get_today_moneys()
    {
        $now_time = gmtime();
        $end_time = $now_time;
        $lastFanli = $this->_get_last_operate();

        if ($lastFanli == false) {
            $begin_time = 0;
        } else {
            $begin_time = $lastFanli['add_time'];
        }

        //begin_time到end_time这个时间段内的订单流水
        $order_mod = &m('order');
        $totalAmount = $order_mod->getOne("select sum(goods_amount) from " . DB_PREFIX . "order where status=40 and finished_time>" . $begin_time . " and finished_time<=" . $end_time);
        return $totalAmount == null ? 0 : $totalAmount;
    }

    /**
     * @return mixed
     * 作用:平台上所有积分赠送权的集合
     * Created by QQ:710932
     */
    function _get_integral_power_count()
    {
        //  var $integral_power;

        $epay_members = $this->_epay_mod->find(array(
            'conditions' => 'integral_power>100000',
            'count' => true,
            'order' => 'integral_power DESC',
        ));

        $integral_power = 0;
        foreach ($epay_members as $key => $member) {
            $integral_power = $integral_power + intval($epay_members[$key]['integral_power'] / 100000);
        }

        return $integral_power;
    }

    /**
     * 作用: 获得最后一次运营数据
     * Created by QQ:710932
     */
    function _get_last_operate()
    {
        $operate = $this->_operate_log_mod->get(array(
            'order' => 'add_time DESC',
        ));

        return $operate;
    }


    /**
     * 作用:运营历史数据
     * Created by QQ:710932
     */
    function history(){
        $page = $this->_get_page(20);

        $operate_log = $this->_operate_log_mod->find(array(
            'count' => true,
            'limit' => $page['limit'],
            'order' => 'add_time DESC',
        ));


        $page['item_count'] = $this->_operate_log_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);

        $this->assign('operates',$operate_log);

        $this->display('paycenter/operate_history.html');
    }
}

?>

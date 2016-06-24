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
            'conditions' => "(total_white)>=100000",
             'count' => true,
            'limit' => $page['limit'],
            'order' => '(total_white) DESC',
        ));


        foreach ($epay_members as $key => $member) {
            $epay_members[$key]['integral_power'] = floor($epay_members[$key]['total_white'] / 100000)-floor($epay_members[$key]['used_white'] / 100000);
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




    function todayfanli()
    {
//        echo '2016年06月15日18:07:24';
//        return;

        $operate = $this->_get_last_operate();
        
        //金币汇率

        $power_rate = $operate['power_rate'];
        $operate_id = $operate['id'];


        $epay_members = $this->_epay_mod->find(array(
            'conditions' => "total_white>=100000",
            'count' => true,
            'order' => 'total_white DESC',
        ));

        $epay_used_members = $this->_epay_mod->getAll("SELECT *  FROM `ecm_operate_change_log` WHERE `operate_id` =  {$operate_id}");



        foreach($epay_members as $key=>$epay){
            foreach($epay_used_members as $key2=>$epay2)
            {
                if($epay['user_name']==$epay2['user_name']){
                    unset($epay_members[$key]);
                }
            }
        }


        //今日操作日志
        foreach ($epay_members as $key => $epay) {
            $this->_member_fanli($epay, $power_rate, $operate_id);
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
            'conditions' => 'total_white>=100000',
            'count' => true,
            'order' => 'total_white DESC',
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


    //给每个会员分配资金
    function _member_fanli($epay, $power_rate, $operate_id)
    {

        $member = $this->_member_mod->get(array(
            'conditions' => "user_id=" . $epay['user_id'],
        ));

        //积分赠送权的实际数量
        $power = floor($epay['total_white']/100000) - floor($epay['used_white']/100000);


        $red = floor($power * $power_rate * 100) / 100;


        //红积分增加
        $per_integral_red = $epay['integral_red'];
        $epay['integral_red'] = $per_integral_red + $red;

        //消耗白积分增加
        $epay['used_white'] = $epay['used_white'] + $red * 100;
        $this->_epay_mod->edit('user_id='.$epay['user_id'], $epay);


        //白积分减少记录
        //操作记录入积分记录
        $integral_log_mod = &m('integral_log');
        $integral_log = array(
            'user_id' => $member['user_id'],
            'user_name' => $member['user_name'],
            'point' => $red * 100,
            'add_time' => gmtime(),
            'remark' => '今日奖励消耗白积分:'.($red*100).'历史消耗白积分:'.$epay['used_white'],
            'integral_type' => INTEGRAL_FANLI,
        );
        $integral_log_mod->add($integral_log);

        //写入日志表
        $operate_change_log = array(
            'operate_id' => $operate_id,
            'user_id' => $epay['user_id'],
            'user_name' => $epay['user_name'],
            'change_integral_white' => $red * 100,  //减少
            'change_integral_red' => $red,   //增加
        );

        $operate_change_log_mod = &m('operate_change_log');
        $operate_change_log_mod->add($operate_change_log);


        //todo 短信提醒
//        $msgtext = '今日赠送的红积分数量为：' . $red . '，请登录平台查看！';
//        $to_mobile = trim($member['phone_mob']);
//        if ($this->mobile_msg->isMobile($to_mobile)) {
//            $this->mobile_msg->send_msg(0, 'zengsong', $to_mobile, $msgtext);
//        }
    }

    function sendmsg(){
        $mod_member = &m('member');
        $mod_operate_log = &m('operate_change_log');
        
        $operate = $this->_get_last_operate();

        //金币汇率
        $operate_id = $operate['id'];

        $members = $this->_operate_change_log_mod->getAll("select * from ecm_operate_change_log WHERE operate_id={$operate_id}");

        $mod_msglog = &m("msglog");

        $msg_members = $mod_msglog->getAll("select * from ecm_msglog WHERE user_id={$operate_id}");

        foreach ($members as $key=>$user){
            foreach ($msg_members as $k2=>$v2)
            {
                if ($user['user_name'] == $v2['user_name'])
                {
                    unset($members[$key]);
                }
            }
        }

        echo "全部".sizeof($members);
        echo "完成".sizeof($msg_members);

        foreach ($members as $k =>$v){

            $red = floor($v['change_integral_white']*100)/10000;

            $msgtext = '今日赠送的红积分数量为：' . $red . '，请登录平台查看！';

            $phone_mob = $mod_member->getOne("select phone_mob from ecm_member WHERE user_name = '{$v['user_name']}'");

            $to_mobile = trim($phone_mob);


            if ($this->mobile_msg->isMobile($to_mobile)) {
                $this->mobile_msg->send_msg($operate_id, $v['user_name'], $to_mobile, $msgtext);
            }
        }

        echo 'ok';
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
            'conditions' => "('total_white'-'used_white')>100000",
            'count' => true,
            'order' => 'integral_power DESC',
        ));

        $integral_power = 0;
        foreach ($epay_members as $key => $member) {
            $integral_power = $integral_power + (floor($member['total_white'] / 100000)-floor($member['used_white'] / 100000));
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

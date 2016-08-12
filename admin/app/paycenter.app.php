<?php
/**
 * Created by PhpStorm.
 * User: xiaokang
 * Date: 2016/5/3
 * Time: 22:55
 */
//重要sql语句
//当日返还是否有重复账户
//select `user_name` ,count(`user_name`) FROM `ecm_operate_change_log` WHERE  `operate_id` = 50 group by `user_name`  having count(`user_name`)> 1

//重复短信
//select `user_name` ,count(`user_name`) FROM `ecm_msglog` WHERE  `user_id` = 55 group by `user_name`  having count(`user_name`)> 1

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
        ini_set("max_execution_time", "0");
        set_time_limit(0);

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
        $operate = $this->_get_last_operate();

        //金币汇率

        $power_rate = $operate['power_rate'];
        $operate_id = $operate['id'];


        $epay_members = $this->_epay_mod->find(array(
            'conditions' => "total_white>=100000",
            'count' => true,
            'order' => 'total_white DESC',
        ));

        $epay_unused_members = $this->_epay_mod->getALL("SELECT * FROM `ecm_epay` WHERE ( `total_white` >= 100000) and `user_name` NOT IN (select `user_name`  FROM `ecm_operate_change_log` WHERE  `operate_id` = {$operate_id} )");

        $epay_used_count = $this->_epay_mod->getOne("select count(*)  FROM `ecm_operate_change_log` WHERE  `operate_id` = {$operate_id}");

        echo "账户数量:".sizeof($epay_members)."个<br>";
        echo "未处理量:".sizeof($epay_unused_members)."个<br>";
        echo "已处理量:".$epay_used_count."个<br>";

        //今日操作日志
        foreach ($epay_unused_members as $key => $epay) {
            $this->_member_fanli($epay, $power_rate, $operate_id);
        }

        echo "本次操作结束<br>";
        return;
    }

    /**
     * 作用:通过汇率进行分配
     * Created by QQ:710932
     */
    function fanli_ratio(){
        
        //todo 检查今天是否已经提交
        
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

        $msg_used_members = $mod_msglog->getAll("select * from ecm_msglog WHERE user_id={$operate_id}");

        echo "共需要给".sizeof($members)."个账户发送短信提醒<br>";
        echo "已发送".sizeof($msg_used_members)."人<br>";

        $msg_unused_members = $mod_msglog->getAll("SELECT * FROM `ecm_operate_change_log` WHERE `operate_id` = {$operate_id} and `user_name` NOT IN (select  `user_name`  FROM `ecm_msglog` WHERE  `user_id` = {$operate_id})");

        echo "待发送".sizeof($msg_unused_members)."人<br>";


        foreach ($msg_unused_members as $k =>$v){

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


    /*********************************************************/
    function callmsg(){
        set_time_limit(0);

        $page = empty($_GET['page']) ? 1 : intval($_GET['page']); // 当前页

        //每页处理账户个数
        $page_per = empty($_GET['pagesize']) ? 20 : intval($_GET['pagesize']); // 每页送数量

        $total_count = empty($_GET['total']) ? 0 : intval($_GET['total']);
        if (!$total_count){
            //计算全部数量
            $total_count = $this->_epay_mod->getOne("select count(*) from ecm_epay WHERE total_white>=100000");
        }

        //计算页码数
        $totalpage = ceil($total_count / $page_per);

        $start = ($page -1) * $page_per;

        $epay_members = $this->_epay_mod->find(array(
            'conditions'    => '1=1 ' . 'and total_white>=100000',
            'limit'         => "{$start},{$page_per}",  //获取当前页的数据
            'order'         => "total_white DESC",
            'count'         => true             //允许统计
        ));



        foreach ($epay_members as $k => $member){
            echo $member['user_name'].'<br>';

          //  $this->_member_fanli($member, 1.2, 888);

        }

        if($page < $totalpage) {
            $tip = "共{$totalpage}页,已完成{$page}页面,进行中，请稍后！";

            $page = $page + 1;
            $nurl = "index.php?app=paycenter&act=callmsg&total=$total_count&page=$page&page_per=$page_per";
            $this->ShowMsg($tip, $nurl, 0, 100);
        } else {
            $this->ShowMsg("完成所有短信任务！","javascript:;");
        }
    }




    function ShowMsg($msg, $gourl, $onlymsg=0, $limittime=0) {
        $htmlhead  = "<html>\r\n<head>\r\n<title>批量提示信息</title>\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\r\n";
        $htmlhead .= "<base target='_self'/>\r\n<style>div{line-height:160%;}</style></head>\r\n<body leftmargin='0' topmargin='0' bgcolor='#FFFFFF'>\r\n<center>\r\n<script>\r\n";
        $htmlfoot  = "</script>\r\n</center>\r\n</body>\r\n</html>\r\n";

        $litime = ($limittime==0 ? 1000 : $limittime);
        $func = '';

        if($gourl=='-1') {
            if($limittime==0) $litime = 5000;
            $gourl = "javascript:history.go(-1);";
        }

        if($gourl=='' || $onlymsg==1) {
            $msg = "<script>alert(\"".str_replace("\"","“",$msg)."\");</script>";
        } else {
            if(preg_match('/close::/',$gourl)) {
                $tgobj = trim(preg_replace('/close::/', '', $gourl));
                $gourl = 'javascript:;';
                $func .= "window.parent.document.getElementById('{$tgobj}').style.display='none';\r\n";
            }

            $func .= "      var pgo=0;
      function JumpUrl(){
        if(pgo==0){ location='$gourl'; pgo=1; }
      }\r\n";
            $rmsg = $func;
            $rmsg .= "document.write(\"<br /><div style='width:450px;padding:0px;border:1px solid #DADADA;'>";
            $rmsg .= "<div style='padding:6px;font-size:14px; color:#990033; border-bottom:1px solid #DADADA;background:#DBEEBD url({$GLOBALS['cfg_plus_dir']}/img/wbg.gif)';'><b>批量提示信息！</b></div>\");\r\n";
            $rmsg .= "document.write(\"<div style='height:130px;font-size:10pt;background:#F7FCFF'><br />\");\r\n";
            $rmsg .= "document.write(\"".str_replace("\"","“",$msg)."\");\r\n";
            $rmsg .= "document.write(\"";

            if($onlymsg==0) {
                if( $gourl != 'javascript:;' && $gourl != '') {
                    $rmsg .= "<br /><a href='{$gourl}'>如果你的浏览器没反应，请点击这里...</a>";
                    $rmsg .= "<br/></div>\");\r\n";
                    $rmsg .= "setTimeout('JumpUrl()',$litime);";
                } else {
                    $rmsg .= "<br/></div>\");\r\n";
                }
            } else {
                $rmsg .= "<br/><br/></div>\");\r\n";
            }
            $msg  = $htmlhead.$rmsg.$htmlfoot;
        }
        echo $msg;
    }
}

?>

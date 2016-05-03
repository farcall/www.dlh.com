<?php
/**
 * 会员升级
 * Created by PhpStorm.
 * User: xiaokang
 * Date: 2016/4/24
 * Time: 21:05
 */

require(ROOT_PATH . '/app/paycenterbase.app.php');

class VipgradeAPP extends PaycenterbaseApp{

    function __construct(){
        $this->VipgradeAPP();
    }


    function VipgradeAPP(){
        parent::__construct();
        $this->mod_epay = & m('epay');
    }
    function index(){
       $this->step1();
    }

    /**
     * 作用:显示VIP的功能
     * Created by QQ:710932
     */
    function step1(){
        $this->display('paycenter/vipgrade.html');
    }

    /**
     * 作用:
     * Created by QQ:710932
     */
    function step2(){
        $epay = $this->mod_epay->get_info($this->user['user_id']);
        $this->assign('epay',$epay);

        if(!IS_POST){
            //获取当前用户的推荐人
            $parent_refers = $this->member_mod->get($this->member['referid']);
            $this->assign('parent_refers', $parent_refers);

            $this->display('paycenter/vipgrade_step2.html');
            return;
        }

        if($this->member['vip'] != '0'){
            $this->show_warning("您已经是VIP会员！请不要重复提交");
            return;
        }

        //验证余额
        if($epay['money'] < 1000){
            $this->show_warning('余额不足，请先充值','立即充值','index.php?app=epay&act=czlist');
            return;
        }

        //验证支付密码
        $zf_pass = trim($_POST['zhifupass']);
        //如果未设置支付密码，则直接设置,已设置支付密码 需要验证原支付密码
        $md5zf_pass = md5($zf_pass);
        if ($epay['zf_pass'] != "") {
            if ($epay['zf_pass'] != $md5zf_pass) {
                $this->show_warning('cuowu_yuanzhifumimayanzhengshibai');
                return;
            }
        }
        else{
            //密码为空，请先设置密码
            $this->show_warning('请先设置支付密码','支付密码设置','index.php?app=epay&act=editpassword');
            return;
        }

        import('vip.lib');
        $Vip=new Vip();
        $Vip->setvip($this->user['user_id']);
        $this->show_message("恭喜您成为得来惠VIP会员");
        return;
    }



}
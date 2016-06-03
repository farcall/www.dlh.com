<?php
/**
 * Created by PhpStorm.
 * User: xiaokang
 * Date: 2016/4/25
 * Time: 0:16
 */

require(ROOT_PATH . '/app/member.app.php');

class PaycenterbaseApp extends MemberbaseApp
{
    function __construct()
    {
        $this->PaycenterbaseApp();

        $this->user = $this->visitor->get();
        $this->member_mod = &m('member');
        $this->member = $this->member_mod->get_info($this->user['user_id']);


        $this->user['portrait'] = portrait($this->user['user_id'], $this->member['portrait'], 'middle');
        $this->assign('user', $this->user);

        //是否是会员
        import('vip.lib');
        $Vip = new Vip();
        if ($Vip->isVip($this->user['user_id'])) {
            $this->assign('vip', 1);
        } else {
            $this->assign('vip', 0);
        }

        //是否是商家
        if (!$this->visitor->get('has_store')) {
            $this->assign('store', 0);
        } else {
            $this->assign('store', 1);
        }
    }

    function  PaycenterbaseApp()
    {
        parent::__construct();
    }
}
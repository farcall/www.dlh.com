<?php

/**
 *
 * 第三方登录
 * @author xiaozhuge
 *
 */
class TestApp extends MallbaseApp
{

    function __construct()
    {
        $this->TestApp();
    }

    function TestApp()
    {
        parent::__construct();
    }

    function index()
    {
        echo 'test';

        /*交易成功后,推荐者可以获得佣金  BEGIN*/
        import('tuijian.lib');
        $tuijian = new tuijian();

        $user_id = $this->visitor->get('user_id');
        $member_mod = &m('member');
        $member_data = $member_mod->get($user_id);

        $referid_member = $tuijian->get_referid_member($member_data);

        if (empty($referid_member)) {
            echo '没有推荐人';
            return;
        } else {
            var_dump($referid_member);
            return;
        }

        $this->show_message('OK');
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Dong
 * Date: 16/7/26
 * Time: 下午5:50
 */

class My_tuijiandailiApp extends StoreadminbaseApp
{
    var $_store_id;
    var $_store_mod;
    var $_proxy_mod;
    function __construct() {
        $this->My_tuijiandailiApp();
    }

    function My_tuijiandailiApp() {
        parent::__construct();
        $this->_store_id = intval($this->visitor->get('manage_store'));
        $this->_store_mod = & m('store');
        $this->_proxy_mod = & m('proxy');
    }

    function index() {
        $tmp_info = $this->_store_mod->get(array(
            'conditions' => $this->_store_id,
        ));

        if (IS_POST){
            $tuijiandaili = $_POST['tuijiandaili'];

            if (strlen($tuijiandaili)>=50){
                return '请输入合法名称';
            }


            $proxy = $this->_proxy_mod->find("user_name= '$tuijiandaili'");

            if (!$proxy){
                $this->show_warning('请输入正确的代理名称');
                return;
            }

            $this->_store_mod->edit($this->_store_id, array(
                'tuijiandaili'=>$tuijiandaili,
            ));

            $this->show_message('更新成功');
            return;
        }else{
                $this->assign('tuijiandaili',$tmp_info['tuijiandaili']);
                $this->display('my_tuijiandaili.index.html');
        }

    }
}
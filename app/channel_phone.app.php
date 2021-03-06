<?php
class Channel_phoneApp extends MallbaseApp {

    function index() {
        
        $this->assign('index', 1); // 标识当前页面是首页，用于设置导航状态

        $this->_config_seo(array(
            'title' => Lang::get('mall_index') . ' - ' . Conf::get('site_title'),
        ));
        $this->assign('page_description', Conf::get('site_description'));
        $this->assign('page_keywords', Conf::get('site_keywords'));
        
        $this->display('channel_phone.index.html');
    }
}

?>
                    
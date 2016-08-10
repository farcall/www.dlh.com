<?php

header("Content-type:text/html;charset=utf-8");

define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));

include(ROOT_PATH . '/eccore/ecmall.php');

/* 定义配置信息 */
ecm_define(ROOT_PATH . '/data/config.inc.php');
include(ROOT_PATH . '/eccore/model/model.base.php');
define('CHARSET', 'utf-8');

//$settings = include(ROOT_PATH . '/data/settings.inc.php');

//交易状态
$trade_status = trim($_POST['tranState']);

//logResult1('return', $_POST);

?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <?php
		if($trade_status == '02')
		{
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代码
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
            //商户订单号
       
		   // 商户订单号
    		$out_trade_no = trim($_POST['oriMerOrderId']);

//            echo "验证成功<br />";
            echo "<script>" . "window.location.href='".SITE_URL."/index.php?app=epay&act=tenpay_return_url&order_sn=".$out_trade_no."'; " . "</script>";

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
            //验证失败
            echo "验证失败";
        }
        ?>
        <title>金运通支付接口</title>
    </head>
    <body>
    </body>
</html>
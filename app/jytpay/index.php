<?php

header("Content-type:text/html;charset=utf-8");

define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));

include(ROOT_PATH . '/eccore/ecmall.php');

/* 定义配置信息 */
ecm_define(ROOT_PATH . '/data/config.inc.php');
include(ROOT_PATH . '/includes/libraries/time.lib.php');

$settings = include(ROOT_PATH . '/data/settings.inc.php');

include('lib/jytpay_notify.class.php');

$site_url  = SITE_URL;
$channel   = (intval($_POST['isWap']) == 1) ? '01' : '00';
$total_fee = $_POST['money'];
$subject   =  $_POST['user_name']." 支付 ".$_POST['money']." 元";

//构造要请求的参数数组，无需改动
$parameter = array(
		"tranCode" 		=> "TN1001",
		"version"		=> "1.0.0",
		"charset"		=> "utf-8",
		"uaType"		=> $channel, //支付客户端类型，00:PC端，01:手机端，目前只支持00
		"merchantId"	=> $settings['epay_jyt_merchantId'],
		"merOrderId" 	=> trim($_POST['dingdan']),
		"merTranTime"   => local_date('YmdHis', gmtime()),
		"merUserId"		=> intval($_POST['user_id']), // 商户系统支付用户ID
		"orderDesc"		=> $subject,
		"prodInfo" 		=> $subject,
		"prodDetailUrl" => "",
		"tranAmt"		=> $total_fee,
		"curType" 		=> "CNY",
		"payMode"		=> "00", // 支付方式，00:B2C，01:B2B
		"bankCode"		=> "",  // 如支付银行为工商银行，则此字段输入：01020000。如果为空则默认在金运通支付平台选择支付银行
		"bankCardType"	=> "", // 01纯借记卡, 02 信用卡,99:企业账户。支付模式为00，此处可选01,02；若支付模式为01，则此处只能是99
		"notifyUrl"		=> $site_url."/app/jytpay/notify_url.php",
		"backUrl"		=> $site_url."/app/jytpay/return_url.php",
		"validTime"		=> "",
		"reserve1"		=> "",
		"reserve2"		=> "",
		"signType"		=> "SHA256",
);

$JytPay = new JytpayNotify($settings);
$html_text = $JytPay->buildRequestForm($parameter, "POST", "submit");
echo $html_text;

		 
?>
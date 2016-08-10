<?php

class JytpayNotify {
	
	//var $_gateway = 'http://220.248.70.90:30080/JytNetpay/payment.do';
	//var $_query_url = 'http://220.248.70.90:30080/JytNetpay/payment-query.do';
	
	var $_gateway = 'https://www.jytpay.com:9510/JytNetpay/payment.do';
	var $_query_url = 'https://www.jytpay.com:9510/JytNetpay/payment-query.do';

	var $_config;

	function __construct($config){
		$this->_config = $config;
	}
    function JytpayNotify($config) {
    	$this->__construct($config);
    }
	
	function buildRequestForm($parameter, $method, $button_name) 
	{
		//待请求参数数组
		$parameter['sign'] = $this->_get_sign($parameter);
		
		$sHtml = "<form id='payform' name='payform' action='".$this->_gateway."' method='".$method."' style='display:none'>";
		foreach($parameter as $key => $val) {
			$sHtml.= '<input type="hidden" name="'.$key.'" value="'.$val.'"/>';
    	 }

		//submit按钮控件请不要含有name属性
		$sHtml = $sHtml."<input type='submit' value='".$button_name."'></form>";
		
		$sHtml = $sHtml."<script>document.forms['payform'].submit();</script>";

		return $sHtml;
	}
	
	function query($params = array())
	{
		$parameter = array(
			'tranCode'		=> 'TN2001',
			'version'		=> '1.2.0',
			'charset'		=> 'utf-8',
			'merchantId'	=> $this->_config['epay_jyt_merchantId'],
			'oriMerOrderId' => $params['oriMerOrderId'],
			'orderType'		=> '0',
			'signType'		=> 'SHA256',
		);
		
		$parameter['sign'] = $this->_get_sign($parameter);
		
		//logResult1('paramter_TN2001', $parameter);
		
		$result = $this->curl($this->_query_url, 'POST', $parameter);
		
		return json_decode($result, TRUE);			
	}

	/**
     *    获取签名字符串
     *
     *    @author    Garbin
     *    @param     array $params
     *    @return    string
     */
    function _get_sign($params)
    {		
        /* 去除不参与签名的数据 */
        unset($params['sign']);

        /* 排序 */
        ksort($params);
        reset($params);

        $sign  = '';
        foreach ($params AS $key => $value)
        {
			if($value != '') {
            	$sign  .= "{$key}={$value}&";
			}
        }
		
		//echo '原串：'.substr($sign, 0, -1) . $settings['epay_jyt_key'];
		
		$sign = hash('sha256', substr($sign, 0, -1) . $this->_config['epay_jyt_key']);
		
		//echo '<br/>加密后：'.$sign;exit;
        return $sign;
    }
	
	function checkSign($params = array())
	{ 
		$local_sign = $this->_get_sign($params);
		
		return ($local_sign == $params['sign']);
	}
	
	function curl($url, $method = 'GET', $post = array(), $cacert_url = '', $input_charset = '')
	{
		if (trim($input_charset) != '') {
			$url = $url."_input_charset=".$input_charset;
		}
		
		//初始化curl
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		
		if($cacert_url) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
			curl_setopt($ch, CURLOPT_CAINFO, $cacert_url);//证书地址
		}
		
		//设置超时
		//curl_setopt($ch, CURLOP_TIMEOUT, $this->curl_timeout);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		if(strtoupper($method) == 'POST'){
			curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		
		//运行curl，结果以jason形式返回
		$res = curl_exec($ch);
		//var_dump( curl_error($ch) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
		curl_close($ch);
		return $res;
	}

}
		 
?>
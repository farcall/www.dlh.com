<?php

define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));

//include_once "Snoopy.class.php";
include_once "ENC.class.php";
include_once "ArrayToXML.class.php";
		
class JytRNPayNotify {
	
	//var $_gateway = 'http://test1.jytpay.com:16080/JytRNPay/tranCenter/encXmlReq.do';
	var $_gateway = 'https://www.jytpay.com:9410/JytRNPay/tranCenter/encXmlReq.do';
	//var $_query_url = 'http://220.248.70.90:30080/JytNetpay/payment-query.do';

	var $_config;

	function __construct($config){
		$this->_config = $config;
	}
    function JytRNPayNotify($config) {
    	$this->__construct($config);
    }
	
	function buildRequestForm($tran_code = 'TN1001', $req_body = array()) 
	{
		$mer_pub_file = ROOT_PATH . '/app/jytrnpay/cert/mer_rsa_public.pem';                         // 商户RSA公钥
	  	$mer_pri_file = ROOT_PATH . '/app/jytrnpay/cert/mer_rsa_private.pem';                        // 商户RSA私钥
	  	$pay_pub_file = ROOT_PATH . '/app/jytrnpay/cert/pay_rsa_public.pem';                         // 平台RSA公钥
	  	$m = new ENC($pay_pub_file, $mer_pri_file);

	
	  	/* 1. 组织报文头  */
	  	$req_param[ 'merchant_id' ] = $this->_config['epay_jytrn_merchantId'];
	  	$req_param[ 'tran_type' ] 	= '01' ; 
	  	$req_param[ 'version' ] 	= '1.0.0' ; 
	  	$req_param[ 'tran_flowid' ] = $req_param['merchant_id'].date('YmdHis').rand(10000,99999); // 请根据商户系统自行定义订单号
	  	$req_param[ 'tran_date' ] 	= date ( 'Ymd' ); 
	 	$req_param[ 'tran_time' ] 	= date ( 'His' ); 
	
	  	/* 2. --- 请根据接口报文组织请求报文体 ，下面例子为身份认证交易请求报文体，请按照实际交易接口填充内容  */
	  	$req_param[ 'tran_code' ] 	= $tran_code;

	  	/* 3. 转换请求数组为xml格式  */	
	 	$data=array("head"=>$req_param,"body"=>$req_body);
	  	$xml_ori = ArrayToXML::toXml($data);
	  	/* 4. 组织POST字段  */	
	  	$req['merchant_id'] = $req_param['merchant_id'];


	 	$req['sign' ]  = $m->sign($xml_ori,'hex');
	  	$key = rand(pow(10,(8-1)), pow(10,8)-1);
	
	  	$req['key_enc'] = $m->encrypt($key,'hex');
	  	$req['xml_enc'] = $m->desEncrypt($xml_ori,$key);
	
	
	
	  	/* 5. post提交到支付平台 */
	  	//$snoopy = new Snoopy; 
	  	//$snoopy->submit( $this->_gateway, $req); 
	  
	  	/* 6. 正则表达式分解返回报文 */
	  	//preg_match('/^merchant_id=(.*)&xml_enc=(.*)&key_enc=(.*)&sign=(.*)$/', $snoopy->results, $matches );

		$result = ecm_curl( $this->_gateway , 'POST', $req);
		preg_match('/^merchant_id=(.*)&xml_enc=(.*)&key_enc=(.*)&sign=(.*)$/', $result, $matches );
	  	$xml_enc = $matches[2];
	  	$key_enc = $matches[3];
	  	$sign = $matches[4];


	  	/* 7. 解密并验签返回报文  */
	  	$key = $m->decrypt($key_enc,'hex');
	  	$xml = $m->desDecrypt($xml_enc,$key);


	  	if(!$m->verify($xml,$sign,'hex')) {
		  
		   	//echo "--- 验签失败!\n"; 
		   	return array('tran_state' => 10000, 'remark' => '支付失败，请重新发起');
	  	}
	  	else 
	  	{
		  	//echo $xml;
		  	//echo $xml;
		  	$ar = $m->simplest_xml_to_array($xml);
		  	return $ar;
		  
		  	//echo json_encode($ar['body']);
	  	}
	}
}
		 
?>
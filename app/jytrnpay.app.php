<?php
header("Content-type:text/html;charset=utf-8");

include(ROOT_PATH . '/app/jytrnpay/lib/jytrnpay_notify.class.php');

class JytrnpayApp extends ShoppingbaseApp {

	function index()
	{
		// 建表操作， 成功之后后，可以注释
		/*$sql = " CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "jytpay_card` (
	  		`id` int(255) NOT NULL AUTO_INCREMENT,
	  		`user_id` int(11) NOT NULL,
	  		`bank_code` varchar(20) NOT NULL,
	  		`bank_card_no`  varchar(50) NOT NULL,
			`phone_no`  varchar(50) NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE = MYISAM DEFAULT CHARSET=" . str_replace('-', '', CHARSET) . ";";
    	db()->query($sql);
		*/

		// 订单信息
		$order = array('order_id' => $_GET['order_id'],'money' => $_GET['money']);

		// 获取已绑卡信息
		$parameter = array(
			"cust_no"   	=> $this->visitor->get('user_id'),
		);

		$settings = include(ROOT_PATH . '/data/settings.inc.php');


		$JytRNPay = new JytRNPayNotify($settings);
		$result = $JytRNPay->buildRequestForm("TD2002", $parameter);
		
		if($result['head']['resp_code'] == 'S0000000') {

			if($result['body']['card_num'] == 0)
			{
				header("location:index.php?app=jytrnpay&act=firstpay&order_id={$order['order_id']}&money={$order['money']}");
				exit;
			}

			$jytpay_card_mod = &m('jytpay_card');

			$cardList = $jytpay_card_mod->find("user_id=".$this->visitor->get('user_id'));
			foreach($cardList as $key => $card_info)
			{
				// 卡号显示后四位
				$cardList[$key]['short_bank_card_no'] = '****'.substr($card_info['bank_card_no'], -4);

			}

			$this->assign('cardList', $cardList);
		}
		else
		{
			header("location:index.php?app=jytrnpay&act=firstpay&order_id={$order['order_id']}&money={$order['money']}");
			exit;
		}

		$this->_config_seo(array(
			'title' => Conf::get('site_title'),
		));



		$this->assign('order', $order);
		$this->display('jytrnpay.quickpay.html');
	}

	function firstpay()
	{
		// 订单信息
		$order = array('order_id' => $_GET['order_id'],'money' => $_GET['money']);

		$this->_config_seo(array(
			'title' => Conf::get('site_title'),
		));

		$this->assign('order', $order);
		$this->display('jytrnpay.firstpay.html');
	}

	function getcode()
	{
		if(!IS_POST)
		{
			echo json_encode(array('remark' => '请求非法！'));
			exit;
		}
		else
		{
			$firstUsed = in_array(trim($_POST['tran_code']), array('TD4001')) ? TRUE : FALSE;
			$order_sn = trim($_POST['order_id']);
			$epaylog_mod = &m('epaylog');

			$epaylog = $epaylog_mod->get(array(
				'conditions' => 'user_id='.$this->visitor->get('user_id').' AND order_sn="'.$order_sn.'"', 'fields' => 'money'));

			if(!$epaylog) {
				echo json_encode(array('remark' => '支付金额不合理！'));
				exit;
			}

			// 首次支付并绑卡
			if($firstUsed)
			{
				$tran_code = 'TD1001';

				//构造要请求的参数数组，无需改动
				$parameter = array(
					"cust_no" 		=> $this->visitor->get('user_id'),//trim($_POST['cust_no']);//'客户号(必填)';
					"order_id"		=> $order_sn,//trim($_POST['order_id']);//'订单号(必填)';    //一个订单号只能提交一次
					"bank_code"		=> trim($_POST['bank_code']),
					"bank_card_no"	=> trim($_POST['bank_card_no']),//'银行卡号(必填)';
					"id_card_no"	=> trim($_POST['id_card_no']),//'持卡人身份证(必填)';
					"mobile"		=> trim($_POST['mobile']),//'银行卡绑定手机号(必填)';
					"name" 			=> trim($_POST['name']),//'持卡人姓名(必填)';
					"tran_amount"   => $epaylog['money'],
				);
			}
			// 快捷支付
			else
			{
				$tran_code = 'TD1002';

				//构造要请求的参数数组，无需改动
				$parameter = array(
					"cust_no" 		=> $this->visitor->get('user_id'),//trim($_POST['cust_no']);//'客户号(必填)';
					"order_id"		=> $order_sn,//trim($_POST['order_id']);//'订单号(必填)';    //一个订单号只能提交一次
					"bank_card_no"	=> trim($_POST['bank_card_no']),//'银行卡号(必填)';
					"tran_amount"   => $epaylog['money'],
				);
			}


			$settings = include(ROOT_PATH . '/data/settings.inc.php');
			$JytRNPay = new JytRNPayNotify($settings);
			$result = $JytRNPay->buildRequestForm($tran_code, $parameter);
			if($result['head']['resp_code'] == 'S0000000') {

				$this->insertCardInfo($_POST);

				echo json_encode($result['body']);
			}
			else
			{

				// 已经成功获取过一次短信了，短信过期，再次获取
				if($result['head']['resp_code'] == "ERN00000")
				{
					// 从数据库读取手机号
					$jytpay_card_mod = &m('jytpay_card');
					$card = $jytpay_card_mod->get("user_id=".$this->visitor->get("user_id").' AND bank_card_no="'.$parameter['bank_card_no'].'"');

					$result = $this->getcodeagain(array('order_id' => $parameter['order_id'], 'mobile' => $card['phone_no']));

					echo json_encode($result);
				}
				else {
					var_dump($result);
					echo json_encode(array('remark' => $result['head']['resp_desc']));
				}
			}
		}
	}

	function insertCardInfo($post = array())
	{
		// 首次绑卡，记录绑定信息到本地数据库
		$jytpay_card_mod = &m('jytpay_card');
		$data = array(
			'user_id' => $this->visitor->get('user_id'),
			'bank_card_no' => trim($post['bank_card_no'])
		);
		if(isset($post['bank_code']) && !empty($post['bank_code'])) {
			$data['bank_code'] = trim($post['bank_code']);
		}
		if(isset($post['mobile']) && !empty($post['mobile'])){
			$data['phone_no'] = $post['mobile'];
		}
		if(isset($post['name']) && !empty($post['name'])){
			$data['name'] = $post['name'];
		}
		if(isset($post['id_card_no']) && !empty($post['id_card_no'])){
			$data['id_card_no'] = $post['id_card_no'];
		}


		if($card = $jytpay_card_mod->get("user_id=".$this->visitor->get("user_id").' AND bank_card_no="'.trim($post['bank_card_no']).'"')){
			$jytpay_card_mod->edit($card['id'], $data);
		} else $jytpay_card_mod->add($data);
	}

	function getcodeagain($parameter = array())
	{
		$settings = include(ROOT_PATH . '/data/settings.inc.php');
		$JytRNPay = new JytRNPayNotify($settings);
		$result = $JytRNPay->buildRequestForm("TD4003", $parameter);
		if($result['head']['resp_code'] == 'S0000000') {
			if($result['body']['tran_state'] == '01') {
				$remark = '验证码已发送';
			} elseif($result['body']['tran_state'] == '02') {
				$remark = '订单不支持重新获取验证码';
			} else $remark = '验证码获取失败';
		} elseif(isset($result['head']['resp_desc'])) {
			$remark = $result['head']['resp_desc'];
		} else $remark = '验证码获取失败';

		return array('remark' => $remark);
	}

	//  获取绑卡记录(目前没用到）
	function getcard()
	{
		$settings = include(ROOT_PATH . '/data/settings.inc.php');

		//构造要请求的参数数组，无需改动
		$parameter = array(
			"cust_no" 		=> $this->visitor->get('user_id'), // 需要查询的客户号
			"bank_card_no"  => '6226197902709269',
		);

		$JytRNPay = new JytRNPayNotify($settings);
		$result = $JytRNPay->buildRequestForm("TD2002", $parameter);
		echo json_encode($result['body']);
	}

	// 解除卡绑定
	function relievecard()
	{
		$jytpay_card_mod = &m('jytpay_card');

		// 验证当前卡号是不是自己的
		$jytpay_card = $jytpay_card_mod->get("user_id=".$this->visitor->get('user_id')." AND bank_card_no='".trim($_POST['bank_card_no'])."'");
		if(!$jytpay_card)
		{
			echo json_encode(array('remark' => '卡号不存在！'));
			exit;
		}

		$settings = include(ROOT_PATH . '/data/settings.inc.php');

		//构造要请求的参数数组，无需改动
		$parameter = array(
			"cust_no" 		=> $this->visitor->get('user_id'), // 需要查询的客户号
			"bank_card_no"  => trim($_POST['bank_card_no']),
		);

		$JytRNPay = new JytRNPayNotify($settings);
		$result = $JytRNPay->buildRequestForm("TD4002", $parameter);
		if($result['head']['resp_code'] == 'S0000000') {

			// 删除本地绑卡记录
			$jytpay_card_mod = &m('jytpay_card');
			$jytpay_card_mod->drop("user_id=".$this->visitor->get("user_id").' AND bank_card_no="'.trim($_POST['bank_card_no']).'"');

			echo json_encode($result['body']);
		}
		else {
			echo json_encode(array('remark' => $result['head']['resp_desc']));
		}
	}
}

?>
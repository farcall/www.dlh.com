<?php
class epayjytPayment extends BasePayment {

    var $_code = 'epayjyt';
    
    var $_gateway = 'index.php?app=epay&act=czfs';
    
    function get_payform($order_info) {
        
        $params = array(
            'cz_money'=>$order_info['order_amount'],
            'czfs'=>'jytpay',
            'order_sn'=>$order_info['order_sn'],
        );
        if(ECMALL_WAP ==1){
            //$params['czfs'] = 'wapjytpay';
			$params['czfs'] = 'jytpay';
        }
        
        return $this->_create_payform('POST', $params);
    }

}
?>

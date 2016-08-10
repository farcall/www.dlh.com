 <?php
/**
 * 地区代理
 * Created by PhpStorm.
 * User: xiaokang
 * Date: 2016/4/30
 * Time: 20:44
 */

class ProxyApp extends BackendApp{
    var $_region_mod;
    var $_store_mod;
    var $_order_mod;
    function __construct() {
        $this->ProxyApp();
        $this->_region_mod = &m('region');
        $this->_store_mod = &m('store');
        $this->_order_mod = &m('order');
    }

    function ProxyApp() {
        parent::BackendApp();
    }

	/**
	 * hastuijian = 1 调用有推荐人的商家信息
	 * hastuijian = 2 调用没有推荐人的商家信息
	 * hastuijian = 3 调用所有的商家信息
	 */
	function _getConditions($region_id){

		$conditions =  'region_id = '.$region_id." and `tuijiandaili` IS NOT NULL  AND `tuijiandaili` NOT IN ('自由商户')";

		if ($_GET['hastuijian'] == 1){
			$conditions =  'region_id = '.$region_id." and `tuijiandaili` IS NOT NULL  AND `tuijiandaili` NOT IN ('自由商户')";
		}elseif ($_GET['hastuijian'] == 2){
			$conditions =  'region_id = '.$region_id." and (`tuijiandaili` IS NULL  or `tuijiandaili` IN ('自由商户'))";
		}elseif ($_GET['hastuijian'] == 3){
			$conditions =  'region_id = '.$region_id;
		}

		return $conditions;
	}

    function index(){
		
		$this->import_resource(array('script' => 'jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                                      'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
            $area = html_script($_GET['area']);
			
			$timeIntval = array('add_time_from'  => gmstr2time($_GET['add_time_from']), 'add_time_to' => gmstr2time_end($_GET['add_time_to']) - 1);

            $region = $this->_region_mod->get(array(
                  'conditions' => "region_name = '$area'",
            ));
            
			if($region)
			{

				$parent_id =  $region['region_id'];
				$region_childs = $this->_region_mod->getAll("select * from ecm_region where parent_id=$parent_id");
				$regions[] = $region;
				$regions = array_merge($regions,$region_childs);
	
				$total_amount = 0;
				$allstorecount = 0;
				$all_stores = array();
	
				foreach($regions as $key => $region){
					$stores = $this->_store_mod->find(array(
						'conditions' =>  $this->_getConditions($region['region_id']), //'region_id = '.$region['region_id']." and `tuijiandaili` IS NOT NULL  AND `tuijiandaili` NOT IN ('自由商户')",
					));
					$region_amount = 0;
	
					foreach( $stores as $k =>$store){
						$storeAmounts = $this->_getStoreAmount($store['store_id']);
						$region_amount += $storeAmounts;
						$total_amount  += $storeAmounts;
					}
	
					$regions[$key]['amount'] = $region_amount;
					$regions[$key]['count'] = sizeof($stores);
					$allstorecount += $regions[$key]['count'];
				}



				$this->assign('regions',$regions);
				$this->assign('total_amount',$total_amount);
				$this->assign('yongjin',$total_amount/10);
				$this->assign('dailifei',$total_amount/100);
				$this->assign('count',$allstorecount);
				$this->assign('area',$area);
	
				
			}

		$this->assign('hastuijian',$_GET['hastuijian']);
		$this->display('paycenter/proxy.html');

    }
	
	function export_area()
	{
            $area = html_script($_GET['area']);
			
            $region = $this->_region_mod->get(array(
                  'conditions' => "region_name = '$area'",
            ));
            
			if($region)
			{

				$parent_id =  $region['region_id'];
				$region_childs = $this->_region_mod->getAll("select * from ecm_region where parent_id=$parent_id");
				$regions[] = $region;
				$regions = array_merge($regions,$region_childs);
	
				$total_amount = 0;
				$allstorecount = 0;
				$all_stores = array();
	
				foreach($regions as $key => $region){
					$stores = $this->_store_mod->find(array(
						'conditions' =>  $this->_getConditions($region['region_id']), //'region_id = '.$region['region_id']." and `tuijiandaili` IS NOT NULL  AND `tuijiandaili` NOT IN ('自由商户')",
					));
					$region_amount = 0;
	
					foreach( $stores as $k =>$store){
						$storeAmounts = $this->_getStoreAmount($store['store_id']);
						$region_amount += $storeAmounts;
						$total_amount  += $storeAmounts;
					}
	
					$regions[$key]['amount'] = $region_amount;
					$regions[$key]['count'] = sizeof($stores);
					$allstorecount += $regions[$key]['count'];
				}
				
				list($timeIntval, $conditions) = $this->_get_conditions_by_timeIntval();
				
				$lang_title = array(
                	'region_id'         => '地区ID',
                	'region_name'       => '地区名称',
                	'count'             => '店铺数量',
                	'amount' 			=> '销售额',
                
        		);

				/* xls文件数组 */
				$record_xls = $record_value = array();
				
				if($timeIntval['add_time_from'] && $timeIntval['add_time_to'] && ($timeIntval['add_time_to'] > $timeIntval['add_time_from'])) {
					$folder = 'proxy_'.$region['region_id'].'_'.local_date('Y-m-d',$timeIntval['add_time_from']).'-'. local_date('Y-m-d', $timeIntval['add_time_to']);
				} else $folder = 'proxy_'.$region['region_id'].'_all';
		
				$record_xls[]  = $lang_title;
				foreach($regions as $key=>$record)
				{
					$record_value['region_id']	    = $record['region_id'];
					$record_value['region_name']   	= $record['region_name'];
					$record_value['count']			= $record['count'];
					$record_value['amount']     	= $record['amount'];
		
					$record_xls[] = $record_value;
				}
		
				import('excelwriter.lib');
				$ExcelWriter = new ExcelWriter(CHARSET, $folder);
				$ExcelWriter->add_array($record_xls);
				$ExcelWriter->output();
				
			}
    }

    /**
     * 作用:增加代理
     * Created by QQ:710932
     */
    function add(){
        if(IS_POST){
            $member_mod = &m('member');

            $region_name = trim($_POST['region_name']);
            $user_name = trim($_POST['user_name']);

            $user_data = $member_mod->get(array(
                'conditions' => "user_name = '$user_name'",
            ));

            if(empty($user_data)){
                $this->show_warning('对不起,会员账号不存在，请先注册账号');
                return;
            }

            $region_data = $this->_region_mod->get(array(
                'conditions' => "region_name = '$region_name'",
            ));

            if(empty($region_data)){
                $this->show_warning('代理库中没有该区域!');
                return;
            }


            $proxy = &m('proxy')->get(array(
                'conditions' => "region_name = '$region_name'",
            ));
            if(!empty($proxy)){
                $this->show_warning("该区域已存在代理，请不要重复提交");
                return;
            }

            $result = &m('proxy')->add(array(
                'user_id'=>$user_data['user_id'],
                'user_name'=>$user_data['user_name'],
                'region_id'=>$region_data['region_id'],
                'region_name'=>$region_data['region_name'],
                'create_time'=>gmtime(),
            ));

            if(empty($result)){
                $this->show_warning('代理添加失败');
                return;
            }

            $this->show_message("代理添加成功");
        }
        else{
            $this->display("paycenter/proxy_add.html");
        }
    }
    /**
     * 作用:查看店铺下的所有订单
     * Created by QQ:710932
     */
    function store(){
        $store_id = $_GET['id'];
        if(!is_numeric($store_id)){
            $this->show_warning('请不要非法提交');
            return;
        }

        //todo 权限控制
		list($timeIntval, $conditions) = $this->_get_conditions_by_timeIntval();

        $page = $this->_get_page(20);

        $orders = $this->_order_mod->find(array(
            'limit'=>$page['limit'],
            'conditions'=>'seller_id = '.$store_id .' and status='.ORDER_FINISHED . $conditions,
            'count' => true,
        ));



        $page['item_count'] = $this->_order_mod->getCount();   //获取统计数据
        $this->_format_page($page);
        $this->assign('page_info', $page);   //将分页信息传递给视图，用于形成分页条



        $this->assign('orders',$orders);
        $this->display('paycenter/proxy_store.html');
    }
	
	function export_store(){
        $store_id = $_GET['id'];
        if(!is_numeric($store_id)){
            $this->show_warning('请不要非法提交');
            return;
        }

        //todo 权限控制
		
		list($timeIntval, $conditions) = $this->_get_conditions_by_timeIntval();

        $orders = $this->_order_mod->find(array(
            'conditions'=>'seller_id = '.$store_id .' and status='.ORDER_FINISHED . $conditions,
        ));

        $lang_title = array(
             'order_id'        => '订单ID',
             'order_sn'      => '订单SN',
             'seller_name'      => '操作员',
             'buyer_id' 			=> '买家ID',
			'buyer_name' 			=> '买家',
			'order_amount'=> '订单金额',
			'finished_time' 			=> '完成时间',
                
        );

				/* xls文件数组 */
				$record_xls = $record_value = array();
				
				if($timeIntval['add_time_from'] && $timeIntval['add_time_to'] && ($timeIntval['add_time_to'] > $timeIntval['add_time_from'])) {
					$folder = 'proxy_order_'.$store_id.'_'.local_date('Y-m-d',$timeIntval['add_time_from']).'-'. local_date('Y-m-d', $timeIntval['add_time_to']);
				} else $folder = 'proxy_order_'.$store_id.'_all';
		
				$record_xls[]  = $lang_title;
				foreach($orders as $key=>$record)
				{
					$record_value['order_id']	    = $record['order_id'];
					$record_value['order_sn']   	= $record['order_sn'];
					$record_value['seller_name']			= $record['seller_name'];
					$record_value['buyer_id']			= $record['buyer_id'];
					$record_value['buyer_name']			= $record['buyer_name'];
					$record_value['order_amount']     	= $record['order_amount'];
					$record_value['finished_time']			= local_date('Y-m-d H:i:s', $record['finished_time']);
		
					$record_xls[] = $record_value;
				}
		
				import('excelwriter.lib');
				$ExcelWriter = new ExcelWriter(CHARSET, $folder);
				$ExcelWriter->add_array($record_xls);
				$ExcelWriter->output();
    }


    /**
     * 通过地区ID查看当地所有店铺的业绩
     * Created by QQ:710932
     */
    function region(){
        //地区ID
        $region_id = $_GET['id'];
        if(!is_numeric($region_id)){
            $this->show_warning('请不要非法提交');
            return;
        }

        //todo 权限控制
		
        $region = $this->_region_mod->get($region_id);
        if(empty($region)){
            $this->show_warning('对不起，您查询的地区不在代理库中！');
            return;
        }
		
		//list($timeIntval) = $this->_get_conditions_by_timeIntval();

        $stores = $this->_store_mod->find(array(
			'conditions' =>  $this->_getConditions($region['region_id']), //'region_id = '.$region['region_id']." and `tuijiandaili` IS NOT NULL  AND `tuijiandaili` NOT IN ('自由商户')",
			'order' => "tuijiandaili desc",
		));

        foreach( $stores as $k =>$store){
            $storeAmounts = $this->_getStoreAmount($store['store_id']);
            $stores[$k]['amount'] = $storeAmounts;
            $stores[$k]['region_name'] = $region['region_name'];
        }


        $this->assign('stores',$stores);
        $this->display('paycenter/proxy_region.html');
    }
	
	function export_region(){
        //地区ID
        $region_id = $_GET['id'];
        if(!is_numeric($region_id)){
            $this->show_warning('请不要非法提交');
            return;
        }

        //todo 权限控制


        $region = $this->_region_mod->get($region_id);
        if(empty($region)){
            $this->show_warning('对不起，您查询的地区不在代理库中！');
            return;
        }
		
		list($timeIntval) = $this->_get_conditions_by_timeIntval();

        $stores = $this->_store_mod->find(array(
			'conditions' =>  $this->_getConditions($region['region_id']), //'region_id = '.$region['region_id']." and `tuijiandaili` IS NOT NULL  AND `tuijiandaili` NOT IN ('自由商户')",
        ));
		

        foreach( $stores as $k =>$store){
            $storeAmounts = $this->_getStoreAmount($store['store_id']);
            $stores[$k]['amount'] = $storeAmounts;
            $stores[$k]['region_name'] = $region['region_name'];
        }


        $lang_title = array(
             'store_id'        => '店铺ID',
			'tuijiandaili' => '推荐代理',
             'store_name'      => '店铺名称',
             'owner_name'      => '店主名称',
             'tel' 			=> '联系电话',
			'add_time' 			=> '开店时间',
			'fuwuzhuanyuan_name'=> '服务专员',
			'amount' 			=> '销售额',
                
        );

				/* xls文件数组 */
				$record_xls = $record_value = array();
				
				if($timeIntval['add_time_from'] && $timeIntval['add_time_to'] && ($timeIntval['add_time_to'] > $timeIntval['add_time_from'])) {
					$folder = 'proxy_store_'.$region['region_id'].'_'.local_date('Y-m-d',$timeIntval['add_time_from']).'-'. local_date('Y-m-d', $timeIntval['add_time_to']);
				} else $folder = 'proxy_store_'.$region['region_id'].'_all';
		
				$record_xls[]  = $lang_title;
				foreach($stores as $key=>$record)
				{
					$record_value['store_id']	    = $record['store_id'];
					$record_value['tuijiandaili']	    = $record['tuijiandaili']?$record['tuijiandaili']:'自由商户';

					$record_value['store_name']   	= $record['store_name'];
					$record_value['owner_name']			= $record['owner_name'];
					$record_value['tel']			= $record['tel'];
					$record_value['add_time']			= local_date('Y-m-d H:i:s', $record['add_time']);
					$record_value['fuwuzhuanyuan_name']			= $record['fuwuzhuanyuan_name'];
					$record_value['amount']     	= $record['amount'];
		
					$record_xls[] = $record_value;
				}
		
				import('excelwriter.lib');
				$ExcelWriter = new ExcelWriter(CHARSET, $folder);
				$ExcelWriter->add_array($record_xls);
				$ExcelWriter->output();
    }



    /**
     * @param $store_id
     * 作用:获得指定店铺的销售额
     * Created by QQ:710932
     */
    function _getStoreAmount($store_id){
		
		list($timeIntval, $conditions) = $this->_get_conditions_by_timeIntval();
        //order_amount
       // select sum(order_amount) from ecm_order where seller_id = 2 and status=40
        return $this->_order_mod->getOne('select sum(order_amount) from '.DB_PREFIX.'order where seller_id = '.$store_id.' and status='.ORDER_FINISHED. $conditions);
    }
	
	function _get_conditions_by_timeIntval()
	{
		$timeIntval = array('add_time_from'  => gmstr2time($_GET['add_time_from']), 'add_time_to' => gmstr2time_end($_GET['add_time_to']) - 1);
		
		$conditions = '';
		if($timeIntval)
		{
			$add_time_from = $timeIntval['add_time_from'];
			$add_time_to   = $timeIntval['add_time_to'];
			
			if($add_time_from && $add_time_to && ($add_time_to >= $add_time_from))
			{
				 $conditions = " AND pay_time >={$add_time_from} AND pay_time <={$add_time_to} ";
			}
		}
		
		return array($timeIntval, $conditions);
	}
}
<?php

define('LOCK_FILE', ROOT_PATH . '/data/init.lock');

if(!file_exists(LOCK_FILE)) 
{
	// 如果有手机版的，需要加上此项
	if(!defined('CHARSET')) {
		define('CHARSET', substr(LANG, 3));
	}
	
	Psmb_init()->create_table();
	
	/* 创建完表后，生成锁定文件 */
	file_put_contents(LOCK_FILE,1);
}

class Psmb_init 
{
	function create_table()
	{
		
		/* store  table */
		$result = db()->getAll('SHOW COLUMNS FROM '. DB_PREFIX . 'store');
		$fields = array();
		foreach($result as $v) {
			$fields[] = $v['Field'];
		}
		if(!in_array('latlng', $fields)){
			$sql = 'ALTER TABLE `'.DB_PREFIX.'store` ADD `latlng` varchar(100) NOT NULL';
			db()->query($sql);
		}
	
	}
}

?>
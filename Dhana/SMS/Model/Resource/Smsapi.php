<?php

class Dhana_SMS_Model_Resource_Smsapi 
	extends Mage_Core_Model_Resource_Db_Abstract{
	
	protected function _construct(){
		
		$this->_init('dhsms/smsapi', 'channel_id');
	}
}

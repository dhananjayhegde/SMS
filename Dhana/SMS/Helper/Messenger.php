<?php
	/**
	 * @deprecated
	 */
  class Dhana_SMS_Helper_Messenger extends Mage_Core_Model_Abstract{


    protected $user         ="add_your_user_name_pasword_here";
    protected $receipientno = '';
    protected $senderID     ="<<sender_id>>";
    protected $msgtxt       = '';
    protected $smstype		= "";
    protected $url			= "";

    protected function _construct(){

      $this->_init('dhsms/messenger');
    }

    public function send($sms){


      if($sms instanceof Dhana_SMS_Model_SMS){

        $ch = curl_init();
	
        $this->smstype 		= $sms->getData('smsType');
        $this->receipientno = $sms->getData('telephone');
        $this->msgtxt 		= $sms->getData('msgtxt');
        
        //read the sms_api_provider table for url and username of the api
        $smsapi = Mage::getModel('dhsms/smsapi')->load(1);
        
        $this->user	 	=	$smsapi->getData('user_id');
        $this->senderID =	$smsapi->getData('sender_id');
        $this->url 		=	$smsapi->getData('url');

        curl_setopt($ch, CURLOPT_URL,  $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "user=$this->user&senderID=$this->senderID&receipientno=$this->receipientno&msgtxt=$this->msgtxt");

        $buffer = curl_exec($ch);
        curl_close($ch);
        
        return $buffer;

      }
    }
  }
 ?>

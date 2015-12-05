<?php

class Dhana_SMS_Model_SMS extends Mage_Core_Model_Abstract{


  const SEND_SMS_ACTIVE       = TRUE;
  private $smsTemplate        = null;
  private $msgtxt             = "this is only a test message";
  private $smsType            = 0;
  private $delivery_status    = false;
  private $telephone          = '';
  private $variables          = Array();
  


  protected function _construct(){

    $this->_init('dhsms/sms');
  }
  /**
    * logSMS function
    */
  public function logSMS($template, $variables){

      /*error handling*/
      $this->smsType 		= $variables['smstype'];
      $this->telephone  	= $variables['telephone'];
      $this->smsTemplate 	= $template;
      $this->variables 		= $variables;
      
      $this->_composeSMS();
      
      //Sample comment

      if(self::SEND_SMS_ACTIVE){

        //call the helper to send the SMS
        
      	$messenger = Mage::helper('dhsms/advancedMessenger');
      	$messenger->send($this);
      }
  }

  /**
    * _composeSMS function
    */
  private function _composeSMS(){


    $templateCode   = Mage::getStoreConfig($this->smsTemplate, 'default');
    $smsTemplate    = Mage::getModel('core/email_template')->loadByCode($templateCode);
    $templateText   = $smsTemplate->getTemplateText();

    $this->msgtxt = str_replace(array_keys($this->variables), array_values($this->variables), $templateText);

    Mage::log('SMS sent successfully ==> '.$this->msgtxt, null, "order.log");

  }
  
  public function getData($field){
  	
  	return $this->$field;
  	
  }

}
?>

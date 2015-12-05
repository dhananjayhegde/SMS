<?php

class Dhana_SMS_Model_Observer{

  const XML_PATH_SMS_ORDER_TEMPLATE   			= 'dhana_sms/order/template';
  const XML_PATH_SMS_ORDER_SUPP_TEMPLATE   		= 'dhana_sms/order_supp/template';
  const XML_PATH_SMS_ORDER_COMPLETE_TEMPLATE   	= 'dhana_sms/order_complete/template';
  const TRANSACTIONAL_SMS       = '1';
  const PROMOTIONAL_SMS         = '0';


  public function sendOrderConfirmationSMS($observer){

    $order = $observer->getOrder();
   
    $variables = array(
      'order_id'  	=> $order->getIncrementId(),
      'store_name'  => $order->getStoreGroupName(),
      'telephone'   => $order->getBillingAddress()->getData('telephone'),
      'smstype'     => self::TRANSACTIONAL_SMS
    );

    $smsmodel = Mage::getModel('dhsms/sms');
    $smsmodel->logSMS(self::XML_PATH_SMS_ORDER_TEMPLATE, $variables);
    
    //Send the supplier an SMS about the new order created
    //Assuming that each a new store is created for each supplier
    $variables = array(
    		'order_id'    	=> $order->getIncrementId(),
    		'telephone'   	=> Mage::getStoreConfig('general/store_information/phone', $order->getStoreId()),
    		'smstype'     	=> self::TRANSACTIONAL_SMS,
    		'order_amount' 	=> $order->getGrandTotal()
    );
    
    $smsmodel->logSMS(self::XML_PATH_SMS_ORDER_SUPP_TEMPLATE, $variables);

  }
  
  
  /*
   * Send the "completed" SMS only if the order status is "Completed"
   * 
   */
  protected function sendSalesOrderCompleted($changedOrder){
  	  	
  	$variables = array(
  			'order_id'  	=> $changedOrder->getIncrementId(),
  			'store_name'  	=> $changedOrder->getStoreGroupName(),
  			'telephone'   	=> $changedOrder->getBillingAddress()->getData('telephone'),
  			'smstype'     	=> self::TRANSACTIONAL_SMS,
  			'customer_name'	=> $changedOrder->getCustomerName()
  	);
  	
  	$smsmodel = Mage::getModel('dhsms/sms');
  	$smsmodel->logSMS(self::XML_PATH_SMS_ORDER_COMPLETE_TEMPLATE, $variables);
  	
  }
  
  
  /*
   * Once the shipment is posted, check if the order status is changed to
   * completed. Only if so, send the SMS
   */
  
  public function salesOrderShipmentCompleted($observer){
  	
  	$order = $observer->getShipment()->getOrder();
  	 
  	if($order == null){
  			
  		return;
  	}
  	
  	$changedOrder = Mage::getModel('sales/order')
  					->loadByIncrementId($order->getIncrementId());
  	
  	if($changedOrder->getStatus() != 'complete'){
  		 
  		return; //do nothing
  	}
  	
  	$this->sendSalesOrderCompleted($changedOrder);
  	
  }
  
  
  /*
   * Once the invoice is posted, check if the order status is changed to
   * complete. Only if so, send the SMS
   */
  public function salesOrderInvoiceCompleted($observer){
  	
  	$order = $observer->getInvoice()->getOrder();
  	
  	if($order == null){
  		 
  		return;
  	}
  	 
  	$changedOrder = Mage::getModel('sales/order')
  					->loadByIncrementId($order->getIncrementId());
  	 
  	if($changedOrder->getStatus() != 'complete'){
  		 
  		return; //do nothing
  	}
  	
  	$this->sendSalesOrderCompleted($changedOrder);
  	
  	
  }
  
  
}
 ?>

<?php

/*
 * This class is not named 'Advanced' for nothing!!!
 * This is an intelligent program.
 * We store the api details in the table sms_api_provider
 * 
 * Detilas such as URL and the parameters such as useer name, password, message, sender ID
 * etc are fetched from the database. 
 * 
 * These parameter names and sequence may vary from api to api. So, we cannot hardcode
 * parameter names and sequence. These too have to be determined at run time along with their
 * values.
 * 
 * Values for some parameters are supplied by the program - such as Recipient number, message
 * text.
 * 
 */

class Dhana_SMS_Helper_AdvancedMessenger extends Dhana_SMS_Helper_Messenger{
	
	protected $params = '';			//parameters to be passed to the api - CURLOPT_POSTFIELDS
	protected $values = '';			//values of parameters - sms_api_provider table
	
	protected $apiurl = '';			//API URL - obtain from table sms_api_provider
	protected $paramterList = ''; 	//list of api parameters and their values as a string
	protected $sms = '';			//Dhana_SMS_Model_SMS
	protected $errors = '';			//collectoed errors - future use
	private $matcher = array(
			
			'telephone' => 'recipient receiver telephone customer destination',
			'msgtxt'	=>	'message sms text msg',
	);								//matcher strings for parameters that are NOT supplied by api provider
	
	
	public function send($sms){
	
	
		if($sms instanceof Dhana_SMS_Model_SMS){
	
			$this->sms = $sms;
			$ch = curl_init();

			$this->_initialize_params();
			
			if ($this->errors == '' && $this->paramterList != ''){
				
				curl_setopt($ch, CURLOPT_URL,  $this->apiurl);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $this->paramterList);
				
				$buffer = curl_exec($ch);
				
				//error handling
				
			}
			curl_close($ch);
	
			return $buffer;
	
		}
	}
	
	protected function _initialize_params(){
		
		//read the sms_api_provider table for url and username of the api
		$smsapi = Mage::getModel('dhsms/smsapi')->load(2);
		
		$this->apiurl = $smsapi->getData('url');
		$this->params = explode(',', $smsapi->getData('params'));
		$this->values = explode(',', $smsapi->getData('values'));
		
		$index = 0;
		foreach ($this->params as $parameter){
			
			if($this->values[$index] !== 'null'){
				
				//parameters values are avaialble in the databse
				if($index == 0){
					
					$this->paramterList = $parameter.'='.$this->values[$index];
				}else{
					
					$this->paramterList = $this->paramterList.'&'.$parameter.'='.$this->values[$index];				
				}
			}else{
				
				//parameter values provided by the program
				$pos = false;
				foreach ($this->matcher as $matchKey => $matchValue){
					
					$pos = strpos($matchValue, $parameter);
					if ($pos !== false){
						
						if ($index === 0){
								
							$this->paramterList = $parameter.'='.$this->sms->getData($matchKey);
						}else{
								
							$this->paramterList = $this->paramterList.'&'.$parameter.'='.$this->sms->getData($matchKey);
						}

						//found something? break from the loop
						unset($this->matcher[$matchKey]); //optimization
						break;
					}
					
				}
				
				if ($pos === false){
					
					$this->errors = $this->errors."  ".$parameter." : not supplied or not found";
				}
				
			}
			
			$index++;	//pointer to corresponding $values of the $parameters
			
		}
		
	}

}
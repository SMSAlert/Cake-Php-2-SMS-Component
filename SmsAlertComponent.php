<?php
App::uses('Component', 'Controller');
class SmsAlertComponent extends Component {
	var $Controller;
	var $_init_done = false;
	var $api_url = "https://www.smsalert.co.in/api/";
	var $username = null;
	var $password = null;
	var $senderid = null;
	var $route = null;
	var $message_storage_path = null;	
	var $auto_cleanup = true;
	var $delivery_report_url = "";

	
   /* initialize */
    public function initialize(Controller $Controller) {
	  if(!function_exists('curl_init')) {
			$this->initializationError();
			return false;
	  }
	  parent::initialize($Controller);
	  $this->controller = $Controller;
	  $this->_init_done = true;
	 }
   
	/* send sms and schedule sms */
	function send($mobileno, $text, $schedule=null) {
		if($this->_init_done) {
			$error = false;
			
			if($schedule!=null) {	
				$parameters = array( 'user' => $this->username, 'pwd' => $this->password, 'route' => $this->route,
					'sender' => $this->senderid, 'mobileno' => $mobileno, 'text' => $text, 'schedule' => $schedule, );
				$required_parameters = array('user', 'pwd', 'mobileno', 'sender', 'text', 'schedule');	
				
			}
			else {
				$parameters = array( 'user' => $this->username, 'pwd' => $this->password, 'route' => $this->route,
					'sender' => $this->senderid, 'mobileno' => $mobileno, 'text' => $text );
				$required_parameters = array('user', 'pwd', 'mobileno', 'sender', 'text');
			}
			
			foreach($required_parameters as $parameter) {
				if(empty($parameters[$parameter])) {
					trigger_error("Parameter {$parameter} is required");
					$error = true;
				}
			}
				
			if(!$error) {
				
				$delivery_report_url = $this->delivery_report_url;
				if(!empty($delivery_report_url)) {
					$message_id = $this->storeMessage(array('mobileno' => $parameters['mobileno'], 'sender' => $parameters['sender'], 'text' => $parameters['text']));
					if($message_id !== FALSE) {
						$parameters['reference'] = $message_id;
						$parameters['dlrurl'] = $delivery_report_url;
					}
					echo $message_id; 
				}
				
				$url = $this->api_url.'push.json?';
				foreach($parameters as $key => $value) {
					$url .= urlencode($key)."=".urlencode($value)."&";
				}
				
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$result = curl_exec($ch);

				$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				if(($code >= 200) && ($code <= 400)) {
					return true; /* Successfully sent */
				}
			}
		} else {
			$this->initializationError();
		}
		return false;
	}
	
	//generate message id
	function generateMessageId() {
		return md5(md5(microtime(true).""));
	}

	//store message
	function storeMessage($details) {
		if(is_null($this->message_storage_path)) {
			$this->message_storage_path = TMP;
		}

		$message_id = $this->generateMessageId(); //123
		if(@file_put_contents($this->message_storage_path."dlr".$message_id, serialize($details))) {
			return $message_id;
		}
		return false;
	}
	
	 //delivery reports
	function getDeliveryReport() {
		if(!empty($_GET['msgid']) && !empty($_GET['status'])) {
			if(is_null($this->message_storage_path)) {
				$this->message_storage_path = TMP;
			}
			
			$file_path = $this->message_storage_path."dlr".$_GET['msgid'];
			$details = @file_get_contents($file_path);
			
			if($details !== FALSE) {
				$result = unserialize($details);
				if($result !== FALSE) {
					$result['status'] = $_GET['status'];
					if($this->auto_cleanup) {
						//unlink($file_path);
					}
					return $result;
				}
			}
		}
		return false;
	}
	

	
	/* get credits */
	function creditstatus()
	{
		if($this->_init_done) {
			$error = false;
			
			$parameters = array( 'user' => $this->username, 'pwd' => $this->password);
			$required_parameters = array('user', 'pwd');
			
			foreach($required_parameters as $parameter) {
				if(empty($parameters[$parameter])) {
					trigger_error("Parameter {$parameter} is required");
					$error = true;
				}
			}
				
			if(!$error) {
				$url = $this->api_url.'creditstatus?';
				foreach($parameters as $key => $value) {
					$url .= urlencode($key)."=".urlencode($value).'&';
				}

				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$result = curl_exec($ch);

				$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				if(($code >= 200) && ($code <= 400)) {
					return $result; 
				}
			}
		} else {
			$this->initializationError();
		}
		return false;
	}
	
	/* Generic Initialization error */
	function initializationError() {
		trigger_error('The SMS Alert component requires curl to operate.');
	}


}
?>
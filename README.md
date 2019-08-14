
## Overview

Sms Alert Cake php 2.x Component for sending transactional/promotional SMS, through your custom code. Easy to integrate, you just need to write 2 lines of code to send SMS.

## Parameters Details
### If you have no account on smsalert.co.in, kindly register https://www.smsalert.co.in/

* username : SMS Alert User Name

* password : SMS Alert current Password

* mobileno : single or multiple 10 digit mobile numbers (separated by comma)

* text : Message Content to be sent

* senderid : Receiver will see this as sender's ID(for demo account use DEMOOO)


## Usage 
### Include Component file into Controller
    public $components = array('SmsAlert');
	
### Now, in your controller functions, when you wish to send an SMS/text message, your code should look something like this:
	$this->SmsAlert->username = "demo"; /* This is your SMS Alert User Name */
	$this->SmsAlert->password = "*******"; /* This is your SMS Alert Password */
	$this->SmsAlert->senderid = "eStore"; /* This is your SMS Alert Sender Id */
	$this->SmsAlert->message_storage_path = WWW_ROOT."file path"; /* This is your file path */
	$this->SmsAlert->delivery_report_url = "";  /* This is your dlr url */
	
	
	//send single sms
	if($this->SmsAlert->send("97xxxxxx23", "Welcome to SmsAlert!")) {
		/* SMS Sent */
	}
	
	//send multiple sms
	if($this->SmsAlert->send("97xxxxxx23, 97xxxxxx22", "Hi friends! Welcome to SmsAlert!")) {
		/* SMS Sent */
	}
	
	//schedule sms
	if($this->SmsAlert->send("97xxxxxx23", "Hi! Happy Birthday!", "2019-09-03 23:59:59")) {
		/* SMS Scheduled */
	}
	
### Get SMS Alert Credits Details
   if($this->SmsAlert->creditstatus()) {
		/* Credits Details */
	}
	
### Get SMS Campaign Reports Details
   if($this->SmsAlert->getDeliveryReport()) {
		/* Campaign Details */
	}	


## Support 
Email :  support@cozyvision.com
Phone :  080-1055-1055

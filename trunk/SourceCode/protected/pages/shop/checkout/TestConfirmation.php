<?php

class TestConfirmation extends TPage {
	
	public function onLoad($param) {
		parent::onLoad($param);
		var_dump('Order ID: '.$this->Request['order_id']);
		var_dump('Order Number: '.$this->Request['order_num']);
		var_dump('Payment ID: '.$this->Request['payment_id']);
		
		/***** CHECK PAYPAL *****/
		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-synch';
		
		$tx_token = $this->Request['tx'];
		var_dump('TX Token: '.$this->Request['tx']);
		$paypalInfo = TPropertyValue::ensureArray($this->Application->Parameters['PAYPAL_INFO']);
		$auth_token = $paypalInfo['auth_token'];
		var_dump('Auth Token: '.$auth_token);
		$req .= "&tx=$tx_token&at=$auth_token";
		
		$header='';
		// post back to PayPal system to validate
		$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
		$fp = fsockopen('www.paypal.com', 80, $errno, $errstr, 30);
		if (!$fp) 
		{
			// error
			$this->Notice->Type = UserNoticeType::Error;
			$this->Notice->Text = $this->Application->getModule("message")->translate("PAYPAL_CONNECT_ERROR");
			$this->mainBox->Visible = false;
			return;
		} 
		else
		{
			fputs ($fp, ($header . $req));
			// read the body data 
			$res = '';
			$headerdone = false;
			while (!feof($fp)) 
			{
				$line = fgets ($fp, 1024);
				if (strcmp($line, "\r\n") == 0) 
				{
					// read the header
					$headerdone = true;
				}
				else if ($headerdone)
				{
					// header has been read. now read the contents
					$res .= $line;
				}
			}
			
			// parse the data
			var_dump('Result from Paypal: '.$res);
			$lines = explode("\n", $res);
			$keyarray = array();
			if (strcmp ($lines[0], "SUCCESS") == 0) 
			{
				for ($i=1; $i<count($lines);$i++)
				{
					list($key,$val) = explode("=", $lines[$i]);
					$keyarray[urldecode($key)] = urldecode($val);
				}
				// check that payment_amount/payment_currency are correct
				var_dump('Return from Paypal: ');
				var_dump($keyarray);
				$firstname = $keyarray['first_name'];
				$lastname = $keyarray['last_name'];
				$itemname = $keyarray['item_name'];
				$payment_gross = $keyarray['payment_gross'];
				$txn_id = $keyarray['txn_id'];
				$receiver_email = $keyarray['receiver_email'];
				$payer_email = $keyarray['payer_email'];
				$payment_status = $keyarray['payment_status'];
			}
			else if (strcmp ($lines[0], "FAIL") == 0)
			{
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("PAYPAL_PAYMENT_ERROR");
				$this->mainBox->Visible = false;
			}
			else
			{
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("UNKNOWN_ERROR");
				$this->mainBox->Visible = false;
			}
		}
		fclose ($fp);
		/***** END CHECK PAYPAL *****/
	}
}
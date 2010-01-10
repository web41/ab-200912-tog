<?php

class Confirmation extends TPage
{
	private $_order;
	private $_payment;
	private $_orderItems;
	private $_shippingMethod;
	private $_hash;
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			$this->setHash($this->Request['hash']);
			$this->setOrder();
			if ($this->Order)
			{
				$this->setOrderItems();
				$this->setPayment();
				$this->setShippingMethod();
			}
			$paymentRecord = $this->getPayment();
			
			if ($paymentRecord->PaymentMethodID == 1)  // Paypal
			{
				/***** CHECK PAYPAL *****/
				// read the post from PayPal system and add 'cmd'
				$req = 'cmd=_notify-synch';
	
				$tx_token = $this->Request['tx'];
				$paypalInfo = TPropertyValue::ensureArray($this->Application->Parameters['PAYPAL_INFO']);
				$auth_token = $paypalInfo['auth_token'];
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
						$firstname = $keyarray['first_name'];
						$lastname = $keyarray['last_name'];
						$itemname = $keyarray['item_name'];
						$payment_gross = $keyarray['payment_gross'];
						$txn_id = $keyarray['txn_id'];
						$receiver_email = $keyarray['receiver_email'];
						$payer_email = $keyarray['payer_email'];
						$payment_status = $keyarray['payment_status'];
	
						// update payment & order
						if ($payment_status == "Completed")
						{
							$paymentRecord->Amount = TPropertyValue::ensureFloat($payment_gross);
							$paymentRecord->Status = 1;
						}
						else $paymentRecord->Status = -1;
						$paymentRecord->save();
	
						$this->populateData();
	
						// delete temp data
						$criteria = new TActiveRecordCriteria;
						$criteria->Condition = "session_id = :id and user_id = :user";
						$criteria->Parameters[':id'] = $this->Application->Session->SessionID;
						$criteria->Parameters[':user'] = $this->Application->User->ID;
						CartTempRecord::finder()->deleteAll($criteria);
					}
					else if (strcmp ($lines[0], "FAIL") == 0)
					{
						$this->Notice->Type = UserNoticeType::Error;
						$this->Notice->Text = $this->Application->getModule("message")->translate("PAYPAL_CONNECT_ERROR");
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
			else if ($paymentRecord->PaymentMethodID == 2) // Cash on delivery
			{
				$paymentRecord->Status = 0; // pending
				$paymentRecord->save();
				$this->populateData();

				// delete temp data
				$criteria = new TActiveRecordCriteria;
				$criteria->Condition = "session_id = :id and user_id = :user";
				$criteria->Parameters[':id'] = $this->Application->Session->SessionID;
				$criteria->Parameters[':user'] = $this->Application->User->ID;
				CartTempRecord::finder()->deleteAll($criteria);
			}
			else
			{
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","payment");
				$this->mainBox->Visible = false;
			}
		}
	}
	
	public function render($writer)
	{
		parent::render($writer);
		if ($this->Order && (!$this->Session->contains("OrderNumber") || $this->Order->Num != $this->Application->SecurityManager->validateData(base64_decode($this->Session["OrderNumber"]))))
		{
			//send email here
			$emailer = $this->Application->getModule('mailer');
			$email = $emailer->createNewEmail("OrderConfirmation");
			$html = $email->HtmlContent->flush();
			$stlyeUrl = $this->Request->getBaseUrl($this->Request->IsSecureConnection).($this->Request->UrlManagerModule->UrlPrefix!='/'?$this->Request->UrlManagerModule->UrlPrefix:'')."/themes/default/style.css";
			$dynamicContent = html_entity_decode($this->Session["HtmlContent"], ENT_QUOTES, "UTF-8");
			$html = str_replace("%%STYLE_URL%%",$stlyeUrl,$html);
			$html = str_replace("%%DYNAMIC_CONTENT%%",$dynamicContent,$html);
			$email->setHtmlContent($html);
			try
			{
				$emailer->send($email);
				$this->Session["OrderNumber"] = base64_encode($this->Application->SecurityManager->hashData($this->Order->Num));
			}
			catch(TException $ex)
			{
				//throw new THttpException(500, $ex->ErrorMessage);
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule('message')->translate('UNKNOWN_ERROR');
			}
		}
	}
	
	public function populateData()
	{
		$this->rptCart->DataSource = $this->OrderItems;
		$this->rptCart->DataBind();
	}
	
	public function setHash($value)
	{
		if(($value = $this->Application->SecurityManager->validateData($value))!==false)
		{
			$value=unserialize($value);
			if(is_array($value) && count($value)===3)
			{
				list($oid,$onum,$pid)=$value;
				$oid = TPropertyValue::ensureInteger($this->Application->SecurityManager->validateData(base64_decode($oid)));
				$onum = $this->Application->SecurityManager->validateData(base64_decode($onum));
				$pid = TPropertyValue::ensureInteger($this->Application->SecurityManager->validateData(base64_decode($pid)));
				$this->_hash = array('oid'=>$oid,'onum'=>$onum,'pid'=>$pid);
			}
		}
	}
	
	public function getHash()
	{
		return $this->_hash;
	}
	
	public function setOrder()
	{
		if (isset($this->Hash["oid"]) && isset($this->Hash["onum"]))
		{
			// use Active Record to look for the specified post ID
			$activeRecord = OrderRecord::finder()->withShippingMethod()->withBCountry()->withSCountry()->findByorder_idAndorder_numAnduser_id(TPropertyValue::ensureInteger($this->Hash['oid']), $this->Hash['onum'],$this->Application->User->ID);
			if($activeRecord === null)
			{
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","order");
				$this->mainBox->Visible = false;
			}
			$this->_order = $activeRecord;
		}
		else
		{
			$this->Notice->Type = UserNoticeType::Error;
			$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","order");
			$this->mainBox->Visible = false;
		}
	}
	
	public function getOrder()
	{
		if (!$this->_order) $this->setOrder();
		return $this->_order;
	}

	public function setPayment()
	{
		if (isset($this->Hash["oid"]) && isset($this->Hash["pid"]))
		{
			// use Active Record to look for the specified post ID
			$activeRecord = PaymentRecord::finder()->withPaymentMethod()->findBypayment_idAndorder_id(TPropertyValue::ensureInteger($this->Hash['pid']), TPropertyValue::ensureInteger($this->Hash['oid']));
			if($activeRecord === null)
			{
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","payment");
				$this->mainBox->Visible = false;
			}
			$this->_payment = $activeRecord;
		}
		else
		{
			$this->Notice->Type = UserNoticeType::Error;
			$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","payment");
			$this->mainBox->Visible = false;
		}
	}
	
	public function getPayment()
	{
		if (!$this->_payment) $this->setPayment();
		return $this->_payment;
	}
	
	public function setOrderItems()
	{
		$this->_orderItems = OrderItemRecord::finder()->withProduct()->findAllByorder_id($this->Order->ID);
	}
	
	public function getOrderItems()
	{
		if (!$this->_orderItems) $this->setOrderItems();
		return $this->_orderItems;
	}
	
	public function setShippingMethod()
	{
		$this->_shippingMethod = $this->Order->ShippingMethod;
	}
	
	public function getShippingMethod()
	{
		if (!$this->_shippingMethod) $this->setShippingMethod();
		return $this->_shippingMethod;
	}
	
	public function getFormattedValue($value,$pattern="c",$currency="USD")
	{
		$formatter = new NumberFormat($this->Application->Globalization->Culture);
		return $formatter->format($value,$pattern,$currency,$this->Application->Globalization->Charset);
	}
}

?>
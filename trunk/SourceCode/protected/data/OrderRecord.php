<?php
/**
 * Auto generated by prado-cli.php on 2009-12-13 01:29:37.
 */
class OrderRecord extends TActiveRecord
{
	const TABLE='tbl_order';

	public $ID;
	public $Num;
	public $UserID;
	public $BTitle;
	public $BCompanyName;
	public $BFirstName;
	public $BLastName;
	public $BAddress1;
	public $BAddress2;
	public $BCity;
	public $BState;
	public $BCountryCode;
	public $BZipCode;
	public $BPhone1;
	public $BPhone2;
	public $BFax;
	public $STitle;
	public $SCompanyName;
	public $SFirstName;
	public $SLastName;
	public $SAddress1;
	public $SAddress2;
	public $SCity;
	public $SState;
	public $SCountryCode;
	public $SZipCode;
	public $SPhone1;
	public $SPhone2;
	public $SFax;
	public $Subtotal;
	public $TaxAmount;
	public $ShippingMethodID;
	public $ShippingAmount;
	public $EstDeliveryDate;
	public $CouponCode;
	public $CouponAmount;
	public $RewardPointsRebate;
	public $Deliverer;
	public $TotalPacks;
	public $Total;
	public $Currency;
	public $IPAddress;
	public $Comments;
	public $CreateDate;
	public $ModifyDate;
	public $TotalOrder;
	public $TotalAmount;
	
	public static $COLUMN_MAPPING=array
	(
		'order_id'=>'ID',
		'order_num'=>'Num',
		'user_id'=>'UserID',
		'b_title'=>'BTitle',
		'b_company_name'=>'BCompanyName',
		'b_first_name'=>'BFirstName',
		'b_last_name'=>'BLastName',
		'b_address_1'=>'BAddress1',
		'b_address_2'=>'BAddress2',
		'b_city'=>'BCity',
		'b_state'=>'BState',
		'b_country_code'=>'BCountryCode',
		'b_zip_code'=>'BZipCode',
		'b_phone_1'=>'BPhone1',
		'b_phone_2'=>'BPhone2',
		'b_fax'=>'BFax',
		's_title'=>'STitle',
		's_company_name'=>'SCompanyName',
		's_first_name'=>'SFirstName',
		's_last_name'=>'SLastName',
		's_address_1'=>'SAddress1',
		's_address_2'=>'SAddress2',
		's_city'=>'SCity',
		's_state'=>'SState',
		's_country_code'=>'SCountryCode',
		's_zip_code'=>'SZipCode',
		's_phone_1'=>'SPhone1',
		's_phone_2'=>'SPhone2',
		's_fax'=>'SFax',
		'subtotal'=>'Subtotal',
		'tax_amount'=>'TaxAmount',
		'shipping_method_id'=>'ShippingMethodID',
		'shipping_amount'=>'ShippingAmount',
		'est_delivery_date'=>'EstDeliveryDate',
		'coupon_code'=>'CouponCode',
		'coupon_amount'=>'CouponAmount',
		'reward_points_rebate'=>'RewardPointsRebate',
		'deliverer_name'=>'Deliverer',
		'total_packs'=>'TotalPacks',
		'total'=>'Total',
		'currency'=>'Currency',
		'ip_address'=>'IPAddress',
		'comments'=>'Comments',
		'c_date'=>'CreateDate',
		'm_date'=>'ModifyDate'
	);
	
	public static $RELATIONS=array
	(
		'User'=>array(self::BELONGS_TO,'UserRecord','user_id'),
		'ShippingMethod'=>array(self::BELONGS_TO,'ShippingMethodRecord','shipping_method_id'),
		'Coupon'=>array(self::BELONGS_TO,'CouponRecord','coupon_code'),
		'OrderHistories'=>array(self::HAS_MANY,'OrderHistoryRecord','order_id'),
		'Payments'=>array(self::HAS_MANY,'PaymentRecord','order_id'),
		'BCountry'=>array(self::BELONGS_TO,'CountryRecord','b_country_code'),
		'SCountry'=>array(self::BELONGS_TO,'CountryRecord','s_country_code'),
		'OrderItems'=>array(self::HAS_MANY,'OrderItemRecord','order_id')
	);

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
	
	public function generateOrderNumber($prefix="",$length=6,$number=0)
	{
		$start = 155001;
		if ($number<=0)
		{
			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = "order_id > 0";
			$number = self::finder()->count($criteria)+$start;
		}
		$arg = "%0{$length}d";
		$orderNumber = $prefix.date("dmY",time())."-".sprintf($arg, $number);
		$orderNumberExist = true;
		do {
			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = "order_num = :num";
			$criteria->Parameters[':num'] = $orderNumber;
			$orderNumberExist = self::finder()->count($criteria)>0;
			if ($orderNumberExist) {
				$orderNumber = $prefix.date("dmY",time())."-".sprintf($arg, $number+1);
			}
		}
		while($orderNumberExist);
		return $orderNumber;
	}
	
	public function save()
	{
		if ($this->ID<=0)
		{
			$this->Num = $this->generateOrderNumber();
			$this->UserID = Prado::getApplication()->User->ID;
			$this->Deliverer = "";
			$this->TotalPacks = 0;
			//$this->EstDeliveryDate = $this->estimateDeliveryDate();
			$this->CreateDate = time();
		}
		$this->ModifyDate = time();
		parent::save();
	}
	
	protected function getLatestHistory()
	{
		$sql = "select * from tbl_order_history where order_id = :id order by c_date desc limit 1";
		return OrderHistoryRecord::finder()->withOrderStatus()->findBySql($sql,array(":id"=>$this->ID));
	}
	
	public function estimateDeliveryDate($specialCase=false)
	{
		//return 0;
		/*$next2day = time()+2*24*60*60;
		while(date("N",$next2day)==6||date("N",$next2day)==7)
		{
			$next2day = $next2day + 24*60*60;
		}
		return $next2day;*/
		
		
		$today = time();
		$availDeliveryDate=array();
		/*if ($specialCase)
		{
			if (date('N',$today) <= 2 && $today <= mktime(12,0,0,date('n',$today),date('j',$today),date('Y',$today)))
			{
				$shipday = $today;
				while(date('N',$today) != 5)
				{
					$shipday = $shipday + 24*60*60;
				}
				$availDeliveryDate[] = array('day'=>$shipday,'time'=>'PM');
				$availDeliveryDate[] = array('day'=>$shipday+24*60*60,'time'=>'AM');
				$availDeliveryDate[] = array('day'=>$shipday+24*60*60,'time'=>'PM');
			}
			else 
			{
				$shipday = $today;
				while(date('N',$today) != 2)
				{
					$shipday = $shipday + 24*60*60;
				}
				$availDeliveryDate[] = array('day'=>$shipday,'time'=>'PM');
				$availDeliveryDate[] = array('day'=>$shipday+24*60*60,'time'=>'AM');
				$availDeliveryDate[] = array('day'=>$shipday+24*60*60,'time'=>'PM');
			}
			return $availDeliveryDate;
		}
		// if order date is later than midnoon, move it to the next day
		if ($today > $todayNoon) $today = $today + 24*60*60;
		
		switch(date('N',$today))
		{
			case 1:
				$availDeliveryDate[] = array('day'=>$today+2*24*60*60,'time'=>'AM');
				$availDeliveryDate[] = array('day'=>$today+2*24*60*60,'time'=>'PM');
				$availDeliveryDate[] = array('day'=>$today+4*24*60*60,'time'=>'AM');
				break;
			case 2:
				$availDeliveryDate[] = array('day'=>$today+3*24*60*60,'time'=>'PM');
				$availDeliveryDate[] = array('day'=>$today+4*24*60*60,'time'=>'AM');
				$availDeliveryDate[] = array('day'=>$today+4*24*60*60,'time'=>'PM');
				break;
			case 3:
				$availDeliveryDate[] = array('day'=>$today+2*24*60*60,'time'=>'PM');
				$availDeliveryDate[] = array('day'=>$today+3*24*60*60,'time'=>'AM');
				$availDeliveryDate[] = array('day'=>$today+3*24*60*60,'time'=>'PM');
				break;
			case 4:
				$availDeliveryDate[] = array('day'=>$today+2*24*60*60,'time'=>'AM');
				$availDeliveryDate[] = array('day'=>$today+2*24*60*60,'time'=>'PM');
				break;
			case 5:
				$availDeliveryDate[] = array('day'=>$today+4*24*60*60,'time'=>'PM');
				$availDeliveryDate[] = array('day'=>$today+5*24*60*60,'time'=>'AM');
				$availDeliveryDate[] = array('day'=>$today+5*24*60*60,'time'=>'PM');
				break;
			case 6:
				$availDeliveryDate[] = array('day'=>$today+3*24*60*60,'time'=>'PM');
				$availDeliveryDate[] = array('day'=>$today+4*24*60*60,'time'=>'AM');
				$availDeliveryDate[] = array('day'=>$today+4*24*60*60,'time'=>'PM');
				break;
			case 7:
				$availDeliveryDate[] = array('day'=>$today+3*24*60*60,'time'=>'AM');
				$availDeliveryDate[] = array('day'=>$today+3*24*60*60,'time'=>'AM');
				$availDeliveryDate[] = array('day'=>$today+5*24*60*60,'time'=>'PM');
				break;
		}*/
		
		$oneDay		= 24*60*60;
		$dayOfWeek	= date('N',$today);
		$hourOfDay	= date('G',$today);
		$tmpDate = $today+$oneDay; //add a day for more accurate
		
		/* BY ALEX UNTIL SEP-2010
		if (($dayOfWeek > 2 && $dayOfWeek < 5) || ($dayOfWeek == 5 && $hourOfDay < 15))
		{
			while (date('N',$tmpDate) != 2) $tmpDate += $oneDay;
			$availDeliveryDate[] = array('day'=>$tmpDate,'time'=>'3pm-6pm');
			while (date('N',$tmpDate) != 5) $tmpDate += $oneDay;
			$availDeliveryDate[] = array('day'=>$tmpDate,'time'=>'3pm-6pm');
		}
		else
		{
			while (date('N',$tmpDate) != 5) $tmpDate += $oneDay;
			$availDeliveryDate[] = array('day'=>$tmpDate,'time'=>'3pm-6pm');
			while (date('N',$tmpDate) != 2) $tmpDate += $oneDay;
			$availDeliveryDate[] = array('day'=>$tmpDate,'time'=>'3pm-6pm');
		}
		*/
		
		// ADD BY TOM OCT-2010
		/* 
			- Mon to Tue: Fri 5-8pm
			- Wed: Sat 10-12noon
			- From Thu to Fri 3pm: Tue 5-8pm
			- Fri after 3pm to Sun: Wed 3-6pm			
			Order on Thursday --> deliver Tuesday 5-8pm (CHOICE OF TUE AND WED)
			Order on Friday before 3pm --> deliver Tuesday 5-8pm (CHOICE OF TUE AND WED)
			Order on Friday after 3pm, Saturday, Sunday ---> deliver Wednesday 3-6pm (ONLY WED)
			Order on Monday, Tuesday ----> deliver Friday 5-8pm (CHOICE OF FRI AND SAT)
			Order on Wednesday -----> deliver Saturday 10-12noon (ONLY SAT)
		*/
		
		// 29-10-2010: change tue and fri to 3pm-7pm
		// 22-11-2010: change tue, wed and fri to 3.30pm-7pm, sat to 11am-2pm
		
		// 20-02-2011: remove saturday deliveries, order on wed will be deliverred on sat
		if (($dayOfWeek == 1) || ($dayOfWeek == 2) || ($dayOfWeek == 3))
		{
			while (date('N',$tmpDate) != 5) $tmpDate += $oneDay;
			$availDeliveryDate[] = array('day'=>$tmpDate,'time'=>'3.30pm-7pm');
			//while (date('N',$tmpDate) != 6) $tmpDate += $oneDay;
			//$availDeliveryDate[] = array('day'=>$tmpDate,'time'=>'11am-2pm');
		}
		/*else if ($dayOfWeek == 3)
		{
			while (date('N',$tmpDate) != 6) $tmpDate += $oneDay;
			$availDeliveryDate[] = array('day'=>$tmpDate,'time'=>'11am-2pm');
			//while (date('N',$tmpDate) != 2) $tmpDate += $oneDay;
			//$availDeliveryDate[] = array('day'=>$tmpDate,'time'=>'3.30pm-7pm');
		}*/
		else if (($dayOfWeek == 4) || ($dayOfWeek == 5 && $hourOfDay < 15))
		{
			while (date('N',$tmpDate) != 2) $tmpDate += $oneDay;
			$availDeliveryDate[] = array('day'=>$tmpDate,'time'=>'3.30pm-7pm');
			while (date('N',$tmpDate) != 3) $tmpDate += $oneDay;
			$availDeliveryDate[] = array('day'=>$tmpDate,'time'=>'3.30pm-7pm');
		}
		else
		{
			while (date('N',$tmpDate) != 3) $tmpDate += $oneDay;
			$availDeliveryDate[] = array('day'=>$tmpDate,'time'=>'3.30pm-7pm');
			//while (date('N',$tmpDate) != 5) $tmpDate += $oneDay;
			//$availDeliveryDate[] = array('day'=>$tmpDate,'time'=>'3.30pm-7pm');
		}
		
		return $availDeliveryDate;
	}
	
	public function validateDeliveryDate($estDate)
	{
		$next2day = time()+2*24*60*60;
		if ($estDate > $next2day && date("N",$estDate) != 6 && date("N",$estDate) != 7)
			return true; 
		else return false;
	}
}
?>
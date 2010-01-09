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
	public $CouponCode;
	public $CouponAmount;
	public $Total;
	public $Currency;
	public $IPAddress;
	public $CreateDate;
	public $ModifyDate;
	
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
		'coupon_code'=>'CouponCode',
		'coupon_amount'=>'CouponAmount',
		'total'=>'Total',
		'currency'=>'Currency',
		'ip_address'=>'IPAddress',
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
		'SCountry'=>array(self::BELONGS_TO,'CountryRecord','s_country_code')
	);

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
	
	public function generateOrderNumber($prefix="TOG")
	{
		return $prefix.strtoupper(uniqid(time()));
	}
	
	public function save()
	{
		if ($this->ID<=0)
		{
			$this->Num = $this->generateOrderNumber();
			$this->UserID = Prado::getApplication()->User->ID;
			$this->CreateDate = time();
		}
		$this->ModifyDate = time();
		parent::save();
	}
	
	protected function getLatestHistory()
	{
		$criteria = new TActiveRecordCriteria;
		$criteria->Condition = "order_id = :id and c_date = (select max(c_date) from tbl_order_history where order_id = :id)";
		$criteria->Parameters[":id"] = $this->ID;
		return OrderHistoryRecord::finder()->withOrderStatus()->find($criteria);
	}
}
?>
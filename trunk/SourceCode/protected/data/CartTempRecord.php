<?php
/**
 * Auto generated by prado-cli.php on 2009-12-09 09:55:26.
 */
class CartTempRecord extends TActiveRecord
{
	const TABLE='tbl_cart_temp';

	public $SessionID;
	public $UserID=0;
	public $BillingID=0;
	public $ShippingID=0;
	public $Subtotal=0;
	public $ShippingMethodID=0;
	public $ShippingAmount=0;
	public $CouponCode=0;
	public $CouponAmount=0;
	public $TaxAmount=0;
	public $CreateDate=0;
	
	public static $COLUMN_MAPPING=array
	(
		'session_id'=>'SessionID',
		'user_id'=>'UserID',
		'billing_id'=>'BillingID',
		'shipping_id'=>'ShippingID',
		'subtotal'=>'Subtotal',
		'shipping_method_id'=>'ShippingMethodID',
		'shipping_amount'=>'ShippingAmount',
		'coupon_code'=>'CouponCode',
		'coupon_amount'=>'CouponAmount',
		'tax_amount'=>'TaxAmount',
		'c_date'=>'CreateDate'
	);
	
	public static $RELATIONS=array
	(
		'User'=>array(self::BELONGS_TO,'UserRecord','user_id'),
		'BillingAddress'=>array(self::BELONGS_TO,'UserAddressRecord','billing_id'),
		'ShippingAddress'=>array(self::BELONGS_TO,'UserAddressRecord','shipping_id'),
		'ShippingMethod'=>array(self::BELONGS_TO,'ShippingMethodRecord','shipping_method_id'),
		'Coupon'=>array(self::BELONGS_TO,'CouponRecord','coupon_code'),
		'CartTempDetails'=>array(self::HAS_MANY,'CartTempDetailRecord','session_id')
	);
	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
	
	public function getSubtotalInSession()
	{
		$activeRecord = self::finder()->withCartTempDetails()->findByPk(Prado::getApplication()->Session->SessionID);
		if ($activeRecord instanceof CartTempRecord)
		{
			$subtotal = 0;
			foreach($activeRecord->CartTempDetails as $item)
			{
				$subtotal += $item->Subtotal;
			}
		}
		return $subtotal;
	}
}
?>
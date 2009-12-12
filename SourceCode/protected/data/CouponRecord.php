<?php
/**
 * Auto generated by prado-cli.php on 2009-12-13 01:28:48.
 */
class CouponRecord extends TActiveRecord
{
	const TABLE='tbl_coupon';

	public $ID;
	public $Code;
	public $Type;
	public $Amount;
	
	public static $COLUMN_MAPPING=array
	(
		'coupon_id'=>'ID',
		'coupon_code'=>'Code',
		'coupon_type'=>'Type',
		'coupon_amount'=>'Amount'
	);
	
	public static $RELATIONS=array
	(
		'Orders'=>array(self::HAS_MANY,'OrderRecord','coupon_code')
	);

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>
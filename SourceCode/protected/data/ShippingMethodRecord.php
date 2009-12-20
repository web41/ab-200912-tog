<?php
/**
 * Auto generated by prado-cli.php on 2009-12-13 01:32:08.
 */
class ShippingMethodRecord extends TActiveRecord
{
	const TABLE='tbl_shipping_method';

	public $ID;
	public $Alias;
	public $Name;
	public $DiscountID;
	public $Price;
	public $IsPublished;
	
	public static $COLUMN_MAPPING=array
	(
		'method_id'=>'ID',
		'method_alias'=>'Alias',
		'method_name'=>'Name',
		'discount_id'=>'DiscountID',
		'method_price'=>'Price',
		'method_publish'=>'IsPublished'
	);
	
	public static $RELATIONS=array
	(
		'Orders'=>array(self::HAS_MANY,'OrderRecord','method_id')
	);

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>
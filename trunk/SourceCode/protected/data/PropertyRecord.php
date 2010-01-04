<?php
/**
 * Auto generated by prado-cli.php on 2010-01-02 09:18:10.
 */
class PropertyRecord extends TActiveRecord
{
	const TABLE='tbl_product_property';

	public $ID;
	public $ProductID;
	public $Name;
	public $Price;
	public $InStock=99;
	
	public static $COLUMN_MAPPING=array
	(
		'prop_id'=>'ID',
		'product_id'=>'ProductID',
		'prop_name'=>'Name',
		'prop_price'=>'Price',
		'prop_in_stock'=>'InStock'
	);
	
	public static $RELATIONS=array
	(
		'Product'=>array(self::BELONGS_TO,'ProductRecord','product_id')
	);

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
	
	public function save()
	{
		if ($this->ID === null)
			$this->ID = md5(uniqid(time()));
		parent::save();
	}
}
?>
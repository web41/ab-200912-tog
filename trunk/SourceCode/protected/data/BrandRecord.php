<?php
/**
 * Auto generated by prado-cli.php on 2009-12-09 09:25:46.
 */
class BrandRecord extends TActiveRecord
{
	const TABLE='tbl_brand';

	public $ID;
	public $Alias;
	public $Name;
	public $IsPublished;
	
	public static $COLUMN_MAPPING=array
	(
		'brand_id'=>'ID',
		'brand_alias'=>'Alias',
		'brand_name'=>'Name',
		'brand_publish'=>'IsPublished',
	);
	
	public static $RELATIONS=array
	(
		'Products'=>array(self::HAS_MANY,'ProductRecord','brand_id')
	);

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
	
	public function save()
	{
		$this->Alias = String::removeAccents(strlen($this->Alias) > 0 ? $this->Alias : $this->Name);
		parent::save();
	}
	
	public function getAllItems($publishOnly=true)
	{
		$criteria = new TActiveRecordCriteria;
		$criteria->Condition = "brand_id > 0";
		if ($publishOnly) $criteria->Condition .= " and brand_publish = 1";
		$criteria->OrdersBy["brand_name"] = "asc";
		return self::finder()->findAll($criteria);
	}
}
?>
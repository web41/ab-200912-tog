<?php
/**
 * Auto generated by prado-cli.php on 2009-12-13 01:29:24.
 */
class ManufacturerRecord extends TActiveRecord
{
	const TABLE='tbl_manufacturer';

	public $ID;
	public $Alias;
	public $Name;
	public $Description;
	public $Email;
	public $Url;
	
	public static $COLUMN_MAPPING=array
	(
		'mf_id'=>'ID',
		'mf_alias'=>'Alias',
		'mf_name'=>'Name',
		'mf_desc'=>'Description',
		'mf_email'=>'Email',
		'mf_url'=>'Url'
	);
	
	public static $RELATIONS=array
	(
		'Products'=>array(self::HAS_MANY,'ProductRecord','mf_id')
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
	
	public function getAllItems()
	{
		$criteria = new TActiveRecordCriteria;
		$criteria->Condition = "mf_id > 0";
		$criteria->OrdersBy["mf_name"] = "asc";
		return self::finder()->findAll($criteria);
	}
}
?>
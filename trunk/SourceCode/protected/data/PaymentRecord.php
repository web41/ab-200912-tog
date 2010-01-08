<?php
/**
 * Auto generated by prado-cli.php on 2009-12-13 01:30:40.
 */
class PaymentRecord extends TActiveRecord
{
	const TABLE='tbl_payment';

	public $ID;
	public $PaymentMethodID;
	public $OrderID;
	public $Amount;
	public $Status;
	public $CreateDate;
	public $ModifyDate;
	
	public static $COLUMN_MAPPING=array
	(
		'payment_id'=>'ID',
		'payment_method_id'=>'PaymentMethodID',
		'order_id'=>'OrderID',
		'payment_amount'=>'Amount',
		'payment_status'=>'Status',
		'c_date'=>'CreateDate',
		'm_date'=>'ModifyDate'
	);
	
	public static $RELATIONS=array
	(
		'PaymentMethod'=>array(self::BELONGS_TO,'PaymentMethodRecord','payment_method_id'),
		'Order'=>array(self::BELONGS_TO, 'OrderRecord','order_id')
	);

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
	
	public function getAllItems()
	{
		$criteria = new TActiveRecordCriteria;
		$criteria->Condition = "payment_id > 0";
		return self::finder()->findAll($criteria);
	}
	
	public function save()
	{
		if ($this->ID<=0)
		{
			$this->CreateDate = time();
		}
		$this->ModifyDate = time();
		parent::save();
	}
}
?>
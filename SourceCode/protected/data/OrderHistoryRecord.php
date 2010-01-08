<?php
/**
 * Auto generated by prado-cli.php on 2009-12-13 01:29:54.
 */
class OrderHistoryRecord extends TActiveRecord
{
	const TABLE='tbl_order_history';

	public $ID;
	public $OrderID;
	public $StatusCode;
	public $Comments;
	public $CreateDate;
	
	public static $COLUMN_MAPPING=array
	(
		'history_id'=>'ID',
		'order_id'=>'OrderID',
		'order_status_code'=>'StatusCode',
		'comments'=>'Comments',
		'c_date'=>'CreateDate'
	);
	
	public static $RELATIONS=array
	(
		'Order'=>array(self::BELONGS_TO,'OrderRecord','order_id'),
		'OrderStatus'=>array(self::BELONGS_TO, 'OrderStatusRecord','order_status_code')
	);

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
	
	public function save()
	{
		if ($this->ID<=0)
		{
			$this->CreateDate = time();
		}
		parent::save();
	}
}
?>
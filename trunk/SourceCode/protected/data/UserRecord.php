<?php
/**
 * Auto generated by prado-cli.php on 2009-12-13 01:32:20.
 */
class UserRecord extends TActiveRecord
{
	const TABLE='tbl_user';

	public $ID;
	public $Username;
	public $Password;
	public $Email;
	public $UserTypeID;
	public $Credits;
	public $Status;
	public $Hash;
	public $IPAddress;
	public $LastVisitIP;
	public $LastVisitDate;
	public $CreateDate;
	public $ModifyDate;
	
	public static $COLUMN_MAPPING=array
	(
		'user_id'=>'ID',
		'user_name'=>'Username',
		'user_pwd'=>'Password',
		'user_email'=>'Email',
		'user_type_id'=>'UserTypeID',
		'user_credits'=>'Credits',
		'user_status'=>'Status',
		'user_hash'=>'Hash',
		'ip_address'=>'IPAddress',
		'last_visit_ip'=>'LastVisitIP',
		'last_visit_date'=>'LastVisitDate',
		'c_date'=>'CreateDate',
		'm_date'=>'ModifyDate'
	);
	
	public static $RELATIONS=array
	(
		'UserAddresses'=>array(self::HAS_MANY,'UserAddressRecord','user_id'),
		'Orders'=>array(self::HAS_MANY,'OrderRecord','user_id'),
		'UserType'=>array(self::BELONGS_TO,'UserTypeRecord','user_type_id')
	);

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
	
	public function save()
	{
		if ($this->ID <= 0)
		{
			$this->CreateDate = time();
		}
		$this->ModifyDate = time();
		parent::save();
	}
}
?>
<?php
/**
 * Auto generated by prado-cli.php on 2009-12-13 01:32:31.
 */
class UserAddressRecord extends TActiveRecord
{
	const TABLE='tbl_user_address';

	public $ID;
	public $UserID;
	public $Type;
	public $IsDefault;
	public $CompanyName;
	public $Title;
	public $FirstName;
	public $LastName;
	public $Address1;
	public $Address2;
	public $City;
	public $State;
	public $CountryCode;
	public $ZipCode;
	public $Phone1;
	public $Phone2;
	public $Fax;
	public $CreateDate;
	public $ModifyDate;
	
	public static $COLUMN_MAPPING=array
	(
		'address_id'=>'ID',
		'user_id'=>'UserID',
		'address_type'=>'Type',
		'is_default'=>'IsDefault',
		'company_name'=>'CompanyName',
		'title'=>'Title',
		'first_name'=>'FirstName',
		'last_name'=>'LastName',
		'address_1'=>'Address1',
		'address_2'=>'Address2',
		'city'=>'City',
		'state'=>'State',
		'country_code'=>'CountryCode',
		'zip_code'=>'ZipCode',
		'phone_1'=>'Phone1',
		'phone_2'=>'Phone2',
		'fax'=>'Fax',
		'c_date'=>'CreateDate',
		'm_date'=>'ModifyDate'
	);
	
	public static $RELATIONS=array
	(
		'User'=>array(self::BELONGS_TO,'UserRecord','user_id'),
		'Country'=>array(self::BELONGS_TO,'CountryRecord','country_code')
	);

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>
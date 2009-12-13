<?php

class TOGUser extends TDbUser
{
	private $_ID;
	private $_Username;
	private $_Password;
	private $_Email;
	private $_UserTypeID;
	private $_Credits;
	private $_Status;
	private $_Hash;
	private $_IPAddress;
	private $_LastIP;
	private $_LastDate;
	private $_CreateDate;
	private $_ModifyDate;
	
	public function validateUser($username, $password)
	{
		//$criteria = new TActiveRecordCriteria;
		//$criteria->Condition = 'user_name = :user and user_pwd = :pwd';
		//$criteria->Parameters[':user'] = $username;
		//$criteria->Parameters[':pwd'] = md5($password);
		//return (UserAccount::finder()->count($criteria) == 1) ? true : false;
	}
	
	public function createUser($username)
	{
		//$user_account = UserAccount::finder()->findByuser_name($username);
		//if ($user_account instanceof UserAccount)
		//{
		//	$user = new BSJUser($this->Manager);
		//	$user->Name = $user_account->user_name;
		//	$user->Roles = UserType::finder()->findByPk($user_account->user_type_id)->user_type_name;
		//	$user->IsGuest = false;
		//	return $user;
		//}
	}
}

?>
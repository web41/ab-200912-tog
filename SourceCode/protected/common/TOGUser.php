<?php

class TOGUser extends TDbUser
{
	public $ID;
	public $Username;
	public $Password;
	public $Email;
	public $UserTypeID;
	public $Credits;
	public $Status;
	public $Hash;
	public $IPAddress;
	public $LastIP;
	public $LastDate;
	public $CreateDate;
	public $ModifyDate;
	
	public function validateUser($email, $password)
	{
		return (UserRecord::finder()->findByuser_emailAnduser_pwd($email,md5($password)) instanceof UserRecord);
	}
	
	public function createUser($username)
	{
		$activeRecord = UserRecord::finder()->findByuser_name($username);
		if ($user instanceof UserRecord)
		{
			$user = new TOGUser($this->Manager);
			$user->Name = $activeRecord->Username;
			$user->ID = $activeRecord->ID;
			$user->Username = $activeRecord->Username;
			$user->Password = $activeRecord->Password;
			$user->Email = $activeRecord->Email;
			$user->UserTypeID = $activeRecord->UserTypeID;
			$user->Credits = $activeRecord->Credits;
			$user->Status = $activeRecord->Status;
			$user->Hash = $activeRecord->Hash;
			$user->IPAddress = $activeRecord->IPAddress;
			$user->LastIP = $activeRecord->LastIP;
			$user->LastDate = $activeRecord->LastDate;
			$user->CreateDate = $activeRecord->CreateDate;
			$user->ModifyDate = $activeRecord->ModifyDate;
			$user->Roles = $activeRecord->UserType->Name;
			$user->IsGuest = false;
			return $user;
		}
	}
}

?>
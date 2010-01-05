<?php

class TOGUser extends TDbUser
{
	public function getID()
	{
		return $this->getState("ID",0);
	}
	public function setID($value)
	{
		$this->setState("ID",TPropertyValue::ensureInteger($value),0);
	}
	
	public function getUsername()
	{
		return $this->getState("Username","");
	}
	public function setUsername($value)
	{
		$this->setState("Username",$value,"");
	}
	
	public function getPassword()
	{
		return $this->getState("Password","");
	}
	public function setPassword($value)
	{
		$this->setState("Password",$value,"");
	}
	
	public function getEmail()
	{
		return $this->getState("Email","");
	}
	public function setEmail($value)
	{
		$this->setState("Email",$value,"");
	}
	
	public function getFirstName()
	{
		return $this->getState("FirstName","");
	}
	public function setFirstName($value)
	{
		$this->setState("FirstName",$value,"");
	}
	
	public function getLastName()
	{
		return $this->getState("LastName","");
	}
	public function setLastName($value)
	{
		$this->setState("LastName",$value,"");
	}
	
	public function getUserTypeID()
	{
		return $this->getState("UserTypeID",0);
	}
	public function setUserTypeID($value)
	{
		$this->setState("UserTypeID",TPropertyValue::ensureInteger($value),0);
	}
	
	public function getCredits()
	{
		return $this->getState("Credits",0);
	}
	public function setCredits($value)
	{
		$this->setState("Credits",TPropertyValue::ensureInteger($value),0);
	}
	
	public function getStatus()
	{
		return $this->getState("Status",0);
	}
	public function setStatus($value)
	{
		$this->setState("Status",TPropertyValue::ensureInteger($value),0);
	}
	
	public function getHash()
	{
		return $this->getState("Hash","");
	}
	public function setHash($value)
	{
		$this->setState("Hash",$value,"");
	}
	
	public function getIPAddress()
	{
		return $this->getState("IPAddress","");
	}
	public function setIPAddress($value)
	{
		$this->setState("IPAddress",$value,"");
	}
	
	public function getLastVisitIP()
	{
		return $this->getState("LastVisitIP","");
	}
	public function setLastVisitIP($value)
	{
		$this->setState("LastVisitIP",$value,"");
	}
	
	public function getLastVisitDate()
	{
		return $this->getState("LastVisitDate",0);
	}
	public function setLastVisitDate($value)
	{
		$this->setState("LastVisitDate",TPropertyValue::ensureInteger($value),0);
	}
	
	public function getCreateDate()
	{
		return $this->getState("CreateDate",0);
	}
	public function setCreateDate($value)
	{
		$this->setState("CreateDate",TPropertyValue::ensureInteger($value),0);
	}
	
	public function getModifyDate()
	{
		return $this->getState("ModifyDate",0);
	}
	public function setModifyDate($value)
	{
		$this->setState("ModifyDate",TPropertyValue::ensureInteger($value),0);
	}
	
	public function validateUser($email, $password)
	{
		return (UserRecord::finder()->findByuser_emailAnduser_pwd($email,md5($password)) instanceof UserRecord);
	}
	
	public function createUser($username)
	{
		$activeRecord = UserRecord::finder()->findByuser_name($username);
		if ($activeRecord instanceof UserRecord)
		{
			$user = new TOGUser($this->Manager);
			$user->Name = $activeRecord->Username;
			$user->ID = $activeRecord->ID;
			$user->Username = $activeRecord->Username;
			$user->Password = $activeRecord->Password;
			$user->Email = $activeRecord->Email;
			$user->FirstName = $activeRecord->FirstName;
			$user->LastName = $activeRecord->LastName;
			$user->UserTypeID = $activeRecord->UserTypeID;
			$user->Credits = $activeRecord->Credits;
			$user->Status = $activeRecord->Status;
			$user->Hash = $activeRecord->Hash;
			$user->IPAddress = $activeRecord->IPAddress;
			$user->LastVisitIP = $activeRecord->LastVisitIP;
			$user->LastVisitDate = $activeRecord->LastVisitDate;
			$user->CreateDate = $activeRecord->CreateDate;
			$user->ModifyDate = $activeRecord->ModifyDate;
			$user->Roles = $activeRecord->UserType->Name;
			$user->IsGuest = false;
			return $user;
		}
	}
}

?>
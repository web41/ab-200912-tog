<?php

class TOGAuthManager extends TAuthManager
{
	public function logout()
	{
		$criteria = new TActiveRecordCriteria;
		$criteria->Condition = "session_id = :id and user_id = :user";
		$criteria->Parameters[':id'] = $this->Application->Session->SessionID;
		$criteria->Parameters[':user'] = $this->Application->User->ID;
		CartTempRecord::finder()->deleteAll($criteria);
		$userOnlineCounter = TPropertyValue::ensureInteger(Prado::getApplication()->getGlobalState("UserOnlineCounter",0));
		Prado::getApplication()->setGlobalState("UserOnlineCounter",$userOnlineCounter-1);
		parent::logout();
	}
}

?>
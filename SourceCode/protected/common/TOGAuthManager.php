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
		$userOnlineCount = TPropertyValue::ensureInteger(App::get("UserOnlineCount"));
		App::set("UserOnlineCount",($userOnlineCount-1));
		parent::logout();
	}
}

?>
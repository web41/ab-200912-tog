<?php

class TOGAuthManager extends TAuthManager
{
	public function logout()
	{
		$criteria = new TActiveRecordCriteria;
		$criteria->Condition = "session_id = ':id' and user_id = ':user'";
		$criteria->Parameters[':id'] = $this->Application->Session->ID;
		$criteria->Parameters[':user'] = $this->Application->User->ID;
		CartTempRecord::finder()->deleteAll($criteria);
		CartTempDetailRecord::finder()->deleteAll($criteria);
		parent::logout();
	}
}

?>
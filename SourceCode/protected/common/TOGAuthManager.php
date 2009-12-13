<?php

class TOGAuthManager extends TAuthManager
{
	public function logout()
	{
		// we need hold session even if user logout.
		// alex: i really dont recommend this.
		//$session_id = $this->Application->Session['CART_SESSION_ID'];
		//var_dump($session_id);
		
		// delete cart temp
		//$criteria = new TActiveRecordCriteria;
		//$criteria->Condition = 'session_id = :id';
		//$criteria->Parameters[':id'] = $this->Application->Session['CART_SESSION_ID'];
		//CartTemp::finder()->deleteAll($criteria);
		//CartTempDetail::finder()->deleteAll($criteria);
		//parent::logout();
		//$this->Application->Session->open();
		//$this->Application->Session['CART_SESSION_ID'] = $session_id;
		//var_dump($this->Application->Session['CART_SESSION_ID']);
	}
}

?>
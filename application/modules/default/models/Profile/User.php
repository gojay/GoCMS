<?php
class Model_Profile_User extends My_Db_Profile_Abstract
{
	public function __construct($db, $user_id = null)
	{
		parent::__construct($db, 'user_profiles');
		
		if($user_id > 0)
			$this->setParentId($user_id);
	}
	
	public function setParentId($user_id)
	{
		$filters = array('id' => (int) $user_id);
		$this->_filters = $filters;
	}
}
?>
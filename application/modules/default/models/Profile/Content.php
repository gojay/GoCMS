<?php
class Model_Profile_Content extends My_Db_Profile_Abstract
{
	public function __construct($db, $content_id = null)
	{
		parent::__construct($db, 'go_content_profiles');
		
		if($content_id > 0)
			$this->setParentId($content_id);
	}
	
	public function setParentId($content_id)
	{
		$filters = array('profile_id' => $content_id);
		$this->_filters = $filters;
	}
}
?>
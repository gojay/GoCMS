<?php
class Model_User extends My_Db_Object_Abstract
{
	public function __construct($db)
	{
		parent::__construct($db, 'snap_users', 'user_id');
		
		$this->add('username');
		$this->add('password');
		$this->add('firstname');
		$this->add('lastname');
		$this->add('email');
		$this->add('role');
	}
	
	protected function preInsert()
	{
		$this->password = md5($this->password);
		return true;
	}
	
	public static function GetCount($db, $options = array())
	{
		$select = self::_GetBaseQuery($db, $options);
		$select->from(null, 'count(*)');
		return $db->fetchOne($select);
	}
	
	public static function GetUsers($db, $options = array())
	{
		$select = self::_GetBaseQuery($db, $options);
		$data = $db->fetchAll($select);		
		
		return parent::BuildMultiple($db, __CLASS__, $data);
	}
	
	private static function _GetBaseQuery($db, $options)
	{
		// initialize the options
		$defaults = array(
			'id'		=> array(),
			'order'		=> 'user_id ASC'
		);

		foreach ($defaults as $k => $v) {
			$options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
		}
		
		$select = $db->select();
		$select->from('users', array('*'));
		
		if(count($options['id']) > 0)
			$select->where('user_id IN (?)', $options['id']);
		
		$select->order($options['order']);			
		return $select;
	}
}
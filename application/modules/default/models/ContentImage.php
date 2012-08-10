<?php
class Model_ContentImage extends My_Db_Object_Abstract
{
	protected $_upload;
	
	public function __construct($db)
	{
		parent::__construct($db, 'go_content_images', 'image_id');
		
		$this->add('content_id');
		$this->add('filename');
		
		$this->_upload = Model_UploadImage::GetOptions();
	}
	
	public function preDelete()
	{
		$file_path = $this->_upload->options['upload_dir'] . '/' . $this->filename;
        $thumb_path = $this->_upload->options['image_versions']['thumbnail']['upload_dir'] . '/' . $this->filename;
        $success = is_file($file_path) && is_file($thumb_path) && unlink($file_path) && unlink($thumb_path);
		return $success;
	}
	
	public static function GetCount($db, $options = array())
	{
		$select = self::_GetBaseQuery($db, $options);
		$select->from(null, 'count(*)');
		return $db->fetchOne($select);
	}
	
	public static function GetImages($db, $options = array())
	{
		$select = self::_GetBaseQuery($db, $options);
		$data = $db->fetchAll($select);		
		return parent::BuildMultiple($db, __CLASS__, $data);	
	}
	
	private static function _GetBaseQuery($db, $options)
	{
		$defaults = array(
			'content_id' => array(),
			'order'		 => 'image_id ASC'
		);

		foreach ($defaults as $k => $v) {
			$options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
		}
		
		$select = $db->select();
		$select->from('go_content_images', array('*'));
		
		if(count($options['content_id']) > 0)
			$select->where('content_id IN (?)', $options['content_id']);
		
		$select->order($options['order']);			
		return $select;
	}
}
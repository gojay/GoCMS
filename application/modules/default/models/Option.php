<?php
class Model_Option extends My_Db_Object_Abstract
{
	private $_options = array();
	
	protected $old_name;
	
	public function __construct($db)
	{
		parent::__construct($db, 'go_options', 'option_id');

		$this->add('option_name');
		$this->add('option_value');
	}
	
	/**
	 * postLoad
	 * 
	 * setelah proses load option
	 */
	protected function postLoad()
	{
		$this->old_name = $this->option_name;
	}

	/**
	 * preInsert
	 * 
	 * sebelum proses insert option
	 * buat slug dari nama
	 */
	protected function preInsert()
	{
		$this->option_name = $this->_generateSlug($this->option_name, 'option_name', '_');
		return true;
	}
	
	/**
	 * preUpdate
	 */
	protected function preUpdate()
	{
		if( strtolower($this->option_name) != strtolower($this->old_name) )
		{
			$this->option_name = $this->_generateSlug( $this->option_name, 'option_name', '_' );
		}
		return true;
	}
	
	/**
	 * saveOption
	 * 
	 * @param array
	 * @return boolean 
	 */
	public function saveOption( $data )
	{
		if( !is_array( $data ) )
			return false;
		
		// profile
		if( isset($data['option_profile_id']) )
		{
			$user = new Model_User ($this->_db );
			$user->load(1); // admin
			$user->username = $data['username'];
			if( '' !== $data['password'] )
				$user->password = md5( $data['password'] );
			$user->firstname = $data['firstname'];
			$user->lastname = $data['lastname'];
			$user->email = $data['email'];
			return $user->save();
		} else {
			//if(isset($data['g_password'])){
			//	$g_password = array('g_password' => self::SetEncrypt( $data['g_password'] ));		
			//}
			// struktur array option, key dan value
			$options = array(
				'option_general' => array(
					'option_id' => $data['option_general_id'],
					'data' => array(
						'email' => $data['email'],
						'show_news' => $data['show_news'],
						'show_portfolios' => $data['show_portfolios'],
						'front_page' => $data['front_page'],
						'news_page' => $data['news_page'],
						'portfolios_page' => $data['portfolios_page'],
						'g_mail' => $data['g_mail'],
						'g_mail' => $data['g_mail'],
						'g_pass' => $data['g_pass'],
						'ga_profile_id' => $data['ga_profile_id'],
						'rss_title' => $data['rss_title'],
						'rss' => $data['rss']
					)
				),
				'option_social' => array(
					'option_id' => $data['option_social_id'],
					'data' => array(
						'facebook' => $data['facebook'],
						'twitter' => $data['twitter'],
						//'twitter_access_token' => $data['twitter_access_token'],
						//'twitter_access_token_secret' => $data['twitter_access_token_secret'],
						//'twitter_count'	=> $data['twitter_count'],
						'linkedin'	=> $data['linkedin']
					)
				)
			);
			
			// looping untuk update option
			foreach($options as $option)
			{
				$option_id = $option['option_id'];
				$option_value = serialize( $option['data'] );
				// update
				if( !empty($option_id) && $option_id !== '' ){
					$this->updateOption( $option_value, $option_id);
				}
				
			}

			return true;
		}
	}
	
	/**
	 * updateOption
	 * 
	 * @param string
	 * @param int
	 * @return boolean
	 */
	public function updateOption( $value, $option_id )
	{
		return $this->_db->update('go_options',
							array('option_value' => $value),
                            'option_id = ' . $option_id);
	}
	
	/**
	 * populate
	 * untuk populate form option
	 * 
	 * @param string, default 'option'
	 * @return array
	 */
	public function populate( $key = 'option' )
	{
		$data = array();
		$options = self::GetOptions($this->_db, array( 'key' => $key ));
		if($options)
		{
			foreach ($options as $option) 
			{
				$data[$option['name'].'_id'] = $option['id'];
				if($option['value'])
				{
					foreach ($option['value'] as $k => $v)
					{
						$data[$k] = $v;
					}
				}
			}	
		}
		
		return $data;
	}
	
	/**
	 * getOption
	 * ambil option, berdasarkan key dan option_name
	 * 
	 * @param $key, search tipe option_name LIKE option, gallery, dst
	 * @param $value
	 * @return array
	 */
	public function getOption( $key, $value )
	{
		// ambil array
		$options = self::GetOptions($this->_db, array( 'key' => $key ));
		// search array (array_search)
		$search = self::_array_search( $options, 'name', $value );
		// hilangkan array index
	    $data = array();
		foreach ($search as $value) {
			$data = $value;
		}
	    return $data;
	}
	
	/**
	 * GetOptions
	 * Fecth all option kedalam array
	 * 
	 * @param db
	 * @param option
	 * @return array
	 */
	public static function GetOptions($db, $options = array())
	{
		$select = self::_GetBaseQuery($db, $options);
		$data = $db->fetchAll($select);
		$options = parent::BuildMultiple($db, __CLASS__, $data);
		
		$array = array();
		// rubah kedalam array
		foreach($options as $option){
			$array[] = array(
				'id' => $option->getId(),
				'name' => $option->option_name,
				'value' => unserialize( $option->option_value ),
			);
		}
		
		return $array;
	}

	public static function SetEncrypt( $string )
	{
		$encrypt = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5( KEY ), $string, MCRYPT_MODE_CBC, md5(md5( KEY ))));
		return $encrypt;
	}
	
	/**
	 * ========================================================== *
	 * ========================	GALLERY ========================= *
	 * ========================================================== *
	 */
	
	/**
	 * updateGallery
	 * 
	 * @param array items
	 * @param string preffix, default = 'gallery-'
	 * @return boolean
	 */
	public function updateGallery( array $items, $preffix = 'gallery-' )
	{
		$values = array();
		foreach($items as $name){
			// ambil id, replace preffix
			$id = str_replace($preffix, '', $name);
			// set dalam array
			$values[] = $id;
		}
		
		// ambil data option gallery_home		
		$data = $this->getOption( 'gallery', 'gallery_home' );	
		// ambil data id	
		$option_id = $data['id'];
		// serialize values
		$option_value = serialize( $values );
		
		return $this->updateOption( $option_value, $option_id);
	}
	
	/**
	 * removeGallery
	 * 
	 * @param string $name
	 * @param string preffix, default = 'gallery-'
	 * @return boolean
	 */
	public function removeGallery( $name, $preffix = 'gallery-' )
	{
		$gallery_id = str_replace($preffix, '', $name);
		// ambil data option gallery_home		
		$data = $this->getOption( 'gallery', 'gallery_home' );	
		// hapus option value
		$value = array_diff( $data['value'], array( $gallery_id ) );
		// data 
		$option_id = $data['id'];
		$option_value = serialize( $value );
		
		return $this->updateOption( $option_value, $option_id);
	}
	
	/**
	 * ========================================================== *
	 * ========================= OFFICE ========================= *
	 * ========================================================== *
	 */
	
	/**
	 * addOffice
	 * tambah contact office
	 * 
	 * @param array
	 * @return boolean
	 */
	public function addOffice( $values )
	{
		// ambil data option_office
		if( !$this->load( 'option_office', 'option_name' ) )
			return false;
		
		$data = unserialize($this->option_value);
		// array merge values
		$option_value = array_merge( $data, $values ) ;
		// prepare untuk update option
		$option_id = $this->getId();
		$option_value = serialize( $option_value );
		// update option
		return $this->updateOption( $option_value, $option_id );
	}
	
	/**
	 * replaceOffice
	 * update option value dengan array_replace
	 * 
	 * @param array
	 * @return boolean
	 */
	public function replaceOffice( $index, $replace )
	{
		// ambil data option_office
		if( !$this->load( 'option_office', 'option_name' ) )
			return false;
		
		$values = unserialize($this->option_value);
		
		// buat replace dengan index dan array replace
		$replacements = array(
			(int)$index => $replace
		);
		// proses array replace
		$replacedvalues = self::_array_replace( $values, $replacements );
		
		// prepare untuk update option
		$option_id = $this->getId();
		$option_value = serialize( $replacedvalues );
		// update option
		return $this->updateOption( $option_value, $option_id );
	}
	
	/**
	 * removeOffice
	 * hapus option values sesuai index
	 * 
	 * @param int 
	 * @return boolean
	 */
	public function removeOffice( $index )
	{
		// ambil data option_office
		if( !$this->load( 'option_office', 'option_name' ) )
			return false;
		
		$values = unserialize($this->option_value);
		// hapus array sesuai index
		unset( $values[$index] );
		// reorder index array
		$values = array_values($values);
		
		// prepare untuk update option
		$option_id = $this->getId();
		$option_value = serialize( $values );
		// update option
		return $this->updateOption( $option_value, $option_id );
	}
	
	/**
	 * ========================================================== *
	 * ========================	WIDGETS ========================= *
	 * ========================================================== *
	 */
	
	/**
	 * _getItem
	 * parsing item
	 * 
	 * @param string
	 * @return array
	 */
	protected function _getItem($item, $preffix = 'widget_')
	{
		// pisahkan widget dan position
		// ex: widget_4-1
		// w :widget_4 ; p:1
		list($w, $p) = explode('-', $item);
		// ambil id widget, replace preffix(widget_)
		$id = str_replace($preffix, '', $w);
		
		$item = array(
			'id' => $id,
			'pos' => $p
		);
		return $item;
	}
	
	/**
	 * saveWidget
	 * 
	 * @param string, widget id
	 * @param array, data
	 * @param string, action
	 * @return bool, save()
	 */
	public function saveWidget( $widget_id, $action, $data = array() )
	{
		$item = $this->_getItem( $widget_id );
		// load widget
		$this->load( $item['id'] );
		// unserialize value
		$value = unserialize($this->option_value);
		// aksi
		switch($action){
			// tambah data widget
			case 'add':
				// isi data awal	
				$data = array(
					'data' => array(
						$item['pos'] => array(
							'title' => $data['title'],
							//'count' => $data['count'],
							//'menu'  => $data['menu'],
							'value'  => $data['value']
						)
					)
				);
		
				// simpan array assoc dgn array_merge_recursive
				if( !$value = array_merge_recursive( $value, $data ))
					return false;
				break;
			
			// update/replace data widget	
			case 'update':
				// pos : index widget data
				$index = $item['pos'];
				// cek widget data
				// jika widget data tidak ada 
				// jikapun ada, tetapi widget data index tidak ditemukan
				// return false
				if( !is_array($value['data']) || !$value['data'][$index] )
					return false;
					
				// update data
				$replacements = array(
					$index => array(
						'title' => $data['title'],
						//'count' => $data['count'],
						//'menu'  => $data['menu'],
						'value'  => $data['value']
					)
				);
				$replace = self::_array_replace( $value['data'], $replacements );
				// replace data
				$replacements = array(
					'data' => $replace
				);
				$value = self::_array_replace( $value, $replacements );
				break;
				
			case 'delete':
				// pos : index widget data
				$index = $item['pos'];
				// cek widget data
				// jika widget data tidak ada 
				// jikapun ada, tetapi widget data index tidak ditemukan
				// return false
				if( !is_array($value['data']) || !$value['data'][$index] )
					return false;
				// jumlah widget data
				$count = count($value['data']);
				// jika jumlah widget data == 1, hapus widget data
				if( $count == 1 )
					unset($value['data']);
				else 
					unset($value['data'][$index]);
				
				break;
		}
		
		// serialize untuk option_value
		$this->option_value = serialize($value);
		// save
		return $this->save();
	}
	
	/**
	 * sortWidget
	 * mengurutkan widget
	 * 
	 * @param array items
	 * @param string position
	 * @return array, save()
	 */
	public function sortWidget( $items, $widget_name )
	{
		$availablewidgets = self::GetWidgets($this->_db, array( 'key' => 'widget' ));
		
		// ambil register widgets
		$this->load('register_widgets', 'option_name');
		$widgets = unserialize($this->option_value);	
		
		if( is_array($items) )
		{
			/* sort widget	*/
			foreach( $items as $item )
			{
				$item = $this->_getItem( $item );
				// search array widget sesuai id-nya (dengan fungsi array_search)
				$_widgets = self::_array_search( $availablewidgets, 'id', $item['id'] );
				// masukan kedalam array (dengan penamaannya "nama_widget-order")
				$data[] = $_widgets[0]['name'].'-'.$item['pos'];
			}
			$replacements = array(
				$widget_name => $data
			);
			
		} else {
			/* kosongkan widget value */
			$replacements = array(
				$widget_name => array()
			);
		}
		
		// update dengan array_replace
		$widget_values = self::_array_replace( $widgets, $replacements );
		// serialize untuk option_value
		$this->option_value = serialize($widget_values);
		// save
		return $this->save();	
	}
	
	/**
	 * GetWidgets
	 * ambil semua widget
	 * 
	 * @param DB
	 * @param array
	 * @return array 
	 */
	public static function GetWidgets($db)
	{
		$select = self::_GetBaseQuery($db, array( 'key' => 'widget' ));
		$data = $db->fetchAll($select);
		$options = parent::BuildMultiple($db, __CLASS__, $data);
		
		$widgets = array();
		foreach($options as $option)
		{
			$value = unserialize($option->option_value);
			$identity = array(
				'id' 			=> $option->getId(),
				'name' 			=> $option->option_name,
			);
			$widget = array_merge($value, $identity);
			// KEY : nama widget
			$widgets[$option->option_name] = $widget;
		}
		
		return $widgets;
	}
	
	/**
	 * ========================================================== *
	 * ========================= MENUS ========================== *
	 * ========================================================== *
	 */
	
	/**
	 * GetMenus
	 * ambil semua menu
	 * 
	 * @param DB
	 * @return array 
	 */
	public static function GetMenus($db)
	{
		$select = self::_GetBaseQuery($db, array( 'key' => 'menu', 'order' => 'option_id DESC' ));
		$data = $db->fetchAll($select);
		$options = parent::BuildMultiple($db, __CLASS__, $data);
		
		$menus = array();
		foreach($options as $option)
		{
			$value = unserialize($option->option_value);
			// KEY : nama widget
			$menus[$option->option_name] = $value;
		}
		
		return $menus;
	}
	
	/**
	 * _GetBaseQuery
	 * basis query
	 * 
	 * @param db
	 * @param array
	 * @return string
	 */
	private static function _GetBaseQuery($db, $options)
	{
		// initialize the options
		$defaults = array(
			'in'	=> array(),	
			'key'	=> null,
			'order'	=> null	
		);

		foreach ($defaults as $k => $v) {
			$options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
		}
		
		$select = $db->select();
		$select->from('go_options', '*');
		
		// WHERE IN
		if (count($options['in'])){
			$select->where('option_id IN (?)', $options['in']);
		}
		// LIKE 
		if (!is_null($options['key'])){
			$select->where('option_name LIKE (?) AND option_name != "register_widgets"', strtolower($options['key']).'%');
		}
		// ORDER 
		if (!is_null($options['order'])){
			$select->order($options['order']);
		}
		return $select;
	}
	
	/**
	 * _array_search
	 * for PHP <=5.3
	 * 
	 * @param array
	 * @param string
	 * @param string
	 * @return array
	 */
	public static function _array_search($array, $key, $value)
	{
		$results = array();

		if (is_array($array))
		{
			if (isset($array[$key]) && $array[$key] == $value)
				$results[] = $array;

			foreach ($array as $subarray)
				$results = array_merge($results, self::_array_search($subarray, $key, $value));
		}

		return $results;
	}
	
	/**
	 * _array_replace
	 * for PHP <=5.3
	 * 
	 * @param args
	 * @return array
	 */
	public static function _array_replace( )
	{
		$numArgs = func_num_args();
        $argList = func_get_args();
 
        if( $numArgs == 1 ){ return $argList[0]; }
        $toBeReplacedArray = $argList[0];
		
		if( !function_exists( 'array_replace' ) )
        {
            for( $i = 1; $i < $numArgs ; $i++ ){
                foreach( $argList[$i] as $key => $value ){
                    $toBeReplacedArray[$key] = $value;
                }
            }
        } else {
            //normal Flux array_replace()
            for( $i = 1; $i < $numArgs ; $i++ ){
                $toBeReplacedArray = array_replace( $toBeReplacedArray, $argList[$i] );
            }
        }
		return $toBeReplacedArray;
	}
}
?>
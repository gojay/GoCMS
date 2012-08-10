<?php
class Model_Content extends My_Db_Object_Abstract
{
	public $profile;
	public $categories;
	public $tags;
	public $term;
	public $content_categories, $content_tags, $images = array();
	public $contentImage;
	
	protected $old_name;
	protected $old_slug;
	
	const STATUS_DRAFT = 'D';
	const STATUS_LIVE  = 'L';
	
	public function __construct($db)
	{
		parent::__construct($db, 'go_contents', 'content_id');
		
		$this->add('content_parent', 0);
		$this->add('content_type');
		$this->add('content_slug');
		$this->add('content_name');
		$this->add('content_image');
		$this->add('content_excerpt');
		$this->add('content_description');
		$this->add('content_order', 0);
		$this->add('content_date', time(), self::TYPE_TIMESTAMP);
		$this->add('content_status');
		
		$this->profile = new Model_Profile_Content( $db );
		$this->term = new Model_Term( $db );
	}
	
	/**
	 * postLoad
	 * 
	 * setelah proses load content
	 * load profile
	 * load images
	 */
	protected function postLoad()
	{
		$this->old_name = $this->content_name;
		$this->old_slug = $this->content_slug;
		
		// terms [categories & tags]
		$terms = Model_Term::GetTerms($this->_db, array('relation' => $this->getId() ));
		if( $terms ){
			$this->content_categories = array_keys($terms['categories']);
			$this->content_tags = $terms['tags'];
		}
		
		// profile
		$this->profile->setParentId($this->getId());
		$this->profile->load();
		
		// images
		$this->contentImage = new Model_ContentImage($this->_db);
		$this->images = Model_ContentImage::GetImages($this->_db, array('content_id' => $this->getId()));
	}

	/**
	 * preInsert
	 * 
	 * sebelum proses insert content
	 * buat slug dari nama
	 */
	protected function preInsert()
	{
		$this->content_slug = $this->_generateSlug($this->content_name);
		return true;
	}
	
	/**
	 * postInsert
	 * 
	 * Setelah proses insert content
	 * Set content ID untuk insert profile data.
	 */
	protected function postInsert()
	{
		// profile
		$this->profile->setParentId($this->getId());
		$this->profile->save(false);
		return true;
	}
	
	/**
	 * preUpdate
	 */
	protected function preUpdate()
	{
		if( strtolower($this->content_name) != strtolower($this->old_name) )
		{
			$slug = ( $this->content_slug && ($this->content_slug != $this->old_slug) ) ? $this->content_slug : $this->content_name;
			$this->content_slug = $this->_generateSlug( $slug );
		}
		return true;
	}
	
	/**
	 * postUpdate
	 * 
	 * Setelah proses update content 
	 * Set content ID untuk update profile data.
	 * tambah index
	 */
	protected function postUpdate()
	{
		$this->profile->save(false);
		return true;
	}
	
	/**
	 * preDelete
	 * 
	 * Sebelum proses delete content, 
	 * Hapus semua profile data. 
	 * Hapus semua data images.
	 */
	protected function preDelete()
	{
		// hapus child
		if( $this->content_parent == 0 ) 
		{
			$childs = self::GetContents($this->_db, array('parent_in' => $this->getId()));
			if( $childs )
			{
				foreach( $childs as $child )
					$child->delete(false);
				
				$content_ids = array_keys( $childs );
				$this->term->deleteAllTerms( $content_ids );
			}
		} 
		
		// hapus term
		$this->term->deleteAllTerms( $this->getId() );
		
		// hapus profile ( custom fields )
		$this->profile->delete();
		
		if( $this->images ) 
		{
			// hapus semua gambar terkait
			foreach ( $this->images as $image )
			{
				$image->delete(false);
			}
		}
		
		return true;
	}
	
	/**
	 * sendLive
	 * simpan dengan status LIVE, serta ubah waktu sekarang
	 */
	public function sendLive()
	{
		if ($this->content_status != self::STATUS_LIVE) {
			$this->content_status = self::STATUS_LIVE;
			$this->content_date = time();
		}
		return $this->save();
	}
	
	/**
	 * isLive
	 * cek status LIVE
	 * 
	 * @return boolean
	 */
	public function isLive()
	{
		return $this->isSaved() && $this->content_status == self::STATUS_LIVE;
	}

	/**
	 * sendToDraft
	 * simpan dengan status DRAFT
	 */
	public function sendToDraft()
	{
		$this->content_status = self::STATUS_DRAFT;
		return $this->save();
	}
	
	/**
	 * isDraft
	 * cek status DRAFT
	 * 
	 * @return boolean
	 */
	public function isDraft()
	{
		return $this->isSaved() && $this->content_status == self::STATUS_DRAFT;
	}

	/**
	 * hasChild
	 * 
	 * @return boolean
	 */
	public function hasChild()
	{
		return count($this->getChild()) > 0;
	}

	/**
	 * getChild
	 * 
	 * @return array
	 */
	public function getChild()
	{
		$childs = Model_Content::GetContents( $this->_db, array(
			'parent_in' => $this->getId()) 
		);
		
		return $childs;
	}

	/**
	 * isChild
	 * 
	 * @return boolean
	 */
	public function isChild()
	{
		return $this->content_parent != 0;
	}

	/**
	 * getParent
	 * 
	 * @return object
	 */
	public function getParent()
	{
		if( !$this->isChild() )
			return null;
		
		$page = new Model_Content( $this->_db );
		$page->load($this->content_parent);
		return $page;
	}
	
	/**
	 * isFrontPage
	 */
	public function isFrontPage()
	{
		$option = new Model_Option( $this->_db );
		//$option->load( 'option_general', 'option_name' );
		//$general = unserialize($option->option_value);
		
		//$is_front_page = $general['front_page'];
		
		return false;
	}
	
	/**
	 * getUri
	 * 
	 * @return string
	 */
	public function getUri()
	{
		if( $this->isFrontPage() )
			$URI = '';
		else {
			if( $this->isChild() )
			{
				$parent = $this->getParent();
				$URI = $parent->content_slug . '/' . $this->content_slug;
			} 
			else
				$URI = $this->content_slug;
		}
		
		return $URI;
	}

	/**
	 * saveContent
	 * 
	 * @param array
	 * @return @boolean
	 */
	public function saveContent( array $data )
	{
	 	$id = (int) $data['content_id'];
		if( $id > 0 ) {
			$this->load( $id );
		}
		unset($data['content_id']);
		
	 	foreach( $data as $col => $val )
		{
			$this->$col = $val;
		}
		
		// save term
		$this->term->addTerms( $id, array(
			$data['content_type'] => $data['content_categories'],
			'tag' => $data['content_tags']
		));
		
		return $this->save();
	}
	
	/**
	 * toPopulate
	 * 
	 * @return array
	 */
	public function toPopulate()
	{
		if( !$this->isSaved() )
			return array();
		
		$populate = $this->toArray();
		$merge = array(
			'content_categories' => $this->content_categories,
			'content_tags' => (is_array($this->content_tags)) ? join(',', $this->content_tags) : $this->content_tags,
		);
		return array_merge($populate, $merge);
	}

	/**
	 * 
	 * ============================= QUERY Contents =============================
	 * 
	 */
	
	/**
	 * _GetBaseQuery
	 * Basis query select
	 * 
	 * @param db
	 * @param array
	 * 
	 * @return string select
	 */
	private static function _GetBaseQuery($db, $options)
	{
		// initialize the options
		$defaults = array(
			'parent_in' 	=> array(),	
			'parent_not_in' => 0,	
			'in' 			=> array(),			
			'not_in' 		=> 0,			
			'type'			=> array(),		
			'type_not'		=> array(),
			'order'      	=> 'c.content_id DESC',
			'status'		=> null,
		);

		foreach ($defaults as $k => $v) {
			$options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
		}

		$select = $db->select();
		$select->from(array('c' => 'go_contents'), array());
		
		// in
		if ($options['in']){
			$select->where('c.content_id in (?)', $options['in']);
		}
		// not in
		if($options['not_in'] > 0){
			$select->where('c.content_id != ?', $options['not_in']);
		}
		// parent
		if ($options['parent_in']){
			$select->where('c.content_parent in (?)', $options['parent_in']);
		}
		// parent not in
		if ($options['parent_not_in'] > 0){
			$select->where('c.content_parent != ?', $options['parent_not_in']);
		}
		// content type
		if($options['type']){
			$select->where('c.content_type in (?)', $options['type']);
		}
		// content type not
		if($options['type_not']){
			$select->where('c.content_type not in (?)', $options['type_not']);
		}
		
		// status	
		if(!is_null($options['status'])){
			$select->where('c.content_status = ?', $options['status']);
		}
		// order
		$select->order($options['order']);
		$select->group('c.content_id');
		
		return $select;
	}
	 
	/**
	 * GetContents
	 * ambil semua data content, profile, dan images
	 * 
	 * @param db
	 * @param array
	 * 
	 * @return object
	 */
	public static function GetContents($db, $options = array())
	{
		// initialize the options
		$defaults = array(
			'offset' => 0,
			'limit'  => 0,
			'order'  => 'c.content_date desc'
		);

		foreach ($defaults as $k => $v) {
			$options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
		}

		$select = self::_GetBaseQuery($db, $options);
		$select->from(null, 'c.*');
		// limit
		if ($options['limit'] > 0)
			$select->limit($options['limit'], $options['offset']);
			
		$select->order($options['order']);
		$data = $db->fetchAll($select);
		
		// get content ids
		$contents = self::BuildMultiple($db, __CLASS__, $data);		
		$content_ids = array_keys($contents);
		if (count($content_ids) == 0)
			return array();	
			
		// load the profile data
		$profiles = Model_Profile_Content::BuildMultiple($db,
                                           'Model_Profile_Content',
										   array('profile_id' => $content_ids));
		foreach ($contents as $content_id => $content) {
			if (array_key_exists($content_id, $profiles)
			&& $profiles[$content_id] instanceof Model_Profile_Content) {

				$contents[$content_id]->profile = $profiles[$content_id];
			}
			else {
				$contents[$content_id]->profile->setParentId($content_id);
			}
		}
		
		// load the terms
		$terms = Model_Term::GetTerms( $db, array('relation' => $content_ids, 'order' => 't.term_name') );
		if( $terms ){
			foreach ($terms as $content_id => $term) {
				if( $term['category'] )
					$contents[$content_id]->content_categories = array_keys($term['categories']);
				if( $term['tag'] )
					$contents[$content_id]->content_tags = $term['tags'];
			}
		}
			
		// load the images
		$options = array('content_id' => $content_ids);
		$images = Model_ContentImage::GetImages($db, $options);
		foreach ($images as $image) {
			$contents[$image->content_id]->images[$image->getId()] = $image;
		}
		
		return $contents;
	}

	/**
	 * GetContentsParentChild
	 * ambil kontent parent child
	 *  
	 * @param db
	 * @param array
	 */
	public static function GetContentsParentChild($db, $options = array())
	{
		$contents = self::GetContents($db, $options);
		$cnts = array();
		foreach ($contents as $content) 
		{
			$attributes = array(
				'content_categories' => ($content->content_categories) ? join(',', $content->content_categories) : '',
				'content_tags' => ($content->content_tags) ? join(',', $content->content_tags) : '',
				'images' => Model_ContentImage::GetImages( $db, array('content_id' => $content->getId()) ),
				'profiles' => $content->profile
			);
			// merge attributes into content array
			$data = array_merge($content->toArray(), $attributes);
			
			if ($content->content_parent == 0) 
			{
				// membuat array baru untuk setiap konten level teratas
				$cnts[$content->getId()] = array(
					'parent' 	=> $data, 
					'children' 	=> array()
				);
			} else {
				// letakkan child kontent pada array parent
				$cnts[$content->content_parent]['children'][$content->getId()] = $data;	
			}
		}
		krsort($cnts);
		return $cnts;
	}
	
	/**
	 * GetPaginationContents
	 * ambil data content, profile, dan images untuk Paginator
	 * 
	 * @param db
	 * @param array
	 * 
	 * @return object Paginator
	 */
	public static function GetPagination($db, $options = array())
	{
		// initialize the options
		$defaults = array(
			'page' 	 => 1,
			'show' 	 => 10,
			'range'  => 3,
			'type'	=> null
		);

		foreach ($defaults as $k => $v) {
			$options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
		}
		
		$contents = self::GetContentsParentChild( $db, array(
															'type' => $options['type'], 
															'order' => 'c.content_id',
														));
		// paginator
		$paginator = Zend_Paginator::factory($contents);
		$paginator->setItemCountPerPage($options['show'])
				  ->setCurrentPageNumber($options['page'])
				  ->setPageRange($options['range']);	
				  
		return $paginator;
	}
	
	/**
	 * 
	 * GetProfileNames
	 * ambil semua custom field name
	 * 
	 * @param db
	 * @return array
	 */
	public static function GetProfileNames($db)
	{
		$select = $db->select();
		$select->from(array('p' => 'go_content_profiles'), 
					  array('p.profile_name'))
			   ->joinInner(array('c' => 'go_contents'), 
			   			   'c.content_id = p.profile_id', 
			   			   array())
			   ->group('p.profile_name');
		
		return $db->fetchCol($select);
	}
	
	/**
	 * GetProfiles
	 * ambil semua custom field
	 * 
	 * @param db
	 * @param int
	 * @return array
	 */
	public static function GetProfiles($db, $content_id)
	{
		$select = $db->select();
		$select->from('go_content_profiles', '*')
			   ->where('profile_id IN (?)', $content_id);
		
		return $db->fetchAll($select);
	}
	
	/**
	 * 
	 * ============================= INDEX LUCENE =============================
	 * 
	 */
	
	/**
	 * getIndexableDocument
	 * 
	 * @return object Zend_Search_Lucene_Document
	 */ 
	public function getIndexableDocument()
	{	
		$doc = new Zend_Search_Lucene_Document();
		$doc->addField(Zend_Search_Lucene_Field::keyword('content_id', $this->getId()));		
		$doc->addField(Zend_Search_Lucene_Field::unIndexed('content_name', $this->name));
		$doc->addField(Zend_Search_Lucene_Field::text('content_slug', $this->slug));
		$doc->addField(Zend_Search_Lucene_Field::text('content_description', $this->description));
		$doc->addField(Zend_Search_Lucene_Field::unIndexed('content_date', $this->date));		
		return $doc;
	}
	
	/**
	 * _addToIndex
	 * tambah index
	 */
	protected function _addToIndex()
	{
		try {
			$index = Zend_Search_Lucene::open(APPLICATION_PATH . '/indexes');
		} catch (Exception $e) {
			self::RebuildIndex();
			return;
		}
		
		try {
			$term = new Zend_Search_Lucene_Index_Term($this->getId(), 'content_id');
			$query = new Zend_Search_Lucene_Search_Query_Term($term);
			
			$hits = $index->find($query);
			foreach ($hits as $hit){
				$index->delete($hit->id);
			}
			
			$index->addDocument($this->getIndexableDocument());
			$index->commit();
		} catch (Exception $e) { 
			//
		}
	}
	
	/**
	 * _deleteFromIndex
	 * hapus index
	 */
	protected function _deleteFromIndex()
	{
		try {
			$index = Zend_Search_Lucene::open(APPLICATION_PATH . '/indexes');
		} catch (Exception $e) {
			self::RebuildIndex();
			return;
		}
		
		try {
			$term = new Zend_Search_Lucene_Index_Term($this->getId(), 'content_id');
			$query = new Zend_Search_Lucene_Search_Query_Term($term);
			
			$hits = $index->find($query);
			foreach ($hits as $hit){
				$index->delete($hit->id);
			}
			
			$index->commit();
		} catch (Exception $e) { 
			//
		}
	}
	
	/**
	 * RebuildIndex
	 * index ulang
	 * 
	 * @param db
	 * @return int
	 */
	public static function RebuildIndex($db)
	{
		try {
			$index = Zend_Search_Lucene::create(APPLICATION_PATH . '/indexes');
			$pages = self::GetPages($db);
			foreach ($pages as $page) {
				$index->addDocument($page->getIndexableDocument());
			}
			$index->commit();
			return $index->numDocs();
		} catch (Exception $ex) {
			//
		}
	}
}
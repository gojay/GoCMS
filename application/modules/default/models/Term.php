<?php
class Model_Term extends My_Db_Object_Abstract
{
	protected $_table_relation = 'go_term_relations';
	
	protected $old_name, $old_slug;
	
	public function __construct($db)
	{
		parent::__construct($db, 'go_terms', 'term_id');
		
		$this->add('term_parent', 0);
		$this->add('term_name');
		$this->add('term_slug');
		$this->add('term_description', '');
		$this->add('term_taxonomy');
		$this->add('term_count', 0);
	}
	
	/**
	 * postLoad
	 * 
	 */
	protected function postLoad()
	{
		$this->old_name = $this->term_name;
		$this->old_slug = $this->term_slug;
	}

	/**
	 * preInsert
	 * 
	 * sebelum proses insert content
	 * buat slug dari nama
	 */
	protected function preInsert()
	{
		$this->term_slug = $this->_generateSlug($this->term_name, 'term_slug');
		return true;
	}
	
	/**
	 * preUpdate
	 * 
	 * Sebelum proses update content
	 * Jika nama kontent tidak sama, buat slug baru
	 */
	protected function preUpdate()
	{
		if( strtolower($this->term_name) != strtolower($this->old_name) ){
			
			$slug = ( $this->term_slug && $this->term_slug != $this->old_slug ) ? $this->term_slug : $this->term_name;
			
			$this->term_slug = $this->_generateSlug($slug, 'term_slug');
		}
		
		return true;
	}
	
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
			'taxonomy'		=> null,		
			'term'			=> null,		
			'slug'			=> null,		
			'order'      	=> 't.term_id DESC',
		);

		foreach ($defaults as $k => $v) {
			$options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
		}

		$select = $db->select();
		$select->from(array('t' => 'go_terms'), array());
		
		// in
		if (count($options['in'])){
			$select->where('t.term_id in (?)', $options['in']);
		}
		// not in
		if($options['not_in'] > 0){
			$select->where('t.term_id != ?', $options['not_in']);
		}
		// parent
		if (count($options['parent_in'])){
			$select->where('t.term_parent in (?)', $options['parent_in']);
		}
		// parent not in
		if ($options['parent_not_in'] > 0){
			$select->where('t.term_parent != ?', $options['parent_not_in']);
		}
		// type
		if($options['taxonomy']){
			$select->where('t.term_taxonomy in (?)', $options['taxonomy']);
		}
		// term
		if($options['term']){
			$select->where('lower(t.term_name) = ?', strtolower($options['term']));
		}
		// slug
		if($options['slug']){
			$select->where('t.term_slug = ?', $options['slug']);
		}
		
		// order
		$select->order($options['order']);
		
		return $select;
	}
	
	/**
	 * GetTerms
	 * 
	 * @param db
	 * @param array
	 * 
	 * @return object
	 */
	public static function GetTerms($db, $options = array())
	{
		$select = self::_GetBaseQuery($db, $options);
		$select->from(null, 't.*');
		
		if( $options['relation'] )
		{
			$select->joinInner(array('r' => 'go_term_relations'), 
				   			   'r.term_id = t.term_id', 
				   			   array('r.content_id'))
				   ->where('r.content_id IN (?)', $options['relation'] );
			$data = $db->fetchAll($select);
			foreach( $data as $term ){
				if( $term['term_taxonomy'] == 'tag'){
					$terms[$term['content_id']]['tags'][$term['term_id']] = $term['term_name']; 
				} else {
					$terms[$term['content_id']]['categories'][$term['term_id']] = $term['term_name']; 
				}
			}
			
			// single content
			if( !is_array($options['relation']) )
				$terms = $terms[$options['relation']];
			
			$taxonomy = $options['taxonomy'];
			$result = ( $taxonomy ) ? $terms[$taxonomy] : $terms ;
		} else {
			$result = $db->fetchAll($select);
		}
		
		return $result;
	}
	
	public static function GetTermsParentChild($db, $options = array())
	{
		$data = self::GetTerms($db, $options);
		$terms = self::BuildMultiple($db, __CLASS__, $data);	
		
		$_terms = array();
		if($terms)
		{
			foreach ($terms as $term) 
			{
				if ($term->term_parent == 0) 
				{
					// create a new array for each top level content
					$_terms[$term->getId()] = array(
						'parent' 	=> $term->toArray(), 
						'children' 	=> array()
					);
				} else {
					// the child content are put int the parent content's array
					$_terms[$term->term_parent]['children'][$term->getId()] = $term->toArray();	
				}
			}
		}
		krsort($_terms);
		return $_terms;
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
		);

		foreach ($defaults as $k => $v) {
			$options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
		}
		
		$terms = self::GetTermsParentChild( $db, array(
														'taxonomy' => $options['taxonomy'], 
														'order' => 't.term_id',
													));
		// paginator
		$paginator = Zend_Paginator::factory($terms);
		$paginator->setItemCountPerPage($options['show'])
				  ->setCurrentPageNumber($options['page'])
				  ->setPageRange($options['range']);	
				  
		return $paginator;
	}

	/**
	 * GetCategories
	 * ambil categories parent child untuk populate
	 *  
	 * @param db
	 * @param array
	 */
	public static function GetCategoriesForPopulate($db, $taxonomy = 'category')
	{
		// set taxonomy
		$taxonomy = ($taxonomy == 'post') ?  'category' : $taxonomy;
		
		$terms = self::GetTerms($db, array('taxonomy' => $taxonomy, 'parent' => 0, 'order' => 't.term_id'));
		$cats = array();
		if($terms){
			foreach ($terms as $term) 
			{
				if ($term['term_parent'] == 0) 
				{
					// create a new array for each top level content
					$cats[$term['term_id']] = array(
						'title' 	=> $term['term_name'], 
						'children' 	=> array()
					);
				} else {
					// the child content are put int the parent content's array
					$cats[$term['term_parent']]['children'][$term['term_id']] = array(
						'title' 	=> $term['term_name'], 
						'children' 	=> array()
					);	
				}
			}
		}
		
		return $cats;
	}
	
	/**
	 * 
	 * Get Tag Suggestions
	 * ambil data tag LIKE tag
	 * digunakan untuk AJAX autocomplete
	 * 
	 * Query : 
	 * 		SELECT DISTINCT term_name
	 * 		FROM go_terms t
	 * 		WHERE t.term_name LIKE '{q}%'
	 * 		ORDER BY lower(t.term_name)
	 * 
	 * @param db
	 * @param string tag
	 * @param int limit
	 * @return array
	 */
	public static function GetTagSuggestions($db, $tag, $limit = 0)
	{
		$tag = trim($tag);
		if ( !$tag )
			return array();

		$select = self::_GetBaseQuery( $db, array('taxonomy' => 'tag' ));
		$select->distinct();
		$select->from(null, array('t.term_name'));
		$select->where('lower(t.term_name) like lower(?)', $tag . '%')
			   ->order('lower(t.term_name)');

		if ($limit)
			$select->limit($limit);

		return $db->fetchCol($select);
	}
	
	/**
	 * GetTagCloud
	 * membuat tag cloud
	 * 
	 * @param db
	 * @param array
	 * @return array
	 */
	public function GetTagCloud( $db, $options = array() )
	{
		$tems = self::GetTerms( $db, array('taxonomy' => 'tag' ));
		
		$tagCloud = '';
		if( $tems ){
			foreach( $tems as $tag )
			{
				$tags[] = array( 
					'title' => $tag['term_name'], 
					'weight' => $tag['term_count'], 
					'params' => array('url' => '#tag-'.$tag['term_id'])
				);
			}
			
			$tagCloud = new Zend_Tag_Cloud( array(
				'tags' => $tags,
				'cloudDecorator' => array(
					'decorator' => 'HtmlCloud',
					'options' => array(
						'htmlTags' => array('div' => array('id' => 'tags')), 
						'separator' => ''
					)
				),
				'tagDecorator' => array(
					'decorator' => 'HtmlTag',
					'options' => array(
						'htmlTags' => array('div' => 'span'),
						'minFontSize' => 12,
						'maxFontSize' => 26,
					)
				)
			));
		}
		return $tagCloud;
	}
	
	/**
	 * 
	 * ============================= TERMS =============================
	 * 
	 */
	
	/**
	 * buat terms
	 * dengan KEY = term huruf kecil/lower
	 * @param array/string,
	 * 		  Array, berisi array int untuk taxonomy category, example : (7,8)
	 * 		  String, berisi string dengan separator "," untuk taxonomy tag, example : (php,perl)
	 * @return array
	 * example : 
	 * array(3) {
		  ["php"] => string(3) "PHP"
		  ["pdo"] => string(3) "pdo"
		}
	 */
	protected function _getTerms( $terms )
	{
		$_terms = array();
		if( is_array($terms) ){
			$terms = self::GetTerms($this->_db, array('in' => $terms));
		} else {
			$terms = trim($terms, ',');
			$terms = explode(',', $terms);
		}
		
		if( $terms ){
			// buat format array key lower term
			foreach( $terms as $t ){
				$name = ( is_array($t) ) ? $t['term_name'] : $t ;
				$_terms[strtolower($name)] = trim($name, ' ');
			}
		}
		
		return $_terms;
	}
	
	/**
	 * _getTermIds
	 * ambil term id berdasarkan content id
	 * 
	 * @param int
	 * @return array
	 */
	protected function _getTermIds($content_id)
	{
		$db = $this->_db;
		$select = $db->select();
		$select->from('go_term_relations', 'term_id')
			   ->where('content_id = ?', $content_id);
			   
		return $db->fetchCol($select);
	}
	 
	/**
	 * _getTermsByTax
	 * ambil data terms
	 * 
	 * @param string
	 * @return array
	 * example :
	 * array(2) {
		  ["pdo"] => int(2)
		  ["php"] => int(1)
		}
	 */
	protected function _getTermsByTax( $taxonomy )
	{
		$terms = self::GetTerms($this->_db, array('taxonomy' => $taxonomy));
		$ts = array();
		if( $terms ){
			foreach( $terms as $t )
				$ts[strtolower($t['term_name'])] = (int) $t['term_id'];
		}
		
		return $ts;
	}
	
	/**
	 * addTerm
	 * tambah term untuk content
	 * 
	 * @param int
	 * @param string
	 * @param string
	 * @return 
	 */
	public function addTerm( $content_id, $terms, $taxonomy )
	{
		if( !$terms )
			return;
		
		// buat new terms
		$new_terms = $this->_getTerms( $terms );
		
		// set taxonomy
		$taxonomy = ($taxonomy == 'post') ?  'category' : $taxonomy;
		
		// ambil terms
		$terms = Model_Term::GetTerms( $this->_db, array('taxonomy' => $taxonomy, 'relation' => $content_id) );
		if( $terms ){
			$terms = array_map('strtolower', $terms);
			// pengecekan "compare" content term "new terms" dengan "terms"
			// jika terjadi perbedaan/perubahan (Different), maka delete term yg tdk terpakai
			$terms_diff = array_diff($terms, array_keys($new_terms));
			if( $terms_diff ){
				$this->deleteTerm(array_keys($terms_diff), $content_id);
			}
		}
		
		// ambil "term yang terdaftar" berdasarkan taxonomy
		$existingTerms = $this->_getTermsByTax( $taxonomy );
		foreach( $new_terms as $new_lower => $new_term ) 
		{
			// jika term baru sudah terdaftar pd tabel Terms
			if( array_key_exists($new_lower, $existingTerms) )
			{
				// lanjutkan/abaikan jika term sudah terdaftar sebagai relation/content term
		        if( $terms && in_array($new_lower, $terms) )
					continue;
				// ambil term id, dengan "new term" (lower) sebagai key dari array "term yang terdaftar"
				$term_id = $existingTerms[$new_lower];
				// load n update increase term count
				$this->load($term_id);
				$this->term_count++;
				$this->save();
			} else {
				// sebaliknya jika belum terdaftar save "new term"
				$this->term_name = $new_term;
				$this->term_taxonomy = $taxonomy;
				$this->term_count = 1;
				$this->save();
			}
			
			$this->addRelation( $content_id ); // daftar relation/content term
		}
		
		return true;
	}

	/**
	 * addTerms
	 * 
	 * @param int
	 * @param string
	 */
	public function addTerms( $content_id, array $terms )
	{
		foreach( $terms as $taxonomy => $term )
		{
			$this->addTerm( $content_id, $term, $taxonomy );
		}
		 
	}

	/**
	 * addRelation
	 * 
	 * @param int
	 * @return boolean
	 */
	public function addRelation( $content_id )
	{
		$data = array(
			'content_id' => $content_id,
			'term_id'	 => $this->getId()
		);
		$this->clear();	// clear id
		return $this->_db->insert($this->_table_relation, $data);
	}
	
	/**
	 * deleteTerm
	 * hapus term dan decrease term count pada content
	 * 
	 * DELETE FROM`go_term_relations` WHERE `content_id` = 1 AND `term_id` IN (2,3)
	 * UPDATE `go_terms` SET term_count = term_count - 1 WHERE `term_id` IN (2,3)
	 * 
	 * @param array
	 * @param int
	 * @return
	 */
	public function deleteTerm( $d, $content_id )
	{
		$term_id = ( is_array($d) ) ? join(',', $d) : $d ;
		// delete relation term
		$where = array(
			'content_id = ' . $content_id,
			sprintf('term_id IN (%s)', $term_id)
		);
		$this->_db->delete( $this->_table_relation, $where );
		// update decrease term count
		$update = array( 'term_count' => new Zend_Db_Expr('term_count - 1') );
		$this->_db->update( $this->_table, $update, sprintf('term_id IN (%s)', $term_id) );
	}
	
	/**
	 * deleteAllTerms
	 * hapus semua terms pada content
	 */
	public function deleteAllTerms( $content_id )
	{
		$termIds = $this->_getTermIds( $content_id );
		$this->deleteTerm( $termIds, $content_id );
	}
}

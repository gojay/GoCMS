<?php
class Admin_AjaxController extends Zend_Controller_Action
{

    public function init()
    {
       $this->db = Zend_Registry::get( 'db' );
    }
	
	/**
	 * =============================== Content =============================== 
	 */
	 
	/**
	 * autosave
	 * edit permalink
	 * inline save
	 * add category
	 * tag suggestions (autocomplete)
	 */
	public function contentAction()
	{
		$act = $this->_getParam('act');
		
		$content_id = (int) $this->_getParam('content_id');
		$content_type = $this->_getParam('content_type');
		$content_name = $this->_getParam('content_name'); 
		$content_slug = $this->_getParam('content_slug'); 
		$content_tags = $this->_getParam('content_tags'); 
		$content_categories = $this->_getParam('content_categories');
		$content_parent = (int) $this->_getParam('content_parent');
		$content_order = (int) $this->_getParam('content_order');  
		$content_status = $this->_getParam('content_status');
		
		$content = new Model_Content( $this->db );
		$term = new Model_Term( $this->db );
				
		switch ( $act ) {
			case 'autosave':
				$content->content_type = $content_type;
				$content->content_name = $content_name;
				$isSaved = $content->sendToDraft();
				if( $isSaved )
					$this->view->permalink = $content;
				break;
			// edit permalink	
			case 'edit-permalink':
				if( !$content->load($content_id) )
					die(-1);
				
				$content->content_name = $content_name;
				$content->content_slug = $content_slug;
				$isSaved = $content->saveAndCleanSlug();
				if( $isSaved )
					$this->view->permalink = $content;
				break;
			// inline save	
			case 'inline-save':
				if( !$content->load($content_id) )
					die(-1);
				
				$content->content_parent = $content_parent;
				$content->content_name = $content_name;
				$content->content_slug = $content_slug;
				$content->content_status = $content_status;
				$content->content_parent = $content_parent;
				$content->content_order = $content_order;
				
				$isSaved = $content->save();
				if( $content_categories || $content_tags )
				{
					// save term
					$term->addTerms( $content->getId(), array(
						'category' => $content_categories,
						'tag' => $content_tags
					));
					// set categories n tags to populate
					$content->content_categories = $content_categories;
					$content->content_tags = $content_tags;
				}
				
				$this->view->inlineSave = $content->toPopulate();
				break;
			// category					
			case 'category':
				$category = $this->_getParam('category'); 
				$parent = $this->_getParam('parent'); 
				$taxonomy = $this->_getParam('taxonomy'); 
				
				$term = new Model_Term( $this->db );
				$term->term_name = $category;
				$term->term_parent = $parent;
				$term->term_taxonomy = $taxonomy ;
				if( !$term->save() )
					die(-1);
				
				$cats = Model_Term::GetCategoriesForPopulate( $this->db, $taxonomy );
				$tree = new My_Form_Element_TreeView('content_categories');
				$tree->setOptions(array('multioptions' => $cats))
					 ->setDecorators(array('ViewHelper'));
					 
				$this->_helper->json( array('id' => $term->getId(), 'cats' => $tree->__toString()) );
				break;
			// tag	
			case 'tag':
				$q = $this->_getParam('term');
				$tags = Model_Term::GetTagSuggestions($this->db, $q, 3);	
				$this->_helper->json( $tags );
				break;
		}	

		$this->_helper->layout->disableLayout();
	}

	/**
	 * Content Custom Fields 
	 * add Field
	 * update field
	 * delete field
	 */
	public function fieldAction()
	{
		$act = $this->_getParam('act');
		
		$id = $this->_getParam('id');
		$name = $this->_getParam('name');
		$value = $this->_getParam('value');
		
		$content = new Model_Content( $this->db );
		if( !$content->load($id) )
			die(-1);
		
		switch ($act) {
			case 'add':
				$content->profile->$name = $value;
				$content->profile->save();
				
				$this->view->field = array('name' => $name, 'value' => $value);
				break;
				
			case 'update':
				// update
				$update = array(
					'profile_name' => $name,
					'profile_value' => $value
				);
				// condition
				$where = array(
					'profile_id = ?' => $id,
					'profile_name = ?' => $name
				);
				$isSaved = $this->db->update( 'go_content_profiles', $update, $where );
				$this->_helper->json(array('ok' => $isSaved));
				break;
				
			case 'delete':
				// condition
				$where = array(
					'profile_id = ?' => $id,
					'profile_name = ?' => $name
				);
				$isSaved = $this->db->delete( 'go_content_profiles', $where );
				$this->_helper->json(array('ok' => $isSaved));
				break;
		}
		$this->_helper->layout->disableLayout();
	}
	
	/**
	 * Content Terms ( category/tag )
	 * add Term
	 * update Term
	 * delete Term
	 */
	public function termAction()
	{
		$act = $this->_getParam('act');
		
		$term_name = $this->_getParam('term_name');	
		$term_slug = $this->_getParam('term_slug');	
		$term_parent = (int) $this->_getParam('term_parent');
		$term_description = $this->_getParam('term_description');
		$term_taxonomy = $this->_getParam('term_taxonomy');
		$term_id = (int) $this->_getParam('term_id');
		
		$term = new Model_Term( $this->db );
		switch ($act) {
			case 'add':
				$term->term_name = $term_name;
				$term->term_slug = $term_slug;
				$term->term_parent = $term_parent;
				$term->term_description = $term_description;
				$term->term_taxonomy = $term_taxonomy;
				$term->save();
				
				$terms = Model_Term::GetTermsParentChild( $this->db, array(
					'taxonomy' => $term_taxonomy, 
					'order' => 't.term_id'
				));
				$this->view->terms = $terms;
				break;
				
			case 'update':
				if( !$term->load($term_id) )
					die(-1);
				
				$term->term_name = $term_name;
				$term->term_slug = $term_slug;
				$term->term_parent = $term_parent;
				$term->term_description = $term_description;
				$term->save();	
				
				$this->view->term = $term->toArray();
				break;
				
			case 'delete':
				if( !$term->load($term_id) )
					die(-1);
				
				$isSaved = $term->delete();
				
				$this->_helper->json(array('ok' => $isSaved));
				break;
		}
		$this->_helper->layout->disableLayout();
	}
	
	/**
	 * Modal Images
	 * Modal Iframe upload/set image
	 */
	public function imagesAction()
	{
		$id = $this->_request->getParam('id');
		
		$content = new Model_Content($this->db);
		if( !$content->load($id) )
			throw new Zend_Controller_Action_Exception('This page dont exist',404);
		
		$this->view->content = $content;
		
		$this->_helper->layout->disableLayout();
	}
	
	/**
	 * =============================== Settings =============================== 
	 */

	/**
	 * Analytics
	 * tampil graph google analytics
	 */
	public function analyticsAction()
	{
		$analytics = $this->_helper->hooks->getOption( 'option_analytics' );
		
		try{
			$ga = new My_Google_Analytics(
				$analytics['g_mail'], // email
				$this->_helper->hooks->getDecrypt( $analytics['g_password'] ) // passsword
			);
			// profileID
			$ga->setProfile( 'ga:'.$analytics['ga_profile_id'] ); 
			
			// date range
			$ga->setDateRange(
				date('Y-m-d',strtotime( '-30 days' )) , 
				date('Y-m-d',strtotime('now')) 
			);
			
			// visit graph
			$visitsgraph = $ga->getReportArray(
				array(
					'dimensions' => urlencode('ga:date'),
					'metrics' => urlencode('ga:visits')
				)
			);
			
			// referrer
			$referrers = $ga->getReportArray(
				array(
					'dimensions' => urlencode('ga:city'),
					'metrics' => urlencode('ga:visits'),
					'sort' => urlencode('-ga:visits'),
					'max-results' => 5
				)
			);
			
			$this->_helper->json(array(
				'visits' => $visitsgraph, 
				'referrers' => $referrers
			));
			
		} catch(exception $e){
			$this->_helper->json( array( 'exception' => $e->getMessage()) );
		}
		
	}
	
	/**
	 * Gallery manage
	 * aksi mengatur gallery sortable
	 * untuk tampil home gallery
	 * 
	 * @param PARAM
	 * @return JSON
	 */
	public function galleryAction()
	{
		// aksi
		$act = $this->_getParam( 'act' );
		
		// update widget gallery
		$option = new Model_Option($this->db);	
		if( $act == 'add' ){
			// pisahkan (explode) koma, return array
			$gallery = explode( ',', $this->_getParam( 'dropped' ));
			$isSaved = $option->updateGallery( $gallery );
		// delete widget gallery
		} else if( $act == 'delete' ){
			$gallery = $this->_getParam( 'gallery' );
			$isSaved = $option->removeGallery( $gallery );
		}
		
		// response
		$status = ( $isSaved ) ? 'success' : 'error';
		$message = ( $isSaved ) ? 'Saved Succesfully' : 'Error occured. Please try again later';
		$this->_helper->json( array('status' => $status, 'message' => $message) );
	}

	/**
	 * option
	 * update value option general
	 * update value option social
	 * update value option profile
	 */
	public function optionAction()
	{
		// ambil type
		$type = $this->_getParam( 'type' );
		// model action
		$option = new Model_Option( $this->db );
		// ajax request
		if( $this->_request->isXmlHttpRequest() )
		{
			// inisialisasi form sesuai type
			switch ( $type ) {
				case 'option_general':
					$form = new Admin_Form_OptionGeneral();
					break;
				case 'option_contact':
					$form = new Admin_Form_OptionContact();
					break;
				case 'option_social':
					$form = new Admin_Form_OptionSocial();
					break;
				case 'option_profile':
					$form = new Admin_Form_OptionProfile();
					break;
				case 'option_analytics':
					$form = new Admin_Form_OptionAnalytics();
					break;
				default:
			}
			
			if( $form->isValid( $this->_request->getPost() ))
			{
				// ambil data input
				$data = $form->getValues();
				// simpan option
				$isSaved = $option->saveOption( $data );
				// kirim response
				$status = ( $isSaved ) ? 'success' : 'error';
				$message = ( $isSaved ) ? 'Saved Succesfully' : 'Error occured. Please try again later';
				$this->_helper->json( array('status' => $status, 'message' => $message) );
				
			} else {
				$this->_helper->json( array('status' => 'error', 'message' => 'Data invalid') );
			}
			
		} else {
			$this->_helper->json( array('status' => 'error', 'message' => 'Not AJAX Requested') );
		}
			
	}
	
	/**
	 * validate
	 */
	public function validateAction()
	{
		// ambil type
		$type = $this->_getParam( 'type' );
		// model action
		$option = new Model_Option( $this->db );
		// ajax request
		if( $this->_request->isXmlHttpRequest() )
		{
			// inisialisasi form sesuai type
			switch ( $type ) {
				case 'option_general':
					$form = new Admin_Form_OptionGeneral();
					break;
				case 'option_contact':
					$form = new Admin_Form_OptionContact();
					break;
				case 'option_social':
					$form = new Admin_Form_OptionSocial();
					break;
				case 'option_profile':
					$form = new Admin_Form_OptionProfile();
					break;
				case 'option_analytics':
					$form = new Admin_Form_OptionAnalytics();
					break;
			}
			
			$form->isValid( $this->_request->getPost() );
			// get messages form validasi
			$response = $form->getMessages();
			$response = array('messages' => $response) ;
			// buat validasi untuk geocoding maps dari alamat
			$maps = $this->_request->getPost('maps');
			
			if( isset($maps) && '' !== $maps )
			{
				// proses geocoding
				$geo = $this->_helper->hooks->doGeocoder( $maps );
				// jika gagal geocoding
				if( !$geo )
				{
					// tambahkan kesalahan validasi maps 
					$message = 'geocoding can not be parsed, please verify your address';
					$response = array_merge_recursive( $response, array(
													'messages' => array(
														'maps' => array(
															'message' => $message
														)
													)
					));
				} else {
					// berhasil geocoding, tambahkan response geo untuk latitude dan longtitude
					$response = array_merge_recursive( $response, array('geo' => array(
							'lat' => $geo['lat'],
							'long' => $geo['lon']
						)) 
					);
				}
			}
			// kirim output response dengan json
			$this->_helper->json( $response );
		}
	}

    public function officeAction()
    {
        $id = $this->_getParam('id');
 
		$element1 = new Zend_Form_Element_Text("office_address");
		$element1->setRequired(true)->setLabel('Address')
				 ->setAttrib('class', 'large')
				 ->setRequired(TRUE)
				 ->addFilters(array('StringTrim'))
				 ->addValidator('NotEmpty');
		$element1->setBelongsTo('other['.$id.']');
		
 		$element2 = new Zend_Form_Element_Text("office_phone");
		$element2->setRequired(true)->setLabel('Phone')
				 ->setAttrib('class', 'medium')
			     ->setRequired(TRUE)
				 ->addFilters(array('StringTrim'))
				 ->addValidator('NotEmpty');
		$element2->setBelongsTo('other['.$id.']');
 
		$element3 = new Zend_Form_Element_Text("office_fax");
		$element3->setRequired(true)
				 ->setLabel('Fax')->setAttrib('class', 'small')
			     ->setRequired(TRUE)
				 ->addFilters(array('StringTrim'))
				 ->addValidator('NotEmpty');
		$element3->setBelongsTo('other['.$id.']');
 
		$element4 = new Zend_Form_Element_Text("office_email");
		$element4->setRequired(true)->setLabel('Email')
				 ->setAttrib('class', 'small')
			   	 ->setRequired(TRUE)
				 ->addFilters(array('StringTrim'))
				 ->addValidator('NotEmpty');
		$element4->setBelongsTo('other['.$id.']');
		 
		$this->view->id = $id;
		$this->view->fields = array($element1, $element2, $element3, $element4);
		$this->_helper->layout->disableLayout();
    }

	/**
	 * Option Contact
	 * tambah value option office
	 * update value option office
	 * remove value option office
	 * 
	 * @param POST
	 * @return JSON
	 */
	public function contactAction()
	{
		// action
		$act = $this->_getParam('act');
		// ambil element id
		$fId = $this->_getParam( 'elementId' );
		// ambil index
		list($element, $value) = explode('-', $fId);
		$index = (int) str_replace('office_', '', $value);
		
		// ambil model option
		$option = new Model_Option( $this->db );
		// aksi
		switch ( $act ) 
		{
			case 'office':
				return $this->_forward('office');
				break;
				
			case 'add':
				$offices = $this->_getParam( 'other' );
				if ( isset($offices) ) {
					// kirim response error, jika PARAM other NOT ARRAY || kosong
					if( !is_array( $offices ) ){
						$this->_helper->json( array('status' => 'attention', 'message' => 'Data is empty') );
					}
					
					$values = array();
					// looping dan set ke dalam array
					foreach($offices as $office)
					{
						$values[] = array(
							'city' => $office['office_city'],
							'address' => $office['office_address'],
							'phone' => $office['office_phone'],
							'fax' => $office['office_fax'],
							'email' => $office['office_email']
						);
					}
					// add option_office
					$isSaved = $option->addOffice( $values );
				}
				
				break;
				
			case 'update':
				// ambil post data
				$data = $this->_getParam( 'data' );
				// buat array untuk replace
				$contact = array(
					'city' => $data['office_city_'.$index],
					'address' => $data['office_address_'.$index],
					'phone' => $data['office_phone_'.$index],
					'email' => $data['office_email_'.$index],
					'fax' => $data['office_fax_'.$index]
				);
				
				$isSaved = $option->replaceOffice( $index, $contact );
				break;
			
			case 'remove':
				$isSaved = $option->removeOffice( $index );
				break;
				
			default:
				$this->_helper->json( array('status' => 'error', 'message' => 'Invalid request !!!') );
		}
		
		$status = ( $isSaved ) ? 'success' : 'error';
		$message = ( $isSaved ) ? 'Saved Succesfully' : 'Error occured. Please try again later';
		$this->_helper->json( array('ok' => $isSaved, 'status' => $status, 'message' => $message) );
	}

	/**
	 * Widget
	 * sort widget
	 * save widget
	 */
	public function widgetAction()
	{
		$act = $this->_getParam('act');
		
		$widget = new Model_Option($this->db);
		
		switch ($act) 
		{
			case 'sort':
				$widget_name = $this->_getParam('widget');
				$items 	= $this->_getParam('data');
				
				// buat array items
				if( $items )
					$items = explode(',', $items);	
				
				// sorting
				$response = $widget->sortWidget( $items, $widget_name );
				break;
			
			case 'save':
				$action = $this->_getParam('act_widget');
				$widget_id = $this->_getParam('id');
				$data = array(
					'title' => $this->_getParam('title'),
					//'count' => (int) $this->_getParam('count'),
					//'menu' => $this->_getParam('menu'),
					'value' => $this->_getParam('value')
				);
				switch($action)
				{
					case 'add-widget':
						$isSaved = $widget->saveWidget( $widget_id, 'add', $data );
						$response = array( 'ok' => $isSaved );
						break;
						
					case 'update-widget':
						$isSaved = $widget->saveWidget( $widget_id, 'update', $data  );
						$response = array(
							'ok' => $isSaved, 
							'message' => ($isSaved) ? 'Saved successfully' : 'Error occured'
						);
						break;
						
					case 'delete-widget':
						$isSaved = $widget->saveWidget( $widget_id, 'delete' );
						$response = array( 'ok' => $isSaved );
						break;
				}
				break;
		}
				
		$this->_helper->json( $response );
	}

	/**
	 * Custom Menu
	 * 
	 * tambah page link
	 * tambah page content
	 * set navigation
	 * save menu
	 * update menu
	 * remove menu
	 * 
	 */
	public function menuAction()
	{
		$this->_helper->layout->disableLayout();
		
		$action = $this->_getParam('act');
		
		$option = new Model_Option( $this->db );
		
		switch ( $action ) {
			/* tambah page */
			case 'add-page':
				$page = $this->_getParam('page');
				// ambil page sesuai id
				$pages = Model_Content::GetContents( $this->db, array('in' => $page));
				$this->view->pages = $pages;
				break;
				
			/* tambah page link */
			case 'add-page-link':
				$link_id = $this->_getParam('id');
				$link_label = $this->_getParam('label');
				$link_url = $this->_getParam('url');
				
				$links = array(
					'content_id' => strtolower($link_label),
					'content_name' => $link_label,
					'content_url' => $link_url,
					'content_parent' => 0,
					'content_type' => 'link'
				);
				
				$this->view->links = $links;
				break;
				
			/* tambah page term */
			case 'add-page-term':
				$term = $this->_getParam('term');
				// ambil page sesuai id
				$terms = Model_Term::GetTerms( $this->db, array('in' => $term));
				$this->view->terms = $terms;
				break;
				
			/* set navigation */
			case 'navigation':
				// parameter
				$menu = $this->_getParam('menu');
				$navigation = $this->_getParam('navigation');
				// load navigation
				if(!$option->load( $navigation, 'option_name' ))
					$this->_helper->json( false );
				// save	
				$option->option_value = $menu;
				$response = $option->save();
				$this->_helper->json( $response );
				break;
				
			/* save menu */
			case 'save':
				// parameter
				$menu = $this->_getParam('menu');	// string
				$label = $this->_getParam('label'); // array
		        $title = $this->_getParam('title'); // array
		        $pages = $this->_getParam('page');  // array
		        $url = $this->_getParam('url'); 	// array
		        
				// load menu
				if( !$option->load($menu, 'option_name'))
					$this->_helper->json( false );	
		        
				// build kontent untuk menu page
				$pageIds = array_keys($pages);
				$data = Model_Content::GetContents( $this->db, array('in' => $pageIds));
				foreach($data as $content){
					$contents[$content->getId()] = array(
						'controller'    => 'index',
						'params'  		=> array(
							'title' => $content->content_slug
						),
						'route' => 'page'
					);
				}
				
				// buat menus
				$_menus = array();
				foreach( $pages as $id => $val )
				{
					// parent,label,title navigation 
					// isi sesuai id parameter parent,label,title
					$data = array( 
						'parent' => $val,
						'label' => $label[$id],
						'title' => $title[$id]
					);
					
					// buat uri
					if( $url[$id] )
						$uri = array( 'uri' => $url[$id] ); // tambah item page dari "Custom Links"
					else 
						$uri = $contents[$id]; // tambah item page dari "Pages"
						
					// tambahkan array data navigation uri
					$data = array_merge_recursive($data, $uri);
					
					// add menus
					$_menus[$id] = $data;
				}
				
				// buat tree menus, sesuai parent child
				$navigation = new Model_Navigation($_menus);
				$menus = $navigation->getMenu();
				// nama menu
				list($key, $menu_name) = explode('_', $menu);
				// data menu
				$data_menu = array(
					'name' => $menu_name,
					'menus' => $menus
				);
				
				// save
				$option->option_value = serialize($data_menu);
				$response = $option->save();
					
				$this->_helper->json( $response );	
				break;
				
			/* update menu */
			case 'update':
				$menu = $this->_getParam('menu');
				$name = $this->_getParam('name');
				
				// nama menu
				$name = strtolower($name);
				
				$option->load( $menu, 'option_name' );
				// ubah value name dgn ARRAY_REPLACE
				$value = unserialize($option->option_value);
				$value = Model_Option::_array_replace( $value, array( 'name' => $name ));
				
				// save
				$option->option_name = 'menu_' . $name;
				$option->option_value = serialize($value);
				$isSaved = $option->save();
				
				// set response
				$response = array(
					'ok' 	=> $isSaved,
					'name'  => ucwords($name),
					'value' => $option->option_name,
				);
				
				$this->_helper->json( $response );
				break;
				
			/* remove menu */
			case 'remove':
				// parameter
				$menu = $this->_getParam('menu');
				// load menu
				$option->load( $menu, 'option_name' );
				// delete
				$response = $option->delete();
				$this->_helper->json( $response );
				break;
		}
		
	}
	
}


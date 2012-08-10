<?php
class Admin_Form_Content extends My_Form
{
	protected $attribut_decorator = array(
		'ViewHelper', 
		array( 'Label', array('class' => 'control-label alignleft quarter') ),
		array( 'HtmlTag', array('tag' => 'div', 'class' => 'control-group') )
	);
		
	protected $attribut_decorator_close = array(
		'ViewHelper', 
		array( 'Label', array('class' => 'control-label alignleft quarter') ),
		array( array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group') ),
		array( array('row' => 'HtmlTag'), array('tag' => 'fieldset', 'closeOnly' => true) )
	);
	
    public function init()
    {
		$db = Zend_Registry::get( 'db' );
		
		/* =================================== EDITOR ====================================== */
		
		// title : input text => class full
		$title = $this->createElement('text', 'content_name')
			 		 ->setAttribs(array(
				 		 'class' => 'full', 
				 		 'placeholder' => 'Enter title here..',
						 'rel' => 'tooltip',
						 'title' => 'Enter title here'
					 ))
					 ->setRequired(TRUE)
					 ->addFilters(array('StringTrim'))
					 ->addValidator('NotEmpty')
				     ->setDecorators(array(
						'ViewHelper', 
						array( 'Errors', array('tag' => 'div', 'class' => 'alert alert-warning') ),  
						array( 'HtmlTag', array('tag' => 'div', 'class' => 'control-group', 'openOnly' => true) )
					));
		
		// permalink : html
		$permalink = new My_Form_Element_Html('content_permalink');
		$permalink->setAttrib('style','margin-top:10px')
			 	  ->setDecorators(array(
					'ViewHelper', 
					array( 'HtmlTag', array('tag' => 'div', 'closeOnly' => true) )
				  ));
					 
		// description : TinyMce
		$description = $this->createElement('textarea', 'content_description')
			 		 ->setAttribs(array(
				 		 'class' => 'full', 
				 		 'rows' => 15
					 ))
				     ->setDecorators(array(
						'ViewHelper',
						array( 'HtmlTag', array('tag' => 'div', 'class' => 'control-group') )
					));
		/*
		 * description : FCKEditor		 
		$description = new My_Form_Element_FCKEditor('content_description');
		$description ->setAttribs(array(
				 		 'width' => '525', 
				 		 'toolbar' => 'ZF'
					 ))
				    ->setDecorators(array(
						'UiWidgetElement',
						array( 'HtmlTag', array('tag' => 'div', 'class' => 'control-group') )
					));
		*/
		
		$excerpt = $this->createElement('textarea', 'content_excerpt');
		$excerpt->setDescription('Excerpts are optional hand-crafted summaries of your content')
				->setAttribs(array(
					 'class' => 'full', 
					 'rows' => 2
				))
				->setDecorators(array(array('ViewScript', array(
				    'viewScript' => '_decorator.phtml',
				    'title'      => 'Excerpt'
				))));
			
		// custom fields	
		$field_content = new My_Form_Element_link('field_content');
		$field_content->setDecorators(array(
				array('ViewScript', array(
				    'viewScript' => '_decorator_fields.phtml',
				    'openOnly' => true,
				    'title' => 'Custom Fields',
				))));
		// list field name  : select
		$field_list = $this->createElement('select', 'field_list');
		$field_list->setAttribs(array(
						'rel' => 'tooltip',
						'title' => 'Select field list'
					))
				   ->addMultiOption(0, '— Select —')
				   ->setDecorators(array(
						'ViewHelper', 
						array( 'HtmlTag', array('tag' => 'td', 'openOnly' => true) ),
					));
		$fields = Model_Content::GetProfileNames($db);
		if( $fields ){
			foreach( $fields as $field )
				$field_list->addMultiOption($field, $field);
		}
		// field name : text
		$field_name = $this->createElement('text', 'field_name');
		$field_name->setAttribs(array(
						'class' => 'hide',
						'rel' => 'tooltip',
						'title' => 'Enter field name'
					))
				  ->setDecorators(array('ViewHelper'));
		// field new => link JS
		$field_new = new My_Form_Element_link('field_new');
		$field_new->setValue('Enter new')
				  ->setAttribs(array(
					 	'href' => '#field_name'
				  ))
				   ->setDecorators(array(
						'ViewHelper', 
						array( 'HtmlTag', array('tag' => 'td', 'closeOnly' => true) ),
				   ));
		// field value : textarea
		$field_value = $this->createElement('textarea', 'field_value');
		$field_value->setAttribs(array(
						'class' => 'full', 
						'rows' => 2,
						'rel' => 'tooltip',
						'title' => 'Enter field value'
					))
				    ->setDecorators(array(
						'ViewHelper', 
						array( 'HtmlTag', array('tag' => 'td') ),
					));
		$field_footer = new My_Form_Element_link('field_footer');
		$field_footer->setDecorators(array(array('ViewScript', array(
				    'viewScript' => '_decorator_fields.phtml',
				    'closeOnly' => true,
				))));
					
		// add Elements
		$this->addElements(array(
			$title,
			$permalink,
			$description,
			$excerpt,
			$field_content,
			$field_list,
			$field_name,
			$field_new,
			$field_value,
			$field_footer
		));			
		
		// add group submit
		$this->addDisplayGroup(array(
			'content_name',
			'content_permalink',
			'content_description',
			'content_excerpt',
			'field_content',
			'field_list',
			'field_name',
			'field_new',
			'field_value',
			'field_footer',
		), 
		'editor', 
		array( 
		 	'decorators' => array(
				'FormElements',
				array( array('data' => 'HtmlTag'), array('tag' => 'div', 'id' => 'editor_content', 'class' => 'span8'))
			)
		)); 
		
		/* =================================== STATUS ====================================== */
		
		$publish_header = new My_Form_Element_link('publish_header');
		$publish_header->setDecorators(array(array('ViewScript', array(
				    'viewScript' => '_decorator_wrap.phtml',
				    'openOnly' => true,
				    'title' => 'Publish'
				))));
		// status : select
		$status = $this->createElement('select', 'content_status')
					   ->setLabel('Status')
					   ->setAttrib('class', 'half')
					   ->addMultiOption('L', 'Publish')
					   ->addMultiOption('D', 'Draft')
					   ->setDecorators($this->attribut_decorator);
		// redirect : select
		$redirect = $this->createElement('select', 'redirect')
				         ->setLabel('Direct')
					     ->setAttrib('class', 'half')
					     ->addMultiOptions(array(
					   		'add' => 'Add new',
					   		'index' => 'Back'
					     ))
					  ->setDecorators($this->attribut_decorator);
						 
		// image : input hidden
		$content_image = $this->createElement('hidden', 'content_image')
				   			  ->setDecorators(array('ViewHelper'));
		
		// type : input hidden
		$content_type = $this->createElement('hidden', 'content_type')
				   		   	 ->setDecorators(array('ViewHelper'));
		
		// id : input hidden
		$content_id = $this->createElement('hidden', 'content_id')
				   		   ->setDecorators(array('ViewHelper'));
		
		// submit : button
		$submit = $this->createElement('submit', 'submit')
					   ->setLabel('Save')
					   ->setAttrib('class', 'btn btn-primary')
					   ->setDecorators(array('ViewHelper'));
		// portlet footer			   
		$publish_footer = new My_Form_Element_link('publish_footer');
		$publish_footer->setDecorators(array(array('ViewScript', array(
				    'viewScript' => '_decorator_wrap.phtml',
				    'closeOnly' => true
				))));
		
		/* =================================== ATRRIBUTES ====================================== */
		
		$attribut_header = new My_Form_Element_link('attribut_header');
		$attribut_header->setDecorators(array(array('ViewScript', array(
				    'viewScript' => '_decorator_wrap.phtml',
				    'openOnly' => true,
				    'title' => 'Atributes'
				))));
		// parent : select
		$parent = $this->createElement('select', 'content_parent')
					   ->setLabel('Parent')
					   ->addMultiOption(0, 'None')
					   ->setDecorators($this->attribut_decorator);
		$parents = Model_Content::GetContents( $db, array('type' => 'page', 'parent_in' => 0, 'order' => 'content_id') );
		if($parents)
		{
			foreach($parents as $p)
			{
				$parent->addMultiOption($p->getId(), ucwords($p->content_name)); // tambahkan option kategori
			}
		}
		
		// order : input
		$order = $this->createElement('text', 'content_order')
					  ->addValidator('Int')
			 		  ->setAttrib('style', 'width:20px')
					  ->setLabel('Order')
					  ->setValue(0)
					  ->setDecorators($this->attribut_decorator);
		// portlet footer			   
		$attribut_footer = new My_Form_Element_link('attribut_footer');
		$attribut_footer->setDecorators(array(array('ViewScript', array(
				    'viewScript' => '_decorator_wrap.phtml',
				    'closeOnly' => true
				))));
				
		/* =================================== CATEGORIES ====================================== */
					
		$category_header = new My_Form_Element_link('category_header');
		$category_header->setDecorators(array(array('ViewScript', array(
				    'viewScript' => '_decorator_wrap.phtml',
				    'openOnly' => true,
				    'title' => 'Categories'
				))));  
		// categories : tree checkbox
		$categories = new My_Form_Element_TreeView('content_categories');
		// populate categories
		$categories->setDecorators(array(
						'ViewHelper', 
						array( 'HtmlTag', array('tag' => 'div', 'id' => 'treecheckbox', 'class' => 'control-group') ),
						array( 'Label', array('class' => 'control-label alignleft quarter') ),
						array( array('row' => 'HtmlTag'), array('tag' => 'fieldset', 'openOnly' => true) )
					));
					
		// add category => link JS Collapse
		$add_category = new My_Form_Element_link('add_category');
		$add_category->setValue('Add category')
					 ->setAttribs(array(
					 		'icon' => 'icon-plus',
						 	'href' => '#category',
						 	'data-toggle' => 'collapse'
					 ))
					->setDecorators(array(
						'ViewHelper', 
						array( array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group', 'openOnly' => true) ),
					));
		// add category input : input text => class full
		$add_category_input = $this->createElement('text', 'add_category_input');
		$add_category_input->setAttribs(array(
						 		 'style' => 'width:95%', 
						 		 'placeholder' => 'Category name'
							 ))
					 		->addFilters(array('StringTrim'))
				    		->setDecorators(array(
								'ViewHelper', 
								array( 'Label', array('class' => 'control-label alignleft quarter') ),
								array( array('row' => 'HtmlTag'), array('tag' => 'div', 'id' => 'category', 'class' => 'control-group collapse', 'openOnly' => true) )
							));
		// add category parent : select
		$add_category_parent = $this->createElement('select', 'add_category_parent');
		$add_category_parent->setAttrib('class','full')
					   		->addMultiOption(0, '— Parent Category —')
					   		->setDecorators(array('ViewHelper'));
		$db = Zend_Registry::get( 'db' );
		$parents = Model_Term::GetTerms( $db, array('taxonomy' => 'category', 'parent_in' => 0, 'order' => 'term_id') );
		if($parents)
		{
			foreach($parents as $p)
			{
				$add_category_parent->addMultiOption($p['term_id'], ucwords($p['term_name'])); // tambahkan option kategori
			}
		}
		// add category submit : link button
		$add_category_submit = new My_Form_Element_link('add_category_submit');
		$add_category_submit->setValue('Add Category')
							->setAttribs(array(
								'class' => 'btn',
							 	'href' => '#'
							))
							->setDecorators(array(
								'ViewHelper', 
								array( 'HtmlTag', array('tag' => 'fieldset', 'closeOnly' => true) )
							));
		// portlet footer			   
		$category_footer = new My_Form_Element_link('category_footer');
		$category_footer->setDecorators(array(array('ViewScript', array(
				    'viewScript' => '_decorator_wrap.phtml',
				    'closeOnly' => true
				))));
		
		/* =================================== TAGS ====================================== */
		
		$tag_header = new My_Form_Element_link('tag_header');
		$tag_header->setDecorators(array(array('ViewScript', array(
				    'viewScript' => '_decorator_wrap.phtml',
				    'openOnly' => true,
				    'title' => 'Tags'
				))));  
		// tags => jQuery autocomplete
		$tags = new ZendX_JQuery_Form_Element_AutoComplete('content_tags', array('rows' => 2));
		$tags->setJQueryParams( array(
									"source" => new Zend_Json_Expr(
												"function( request, response ) {
													$.getJSON( Editor.baseUrl + '/../admin/ajax/content', {
														act: 'tag',
														term: Tags.setLast( request.term )
													}, response );
												}"),
									"select" => new Zend_Json_Expr(
												"function( event, ui ) {
													var terms = Tags.split( this.value ),
														tag = ui.item.value;
													this.value = Tags.getTerms(terms, tag, 0);
													return false;
												}")
	    	))
			->setAttrib('class', 'full')
			->setDescription('Keywords separated by comma')
			->setDecorators(array(
				'UiWidgetElement', 
				array( 'Description', array('tag' => 'p', 'class' => 'help-block') ),  
				array( 'Label', array('class' => 'control-label alignleft quarter') ),
				array( 'HtmlTag', array('tag' => 'div', 'class' => 'control-group') ),
			)); 
		// recent tag => link JS Collapse
		$recent_tag = new My_Form_Element_link('recent_tag');
		$recent_tag->setValue('Choose from the most recent tags')
				->setAttribs(array(
					'icon' => 'icon-tags',
				 	'href' => '#recentTags',
				 	'data-toggle' => 'collapse'
				))
				->setDecorators($this->attribut_decorator);
		// tag cloud => Html
		$_tags = Model_Term::GetTagCloud( $db );
		$tag_cloud = new My_Form_Element_Html('tag_cloud');
		$tag_cloud->setValue( $_tags )
				  ->setAttribs(array(
					  'id' => 'recentTags', 
					  'class' => 'collapse'
				  ))
				  ->setDecorators(array('ViewHelper'));
		// portlet footer			   
		$tag_footer = new My_Form_Element_link('tag_footer');
		$tag_footer->setDecorators(array(array('ViewScript', array(
				    'viewScript' => '_decorator_wrap.phtml',
				    'closeOnly' => true
				))));
				  
		/* =================================== FEATURED IMAGE ====================================== */
		
		$featured_header = new My_Form_Element_link('featured_header');
		$featured_header->setDecorators(array(array('ViewScript', array(
				    'viewScript' => '_decorator_wrap.phtml',
				    'openOnly' => true,
				    'title' => 'Featured Image'
				))));  
		// set featured image => button link JS Modal
		$set_featured_img = new My_Form_Element_link('set_featured_image');
		$set_featured_img->setValue('Set featured image')
						 ->setAttribs(array(
						 	'href' => '#modalImage',
						 	'rel' => 'popup',
				          	'data-title' => 'Featured Image',
				          	'data-toggle' => 'modal'
						 ))
					     ->setDecorators(array(
							'ViewHelper', 
							array( 'HtmlTag', array('tag' => 'div', 'class' => 'control-group', 'openOnly' => true) )
						));
		// featured image => html
		$featured_img = new My_Form_Element_Html('featured_image');
		$featured_img->setDecorators(array(
						'ViewHelper', 
						array( 'HtmlTag', array('tag' => 'div', 'closeOnly' => true) )
					));
		// portlet footer			   
		$featured_footer = new My_Form_Element_link('featured_footer');
		$featured_footer->setDecorators(array(array('ViewScript', array(
				    'viewScript' => '_decorator_wrap.phtml',
				    'closeOnly' => true
				))));
		
		// add Elements
		$this->addElements(array(
			/* status */
			$publish_header,
			$status,
			$redirect,
			$content_image,
			$content_type,
			$content_id,
			$submit,
			$publish_footer,
			/* atrributes */
			// content categories
			$category_header,
			$categories,
			$add_category,
			$add_category_input,
			$add_category_parent,
			$add_category_submit,
			$category_footer,
			// content tags
			$tag_header,
			$tags,
			$recent_tag,
			$tag_cloud,
			$tag_footer,
			// content parent & order
			$attribut_header,
			$parent,
			$order,
			$attribut_footer,
			/* featured image */
			$featured_header,
			$set_featured_img,
			$featured_img,
			$featured_footer
		));
		
		// add group atrribut
		$this->addDisplayGroup(array(
			/* status */
			'publish_header',
			'content_status',
			'redirect',
			'content_image',
			'content_type',
			'content_id',
			'submit',
			'publish_footer',
			/* atrributes */
			// content categories
			'category_header',
			'content_categories',
			'add_category',
			'add_category_input',
			'add_category_parent',
			'add_category_submit',
			'category_footer',
			// content tags
			'tag_header',
			'content_tags',
			'recent_tag',
			'tag_cloud',
			'tag_footer',
			// content parent & order
			'attribut_header',
			'content_parent',
			'content_order',
			'attribut_footer',
			/* featured image */
			'featured_header',
			'set_featured_image',
			'featured_image',
			'featured_footer'
		), 
		'atrribut', 
		array( 
		 	'decorators' => array(
				'FormElements',
				array( 'HtmlTag', array('tag' => 'div', 'id' => 'attribut_content', 'class' => 'span4')),
			)
		));
		
    }

	/**
	 * setContent
	 * 
	 * @param string
	 * @param bool
	 */
	public function setContent( $type, $setToElement = false )
	{
		$db = Zend_Registry::get( 'db' );
		
		$categories = $this->getElement('content_categories');
		if( $type == 'post' ){ 
			$cats = Model_Term::GetCategoriesForPopulate( $db );
			// populate categories
			$categories->setOptions(array('multioptions' => $cats));
			// remove categories & tags element
			$this->_removeElementParentOrder();
		} elseif( $type == 'page' ) { // remove categories & tags elements
			$this->_removeElementCategories();
			$this->_removeElementTags();
		} else { 
			$cats = Model_Term::GetCategoriesForPopulate( $db, $type );
			// populate categories
			$categories->setOptions(array('multioptions' => $cats));
			// remove categories & tags element
			$this->_removeElementParentOrder();
			$this->_removeElementTags();
		}
		
		// set value element content_type
		if( $setToElement )
			$this->getElement('content_type')->setValue( $type );
	}

	protected function _removeElementCategories()
	{
		// remove categories
		$this->removeElement('category_header');
		$this->removeElement('content_categories');
		$this->removeElement('add_category');
		$this->removeElement('add_category_input');
		$this->removeElement('add_category_parent');
		$this->removeElement('add_category_submit');
		$this->removeElement('category_footer');
	}

	protected function _removeElementTags()
	{
		$this->removeElement('tag_header');
		$this->removeElement('content_tags');
		$this->removeElement('recent_tag');
		$this->removeElement('tag_cloud');
		$this->removeElement('tag_footer');
	}

	protected function _removeElementParentOrder()
	{
		// remove categories
		$this->removeElement('attribut_header');
		$this->removeElement('content_parent');
		$this->removeElement('content_order');
		$this->removeElement('attribut_footer');
	}
	
	/**
	 * setEdit
	 * tambah opsi redirect edit, dan selected
	 */
	public function setEdit()
	{
		$this->getElement( 'redirect' )
			 ->addMultiOption('edit', 'Edit')
			 ->setValue('edit');
	}
	
	/**
	 * setPermalink
	 * 
	 * @param string
	 * @param int
	 */
	public function setPermalink( $slug, $id )
	{
		// Get View
		$view = Zend_Layout::getMvcInstance()->getView();
		
		$permalink = sprintf('<div id="edit-slug-box">
			<strong>Permalink:</strong>
			<span id="sample-permalink">
				%s/<span title="Click to edit this part of the permalink" id="editable-content-slug">%s</span>/ 
				</span>&lrm; <span id="edit-slug-buttons"> 
					<a id="editPermalink" class="edit-slug btn" data-parameter="%d" href="#">Edit</a> 
				</span>
			<input type="hidden" name="content_slug" value="%s" />
		</div>', $view->baseUrl(false), $slug, $id, $slug );
		$this->getElement('content_permalink')->setValue($permalink);
	}
	
	/**
	 * setFeaturedImage
	 * 
	 * @param string
	 */
	public function setFeaturedImage( $image )
	{
		if( $image )
		{
			// hide link featured image
			$this->getElement('set_featured_image')
				 ->setAttrib('style','display:none');
			// set featured image
			$this->getElement('featured_image')
				 ->setvalue('
				 	<img src="'.$image.'" /><br/>
				 	<a href="#" class="remove-featured-image">Remove featured image</a>
				 ');
		}
	}
	
	/**
	 * setFieldId
	 * 
	 * @param int
	 */
	public function setFieldId( $content_id )
	{
		$this->getElement('field_content')->setValue($content_id);
	}

}



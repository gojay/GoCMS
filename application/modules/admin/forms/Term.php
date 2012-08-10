<?php
class Admin_Form_Term extends My_Form
{
	public function init()
	{
		$db = Zend_Registry::get( 'db' );

		// title : input text => class full
		$title = $this->createElement('text', 'term_name')
					  ->addFilters(array('StringTrim'))
					  ->setLabel('Name')
			 		  ->setAttribs(array(
				 		 'class' => 'full', 
						 'rel' => 'tooltip',
						 'title' => 'Enter title'
					 ))
					 ->setDescription('The name is how it appears on your site.')
				     ->setDecorators(array(
						'ViewHelper', 
						'Label',
						array( 'Description', array('tag' => 'p', 'class' => 'help-block') ),
						array( 'HtmlTag', array('tag' => 'div', 'class' => 'control-group') )
					));
		
		// slug : input text => class full
		$slug = $this->createElement('text', 'term_slug')
					  ->addFilters(array('StringTrim', 'StringToLower'))
					  ->setLabel('Slug')
			 		  ->setAttribs(array(
				 		 'class' => 'full', 
					 ))
					 ->setDescription('The "slug" is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens ( or just leave it blank to create automaticly )')
				     ->setDecorators(array(
						'ViewHelper', 
						'Label',
						array( 'Description', array('tag' => 'p', 'class' => 'help-block') ),
						array( 'HtmlTag', array('tag' => 'div', 'class' => 'control-group') )
					));
					
		// parent (category) : select
		$parent = $this->createElement('select', 'term_parent')
					   ->addMultiOption(0, 'None')
					   ->setLabel('Parent')
					   ->setDescription('Categories, unlike tags, can have a hierarchy (optional).')
					   ->setDecorators(array(
							'ViewHelper', 
							'Label',
							array( 'Description', array('tag' => 'p', 'class' => 'help-block') ),
							array( 'HtmlTag', array('tag' => 'div', 'class' => 'control-group') )
					   ));
		$parents = Model_Term::GetTerms( $db, array('taxonomy' => 'category', 'parent_in' => 0, 'order' => 'term_name') );
		if($parents)
		{
			foreach($parents as $p)
			{
				$parent->addMultiOption($p['term_id'], $p['term_name']); // tambahkan option parent
			}
		}
					
		// description : textarea
		$description = $this->createElement('textarea', 'term_description')
					  		->setLabel('Description')
				 		 	->setAttribs(array(
						 		 'class' => 'full', 
						 		 'rows' => 5
							))
					   		->setDescription('The description is not prominent by default; however, some themes may show it.')
				     		->setDecorators(array(
								'ViewHelper',
								'Label',
								array( 'Description', array('tag' => 'p', 'class' => 'help-block') ),
								array( 'HtmlTag', array('tag' => 'div', 'class' => 'control-group') )
							));
		// id : input hidden
		$term_id = $this->createElement('hidden', 'term_id')
				   		   ->setDecorators(array('ViewHelper'));
						   
		// id : input hidden
		$term_taxonomy = $this->createElement('hidden', 'term_taxonomy')
				   		   ->setDecorators(array('ViewHelper'));
		
		// cancel : button
		$reset = $this->createElement('reset', 'reset')
					   ->setLabel('Cancel')
					   ->setAttrib('class', 'btn')
					   ->setDecorators(array('ViewHelper'));
		// save : button
		$save = new My_Form_Element_link('save_term');
		$save->setValue('Save')
			 ->setAttribs(array(
				 'class' => 'btn',
				 'spin' => true
			 ))
			 ->setDecorators(array('ViewHelper'));
					   
		// add Elements
		$this->addElements(array(
			$title,
			$slug,
			$parent,
			$description,
			$term_id,
			$term_taxonomy,
			$reset,
			$save,
		));			
		
		// add group submit
		$this->addDisplayGroup(array(
			'term_name',
			'term_slug',
			'term_parent',
			'term_description',
			'term_id',
			'term_taxonomy',
			'reset',
			'save_term',
		), 'editor'); 
		
	}

	public function setTaxonomy( $taxonomy )
	{
		if( $taxonomy == 'tag' ) {
			$this->removeElement('term_parent');
		}
		
		$this->getDisplayGroup('editor')
			 ->setAttrib('legend', sprintf('Add New %s', ucwords($taxonomy)))
			 ->setDecorators(array( 
				'FormElements',
				'Fieldset'
			 ));
		// set value term_taxonomy
		$this->getElement('term_taxonomy')->setValue( $taxonomy );
	}

}

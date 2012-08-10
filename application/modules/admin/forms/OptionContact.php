<?php
class Admin_Form_OptionContact extends My_Form
{
	protected $decorators_button = array(
		'ViewHelper', 
		array( 'HtmlTag', array( 'tag' => 'li' )), 
		array( array('data' => 'HtmlTag'), array(
				'tag' => 'dd', 
				'class' => 'button-switch', 
				'style' => 'margin:10px 0', 
			)
		)
	);
	
	public function init()
	{
		 /**
		  * Addresses
		  * office address : text
		  * office phone : text
		  * office fax : text
		  * office email : text
		  * 
		  * Contact
		  * email : text
		  * map : text large
		  * 
		  */
		  
		// ambil option value pada option_office
		$db = Zend_Registry::get( 'db' );	 
		$option = new Model_Option( $db );
        $data = $option->getOption( 'option', 'option_office' );
		
		// addElement => html
		$add = new My_Form_Element_Html('addElement');
		$add->setValue('
			<div class="widget add-new-widget">
				<a href="#" id="addElement">
					<span>Add</span>
					<strong>Add Office</strong>
				</a>
			</div>
		')
		->setAttrib('style', 'overflow:hidden')
		->setDecorators(array('ViewHelper'));

		// hidden id untuk nilai index address yang ditambahkan
		$office_id = $this->createElement('hidden', 'office_id', array('value' => count($data['value'])))
						  ->setDecorators(array('ViewHelper'));
		  
		$submit = $this->createElement('submit', 'submit', array('value' => 'Save'))
					   ->setAttrib('class', 'button')
					   ->setDecorators(array('ViewHelper'));
					   
		// add Elements
		$this->addElements(array(
			$add,
			$office_id,
			$submit
		));
		
		// add group contact
		$this->addDisplayGroup(
			array(
				'addElement',
				'office_id',
				'submit'
			), 
			'office', 
			array( 
			 	'decorators' => array( 'FormElements' )
			)
		);
		
		/** ========================== OFFICE ADDRESS ========================== **/
		
		$elementName = array();
		if( $data['value'] )
		{
			foreach( $data['value'] as $index => $office )
			{
				 // office city : input text => class small
				$office_city = $this->createElement('text', 'office_city_'.$index)
							 ->setLabel('City')
					 		 ->setAttrib('class', 'small')
							 ->setValue( $office['city'] )
							 ->addFilters(array('StringTrim'))
							 ->addValidators(array('NotEmpty'))
							 ->setDecorators($this->decorators);
				 // office address : input text => class medium
				$office_address = $this->createElement('text', 'office_address_'.$index)
							 ->setLabel('Address')
					 		 ->setAttrib('class', 'medium')
							 ->setValue( $office['address'] )
							 ->addFilters(array('StringTrim'))
							 ->addValidators(array('NotEmpty'))
							 ->setDecorators($this->decorators);
				
				 // office phone : input text => class medium
				$office_phone = $this->createElement('text', 'office_phone_'.$index)
							 ->setLabel('Phone')
					 		 ->setAttrib('class', 'medium')
							 ->setValue( $office['phone'] )
							 ->addFilters(array('StringTrim'))
							 ->addValidators(array('NotEmpty'))
							 ->setDecorators($this->decorators);
				 // office fax : input text => class medium
				$office_fax = $this->createElement('text', 'office_fax_'.$index)
							 ->setLabel('Fax')
					 		 ->setAttrib('class', 'small')
							 ->setValue( $office['fax'] )
							 ->addFilters(array('StringTrim'))
							 ->setDecorators($this->decorators);
				 // office email : input text => class small
				$office_email = $this->createElement('text', 'office_email_'.$index)
							 ->setLabel('Email')
					 		 ->setAttrib('class', 'small')
							 ->setValue( $office['email'] )
							 ->addFilters(array('StringTrim'))
							 ->addValidators(array('NotEmpty', 'EmailAddress'))
							 ->setDecorators($this->decorators);
				// editElement			 
				$edit = $this->createElement('button', 'editElement')
					 ->setLabel('Edit')
					 ->setAttrib('class', 'green')
					 ->setDecorators($this->decorators_button); 
				// removeElement			 
				$remove = $this->createElement('button', 'removeElement')
					 ->setLabel('Delete')
					 ->setAttrib('class', 'gray')
					 ->setDecorators($this->decorators_button);  
					 
				// tambah element
				$this->addElements(array(
					$office_city,
					$office_address,
					$office_phone,
					$office_fax,
					$office_email,
					$edit,
					$remove
				));	
						 
				// set element id ke dalam array untuk set display group		 
				$elements[] = array(
					'office_city_'.$index,
					'office_address_'.$index,
					'office_phone_'.$index,
					'office_fax_'.$index,
					'office_email_'.$index,
					'editElement',
					'removeElement',
				);	 
			}
			
			// ambil jumlah element untuk index element terakhir
			$count = count($elements);
			if( $count ){
				// looping elements
				foreach( $elements as $index => $element)
				{
					$position = $index + 1;
					
					// decorator tag html
					// jika group pertama buka tag div #addresses
					if($index == 0){
						$htmlTag = array('HtmlTag', array('tag' => 'div', 'id' => 'addresses', 'style' => 'margin-top:15px', 'openOnly' => true));
					} 
					// tutup tag div, jika group terakhir
					elseif(end($elements)){
						$htmlTag = array('HtmlTag', array('tag' => 'div', 'closeOnly' => true));
					} 
					// lainnya tidak menggunakan decorator tag html
					else{
						$htmlTag = array();
					} 
					
					// add display group fieldset tiap element
					$this->addDisplayGroup(
						$element,
						'office_' . $index, 
						array( 
							'legend' => 'Office ' . $position,
						 	'decorators' => array(
								'FormElements',
								'Fieldset',
		           				$htmlTag
							)
						)
					);
				}
			}
		} 
			 
	}
}

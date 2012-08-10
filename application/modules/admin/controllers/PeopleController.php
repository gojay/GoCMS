<?php

class Admin_PeopleController extends Zend_Controller_Action
{

    public function init()
    {
        $this->db = Zend_Registry::get( 'db' );
    }

    public function indexAction()
    {
    	$this->view->headTitle('Peoples');
		
		$options = array(
			'show' 	=> 10,
			'range' => 3,
			'page' 	=> $this->_request->getParam('page'),
			'type'  => 'people'
		);
        $paginator = Model_Content::GetPagination( $this->db, $options );
		$this->view->paginator = $paginator;
		
        $data = Model_Content::GetContents( $this->db, $options );
		$this->view->data = $data;
    }

    public function addAction()
    {
    	$this->view->headTitle('Add People');
		
		/* FORM */
        // definisi model upload, parameter form
		$modelUpload = new Model_UploadImage('Admin_Form_Page');
		// ambil form
		$form = $modelUpload->getForm();
		// atribut ajax url untuk validasi
		$form->setAttrib( 'ajax-url', $this->_helper->url->simple( 'validate' ) );
		// set form action
		$form->setAction( $this->_helper->url->simple( 'add' ) );
		
		/* ELEMENT */
		// label content_name = Name
		$form->getElement('content_name')->setLabel('Name');
		// set multi option content_parent
		$parent = $form->getElement('content_parent');
		$multiOptions = Model_Content::GetContentsToPopulate( $this->db );
		$parent->setMultiOptions( $multiOptions );
		// remove order
		$form->removeElement('content_order');
		// remove image
		$form->removeElement('image');
		// remove image_preview
		$form->removeElement('image_preview');
		
		/* PROCESS */
		if( $this->_request->isPost() && 
			$form->isValid($this->_request->getPost()))
		{
			/* UPLOAD */
			// set nama file dari nama
			//$modelUpload->setName( $form->getValue( 'content_name' ) );
			//$modelUpload->uploadFile();
			
			/* DATA */
			$data = $form->getValues();
			// array merge data image
			$data = array_merge($form->getValues(), array(
					'attach_file' => array(
						'file_type' => $modelUpload->getFileExtension(),
						'file_name' => $modelUpload->getFileName()
					)
				)
			);
			
			/* SAVE */
			$people = new Model_Content( $this->db );
			$people->content_type = 'people';
			$people->content_parent = $data['content_parent'];
			$people->content_name = $data['content_name'];
			$people->content_description = $data['content_description'];
			$people->content_status = $data['content_status'];
			// attach file simpan di profile
			if( isset($data['attach_file']['file_type']) && 
				isset($data['attach_file']['file_type']) )
			{
				$people->profile->file_type = $data['attach_file']['file_type'];
				$people->profile->file_name = $data['attach_file']['file_name'];
			}
			$isSaved = $people->save();
			if( $isSaved ){
				$this->_helper->FlashMessenger( array('success' => 'People added') );
			} else {
				$this->_helper->FlashMessenger( array('error' => 'Error occured. Please try again later') );
			}
			
			/* REDIRECT */
			$this->_helper->redirector->gotoSimple( $data['redirect'], 'people', 'admin' );
		}
		
		$this->view->form = $form;
    }

    public function editAction()
    {
    	$this->view->headTitle('Edit People');
		
		// ambil id
		$id = $this->_request->getParam('id');
		$people = new Model_Content( $this->db );
		// cek news. jika tidak ada redirect index dan krimi pesan error
		if( !$people->load( $id ) )
		{
			$this->_helper->FlashMessenger( array( 'error' => 'People not found' ) );
			$this->_redirect( '/admin/people/' );
		}
		
		/* FORM */
		// definisi model uopload image, parameter form
		$modelUpload = new Model_UploadImage( 'Admin_Form_Page' );
		// ambil form
		$form = $modelUpload->getForm();
		// attribut ajax url untuk ajax validasi
		$form->setAttrib( 'ajax-url', $this->_helper->url->simple( 'validate' ) );
		// attribut use, element image tidak memerlukan ajax validasi
		$form->image->setAttrib( 'use', 'false' );
		// set form action
		$form->setAction( $this->_helper->url->simple( 'edit' ) . '/id/' . $id );
		// populate form
		$form->populate( $people->toArray() );
		
		/* ELEMENT */
		// label content_name = Name
		$form->getElement('content_name')->setLabel('Name');
		// set multi option content_parent
		$parent = $form->getElement('content_parent');
		$multiOptions = Model_Content::GetContentsToPopulate( $this->db );
		$parent->setMultiOptions( $multiOptions );
		// remove image
		if( isset($people->profile->file_type) || 
			isset($people->profile->file_name) )
		{
			$form->removeElement('image');
			$attach_file = $form->getElement('attach_file');
			$attach_file->setAttrib('style', 'display:block')
						->setValue('
						<div class="attach_file_'.$people->profile->file_type.'">
						<a href="'.$this->view->baseUrl().'/uploads/file/'.$people->profile->file_name.'">'
						.$people->profile->file_name.
						'</a>
						</div>');
		}
		// remove order
		$form->removeElement('content_order');
		// remove image
		$form->removeElement('image');
		// remove image_preview
		$form->removeElement('image_preview');
		
		// tambah opsi redirect edit, dan selected
		$redirect = $form->getElement( 'redirect' );
		$redirect->addMultiOption('edit', 'Edit');
		$redirect->setValue('edit');
		
		/* PROCESS */
		if( $this->_request->isPost() && 
			$form->isValid($this->_request->getPost()))
		{
			$name = $form->getValue( 'content_name' );
			
			/* UPLOAD */
			// set nama file dari nama
			//$modelUpload->setName( $form->getValue( 'content_name' ) );
			//$modelUpload->uploadFile();
			
			/* DATA */
			$data = $form->getValues();
			// array merge data image
			$data = array_merge($form->getValues(), array(
					'attach_file' => array(
						'file_type' => $modelUpload->getFileExtension(),
						'file_name' => $modelUpload->getFileName()
					)
				)
			);
			
			/* SAVE */
			$people->content_type = 'people';
			$people->content_parent = $data['content_parent'];
			$people->content_name = $data['content_name'];
			$people->content_description = $data['content_description'];
			$people->content_status = $data['content_status'];
			// attach file simpan di profile
			if( isset($data['attach_file']['file_type']) && 
				isset($data['attach_file']['file_type']) )
			{
				$people->profile->file_type = $data['attach_file']['file_type'];
				$people->profile->file_name = $data['attach_file']['file_name'];
			}
			$isSaved = $people->save();
			if($isSaved){
				$this->_helper->FlashMessenger(array('success' => sprintf('People "%s" successfully updated', $name)));
			} else {
				$this->_helper->FlashMessenger(array('error' => 'Error occured. Please try again later'));
			}
			
			/* REDIRECT */
			// jika edit set parameter
			$params = ( $data['redirect'] == 'edit') ? array('id' => $data['content_id']) : array();
			$this->_helper->redirector->gotoSimple( $data['redirect'], 'people', 'admin', $params );
		}
		
		$this->view->form = $form;
    }

    public function deleteAction()
    {
        $id = $this->_request->getParam( 'id' );
		$people = new Model_Content( $this->db );
		// cek portfolio
		if( !$people->load( $id ) ){
			$this->_helper->FlashMessenger( array('error' => 'People not found') );
		} else {
			if( $people->delete() )
				$this->_helper->FlashMessenger( array('success' => 'People deleted') );
			else
				$this->_helper->FlashMessenger( array('error' => 'Error occured. Please try again') );
		}
		
		/* REDIRECT */
		$this->_helper->redirector->gotoSimple( 'index', 'people', 'admin' );
    }

    public function validateAction()
    {
		$form = new Admin_Form_Page();
		$form->isValid( $this->_request->getPost() );
		// get messages form validasi
		$response = $form->getMessages();
		// kirim output response dengan json
		$this->_helper->json( $response );
    }
}


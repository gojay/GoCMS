<?php

class Admin_PortfolioController extends Zend_Controller_Action
{

    public function init()
    {
        $this->db = Zend_Registry::get( 'db' );
    }

    public function indexAction()
    {
    	$this->view->headTitle('Portfolios');
		
		$options = array(
			'show' 	=> 10,
			'range' => 3,
			'page' 	=> $this->_request->getParam('page'),
			'type'  => Model_Content::CONTENT_GALLERY
		);
        $paginator = Model_Content::GetPagination( $this->db, $options );
		$this->view->paginator = $paginator;
		
        $data = Model_Content::GetContents( $this->db, $options );
		$this->view->data = $data;
    }

    public function addAction()
    {
    	$this->view->headTitle('Add Portfolio');
		
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
		// set required true element image
		$form->getElement('image')->setRequired(TRUE);
		// remove content_parent
		$form->removeElement('content_parent');
		// remove file
		$form->removeElement('file');
		// remove order
		$form->removeElement('content_order');
		
		/* PROCESS */
		if( $this->_request->isPost() && 
			$form->isValid($this->_request->getPost()))
		{
			/* UPLOAD */
			// nama file gambar dari nama portfolio
			$modelUpload->setName( $form->getValue( 'content_name' ) );
			$modelUpload->upload();
			
			/* DATA */
			$data = $form->getValues();
			// array merge data image
			$data = array_merge($form->getValues(), array('content_image' => $modelUpload->getFileName()));
			
			/* SAVE */
			$portfolio = new Model_Content( $this->db );
			$isSaved = $portfolio->saveGallery( $data );
			if( $isSaved ){
				$this->_helper->FlashMessenger( array('success' => 'Portfolio added') );
			} else {
				$this->_helper->FlashMessenger( array('error' => 'Error occured. Please try again later') );
			}
			
			/* REDIRECT */
			$this->_helper->redirector->gotoSimple( $data['redirect'], 'portfolio', 'admin' );
		}
		
		$this->view->form = $form;
    }

    public function editAction()
    {
    	$this->view->headTitle('Edit Portfolio');
		
		// ambil id
		$id = $this->_request->getParam('id');
		$portfolio = new Model_Content( $this->db );
		// cek news. jika tidak ada redirect index dan krimi pesan error
		if( !$portfolio->load( $id ) )
		{
			$this->_helper->FlashMessenger( array( 'error' => 'Portfolio not found' ) );
			$this->_redirect( '/admin/portfolio/' );
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
		$form->populate( $portfolio->toArray() );
		
		/* ELEMENT */
		// remove content_parent
		$form->removeElement('content_parent');
		// remove file
		$form->removeElement('file');
		// remove order
		$form->removeElement('content_order');
		
		// show element image untuk preview
		$imagePreview = $form->getElement( 'image_preview' );
		$imagePreview->setAttrib( 'src', $this->view->baseUrl() . '/uploads/image/' . $portfolio->content_image )
				 	 ->setAttrib( 'style', 'display:block; width:200px; height:auto;' );
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
			// set nama file gambar dari nama portfolio
			$modelUpload->setName($name);
			$modelUpload->upload();
			
			/* DATA */
			$data = $form->getValues();
			if( $form->image->isUploaded() )
			{
				// array merge data image
				$data = array_merge( $form->getValues(), array( 'content_image' => $modelUpload->getFileName() ));
			}
			
			/* SAVE */
			$isSaved = $portfolio->saveGallery( $data );
			if($isSaved){
				$this->_helper->FlashMessenger(array('success' => sprintf('Portfolio "%s" successfully updated', $name)));
			} else {
				$this->_helper->FlashMessenger(array('error' => 'Error occured. Please try again later'));
			}
			
			/* REDIRECT */
			// jika edit set parameter
			$params = ( $data['redirect'] == 'edit') ? array('id' => $data['content_id']) : array();
			$this->_helper->redirector->gotoSimple( $data['redirect'], 'portfolio', 'admin', $params );
		}
		
		$this->view->form = $form;
    }

    public function deleteAction()
    {
        $id = $this->_request->getParam( 'id' );
		$portfolio = new Model_Content( $this->db );
		// cek portfolio
		if( !$portfolio->load( $id ) ){
			$this->_helper->FlashMessenger( array('error' => 'Portfolio not found') );
		} else {
			if( $portfolio->delete() )
				$this->_helper->FlashMessenger( array('success' => 'Portfolio deleted') );
			else
				$this->_helper->FlashMessenger( array('error' => 'Error occured. Please try again') );
		}
		
		/* REDIRECT */
		$this->_helper->redirector->gotoSimple( 'index', 'portfolio', 'admin' );
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

    public function galleryAction()
    {
        $this->view->headTitle('Gallery Portfolio');
		
        $id = $this->_request->getParam( 'id' );
        $portfolio = new Model_Content( $this->db );
		if( !$portfolio->load($id) )
		{
			$this->_helper->FlashMessenger( array( 'error' => 'Portfolio not found' ) );
			$this->_redirect( '/admin/portfolio/' );
		}
		
		$this->view->portfolio = $portfolio;
    }


}










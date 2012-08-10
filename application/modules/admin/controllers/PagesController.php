<?php
class Admin_PagesController extends Zend_Controller_Action
{

    public function init()
    {
        $this->db = Zend_Registry::get( 'db' );
    }

    public function indexAction()
    {
    	$this->view->headTitle('Pages');
		
		// ambil semua kontent tipe page
		$this->view->data = Model_Content::GetContentsParentChild( $this->db, array(
																	'type' => Model_Content::CONTENT_PAGE, 
																	'order' => 'content_id',
																	'status' => null
																));
		// ambil semua kontent parent
		$this->view->pages = Model_Content::GetContents( $this->db, array('content_parent' => 0) );
    }

    public function addAction()
    {
    	$this->view->headTitle('Add Page');
		// ambil form
		$form = new Admin_Form_Page();
		// set form action
		$form->setAction( $this->_helper->url->simple( 'add' ) );
		
		// remove tags
		$form->removeElement('content_tags');
		// proses
		if( $this->_request->isPost() && 
			$form->isValid($this->_request->getPost()))
		{
			$data = $form->getValues();
			$content = new Model_Content( $this->db );
			$isSaved = $content->savePage( $data );
			if( $isSaved ){
				$this->_helper->FlashMessenger( array('success' => 'Page added') );
			} else {
				$this->_helper->FlashMessenger( array('error' => 'Error occured. Please try again later') );
			}
			
			/* REDIRECT */
			$this->_helper->redirector->gotoSimple( $data['redirect'], 'pages', 'admin' );
		}
		
		$this->view->form = $form;
    }

    public function editAction()
    {
    	$this->view->headTitle('Edit Page');
		
		// ambil id
		$id = $this->_request->getParam('id');
		$page = new Model_Content( $this->db );
		// cek page. jika tidak ada redirect index dan kirim pesan error
		if( !$page->load( $id ) )
		{
			//throw new Zend_Controller_Action_Exception('This page dont exist',404);
			$this->_helper->FlashMessenger( array( 'error' => 'Page not found' ) );
			$this->_helper->redirector->gotoSimple( 'index', 'pages', 'admin' );
		}
		// ambil form
		$form = new Admin_Form_Page();
		// set form action
		$form->setAction( $this->_helper->url->simple( 'edit' ) . '/id/' . $id );
		// populate data
		$form->populate( $page->toArray() );
		
		// set featured image
		$featured_image = $page->content_image;
		if( $featured_image )
		{
			// hide link featured image
			$form->getElement('set_featured_image')->setAttrib('style','display:none');
			// set featured image
			$form->getElement('featured_image')
				 ->setvalue('
				 	<img src="'.$featured_image.'" /><br/>
				 	<a href="#" class="remove-featured-image">Remove featured image</a>
				 ');
		}
		// set content_slug
		$permalink = sprintf('<div id="edit-slug-box">
			<strong>Permalink:</strong>
			<span id="sample-permalink">
				%s/<span title="Click to edit this part of the permalink" id="editable-content-slug">%s</span>/ 
				</span>&lrm; <span id="edit-slug-buttons"> 
					<a id="editPermalink" class="edit-slug btn" data-parameter="%s" href="#">Edit</a> 
				</span>
			<input type="hidden" name="content_slug" value="%s" />
		</div>', $this->view->baseUrl(false), $page->content_slug, $page->getId(), $page->content_slug );
		$form->getElement('content_permalink')->setValue($permalink);
		// remove tags
		$form->removeElement('content_tags');
		// tambah opsi redirect edit, dan selected
		$redirect = $form->getElement( 'redirect' );
		$redirect->addMultiOption('edit', 'Edit');
		$redirect->setValue('edit');
		
		// proses
		if( $this->_request->isPost() && 
			$form->isValid($this->_request->getPost()))
		{
			// data
			$data = $form->getValues();
			// save
			$isSaved = $page->savePage( $data );
			if($isSaved){
				$this->_helper->FlashMessenger(array('success' => sprintf('Page "%s" successfully updated', $data['content_name'])));
			} else {
				$this->_helper->FlashMessenger(array('error' => 'Error occured. Please try again later'));
			}
			
			// redirect
			// set parameter id, jika direct edit
			$params = ( $data['redirect'] == 'edit') ? array('id' => $data['content_id']) : array();
			$this->_helper->redirector->gotoSimple( $data['redirect'], 'pages', 'admin', $params );
		}
		
		$this->view->form = $form;
    }

    public function deleteAction()
    {
        $id = $this->_request->getParam( 'id' );
		$page = new Model_Content( $this->db );
		// cek evnt
		if( !$page->load( $id ) ){
			$this->_helper->FlashMessenger( array('error' => 'Page not found') );
		} else {
			if( $page->delete() )
				$this->_helper->FlashMessenger( array('success' => 'Page deleted') );
			else
				$this->_helper->FlashMessenger( array('error' => 'Error occured. Please try again') );
		}
		
		/* REDIRECT */
		$this->_helper->redirector->gotoSimple( 'index', 'pages', 'admin' );
    }

}








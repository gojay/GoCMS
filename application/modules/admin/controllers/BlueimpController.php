<?php
class Admin_BlueimpController extends Zend_Controller_Action
{	
	public function init()
	{
		$this->db = Zend_Registry::get('db');
		$this->upload = Model_UploadImage::GetOptions();
		
		$this->option = new Model_Option( $this->db );
		$this->slide = $this->option->getOption( 'gallery', 'gallery_slide' );
	}

	public function indexAction() 
	{
		$content = $this->_request->getParam('content');
		$id = $this->_request->getParam('id');
		
		// get images
		if ($this->_request->isGet()) 
		{
			$this->getAction($id);
		}
		
		// post upload image
		if ($this->_request->isPost()) 
		{
			if( $content == 'slider' ){
				$this->uploadslideAction($id); // slide galery
			} else {
				$this->uploadcontentAction($id); // content gallery
			}
		}
		
		// delete image
		if ($this->_request->isDelete() || $_SERVER['REQUEST_METHOD'] == 'DELETE') 
		{
			$this->deleteAction($id);
		}
	}
	
	public function getAction()
	{
		$content = $this->_request->getParam('content');
		$id = $this->_request->getParam('id');
		
		if( $content == 'slider' )
		{
			// get slider gallery
			$images = $this->slide['value'];
			$data = $this->upload->getSlider($images);
		} else {
			// get content images
			$images = Model_ContentImage::GetImages($this->db, array('content_id' => $id));
			$data = $this->upload->get($images);
		}
			
		$this->_helper->json($data);
	}

	/**
	 * uploadcontentAction
	 * upload content gallery
	 * 
	 * @param int id
	 * @return JSON
	 */
	public function uploadcontentAction() 
	{		
		$id = $this->_request->getParam('id');
		$content = new Model_Content($this->db);
		if( !$content->load($id) )
			$this->_helper->json(array('error' => 'content not found'));
		
		// file adapter
        $adapter = new Zend_File_Transfer_Adapter_Http();		
        $adapter->setDestination($this->upload->options['upload_dir']);
        $adapter->addValidator('Extension', false, 'jpeg, jpg, png, gif');

        $files = $adapter->getFileInfo();
        foreach ($files as $file => $info) 
		{			
			// filename
			$name = $adapter->getFileName($file, false);
			$filename = $content->content_slug . '-' . Model_UploadImage::CreateFilename($name);	
			
			// file uploaded & is valid
			if (!$adapter->isUploaded($file)) continue; 
			if (!$adapter->isValid($file)) continue;
			
			$adapter->addFilter('Rename', array(
                        'target' => $this->upload->options['upload_dir'] 
                                    . DIRECTORY_SEPARATOR
                                    . $filename,
                        'overwrite' => true,
             ));

			// receive the files into the user directory
			$adapter->receive($file); // this has to be on top
			
			// create image thumbnail
			$fullpath = $adapter->getFileName($file);  
			$createdThumbs = Model_UploadImage::CreateThumbnailImage($fullpath, $filename);
		
			// store to database
			if($createdThumbs){
				$content->contentImage->content_id = $id;								
				$content->contentImage->filename = $filename;	
				$content->contentImage->save();
			}			

			// we stripped out the image thumbnail for our purpose, primarily for security reasons
			// you could add it back in here.
			$fileclass = new stdClass();
			$fileclass->name = $filename;  
			$fileclass->size = $adapter->getFileSize($file);  
			$fileclass->type = $adapter->getMimeType($file);  
			$fileclass->delete_url = $this->upload->options['script_url'] . '/index/id/' . $content->contentImage->getId().'/file/'.$filename;
			$fileclass->delete_type = 'DELETE';
			$fileclass->thumbnail_url = $this->upload->options['image_versions']['thumbnail']['upload_url'] . $filename; 
			$fileclass->url = $this->upload->options['upload_url'] . $filename; 

			$datas[] = $fileclass;
		}

		// send response
		$this->_helper->json($datas);
	}

	/**
	 * uploadslideAction
	 * upload slider gallery
	 * 
	 * @param int id
	 * @return JSON
	 */
	public function uploadslideAction() 
	{		
		$index = $this->_request->getParam('id');
		
        $adapter = new Zend_File_Transfer_Adapter_Http();		
        $adapter->setDestination($this->upload->options['upload_dir']);
        $adapter->addValidator('Extension', false, 'jpeg, jpg, png, gif');

        $files = $adapter->getFileInfo();
        foreach ($files as $file => $info) 
		{			
			// filename
			$name = $adapter->getFileName($file, false);
			$filename = 'slide_' . $name;
			// cek is exists
			$file_path = $this->upload->options['upload_dir'] . $filename;
			if (is_file($file_path)){
				$filename = 'slide_' . $index . '_' . $name;
			} 
				
			// file uploaded & is valid
			if (!$adapter->isUploaded($file)) continue; 
			if (!$adapter->isValid($file)) continue;
			
			$adapter->addFilter('Rename', array(
                        'target' => $this->upload->options['upload_dir'] 
                                    . DIRECTORY_SEPARATOR
                                    . $filename,
                        'overwrite' => true,
             ));

			// receive the files into the user directory
			$isReceived = $adapter->receive($file); // this has to be on top
			
			// store to database
			if( $isReceived ){
				$option_id = $this->slide['id'];
				$option_value = ($this->slide['value']) ? array_merge( $this->slide['value'], array($filename) ) : array($filename);
				$this->option->updateOption( serialize($option_value), $option_id);
			}	

			// we stripped out the image thumbnail for our purpose, primarily for security reasons
			// you could add it back in here.
			$fileclass = new stdClass();
			$fileclass->name = $filename;  
			$fileclass->size = $adapter->getFileSize($file);  
			$fileclass->type = $adapter->getMimeType($file);  
			$fileclass->delete_url = $this->upload->options['script_url'] . '/index/id/' .$index .'/file/'.$filename;
			$fileclass->delete_type = 'DELETE';
			$fileclass->url = $this->upload->options['upload_url'] . $filename; 

			$datas[] = $fileclass;
			
		}
		
		// send response
		$this->_helper->json($datas);
	}

	public function deleteAction() 
	{
		$content = $this->_request->getParam('content');	
		$id = $this->_request->getParam('id');
		
		$isRemoved = false;
		
		// konfigurasi file image
        $file_name = $this->_request->getParam('file');
        $file_path = $this->upload->options['upload_dir'] . '/' . $file_name;
        $thumb_path = $this->upload->options['image_versions']['thumbnail']['upload_dir'] . '/' . $file_name;
		
		// slider gallery
		if( isset($content) && $content == 'slider' )
		{
			// hapus image
			$success = is_file($file_path) && unlink($file_path);
			// update option gallery_slide
			if( $success ){
				$values = $this->slide['value'];
				unset( $values[$index] );
				$option_value = array_values($values);
				$option_id = $this->slide['id'];
				$isRemoved = $this->option->updateOption( serialize($option_value), $option_id);
			}
		// portfolio gallery
		} else {
			// hapus image dan thumbnail
			$success = is_file($file_path) && is_file($thumb_path) && unlink($file_path) && unlink($thumb_path);
        	// delete contentImage
			if($success){
				$contentImage = new Model_ContentImage($this->db);
				$contentImage->load($id);
				$isRemoved = $contentImage->delete();
			}
		}
		
		// send response
		$this->_helper->json( $isRemoved );
	}
}
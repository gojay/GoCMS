<?php
class Model_UploadImage
{
	protected $_form;
	
    protected $_upload;
	
    protected $_info = array();
	
    public $name = null;
	
    public $filename = '';
	
	public function __construct($form, $options = array())
	{
		$formName = new $form();
		if($formName instanceof Zend_Form){
			$this->_form = $formName;
		}
		
		$this->_upload = self::GetOptions();
	}
	
    public function getForm()
    {
		return $this->_form;
    }
	
    public function setName($name)
    {
		$this->name = self::CreateFilename($name);
    }
	
    public function getName()
    {
		return $this->name;
    }
	
    public function getFileName()
    {
		return $this->filename;
    }
	
    public function getFileExtension()
    {
		return $this->_info['extension'];
    }
	
	private function _getInfo($fullpath)
	{
		$fileinfo = new SplFileInfo($fullpath);
		$this->_info = array(
			'name' => $fileinfo->getFilename(),
			'path' => $fileinfo->getPath(),
			'extension' => pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION)
		);       
	}
    
    /**
     * 
     * @param array $data
     */
    public function upload()
    {
        $form = $this->getForm();
        if ($form->image->isUploaded()) {
			// create info file
			$this->_getInfo($form->image->getFileName());
			// create name file
			if(!is_null($this->name) || $this->name != ''){
				$this->filename = $this->name . '.' . $this->_info['extension'];
			} else {
				$this->filename = $this->_info['name'];
			}
			// add filter for rename image
            $form->image->addFilter('Rename', array(
                        'target' => $form->image->getDestination()
                                    . DIRECTORY_SEPARATOR
                                    . $this->filename,
                        'overwrite' => true,
             ));
            $form->image->receive();
			// create thumbnail
			self::CreateThumbnailImage(
				$form->image->getFileName(),
				$this->filename
			);
			
			return $this->_isSuccess($this->filename);
       }
    }
    
    /**
     * 
     * @param array $data
     */
    public function uploadFile()
    {
        $form = $this->getForm();
        if ($form->file->isUploaded()) {
			// create info file
			$this->_getInfo($form->file->getFileName());
			// create name file
			if(!is_null($this->name) || $this->name != ''){
				$this->filename = $this->name . '.' . $this->_info['extension'];
			} else {
				$this->filename = $this->_info['name'];
			}
			// add filter for rename image
            $form->file->addFilter('Rename', array(
                        'target' => $form->file->getDestination()
                                    . DIRECTORY_SEPARATOR
                                    . $this->filename,
                        'overwrite' => true,
             ));
			
			return $form->file->receive();
       }
    }
	
	protected function _isSuccess($filename)
	{
		$file_path = $this->_upload->options['upload_dir'] . '/' . $filename;
		$thumb_path = $this->_upload->options['image_versions']['thumbnail']['upload_dir'] . '/' . $filename;
		$success = is_file($file_path) && is_file($thumb_path);
		return $success;
	}
	
	public static function GetOptions()
	{
		return Zend_Registry::get('upload');
	}
    
    /**
	 * Create Thumbnail Image
     * @param array $full_path Absolute path dari gambar
     * @param array $title Title Gambar
     * @return bool
     */	
	public static function CreateThumbnailImage($fullpath, $name)
    {		
		$options = self::GetOptions();
		$imgOptions = $options->options['image_versions']['thumbnail'];
		
		$thumbOptions = array(
            'resizeUp'      => false,
            'jpegQuality'   => 85,
            'correctPermissions' => false,
        );
        
        $thumb = PhpThumbFactory::create($fullpath, $thumbOptions);
		// set size
		if($imgOptions['percent'] > 0){
			$thumb->resizePercent($imgOptions['percent']);		
		} else {			
			$thumb->adaptiveResize(
				$imgOptions['max_width'],
				$imgOptions['max_height']
			);
		}
		
		// path dan filename thumbnail
		$path = $imgOptions['upload_dir'];
		if(!is_null($path)){
			if (!file_exists($path))
				mkdir($path, 0777);
		}

		$filename = $path . $name;		
		// save
        return $thumb->save($filename);
    }
    
    /**
	 * Create costumize image filename 
     * @param string $title
     * @return string costumize title
     */
    public static function CreateFilename($title)
    {
       $filter = new Zend_Filter_Word_SeparatorToDash();
	   return $filter->filter(strtolower($title));
    }
}


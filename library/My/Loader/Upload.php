<?php
class My_Loader_Upload
{
    public $options;
    
    public function __construct($options=array()) 
	{		
		$defaults = array(
            'script_url' => $this->getFullUrl().'/admin/blueimp',
            'upload_dir' => APPLICATION_PATH . '/../public/uploaded/image/',
            'upload_url' => $this->getFullUrl().'/public/uploaded/image/',
            'param_name' => 'files',
            'max_file_size' => null,
            'min_file_size' => 1,
            'accept_file_types' => '/.+$/i',
            'max_number_of_files' => null,
            'discard_aborted_uploads' => true,
            'orient_image' => false,
            'image_versions' => array(
                'thumbnail' => array(
                    'upload_dir' => APPLICATION_PATH . '/../public/uploaded/thumbnail/',
                    'upload_url' => $this->getFullUrl().'/public/uploaded/thumbnail/',
                    'max_width'  => 250,
                    'max_height' => 150,
					'percent' 	 => 0
                )
            )
        );
		
		foreach ($defaults as $k => $v){
			$options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
		}
		
		$this->options = $options;
    }

    private function getFullUrl() 
	{
      	return	(isset($_SERVER['HTTPS']) ? 'https://' : 'http://').
        		(isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
        		(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
        		(isset($_SERVER['HTTPS']) && $_SERVER['SERVER_PORT'] === 443 ||
        		$_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT']))).
        		substr($_SERVER['SCRIPT_NAME'],0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
    }
    
    public function get($images) 
	{
        if(!is_array($images))
			return array();
			
		$file = array();
		$i = 0;
		foreach($images as $image)
		{
			$filename = $image->filename;
			
			$file_path = $this->options['upload_dir'].$filename;
			if (is_file($file_path) && $filename[0] !== '.' && $filename !== 'Thumbs.db') {
				foreach($this->options['image_versions'] as $version => $options) {
					if (is_file($options['upload_dir'].$filename)) {
						$file_thumbs = $options['upload_url'].rawurlencode($filename);
					}
				}
				
				$file[] = array(
					'name' 			=> $filename,
					'size' 			=> filesize($file_path),
					'url' 			=> $this->options['upload_url'].rawurlencode($filename),
					'thumbnail_url' => $file_thumbs,
					'delete_url' 	=> $this->options['script_url'].'/index/id/'.$image->getId().'/file/'.rawurlencode($filename),
					'delete_type' 	=> 'DELETE'					
				);
			}
		}
		
		return $file;
    }
    
    public function getSlider($images) 
	{
        if( !is_array($images) || count($images) == 0 )
			return array();
			
		$file = array();
		$i = 0;
		foreach($images as $index => $image){
			$filename = $image;
			
			$file_path = $this->options['upload_dir'].$filename;
			if (is_file($file_path) && $filename[0] !== '.' && $filename !== 'Thumbs.db') {
				
				$file[] = array(
					'name' 			=> $filename,
					'size' 			=> filesize($file_path),
					'url' 			=> $this->options['upload_url'].rawurlencode($filename),
					'thumbnail_url' => $this->options['upload_url'].rawurlencode($filename),
					'delete_url' 	=> $this->options['script_url'].'/index/id/'.$index.'/file/'.rawurlencode($filename),
					'delete_type' 	=> 'DELETE'					
				);
			}
		}
		
		return $file;
    }
}
?>
<?php
class My_Loader_PhpThumb implements Zend_Loader_Autoloader_Interface
{
    protected $_class_map = array(
        'GdThumb'         => 'GdThumb.inc.php',
        'PhpThumb'        => 'PhpThumb.inc.php',
        'ThumbBase'       => 'ThumbBase.inc.php',  
        'PhpThumbFactory' => 'ThumbLib.inc.php',
        'GdReflectionLib' => 'thumb_plugins/gd_reflection.inc.php',
    );
    
    /**
     * @see Zend_Loader_Autoloader_Interface::autoload()
     * @link http://blog.montmere.com/2010/12/26/autoload-phpthumb-with-zend-framework/
     */
    public function autoload($class)
    {
        $file = APPLICATION_PATH . '/../library/PhpThumb/' . $this->_class_map[$class];
        if (is_file($file)) {
            require_once($file);
            return $class;
        }
        return false;
    }
}

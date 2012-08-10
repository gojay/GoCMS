<?php
class My_Form_Element_FCKEditor extends ZendX_JQuery_Form_Element_UiWidget
{
	public $helper = 'formFCKEditor';
	
	public function __construct($spec, $options = null)
	{
		parent::__construct($spec, $options);
	}
}
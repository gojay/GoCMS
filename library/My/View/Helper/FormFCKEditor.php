<?php
class My_Form_View_Helper_FormFCKEditor extends ZendX_JQuery_View_Helper_UiWidget
{
        /**
        * The default number of rows for a textarea.
        *
        * @access public
        *
        * @var int
        */
        public $rows = 24;
        
        /**
        * The default number of columns for a textarea.
        *
        * @access public
        *
        * @var int
        */
        public $cols = 80;
        
        
        public $height = 300;
        
        
        public function formFCKEditor($id, $value = null, array $params = array(), array $attribs = array())
        {
			$attribs = $this->_prepareAttributes($id, $value, $attribs);
                
			$info = $attribs;
			extract($info);

			$params = ZendX_JQuery::encodeJson($params);
                
			if (empty($attribs['rows'])) {
				$attribs['rows'] = (int) $this->rows;
			}
			if (empty($attribs['cols'])) {
				$attribs['cols'] = (int) $this->cols;
			}
                
			if (empty($attribs['height'])){
				$attribs['height'] = (int) $this->height;
			}
               
			$width = (int) (empty($attribs['width'])) ? '100%' : $attribs['width'];
			$toolbar = (empty($attribs['toolbar'])) ? 'Default' : $attribs['toolbar'];
                
			$js = '$.fck.config = {path: \''.$this->view->baseUrl().'/js/fckeditor/\', width:'.$width.',height:'.$attribs['height'].', toolbar:"'.$toolbar.'"};';
			$js .= "\n";
			$js .= "$('textarea#{$name}').fck(/* default settings */); ";
                
			$xhtml = '<div style="clear:left;width:'.$width.'px;margin:0; border: 1px solid; border-color: #DDDDDD; box-shadow: 0 0 4px #D9D9D9 inset; border-radius: 1px 1px 1px 1px;">
			<textarea name="' . $this->view->escape($name) . '"'
			. ' id="' . $this->view->escape($id) . '"'
			. $this->_htmlAttribs($attribs) . '>'
            . $this->view->escape($value) . '</textarea>
			</div>';
                
			$this->jquery->addJavascriptFile($this->view->baseUrl().'/js/fckeditor/jquery.MetaData.js');
			$this->jquery->addJavascriptFile($this->view->baseUrl().'/js/fckeditor/jquery.form.js');
			$this->jquery->addJavascriptFile($this->view->baseUrl().'/js/fckeditor/jquery.FCKEditor.js');                                           
			$this->jquery->addOnLoad($js);
                                          
			return $xhtml;
        }
}
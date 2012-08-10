<?php
class Zend_View_Helper_FormHtml extends Zend_View_Helper_FormElement
{
    public function formHtml($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable

        // Render the button.
        $xhtml = '<div id="' . $this->view->escape($id) . '" '
            . $this->_htmlAttribs($attribs) . '>'
            . $value . '</div>';

        return $xhtml;
    }
}

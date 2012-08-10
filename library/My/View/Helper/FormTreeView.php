<?php
class Zend_View_Helper_FormTreeView extends Zend_View_Helper_FormElement
{
	protected $_info = null;
	
	protected function _addJavascript( $id )
	{
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$jQuery = $viewRenderer->view->jQuery();
		$jQuery->addJavascriptFile('http://jquery.bassistance.de/treeview/lib/jquery.cookie.js')
			   ->addJavascriptFile('http://jquery.bassistance.de/treeview/jquery.treeview.js')
			   ->addStylesheet('http://jquery.bassistance.de/treeview/demo/screen.css')
			   ->addStylesheet('http://jquery.bassistance.de/treeview/jquery.treeview.css')
			   ->addOnLoad('
					$("#'.$id.'").treeview({
						persist: "cookie",
						cookieId: "treeview-black"
					});
			   ');
	}

	protected function _renderBranch($nodes)
	{
		$id = $this->_info['id'];
		
		if( $nodes ){
			$output = '<ul id="'.$id.'"'.$this->_htmlAttribs($this->_info['attribs']).' class="treeview">';
			foreach ($nodes as $node_id=>$node) 
			{
				$node_control_id = $id.'-'.$node_id;
				$node_control_name = $this->_info['name'];

				if (is_array($this->_info['value'])) {
					$checked = in_array($node_id, $this->_info['value'])?'checked="checked"':'';
				} else {
					$checked = '';
				}

				$output .= '<li>';
					$output .= '<input '.$checked.' value="'.$node_id.'" id="'.$node_control_id.'" name="'.$node_control_name.'" type="checkbox" />';
					$output .= '<label for="'.$node_control_id.'">'.$node['title'].'</label>';
					if (!empty($node['children'])) {
						$output	.= $this->_renderBranch($node['children']);
					}
				$output .= '</li>';
			}
			$output .= '</ul>';
		} else {
			$output = '<div class="alert alert-warning">
			You do\'nt have any category. Please click the link below to add a category
			</div>';
		}		 
		
		
		//$this->_addJavascript( $id );

		return $output;
	}

	public function formTreeView ($name, $value = null, $attribs = null, $options = null)
	{
		$this->_info = $this->_getInfo($name, $value, $attribs, $options);
		return $this->_renderBranch($this->_info['options']);
	}
}
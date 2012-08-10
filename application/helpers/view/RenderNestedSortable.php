<?php
class Zend_View_Helper_RenderNestedSortable extends Zend_View_Helper_Abstract
{
	public function renderNestedSortable( $menus, $pages, $isParent = true )
	{
		foreach( $menus as $id => $menu )
		{
			$render[] = '<li id="page_'.$id.'">';
			$render[] = $this->view->partial('partials/loop-menu.phtml', array(
				'id' 		=> $id,
				'parent' 	=> $isParent, 
				'page' 		=> $pages[$id],
				'menu' 		=> $menu
			));
			
			if( isset($menu['pages']) ){
				$render[] = '<ol>';
				$render[] = $this->renderNestedSortable( $menu['pages'], $pages, false );
				$render[] = '</ol>';
			}
				
			$render[] = '</li>';
		}
			
		return join('', $render);
	}
}

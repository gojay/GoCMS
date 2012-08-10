<?php
class My_Layout_Plugin_Module extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getModuleName();
		$layout = 'layout';
		
		$layoutsDir = APPLICATION_PATH . "/layouts/scripts/" . $module;
		if(!is_dir($layoutsDir) || !file_exists($layoutsDir. DIRECTORY_SEPARATOR . $layout . '.phtml')) 
		{
			if(APPLICATION_ENV == 'development')
				print sprintf('layout %s not found. please make first the layout in %s/%s.phtml', $module, $layoutsDir, $layout);
            
            $layoutsDir = APPLICATION_PATH . "/layouts/scripts/default";
        } 
		// set up layout modules
        $options = array(
             'layout'     => $layout,
             'layoutPath' => $layoutsDir,
		);
		Zend_Layout::startMvc($options);
		
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        // set up variables that the view may want to know
		$viewRenderer->view->module = $request->getModuleName();
		$viewRenderer->view->controller = $request->getControllerName();
		$viewRenderer->view->action = $request->getActionName();
		$viewRenderer->view->params = $request->getParams();
		
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
			$viewRenderer->view->identity = $auth->getIdentity();
	}

}
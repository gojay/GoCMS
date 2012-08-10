<?php
class My_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
	public $acl;
	
	public function __construct()
	{
		// set up Acl
		$this->acl = new Zend_Acl();
		// add the roles
		$this->acl->addRole(new Zend_Acl_Role('guest'));
		$this->acl->addRole(new Zend_Acl_Role('administrator'));
		
		// add the resources
		$this->acl->add(new Zend_Acl_Resource('default'));
        $this->acl->add(new Zend_Acl_Resource('default:contact'));
        $this->acl->add(new Zend_Acl_Resource('default:error'));
		$this->acl->add(new Zend_Acl_Resource('default:index'));
        $this->acl->add(new Zend_Acl_Resource('default:rss'));
        $this->acl->add(new Zend_Acl_Resource('default:sitemap'));
        $this->acl->add(new Zend_Acl_Resource('default:widget'));

        $this->acl->add(new Zend_Acl_Resource('admin'));
        $this->acl->add(new Zend_Acl_Resource('admin:ajax'));
        $this->acl->add(new Zend_Acl_Resource('admin:auth'));
        $this->acl->add(new Zend_Acl_Resource('admin:blueimp'));
        $this->acl->add(new Zend_Acl_Resource('admin:dashboard'));
        $this->acl->add(new Zend_Acl_Resource('admin:index'));
        $this->acl->add(new Zend_Acl_Resource('admin:news'));
        $this->acl->add(new Zend_Acl_Resource('admin:pages'));
        $this->acl->add(new Zend_Acl_Resource('admin:people'));
        $this->acl->add(new Zend_Acl_Resource('admin:portfolio'));
        $this->acl->add(new Zend_Acl_Resource('admin:setting'));
        $this->acl->add(new Zend_Acl_Resource('admin:slide'));
		
		// set up the access rules
		$this->acl->allow(null, 'default:error');
		
		// guest access rule
		$this->acl->allow('guest', 'default:index');
		$this->acl->allow('guest', 'default:contact');
		$this->acl->allow('guest', 'default:widget');
		$this->acl->allow('guest', 'default:rss');
		$this->acl->allow('guest', 'default:sitemap');
		
		// admin access rule
        $this->acl->allow('administrator', null);
	}
	
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{		
		// fetch the current user
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$identity = $auth->getIdentity();
			$role = strtolower($identity->role);
		} else {
			$role = 'guest';
		}
		Zend_Registry::set('role', $role);
		
		// if not allowed
		$module = $request->module;
		$resource = $request->controller;
		$action = $request->action;
		
		if(!$this->acl->isAllowed($role, $module.':'.$resource, $action))
		{
			$request->setModuleName('admin');
			$request->setControllerName('auth');
			$request->setActionName('login');
		}
	}
}
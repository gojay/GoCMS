<?php
class Admin_Form_Tree extends Zend_Form
{
	public function init()
	{
		$tree = new My_Form_Element_TreeView('content_categories');
		$tree->setLabel('Category');
		
		$db = Zend_Registry::get('db');
		$cats = Model_Term::GetCategoriesForPopulate( $db );
		$tree->setOptions(array(
			'multioptions' => $cats
		));
		
		$this->addElement($tree);
		/*
		$this->setOptions(array(
			'elements' => array(
				'tree' => array(					
					'type' => 'treeView',
					'options' => array(
						'label' => 'Tree:',
						'multioptions' => array(
							1 => array('title'=>'Node 1', 'children'=>array()),
							2 => array('title'=>'Node 2', 'children'=>array(
								21 => array('title'=>'Node 2.1', 'children'=>array()),
								22 => array('title'=>'Node 2.2', 'children'=>array(
									221 => array('title'=>'Node 2.2.1', 'children'=>array()),
									222 => array('title'=>'Node 2.2.2', 'children'=>array()),
									223 => array('title'=>'Node 2.2.3', 'children'=>array()),
								)),
							)),
							3 => array('title'=>'Node 3', 'children'=>array()),
							4 => array('title'=>'Node 4', 'children'=>array(
								41 => array('title'=>'Node 4.1', 'children'=>array()),
								42 => array('title'=>'Node 4.2', 'children'=>array(
									421 => array('title'=>'Node 4.2.1', 'children'=>array()),
									422 => array('title'=>'Node 4.2.2', 'children'=>array()),
								)),
							)),
							5 => array('title'=>'Node 5', 'children'=>array()),
						)
					)
				),
				'submit' => array(
					'type' => 'submit',
					'options' => array(
						'ignore' => true,
						'label' => 'Submit',
						'class' => 'submit',
					)
				),
			),
		));*/
	}
}

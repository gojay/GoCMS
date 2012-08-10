<?php
class My_View_Helper_NavigationBootstrap extends Zend_View_Helper_Navigation_HelperAbstract
{
    /**
     * class applied to LI of the active page
     * @var string
     */
    public $activeClass = 'active';

    /**
     * class applied to the top-level LI of the active branch
     * @var string
     */
    public $topActiveClass = 'topActiveBranch';

    /**
     * class applied to LI above the LI of the active page
     * @var string
     */
    public $parentActiveClass = 'activeParent';

    /**
     * if a non-empty string S exists for key I, a sublist at depth I will be
     * wrapped with a DIV with classname S.
     * @var array
     */
    public $listWrappersByDepth = array();

    /**
     * classnames for sublist elements at various depths.
     * @var array
     */
    public $listClassesByDepth = 'sub-menu';

    /**
     * attributes for the root-level UL
     * @var array
     */
    public $rootListAttrs = array('class' => 'nav');
	
	public $rootId;

    /**
     * deepest active page found, or null
     * @var Zend_Navigation_Page
     */
    protected $_foundPage;

    /**
     * depth of deepest active page found, or null
     * @var int
     */
    protected $_foundDepth;


    /**
     * View helper entry point:
     * Retrieves helper and optionally sets container to operate on
     *
     * @param  Zend_Navigation_Container $container  [optional] container to
     *                                               operate on
     * @return Zend_View_Helper_Navigation_Menu      fluent interface,
     *                                               returns self
     */
    public function navigationBootstrap(Zend_Navigation_Container $container = null)
    {
        if (null !== $container) {
            $this->setContainer($container);
        }
        return $this;
    }

    /**
     * Returns an HTML string containing an 'a' element for the given page if
     * the page's href is not empty, and a 'span' element if it is empty
     *
     * Overrides {@link Zend_View_Helper_Navigation_Abstract::htmlify()}.
     *
     * @param  Zend_Navigation_Page $page  page to generate HTML for
     * @return string                      HTML string for the given page
     */
    public function htmlify(Zend_Navigation_Page $page)
    {
        return $page->isActive()
           ? $this->_renderActivePage($page)
           : $this->_renderInactivePage($page);
    }
	
	/** 
	 * set Ul ID
	 * @param string id
	 * @return $this
	 */
	public function setUlId($id)
	{
		$this->rootId = $id;
		return $this;
	}
	
	/** 
	 * set Ul class
	 * @param string class
	 * @return $this
	 */
	public function setUlClass($class)
	{
		$this->rootListAttrs['class'] = $class;
		return $this;
	}


    /**
     * Renders menu
     *
     * Implements {@link Zend_View_Helper_Navigation_Helper::render()}.
     *
     * @param  Zend_Navigation_Container $container  [optional] container to
     *                                               render. Default is to
     *                                               render the container
     *                                               registered in the helper.
     * @return string                                helper output
     */
    public function render(Zend_Navigation_Container $container = null)
    {
        if (null === $container) {
            $container = $this->getContainer();
        }
        if (null === $this->view) {
            $this->setView(new Zend_View());
        }
        // find deepest active
        if ($found = $this->findActive($container)) {
            $this->_foundPage = $found['page'];
            $this->_foundDepth = $found['depth'];
        } else {
            $this->_foundPage = null;
            $this->_foundDepth = null;
        }

        $listElement = 'ul';
		$listId = ($this->rootId) ? ' id='.$this->rootId : '';
        $listAttrs = $this->_htmlAttribs($this->rootListAttrs);

        $html = "<$listElement$listId$listAttrs>";

        unset($listAttrs, $found); // save a little mem while recursing

        foreach ($container->getPages() as $page) {
            $isActiveBranch = $page->isActive(true);
            $html .= $this->_renderPageAndSubmenu($page, $isActiveBranch, 0);
        }

        return "$html</$listElement>";
    }

    /**
     * render HTML for an LI and possibly a nested submenu
     * @param Zend_Navigation_Page $page
     * @param bool $isActiveBranch
     * @param int $depth
     * @return string
     */
    protected function _renderPageAndSubmenu(Zend_Navigation_Page $page, $isActiveBranch, $depth)
    {
        // render li and page
        if (! is_array($liAttrbs = $page->get('liAttrbs'))) {
            $liAttrbs = array();
        }
        if ($isActiveBranch) {
            if ($page === $this->_foundPage) {
                $this->_appendClass($liAttrbs, $this->activeClass);
            }
            /*if ($depth === 0) {
                $this->_appendClass($liAttrbs, $this->topActiveClass);
            }*/
            if ($page->hasPage($this->_foundPage)) {
                $this->_appendClass($liAttrbs, $this->activeClass);
            }
        }
		
		$id = '';
		if( $liAttrbs['id'] ){
			$id = ' id = ' . $liAttrbs['id'] . ' ';
			unset($liAttrbs['id']);
		}
        $html = '<li' . $id . $this->_htmlAttribs($liAttrbs) . '>' . $this->htmlify($page);

        $beforeUl = $page->get('beforeUl');
        $afterUl = $page->get('afterUl');
        if (! empty($this->listWrappersByDepth[$depth + 1])) {
            $beforeUl = "<div class=\"{$this->listWrappersByDepth[$depth + 1]}\">" . $beforeUl;
            $afterUl .= '</div>';
        }
        $html .= $beforeUl;

        // render sublist
        unset($liAttrbs, $beforeUl); // save a little mem while recursing

        if ($subpages = $page->getPages()) {
            $html .= $this->_renderSubmenu($page, $subpages, $isActiveBranch, $depth);
        }

        return $html . $afterUl . '</li>';
    }

    /**
     * render HTML for a nested submenu
     * @param Zend_Navigation_Page $page
     * @param bool $isActiveBranch
     * @param int $depth
     * @return string
     */
    protected function _renderSubmenu(Zend_Navigation_Page $page, $subpages, $isActiveBranch, $depth)
    {
        $html = '';

        // allow page to set list attributes
        if (! is_array($ulAttrbs = $page->get('ulAttrbs'))) {
            $ulAttrbs = array();
        }
       	$this->_appendClass($ulAttrbs, $this->listClassesByDepth);
		
		
		$id = '';
		if( $ulAttrbs['id'] ){
			$id = ' id = ' . $ulAttrbs['id'] . ' ';
			unset($ulAttrbs['id']);
		}
        $html = '<ul' . $id . $this->_htmlAttribs($ulAttrbs) . '>';

        unset($ulAttrbs, $beforeUl); // save a little mem while recursing

        foreach ($subpages as $subpage) {
            $html .= $this->_renderPageAndSubmenu($subpage, $isActiveBranch, $depth + 1);
        }

        return $html . "</ul>";
    }

    protected function _renderActivePage(Zend_Navigation_Page $page)
    {
        // same for now, but we could render active pages as STRONG elements, etc
        return $this->_renderInactivePage($page);
    }

    protected function _renderInactivePage(Zend_Navigation_Page $page)
    {
        // get label and title
        $label = $page->getLabel();
        $title = $page->getTitle();
		
        // get attribs for element
        $attribs = array(
            'id'     => $page->getId(),
            'title'  => $title,
            'class'  => $page->getClass(),
            'style'  => $page->get('style')
        );

        // does page have a href?
        if ($href = $page->getHref()) {
            $element = 'a';
            $attribs['href'] = $href;
            $attribs['target'] = $page->getTarget(); 
			if( $data = $page->get('data') ){
				foreach ($data as $key => $value) {
					$attribs[$key] = $value;
				}
			}          
        } else {
            $element = 'span';
        }

        if (! $page->get('labelIsHtml')) {
            $label = $this->view->escape($label);
        }
		
		// before & after text
		$beforeText = ( $page->get('beforeText') ) ? $page->get('beforeText') : '';
		$afterText = ( $page->get('afterText') ) ? $page->get('afterText') : '';

        return '<' . $element . $this->_htmlAttribs($attribs) . '>'
             . $beforeText . $label . $afterText
             . '</' . $element . '>';
    }

    /**
     * Append to the class attribute of a set of attributes
     * @param array $attrs
     * @param string $class
     */
    protected function _appendClass(& $attrs, $class)
    {
        $attrs['class'] = empty($attrs['class'])
            ? $class
            : "{$attrs['class']} $class";
    }
}

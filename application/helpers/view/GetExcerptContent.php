<?php
class Zend_View_Helper_GetExcerptContent extends Zend_VIew_Helper_Abstract
{
	public function getExcerptContent($content, $more_link_text = null, $noImg = true)
    {
    	if ( null === $more_link_text )
			$more_link_text = '...read more';
		
		$hasTeaser = false;
		
    	$text = '';
		if ( preg_match('/<!--more(.*?)?-->/', $content, $matches) ) {
			$content = explode($matches[0], $content, 2);
			$hasTeaser = true;
		} else {
			$content = array($content);
		}
		
		$text = $content[0];
		if( $noImg )
			$text = preg_replace("/<img[^>]+\>/i", "", $text);
		if($hasTeaser)
			$text .= $more_link_text;
		
		return $text;
	}
}

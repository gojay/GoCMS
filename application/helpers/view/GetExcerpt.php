<?php
class Zend_View_Helper_GetExcerpt extends Zend_VIew_Helper_Abstract
{
	public function getExcerpt($text, $length = 15, $html = null, $noImg = true )
    {
		if ($noImg)
			$text = preg_replace("/<img[^>]+\>/i", "", $text);
		
		$words = explode(' ', $text, $length + 1);
		if (count($words) > $length) {
			array_pop($words);
			array_push($words, (!is_null($html)) ? $html : '');
			$text = implode(' ', $words);
		}
		
		return $text;
	}
}

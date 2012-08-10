<?php
class Zend_View_Helper_GetDate extends Zend_View_Helper_Abstract
{
	public function getDate( $date, $part = Zend_Date::DATE_FULL, $locale = 'id' )
	{
		$dateObj = new Zend_Date( $date, $locale );
		return $dateObj->get( $part );
	}
}

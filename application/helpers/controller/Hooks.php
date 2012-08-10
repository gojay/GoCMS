<?php
class Zend_Controller_Action_Helper_Hooks extends Zend_Controller_Action_Helper_Abstract {
	/**
	 * getDB
	 * ambil database
	 *
	 * @return db
	 */
	public function getDb() {
		return Zend_Registry::get('db');
	}

	/**
	 * getExcerpt
	 * untuk buat deskripsi/memotong kata berdasarkan spasi
	 *
	 * @param string
	 * @param int, jumlah spasi yang dipotong, default 15
	 * @param string, kata pengganti potongan, default ''
	 * @return string
	 */
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
	

	/**
	 * getExcerptContent
	 * untuk buat deskripsi/memotong kata berdasarkan pagebreak
	 *
	 * @param string
	 * @param string, kata pengganti potongan, default ''
	 * @param boolean
	 * @return string
	 */
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
			$excerpt = $this->getExcerpt($content, 100, $more_link_text, $noImg );
			$content = array($excerpt);
		}
		
		$text = $content[0];
		if( $noImg )
			$text = preg_replace("/<img[^>]+\>/i", "", $text);
		if($hasTeaser)
			$text .= $more_link_text;
		
		return $text;
	}

	/**
	 * getSeparatorToDash
	 * membuat slug atau mereplace spasi dengan separator -
	 *
	 * @param string
	 * @return string
	 */
	public function getSeparatorToDash($title) {
		$title = strtolower($title);
		$filter = new Zend_Filter_Word_SeparatorToDash();
		return $filter->filter($title);
	}

	/**
	 * getOption
	 * ambil setting option
	 *
	 * @param $int
	 * @return string
	 */
	public function getOption($name, $key = 'value', $option_key = 'option') {
		$db = Zend_Registry::get('db');
		$option = new Model_Option($db);
		$data = $option -> getOption($option_key, $name);
		return $data[$key];
	}

	/**
	 * geo
	 * ambil geocoding latitude dan longtitude dari alamat
	 *
	 * @param string address
	 * @return array
	 */
	public function doGeocoder($address) 
	{
		$google = Zend_Registry::get('google');
		// model geocoder
		$geocoder = new Model_Geocoder($google['map_api_key']);
		// proses geocoding
		$latlon = $geocoder -> getCoordinates($address);
		$coordinates = array();
		if ($latlon) {
			$coordinates = array('lat' => $latlon['lat'], 'lon' => $latlon['lon']);
		}

		return $coordinates;
	}
	
	/**
	 * geTimeSince
	 * ambil perbedaan waktu
	 *
	 * @param string date
	 * @return date
	 */
	public function geTimeSince( $date ) 
	{
		$timeNow = new Zend_Date();
    	$timeThen = new Zend_Date( $date );
    	$difference = $timeNow->sub( $timeThen );
    	$since = $difference->toValue();

		$chunks = array(
			'year' => 60 * 60 * 24 * 365, // 31,536,000 seconds
			'month' => 60 * 60 * 24 * 30, // 2,592,000 seconds
			'week' => 60 * 60 * 24 * 7, // 604,800 seconds
			'day' => 60 * 60 * 24, // 86,400 seconds
			'hour' => 60 * 60, // 3600 seconds
			'minute' => 60, // 60 seconds
			'second' => 1 // 1 second
		);

		foreach ($chunks as $key => $seconds)
			if (($count = floor($since / $seconds)) != 0)
				break;

		$messages = array(
			'year' => sprintf('about %s years ago', $count), 
			'month' => sprintf('about %s months ago', $count), 
			'week' => sprintf('about %s weeks ago', $count), 
			'day' => sprintf('about %s days ago', $count), 
			'hour' => sprintf('about %s hours ago', $count), 
			'minute' => sprintf('about %s minutes ago', $count), 
			'second' => sprintf('about %s seconds ago', $count), 
		);
		return sprintf($messages[$key], $count);
	}

	/**
	 * 
	 */
	public function array_search($array, $key, $value)
	{
	    $results = array();
	
	    if (is_array($array))
	    {
	        if (isset($array[$key]) && $array[$key] == $value)
	            $results[] = $array;
	
	        foreach ($array as $subarray)
	            $results = array_merge($results, $this->array_search($subarray, $key, $value));
	    }
	
	    return $results;
	}

}

<?php
/**
 * @package     MapsByJoaktree
 * @subpackage  Service
 *
 * @copyright   Joaktree.com
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

JLoader::register('MBJServiceGeocode', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'services'.DS.'geocode.php');

/**
 * Service connector class.
 *
 * @package     MapsByJoaktree
 * @subpackage  Service
 * @since       1.0
 */
class MBJServiceGeocodeYahoo extends MBJServiceGeocode {

	/**
	 * Test to see if service for this provider is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   1.0
	 */
	public static function test()
	{
		return false;
	}
	
	public function parameters() {
		static $params;
		
		if (!isset($params)) {
			$params = array();
			
			$params['indHttps']['type']  = 'boolean';
			$params['indHttps']['value'] = 'false';
			$params['format']['type']    = 'string';
			$params['format']['value']   = 'xml';
			$params['country']['type']   = 'string';
			$params['country']['value']  = null;
			$params['language']['type']  = 'string';
			$params['language']['value'] = null;
			
		}
		
		return $params;
	}
	
	protected function getBaseUrl() {
		static $baseUrl;
		
		if (!isset($baseUrl)) {
			$keys   = self::getKeys();
			$params = self::parameters(); 	
			$base_url = '';
			
			$base_url .= 'http://';
			$base_url .= 'where.yahooapis.com/geocode';
			$base_url .= '?appid='.$this->provider->getAPIkey();
			
			$country   = (isset($keys->country)) ? $keys->country : $params['country']['value'];
			$language  = (isset($keys->language)) ? $keys->language : $params['language']['value'];
			$base_url .= (($country) && ($language)) ? '&locale='.$language.'_'.$country : '';
		}
		return $base_url;
		
	}
	
	protected function getUrl(&$data) {
		$url  = $this->getBaseUrl();
		$url .= '&q='.urlencode($data->value);
		
		return $url;		
	}
	
	protected function getStatus(&$xml) {		
		$status = $xml->Error;											
			switch ($status) {
				case "0"   : 	$return = 'found';
								break;
				default    : 	$return = $status.': '.$xml->ErrorMessage;
								break;
			}		

		return $return;
	}

	protected function getCoordinates(&$xml) {		
		// Format coordinatest: Longitude, Latitude, Altitude
		$coordinates = array();
		$coordinates['lon'] = $xml->Result->longitude;
		$coordinates['lat'] = $xml->Result->longitude;
		return $coordinates;
	}
	
	protected function getNumberOfHits(&$xml) {		
		return 1;
	}
	
	protected function getResultAddress(&$xml) {	
		return '';
	}
	public function setResultSet(&$xml){
		$resultSet = array();
		
		foreach ($xml as $result) {
			if ($result->longitude) {
				$resultSet[]['lon'] = $result->longitude;
				$resultSet[]['lat'] = $result->longitude;
				$resultSet[]['adr'] = $result->longitude;		
			}
		}
		
		return $resultSet;
	}
	
	
}

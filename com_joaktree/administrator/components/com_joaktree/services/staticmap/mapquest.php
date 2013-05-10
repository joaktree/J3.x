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

JLoader::register('MBJServiceStaticmap', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'services'.DS.'staticmap.php');

/**
 * Service connector class.
 *
 * @package     MapsByJoaktree
 * @subpackage  Service
 * @since       1.0
 */
class MBJServiceStaticmapMapquest extends MBJServiceStaticmap {
	
	protected static $version = 'v4';

	/**
	 * Test to see if service for this provider is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   1.0
	 */
	public static function test()
	{
		return true;
	}
	
	public function parameters() {
		static $params;
		
		if (!isset($params)) {
			$params = array();
						
			$params['indHttps']['type']  = 'boolean';
			$params['indHttps']['value'] = 'false';
			$params['imagetype']['type']  = 'string';
			$params['imagetype']['value'] = 'png';
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
			$base_url .= 'open.mapquestapi.com/staticmap/'.self::$version.'/getmap';
			
			$base_url .= '?imagetype='.$params['imagetype']['value'];
			
		}
		return $base_url;
	}
	
	public function fetch(&$data, $options = array()) {
		$indCenter = false;
		$indZoom   = false;
		$mapview  = $this->getBaseUrl();
		
		// size
		$width  = ((isset($options['width']))  && (!empty($options['width']))  ) ? $options['width']  : '650';
		$height = ((isset($options['height'])) && (!empty($options['height'])) ) ? $options['height'] : '450';
		$mapview .= '&size='.$width.','.$height;
		
		if (  (isset($options['longitude'])) && (!empty($options['longitude'])) 
		   && (isset($options['latitude']))  && (!empty($options['latitude']))
		   ) {
			$mapview .= '&center='.$options['latitude'].','.$options['longitude'];
			$indCenter = true;
		} else if ((isset($options['center'])) && (!empty($options['center']))) {
			$centerdata = new stdClass;
			$centerdata->value = $options['center'];

			JLoader::register('MBJServiceGeocode',    JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'services'.DS.'geocode.php');
			$geocode = MBJServiceGeocode::getInstance();
			$geocode->_('findLocation', $centerdata);
			
			$mapview .= '&center='.$centerdata->latitude.','.$centerdata->longitude;
			$indCenter = true;
		} 
		
		if ((isset($options['zoomlevel'])) && (!empty($options['zoomlevel']))) {
			$mapview .= '&zoom='.$options['zoomlevel'];
			$indZoom = true;
		}
		
		// maptype
		if ((isset($options['maptype'])) && (!empty($options['maptype']))) {
			switch ($options['maptype']) {
				case "sat"	: 	$maptype = 'sat';
								break;
				case "hyb"	: 	$maptype = 'hyb';
								break;
				case "ter"	: 	//continue
				case "road"	:	//continue									
				default 	: 	$maptype = 'map';
				          		break;
			}	
		} else {
			$maptype = 'map';
		}
		$mapview .= '&type='.$maptype;
		
		// markers
		if (count($data)) {
			$indContinue = true;
			$tmpdata = $data;					
			$item = array_shift($tmpdata);
			$mapview .= '&pois=';
			$color = (isset($options['color'])) && !empty($options['color']) ? $options['color'] : 'orange';		
			
			while ($indContinue) {
				if (is_object($item)) {
					// save the current string in case we go over the max length
					$tmpUrl = $mapview;
					
					// color + label
					$mapview .= $color.((is_numeric($item->label)) ? '-'.(int)$item->label : '');
					
					$mapview .= ','.$item->latitude.','.$item->longitude;
					$mapview .= (count($tmpdata)) ? '|' : '';
					$indCenter = true;
					$indZoom = true;
														
					$item = array_shift($tmpdata);
					if (!$item) {
						// we are done
						$indContinue = false;
					}
					if (strlen($mapview) > 1024) {
						// we reached max url length
						$indContinue = false;
						$mapview = $tmpUrl;
					}
					
				} else {
					$indContinue = false;
				}		
			}			
		}
		
		if ((!$indCenter) || (!$indZoom)) {
			$this->log[] = JText::_('MBJ_MAP_ERROR_NO_CENTERZOOM');
			return false;
		}
		
		return $mapview;
	}
	
}

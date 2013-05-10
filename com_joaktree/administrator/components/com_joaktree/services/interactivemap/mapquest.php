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

JLoader::register('MBJServiceInteractivemap', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'services'.DS.'interactivemap.php');

/**
 * Service connector class.
 *
 * @package     MapsByJoaktree
 * @subpackage  Service
 * @since       1.0
 */
class MBJServiceInteractivemapMapquest extends MBJServiceInteractivemap {

	protected static $version = 'v7.0.s';
	
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
		}
		
		return $params;
	}
	
	public function fetch(&$data, $options = array()) {
		$script = array();
		$indCenter = false;
		
		// center
		if (  (isset($options['longitude'])) && (!empty($options['longitude'])) 
		   && (isset($options['latitude']))  && (!empty($options['latitude']))
		   ) {		   
			$center =  'latLng:{lat:'.$options['latitude'].', lng:'.$options['longitude'].'} '; 
			$indCenter = true;
		} else if ((isset($options['center'])) && (!empty($options['center']))) {
			$centerdata = new stdClass;
			$centerdata->value = $options['center'];

			JLoader::register('MBJServiceGeocode',    JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'services'.DS.'geocode.php');
			$geocode = MBJServiceGeocode::getInstance();
			$geocode->_('findLocation', $centerdata);
			
			$center =  'latLng:{lat:'.$centerdata->latitude.', lng:'.$centerdata->longitude.'} '; 
			$indCenter = true;
		} else if (count($data)) {
			// take the first marker as center
			$center =  'latLng:{lat:'.$data[0]->latitude.', lng:'.$data[0]->longitude.'} '; 		
			$indCenter = true;
		}
		
		if (!$indCenter) {
			$this->log[] = JText::_('MBJ_MAP_ERROR_NO_CENTERZOOM');
			return false;
		}
		
		// zoom
		if (  (isset($options['zoomlevel'])) && (!empty($options['zoomlevel'])) ) {
			$zoom = 'zoom: '.$options['zoomlevel'];
		} else {
			$zoom = 'zoom: 9';
		}		
		
		// maptype //map, sat, hyb, osm
		if ((isset($options['maptype'])) && (!empty($options['maptype']))) {
			switch ($options['maptype']) {
				case "sat": 	$maptype = 'mtype:\'sat\'';
								break;
				case "hyb"	: 	$maptype = 'mtype:\'hyb\'';
								break;
				case "ter"	: 	// continue
				case "road"	:	// continue									
				default 	: 	// maptype is default roadmap
								$maptype = 'mtype:\'map\'';
				          		break;
			}	
		} else {
			$maptype = 'mtype:\'map\'';
		}
				
		// generate script
		$script[] = 'function init_dyn_map() { ';
		
		$script[] = '   var Elb = document.getElementById("map_canvas").parentNode; ';
		$script[] = '   Elb.style.margin = "0"; ';
		$script[] = '   Elb.style.height = "100%"; ';
		
		$script[] = '   var myOptions = { ';
		$script[] = '      elt:document.getElementById("map_canvas"), ';  /*ID of element on the page where you want the map added*/ 
		$script[] = '      '.$zoom.', ';								  /*initial zoom level of the map*/ 
		$script[] = '      '.$center.', ';								  /*center of map in latitude/longitude */ 
		$script[] = '      '.$maptype.' ';								  /*map type (osm)*/ 	
		$script[] = '   } ';
		$script[] = '   var myMap = new MQA.TileMap(myOptions); ';		
		$script[] = '   myMap.setSize (); '; 
		
		// add controls -- 
		$script[] = '   MQA.withModule("smallzoom","viewoptions","mousewheel", function() { '; 
		$script[] = '     myMap.addControl( ';
		$script[] = '       new MQA.SmallZoom(), ';
		$script[] = '       new MQA.MapCornerPlacement(MQA.MapCorner.TOP_LEFT, new MQA.Size(5,5)) ';
		$script[] = '     ); ';
		$script[] = '     myMap.addControl( ';
		$script[] = '       new MQA.ViewOptions(), ';		
		$script[] = '       new MQA.MapCornerPlacement(MQA.MapCorner.TOP_RIGHT, new MQA.Size(5,5)) ';
		$script[] = '     ); ';
		$script[] = '     myMap.enableMouseWheelZoom(); ';
		$script[] = ' }); ';
	
		if (  ($options['selection'] == 'location')
		   && isset($options['distance']) 
		   && !empty($options['distance'])
		   && ($options['distance'] > 0)
		   ) {
		   	// we are adding a circle
			$script[] = '   MQA.withModule("shapes", function() { ';
			$script[] = '     var myCircle = new MQA.CircleOverlay(); ';
			$script[] = '     myCircle.radius="'.($options['distance']/1.6).'"; ';
			$script[] = '     myCircle.shapePoints=['.$options['latitude'].', '.$options['longitude'].']; ';
			$script[] = '     myCircle.color="#2D36AD"; ';
			$script[] = '     myCircle.colorAlpha=0.7; ';
			$script[] = '     myCircle.borderWidth=2; ';
			$script[] = '     myCircle.fillColor="#2D36AD"; ';
			$script[] = '     myCircle.fillColorAlpha=.2; ';
			
			$script[] = '     myMap.addShape(myCircle); ';
			$script[] = '   }); ';
		   }
		
		// setup possible images
		$iconset = (isset($options['color']) && !empty($options['color'])) ? (int) $options['color'] : 0 ; 
		if ((isset($options['icon_file'])) && (!empty($options['icon_file'])) ) {
			$iconDir = JURI::base().$options['icon_file'].'/'.$iconset.'/';
		} else {
			$iconDir = JURI::base().'administrator/components/com_joaktree/services/images/'.$iconset.'/';
		}

		for ($i=1; $i<22; $i++) {			
			$script[] = '   var img'.($i).' = new MQA.Icon("'.$iconDir.'jt-icon-'.$i.'.png",24,32); '; 
		}
				   		
		if (count($data)) {
			foreach ($data as $item) {
				$script[] = '   var cnt'.$item->id.' = \'<div id="cnt'.$item->id.'">'.$item->information.'</div>\'; ';
				$script[] = '   var mrk'.$item->id.' = new MQA.Poi( {lat:'.$item->latitude.', lng:'.$item->longitude.'} ); ';
				$script[] = '   mrk'.$item->id.'.setIcon(img'.(((int)$item->label < 21) ? (int)$item->label : 21).'); ';
				$script[] = '   mrk'.$item->id.'.setRolloverContent(\''.$item->value.'\'); ';
				$script[] = '   mrk'.$item->id.'.setInfoTitleHTML(\''.$item->value.'\'); '; 
				$script[] = '   mrk'.$item->id.'.setInfoContentHTML(cnt'.$item->id.'); '; 
				$script[] = '   myMap.addShape(mrk'.$item->id.'); ';
			} 		
		}
				
		$script[] = '} ';
		$script[] = '';
				
		$script[] = 'window.onload = init_dyn_map; ';
		
		return implode("\n", $script);
	}
	
	public function getToolkit() {
		return $this->getScriptUrl();
	}
	
	private function getScriptUrl() {
		static $baseUrl;
		
		if (!isset($baseUrl)) {
			//$keys   = self::getKeys();
			//$params = self::parameters(); 	

			/*load the SDK*/ 
			$base_url  = 'http://open.mapquestapi.com/sdk/js/'.self::$version.'/mqa.toolkit.js';
			
		}
		return $base_url;
	}
	
}

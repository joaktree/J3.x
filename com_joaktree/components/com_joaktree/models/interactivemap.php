<?php
/**
 * Joomla! component Joaktree
 * file		front end map model - map.php
 *
 * @version	1.5.0
 * @author	Niels van Dantzig
 * @package	Joomla
 * @subpackage	Joaktree
 * @license	GNU/GPL
 *
 * Component for genealogy in Joomla!
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.modellist');
require_once(JPATH_COMPONENT.DS.'helper'.DS.'map.php');

class JoaktreeModelInteractivemap extends JModelLegacy {
	
	protected static $map;
	
	function __construct() {
		$id = array();
		$id['map']		= Map::getMapId(true);
		$id['location']	= Map::getLocationId(true);
		$id['distance']	= Map::getDistance(true);
		$id['person']	= JoaktreeHelper::getPersonId(false, true);
		$id['tree']		= JoaktreeHelper::getTreeId(false, true);
		$id['app']		= JoaktreeHelper::getApplicationId(false, true);

		$this->map 	= new Map($id);
		parent::__construct();            		
	} 
	
	private function getMapId() {
		return Map::getMapId();
	}
					
	public function getMap() {
		return $this->map;
	}
	
	public function getMapView() {
		return $this->map->getMapView();
	}

}
?>
<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_locations.php
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

defined('_JEXEC') or die('Restricted access');
jimport('joomla.filter.input');
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'services'.DS.'geocode.php';

class TableJoaktree_locations extends JTable
{
	var $id 				= null;
	var $indexLoc           = null;
	var $value				= null;
	var $latitude			= null;
	var $longitude			= null;
	var $indServerProcessed = null;
	var $indDeleted			= null;
	var $results			= null;	
	var $resultValue		= null;
	
	function __construct( &$db) {		
		parent::__construct('#__joaktree_locations', 'id', $db);
	}
	
	protected function getSettings() {
		static $settings;

		if (!isset($settings) ) {
			$settings =  MBJServiceGeocode::getKeys();
			
			if (isset($settings->geocode)) { 
				$geocodeAPIkey	= $settings->geocode.'APIkey';
				if (  (empty($settings->geocode)) 
				   || (  !empty($settings->geocode) 
				      && isset($settings->$geocodeAPIkey) 
				      && empty($settings->$geocodeAPIkey)
				      )		 
				   ) {
				   	$settings->indGeocode = false;
				} else {
					$settings->indGeocode = true;
				}
			} else {
				$settings->indGeocode = false;
			}
		}
		
		return $settings;
	}
	
	public function checkLocation($value) {
		if (!isset($value) || empty($value)) {
			// no location -> no location id
			return '';
		}
		
		// check for locations
		$query = $this->_db->getQuery(true);
		$query->select(' jln.id ');
		$query->from(  ' #__joaktree_locations jln ');
		$query->where( ' jln.value       = '.$this->_db->quote($value).' ');

		$this->_db->setQuery( $query );
		$result = $this->_db->loadResult();

		if (!$result) {
			// we are inserting the location			
			$query->clear();
			$query->insert(' #__joaktree_locations ');
			$query->set(   ' value       = '.$this->_db->quote($value).' ');
			$query->set(   ' indexLoc    = '.$this->_db->quote(mb_strtoupper(mb_substr($value, 0, 1 ))).' ');
			
			// check for coordinates
			$settings = self::getSettings();
			if ($settings->indGeocode) {
				$data		 = new StdClass;
				$data->value = $value; 
				$service 	 = MBJServiceGeocode::getInstance();	
				$status 	 = $service->_('findLocation', $data);
				
				if ($status == 'found') {
					$query->set(   ' latitude       	= '.$data->latitude.' ');
					$query->set(   ' longitude       	= '.$data->longitude.' ');
					$query->set(   ' indServerProcessed	= '.$data->indServerProcessed.' ');
					$query->set(   ' results      		= '.$data->results.' ');
					$query->set(   ' resultValue      	= '.$this->_db->quote($data->result_address).' ');
				} else {
					$query->set(   ' indServerProcessed	= '.$data->indServerProcessed.' ');
					$query->set(   ' results      		= '.$data->results.' ');
				}
			}
						
			$this->_db->setQuery( $query );
			$this->_db->query();
			
			// ... and retrieving the new id
			$query->clear();
			$query->select(' jln.id ');
			$query->from(  ' #__joaktree_locations jln ');
			$query->where( ' jln.value       = '.$this->_db->quote($value).' ');
	
			$this->_db->setQuery( $query );
			$result = $this->_db->loadResult();
		}

		return $result;
	}
	
	public function bind($src, $ignore = array()) {
		$src['latitude'] = (isset($src['latitude']) && !empty($src['latitude']))
							 ? $src['latitude'] 
							 : NULL;
		$src['longitude'] = (isset($src['longitude']) && !empty($src['longitude']))
							 ? $src['longitude'] 
							 : NULL;
		$src['resultValue'] = (isset($src['resultValue']) && !empty($src['resultValue']))
							 ? $src['resultValue'] 
							 : NULL;
		$src['indServerProcessed'] = (  isset($src['latitude']) 
									 && !empty($src['latitude']) 
									 && isset($src['longitude']) 
									 && !empty($src['longitude'])
		                             ) ? true : false;
		                             
		// when the item is indicated as being deleted, the indication server processed is overriden.
        $src['indServerProcessed'] = ($src['indDeleted']) ? true : $src['indServerProcessed'];		                             
		                             							 
		return parent::bind($src);
	}
	
	public function store($updateNulls = false) {
		if (!empty($this->value)) {
			$this->indexLoc = mb_strtoupper(mb_substr($this->value, 0, 1 ));	
		}
		return parent::store($updateNulls);
	}
	
}
?>
<?php
/**
 * Joomla! component Joaktree
 * file		view maps - view.html.php
 *
 * @version	1.5.0
 * @author	Niels van Dantzig
 * @package	Joomla
 * @subpackage	Joaktree
 * @license	GNU/GPL
 *
 * Component for genealogy in Joomla!
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Joaktree component
 */
class JoaktreeViewMap extends JViewLegacy {
	function display($tpl = null) {
		$this->lists	= array();
		$this->map 		= $this->get( 'map' );
			
		if ($this->map->params['service'] == 'staticmap') {	
			// Get data from the model
			$this->mapview 				= $this->get( 'mapView' );
			$this->lists['userAccess']	= ($this->mapview) ? true : false;			
		} 
					
		parent::display($tpl);
	}	
}
?>
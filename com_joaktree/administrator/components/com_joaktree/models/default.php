<?php
/**
 * Joomla! component Joaktree
 * file		JoaktreeModel - joaktree.php
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

// Import Joomla! libraries
jimport('joomla.application.component.model');
JLoader::register('MBJProvider', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'services'.DS.'provider.php');

class JoaktreeModelDefault extends JModelLegacy {

	var $_persons;

	function __construct() {
		parent::__construct();
	}
	
	public function getProviders() {
		return MBJProvider::getConnectors();
	}
}
?>
<?php
/**
 * Joomla! component Joaktree
 * file		front end joaktree model - joaktree.php
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

// import component libraries
JLoader::import('helper.person', JPATH_COMPONENT);

class JoaktreeModelAncestors extends JModelLegacy {
	
	function __construct() {
		parent::__construct();            		
	} 
	
	public function getUserAccess() {
		return JoaktreeHelper::getUserAccess();
	}
		
 	public function getTreeId() {
		return JoaktreeHelper::getTreeId();
 	}
		
	public function getTechnology() {
		return JoaktreeHelper::getTechnology();
	}
				
	public function getAccess() {
		static $_access;
		
		if (!isset($_access)) {
			$params = JoaktreeHelper::getJTParams();
			$ancestorEnabled = $params->get('ancestorchart', 0);
			
			if ($ancestorEnabled != 1) {
				// chart is not enabled
				$_access = false;
			} else {
				// chart is enabled
				$_access = JoaktreeHelper::getAccess();	
			}				
		}
						
		return $_access;
	}
	
	public function getPerson() {
		static $person;
		
		if (!isset($person)) {
			$id[ 'app_id' ] 	= JoaktreeHelper::getApplicationId();
			$id[ 'person_id' ] 	= JoaktreeHelper::getPersonId(); 
			$person	  =  new Person($id, 'basic');
		}
		
		return $person;
	}
}
?>
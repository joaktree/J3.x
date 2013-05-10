<?php
/**
 * Joomla! component Joaktree
 * file		view joaktree person - view.html.php
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
class JoaktreeViewDescendants extends JViewLegacy {
	function display($tpl = null) {		
		$params					= JoaktreeHelper::getJTParams();
		
		// Access
		$lists['userAccess'] 	= $this->get( 'access' );
		$lists['treeId'] 		= $this->get( 'treeId' );
		$lists['technology'] 	= $this->get( 'technology' );
			
		// Person + generations
		$personId	 			= array();
		$this->person			= $this->get( 'person' );

		$personId[]		 		= $this->person->id.'|1';

		$lists[ 'startGenNum' ]	= 1;
		$lists[ 'endGenNum' ]	= (int) $params->get('descendantlevel', 20);
		$lists[ 'app_id' ]		= $this->person->app_id;
		
		$this->assignRef( 'personId', $personId);
		$this->assignRef( 'lists',	  $lists);
		
		parent::display($tpl);
	}
	
	
}
?>
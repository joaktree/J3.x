<?php
/**
 * Joomla! component Joaktree
 * file		view joaktree locations - view.html.php
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
class JoaktreeViewLocations extends JViewLegacy {
	function display($tpl = null) {	
		$this->lists 		= array();

		// Load the parameters.
		$this->params 		= JoaktreeHelper::getJTParams();
		
		// Get data from the model
		$this->treeinfo		= $this->get( 'treeinfo' );  
		$menus  			= $this->get( 'menus' );

		// Id's and settings
		$this->lists['tree_id']		= $this->get( 'treeId' ); 
		$this->lists['userAccess'] 	= $this->get( 'access' );
		$this->lists['menuItemId'] 	= $menus[ $this->lists['tree_id'] ];
		$this->lists['interactiveMap'] 	= $this->get( 'interactiveMap' );
		
		//location list
		$this->lists['columns']		= (int) $this->params->get('columnsLoc', '3');
		$this->locationlist  		= $this->get( 'locationlist' );
		$this->lists['numberRows']	= (int) ceil( count($this->locationlist) /  $this->lists['columns']);
		
		$this->lists['linkMap'] 	= 'index.php?option=com_joaktree'
										.'&view=interactivemap'
										.'&tmpl=component'
										.'&format=raw'
										.'&treeId='.$this->lists['tree_id'];	
		$this->lists['linkList'] 	=  'index.php?option=com_joaktree'
										.'&view=joaktreelist'
										.'&tmpl=component'
										.'&layout=location'
										.'&treeId='.$this->lists['tree_id'];		
		parent::display($tpl);
	}
}
?>
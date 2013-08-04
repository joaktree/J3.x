<?php
/**
 * Joomla! component Joaktree
 * file		view joaktree start - view.html.php
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
class JoaktreeViewJoaktreestart extends JViewLegacy {
	function display($tpl = null) {	
		$this->lists 		= array();
		
		// Load the parameters.
		$this->params 		= JoaktreeHelper::getJTParams();		
		
		// Get data from the model
		$this->treeinfo		= $this->get( 'treeinfo' );  
		$menus  			= $this->get( 'menus' ); 
		
		// Id's and settings
		$this->lists['tree_id']		= $this->get( 'treeId' );
		$this->lists['userAccess'] 		= $this->get( 'access' );
		$this->lists['menuItemId'] 		= $menus[ $this->lists['tree_id'] ];
				
		//namelist
		$this->lists['columns']		= (int) $this->params->get('columns', '3');
		$this->namelist	  			= $this->get( 'namelist' );
		$this->lists['numberRows']	= (int) ceil( count($this->namelist) /  $this->lists['columns']);

		$this->lists['link'] 		=  'index.php?option=com_joaktree'
										.'&view=joaktreelist'
										.'&Itemid='.$this->lists['menuItemId']
										.'&treeId='.$this->lists['tree_id'];								
				
		parent::display($tpl);
	}
}
?>
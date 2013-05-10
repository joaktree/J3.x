<?php
/**
 * Joomla! component Joaktree
 * file		view gendex - view.html.php
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
class JoaktreeViewGendex extends JViewLegacy {
	function display($tpl = null) {	
				
		// Get data from the model
		$items	= & $this->get( 'Items' );
		$menus	= JoaktreeHelper::getMenus('joaktree');
		
		$this->assignRef( 'items',  $items );
		$this->assignRef( 'menus',  $menus );
		
		parent::display($tpl);
	}
}
?>
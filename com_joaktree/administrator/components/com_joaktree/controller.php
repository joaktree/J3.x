<?php
/**
 * Joomla! component Joaktree
 * file		Joaktree Controller - controller.php
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

jimport( 'joomla.application.component.controller' );

class JoaktreeController extends JControllerLegacy {

	function __construct() {
		// create an input object
		$input = JFactory::getApplication()->input;

		if($input->get('view') == '') {
			$input->set('view', 'default');
		}	
		
		parent::__construct();	
 
	}

	function display() {
		parent::display();
	}

}
?>
<?php
/**
 * Joomla! component Joaktree
 * file		front end controller.pjp
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

jimport('joomla.application.component.controller');

/**
 * Joaktree Component Controller
 */
class JoaktreeController extends JControllerLegacy {
	public function display($cachable = false, $urlparams = array()) {
		// Make sure we have a default view
		// create an input object
		$input = JFactory::getApplication()->input;

		if($input->get('view') == '') {
			$input->set('view', 'default');
		}	

		parent::display();
	}
}
?>
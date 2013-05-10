<?php
/**
 * Joomla! component Joaktree
 * file		administrator default view - view.html.php
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
jimport( 'joomla.application.component.view');
JLoader::register('MBJService',  JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'services'.DS.'service.php');

class JoaktreeViewDefault extends JViewLegacy {
	function display($tpl = null) {
		$this->lists = array();
		
		JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
		
		//load the language file
		MBJService::setLanguage();
		
		$this->lists['version']   = JoaktreeHelper::getJoaktreeVersion();
		$this->lists['providers'] = $this->get('providers');

		JoaktreeHelper::addSubmenu('default');		
		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$canDo	= JoaktreeHelper::getActions();
		
		JToolBarHelper::title(   '&nbsp;&nbsp;' .JText::_( 'COM_JOAKTREE_CONTROL_PANEL' ), 'joaktree' );
		
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_joaktree', '460');
		}
				
		JToolBarHelper::help('JoaktreeManuel', true, 'http://joaktree.com/index.php/en/joaktree/manual');		
	}
	
}
?>
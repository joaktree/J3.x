<?php
/**
 * Joomla! component Joaktree
 * file		administrator jt_themes view - view.html.php
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

class JoaktreeViewJt_themes extends JViewLegacy {
	function display($tpl = null) {
	
		$app = JFactory::getApplication();				
		JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
		$this->canDo	= JoaktreeHelper::getActions();
				
		// Get data from the model
		$this->items	= & $this->get( 'Data' );
		$this->pagination = & $this->get( 'Pagination' );
		
		//Filter
		$context		= 'com_joaktree.jt_themes.list.';
		
		$filter_order		= $app->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'jtmp.id',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',	'word' );
		$search			= $app->getUserStateFromRequest( $context.'search',			'search',	'',	'string' );
		$search			= JString::strtolower( $search );
		
		// table ordering
		$this->lists['order_Dir'] = $filter_order_Dir;
		$this->lists['order'] = $filter_order;
		
		// search filter
		$this->lists['search']= $search;
				
		JoaktreeHelper::addSubmenu('themes');		
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
		JToolBarHelper::title(   '&nbsp;&nbsp;' .JText::_( 'JTTHEMES_TITLE' ), 'theme' );

		if ($this->canDo->get('core.create')) {
			JToolBarHelper::addNew('add', 'JTOOLBAR_NEW');
		}
		
		if ($this->canDo->get('core.edit')) {
			JToolBarHelper::editList('edit','JTOOLBAR_EDIT');
		}

		if ($this->canDo->get('core.delete')) {
			JToolBarHelper::deleteList('JT_CONFIRMDELETE', 'delete','JTOOLBAR_DELETE');
		}

		if ($this->canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::makeDefault('setDefault', 'JTTHEME_TOOLBAR_SET_HOME');
		}

		if ($this->canDo->get('core.edit')) {
			JToolBarHelper::divider();
			JToolBarHelper::custom('edit_css', 'editcss', 'editcss', 'JTOOLBAR_EDIT_CSS', true);
		}
		
		JToolBarHelper::divider();
		JToolBarHelper::help('JoaktreeManuel', true, 'http://joaktree.com/index.php/en/joaktree/manual');		
	}
	
	protected function getSortFields()
	{
		return array(
			'jtmp.name' => JText::_('JTTHEMES_HEADING_THEME'),
			'jtmp.home' => JText::_('JTTHEMES_HEADING_DEFAULT'),
			'jtmp.id'   => JText::_('JT_HEADING_ID')
		);
	}
	
}
?>
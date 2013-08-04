<?php
/**
 * Joomla! component Joaktree
 * file		view joaktree jt_maps - view.html.php
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
class JoaktreeViewJt_maps extends JViewLegacy {
	function display($tpl = null) {
	
		$app = JFactory::getApplication();				
		JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
		$this->canDo	= JoaktreeHelper::getActions();
				
		// Get data from the model
		$this->items		= & $this->get( 'Data' );
		$this->pagination	= & $this->get( 'Pagination' );
		$this->mapSettings	= & $this->get( 'mapSettings' );
		
		//Filter
		$context			= 'com_joaktree.jt_maps.list.';
		
		$filter_order		= $app->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'jmp.name',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',	'word' );
		$search				= $app->getUserStateFromRequest( $context.'search',			'search',	'',	'string' );
		$search				= JString::strtolower( $search );
		
		// table ordering
		$this->lists['order_Dir'] = $filter_order_Dir;
		$this->lists['order'] = $filter_order;
		
		// search filter
		$this->lists['search']= $search;
				
		JoaktreeHelper::addSubmenu('maps');		
		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar() {
		$canDo	= JoaktreeHelper::getActions();
		
		JToolBarHelper::title(   '&nbsp;&nbsp;' .JText::_( 'JTMAPS_TITLE' ), 'map' );

		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew();
		}
		
		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList();
		}

		if ($canDo->get('core.delete')) {
			JToolBarHelper::deleteList('JT_CONFIRMDELETE');
		}
		
		JToolBarHelper::divider();
		//($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true)
		JToolBarHelper::custom('locations', 'location', 'JT_SUBMENU_LOCATIONS', 'JT_SUBMENU_LOCATIONS', false);
		JToolBarHelper::divider();
		JToolBarHelper::help('JoaktreeManuel', true, 'http://joaktree.com/index.php/en/joaktree/manual');

		
		// Sidebar
		JHtmlSidebar::setAction('index.php?option=com_joaktree&view=jt_maps');
	}	

	protected function getSortFields()
	{
		return array(
			'jmp.name' => JText::_('JTAPPS_LABEL_TITLE'),
			'jmp.period_start' => JText::_('JT_LABEL_PERIODSTART'),
			'jmp.period_end' => JText::_('JT_LABEL_PERIODEND')
		);
	}
}
?>
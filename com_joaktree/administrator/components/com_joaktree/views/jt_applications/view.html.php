<?php
/**
 * Joomla! component Joaktree
 * file		administrator jt_applications view - view.html.php
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

// Import custom library
JLoader::import('assets.includes.toolbar', JPATH_COMPONENT);


class JoaktreeViewJt_applications extends JViewLegacy {
	function display($tpl = null) {
	
		$app = JFactory::getApplication();				
		JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
		$this->canDo	= JoaktreeHelper::getActions();
				
		// Get data from the model
		$items			= & $this->get( 'Data' );
		$pagination		= & $this->get( 'Pagination' );
		
		//Filter
		$context		= 'com_joaktree.jt_applications.list.';
		
		$filter_order		= $app->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'jte.id',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',	'word' );
		$search				= $app->getUserStateFromRequest( $context.'search',			'search',	'',	'string' );
		$search				= JString::strtolower( $search );
		
		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;
		
		// search filter
		$lists['search']= $search;
				
		$this->assignRef( 'items',  $items );
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('lists',		$lists);
		
		JoaktreeHelper::addSubmenu('applications');		
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
		JToolBarHelper::title(   '&nbsp;&nbsp;' .JText::_( 'JTAPPS_TITLE' ), 'application' );

		if ($this->canDo->get('core.create')) {
			JToolBarHelper::addNew('add', 'JTOOLBAR_NEW');
		}
		
		if ($this->canDo->get('core.edit')) {
			JToolBarHelper::editList('edit','JTOOLBAR_EDIT');
		}

		if ($this->canDo->get('core.delete')) {
			JToolBarHelper::deleteList('JT_CONFIRMDELETE', 'delete','JTOOLBAR_DELETE');
		}

		if (($this->canDo->get('core.create')) && ($this->canDo->get('core.edit'))) {
			JToolBarHelper::divider();
			JToolBarHelper::custom( 'processGedCom', 'importgedcom', 'importgedcom', JText::_( 'JTPERSONS_BUTTON_PROCESSGEDCOM' ), true );
			JToolBarHelper::custom( 'exportGedCom' , 'exportgedcom', 'exportgedcom', JText::_( 'JTPERSONS_BUTTON_EXPORTGEDCOM' ), true );
		}

		if ($this->canDo->get('core.delete')) {
			JToolBarCustomHelper::custom('clearGedCom', 'cleargedcom','cleargedcom', JText::_( 'JTPERSONS_BUTTON_CLEARGEDCOM' ), 'JT_CONFIRMDELETE', true );
			JToolBarCustomHelper::custom('deleteGedCom', 'deletegedcom','deletegedcom', JText::_( 'JTPERSONS_BUTTON_DELETEGEDCOM' ), 'JT_CONFIRMDELETE', true );
		}
		
		JToolBarHelper::divider();
		JToolBarHelper::help('JoaktreeManuel', true, 'http://joaktree.com/index.php/en/joaktree/manual');		
	}
	
	protected function getSortFields()
	{
		return array(
			'japp.title' => JText::_('JTAPPS_HEADING_TITLE'),
			'japp.description' => JText::_('JTAPPS_HEADING_DESCRIPTION'),
			'japp.programName' => JText::_('JTAPPS_HEADING_PROGRAM'),
			'NumberOfPersons' => JText::_('JTAPPS_HEADING_PERSONS'),
		);
	}
	
}
?>
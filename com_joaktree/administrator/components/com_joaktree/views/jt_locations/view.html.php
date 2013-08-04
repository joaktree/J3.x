<?php
/**
 * Joomla! component Joaktree
 * file		view joaktree jt_locations - view.html.php
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
jimport( 'joomla.html.html.select' );

/**
 * HTML View class for the Joaktree component
 */
class JoaktreeViewJt_locations extends JViewLegacy {
	function display($tpl = null) {

		$app = JFactory::getApplication();				
		JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
		$this->canDo	= JoaktreeHelper::getActions();
				
		// Get data from the model
		$this->items		= & $this->get( 'Data' );
		$this->pagination	= & $this->get( 'Pagination' );
		$this->mapSettings	= & $this->get( 'mapSettings' );
		
		//Filter
		$context		= 'com_joaktree.jt_locations.list.';
		
		$this->lists['order']	= $app->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'jln.value',	'cmd' );
		$this->lists['order_Dir'] = $app->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',	'word' );
		$this->lists['server']	= $app->getUserStateFromRequest( $context.'filter_server',	'filter_server',	'',		'word' );
		$this->lists['status']	= $app->getUserStateFromRequest( $context.'filter_status',	'filter_status',	'',		'word' );
		
		// search filter
		$search					= $app->getUserStateFromRequest( $context.'search',			'search',	'',	'string' );
		$search					= JString::strtolower( $search );
		$this->lists['search']	= $search;
				
//		$select_attr = array();
//		$select_attr['class'] = 'inputbox';
//		$select_attr['size'] = '1';
//		$select_attr['onchange'] = 'submitform( );';
		
		// server filter
		$this->server 		= array();
		$selectObj 			= new StdClass;
		$selectObj->value 	= 'Y';
		$selectObj->text	= JText::_('JT_FILTER_SERVER_YES');
		$this->server[]		= $selectObj;  ; 			
		unset($selectObj);		
		$selectObj 			= new StdClass;
		$selectObj->value 	= 'N';
		$selectObj->text	= JText::_('JT_FILTER_SERVER_NO');
		$this->server[]		= $selectObj;  ; 			
		unset($selectObj);		
		
		
		// geocoding status filter
		$this->status 		= array();
		$selectObj 			= new StdClass;
		$selectObj->value 	= 'N';
		$selectObj->text	= JText::_('JT_FILTER_STATUS_NO');
		$this->status[]		= $selectObj;  ; 			
		unset($selectObj);		
		$selectObj 			= new StdClass;
		$selectObj->value 	= 'Y';
		$selectObj->text	= JText::_('JT_FILTER_STATUS_YES');
		$this->status[]		= $selectObj;  ; 			
		unset($selectObj);		
		
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
	protected function addToolbar()
	{
		$canDo	= JoaktreeHelper::getActions();
		
		JToolBarHelper::title(   '&nbsp;&nbsp;' .JText::_( 'JTLOCATIONS_TITLE' ), 'location' );

		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList();
			if (!empty($this->mapSettings->geocode)) {
				JToolBarHelper::custom( 'resetlocation', 'resetlocation', 'resetlocation', JText::_( 'JTLOCATIONS_BUTTON_RESET' ), true );
				JToolBarHelper::divider();
				JToolBarHelper::custom( 'geocode', 'geocode', 'geocode', JText::sprintf( 'JTLOCATIONS_BUTTON_GEOCODE', ucfirst($this->mapSettings->geocode) ), false );
			}
		}

		if ($canDo->get('core.delete')) {
			JToolBarHelper::divider();
			//$bar = JToolBar::getInstance('toolbar');
			// explanation: $bar->appendButton('Standard', $icon, $alt, $task, $listSelect);
			//$bar->appendButton('purge', 'location', 'JTFAMTREE_TASK', 'purgeLocation', false);
			JToolBarHelper::custom( 'purgelocations', 'purgelocations', 'purgelocations', JText::_( 'JTLOCATIONS_BUTTON_PURGE' ), false );			
		}
		
		JToolBarHelper::divider();
		JToolBarHelper::help('JoaktreeManuel', true, 'http://joaktree.com/index.php/en/joaktree/manual');
				
		// Sidebar
		JHtmlSidebar::setAction('index.php?option=com_joaktree&view=jt_locations');

		JHtmlSidebar::addFilter(
			JText::_('JT_FILTER_SERVER'),
			'filter_server',
			JHtml::_('select.options', $this->server, 'value', 'text', $this->lists['server'], true)
		);

		JHtmlSidebar::addFilter(
			JText::_('JT_FILTER_STATUS_ALL'),
			'filter_status',
			JHtml::_('select.options', $this->status, 'value', 'text', $this->lists['status'], true)
		);
	}	

	protected function getSortFields()
	{
		return array(
			'jln.value' => JText::_('JT_LABEL_LOCATION'),
			'jln.resultValue' => JText::_('JT_LABEL_GEOCODELOCATION'),
			'jln.latitude' => JText::_('JT_LABEL_LATITUDE'),
			'jln.longitude' => JText::_('JT_LABEL_LONGITUDE'),
			'jln.results' => JText::_('JT_LABEL_RESULTS')
		);
	}
}
?>
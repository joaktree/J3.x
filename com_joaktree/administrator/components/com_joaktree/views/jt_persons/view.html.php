<?php
/**
 * Joomla! component Joaktree
 * file		administrator jt_persons view - view.html.php
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

class JoaktreeViewJt_persons extends JViewLegacy {
	function display($tpl = null) {
		
		$app = JFactory::getApplication();
		JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
		JHTML::script( JoaktreeHelper::jsfile() );		
		
		// add script 
		$document 		= &JFactory::getDocument();
		$document->addScriptDeclaration($this->addScript());
 		
		// Get data from the model
		$this->items   		= $this->get( 'Persons' );
		$trees	     		= $this->get( 'Trees' );
		$this->pagination  	= $this->get( 'Pagination' );
		$this->lists['patronym'] 	= $this->get( 'patronymShowing' );
		$this->columns		= $this->get( 'columnSettings' );
		
		//Filter
		$context			= 'com_joaktree.jt_persons.list.';
		
		$this->filter['state']		= $app->getUserStateFromRequest( $context.'filter_state',	'filter_state',		'',		'cmd' );
		$this->filter['living']		= $app->getUserStateFromRequest( $context.'filter_living',	'filter_living',	'',		'word' );
		$this->filter['page']		= $app->getUserStateFromRequest( $context.'filter_page',	'filter_page',		'',		'word' );
		$this->filter['map']		= $app->getUserStateFromRequest( $context.'filter_map',		'filter_map',		'',		'int' );
		$this->filter['tree']		= $app->getUserStateFromRequest( $context.'filter_tree',	'filter_tree',		'',		'int' );
		$this->filter['apptitle']	= $app->getUserStateFromRequest( $context.'filter_apptitle','filter_apptitle',	'',		'int' );
		$this->filter['robots']		= $app->getUserStateFromRequest( $context.'filter_robots',	'filter_robots',	'',		'int' );
		$this->filter['order']		= $app->getUserStateFromRequest( $context.'filter_order',	'filter_order',		'jpn.id',	'cmd' );
		$this->filter['order_Dir']	= $app->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir',	'',		'word' );
		$search1					= $app->getUserStateFromRequest( $context.'search1',		'search1',		'',		'string' );
		$search1					= JString::strtolower( $search1 );
		$search2					= $app->getUserStateFromRequest( $context.'search2',		'search2',		'',		'string' );
		$search2					= JString::strtolower( $search2 );
		$search3					= $app->getUserStateFromRequest( $context.'search3',		'search3',		'',		'string' );
		$search3					= JString::strtolower( $search3 );
		
		// table ordering
		$this->lists['order_Dir'] 	= $this->filter['order_Dir'] ;
		$this->lists['order'] 		= $this->filter['order'];

		// search filter
		$this->lists['search1']= $search1;
		$this->lists['search2']= $search2;
		$this->lists['search3']= $search3;

		// application filter
		$this->appTitle 	= JoaktreeHelper::getApplications();
		
		// default family tree filter
		$this->tree = array();
		for ($i = 1; $i <= count($trees); $i++) {
			$selectObj 			= new StdClass;
			$selectObj->value 	= $trees[$i-1]->id;
			$selectObj->text	= $trees[$i-1]->name;
			$this->tree[]	= $selectObj;  ; 			
			unset($selectObj);		
		}
				
		// state filter
		$this->state = array( 'published' => 1
						 	, 'unpublished' => 1
							, 'archived' => 0
							, 'trash' => 0
							, 'all' => 0
							);			

//		$select_attr = array();
//		$select_attr['class'] = 'inputbox';
//		$select_attr['size'] = '1';
//		$select_attr['onchange'] = 'submitform( );';

		// living filter
		$this->living = array();
		$selectObj 			= new StdClass;
		$selectObj->value 	= 'L';
		$selectObj->text	= JText::_('JT_FILTER_VAL_LIVING');
		$this->living[]	= $selectObj;  ; 			
		unset($selectObj);		
		$selectObj 			= new StdClass;
		$selectObj->value 	= 'D';
		$selectObj->text	= JText::_('JT_FILTER_VAL_NOTLIVING');
		$this->living[]	= $selectObj;  ; 			
		unset($selectObj);		
			   
		// page filter
		$this->page = array();
		$selectObj 			= new StdClass;
		$selectObj->value 	= 'Y';
		$selectObj->text	= JText::_('JT_FILTER_VAL_PAGE');
		$this->page[]	= $selectObj;  ; 			
		unset($selectObj);		
		$selectObj 			= new StdClass;
		$selectObj->value 	= 'N';
		$selectObj->text	= JText::_('JT_FILTER_VAL_NOPAGE');
		$this->page[]	= $selectObj;  ; 			
		unset($selectObj);		
		
		// map filter
		$this->map = array();
		$selectObj 			= new StdClass;
		$selectObj->value 	= 2;
		$selectObj->text	= JText::_('JT_FILTER_VAL_STATMAP');
		$this->map[]	= $selectObj;  ; 			
		unset($selectObj);		
		$selectObj 			= new StdClass;
		$selectObj->value 	= 3;
		$selectObj->text	= JText::_('JT_FILTER_VAL_DYNMAP');
		$this->map[]	= $selectObj;  ; 			
		unset($selectObj);		
		$selectObj 			= new StdClass;
		$selectObj->value 	= 1;
		$selectObj->text	= JText::_('JT_FILTER_VAL_NOMAP');
		$this->map[]	= $selectObj;  ; 			
		unset($selectObj);		
				
		// robots filter
		$this->robots = array();
		$selectObj 			= new StdClass;
		$selectObj->value 	= 1;
		$selectObj->text	= JText::_('JT_ROBOT_USE_TREE');
		$this->robots[]	= $selectObj;  ; 			
		unset($selectObj);		
		$selectObj 			= new StdClass;
		$selectObj->value 	= 2;
		$selectObj->text	= JText::_('JGLOBAL_INDEX_FOLLOW');
		$this->robots[]	= $selectObj;  ; 			
		unset($selectObj);		
		$selectObj 			= new StdClass;
		$selectObj->value 	= 3;
		$selectObj->text	= JText::_('JGLOBAL_NOINDEX_FOLLOW');
		$this->robots[]	= $selectObj;  ; 			
		unset($selectObj);		
		$selectObj 			= new StdClass;
		$selectObj->value 	= 4;
		$selectObj->text	= JText::_('JGLOBAL_INDEX_NOFOLLOW');
		$this->robots[]	= $selectObj;  ; 			
		unset($selectObj);		
		$selectObj 			= new StdClass;
		$selectObj->value 	= 5;
		$selectObj->text	= JText::_('JGLOBAL_NOINDEX_NOFOLLOW');
		$this->robots[]	= $selectObj;  ; 			
		unset($selectObj);		
		// end of filters
		
		JoaktreeHelper::addSubmenu('persons');		
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
		
		JToolBarHelper::title(   '&nbsp;&nbsp;' .JText::_( 'JTPERSONS_TITLE' ), 'person' );

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::custom( 'publishAll', 'publish', 'publish', JText::_( 'JTPERSONS_BUTTON_PUBLISHALL' ), true );
			JToolBarHelper::custom( 'unpublishAll', 'unpublish', 'unpublish', JText::_( 'JTPERSONS_BUTTON_UNPUBLISHALL' ), true );
			JToolBarHelper::divider();
			JToolBarHelper::custom( 'livingAll', 'living', 'living', JText::_( 'JTPERSONS_BUTTON_LIVINGALL' ), true );
			JToolBarHelper::custom( 'notLivingAll', 'notliving', 'notliving', JText::_( 'JTPERSONS_BUTTON_NOTLIVINGALL' ), true );
			JToolBarHelper::divider();
			JToolBarHelper::custom( 'pageAll', 'page', 'page', JText::_( 'JTPERSONS_BUTTON_PAGEALL' ), true );
			JToolBarHelper::custom( 'noPageAll', 'nopage', 'nopage', JText::_( 'JTPERSONS_BUTTON_NOPAGEALL' ), true );
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.edit')) {
			JToolBarHelper::custom( 'mapStatAll', 'statmap', 'statmap', JText::_( 'JTPERSONS_BUTTON_STATMAPALL' ), true );
			JToolBarHelper::custom( 'mapDynAll', 'dynmap', 'dynmap', JText::_( 'JTPERSONS_BUTTON_DYNMAPALL' ), true );
			JToolBarHelper::custom( 'noMapAll', 'nomap', 'nomap', JText::_( 'JTPERSONS_BUTTON_NOMAPALL' ), true );
			JToolBarHelper::divider();
		}
		
		JToolBarHelper::help('JoaktreeManuel', true, 'http://joaktree.com/index.php/en/joaktree/manual');	

		// Sidebar
		JHtmlSidebar::setAction('index.php?option=com_joaktree&view=jt_persons');

		JHtmlSidebar::addFilter(
			JText::_('JT_FILTER_APPLICATION'),
			'filter_apptitle',
			JHtml::_('select.options', $this->appTitle, 'value', 'text', $this->filter['apptitle'], true)
		);
		
		JHtmlSidebar::addFilter(
			JText::_('JT_FILTER_TREE'),
			'filter_tree',
			JHtml::_('select.options', $this->tree, 'value', 'text', $this->filter['tree'], true)
		);
		
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_state',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', $this->state), 'value', 'text',  $this->filter['state'], true)
		);
		
		JHtmlSidebar::addFilter(
			JText::_('JT_FILTER_LIVING'),
			'filter_living',
			JHtml::_('select.options', $this->living, 'value', 'text', $this->filter['living'], true)
		);
		
		JHtmlSidebar::addFilter(
			JText::_('JT_FILTER_PAGE'),
			'filter_page',
			JHtml::_('select.options', $this->page, 'value', 'text', $this->filter['page'], true)
		);
		
		JHtmlSidebar::addFilter(
			JText::_('JT_FILTER_MAP'),
			'filter_map',
			JHtml::_('select.options', $this->map, 'value', 'text', $this->filter['map'], true)
		);
		
		JHtmlSidebar::addFilter(
			JText::_('JT_FILTER_ROBOTS'),
			'filter_robots',
			JHtml::_('select.options', $this->robots, 'value', 'text', $this->filter['robots'], true)
		);
	}
	
	protected function addScript() {
		$script = array();
		$params  	= JComponentHelper::getParams('com_joaktree') ;
		$indCookie	= $params->get('indCookies', true);
		
		$script[] = "function jt_toggle(tag,col) { ";
		$script[] = "  var oEl, i, elements, cEl, num; ";
		$script[] = ($indCookie) ? "  var myCookie; " : " ";
		$script[] = "  elements = document.getElementById('editcell').getElements(tag); ";
		$script[] = "  cEl =  document.getElementById('footer'); ";
		$script[] = "  num = (cEl.getProperty('colspan')).toInt(); ";
		$script[] = "  for (i=0; i < elements.length; i++ ) { ";
		$script[] = "    if($(elements[i])){ ";
		$script[] = "      oEl = $(elements[i]); ";
		$script[] = "      if (oEl.hasClass('jt-hide-'+col)) { ";
		$script[] = "        oEl.removeClass('jt-hide-'+col); ";
		$script[] = "        oEl.addClass('jt-show-'+col); ";
		$script[] = "        num = num + 1; ";
		$script[] = ($indCookie) ? "        myCookie = Cookie.write('jt_'+col, '1', {duration: 0}); " : " ";
		$script[] = "      } else if (oEl.hasClass('jt-show-'+col)) { ";
		$script[] = "        oEl.removeClass('jt-show-'+col); ";
		$script[] = "        oEl.addClass('jt-hide-'+col); ";
		$script[] = "        num = num - 1; ";
		$script[] = ($indCookie) ? "        myCookie = Cookie.read('jt_'+col); " : " ";
		$script[] = ($indCookie) ? "        if (myCookie == '1') { Cookie.dispose('jt_'+col); } " : " ";
		$script[] = "      } ";
		$script[] = "    } ";
		$script[] = "  } ";
//		$script[] = "  if (tag == 'th') { ";
//		$script[] = "    cEl.setProperty('colspan', num); ";
//		$script[] = "    document.getElementById('header').setProperty('colspan', num); ";
//		$script[] = "  } ";
		$script[] = "  return false; ";
		$script[] = "} ";
		$script[] = "";
		
		return implode("\n", $script);		
	}
	
	protected function getSortFields()
	{
		$fields = array(
			'jpn.id' => JText::_('JT_HEADING_ID'),
			'jpn.firstName' => JText::_('JTPERSONS_HEADING_FIRSTNAME')
		);
		
		if ($this->lists['patronym']) {
			$fields['jpn.patronym'] = JText::_('JTPERSONS_HEADING_PATRONYM');
		}
		
		$fields['jpn.familyName'] 	= JText::_('JTPERSONS_HEADING_FAMNAME');
		$fields['13'] 				= JText::_('JTPERSONS_HEADING_PERIOD');
				
		return $fields; 
	}	
}
?>
<?php
/**
 * Joomla! component Joaktree
 * file		administrator jt_trees view - view.html.php
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
//JLoader::register('JButtonStandard', JPATH_LIBRARIES.DS.'joomla'.DS.'html'.DS.'toolbar'.DS.'button'.DS.'standard.php');

class JoaktreeViewJt_trees extends JViewLegacy {
	function display($tpl = null) {
	
		$app = JFactory::getApplication();				
		JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
		JHTML::script( JoaktreeHelper::jsfile() );
		$this->canDo	= JoaktreeHelper::getActions();
		
		// Get data from the model
		$this->items		= & $this->get( 'Data' );
		$this->pagination	= & $this->get( 'Pagination' );
		
		//Filter
		$context		= 'com_joaktree.jt_trees.list.';
		
		$this->filter['state']		= $app->getUserStateFromRequest( $context.'filter_state',		'filter_state',		'',	'cmd' );
		$this->filter['apptitle']	= $app->getUserStateFromRequest( $context.'filter_apptitle',	'filter_apptitle',	'',	'int' );
		$this->filter['gendex']		= $app->getUserStateFromRequest( $context.'filter_gendex',		'filter_gendex',	'',	'int' );
		$this->filter['order']		= $app->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'jte.id',	'cmd' );
		$this->filter['order_Dir']	= $app->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',	'word' );
		$search						= $app->getUserStateFromRequest( $context.'search',			'search',	'',	'string' );
		$search						= JString::strtolower( $search );
		
		// table ordering
		$this->lists['order_Dir'] 	= $this->filter['order_Dir'];
		$this->lists['order'] 		= $this->filter['order'];
		
		// search filter
		$this->lists['search']= $search;
		
		// state filter
		$this->state = array( 'published' => 1
						 	, 'unpublished' => 1
							, 'archived' => 0
							, 'trash' => 0
							, 'all' => 0
							);			
		
		// application filter
		$this->appTitle 	= JoaktreeHelper::getApplications();
				
		// gendex filter
		$this->gendex = array();
		$selectObj 			= new StdClass;
		$selectObj->value 	= 1;
		$selectObj->text	= JText::_('JNO');
		$this->gendex[]	= $selectObj;  ; 			
		unset($selectObj);
		$selectObj 			= new StdClass;
		$selectObj->value 	= 2;
		$selectObj->text	= JText::_('JYES');
		$this->gendex[]	= $selectObj;  ; 			
		unset($selectObj);
						
		$this->lists['jsscript'] 	= $this->getJTscript();
		$this->lists['action']	= $this->get('action');
		if ($this->lists['action'] == 'assign') {
			$this->lists['act_treeId']= $this->get('treeId');
		}
				
		JoaktreeHelper::addSubmenu('trees');
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
		JToolBarHelper::title(   '&nbsp;&nbsp;' .JText::_( 'JTFAMTREE_TITLE' ), 'familytree' );

		if ($this->canDo->get('core.create')) {
			JToolBarHelper::addNew();
			//JToolBarHelper::addNew('add', 'JTOOLBAR_NEW');
		}
		
		if ($this->canDo->get('core.edit')) {
			JToolBarHelper::editList();
			//JToolBarHelper::editList('edit','JTOOLBAR_EDIT');
		}

		if ($this->canDo->get('core.delete')) {
			JToolBarHelper::deleteList('JT_CONFIRMDELETE');
		}

		if ($this->canDo->get('core.edit')) {
			JToolBarHelper::divider();
			$bar = JToolBar::getInstance('toolbar');
			// explanation: $bar->appendButton('Standard', $icon, $alt, $task, $listSelect);
			$bar->appendButton('AssignFT', 'assignfamilytree', 'JTFAMTREE_TASK', 'assignFamilyTree', true);
		}
		
		JToolBarHelper::divider();
		JToolBarHelper::help('JoaktreeManuel', true, 'http://joaktree.com/index.php/en/joaktree/manual');

		
		// Sidebar
		JHtmlSidebar::setAction('index.php?option=com_joaktree&view=jt_trees');

		JHtmlSidebar::addFilter(
			JText::_('JT_FILTER_APPLICATION'),
			'filter_apptitle',
			JHtml::_('select.options', $this->appTitle, 'value', 'text', $this->filter['apptitle'], true)
		);
		
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_state',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', $this->state), 'value', 'text',  $this->filter['state'], true)
		);
		
		JHtmlSidebar::addFilter(
			JText::_('JT_FILTER_GENDEX'),
			'filter_gendex',
			JHtml::_('select.options', $this->gendex, 'value', 'text', $this->filter['gendex'], true)
		);
		
	}	
	
	private function getJTscript() {
		$script  = array();
		$title1  = addslashes(JText::_('JTFAMTREE_TASK'));
		$title2  = addslashes(JText::_('JTPROCESS_MSG'));
		$start   = addslashes(JText::_('JTPROCESS_START'));
		$current = addslashes(JText::_('JTPROCESS_CURRENT'));
		$end     = addslashes(JText::_('JTPROCESS_END'));
		$button  = addslashes(JText::_('JTPROCESS_DONE'));
		
		$script[] = "function assignFTInit(trid) { ";
		$script[] = "  var form = document.adminForm; ";
		$script[] = "  var treeid = ''; ";
		
		$script[] = "  if (trid) { ";
		$script[] = "    treeid = trid + '!'; ";
		$script[] = "  } else { ";
		$script[] = "    for (var i = 0; true; i++) { ";
		$script[] = "      var cbx = form['cb'+i]; ";
		$script[] = "      if (!cbx) break; ";
		$script[] = "      if (cbx.checked == true) { ";
		$script[] = "        treeid = treeid + cbx.value + '!'; ";
		$script[] = "      } ";
		$script[] = "    } ";
		$script[] = "  } ";
		$script[] = "   ";
		$script[] = "   ";
		
		$script[] = "  var container = document.getElementById('system-message-container'); ";
		$script[] = "  var lft = new Element('div', {'class': 'width-40 fltrt'});";
		$script[] = "  var fldlft = new Element('fieldset', {'class': 'adminform'}); ";
		$script[] = "  var leglft = new Element('legend', {html: '$title1'}); ";
		$script[] = "  var ullft  = new Element('ul', {'class': 'adminformlist'}); ";
		$script[] = "  var lista  = new Element('li'); ";
		$script[] = "  var licur  = new Element('li'); ";
		$script[] = "  var liend  = new Element('li'); ";
		$script[] = "  var labst  = new Element('label', {html: '$start'}); ";
		$script[] = "  var labcur = new Element('label', {html: '$current'}); ";
		$script[] = "  var labend = new Element('label', {html: '$end'}); ";
		$script[] = "  var inpst  = new Element('input', {id: 'start', type: 'text', 'class': 'readonly'}); ";
		$script[] = "  var inpcur = new Element('input', {id: 'current', type: 'text', 'class': 'readonly'}); ";
		$script[] = "  var inpend = new Element('input', {id: 'end', type: 'text', 'class': 'readonly'}); ";
		$script[] = "  lft.inject(container); ";
		$script[] = "  fldlft.inject(lft); ";
		$script[] = "  leglft.inject(fldlft); ";
		$script[] = "  ullft.inject(fldlft); ";
		$script[] = "  lista.inject(ullft); ";
		$script[] = "  labst.inject(lista); ";	
		$script[] = "  inpst.inject(lista); ";
		$script[] = "  licur.inject(ullft); ";
		$script[] = "  labcur.inject(licur); ";
		$script[] = "  inpcur.inject(licur); ";
		$script[] = "  liend.inject(ullft); ";
		$script[] = "  labend.inject(liend); ";
		$script[] = "  inpend.inject(liend); ";
		
		$script[] = "  var rht = new Element('div', {'class': 'width-50'}); ";
		$script[] = "  var fldrht = new Element('fieldset', {'class': 'adminform', style: 'min-height: 92px;'}); ";
		$script[] = "  var legrht = new Element('legend', {html: '$title2'}); ";
		$script[] = "  var divrht = new Element('div', {id: 'procmsg'}); ";
		$script[] = "  var butrht = new Element('button', {id: 'butprocmsg', html: '$button', disabled: '1', onclick: 'submitform();', style: 'margin-left: 10px;'}); ";
		$script[] = "  rht.inject(container); ";
		$script[] = "  fldrht.inject(rht); ";
		$script[] = "  legrht.inject(fldrht); ";
		$script[] = "  divrht.inject(fldrht); ";
		$script[] = "  butrht.inject(rht); ";
		
		$script[] = "  var url = 'index.php?option=com_joaktree&view=jt_trees&format=raw&tmpl=component&init=1&treeId=' + treeid; ";
		$script[] = "  assignFT(url); ";
		$script[] = "} ";
		$script[] = " ";	
	
		return implode("\n", $script);
	}
	
	protected function getSortFields()
	{
		return array(
			'jte.name' => JText::_('JTFAMTREE_HEADING_TREE'),
			'japp.title' => JText::_('JTFAMTREE_HEADING_APPTITLE'),
			'jte.indGendex' => JText::_('JTFAMTREE_HEADING_GENDEX'),
			'access_level' => JText::_('JT_HEADING_ACCESS'),
			'theme' => JText::_('JT_HEADING_THEME')
		);
	}
}


class JToolbarButtonAssignFT extends JToolbarButtonStandard {
	protected function _getCommand($name, $task, $list)
	{
		JHtml::_('behavior.framework');
		$message = JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
		$message = addslashes($message);

		if ($list)
		{
			//$cmd = "if (document.adminForm.boxchecked.value==0){alert('$message');}else{ Joomla.submitbutton('$task')}";
			$cmd = "if (document.adminForm.boxchecked.value==0){alert('$message');}else{ assignFTInit(); }";
		}
		else
		{
			//$cmd = "Joomla.submitbutton1('$task')";
			$cmd = "assignFTInit();";
		}

		return $cmd;
	}
}
?>
<?php
/**
 * Joomla! component Joaktree
 * file		administrator jt_settings view - view.html.php
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

class JoaktreeViewJt_settings extends JViewLegacy {
	function display($tpl = null) {

		JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
		JHTML::script( JoaktreeHelper::jsfile() );		
		
		// what is the layout
		$this->layout = JFactory::getApplication()->input->get('layout');
		
		// Get data from the model
		if ($this->layout == 'personevent' ) {
			$this->items		= & $this->get( 'DataPersEvent' );
			$this->pagination	= & $this->get( 'personPagination' );
		} else if ($this->layout == 'personname' ) {
			$this->items		= & $this->get( 'DataPersName' );
			$this->pagination	= & $this->get( 'namePagination' );
		} else if ($this->layout == 'relationevent' ) {
			$this->items		= & $this->get( 'DataRelaEvent' );
			$this->pagination	= & $this->get( 'relationPagination' );
		} else {
			$this->items		= & $this->get( 'DataPersEvent' );
			$this->pagination	= & $this->get( 'personPagination' );
		}
		
		//Filter
		$context		= 'com_joaktree.jt_settings.list.';
		
		JoaktreeHelper::addSubmenu($this->layout);	
		$this->addToolbar($this->layout);
		$this->sidebar = JHtmlSidebar::render();
		$this->html = $this->getHtml();		
		parent::display($tpl);
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar($layout)
	{
		$canDo	= JoaktreeHelper::getActions();
		
		// Get data from the model
		if ($this->layout == 'personevent' ) {
			JToolBarHelper::title(   '&nbsp;&nbsp;' .JText::_( 'JTSETTINGS_TITLE_PERSONEVENTS' ), 'display1' );
		} else if ($this->layout == 'personname' ) {
			JToolBarHelper::title(   '&nbsp;&nbsp;' .JText::_( 'JTSETTINGS_TITLE_NAMES' ), 'display2' );
		} else if ($this->layout == 'relationevent' ) {
			JToolBarHelper::title(   '&nbsp;&nbsp;' .JText::_( 'JTSETTINGS_TITLE_RELATIONEVENTS' ), 'display3' );
		} else {
			JToolBarHelper::title(   '&nbsp;&nbsp;' .JText::_( 'JTSETTINGS_TITLE_PERSONEVENTS' ), 'display1' );
		}
		
		if ($canDo->get('core.edit')) {
		//	JToolBarHelper::save('save', JText::_( 'JTSETTINGS_HEADING_SAVE' ), 'title');
			JToolBarHelper::save('save', JText::_( 'JTSETTINGS_HEADING_SAVE' ), 'title');
		}
		
		if ($layout == 'personevent' ) {
			JToolBarHelper::help('JoaktreeManuel', true, 'http://joaktree.com/index.php/en/joaktree/manual');		
		} else if ($layout == 'personname' ) {
			JToolBarHelper::help('JoaktreeManuel', true, 'http://joaktree.com/index.php/en/joaktree/manual');		
		} else if ($layout == 'relationevent' ) {
			JToolBarHelper::help('JoaktreeManuel', true, 'http://joaktree.com/index.php/en/joaktree/manual');		
		} else {
			JToolBarHelper::help('JoaktreeManuel', true, 'http://joaktree.com/index.php/en/joaktree/manual');		
		}
		
		// Sidebar
		JHtmlSidebar::setAction('index.php?option=com_joaktree&view=jt_settings&layout='.$layout);
		
	}	
	
	private function getHtml() {
		$html 	= array();
		$canDo	= JoaktreeHelper::getActions();
		
		$html[] = '<form action="'.JRoute::_('index.php?option=com_joaktree').'" method="post" id="adminForm" name="adminForm" >' ;
		
		$saveOrderingUrl = 'index.php?option=com_joaktree&task=joaktree.saveOrderAjax&tmpl=component';
		$html[] = JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', null, $saveOrderingUrl);
		
		
		if(!empty( $this->sidebar)) {
			$html[] = '<div id="j-sidebar-container" class="span2">';
			$html[] = $this->sidebar;
			$html[] = '</div>';
			$divClassSpan = 'span10';
		} else {
			$divClassSpan = '';
		}
		
		$html[] = '<div id="j-main-container" class="'.$divClassSpan.'">';
		
		$html[] = '<!-- No filter row -->';	
		$html[] = '<div class="clearfix"> </div>'; 
		
		$html[] = '<!--  table -->';
		$html[] = '<table class="table table-striped" id="articleList">';
		$html[] = '  <thead>';
		//$html[] = '    <tr>';
		//$html[] = '      <th colspan="5">&nbsp;</th>';
		//$html[] = '      <th colspan="2">'.JText::_( 'JTSETTINGS_HEADING_LIVINGPERSON' ).'</th>';
		//$html[] = '      <th colspan="2">&nbsp;</th>';
		//$html[] = '    </tr>';
		$html[] = '    <tr>';
		$html[] = '      <th width="1%" class="nowrap center hidden-phone">'.JText::_( 'JT_HEADING_NUMBER' ).'</th>';
		$html[] = '      <th width="1%" class="hidden-phone">';
		$html[] = '        <input type="checkbox" name="checkall-toggle" value="" title="'.JText::_('JGLOBAL_CHECK_ALL').'" onclick="Joomla.checkAll(this)" />';
		$html[] = '      </th>';
		$html[] = '      <th class="nowrap hidden-phone">';
		$html[] = JHtml::_('image', 'admin/icon-16-notice-note.png', null, 'title="'.JText::_( 'JTSETTINGS_HEADING_EXPLANATION' ).'"', true);
		$html[] = '      </th>';	
		$html[] = '      <th class="nowrap hidden-phone">'.JText::_( 'JTSETTINGS_HEADING_FIELD' ).'</th>';
		$html[] = '      <th class="nowrap hidden-phone">'.JText::_( 'JTSETTINGS_HEADING_ORDER' ).'</th>';
		$html[] = '      <th class="nowrap hidden-phone">'.JText::_( 'JT_HEADING_PUBLISHED' ).'</th>';
		$html[] = '      <th class="nowrap hidden-phone">'.JText::_( 'JTSETTINGS_HEADING_SAVE' ).'</th>';
		$html[] = '      <th class="nowrap hidden-phone">'.JText::_( 'JT_HEADING_ACCESS' ).'</th>';
		$html[] = '      <th class="nowrap hidden-phone">'.JText::_( 'JTSETTINGS_HEADING_ACCESS_LIVPERSON' ).'</th>';
		$html[] = '      <th class="nowrap hidden-phone">'.JText::_( 'JTSETTINGS_HEADING_ALT_LIVPERSON' ).'</th>';
		
		$html[] = '    </tr>';
		$html[] = '  </thead>'; 
		$html[] = '  <tbody>'; 
		
		$html[] = '  </tbody>'; 
		foreach ($this->items as $i => $row) {
			$published 	= JHTML::_('grid.published', $row, $i );
			
			$gedcomLabel  = '<input type="hidden" id="jtid'.$row->id.'" name="jtid['.$row->id.']" value="'.$row->id.'" />';
			$gedcomLabel .= '<input type="hidden" id="code'.$row->id.'" name="code['.$row->id.']" value="'.JText::_($row->code).'" />';
			$gedcomLabel .= JText::_( $row->code );
			 
			$access		 = '<select id="access'.$row->id.'" name="access'.$row->id.'" class="inputbox" onchange="javascript:changeAccessLevel(\'cb'.$i.'\')">';
			$access		.= JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $row->access);
			$access		.= '</select>';			
			
			$accessLiving = '<select id="accessLiving'.$row->id.'" name="accessLiving'.$row->id.'" class="inputbox" onchange="javascript:changeAccessLevel(\'cb'.$i.'\')">';
			$accessLiving .= '<option  value="">'.JText::_('JTSETTINGS_LISTVALUE_NOBODY').'</option>';
			$accessLiving .= JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $row->accessLiving);
			$accessLiving .= '</select>';	
			
			$altLiving  = '<select id="altLiving'.$row->id.'" name="altLiving'.$row->id.'" class="inputbox" onchange="javascript:changeAccessLevel(\'cb'.$i.'\')">';
			$altLiving .= '<option  value="">'.JText::_('JTSETTINGS_LISTVALUE_NOBODY').'</option>';
			$altLiving .= JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $row->altLiving);
			$altLiving .= '</select>';	
			
			$html[] = '<tr class="row'.($i % 2).'">';
			$html[] = '  <td class="nowrap center hidden-phone">'.$this->pagination->getRowOffset( $i ).'</td>';
			$html[] = '  <td class="center hidden-phone">'.JHTML::_('grid.id',   $i, $row->id ).'</td>';
			
			$html[] = '  <td class="hidden-phone" >';
			$html[] = JHtml::_('image', 'admin/icon-16-notice-note.png', null, 'title="'.$this->showExplanation($row, $i).'"', true);
			$html[] = '  </td>';
			
			$html[] = '  <td class="nowrap hidden-phone">'.$gedcomLabel.'</td>';
			$html[] = '  <td class="order nowrap center hidden-phone">';
			  if ($canDo->get('core.edit')) {
					$html[] = '<span class="sortable-handler hasTooltip" >';
					$html[] = '<i class="icon-menu"></i>';
					$html[] = '</span>';
					$html[] = '<input type="text" name="order[]" size="5" value="'.$row->ordering.'" class="width-20 text-area-order " />';
			  } else {
			  		$html[] = '<span class="sortable-handler inactive" >';
			  		$html[] = '<i class="icon-menu"></i>';
			  		$html[] = '</span>';
			  }
			$html[] = '  </td>';
			
			$html[] = '  <td class="center hidden-phone">'.JHTML::_('grid.published', $row, $i ).'</td>';
			$html[] = '  <td class="center hidden-phone active">';
			$html[] = '    <a href="javascript:jtsaveaccess(\'cb'.$i.'\')" title="'.JText::_('JTSETTINGS_TOOLTIP_SAVEACCESS').'">';
			$html[] = '    <span style="padding-left: 16px; background: url(/J30/administrator/templates/isis/images/admin/filesave.png) no-repeat;"></span>';
			$html[] = '    </a>';
			$html[] = '  </td>';			
			$html[] = '  <td class="hidden-phone">'.$access.'</td>';
			$html[] = '  <td class="hidden-phone">'.$accessLiving.'</td>';
			$html[] = '  <td class="hidden-phone">'.$altLiving.'</td>';
			
			$html[] = '</tr>';
		}
		$html[] = '</table>';
		
				
		$html[] = '<input type="hidden" name="option" value="com_joaktree" />';
		$html[] = '<input type="hidden" name="task" value="" />';
		$html[] = '<input type="hidden" name="controller" value="jt_settings" />';
		$html[] = '<input type="hidden" name="boxchecked" value="0" />';
		$html[] = '<input type="hidden" name="layout" value="'.$this->layout.'" />';
		$html[] = JHtml::_('form.token');
		
		$html[] = '</div>';
		$html[] = '</form>';						
		
		return implode("\n", $html);
	}
	
	private function showExplanation($row, $i) {
		$html = '';
		$color 		= 'blue';
		
		//$value 		= '<strong>'.strtoupper( JText::_( $row->code ) ).'</strong>';
		$value 			= strtoupper( JText::_( $row->code ) );
		$txtPerson		= JText::_('JTSETTINGS_EXPTEXT_PERSON').'&nbsp;';
		//$personNotLiv	= $txtPerson.'<em>'.JText::_('JTSETTINGS_EXPTEXT_NOT_LIVPERSON').'</em>:&nbsp;';
		$personNotLiv	= $txtPerson.JText::_('JTSETTINGS_EXPTEXT_NOT_LIVPERSON').':&nbsp;';
		//$personLiving	= $txtPerson.'<em>'.JText::_('JTSETTINGS_EXPTEXT_LIVPERSON').'</em>:&nbsp;';
		$personLiving	= $txtPerson.JText::_('JTSETTINGS_EXPTEXT_LIVPERSON').':&nbsp;';
		
		if (!$row->published) {
			// nothing is published
			$html .= $value.'&nbsp;'.JText::_('JTSETTINGS_EXPTEXT_FULLYHIDDEN');
		} else {
			// something is published
			$html .= $personNotLiv.$value.'&nbsp;';
			$html .= JText::_('JTSETTINGS_EXPTEXT_ACCESSLEVELS').'&nbsp;';
			//$html .= '<span style="color: '.$color.';">'.$row->access_level .'</span>.';
			$html .= $row->access_level .'.';
			
			if (  (($row->accessLiving != null) and ($row->accessLiving != 0)) 
			   or (($row->altLiving    != null) and ($row->altLiving    != 0)) 
			   ) {			
			
				if (($row->accessLiving != null) and ($row->accessLiving != 0)) {
					//$html .= '<br/>'.$personLiving.$value.'&nbsp;';
					$html .= $personLiving.$value.'&nbsp;';
					$html .= JText::_('JTSETTINGS_EXPTEXT_ACCESSLEVELS').'&nbsp;';
					//$html .= '<span style="color: '.$color.';">'.$row->access_level_living.'</span>';
					$html .= $row->access_level_living;
				}
					
				if (   ($row->altLiving != null) 
				   and ($row->altLiving != 0)
				   and ($row->altLiving != $row->accessLiving)
				   ) {
					//$html .= '<br/>'.$personLiving;
					$html .= $personLiving;
					$html .= JText::_('JTSETTINGS_EXPTEXT_ALTTEXT').'&nbsp;';
					//$html .= '<span style="color: '.$color.';">'.$row->access_level_alttext .'</span>';		
					$html .= $row->access_level_alttext;		
					
					if (($row->accessLiving != null) and ($row->accessLiving != 0)) {
						$html .= JText::_('JTSETTINGS_EXPTEXT_ALTTEXT2').'&nbsp;';
						//$html .= '<span style="color: '.$color.';">'.$row->access_level_living.'</span>';
						$html .= $row->access_level_living;
					}
				}
				
				$html .= '.';
							
			} else {
				//$html .= '<br/>'.$personLiving.$value.'&nbsp;';
				$html .= $personLiving.$value.'&nbsp;';
				$html .= JText::_('JTSETTINGS_EXPTEXT_FULLYHIDDEN');
			}
		}
		
		return $html;
	}
	
}
?>
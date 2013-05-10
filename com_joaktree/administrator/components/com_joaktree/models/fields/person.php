<?php
/**
 * Joomla! component Joaktree
 * file		person element - person.php
 *
 * @version	1.5.0
 * @author	Niels van Dantzig
 * @package	Joomla
 * @subpackage	Joaktree
 * @license	GNU/GPL
 *
 * Component for genealogy in Joomla!
 *
 */

// no direct access
defined('_JEXEC') or die( 'Restricted access' );
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

jimport('joomla.html.html');
jimport('joomla.form.formfield');
require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'helpers'.DS.'helper.php';

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'tables');

class JFormFieldPerson extends JFormField
{
	protected $type = 'person';

	function getInput()
	{
		$person 		=& JTable::getInstance('joaktree_persons', 'Table');
		if ($this->value) {
			$id = $this->checkValue($this->fieldname, $this->value);
			$person->set('app_id', $id[0]);			
			$person->set('id'    , $id[1]);			
			$person->load();
			
		} else {
			$person->firstName 	= JText::_('JTFIELD_PERSON_SELECTPERSON');
		}

		JHTML::script(JoaktreeHelper::jsfile());
		$apps = JoaktreeHelper::getApplications();		

		$linkTree   = 'index.php?option=com_joaktree&amp;view=jt_trees&amp;layout=element&amp;task=element&amp;tmpl=component&amp;object='.$this->fieldname;
		$linkPerson = 'index.php?option=com_joaktree&amp;view=jt_persons&amp;layout=element&amp;task=element&amp;tmpl=component&amp;object='.$this->fieldname;
		
		JHTML::_('behavior.modal', 'a.modal');
		$html  = "\n".'<br /><br /><div style="clear: both;">';
		$html .= '<input style="background: #ffffff;" type="text" size="50" id="jform_personName" value="'.htmlspecialchars($person->firstName.' '.$person->familyName, ENT_QUOTES, 'UTF-8').'" disabled="disabled" title="'.JText::_('JTFIELD_PERSON_DESC_PERSON').'" />';

		$html .= '<select class="inputbox" id="jform_appTitle" name="apptitle" disabled="disabled">';
		$html .= JHtml::_('select.options', $apps, 'value', 'text', $person->app_id);
		$html .= '</select>';	
		$html .= '</div>';
		
		// buttons
		$html .= "\n".'<div style="clear: both;">';
		
		// button 1
		$html .= '<div class="button2-left">';
		$html .=   '<div class="blank">';
		$html .=   '<a class="modal" title="'.JText::_('JTFIELD_PERSON_BUTTONDESC_TREE').'"  href="'.$linkTree.'" rel="{handler: \'iframe\', size: {x: 650, y: 375}}">'.JText::_('JTFIELD_PERSON_BUTTON_TREE');
		$html .=   '</a>';
		$html .=   '</div>';
		$html .= '</div>'."\n";
		
		// button 2
		$html .= '<div class="button2-left">';
		$html .=   '<div class="blank">';
		$html .=   '<a class="modal" title="'.JText::_('JTFIELD_PERSON_BUTTONDESC_PERSON').'"  href="'.$linkPerson.'" rel="{handler: \'iframe\', size: {x: 650, y: 375}}">'.JText::_('JTFIELD_PERSON_BUTTON_PERSON');
		$html .=   '</a>';
		$html .=   '</div>';
		$html .= '</div>'."\n";
		
		$html .= '</div>';
		
		$html .= "\n".'<input type="hidden" id="jform_personId" name="'.$this->name.'" value="'.$this->value.'" />';
		
		return $html;
	}
	
	private function checkValue($name, $value) {
		static $initCharacters;
		$db	=& JFactory::getDBO();		
		
		if ($name == 'personId') {
			$tmp = explode('!', $value);
			
			if (strlen($tmp[1]) > (int) JoaktreeHelper::getIdlength()) {
				die('wrong request');				
			}
			
			$tmp[0] = (int) $tmp[0];
			$tmp[1] = $db->escape($tmp[1]);
			$retValue = $tmp;
		} else {
			$retValue = $db->escape($value);
			$retValue = (int) $retValue;
		}
		
		return $retValue;
	}
	
}

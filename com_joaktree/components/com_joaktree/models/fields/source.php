<?php
/**
 * Joomla! component Joaktree
 * file		source field - source.php
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

//jimport('joomla.html.html');
jimport('joomla.form.formfield');
//require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'helpers'.DS.'helper.php';

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'tables');

class JFormFieldSource extends JFormField
{
	protected $type = 'source';

	function getInput()
	{
		// Initialize variables.
		$html = array();
		
		// Initialize some field attributes.
		$attr = $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
				
		// Load the current record if available.
		$table = JTable::getInstance('joaktree_sources', 'Table');
		if ($this->value) {
			$id = explode('!', $this->value);
			$appId = $id[0];
			
			if (count($id) == 2) {
				$table->set('app_id', $appId);			
				$table->set('id'    , $id[1]);			
				$table->load();
			}
		} 

		// set up the magic between javascript and php
		if (isset($id[1])) {
			// php - for fields which are present while loading the form
			$iframe = '\'iframe\'';
			$counter = $this->form->getValue('counter');
			
			// Load the modal behavior script.
			JHtml::_('behavior.modal', 'a.modal_src_'.$counter);
			
			$link =  'index.php?option=com_joaktree'
					.'&amp;view=sources'
					.'&amp;tmpl=component'
					.'&amp;appId='.$appId
					.'&amp;action=select'
					.'&amp;counter='.$counter;
		} else {
			// javascript - for fields while adding a new row to the form (after loading).
			$iframe  = '\\\'iframe\\\'';
			$counter = '\'+orderNumber+\'';
			$link =  'index.php?option=com_joaktree'
					.'&amp;view=sources'
					.'&amp;tmpl=component'
					.'&amp;appId='.$appId
					.'&amp;action=select';
		}
			
		// Create a dummy text field with the source title.
		$html[] = '<div >';
		$html[] = '	<input type="text" id="src_'.$counter.'_name"' .
					' value="'.htmlspecialchars($table->title, ENT_COMPAT, 'UTF-8').'"' .
					' disabled="disabled"'.$attr.' />';
		
		// Create the select and clear buttons.
		if ($this->element['readonly'] != 'true') {
			$html[] = '<div class="jt-clearfix"></div>';
			// empty label for layout
			$html[] = '<label>&nbsp;</label>';
	
			// button 1		
			$html[] = '		<a id="modalid_'.$counter.'" class="modal_src_'.$counter.' jt-button-closed jt-buttonlabel" title="'.JText::_('JTSELECT').'"' .
							' href="'.$link.'"' .
							' rel="{handler: '.$iframe.', size: {x: 800, y: 500}}">';
			$html[] = '			'.JText::_('JTSELECT').'</a>';
		}
		
		// Create the real field, hidden, that stored the user id.
		$html[] = ' <input type="hidden" '
						 .'id="src_'.$counter.'_id" '
						 .'name="'.$this->name.'" '
						 .'value="'.(isset($id[1])?$id[1]:null).'" '
						 .$attr
					.' />';
		
		$html[] = '</div>';
		return implode("\n", $html);	
	}
	
}

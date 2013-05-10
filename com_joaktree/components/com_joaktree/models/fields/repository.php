<?php
/**
 * Joomla! component Joaktree
 * file		repository field - repository.php
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

class JFormFieldRepository extends JFormField
{
	protected $type = 'repository';

	function getInput()
	{
		// Initialize variables.
		$html = array();

		// Initialize some field attributes.
		$attr = $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		
		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal_'.$this->id);
		
		// Build the script.
		$script = array();
		$script[] = '	function jtSelectRepo_'.$this->id.'(id, title) {';
		$script[] = '		var old_id = document.getElementById("'.$this->id.'_id").value;';
		$script[] = '		if (old_id != id) {';
		$script[] = '			document.getElementById("'.$this->id.'_id").value = id;';
		$script[] = '			document.getElementById("'.$this->id.'_name").value = title;';
		$script[] = '		}';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';
		$script[] = '	function jtClearRepo_'.$this->id.'() {';
		$script[] = '		document.getElementById("'.$this->id.'_id").value = null;';
		$script[] = '		document.getElementById("'.$this->id.'_name").value = null;';
		$script[] = '	}';
		
		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
//		JHTML::script(JoaktreeHelper::jsfile());
		
		// Load the current record if available.
		$table = JTable::getInstance('joaktree_repositories', 'Table');
		if ($this->value) {
			$id = explode('!', $this->value);
			$appId = $id[0];
			
			if (count($id) == 2) {
				$table->set('app_id', $appId);			
				$table->set('id'    , $id[1]);			
				$table->load();
			}
		} 
				
		// Create a dummy text field with the repository name.
		$html[] = '<div >';
		$html[] = '	<input type="text" id="'.$this->id.'_name"' .
					' value="'.htmlspecialchars($table->name, ENT_COMPAT, 'UTF-8').'"' .
					' disabled="disabled"'.$attr.' />';
		
		// Create the select and clear buttons.
		if ($this->element['readonly'] != 'true') {
			$html[] = '<div class="jt-clearfix"></div>';
			// empty label for layout
			$html[] = '<label>&nbsp;</label>';
	
			// button 1			
			$link = 'index.php?option=com_joaktree&amp;view=repositories&amp;tmpl=component&amp;appId='.$appId.'&amp;action=select';
			$html[] = '		<a class="modal_'.$this->id.' jt-button-closed jt-buttonlabel" title="'.JText::_('JTSELECT').'"' .
							' href="'.$link.'"' .
							' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
			$html[] = '			'.JText::_('JTSELECT').'</a>';

			// button 2
			$html[] = '		<a class="jt-button-closed jt-buttonlabel" title="'.JText::_('JTCLEAR').'"' .
							' href="#"' .
							' onclick="jtClearRepo_'.$this->id.'()" >';
			$html[] = '			'.JText::_('JTCLEAR').'</a>';
		}
		
		// Create the real field, hidden, that stored the user id.
		$html[] = ' <input type="hidden" id="'.$this->id.'_id" name="'.$this->name.'" value="'.$this->value.'" />';
		
		$html[] = '</div>';
		return implode("\n", $html);	
	}
	
}

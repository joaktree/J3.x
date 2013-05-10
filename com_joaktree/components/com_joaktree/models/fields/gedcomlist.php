<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Supports an SQL select list of menu
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldGedcomList extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'GedcomList';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 * @since   11.1
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';
		$gedcomtype = $this->element['gedcom'];
		$indLiving = $this->form->getValue('living', 'person');
		if (!isset($indLiving)) { $indLiving = 1; }

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
			$attr .= ' disabled="disabled"';
		}

		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		// Get the field options.
		$options = (array) $this->getOptions($gedcomtype, $indLiving);

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true') {
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
		}
		// Create a regular list.
		else {
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		}

		return implode($html);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 * @since   11.1
	 */
	protected function getOptions($gedcomtype, $indLiving)
	{
		// Possible gedcom-types are: person, name, relation
		
		// Initialize variables.
		$options = array();
		$levels  = JoaktreeHelper::getUserAccessLevels();

		// Get the database object.
		$db = JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select(' code ');
		$query->from(  ' #__joaktree_display_settings ');
		$query->where( ' level = '.$db->quote($gedcomtype).' ');
		$query->where( ' published = true ');
		$query->where( ' code NOT IN ('
							.$db->quote('NAME').', '
							.$db->quote('NOTE').', '
							.$db->quote('ENOT').', '
							.$db->quote('SOUR').', '
							.$db->quote('ESOU')
							.') ');
		if ($indLiving == true) {
			$query->where( ' accessLiving IN '.$levels.' ');
		} else {
			$query->where( ' access IN '.$levels.' ');
		}
		
		// Set the query and get the result list.
		$db->setQuery($query);
		$items = $db->loadObjectlist();	

		// Check for an error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
			return $options;
		}

		// Build the field options.
		if (!empty($items)) {
			foreach($items as $item) {
				$options[] = JHtml::_('select.option', $item->code, JText::_($item->code));
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}

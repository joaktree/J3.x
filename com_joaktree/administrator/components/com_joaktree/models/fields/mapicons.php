<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Platform.
 * Supports a generic list of options.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldMapicons extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Mapicons';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html 	= array();
		$script = array();
		
		$script[] = "<script type=\"text/javascript\">";
		$script[] = "function jt_mapicons_toggle() { ";
		$script[] = "  var El = $('".$this->id."'); ";
		$script[] = "  if (El.hasClass('jt-map-sprite-0')) { El.removeClass('jt-map-sprite-0'); } ";
		$script[] = "  if (El.hasClass('jt-map-sprite-1')) { El.removeClass('jt-map-sprite-1'); } ";
		$script[] = "  if (El.hasClass('jt-map-sprite-2')) { El.removeClass('jt-map-sprite-2'); } ";
		$script[] = "  if (El.hasClass('jt-map-sprite-3')) { El.removeClass('jt-map-sprite-3'); } ";
		$script[] = "  if (El.hasClass('jt-map-sprite-4')) { El.removeClass('jt-map-sprite-4'); } ";
		$script[] = "  if (El.hasClass('jt-map-sprite-5')) { El.removeClass('jt-map-sprite-5'); } ";
		$script[] = "  if (El.hasClass('jt-map-sprite-6')) { El.removeClass('jt-map-sprite-6'); } ";
		$script[] = "  if (El.hasClass('jt-map-sprite-7')) { El.removeClass('jt-map-sprite-7'); } ";
		$script[] = "  El.addClass('jt-map-sprite-' + El.value); ";

		// Code for the chosen selection ... doesn't work well ..
		//		$script[] = "  var El_chzn = $('".$this->id."_chzn'); ";
		//		$script[] = "  var El2 = El_chzn.getElement('span'); ";
		//		
		//		$script[] = "  if (El2.hasClass('jt-map-sprite-0')) { El2.removeClass('jt-map-sprite-0'); } ";
		//		$script[] = "  if (El2.hasClass('jt-map-sprite-1')) { El2.removeClass('jt-map-sprite-1'); } ";
		//		$script[] = "  if (El2.hasClass('jt-map-sprite-2')) { El2.removeClass('jt-map-sprite-2'); } ";
		//		$script[] = "  if (El2.hasClass('jt-map-sprite-3')) { El2.removeClass('jt-map-sprite-3'); } ";
		//		$script[] = "  if (El2.hasClass('jt-map-sprite-4')) { El2.removeClass('jt-map-sprite-4'); } ";
		//		$script[] = "  if (El2.hasClass('jt-map-sprite-5')) { El2.removeClass('jt-map-sprite-5'); } ";
		//		$script[] = "  if (El2.hasClass('jt-map-sprite-6')) { El2.removeClass('jt-map-sprite-6'); } ";
		//		$script[] = "  if (El2.hasClass('jt-map-sprite-7')) { El2.removeClass('jt-map-sprite-7'); } ";
		//		$script[] = "  El2.addClass('jt-map-sprite-' + El.value); ";
		//		$script[] = "  El2.set('styles', {width: '245px'}); ";	
		//
			
		$script[] = "} ";
		$script[] = "</script>";
		$script[] = "";
		
		$class  = 'jt-map-sprite-'.(int) $this->value;
		$class .= ($this->element['class'] ? (string) ' '.$this->element['class'] : '');

		// Get the field options.
		$html[] = '<select '
				 .($this->element['size'] ? ' size="' . (int) $this->element['size'] . '" ' : '')
				 .'class="'.$class.'" '
				 .'name="'.$this->name.'" '
				 .'id="'.$this->id.'" '
				 .'style="height: 32px;" '
				 .'onchange="jt_mapicons_toggle();" >';
				 
		for ($i=0; $i<8; $i++) {
			$html[] = '<option '
					 .'value="'.$i.'" ' 
					 .(((int) $this->value == $i) ? 'selected="selected" ': '').' '
					 .'class="jt-map-sprite-'.$i.'" '
					 .'>&nbsp;</option>';
		}
		
		$html[] = '</select>';		

		return implode("\n", array_merge($script, $html));
	}
}

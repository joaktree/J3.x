<?php
/**
 * Joomla! component Joaktree
 * file		jt_application modelAdmin - jt_application.php
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

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class JoaktreeModelJt_application extends JModelAdmin
{	
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'joaktree_applications', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_joaktree.application', 'jt_application', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {		
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_joaktree.edit.application.data', array());

		if (empty($data)) {		
			$data = $this->getItem();
		}

		return $data;
	}
		
	public function save($form) {
		$canDo	= JoaktreeHelper::getActions();
		$msg = JText::_('JTAPPS_MESSAGE_NOSAVE');
		
		if ($canDo->get('core.create') || $canDo->get('core.edit')) {						
			$ret = parent::save($form);

			if ($ret) {
				$msg = JText::_('JT_MESSAGE_SAVED');
			}
			
			// Bind the rules.
			if (isset($form['rules'])) {			
				$actions = array();
				$tmp 	 = array();
				$tmp[0]  = '';
				
				foreach ($data['rules'] as $action => $identities) {
					$identities = array_diff($identities, $tmp);
					$actions[$action] = $identities;					
				}
			
				$table = $this->getTable();
				$rules = new JRules($actions);		
				$table->setRules($rules);
			}
			
		}
		
		return $msg;
	}
}

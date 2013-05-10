<?php
/**
 * Joomla! component Joaktree
 * file		jt_location modelAdmin - jt_location.php
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
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'services'.DS.'geocode.php';

class JoaktreeModelJt_location extends JModelAdmin
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
	public function getTable($type = 'joaktree_locations', $prefix = 'Table', $config = array())
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
		$form = $this->loadForm('com_joaktree.location', 'jt_location', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_joaktree.edit.location.data', array());

		if (empty($data)) {		
			$data = $this->getItem();
			$data->resultValue2 = $data->resultValue;
		}

		return $data;
	}
	
	public function getGeocodeResultSet() {
		$settings 		= MBJServiceGeocode::getKeys();
		
		if (isset($settings->geocode)) {
			$geocodeAPIkey	= $settings->geocode.'APIkey';
			
			if (  (empty($settings->geocode)) 
			   || ((!empty($settings->geocode)) && isset($settings->$geocodeAPIkey) && empty($settings->$geocodeAPIkey))		 
			   ) {
			   	// we cannot execute geocode search
			   	$resultSet = array();
			   	
			} else {
				$data 		= $this->loadFormData();
				$service 	= MBJServiceGeocode::getInstance();	
				$status 	= $service->_('findLocation', $data);
				$resultSet 	= $service->_('getResultSet');
			}
		} else {
			$resultSet = array();
		}
		
		return $resultSet;
	}
	
	public function save($data) {
		$canDo	= JoaktreeHelper::getActions();
		$msg = JText::_('JTAPPS_MESSAGE_NOSAVE');
		
		if ($canDo->get('core.create') || $canDo->get('core.edit')) {
			// Initialise variables;
			$table = $this->getTable();
			$key = $table->getKeyName();
			$pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
			$isNew = ($pk > 0) ? false : true;
			
			// Allow an exception to be thrown.
			try {				
				// Bind the data.
				if (!$table->bind($data)) {
					$this->setError($table->getError());
					return $msg;
				}			
									
				// Store the data.
				if (!$table->store(true)) {
					$this->setError($table->getError());
					return $msg;
				}
				
				// Clean the cache.
				$this->cleanCache();
	
			}
			catch (Exception $e) {
				$this->setError($e->getMessage());
				return $msg;
			}
	
			$pkName = $table->getKeyName();
			if (isset($table->$pkName)) {
				$this->setState($this->getName() . '.id', $table->$pkName);
			}
			$this->setState($this->getName() . '.new', $isNew);
	
			return JText::_('JT_MESSAGE_SAVED');
		}
		
		return $msg;
	}
}

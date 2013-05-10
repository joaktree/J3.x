<?php
/**
 * Joomla! component Joaktree
 * file		jt_tree model - jt_tree.php
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
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.modeladmin');
require_once JPATH_COMPONENT.DS.'helpers'.DS.'jt_trees.php';

class JoaktreeModelJt_tree extends JModelAdmin {

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'joaktree_trees', $prefix = 'Table', $config = array())
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
		$form = $this->loadForm('com_joaktree.tree', 'jt_tree', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_joaktree.edit.tree.data', array());

		if (empty($data)) {
			$data = $this->getItem();		
		}

		return $data;
	}
		
	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk	The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 * @since   11.1
	 */
	public function getItem($pk = null)
	{
		
		// Initialise variables.
		$pk		= (!empty($pk)) ? $pk : (int) $this->getState($this->getName().'.id');

		if ($pk > 0) {
			// Attempt to load the row.
			$query = $this->_buildQuery($pk);
			$this->_db->setQuery($query);			
			$item = $this->_db->loadObject();
			
			// Check for a error.
			if ($error = $this->_db->getErrorMsg()) {
				throw new JException($error);
			}			

			if (is_object($item) && property_exists($item, 'params')) {
				$registry = new JRegistry;
				$registry->loadString($item->params, 'JSON');
				$item->params = $registry->toArray();
			}
			
			return $item;
		} else {
			return false;	
		}
	}
	
	public function save($data) {
		$canDo	= JoaktreeHelper::getActions();
		
		if ($canDo->get('core.create') || $canDo->get('core.edit')) {
			// initialize tables and records
			$table		= $this->getTable();
			$msg	= '';
						
			// Bind the form fields to the table
			$table->id             	= $data['id'];
			$table->app_id         	= $data['app_id'];
			$table->holds          	= $data['holds'];
			$table->root_person_id 	= $data['root_person_id'];
			$table->access		 	= $data['access'];
			$table->name           	= $data['name'];
			$table->theme_id       	= $data['theme_id'];
			$table->indGendex      	= $data['indGendex'];
			$table->indPersonCount	= $data['indPersonCount'];
			$table->indMarriageCount	= $data['indMarriageCount'];
			$table->robots      	= (empty($data['robots'])) ? null : $data['robots'];
			$table->catid        	= $data['catid'];
			
			if (!$table->bind($data)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			// Make sure the table is valid
			if (!$table->check()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			// Bind the rules.
			if (isset($data['rules'])) {			
				$actions = array();
				$tmp 	 = array();
				$tmp[0]  = '';
				
				foreach ($data['rules'] as $action => $identities) {
					$identities = array_diff($identities, $tmp);
					$actions[$action] = $identities;					
				}
			
				$rules = new JRules($actions);		
				$table->setRules($rules);
			}
			
			// Store the table to the database
			if (!$table->store()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			$msg .= 'Tree stored: ' . $table->name;			
			$return = $msg;
		
		} else {
			$return = JText::_('JT_NOTAUTHORISED');
		}
		
		return $return;
	}
	
	private function _buildQuery($pk)
	{
		$query = $this->_db->getQuery(true);
		$query->select(' jte.* ');
		$query->from(  ' #__joaktree_trees        jte ');
		$query->where( ' jte.id = '.(int) $pk.' ');
		
		$query->select(  ' japp.title AS appTitle ');
		$query->leftJoin(' #__joaktree_applications japp '
						.' ON japp.id = jte.app_id ');
						
		$query->select(' IFNULL( CONCAT_WS('.$this->_db->Quote(' ').' '
				      .'                  , jpn.firstName '
					  .'                  , jpn.namePreposition '
					  .'                  , jpn.familyName '
					  .'                  ) '
					  .'        , '.$this->_db->Quote(JText::_('JTFIELD_PERSON_BUTTON_PERSON')).' '
					  .'        )    AS rootPersonName ');
		$query->leftJoin(' #__joaktree_persons      jpn '
						.' ON (   jpn.app_id = jte.app_id '
						.'    AND jpn.id     = jte.root_person_id '
						.'    ) ');

		return $query;
	}
}
?>
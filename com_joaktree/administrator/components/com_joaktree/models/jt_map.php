<?php
/**
 * Joomla! component Joaktree
 * file		jt_map modelAdmin - jt_map.php
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

class JoaktreeModelJt_map extends JModelAdmin
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
	public function getTable($type = 'joaktree_maps', $prefix = 'Table', $config = array())
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
		$form = $this->loadForm('com_joaktree.map', 'jt_map', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_joaktree.edit.map.data', array());

		if (empty($data)) {		
			$data = $this->getItem();
		}

		return $data;
	}
	
	public function getItem($pk = null)	{
		$item = parent::getItem($pk);
		
		$item->personName = (!empty($item->person_id)) 
							 ? $this->getPersonName($item->app_id, $item->person_id)
							 : '';
							 
		$item->descendants 		= ($item->selection == 'tree') ? $item->relations : 0;
		$item->person_relations = ($item->selection == 'person') ? $item->relations : 0;		
		
        $person_event   	  = (array) json_decode($item->excludePersonEvents);						 
        $relation_event 	  = (array) json_decode($item->excludeRelationEvents);
        $item->events		  = $this->getIncludeEvents(array_merge($person_event, $relation_event));						 
        
		return $item;
	}
	
	private function getIncludeEvents($excludeCodes = array()) {
		$query = $this->_db->getQuery(true);
		
		if (count($excludeCodes)) {
			$exclude = "('NOTE','ENOT','SOUR','ESOU','".implode("','", $excludeCodes)."') ";
		} else {
			$exclude = "('NOTE','ENOT','SOUR','ESOU')";
		}
		
		$query->select(' code ');
		$query->from(  ' #__joaktree_display_settings ');
		$query->where( ' level IN ( '.$this->_db->quote('person').', '.$this->_db->quote('relation').') ');
		$query->where( ' published = true ');
		$query->where( ' code NOT IN '.$exclude.' ');
		
		$this->_db->setQuery($query);
		$includes = $this->_db->loadColumn();
		
		return $includes;
	}
	
	private function getPersonName($app_id, $person_id) {
		$query = $this->_db->getQuery(true);
		
		$query->select(' IFNULL( CONCAT_WS('.$this->_db->Quote(' ').' '
				      .'                  , jpn.firstName '
					  .'                  , jpn.namePreposition '
					  .'                  , jpn.familyName '
					  .'                  ) '
					  .'        , '.$this->_db->Quote(JText::_('JTFIELD_PERSON_BUTTON_PERSON')).' '
					  .'        )    AS personName ');
		$query->from(  ' #__joaktree_persons      jpn ');
		$query->where( ' jpn.app_id = '.$app_id.' ');
		$query->where( ' jpn.id     = '.$this->_db->Quote($person_id).' ');
		
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();

		return $result;
	}
	
	public function save($form) {
		$canDo	= JoaktreeHelper::getActions();
		$msg = JText::_('JTAPPS_MESSAGE_NOSAVE');
		
		if ($canDo->get('core.create') || $canDo->get('core.edit')) {
			if (($form['selection'] == 'tree') || ($form['selection'] == 'location')) {
				$form['app_id']  	= $this->getAppId($form['tree']);
				$form['tree_id'] 	= $form['tree'];
				$form['subject']    = $form['familyName'];
				$form['relations']	= $form['descendants'];
				unset($form['tree']);
				unset($form['familyName']);
				unset($form['personName']);
				unset($form['root_person_id']);
				unset($form['descendants']);
				unset($form['person_relations']);
			}
			
			if ($form['selection'] == 'person') {
				$form['tree_id']   	= $form['tree'];
				$form['person_id'] 	= $form['root_person_id'];
				$form['relations']	= $form['person_relations'];
				unset($form['tree']);
				unset($form['personName']);
				unset($form['root_person_id']);
				unset($form['familyName']);
				unset($form['descendants']);
				unset($form['person_relations']);
			}
						
			$form['excludePersonEvents']   = $this->getExcludeEvents('person', $form['events']);
			$form['excludeRelationEvents'] = $this->getExcludeEvents('relation', $form['events']);
			unset($form['events']);		
			
			$ret = parent::save($form);
			
			if ($ret) {
				$msg = JText::_('JT_MESSAGE_SAVED');
			}
		}
		
		return $msg;
	}
	
	private function getExcludeEvents($type, $includeCodes = array()) {
		$query = $this->_db->getQuery(true);
		
		if (count($includeCodes)) {
			$exclude = "('NOTE','ENOT','SOUR','ESOU','".implode("','", $includeCodes)."') ";
		} else {
			$exclude = "('NOTE','ENOT','SOUR','ESOU')";
		}
		
		$query->select(' code ');
		$query->from(  ' #__joaktree_display_settings ');
		$query->where( ' level = '.$this->_db->quote($type).' ');
		$query->where( ' published = true ');
		$query->where( ' code NOT IN '.$exclude.' ');
		
		$this->_db->setQuery($query);
		$excludes = $this->_db->loadColumn();
		
		return json_encode($excludes);
	}
	
	private function getAppId($tree_id) {
		$query = $this->_db->getQuery(true);
		$query->select(' jte.app_id ');
		$query->from(  ' #__joaktree_trees  jte ');
		$query->where( ' jte.id = '.(int) $tree_id.' ');
		
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();
		
		return $result;
	}
}

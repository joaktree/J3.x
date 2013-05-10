<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_relation_events.php
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

defined('_JEXEC') or die('Restricted access');
jimport('joomla.filter.input');

require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'tables'.DS.'JMFPKtable.php';
require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'tables'.DS.'joaktree_locations.php';

class TableJoaktree_relation_events extends JMFPKTable
{
	var $app_id			= null; // PK
	var $person_id_1	= null; // PK
	var $person_id_2	= null; // PK
	var $orderNumber	= null; // PK
	var $code			= null;
	var $indNote		= null;
	var $indCitation	= null;
	var $type			= null;
	var $eventDate		= null;
	var $loc_id			= null;
	var $location		= null;
	var $value			= null;

	function __construct( &$db) {
		$pk = array('app_id', 'person_id_1', 'person_id_2', 'orderNumber');
		parent::__construct('#__joaktree_relation_events', $pk, $db);
	}

	function deletePersonEvents($person_id) {
		if ($person_id == null) {
			return false;
		} else {
			$query = $this->_db->getQuery(true);
			$query->delete(' '.$this->_db->quoteName($this->_tbl).' ');
			$query->where( ' app_id = '.(int) $this->app_id.' ');
			$query->where( ' (  person_id_1 = '.$this->_db->quote($person_id).' '
						 .'  OR person_id_2 = '.$this->_db->quote($person_id).' '
						 .'  ) '
						 );
			
			$this->_db->setQuery( $query );
			$result = $this->_db->query();       
		}

		if ($result) {
			return true;
		} else {
			return $this->setError($this->_db->getErrorMsg());
		}
	}

	function deleteEvents($person_id_1, $person_id_2) {
		if (  ($person_id_1 == null) 
		   or ($person_id_2 == null) 
		   or ($person_id_1 == $person_id_2) ) {
			return false;
		} else {
			if ($person_id_1 < $person_id_2) {
				$pid1 = $person_id_1;
				$pid2 = $person_id_2;
			} else {
				$pid1 = $person_id_2;
				$pid2 = $person_id_1;				
			}

			$query = $this->_db->getQuery(true);
			$query->delete(' '.$this->_db->quoteName($this->_tbl).' ');
			$query->where( ' app_id = '.(int) $this->app_id.' ');
			$query->where( ' person_id_1 = '.$this->_db->quote($pid1).' ');
			$query->where( ' person_id_2 = '.$this->_db->quote($pid2).' ');
			
			$this->_db->setQuery( $query );
			$result = $this->_db->query();       
		}

		if ($result) {
			return true;
		} else {
			return $this->setError($this->_db->getErrorMsg());
		}
	}
	
	function truncateApp($app_id) {
		$query = $this->_db->getQuery(true);
		$query->delete(' '.$this->_db->quoteName($this->_tbl).' ');
		$query->where( ' app_id = '.(int) $app_id.' ');
		
		$this->_db->setQuery( $query );
		$result = $this->_db->query();       

		if ($result) {
			return true;
		} else {
			return $this->setError($this->_db->getErrorMsg());
		}
	}
	
	public function check() {
		// mandatory fields
		if (empty($this->app_id)) {
			return false;
		}
		if (empty($this->person_id_1)) {
			return false;
		}
		if (empty($this->person_id_2)) {
			return false;
		}
		if (empty($this->orderNumber)) {
			return false;
		}
		if (empty($this->code)) {
			return false;
		}
		
		if (!$this->checkLocation()) {
			return false;
		}
		
		if (!$this->checkNotesAndReferences()) {
			return false;
		}
		
		return true;
	}

	public function checkLocation() {	
		// check for locations
		$this->loc_id = TableJoaktree_locations::checkLocation($this->location);	
		return true;
	}
	
	private function checkNotesAndReferences() {	
		// check for citations
		$query = $this->_db->getQuery(true);
		$query->select(' COUNT(jcn.objectOrderNumber) AS indCit ');
		$query->from(  ' #__joaktree_citations jcn ');
		$query->where( ' jcn.objectType  = '.$this->_db->quote('relationEvent').' ');
		$query->where( ' jcn.objectOrderNumber = '.$this->orderNumber.' ');
		$query->where( ' jcn.app_id      = '.$this->app_id.' ');
		$query->where( ' jcn.person_id_1 IN ('
							 .$this->_db->quote($this->person_id_1)
						.', '.$this->_db->quote($this->person_id_2)
						.') '
					  );
		$query->where( ' jcn.person_id_2 IN ('
							 .$this->_db->quote($this->person_id_1)
						.', '.$this->_db->quote($this->person_id_2)
						.') '
					  );
					  
		$this->_db->setQuery( $query );
		$result = $this->_db->loadResult();  
		$this->indCitation = ($result) ? true : false;

		// check for notes
		$query->clear();
		$query->select(' COUNT(jre.orderNumber) AS indNot ');
		$query->from(  ' #__joaktree_relation_notes  jre ');
		$query->where( ' jre.app_id     = '.$this->app_id.' ');
		$query->where( ' jre.eventOrderNumber  = '.$this->orderNumber.' ');
		$query->where( ' jre.person_id_1 IN ('
							 .$this->_db->quote($this->person_id_1)
						.', '.$this->_db->quote($this->person_id_2)
						.') '
					  );
		$query->where( ' jre.person_id_2 IN ('
							 .$this->_db->quote($this->person_id_1)
						.', '.$this->_db->quote($this->person_id_2)
						.') '
					  );
		
		$this->_db->setQuery( $query );
		$result = $this->_db->loadResult();  
		$this->indNote = ($result) ? true : false;
		
		return true;
	}
	
	public function delete() {
		// delete citations
		$query = $this->_db->getQuery(true);
		$query->delete(' #__joaktree_citations ');
		$query->where( ' objectType  = '.$this->_db->quote('relationEvent').' ');
		$query->where( ' objectOrderNumber = '.$this->orderNumber.' ');
		$query->where( ' app_id      = '.$this->app_id.' ');
		$query->where( ' person_id_1 IN ('
							 .$this->_db->quote($this->person_id_1)
						.', '.$this->_db->quote($this->person_id_2)
						.') '
					  );
		$query->where( ' person_id_2 IN ('
							 .$this->_db->quote($this->person_id_1)
						.', '.$this->_db->quote($this->person_id_2)
						.') '
					  );
		
		$this->_db->setQuery( $query );
		$result = $this->_db->query(); 
		      
		// deletenotes
		$query->clear();
		$query->delete(' #__joaktree_relation_notes  ');
		$query->where( ' app_id     = '.$this->app_id.' ');
		$query->where( ' eventOrderNumber  = '.$this->orderNumber.' ');
		$query->where( ' person_id_1 IN ('
							 .$this->_db->quote($this->person_id_1)
						.', '.$this->_db->quote($this->person_id_2)
						.') '
					  );
		$query->where( ' person_id_2 IN ('
							 .$this->_db->quote($this->person_id_1)
						.', '.$this->_db->quote($this->person_id_2)
						.') '
					  );
		
		$this->_db->setQuery( $query );
		$result = $this->_db->query();
		 
		// ready to delete
		$ret = parent::delete();
		return $ret;
	}
}
?>
<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_person_notes.php
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

class TableJoaktree_person_notes extends JMFPKTable
{
	var $app_id			= null; // PK
	var $person_id		= null; // PK
	var $orderNumber	= null; // PK
	var $indCitation	= null;
	var $nameOrderNumber	= null;
	var $eventOrderNumber	= null;
	var $note_id	= null;
	var $value		= null;

	function __construct( &$db) {
		$pk = array('app_id', 'person_id', 'orderNumber');
		parent::__construct('#__joaktree_person_notes', $pk, $db);
	}

	function deleteNotes($person_id) {
		if ($person_id == null) {
			return false;
		} else {
			$query = $this->_db->getQuery(true);
			$query->delete(' '.$this->_db->quoteName($this->_tbl).' ');
			$query->where( ' app_id    = '.$this->app_id.' ');
			$query->where( ' person_id = '.$this->_db->quote($person_id).' ');
			
			$this->_db->setQuery( $query );
			$result = $this->_db->query();       
		}

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
		if (empty($this->person_id)) {
			return false;
		}
		if (empty($this->orderNumber)) {
			return false;
		}
		
		// both order numbers cannot be used simultanously
		if ((isset($this->nameOrderNumber)) && (isset($this->eventOrderNumber))) {
			return false;
		}
		
		if (!$this->checkNotesAndReferences()) {
			return false;
		}
		
		
		return true;
	}

	private function checkNotesAndReferences() {	
		// check for citations
		$query = $this->_db->getQuery(true);
		$query->select(' COUNT(jcn.objectOrderNumber) AS indCit ');
		$query->from(  ' #__joaktree_citations jcn ');
		$query->where( ' jcn.objectType  = '.$this->_db->quote('personNote').' ');
		$query->where( ' jcn.objectOrderNumber = '.$this->orderNumber.' ');
		$query->where( ' jcn.app_id      = '.$this->app_id.' ');
		$query->where( ' jcn.person_id_1 = '.$this->_db->quote($this->person_id).' ');

		$this->_db->setQuery( $query );
		$result = $this->_db->loadResult();  
		$this->indCitation = ($result) ? true : false;
		
		return true;
	}
	
	public function delete() {
		// delete citations
		$query = $this->_db->getQuery(true);
		$query->delete(' #__joaktree_citations ');
		$query->where( ' objectType  = '.$this->_db->quote('personNote').' ');
		$query->where( ' objectOrderNumber = '.$this->orderNumber.' ');
		$query->where( ' app_id      = '.$this->app_id.' ');
		$query->where( ' person_id_1 = '.$this->_db->quote($this->person_id).' ');
		
		$this->_db->setQuery( $query );
		$result = $this->_db->query(); 
		      
		// ready to delete
		$ret = parent::delete();
		return $ret;
	}
	
}
?>
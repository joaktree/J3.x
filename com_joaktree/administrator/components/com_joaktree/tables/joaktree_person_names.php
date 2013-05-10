<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_person_names.php
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

class TableJoaktree_person_names extends JMFPKTable
{
	var $app_id			= null; // PK
	var $person_id		= null; // PK
	var $orderNumber	= null; // PK
	var $code			= null;
	var $indNote		= null;
	var $indCitation	= null;
	var $eventDate		= null;
	var $value			= null;

	function __construct( &$db) {
		$pk = array('app_id', 'person_id', 'orderNumber');
		parent::__construct('#__joaktree_person_names', $pk, $db);
	}

	public function deleteNames($person_id) {
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
		if (empty($this->code)) {
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
		$query->where( ' jcn.objectType  = '.$this->_db->quote('personName').' ');
		$query->where( ' jcn.objectOrderNumber = '.$this->orderNumber.' ');
		$query->where( ' jcn.app_id      = '.$this->app_id.' ');
		$query->where( ' jcn.person_id_1 = '.$this->_db->quote($this->person_id).' ');

		$this->_db->setQuery( $query );
		$result = $this->_db->loadResult();  
		$this->indCitation = ($result) ? true : false;

		// check for notes
		$query->clear();
		$query->select(' COUNT(jpe.orderNumber) AS indNot ');
		$query->from(  ' #__joaktree_person_notes jpe ');
		$query->where( ' jpe.app_id     = '.$this->app_id.' ');
		$query->where( ' jpe.person_id  = '.$this->_db->quote($this->person_id).' ');
		$query->where( ' jpe.nameOrderNumber  = '.$this->orderNumber.' ');
		
		$this->_db->setQuery( $query );
		$result = $this->_db->loadResult();  
		$this->indNote = ($result) ? true : false;
		
		return true;
	}
	
	public function delete() {
		// delete citations
		$query = $this->_db->getQuery(true);
		$query->delete(' #__joaktree_citations ');
		$query->where( ' objectType  = '.$this->_db->quote('personName').' ');
		$query->where( ' objectOrderNumber = '.$this->orderNumber.' ');
		$query->where( ' app_id      = '.$this->app_id.' ');
		$query->where( ' person_id_1 = '.$this->_db->quote($this->person_id).' ');
		
		$this->_db->setQuery( $query );
		$result = $this->_db->query(); 
		      
		// deletenotes
		$query->clear();
		$query->delete(' #__joaktree_person_notes ');
		$query->where( ' app_id     = '.$this->app_id.' ');
		$query->where( ' person_id  = '.$this->_db->quote($this->person_id).' ');
		$query->where( ' nameOrderNumber  = '.$this->orderNumber.' ');
		
		$this->_db->setQuery( $query );
		$result = $this->_db->query();
		 
		// ready to delete
		$ret = parent::delete();
		return $ret;
	}
	
}
?>
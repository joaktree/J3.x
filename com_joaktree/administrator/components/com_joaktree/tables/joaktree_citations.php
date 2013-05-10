<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_citations.php
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

define("EMPTY_COLUMN", "EMPTY"); 
class TableJoaktree_citations extends JMFPKTable
{
	var $objectType		= null; // PK
	var $objectOrderNumber	= null; // PK
	var $app_id			= null; // PK
	var $person_id_1	= null; // PK
	var $person_id_2	= null; // PK
	var $source_id		= null; // PK
	var $orderNumber	= null; // PK
	var $dataQuality	= null;
	var $page		= null;
	var $quotation		= null;
	var $note		= null;
	
	function __construct( &$db) {
		$pk = array('objectType', 'objectOrderNumber', 'app_id', 'person_id_1', 'person_id_2', 'source_id', 'orderNumber');
		
		parent::__construct('#__joaktree_citations', $pk, $db);
	}

	public function check() {
		// mandatory fields
		if (empty($this->objectType)) {
			return false;
		}
		if (empty($this->objectOrderNumber)) {
			return false;
		}
		if (empty($this->app_id)) {
			return false;
		}
		if (empty($this->person_id_1)) {
			return false;
		}
		if (empty($this->person_id_2)) {
			return false;
		}
		if (empty($this->source_id)) {
			return false;
		}
		if (empty($this->orderNumber)) {
			return false;
		}
		
		return true;
	}
	
	function deletePersonCitations($person_id) {
		if ($person_id == null) {
			return false;
		} else {
			$query = $this->_db->getQuery(true);
			$query->delete(' '.$this->_db->quoteName($this->_tbl).' ');
			$query->where( ' app_id      = '.(int) $this->app_id.' ');
			$query->where( ' person_id_1 = '.$this->_db->quote($person_id).' ');
			$query->where( ' person_id_2 = '.$this->_db->quote(EMPTY_COLUMN).' ');
			
			$this->_db->setQuery( $query );
			$result = $this->_db->query();       
		}
		
		if ($result) {
			return true;
		} else {
			return $this->setError($this->_db->getErrorMsg());
		}
	}
	
	function deleteAllPersonCitations($person_id) {
		if ($person_id == null) {
			return false;
		} else {
			$query = $this->_db->getQuery(true);
			$query->delete(' '.$this->_db->quoteName($this->_tbl).' ');
			$query->where( ' app_id      = '.(int) $this->app_id.' ');
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
	
	function deleteRelationCitations($person_id_1, $person_id_2) {
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
			$query->where( ' app_id      = '.(int) $this->app_id.' ');
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
	
	function truncateRelationCitations($app_id) {		
		$query = $this->_db->getQuery(true);
		$query->delete(' '.$this->_db->quoteName($this->_tbl).' ');
		$query->where( ' app_id      = '.(int) $this->app_id.' ');
		$query->where( ' person_id_2 <> '.$this->_db->quote('EMPTY').' ');
		$this->_db->setQuery( $query );
		$result = $this->_db->query();       
		
		if ($result) {
			return true;
		} else {
			return $this->setError($this->_db->getErrorMsg());
		}
	}
}
?>
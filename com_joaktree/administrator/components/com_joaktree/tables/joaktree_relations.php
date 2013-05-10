<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_relations.php
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

class TableJoaktree_relations extends JMFPKTable
{
	var $app_id			= null; // PK
	var $person_id_1	= null; // PK
	var $person_id_2	= null; // PK
	var $type			= null;
	var $subtype		= null;
	var $family_id		= null;
	var $indNote		= null;
	var $indCitation	= null;
	var $orderNumber_1	= null;
	var $orderNumber_2	= null;

	function __construct( &$db) {
		$pk = array('app_id', 'person_id_1', 'person_id_2');
		parent::__construct('#__joaktree_relations', $pk, $db);
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
		
		return true;
	}
	
	function deletePersonRelations($person_id) {
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
		       // everything went fine
		       return true;
	       } else {
		       // something went wrong -> error messages is returned.
		       return $this->setError($this->_db->getErrorMsg());
	       }
	}

	function deleteRelations($person_id_1, $person_id_2) {
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
		       // everything went fine
		       return true;
	       } else {
		       // something went wrong -> error messages is returned.
		       return $this->setError($this->_db->getErrorMsg());
	       }
	}
	
	function truncTable() {		
		$query = 'TRUNCATE ' . $this->_tbl;
		$this->_db->setQuery( $query );
		$result = $this->_db->query();       
       
		if ($result) {
	    	// everything went fine
			return true;
		} else {
			// something went wrong -> error messages is returned.
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
}
?>
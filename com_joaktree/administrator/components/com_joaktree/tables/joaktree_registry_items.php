<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_trees.php
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

class TableJoaktree_registry_items extends JTable
{
	var $id 		= null;
	var $regkey		= null;
	var $value 		= null;

	function __construct( &$db) {
		parent::__construct('#__joaktree_registry_items', 'id', $db);
	}
	
	public function storeUK() {
		
		if (isset($this->regkey) && isset($this->value)) {
			$query = $this->_db->getQuery(true);
			$query->select(' id ');
			$query->from(  ' '.$this->_tbl.' ');
			$query->where( ' regkey = '.$this->_db->quote($this->regkey).' ');
			
			$this->_db->setQuery($query);			
			$result = $this->_db->loadResult();
						
			if ($result) {
				$this->id = $result;
				$res = $this->store();
			} else {
				$this->id = null;
				$res = $this->store();
			}
		} else {
			$res = false;
		}
		
		return $res;
	}
	
	public function loadUK($uk) {
			$query = $this->_db->getQuery(true);
			$query->select(' * ');
			$query->from(  ' '.$this->_tbl.' ');
			$query->where( ' regkey = '.$this->_db->quote($uk).' ');
			
			$this->_db->setQuery($query);			
			$tmp = $this->_db->loadObject();
			
			if (is_object($tmp)) {
				$this->id     = $tmp->id;
				$this->regkey = $tmp->regkey;
				$this->value  = $tmp->value;
			} else {
				$this->id     = null;
				$this->regkey = null;
				$this->value  = null;
			}
	}
	
	public function deleteUK($uk) {
			$query = $this->_db->getQuery(true);
			$query->delete(  ' '.$this->_tbl.' ');
			$query->where( ' regkey = '.$this->_db->quote($uk).' ');
			
			$this->_db->setQuery($query);			
			$tmp = $this->_db->query();			
	}
	
}
?>

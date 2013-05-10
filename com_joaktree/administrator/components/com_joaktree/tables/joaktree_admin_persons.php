<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_admin_persons.php
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

class TableJoaktree_admin_persons extends JMFPKTable
{
	var $app_id			= null; // PK
	var $id 			= null; // PK
	var $published 		= null;
	var $living			= null;
	var $page			= null;
	var $robots			= null;
	var $map			= null;
	
	function __construct( &$db) {
		$pk = array('app_id', 'id');
		
		parent::__construct('#__joaktree_admin_persons', $pk, $db);
	}
	
	function loadEmpty () {
		$this->id 			= null;
		$this->published 	= null;
		$this->living		= null;
		$this->page 		= null;
		$this->robots 		= null;
	}
	
	public function check() {
		// mandatory fields
		if (empty($this->app_id)) {
			return false;
		}
		if (empty($this->id)) {
			return false;
		}
				
		return true;
	}
	
	function person_exists() {
		// check whether person exists in admin table
		$query = $this->_db->getQuery(true);
		
		$query->select(' 1 ');
		$query->from(  ' '.$this->_tbl.' ');
		$query->where( ' app_id = '.$this->app_id.' ');
		$query->where( ' id     = '.$this->_db->Quote( $this->id ).' ');
		
		// execute query and retrieve result
		$this->_db->setQuery( $query );
		$result = $this->_db->loadResult();
		
		// if query has no result, record does not exists yet: function returns FALSE
		// if query has result, record exists yet: function returns TRUE
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	
	function insert() {
		$ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );		
		return $ret;	
	}
}
?>
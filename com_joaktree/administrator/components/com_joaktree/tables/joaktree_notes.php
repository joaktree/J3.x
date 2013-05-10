<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_notes.php
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

class TableJoaktree_notes extends JMFPKTable
{
	var $app_id		= null; // PK
	var $id			= null; // PK
	var $value		= null;

	function __construct( &$db) {
		$pk = array('app_id', 'id');
		parent::__construct('#__joaktree_notes', $pk, $db);
	}

	function loadEmpty() {
		$this->value		= null;
	}
	
	function insert() {
		$ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
		return $ret;
	}
	
	function update() {
		$ret = $this->_db->updateObject( $this->_tbl, $this, $this->_tbl_key ); 
		return $ret;
	}
}
?>
<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_repositories.php
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

class TableJoaktree_repositories extends JMFPKTable
{
	var $app_id		= null; // PK
	var $id 		= null; // PK
	var $name 		= null;
	var $website	= null;

	function __construct( &$db) {
		$pk = array('app_id', 'id');
		parent::__construct('#__joaktree_repositories', $pk, $db);
	}

	function loadEmpty() {
		$this->id 	= null;
		$this->name 	= null;
		$this->website	= null;
	}
	
	function insert() {
		$ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
		return $ret;
	}
	
	function update() {
		$ret = $this->_db->updateObject( $this->_tbl, $this, $this->_tbl_key, true );
		return $ret;
	}
	
	function check()
	{
		jimport('joomla.filter.output');
	
		// primary key is mandatory
		if (empty($this->app_id)) {
			return false;
		}
		if (empty($this->id)) {
			return false;
		}
		
		// Set name - name is mandatory
		$this->name = htmlspecialchars_decode($this->name, ENT_QUOTES);
		if (empty($this->name)) {
			return false;
		}

		// Set website
		$this->website = htmlspecialchars_decode($this->website, ENT_QUOTES);

		return true;
	}
	
}
?>
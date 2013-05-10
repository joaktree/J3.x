<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_gedcom_objects.php
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

class TableJoaktree_gedcom_objects extends JTable
{
	var $id 	= null;
	var $tag	= null;
	var $value	= null;

	function __construct( &$db) {
		parent::__construct('#__joaktree_gedcom_objects', 'id', $db);
	}

	function truncate() {
		$query = 'TRUNCATE ' . $this->_tbl;
		$this->_db->setQuery( $query );
		$result = $this->_db->loadResult();
		return $result;
	}
}
?>
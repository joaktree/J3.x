<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_gedcom_objectlines.php
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

class TableJoaktree_gedcom_objectlines extends JTable
{
	var $id 		= null;
	var $object_id	= null;
	var $order_nr	= null;
	var $level		= null;
	var $tag		= null;
	var $value 		= null;
	var $subtype	= null;

	function __construct( &$db) {
		parent::__construct('#__joaktree_gedcom_objectlines', 'id', $db);
	}

	function truncate() {
            $query = 'TRUNCATE ' . $this->_tbl;
            $this->_db->setQuery( $query );
            $result = $this->_db->loadResult();
            return $result;
	}
}
?>


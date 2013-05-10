<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_person_documents.php
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

class TableJoaktree_person_documents extends JMFPKTable
{
	var $app_id			= null; // PK
	var $person_id		= null; // PK
	var $document_id	= null; // PK

	function __construct( &$db) {
		$pk = array('app_id', 'person_id', 'document_id');
		parent::__construct('#__joaktree_person_documents', $pk, $db);
	}

	function deletedocuments($person_id) {
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
}
?>
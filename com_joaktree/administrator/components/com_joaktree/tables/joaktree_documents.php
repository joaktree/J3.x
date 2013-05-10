<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_documents.php
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


class TableJoaktree_documents extends JMFPKTable
{
	var $id 			= null;
	var $app_id			= null;
	var $file			= null;
	var $fileformat		= null;
	var $title	 		= null;
	var $indCitation	= null;
	var $note_id    	= null;
	var $note 			= null;

	function __construct( &$db) {
		$pk = array('id', 'app_id');
		parent::__construct('#__joaktree_documents', $pk, $db);
	}
	
	function loadEmpty() {
		$this->id 			= null;
		$this->file			= null;
		$this->fileformat	= null;
		$this->title	 	= null;
		$this->indCitation	= null;
		$this->note_id      = null;
		$this->note 		= null;
	}
	
	function store() {
		$query = $this->_db->getQuery(true);
		
		if (!isset($this->id)) {				
			// Fetch the document primary key with the unique key
			$query->clear();
			$query->select(' id ');
			$query->from(  ' '.$this->_tbl.' ');
			$query->where( ' file = '.$this->_db->quote($this->file).' ');
			$query->where( ' app_id = '.$this->app_id.' ');
			
			// fetch and save the primary key 
			$this->_db->setQuery($query);
			$document_id = $this->_db->loadResult();			
		
			if ($document_id) {
				// record exists, set the primary key for updating the record with store
				$this->id = $document_id;
			} else {
				// new document record
				$query->clear();
				$query->select(' MAX( id ) ');
				$query->from(  ' '.$this->_tbl.' ');
				$query->where( ' app_id = '.$this->app_id.' ');
				
				// fetch and save the primary key 
				$this->_db->setQuery($query);
				$max_id = $this->_db->loadResult();				
				
				$new_id = (int) trim($max_id, 'D');
				$new_id = $new_id + 1;
				
				// unset the primary key, so the store function will insert a new record
				$this->id = sprintf('D%010s', $new_id);				

			}
		} 
		
		// Store the document
		$ret = parent::store();
				
		if ($ret) {
			// everything went ok
			return $this->id;
		} else {
			return false;
		}
	}
}
?>
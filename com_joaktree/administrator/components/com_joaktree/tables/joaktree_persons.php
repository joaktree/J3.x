<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_persons.php
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

//require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'tables'.DS.'JMFPKtable.php';
JLoader::import('components.com_joaktree.tables.JMFPKtable', JPATH_ADMINISTRATOR);
JLoader::import('components.com_joaktree.helpers.jt_relations', JPATH_ADMINISTRATOR);

class TableJoaktree_persons extends JMFPKTable
{
	var $app_id				= null; // PK
	var $id					= null; // PK
	var $indexNam           = null;
	var $firstName			= null;
	var $patronym			= null; 
	var $namePreposition	= null;
	var $familyName			= null;
	var $prefix				= null;
	var $suffix				= null;
	var $sex				= null;
	var $indNote			= null;
	var $indCitation		= null;
	var $indHasParent		= null;
	var $indHasPartner		= null;
	var $indHasChild		= null;
	var $indIsWitness		= null;
	var $lastUpdateTimeStamp;
	
	function __construct( &$db) {
		$pk = array('app_id', 'id');
		parent::__construct('#__joaktree_persons', $pk, $db);
	}

	function loadEmpty () {
		$this->id				= null;
		$this->indexNam			= null;
		$this->firstName		= null;
		$this->patronym			= null;
		$this->namePreposition	= null;
		$this->familyName		= null;
		$this->prefix			= null;
		$this->suffix			= null;
		$this->sex				= null;
		$this->indNote			= null;
		$this->indCitation		= null;
		$this->indHasParent		= null;
		$this->indHasPartner	= null;
		$this->indHasChild		= null;
		$this->indIsWitness		= null;
	}
       
	public function check() {
		// mandatory fields
		if (empty($this->app_id)) {
			return false;
		}
		if (empty($this->id)) {
			return false;
		}
		
		// set the indications
		if (!$this->checkNotesAndReferences()) {
			return false;
		}
		
		return true;
	}
       
	private function checkNotesAndReferences() {
		// check for citations
		$query = $this->_db->getQuery(true);
		$query->select(' COUNT(jcn.objectOrderNumber) AS indCit ');
		$query->from(  ' #__joaktree_citations jcn ');
		$query->where( ' jcn.app_id      = '.$this->app_id.' ');
		$query->where( ' (  jcn.person_id_1 = '.$this->_db->quote($this->id).' '
					 . ' OR jcn.person_id_2 = '.$this->_db->quote($this->id).' '
					 . ' ) '
					 );
				
		$this->_db->setQuery( $query );
		$result = $this->_db->loadResult();  
		$this->indCitation = ($result) ? true : false;
	
		// check for notes
		$query->clear();
		$query->select(' COUNT(jpe.orderNumber) AS indNot ');
		$query->from(  ' #__joaktree_person_notes jpe ');
		$query->where( ' jpe.app_id     = '.$this->app_id.' ');
		$query->where( ' jpe.person_id  = '.$this->_db->quote($this->id).' ');
		$query->where( ' jpe.nameOrderNumber   IS NULL ');
		$query->where( ' jpe.eventOrderNumber  IS NULL ');
		
		$this->_db->setQuery( $query );
		$result = $this->_db->loadResult();  
		$this->indNote = ($result) ? true : false;
				
		return true;
	}
	
	function insert() {
		if (!empty($this->familyName)) {
			$this->indexNam = mb_strtoupper(mb_substr($this->familyName, 0, 1 ));	
		}
		$ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
		return $ret;
	}

	function update() {
		if (!empty($this->familyName)) {
			$this->indexNam = mb_strtoupper(mb_substr($this->familyName, 0, 1 ));	
		}
		$ret = $this->_db->updateObject( $this->_tbl, $this, $this->_tbl_key, true );
		return $ret;
	}
	
	public function store($updateNulls = false) {
		if (!empty($this->familyName)) {
			$this->indexNam = mb_strtoupper(mb_substr($this->familyName, 0, 1 ));	
		}			
		return parent::store($updateNulls);
	}
	
	function delete() {
		// cascading delete
		$query = $this->_db->getQuery(true);
		$ret = true;

		// joaktree_person_events
		if ($ret) {
			$table = 'joaktree_person_events';
			//$query->clear();
			$query->delete(' #__joaktree_person_events ');
			$query->where( ' app_id    = '.$this->app_id.' ');
			$query->where( ' person_id = '.$this->_db->quote($this->id).' ');
			$this->_db->setQuery( $query );
			$ret = $this->_db->query();			
		}
		
		// joaktree_person_names
		if ($ret) {
			$table = 'joaktree_person_names';
			$query->clear();
			$query->delete(' #__joaktree_person_names ');
			$query->where( ' app_id    = '.$this->app_id.' ');
			$query->where( ' person_id = '.$this->_db->quote($this->id).' ');
			$this->_db->setQuery( $query );
			$ret = $this->_db->query();
		}
				
		// joaktree_person_notes
		if ($ret) {
			$table = 'joaktree_person_notes';
			$query->clear();
			$query->delete(' #__joaktree_person_notes ');
			$query->where( ' app_id    = '.$this->app_id.' ');
			$query->where( ' person_id = '.$this->_db->quote($this->id).' ');
			$this->_db->setQuery( $query );
			$ret = $this->_db->query();
		}
					
		// joaktree_person_documents + joaktree_documents
		if ($ret) {
			$table = 'joaktree_person_document';
			$query->clear();
			$query->delete(' #__joaktree_person_documents ');
			$query->where( ' app_id    = '.$this->app_id.' ');
			$query->where( ' person_id = '.$this->_db->quote($this->id).' ');
			$this->_db->setQuery( $query );
			$ret = $this->_db->query();
		}
				
		if ($ret) {
			$table = 'joaktree_documents';
			$query->clear();
			$query->delete(' #__joaktree_documents ');
			$query->where( ' app_id    = '.$this->app_id.' ');
			$query->where( ' NOT EXISTS ( '
						 . '  SELECT 1 '
						 . '  FROM   #__joaktree_person_documents  jpd '
						 . '  WHERE  jpd.app_id      = app_id '
						 . '  AND    jpd.document_id = id '
						 . '  ) '
						 );
			$this->_db->setQuery( $query );
			$ret = $this->_db->query();
		}
				
		// joaktree_relations
		if ($ret) {
			$table = 'joaktree_relations';
			$query->clear();
			// First select which relations exists (one direction)
			$query->select(' person_id_1 ');
			$query->from(' #__joaktree_relations ');
			$query->where( ' app_id = '.$this->app_id.' ');
			$query->where( ' person_id_2 = '.$this->_db->quote($this->id).' ');
			$this->_db->setQuery( $query );
			$relations = $this->_db->loadColumn();
			$query->clear();
			
			// Second select which relations exists (second direction)
			$query->select(' person_id_2 ');
			$query->from(' #__joaktree_relations ');
			$query->where( ' app_id = '.$this->app_id.' ');
			$query->where( ' person_id_1 = '.$this->_db->quote($this->id).' ');
			$this->_db->setQuery( $query );
			$tmp = $this->_db->loadColumn();
			$relations = array_merge ($relations, $tmp);
			$query->clear();
			
			// now we start deleting ...
			$query->delete(' #__joaktree_relations ');
			$query->where( ' app_id = '.$this->app_id.' ');
			$query->where( ' (  person_id_1 = '.$this->_db->quote($this->id).' '
						 . ' OR person_id_2 = '.$this->_db->quote($this->id).' '
						 . ' ) '
						 );
			$this->_db->setQuery( $query );
			$ret = $this->_db->query();
			
			// Finally, we reset the relation indicators for the remaining relations
			if ($ret) {
				$table = 'joaktree_persons (relationIndicators)';
				$ret = jt_relations::setRelationIndicators($this->app_id, $relations);
			}
		}
				
		// joaktree_relation_events
		if ($ret) {
			$table = 'joaktree_relation_events';
			$query->clear();
			$query->delete(' #__joaktree_relation_events ');
			$query->where( ' app_id = '.$this->app_id.' ');
			$query->where( ' (  person_id_1 = '.$this->_db->quote($this->id).' '
						 . ' OR person_id_2 = '.$this->_db->quote($this->id).' '
						 . ' ) '
						 );
			$this->_db->setQuery( $query );
			$ret = $this->_db->query();
		}
				
		// joaktree_relation_notes
		if ($ret) {
			$table = 'joaktree_relation_notes';
			$query->clear();
			$query->delete(' #__joaktree_relation_notes ');
			$query->where( ' app_id = '.$this->app_id.' ');
			$query->where( ' (  person_id_1 = '.$this->_db->quote($this->id).' '
						 . ' OR person_id_2 = '.$this->_db->quote($this->id).' '
						 . ' ) '
						 );
			$this->_db->setQuery( $query );
			$ret = $this->_db->query();
		}
				
		// joaktree_citations
		if ($ret) {
			$table = 'joaktree_citations';
			$query->clear();
			$query->delete(' #__joaktree_citations ');
			$query->where( ' app_id = '.$this->app_id.' ');
			$query->where( ' (  person_id_1 = '.$this->_db->quote($this->id).' '
						 . ' OR person_id_2 = '.$this->_db->quote($this->id).' '
						 . ' ) '
						 );
			$this->_db->setQuery( $query );
			$ret = $this->_db->query();
		}
		
		// joaktree_notes
		if ($ret) {
			$table = 'joaktree_notes';
			$query->clear();
			$query->delete(' #__joaktree_notes ');
			$query->where( ' app_id    = '.$this->app_id.' ');
			$query->where( ' NOT EXISTS ( '
						 . '  SELECT 1 '
						 . '  FROM   #__joaktree_person_notes  jpn '
						 . '  WHERE  jpn.app_id  = app_id '
						 . '  AND    jpn.note_id = id '
						 . '  ) '
						 );
			$query->where( ' NOT EXISTS ( '
						 . '  SELECT 1 '
						 . '  FROM   #__joaktree_relation_notes  jrn '
						 . '  WHERE  jrn.app_id  = app_id '
						 . '  AND    jrn.note_id = id '
						 . '  ) '
						 );
			$this->_db->setQuery( $query );
			$ret = $this->_db->query();
		}
		
		// joaktree_tree_persons
		if ($ret) {
			$table = 'joaktree_tree_persons';
			$query->clear();
			$query->delete(' #__joaktree_tree_persons ');
			$query->where( ' app_id    = '.$this->app_id.' ');
			$query->where( ' person_id = '.$this->_db->quote($this->id).' ');
			$this->_db->setQuery( $query );
			$ret = $this->_db->query();
		}
				
		// joaktree_persons
		if ($ret) {
			$table = 'joaktree_persons';
			$query->clear();
			$query->delete(' #__joaktree_persons ');
			$query->where( ' app_id = '.$this->app_id.' ');
			$query->where( ' id     = '.$this->_db->quote($this->id).' ');
			$this->_db->setQuery( $query );
			$ret = $this->_db->query();
		}
						
		// joaktree_admin_persons
		if ($ret) {
			$table = 'joaktree_admin_persons';
			$query->clear();
			$query->delete(' #__joaktree_admin_persons ');
			$query->where( ' app_id = '.$this->app_id.' ');
			$query->where( ' id     = '.$this->_db->quote($this->id).' ');
			$this->_db->setQuery( $query );
			$ret = $this->_db->query();
		}

		if (!$ret) {
			$this->setError('Cascading table '.$table.': Error -> '.$this->_db->getErrorMsg());
		}			
				
		return $ret;
	}
}
?>
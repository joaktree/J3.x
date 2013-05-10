<?php
/**
 * Joomla! component Joaktree
 * file		jt_settings model - jt_settings.php
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

// no direct access
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'tables');
JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'models');

// Import Joomla! libraries
//jimport('joomla.application.component.modellist');
jimport('legacy.model.list');
jimport('joomla.html.pagination');
jimport('joomla.language.language' );

class JoaktreeModelJt_settings extends JModelList {

	var $_dataPersName;
	var $_dataPersEvent;
	var $_dataRelaEvent;
	var $_level;
	var $_namePagination = null;
	var $_personPagination = null;
	var $_relationPagination = null;
	var $_total         = null;

	function __construct() {
		parent::__construct();		
	}

	private function _buildQuery() {
		$query = $this->_db->getQuery(true);
		
		$query->select(' jds.* ');
		$query->from(  ' #__joaktree_display_settings jds ');
		$query->where( ' jds.level = '.$this->_db->quote($this->_level).' ');
		
		$query->select(' vll1.title AS access_level ');
		$query->leftJoin(' #__viewlevels vll1 '
						.' ON (vll1.id = jds.access) '
						);
		
		$query->select(' IFNULL( vll2.title, '.$this->_db->quote('None').') AS access_level_living ');
		$query->leftJoin(' #__viewlevels vll2 '
						.' ON (vll2.id = jds.accessLiving) '
						);
						
		$query->select(' IFNULL( vll3.title, '.$this->_db->quote('None').') AS access_level_alttext ');
		$query->leftJoin(' #__viewlevels vll3 '
						.' ON (vll3.id = jds.altLiving) '
						);
						
		$query->order(' jds.level ');
		$query->order(' jds.ordering ');
		
		return $query;
	}

	public function getDataPersName() {
		$this->_level = 'name';
		
		// Lets load the content if it doesn't already exist
		if (empty($this->_dataPersName))
		{
			$query = $this->_buildQuery();
			$this->_dataPersName = $this->_getList( $query );
		}
		
		return $this->_dataPersName;
	}
	
	
	public function getDataPersEvent() {
		$this->_level = 'person';
		
		// Lets load the content if it doesn't already exist
		if (empty($this->_dataPersEvent))
		{
			$query = $this->_buildQuery();
			$this->_dataPersEvent = $this->_getList( $query );
		}
		
		return $this->_dataPersEvent;
	}
	
	
	public function getDataRelaEvent() {
		$this->_level = 'relation';
		
		// Lets load the content if it doesn't already exist
		if (empty($this->_dataRelaEvent))
		{
			$query = $this->_buildQuery();
			$this->_dataRelaEvent = $this->_getList( $query );
		}
		
		return $this->_dataRelaEvent;
	}
	
	public function publish() {
		$canDo	= JoaktreeHelper::getActions();
		
		if ($canDo->get('core.edit.state')) {
			$cids	= JFactory::getApplication()->input->get( 'cid', null, 'array' );
			
			foreach ($cids as $cid_num => $cid) {
				$cid = intval($cid);
				$query = $this->_db->getQuery(true);
				
				$query->update(' #__joaktree_display_settings ');
				$query->set(   ' published = !published ');
				$query->where( ' id = '.$cid.' ');
				
				$this->_db->setQuery($query);
				$msg = $this->_db->query();
			}
		
			$return = JText::sprintf('JTSETTINGS_UPDATED', count($cids));
		} else {
			$return = JText::_('JT_NOTAUTHORISED');
		}
		
		return $return;
	}
		
	public function getTotal() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}
		
		return $this->_total;
	}

	public function getNamePagination() {
		$this->_level = 'name';

		// Lets load the content if it doesn't already exist
		if (empty($this->_namePagination))
		{
			$this->_namePagination = new JPagination( $this->getTotal(), 0, 100 );
		}
		
		return $this->_namePagination;
	}

	public function getPersonPagination() {
		$this->_level = 'person';

		// Lets load the content if it doesn't already exist
		if (empty($this->_personPagination))
		{
			$this->_personPagination = new JPagination( $this->getTotal(), 0, 100 );
		}
		
		return $this->_personPagination;
	}

	public function getRelationPagination() {
		$this->_level = 'relation';

		// Lets load the content if it doesn't already exist
		if (empty($this->_relationPagination))
		{
			$this->_relationPagination = new JPagination( $this->getTotal(), 0, 100 );
		}
		
		return $this->_relationPagination;
	}

	private function setOrder($layout) {
		
		if ($layout == 'personname') {
			$where = ' level = '.$this->_db->quote('name').' ';
		} else if ($layout == 'personevent') {
			$where = ' level = '.$this->_db->quote('person').' ';
		} else if ($layout == 'relationevent') {
			$where = ' level = '.$this->_db->quote('relation').' ';
		} else {
			$where = ' level = '.$this->_db->quote('person').' ';
		}
		
		$settings 	= & JTable::getInstance('joaktree_display_settings', 'Table');
		$jtids		= JFactory::getApplication()->input->get( 'jtid', null, 'array' );

		$query = $this->_db->getQuery(true);
		$i = 0;
		
		foreach ($jtids as $jtid) {
			$query->clear();
			$i++;
			
			$query->update(' #__joaktree_display_settings ');
			$query->set(   ' ordering = '.$i.' ');
			$query->where( ' id = '.(int) $jtid.' ');
			$query->where( ' '.$where.' ');
			
			$this->_db->setQuery( $query );
			
			if (!$this->_db->query()) {
				JError::raiseError(500, $this->_db->getErrorMsg() );
			}
		}
			
		$settings->reorder($where);
		return '';
	}
	
	public function save($layout) {
		$canDo	= JoaktreeHelper::getActions();
		
		if ($canDo->get('core.edit')) {
			// initialize tables and records
			$row   	= & JTable::getInstance('joaktree_display_settings', 'Table');
			
			// get the input object
			$input = JFactory::getApplication()->input;
			
			$cids	= $input->get( 'cid', null, 'array' );
			// We are only doing it when we have items to be updated
			if ((count( $cids ) > 0) and ($cids[0] > 0)) {			
				for ($i=0, $n=count( $cids ); $i < $n; $i++) {
					// Bind the form fields to the table
					$row->id		= intval( $cids[$i] );
					
					$tmp 			= $input->get( 'access'.$row->id, null, 'string' );
					$tmp 			= (int) substr($tmp, 0, 3);
					$row->access	= $tmp;
					
					$tmp 			= $input->get( 'accessLiving'.$row->id, null, 'string' );
					$tmp 			= (int) substr($tmp, 0, 3);
					$row->accessLiving	= $tmp;
					
					$tmp 			= $input->get( 'altLiving'.$row->id, null, 'string' );
					$tmp 			= (int) substr($tmp, 0, 3);
					$row->altLiving	= $tmp;
								
					//$code 			= $input->get( 'code'.$row->id, null, 'string' );		
								
					// Make sure the table is valid
					if (!$row->check()) {
						$this->setError($this->_db->getErrorMsg());
						return false;
					}
					
					// Store the table to the database
					if (!$row->store()) {
						$this->setError($this->_db->getErrorMsg());
						return false;
					}
					
//					$retmsg .= $model->store($post, $code).';&nbsp;';
				}
			}
			
			$this->setOrder($layout);
						
			
			
			$return = '';
		} else {
			$return = JText::_('JT_NOTAUTHORISED');
		}
		
		return $return;
	}
	
//	public function store($data, $code) {
//		$canDo	= JoaktreeHelper::getActions();
//		
//		if ($canDo->get('core.edit')) {
//			// initialize tables and records
//			$row   	= & JTable::getInstance('joaktree_display_settings', 'Table');
//						
//			// Bind the form fields to the table
//			$row->id             = $data['id'];
//			$row->access		 = $data['access'];
//			$row->accessLiving   = $data['accessLiving'];
//			$row->altLiving      = $data['altLiving'];
//			
//			if (!$row->bind($data)) {
//				$this->setError($this->_db->getErrorMsg());
//				return false;
//			}
//			
//			// Make sure the table is valid
//			if (!$row->check()) {
//				$this->setError($this->_db->getErrorMsg());
//				return false;
//			}
//			
//			// Store the table to the database
//			if (!$row->store()) {
//				$this->setError($this->_db->getErrorMsg());
//				return false;
//			}
//			
//			$return = $code;
//		} else {
//			$return = JText::_('JT_NOTAUTHORISED');
//		}
//		
//		return $return;
//	}
}
?>
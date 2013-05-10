<?php
/**
 * Joomla! component Joaktree
 * file		jt_applications modelList - jt_applications.php
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

require_once JPATH_COMPONENT.DS.'helpers'.DS.'jt_names.php';
require_once JPATH_COMPONENT.DS.'helpers'.DS.'jt_relations.php';
require_once JPATH_COMPONENT.DS.'helpers'.DS.'jt_trees.php';
require_once JPATH_COMPONENT.DS.'tables'.DS.'JMFPKtable.php';

require_once JPATH_COMPONENT.DS.'helpers'.DS.'jt_gedcomfile2.php';
require_once JPATH_COMPONENT.DS.'helpers'.DS.'jt_gedcompersons2.php';
require_once JPATH_COMPONENT.DS.'helpers'.DS.'jt_gedcomsources2.php';
require_once JPATH_COMPONENT.DS.'helpers'.DS.'jt_gedcomrepos2.php';
require_once JPATH_COMPONENT.DS.'helpers'.DS.'jt_gedcomnotes2.php';
require_once JPATH_COMPONENT.DS.'helpers'.DS.'jt_gedcomdocuments2.php';


//JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'tables');
JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');
JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'models');


// Import Joomla! libraries
//jimport('joomla.application.component.modellist');
jimport('legacy.model.list');

class processObject {
	var $id			= null;
	var $start		= null;
	var $current	= null;
	var $end		= null;
	var $cursor		= 0;
	var $persons	= 0;
	var $families	= 0;
	var $sources	= 0;
	var $repos		= 0;
	var $notes		= 0;
	var $docs  		= 0;
	var $unknown	= 0;
	var $japp_ids	= null;
	var $status		= 'new';
	var $msg		= null;			
}

class JoaktreeModelJt_applications extends JModelList {

	var $_data;
	var $_pagination 	= null;
	var $_total         = null;

	function __construct() {
		parent::__construct();		

		$app = JFactory::getApplication();
			
		$context			= 'com_joaktree.jt_applications.list.';
		// Get the pagination request variables
		$limit		= $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg('list_limit'), 'int' );
		$limitstart	= $app->getUserStateFromRequest( $context.'limitstart',	'limitstart',	0, 'int' );

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	private function _buildQuery() {
		$query = $this->_db->getQuery(true);
		
		$query->select(' japp.id ');
		$query->select(' japp.title ');
		$query->select(' japp.description ');
		$query->select(' japp.programName ');
		$query->from(  ' #__joaktree_applications  japp ');
		
		$query->select(' COUNT(jpn.id) AS NumberOfPersons ');
		$query->leftJoin(' #__joaktree_persons       jpn '
						.' ON ( jpn.app_id = japp.id ) ' 
						);
		
		// WHERE, GROUP BY and ORDER BY clauses for the query
		$wheres     =  $this->_buildContentWhere();
		foreach ($wheres as $where) {
			$query->where(' '.$where.' ');
		}
		$query->group(' japp.id ');
		$query->group(' japp.title ');
		$query->group(' japp.description ');
		$query->group(' japp.programName ');
		$query->order(' '.$this->_buildContentOrderBy().' ');
		
		return $query;			
	}

	private function _buildContentWhere() {
		$app = JFactory::getApplication();
		
		$context		= 'com_joaktree.jt_applications.list.';
		$search			= $app->getUserStateFromRequest( $context.'search',			'search',			'',				'string' );
		$search			= JString::strtolower( $search );
		
		$where = array();
		
		if ($search) {
			$where[] =   'LOWER(japp.title) LIKE '.$this->_db->Quote('%'.$search.'%').' '
						.'OR LOWER(japp.description) LIKE '.$this->_db->Quote('%'.$search.'%').' '
						.'OR LOWER(japp.programName) LIKE '.$this->_db->Quote('%'.$search.'%').' ';
		}
				
		return $where;
	}

	private function _buildContentOrderBy() {
		$app = JFactory::getApplication();
		
		$context		= 'com_joaktree.jt_applications.list.';
		$filter_order		= $app->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'japp.id',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',		'word' );
		
		if ($filter_order){
			$orderby 	= ' '.$filter_order.' '.$filter_order_Dir.' ';
		} else {
			$orderby 	= ' japp.id '.$filter_order_Dir.' ';
		}
		
		return $orderby;
	}

	public function getData() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit'));
		}
		
		return $this->_data;
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

	public function getPagination() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		
		return $this->_pagination;
	}
	
	/* 
	** function for processing the gedcom file
	*/
	public function getGedcom() {
		$canDo	= JoaktreeHelper::getActions();
		$localMsg = '';
		
		if (($canDo->get('core.create')) && ($canDo->get('core.edit'))) {		
			$cids = JFactory::getApplication()->input->get( 'cid', null, 'array' );
			
			if (count($cids) == 0) {
				// no applications are selected
				$return = JText::_('JTGEDCOM_MESSAGE_NOAPPLICATIONS');
				
			} else {
				
				foreach ($cids as $cid_num => $app_id) {
					$procObject = new processObject();
					
					$current = strftime('%H:%M:%S');	
					$procObject->msg = JText::_('JTPROCESS_START').':'.$current;
					
					$procObject->id = (int) $app_id;
					$procObject->msg .= '<br />'.JText::sprintf('JTPROCESS_START_MSG', $procObject->id);

					$params         = JoaktreeHelper::getJTParams($procObject->id);
					$processStep  	= (int) $params->get('processStep', 9);		
					
					// as of version 1.2: new method 
			 		if ($processStep == 4) {
						$gedcomfile = new jt_gedcomfile2($procObject);
						$procObject = $gedcomfile->process('person');						
			 		}
			
					if ($processStep == 5) {
						$gedcomfile = new jt_gedcomfile2($procObject);
						$procObject = $gedcomfile->process('family');
						$ret = jt_gedcomfile2::clear_gedcom();
						if ($ret) {
							$procObject->msg .= '<br />'.$ret;
						}
					}
					
					if ($processStep == 6) {
						$gedcomfile = new jt_gedcomfile2($procObject);
						$procObject = $gedcomfile->process('source');
					}
			
					if ($processStep == 7) {
						$gedcomfile = new jt_gedcomfile2($procObject);
						$procObject = $gedcomfile->process('repository');
						$procObject = $gedcomfile->process('note');
						$procObject = $gedcomfile->process('document');
					}
					
					if ($processStep == 9) {
						$gedcomfile = new jt_gedcomfile2($procObject);
						$procObject = $gedcomfile->process('all');
						$ret = jt_gedcomfile2::clear_gedcom();
						if ($ret) {
							$procObject->msg .= '<br />'.$ret;
						}
					}
										
					if ($procObject->status != 'error') {
						$this->setInitialChar();
						$this->setLastUpdateDateTime();
								
						if ($procObject->persons > 0) {
							$procObject->msg .= '<br />'.JText::sprintf('JTGEDCOM_MESSAGE_PERSONS',$procObject->persons);
						}
						if ($procObject->families > 0) {
							$procObject->msg .= '<br />'.JText::sprintf('JTGEDCOM_MESSAGE_FAMILIES',$procObject->families);
						}
						if ($procObject->sources > 0) {
							$procObject->msg .= '<br />'.JText::sprintf('JTGEDCOM_MESSAGE_SOURCES',$procObject->sources);
						}
						if ($procObject->repos > 0) {
							$procObject->msg .= '<br />'.JText::sprintf('JTGEDCOM_MESSAGE_REPOS',$procObject->repos);
						}
						if ($procObject->notes > 0) {
							$procObject->msg .= '<br />'.JText::sprintf('JTGEDCOM_MESSAGE_NOTES',$procObject->notes);
						}
						if ($procObject->unknown > 0) {
							$procObject->msg .= '<br />'.JText::sprintf('JTGEDCOM_MESSAGE_UNKNOWN',$procObject->unknown);
						}
						
						$procObject->msg .= '<br />'.JText::sprintf('JTPROCESS_END_MSG', $procObject->id);
					} else {
						$return = $procObject->msg;
					}
					
					$current = strftime('%H:%M:%S');
					$procObject->msg .= '<br />'.JText::_('JTPROCESS_END').':'.$current;
					
					$localMsg .= $procObject->msg;
				}
								
				$return = $localMsg;
			}
		} else {
			$return = JText::_('JT_NOTAUTHORISED');
		}
		
		return $return;
	}
	
	/* 
	** function for clearing the tables for a specfic gedcom file, with exception
	** of the admin table
	*/
	public function clearGedCom() {
		$canDo	= JoaktreeHelper::getActions();
		
		if ($canDo->get('core.delete')) {
			$cids = JFactory::getApplication()->input->get( 'cid', null, 'array' );
			$msg = '';
			
			foreach ($cids as $cid_num => $app_id) {
				$app_id	= (int) $app_id;
				$msg   .= '+'.jt_gedcomfile2::deleteGedcomData($app_id, false);
			}
			
			$return = $msg;
			
		} else {
			$return = JText::_('JT_NOTAUTHORISED');
		}
		
		return $return;
	}
	
	/* 
	** function for clearing the tables for a specfic gedcom file
	*/
	public function deleteGedCom() {
		$canDo	= JoaktreeHelper::getActions();
	
		if ($canDo->get('core.delete')) {
			$cids = JFactory::getApplication()->input->get( 'cid', null, 'array' );
			$msg = '';
			
			foreach ($cids as $cid_num => $app_id) {
				$app_id	= (int) $app_id;
				$msg   .= '+'.jt_gedcomfile2::deleteGedcomData($app_id, true);
			}
			
			$return = $msg;
			
		} else {
			$return = JText::_('JT_NOTAUTHORISED');
		}
		
		return $return;
	}
	
	private function setLastUpdateDateTime() {
		$query = $this->_db->getQuery(true);
		$query->update(' #__joaktree_registry_items ');
		$query->set(   ' value  = NOW() ');
		$query->where( ' regkey = '.$this->_db->quote( 'LAST_UPDATE_DATETIME' ).' ');
		
		$this->_db->setQuery( $query );
		$this->_db->query();		
	}

	private function setInitialChar() {		
		// update register with 0, meaning NO "initial character" present
		$query = $this->_db->getQuery(true);
		$query->update(' #__joaktree_registry_items ');
		$query->set(   ' value  = '.$this->_db->quote('0').' ');
		$query->where( ' regkey = '.$this->_db->quote( 'INITIAL_CHAR' ).' ');
				
		$this->_db->setQuery( $query );
		$this->_db->query();			
	}
}
?>
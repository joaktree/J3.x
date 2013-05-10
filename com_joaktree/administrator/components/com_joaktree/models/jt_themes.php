<?php
/**
 * Joomla! component Joaktree
 * file		jt_themes modelList - jt_themes.php
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

require_once JPATH_COMPONENT.DS.'helpers'.DS.'jt_trees.php';

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'tables');
JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'models');


// Import Joomla! libraries
//jimport('joomla.application.component.modellist');
jimport('legacy.model.list');

class JoaktreeModelJt_themes extends JModelList {

	var $_data;
	var $_pagination 	= null;
	var $_total         = null;

	function __construct() {
		parent::__construct();		

		$app = JFactory::getApplication();
			
		$context			= 'com_joaktree.jt_themes.list.';
		// Get the pagination request variables
		$limit		= $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg('list_limit'), 'int' );
		$limitstart	= $app->getUserStateFromRequest( $context.'limitstart',	'limitstart',	0, 'int' );

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	private function _buildQuery() {
		// Get the WHERE and ORDER BY clauses for the query
		$query = $this->_db->getQuery(true);
		$query->select(' jtmp.* ');
		$query->from(  ' #__joaktree_themes  jtmp ');
		
		$wheres     =  $this->_buildContentWhere();
		foreach ($wheres as $where) {
			$query->where(' '.$where.' ');
		}
		
		$orderby	=  $this->_buildContentOrderBy();
		$query->order(' '.$orderby.' ');
					
		return $query;
	}

	private function _buildContentWhere() {
		$app = JFactory::getApplication();
		
		$context		= 'com_joaktree.jt_themes.list.';
		$search			= $app->getUserStateFromRequest( $context.'search',			'search',			'',				'string' );
		$search			= JString::strtolower( $search );
		
		$wheres = array();
		
		if ($search) {
			$wheres[] = 'LOWER(jtmp.name) LIKE '.$this->_db->Quote('%'.$search.'%');
		}
				
		return $wheres;
	}

	private function _buildContentOrderBy() {
		$app = JFactory::getApplication();
		
		$context		= 'com_joaktree.jt_themes.list.';
		$filter_order		= $app->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'jtmp.id',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',		'word' );
		
		if ($filter_order){
			$orderby 	= ' '.$filter_order.' '.$filter_order_Dir.' ';
		} else {
			$orderby 	= ' jtmp.id '.$filter_order_Dir.' ';
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
	
	public function setDefault($id) {
		$canDo	= JoaktreeHelper::getActions();
		
		if ($canDo->get('core.edit.state')) {
			// set id to default
			$query = $this->_db->getQuery(true);
			$query->update(' #__joaktree_themes ');
			$query->set(   ' home = 1 ');
			$query->where( ' id   = '.(int) $id.' ');
			
			$this->_db->setQuery($query);
			$ret = $this->_db->query();
			
			if ($ret) {
				// set other record not to default
				$query->clear();
				$query->update(' #__joaktree_themes ');
				$query->set(   ' home = 0 ');
				$query->where( ' id   <> '.(int) $id.' ');
				
				$this->_db->setQuery($query);
				$this->_db->query();
			}
			
			if ($ret) {
				$name = JoaktreeHelper::getThemeName($id);
			}
			
			if ($ret) {
				return JText::sprintf('JTTHEME_MESSAGE_SETDEFAULT', $name);
			} else {
				return JText::sprintf('JTTHEME_ERROR_SETDEFAULT', $id);
			}
		} else {
			$return = JText::_('JT_NOTAUTHORISED');
		}
	}
}
?>
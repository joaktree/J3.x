<?php
/**
 * Joomla! component Joaktree
 * file		front end joaktreelist model - joaktreelist.php
 *
 * @version	1.5.0
 * @author	Niels van Dantzig
 * @package	Joomla
 * @subpackage	Joaktree
 * @license	GNU/GPL
 *
 * Component for genealogy in Joomla!
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

// import component libraries
JLoader::import('helper.tree', JPATH_COMPONENT);

class JoaktreeModelJoaktreelist extends JModelLegacy {

	function __construct() {
		parent::__construct();
		
		$app 		= JFactory::getApplication('site');
		
		$context			= 'com_joaktree.joaktreelist.list.';
		// Get the pagination request variables
		$limit		= $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg('list_limit'), 'int' );

		$limitstart	= $app->getUserStateFromRequest( $context.'limitstart',	'limitstart',	0, 'int' );
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function getUserAccess() {
		return JoaktreeHelper::getUserAccess();
	}
	
	public function getTreeId() {
		return JoaktreeHelper::getTreeId();
	}

	public function getRelationId() {
		return JoaktreeHelper::getRelationId();	
	}
	
	public function getTechnology() {
		return JoaktreeHelper::getTechnology();
	}

	public static function getAccess() {
		return JoaktreeHelper::getAccessTree();
	}
	
	private function _buildQuery()
	{
		$treeId     	= intval( $this->getTreeId() );
		$levels			= JoaktreeHelper::getUserAccessLevels();
		$displayAccess 	= JoaktreeHelper::getDisplayAccess();
		
		$query = $this->_db->getQuery(true);
		
		// select from persons
		$query->select(' jpn.id ');
		$query->select(' jpn.app_id ');
		$query->select(' MIN( '.JoaktreeHelper::getSelectFirstName().' ) AS firstName ');
		$query->select(' MIN( '.JoaktreeHelper::getSelectPatronym().' ) AS patronym ');
		$query->select(' MIN( '.JoaktreeHelper::getConcatenatedFamilyName().' ) AS familyName ');
		$query->select(' MIN( '.JoaktreeHelper::getSelectBirthYear().' ) AS birthDate ');
		$query->select(' MIN( '.JoaktreeHelper::getSelectDeathYear().' ) AS deathDate ');
		$query->from(  ' #__joaktree_persons jpn ');

		// select from admin persons
		$query->innerJoin(JoaktreeHelper::getJoinAdminPersons(false));
		
		// select from tree x persons
		$query->innerJoin(' #__joaktree_tree_persons  jtp ' 
						 .' ON (   jtp.app_id    = jpn.app_id '
						 .'    AND jtp.person_id = jpn.id '
						 .'    ) '
						 );
		$query->innerJoin(' #__joaktree_trees         jte ' 
						 .' ON (   jte.app_id    = jtp.app_id '
						 .'    AND jte.id        = jtp.tree_id '
						 .'    AND jte.published = true '
						 .'    AND jte.access    IN '.$levels.' '
						 .'    ) '
						 );
						 
		// select from birth and death
		$query->leftJoin(JoaktreeHelper::getJoinBirth());
		$query->leftJoin(JoaktreeHelper::getJoinDeath());
		
		// Get the WHERE, GROUP BY and ORDER BY clauses for the query
		$wheres      	= $this->_buildContentWhere();
		foreach ($wheres as $where) {
			$query->where(' '.$where.' ');
		}
		$query->group(' jpn.id ');
		$query->group(' jpn.app_id ');
		$query->order(' '.$this->_buildContentOrderBy().' ');
				
			
		return $query;
	}

	private function _buildContentWhere()
	{
		$app 		= JFactory::getApplication('site');
		$treeId     = intval( $this->getTreeId() );
		$levels		= JoaktreeHelper::getUserAccessLevels();
		$params 	= JoaktreeHelper::getJTParams();

		$context	= 'com_joaktree.joaktreelist.list.';

		$search1	= $app->getUserStateFromRequest( $context.'search1',	'search1',	'',	'string' );
		$search1	= $this->_db->escape( $search1, true );
		$search1	= JString::strtolower( $search1 );

		$search2	= $app->getUserStateFromRequest( $context.'search2',	'search2',	'',	'string' );
		$search2	= $this->_db->escape( $search2, true );
		$search2	= JString::strtolower( $search2 );

		$search3	= $app->getUserStateFromRequest( $context.'search3',	'search3',	'',	'string' );
		$search3	= $this->_db->escape( $search3, true );
		$search3	= JString::strtolower( $search3 );

		$search4	= $app->getUserStateFromRequest( $context.'search4',	'search4',	'',	'string' );
		$search4    = base64_decode($search4);
		$search4	= $this->_db->escape( $search4, true );
		
		$where = array();

		if ($treeId) {
			$where[] = 'jtp.tree_id = ' . $treeId;
		}
		
		
		

		if ($search1) {
			$where[] = 'LOWER(jpn.firstName) LIKE '.$this->_db->Quote('%'.$search1.'%');
		}

		if ($search2) {
			$where[] = 'LOWER(jpn.patronym) LIKE '.$this->_db->Quote('%'.$search2.'%');
		}

		if ($search3) {
			$where[] = 'LOWER(jpn.familyName) LIKE '.$this->_db->Quote('%'.$search3.'%') ;
		}
		
		if ($search4) {
			$where[] = 	 'EXISTS '
						.'( '
						.'SELECT 1 '
						.'FROM   #__joaktree_person_events     ejpe '
						.'JOIN   #__joaktree_display_settings  ejds '
						.'ON     (   ejds.code      = ejpe.code '
						.'       AND ejds.level     = '.$this->_db->Quote( 'person' ).' '
						.'       AND ejds.published = true '
						.'       ) '
						.'JOIN   #__joaktree_admin_persons     ejan '
						.'ON     (   ejan.app_id    = ejpe.app_id '
						.'       AND ejan.id        = ejpe.person_id '
						.'       AND ejan.published = true '
			            // privacy filter
						.'       AND (  (ejan.living = false AND ejds.access IN '.$levels.') '
						.'           OR (ejan.living = true  AND ejds.accessLiving IN '.$levels.') '
						.'           ) '
						.'       ) '
						.'WHERE  ejpe.person_id = jpn.id '
						.'AND    ejpe.app_id    = jpn.app_id '
						.'AND    ejpe.location  = '.$this->_db->Quote($search4).' '
						.'UNION '
						.'SELECT 1 '
						.'FROM   #__joaktree_relation_events   ejre '
						.'JOIN   #__joaktree_display_settings  rjds '
						.'ON     (   rjds.code      = ejre.code '
						.'       AND rjds.level     = '.$this->_db->Quote( 'relation' ).' '
						.'       AND rjds.published = true '
						.'       ) '
						.'JOIN   #__joaktree_admin_persons     ejan1 '
						.'ON     (   ejan1.app_id    = ejre.app_id '
						.'       AND ejan1.id        = ejre.person_id_1 '
						.'       AND ejan1.published = true '
			            // privacy filter
						.'       AND (  (ejan1.living = false AND rjds.access IN '.$levels.') '
						.'           OR (ejan1.living = true  AND rjds.accessLiving IN '.$levels.') '
						.'           ) '
						.'       ) '
						.'JOIN   #__joaktree_admin_persons     ejan2 '
						.'ON     (   ejan2.app_id    = ejre.app_id '
						.'       AND ejan2.id        = ejre.person_id_2 '
						.'       AND ejan2.published = true '
			            // privacy filter
						.'       AND (  (ejan2.living = false AND rjds.access IN '.$levels.') '
						.'           OR (ejan2.living = true  AND rjds.accessLiving IN '.$levels.') '
						.'           ) '
						.'       ) '
						.'WHERE  (  ejre.person_id_1 = jpn.id '
						.'       OR ejre.person_id_2 = jpn.id '
						.'       ) '
						.'AND    ejre.app_id    = jpn.app_id '
						.'AND    ejre.location  = '.$this->_db->Quote($search4).' '
						.') '; 
		}

		//$where 		= ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );	

		return $where;
	}

	private function _buildContentOrderBy()
	{
		$app 				= JFactory::getApplication('site');

		$context			= 'com_joaktree.joaktreelist.list.';
		$filter_order		= $app->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'jpn.familyName',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',			'word' );

		if ($filter_order == 'jpn.familyName'){
			//$orderby 	= ' ORDER BY  jpn.familyName '.$filter_order_Dir.', jpn.firstName '.$filter_order_Dir.' ';
			$orderby 	= ' jpn.familyName '.$filter_order_Dir.', jpn.firstName '.$filter_order_Dir.' ';
		} else {
            //$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.', jpn.familyName '.$filter_order_Dir.' ';
            $orderby 	= ' '.$filter_order.' '.$filter_order_Dir.', jpn.familyName '.$filter_order_Dir.' ';
		}

		return $orderby;
	}

	public function getPersonlist() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_personlist)) {
			$query = $this->_buildQuery();					
			$this->_personlist = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit'));
			
			$action = JoaktreeHelper::getAction();
			if ($action == 'saveparent1') {		
				for ($i=0, $n=count($this->_personlist); $i<$n; $i++) {
					$this->_personlist[$i]->partners = $this->getPartners($this->_personlist[$i]->app_id, $this->_personlist[$i]->id);
				}
			}		
		}
		
		return $this->_personlist;
	}

	public function getTree_id() {        
		return $this->_tree_id;
	}


	public function getPatronymSetting() {
		static $_patronymSetting;
		
		if (!isset($_patronymSetting)) {
			$params = JoaktreeHelper::getJTParams();
			$_patronymSetting	= (int) $params->get('patronym');
		}
		
		return $_patronymSetting;
	}

	public function getFilter3() {
		return $this->filter3;
	}


	public function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}
		
		return $this->_total;
	}

	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		
		return $this->_pagination;
	}
	
	public function getMenusJoaktree() {
		static $_menuTreeId1 	= array();
		
		// retrieve the menu item ids - if not done yet
		if ( count($_menuTreeId1) == 0 ) {
			$_menuTreeId1 = JoaktreeHelper::getMenus('joaktree');
		}
		
		return $_menuTreeId1;
	}

	public function getMenusJoaktreelist() {
		static $_menuTreeId2 	= array();
		
		// retrieve the menu item ids - if not done yet
		if ( count($_menuTreeId2) == 0 ) {
			$_menuTreeId2 = JoaktreeHelper::getMenus('joaktreelist');
		}
		
		return $_menuTreeId2;
	}
	
	public function getLastUpdate() {
		return JoaktreeHelper::lastUpdateDateTime();	
	}
	
	private function getPartnerSet($number1, $appId, $personId) {
		$db = JFactory::getDBO();
		$number2 = ($number1 == '1') ? '2' : '1';
		$query = $db->getQuery(true);
		
		// select relationship
		$query->select(' jrn.family_id ');
		$query->select(' jrn.person_id_'.$number2.' AS relation_id ');
		$query->from(  ' #__joaktree_relations  jrn ');
		$query->where( ' jrn.app_id = '.$appId.' ');
		$query->where( ' jrn.person_id_'.$number1.' = '.$db->quote($personId).' ');
		$query->where( ' jrn.type = '.$db->quote('partner').' ');
		
		// select name partner
		$query->select(JoaktreeHelper::getConcatenatedFullName().' AS fullName ');
		$query->innerJoin(' #__joaktree_persons  jpn '
						 .' ON (   jpn.app_id = jrn.app_id '
						 .'    AND jpn.id     = jrn.person_id_'.$number2.' '
						 .'    ) ' 
						 );
		
		// select from admin persons
		$query->innerJoin(JoaktreeHelper::getJoinAdminPersons());
		
		$db->setQuery( $query );
		$partners  = $db->loadAssocList();
		
		return $partners; 
	}
	
	private function getChildrenSet($appId, $personId) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// select relationship
		$query->select(' DISTINCT jrn.family_id ');
		$query->from(  ' #__joaktree_relations  jrn ');
		$query->where( ' jrn.app_id = '.$appId.' ');
		$query->where( ' jrn.person_id_2 = '.$db->quote($personId).' ');
		$query->where( ' jrn.type IN ('.$db->quote('father').', '.$db->quote('mother').') ');
		$query->where( ' NOT EXISTS ' 
					 . ' ( SELECT 1 '
					 . '   FROM   #__joaktree_relations  jrn2 '
					 . '   WHERE  jrn2.app_id    = jrn.app_id '
					 . '   AND    jrn2.family_id = jrn.family_id '
					 . '   AND    jrn2.type      = '.$db->quote('partner').' '
					 . ' ) '
					 );
		
		$db->setQuery( $query );
		$familyId  = $db->loadResult();
		
		return $familyId; 
	}
	
	private function getPartners($appId, $personId) {
		
		$partners1 = $this->getPartnerSet('1', $appId, $personId);
		$partners2 = $this->getPartnerSet('2', $appId, $personId);
		
		// join the arrays and sort them
		$partners = array_merge($partners1, $partners2);
		ksort($partners);
				
		// add "single" person option
		$single = array();
		$familyId = $this->getChildrenSet($appId, $personId);
		$single['family_id'] = ($familyId) ? $familyId : '0';
		$single['relation_id'] = null;
		$single['fullName']  = JText::_('JT_NOPARTNER');
		$partners[] = $single;
			
		return $partners;
	}
}
?>
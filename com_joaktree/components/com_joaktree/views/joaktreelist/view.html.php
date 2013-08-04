<?php
/**
 * Joomla! component Joaktree
 * file		view joaktree list - view.html.php
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

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Joaktree component
 */
class JoaktreeViewJoaktreelist extends JViewLegacy {
	function display($tpl = null) {
		
		// Load the parameters.
		$app 			= JFactory::getApplication('site');
		$params			= JoaktreeHelper::getJTParams();
		$document = &JFactory::getDocument();
		
		// Find the value for tech
		$technology	= $this->get( 'technology' );

		// set up style sheets and javascript files
		JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
		JHTML::stylesheet( JoaktreeHelper::joaktreecss($params->get('theme')) );
				
		// get user info
		$userId			= $this->get( 'userId' );
		if(!$userId || $userId == 0) {
			$document->addScript( JoaktreeHelper::joaktreejs('jtform.js'));
		}
		
		// Get data from the model
		$personlist			= $this->get( 'personlist' );  
		$pagination			= & $this->get( 'Pagination' );
		$tree_id			= $this->get( 'treeId' );  
		$patronymSetting	= $this->get( 'patronymSetting' );
		$userAccess			= $this->get( 'access' );
		$menus1  			= & $this->get( 'menusJoaktree' ); 
		$menus2  			= & $this->get( 'menusJoaktreelist' ); 
		
		// Id's and settings
		$lists['tree_id']		= $tree_id;
		$lists['relationId']	= $this->get( 'relationId' );
		$lists['menuItemId'] 	= $menus1[ $tree_id ];
		$lists['menuItemId2'] 	= $menus2[ $tree_id ];
		$lists['patronym'] 		= $patronymSetting;
		$lists['userAccess'] 	= $userAccess;
		$lists['technology'] 	= $technology;
		$lists['action']		= JoaktreeHelper::getAction();
		
		//Filter
		$context			= 'com_joaktree.joaktreelist.list.';		
		
		$filter_order		= $app->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'jpn.familyName',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',			'word' );
		$search1			= $app->getUserStateFromRequest( $context.'search1',	'search1',	'',	'string' );
		$search1			= JString::strtolower( $search1 );
		$search2			= $app->getUserStateFromRequest( $context.'search2',	'search2',	'',	'string' );
		$search2			= JString::strtolower( $search2 );
		$search3			= $app->getUserStateFromRequest( $context.'search3',	'search3',	'',	'string' );
		$search3			= JString::strtolower( $search3 );
		$search4			= $app->getUserStateFromRequest( $context.'search4',	'search4',	'',	'string' );
		$search4    		= base64_decode($search4);
		
		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order'] 	= $filter_order;
		
		// search filter
		$lists['searchWidth']	= (int) $params->get('search_width', '120'); 
		$lists['search1']		= $search1;
		$lists['search2']		= $search2;
		$lists['search3']		= $search3;
		$lists['search4']		= $search4;
		
		// last update
		$lists[ 'lastUpdate' ]	= $this->get( 'lastUpdate' );
		
		// copyright
		$lists[ 'CR' ]		= JoaktreeHelper::getJoaktreeCR();
		
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('personlist', 	$personlist );
		$this->assignRef('lists',	$lists);
		
		if ((($lists['action'] == 'save') || ($lists['action'] == 'saveparent1')) && (count($personlist) == 0)) {
			JFactory::getDocument()->addScriptDeclaration('window.parent.jtSavePerson();');
		} else {
			
			if ($lists['userAccess']) {
				// set title, meta title
				if ($params->get('treeName')) {
					$title = $params->get('treeName');
					$document->setTitle($title);
					$document->setMetadata('title', $title);
				}
				
				// set additional meta tags
				if ($params->get('menu-meta_description')) {
					$document->setDescription($params->get('menu-meta_description'));
				}
	
				if ($params->get('menu-meta_keywords')) {
					$document->setMetadata('keywords', $params->get('menu-meta_keywords'));
				}
	
				// robots
				if ($params->get('treeRobots') > 0) {
					$document->setMetadata('robots', JoaktreeHelper::stringRobots($params->get('treeRobots')));
				} else if ($params->get('robots')) {
					$document->setMetadata('robots', $params->get('robots'));
				}				
			}
					
			parent::display($tpl);
		}
	}
}
?>
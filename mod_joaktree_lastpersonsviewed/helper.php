<?php
/**
 * Joomla! module Joaktree last persons viewed
 * file		JoaktreeHelper - helper.php
 *
 * @version	1.5.0
 * @author	Niels van Dantzig
 * @package	Joomla
 * @subpackage	Joaktree
 * @license	GNU/GPL
 *
 * Module showing list of persons last viewed by user
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

require_once (JPATH_SITE.DS.'components'.DS.'com_joaktree'.DS.'helper'.DS.'helper.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_joaktree'.DS.'helper'.DS.'person.php');

class modJoaktreeLastPersonsViewedHelper
{		
	public static function getList($numberInList) {		
		// get cookie
		$params     = JComponentHelper::getParams('com_joaktree');
		$indCookie  = $params->get('indCookies', false);	
		
		if (!$indCookie) {
			return false;
		}
		
		// array to return
		$person = array();
		
		// we fetch the cookie
		$cookie = new JInputCookie();
		$tmp	= $cookie->get('jt_last_persons', '', 'string'); 
		
		// prepare the array
		if ($tmp) { 
			$cookieList = (array) json_decode(base64_decode($tmp));
		} else {
			$cookieList = array();
		}
		
		if (count($cookieList)) {
			$db			= JFactory::getDBO();
			$query 		= $db->getQuery(true);
			
			// get user access info
			$levels		= JoaktreeHelper::getUserAccessLevels();
			
			// set up maximum number of items
			$maxItem = (count($cookieList) > $numberInList) ? $numberInList : count($cookieList);
			
			// get menuId & technology
			$menus		= JoaktreeHelper::getMenus('joaktree');
			$linkBase 	= 'index.php?option=com_joaktree&view=joaktree';
			
			for ($i=0; $i<$maxItem; $i++) {
				$query->clear();
				
				// item is a string separated by !: app_id, person_id, tree_id;
				$elements = explode('!', array_shift($cookieList));
				
				// retrieve person
				$app_id 	= array_shift($elements);
				$person_id 	= array_shift($elements);
				$tree_id   	= array_shift($elements);	
				
				if (!empty($app_id) && !empty($person_id) &&!empty($tree_id)) {
					$query->select(JoaktreeHelper::getConcatenatedFullName().' AS fullName ');
					$query->from(  ' #__joaktree_persons   jpn ');
					$query->where( ' jpn.app_id    = '.$app_id.' ');
					$query->where( ' jpn.id        = '.$db->Quote($person_id).' ');
					
					// join with admin persons
					$query->innerJoin(JoaktreeHelper::getJoinAdminPersons(true));
					
					// join with trees
					$query->innerJoin(' #__joaktree_tree_persons  jtp ' 
									 .' ON (   jtp.app_id    = jpn.app_id '
									 .'    AND jtp.person_id = jpn.id '
									 .'    AND jtp.tree_id   = '.$tree_id.' '
									 .'    ) '
									 );
					$query->innerJoin(' #__joaktree_trees         jte ' 
									 .' ON (   jte.app_id    = jtp.app_id '
									 .'    AND jte.id        = jtp.tree_id '
									 .'    AND jte.published = true '
									 .'    AND jte.access    IN '.$levels.' '
									 .'    ) '
									 );
									 
					// fetch from database
					$db->setQuery( $query );	
					$tmpPerson  = $db->loadObject();
									 
					if (($tree_id) && (isset($menus[$tree_id]))) {
						$menuItemId			= $menus[$tree_id];
						$tmpPerson->route	= JRoute::_( $linkBase.'&Itemid='.$menuItemId.'&treeId='.$tree_id.'&personId='. $app_id.'!'.$person_id );
						$tmpPerson->robot	= '';
					} else { 
						$tmpPerson->route	= 0;
						$tmpPerson->robot	= '';
					}
											
					if (!empty($tmpPerson->fullName)) {
						$person[]			= $tmpPerson;
					}
					unset($tmpPerson);
				}
			}
		} 
				
		return $person;
	}
}

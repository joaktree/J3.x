<?php
/**
 * Joomla! component Joaktree
 * file		JoaktreeHelper - helper.php
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

jimport( 'joomla.filesystem.folder' ); 


class JoaktreeHelper {
	
	public function getIdlength() {
		// ID length = 20
		return 20;
	}
	
	public function getJTParams($requestonly = false) {
		static $_params;

		if (!isset($_params)) {
			// Load the parameters.
			$app = JFactory::getApplication('site');
			$_params = $app->getParams();

			// retrieve and merge the parameters of the GedCom
			$gedcom  = self::getGedCom($requestonly);
			$_params->merge($gedcom);		
			
			// retrieve and merge the parameters of the theme
			$theme  = self::getTheme($requestonly, false);
			$_params->merge($theme);

			// retrieve and merge the parameters of the tree
			$tree  = self::getTreeParam($requestonly);
			$_params->merge($tree);		
		}
		
		return $_params;
	}
	
	public static function getActions($tree = true) {
		$user	= JFactory::getUser();
		$result	= new JObject;
		$appId  = self::getApplicationId();
		if ($tree) {
			$treeId = self::getTreeId();
			//$appId  = self::getApplicationId();
			$asset	= 'com_joaktree.application.'.$appId.'.tree.'.$treeId;
		} else {
			//$appId  = self::getAppId();
			$asset	= 'com_joaktree.application.'.$appId;
		}

		$actions = array(
			'core.create', 'core.edit', 'core.delete', 'core.edit.state', 
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $asset));
		}
		
		// special treatement for media - we take the authorisation from media manager
		$result->set('media.create', $user->authorise('core.create', 'com_media'));
		
		return $result;
	}
	
	function getUserAccess() {
		static $_userAccess;
		
		if (!isset($_userAccess)) {
			$user = JFactory::getUser();
			$_userAccess		= $user->getAuthorisedViewLevels();
		}
		
		return $_userAccess;
	}
	
	function getUserAccessLevels() {
		static $_userAccessLevels;
		
		if (!isset($_userAccessLevels)) {
			$_userAccessLevels	= '('.implode(",",self::getUserAccess()).')';
		}
		
		return $_userAccessLevels;
	}
	
 	function getTreeId($intern = false, $requestonly = false) {
		static $_treeId;	

		if (!isset($_treeId)) {
			$db = JFactory::getDBO();
			$input = JFactory::getApplication()->input;			
			
			$tmp1 = $input->get('treeId', null, 'string');
			$tmp2 = $input->get('treeId', null, 'int');
			
			if (empty($tmp2) && (!$requestonly)) {
				// no tree id in request, try the parameters.
				$params = self::getJTParams($requestonly);			
				$tmp1 = $params->get('treeId');		
 				$tmp2 = (int) $tmp1; 			
			}	
			
			if (empty($tmp2)) {
				// no treeId is given in request
				if ($intern) {
					// Function is called from getPersonId
					// That means that there is also no person given in request.
					die('wrong request');
					
				} else {
					$personId 	= JoaktreeHelper::getPersonId(true, $requestonly);					 
					$app_id     = JoaktreeHelper::getApplicationId(true, $requestonly);  
					$levels 	= self::getUserAccessLevels() ;
					
					if (isset($personId) and isset($app_id) and isset($levels)) {								
						$query = $db->getQuery(true);
						$query->select(' jan.default_tree_id ');
						$query->from(  ' #__joaktree_admin_persons  jan ');
						$query->innerJoin(' #__joaktree_trees  jte '
											.'ON     (   jte.app_id = jan.app_id '
											.'       AND jte.id     = jan.default_tree_id '
											.'       ) ');
						$query->where(' jan.app_id    = '.$app_id.' ');
						$query->where(' jan.id        = '.$db->Quote( $personId ).' ');
						$query->where(' jan.published = true ');
						$query->where(' jte.access    IN '.$levels.' ');
						$query->where(' jte.published = true ');								
						$db->setQuery( $query );
						$tmp3 = $db->loadResult(); 
						
						if ($error = $db->getErrorMsg()) {
							throw new JException($error);
						}
						
						if ($tmp3 == null) {
							// Nothing is retrieved. Person does not exists or has an emtpy default tree.
							// We set treeId to 0, and let the standard no access message be the result.
							$tmp3 = 0;
						}
					} else {
						$tmp3 = 0;
					}
				}				
			} else {
				// something is given -> check this
				if ($tmp1 !== (string)$tmp2) {
					die('wrong request');
				} else if ($tmp2 <= 0) {
					die('wrong request');
				} else {
					$tmp3 = intval($tmp2);				
				}	
			}
			
			$_treeId = $db->escape($tmp3);
			
		}		

		return $_treeId;
	}
	
	function getPersonId($intern = false, $requestonly = false) {
		// static $initCharacters;
		static $_personId;
		
		if (!isset($_personId)) {
			$input = JFactory::getApplication()->input;
 			$tmp = $input->get('personId', null, 'string');
			
			// Load the parameters.
			$db = JFactory::getDBO();
			
 			if ((empty($tmp)) && (!$requestonly)) {
				// no person id in request, try the parameters.
 				$params = self::getJTParams($requestonly);	
 				$tmp = $params->get('personId');
 			}		

 			if (!empty($tmp)) {
 				// break the string into app_id and personId
 				$tmp2 = explode('!', $tmp);
 				// continue with personId
 				$tmp  = $tmp2[1];
 			}			

 			if (strlen($tmp) > (int) self::getIdlength()) {
				die('wrong request');
								
			} else if ((!isset($tmp) or ($tmp == null)) && (!$requestonly)) {
				// no person given in request: find the root person of the tree
				if ($intern) {
					// Function is called from getTreeId
					// That means that there is also no tree given in request.
					die('wrong request');
				} else {
					$treeId = intval( $this->getTreeId(true) );
					$levels	= self::getUserAccessLevels();
					
					if (isset($treeId) and ($treeId > 0) and isset($levels)) {
						$query = $db->getQuery(true);
						$query->select(' root_person_id ');
						$query->from(  ' #__joaktree_trees ');
						$query->where( ' id = '.$treeId.' ');
						$query->where( ' access IN '.$levels.' ');						
						$db->setQuery( $query );
						$tmp = $db->loadResult(); 
						
						if ($error = $db->getErrorMsg()) {
							throw new JException($error);
						}
					}
				}
			}
			
 			if (!isset($tmp) or ($tmp == null)) {
				$_personId = null;
			} else {
				if (!isset($params)) {
					$params = self::getJTParams($requestonly);
				}

				$colon = $params->get('colon', 0);
				if ($colon) {
					$_personId = str_replace ( ':' , '-' , $db->escape($tmp));
				} else {				
					$_personId = $db->escape($tmp);
				}
			} 
		}

		return $_personId;
	}
	
	function getApplicationId($intern = false, $requestonly = false) {
		static $_appId;
		
		if (!isset($_appId)) {
			$db = JFactory::getDBO();
			$app = JFactory::getApplication('site');
			$params = $app->getParams();
			$input  = $app->input;
			
			$tmp = $input->get('personId', null, 'string');	
 			
 			if (empty($tmp) && (!$requestonly)) {
				// no person id in request, try the parameters.
	 			$tmp = $params->get('personId');
 			}	
 				
 			if (!empty($tmp)) {
	 			// break the string into app_id and personId
	 			$tmp2 = explode('!', $tmp);
	 			// continue with application id
	 			$tmp  = (int) $tmp2[0];
	 			
	 			if ($tmp == 0) {
	 				// somehing is wrong
					die('wrong request');
	 			}
 			} else {
 				// No personId found -> look for appId
				$tmp1 = $input->get('appId', null, 'string');
				$tmp  = $input->get('appId', null, 'int');
			
				if (empty($tmp1) && (!$requestonly))  {
					// no app id in request, try the parameters.
					$tmp1 = $params->get('appId');		
 					$tmp  = (int) $tmp1; 			
				}

				if (!empty($tmp)) {
					// something is given -> check this
					if ($tmp1 !== (string)$tmp) {
						die('wrong request');
					} else if ($tmp <= 0) {
						die('wrong request');
					} 	
				}	
 			}					

			if ((!isset($tmp) or ($tmp == null)) && (!$requestonly)) {
				// no person given in request: find application id of the tree
				if ($intern) {
					// Function is called from getTreeId
					// That means that there is also no tree given in request.
					die('wrong request');
				} else {
					//$treeId = intval( $this->getTreeId(true) );
					$treeId = intval( self::getTreeId(true, $requestonly) );
					$levels	= self::getUserAccessLevels();
					
					if (isset($treeId) and ($treeId > 0) and isset($levels)) {
						$query = $db->getQuery(true);
						$query->select(' app_id ');
						$query->from(  ' #__joaktree_trees ');
						$query->where( ' id = '.$treeId.' ');
						$query->where( ' access IN '.$levels.' ');						
						$db->setQuery( $query );
						$tmp = $db->loadResult(); 
						
						if ($error = $db->getErrorMsg()) {
							throw new JException($error);
						}						
					}
				}
			}
			
			$_appId = (int) $db->escape($tmp);
		}

		return $_appId;
	}
		
	function getApplicationName($intern = false, $requestonly = false) {
		static $_appName;
		
		if (!isset($_appName)) {
			$appId = self::getApplicationId($intern, $requestonly);
			
			if (isset($appId) && (int) $appId > 0) {
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				
				$query->select(' title ');
				$query->from(  ' #__joaktree_applications ');
				$query->where( ' id = '.(int) $appId.' ');					
				$db->setQuery( $query );
				$_appName = $db->loadResult(); 
			} else {
				$_appName = ''; 
			}
		}

		return $_appName;
	}
	
	function getRelationId() {
		static $_relationId;
		
		if (!isset($_relationId)) {
			$input = JFactory::getApplication()->input;
			$tmp = $input->get('relationId', null, 'string');

			if (empty($tmp)) {
				// no relationId is given in request
				$_relationId = null;					
			} else if (strlen($tmp) > (int) self::getIdlength()) {
				die('wrong request');
			} else {
				$_relationId = $tmp;
			}	
		}

		return $_relationId;
	}
		
	function getRepoId($optional = false) {
		static $_repoId;
		
		if (!isset($_repoId)) {
			$input = JFactory::getApplication()->input;
			$tmp   = $input->get('repoId', null, 'string');
			
			if (empty($tmp)) {
				// no repo Id is given in request
				if ($optional) {
					$_repoId = null;					
				} else {
					die('wrong request');	
				}
			} else if (strlen($tmp) > (int) self::getIdlength()) {
				die('wrong request');
			} else {
				$_repoId = $tmp;
			}	
		}

		return $_repoId;
	}
	
	function getSourceId($optional = false) {
		static $_sourceId;
		
		if (!isset($_sourceId)) {
			$input = JFactory::getApplication()->input;
			$tmp   = $input->get('sourceId', null, 'string');
			
			if (empty($tmp)) {
				// no source Id is given in request
				if ($optional) {
					$_sourceId = null;					
				} else {
					die('wrong request');	
				}
			} else if (strlen($tmp) > (int) self::getIdlength()) {
				die('wrong request');
			} else {
				$_sourceId = $tmp;
			}	
		}

		return $_sourceId;
	}
	
	public function getAction() {
 		static $_action;
 		
 		if (!isset($_action)) {
 			$input = JFactory::getApplication()->input;
 			$tmp = $input->get('action');
 			if ($tmp == 'select') {
 				$_action = $tmp;
 			} else if ($tmp == 'edit') {
 				$_action = $tmp;
 			} else if ($tmp == 'save') {
 				$_action = $tmp;
 			} else if ($tmp == 'saveparent1') {
 				$_action = $tmp;
 			} else if ($tmp == 'addparent') {
 				$_action = $tmp;
 			} else if ($tmp == 'addpartner') {
 				$_action = $tmp;
 			} else if ($tmp == 'addchild') {
 				$_action = $tmp;
 			} else{
 				$_action = 'maintain';
 			}
 		}
 		
		return $_action;
	}
		
	public function getReturnObject() {
 		static $_returnObject;
 		
 		if (!isset($_returnObject)) {
 			$input = JFactory::getApplication()->input;
 			$tmp   = $input->get('retId', null, 'string');
 			$_returnObject = json_decode(base64_decode($tmp));
 		}
 		
		return $_returnObject;
	}
	
	public function getTmpl() {
 		static $_tmpl;
 		
 		if (!isset($_tmpl)) {
 			$input = JFactory::getApplication()->input;
 			$tmp   = $input->get('tmpl');
 			$_tmpl = ($tmp == 'component') ? $tmp : null;
 		}
 		
		return $_tmpl;
	}
		
	public function generateJTId() {
		$config = array();
		$table	= JTable::getInstance('joaktree_registry_items', 'table', $config);
		
		// retrieve the value of the counter
		$table->loadUK('ID_COUNTER');
		
		// set the counter for the new record
		$counter = (int)$table->value + 1;

		// save the counter
		$table->regkey = 'ID_COUNTER';
		$table->value  = $counter;
		$table->storeUK();		
		
		return 'JT'.sprintf('%08d' ,$counter);
	}
	
	function getTechnology() {
		static $_technology;
		
		$app 		= JFactory::getApplication('site');
		$input 		= $app->input;
		$params 	= $app->getParams();
		$indCookie 	= $params->get('indCookies', true);		

		if ($indCookie) {
			$cookie = new JInputCookie();
		}
		
		// use cookies
		if (!isset($_technology)) {
			// Find the value for tech - first from the cookie when cookies are used
			if ($indCookie) {
				$tmp	= $cookie->get('tech'); 
			}
			
			if (!isset($tmp)) {
				// if not found in cookie -> look in url
				$tmp	= $input->get('tech');
			}
			
			if (!isset($tmp)) {
				// if not found in url -> set default value of 'a'
				$_technology	= 'a';
			} else if (($tmp != 'a') and ($tmp != 'j') and ($tmp != 'b')) {
				// if technology is wrong value -> set default value of 'a'
				$_technology	= 'a';
			} else {
				$_technology	= $tmp;
			}		
		}
		
		if ($indCookie) {
			// set up a cookie for non default value
			if ($_technology != 'a') {
				//set a cookie for 1 hour = 3600 seconds
				$expire = time()+60*60;
				//setcookie("tech", $_technology, time()+3600, "/","", 0);
			} else {
				//delete cookie by setting 1 hour (= 3600 seconds) in the past
				$expire = time()-60*60;
				//setcookie("tech", $_technology, time()-3600, "/","", 0);
			}
			$cookie->set('tech', $_technology, $expire, '/');
		}

		return $_technology;
	}
	
	function getAccess() {
		static $_access;
		
		if (!isset($_access)) {
			$db 	= JFactory::getDBO();	
			$treeId = intval( $this->getTreeId() );
			$userAccess = self::getUserAccess();

			// determine the access of the user to the tree
			if (isset($treeId) and (intval($treeId) > 0)) {
				// only execute this query when the tree is known
				$query = $db->getQuery(true);
				$query->select(' jte.access ');
				$query->from(  ' #__joaktree_trees  jte ');
				$query->where( ' jte.published = true ');
				$query->where( ' jte.id = ' . $treeId.' ');						
				$db->setQuery( $query );
				$jte_access = $db->loadResult();
				
				if ($error = $db->getErrorMsg()) {
					throw new JException($error);
				}						
				
				if (isset($jte_access) && isset($userAccess) && in_array($jte_access, $userAccess)) {
					// access to tree is true, but is there a valid person?
					$personId = JoaktreeHelper::getPersonId(); 
					
					if (!isset($personId) or ($personId == null)) {
						// no personID found, therefore no access
						$_access = false;
					} else {
						// personId found, check whether person is accessible
						$person = $this->getPerson();

						if (!isset($person->id) or ($person->id == null)) {
							// no person found, therefore no access
							$_access = false;
						} else {
							// check whether person and tree-id are related
							$query = $db->getQuery(true);
							$query->select(' 1 AS result ');
							$query->from(  ' #__joaktree_tree_persons  jtp ');
							$query->where( ' jtp.app_id    = '.$person->app_id.' ');
							$query->where( ' jtp.person_id = '.$db->Quote($person->id).' ');						
							$query->where( ' jtp.tree_id   = ' . $treeId.' ');						
							$db->setQuery( $query );
							
							$related = $db->loadResult();
							
							if ($error = $db->getErrorMsg()) {
								throw new JException($error);
							}						
							
							if ($related) {
								// tree and person are linked, therefore access is allowed
								$_access = true;
							} else {
								// tree and person are not linked, access is denied
								$_access = false;
							}
						}
					}
				} else {
					// access to tree is false
					$_access = false;
				}		
				
			} else {
				// tree is not known, therefore no access
				$_access = false;
			}
		}
						
		return $_access;
	}
		
	function getAccessGedCom() {
		static $_gedcomAccess;
		
		if (!isset($_gedcomAccess)) {
			$db 		= JFactory::getDBO();	
			$appId 		= intval( self::getApplicationId() );
			$userAccess = self::getUserAccess();
			$_gedcomAccess = false;

			// determine the access of the user to any of the trees related to the gedcom
			if (isset($appId) && ($appId > 0)) {
				// only execute this query when the appId is known
				$query = $db->getQuery(true);
				$query->select(' jte.id ');
				$query->select(' jte.access ');
				$query->from(  ' #__joaktree_trees  jte ');
				$query->where( ' jte.published = true ');
				$query->where( ' jte.app_id = ' . $appId.' ');						
				$db->setQuery( $query );
				$trees = $db->loadObjectList();
				
				if ($error = $db->getErrorMsg()) {
					throw new JException($error);
				}	
					
				foreach ($trees as $tree) {				
					if (isset($userAccess) && in_array($tree->access, $userAccess)) {
						// access to tree is true
						$_gedcomAccess = true;
					}	
				}				
			}
		}
						
		return $_gedcomAccess;
	}
	
	function getModuleId() {
		static $moduleId;
		
		if (!isset($moduleId)) {
			$input = JFactory::getApplication()->input;
			$tmp1  = $input->get('module', null, 'int');
			if (isset($tmp1)) {
				$tmp2 = (int) $tmp1;
				
				if ($tmp2 == $tmp1) {				
					$moduleId = $tmp2;
				} else {
					// somehing is wrong
					die('wrong request');
				}
			} else {
				// ModuleId is not part of request
				$moduleId = null;
			}
		}
		
		return $moduleId;
	}
	
	function getDay() {
		static $day;
		
		if (!isset($day)) {
			//Filter
			$app 	 = JFactory::getApplication('site');
			$tmp1	 = $app->getUserStateFromRequest('com_joaktree.tmya.day',	'day',	'',	'int' );
			if (isset($tmp1)) {
				$tmp2 = (int) $tmp1;
				
				if (($tmp2 == $tmp1) && ($tmp2 >= 0) && ($tmp2 <= 31)) {				
					$day = $tmp2;
				} else {
					// somehing is wrong
					die('wrong request');
				}
			} else {
				// Day is not part of request
				$day = 0;
			}
		}
		
		return $day;
	}
	
	function getMonth() {
		static $month;
		
		if (!isset($month)) {
			//Filter
			$app 	 = JFactory::getApplication('site');
			$tmp1	 = $app->getUserStateFromRequest('com_joaktree.tmya.month',	'month','',	'int' );
			
			if (isset($tmp1)) {
				$tmp2 = (int) $tmp1;
				
				if (($tmp2 == $tmp1) && ($tmp2 >= 0) && ($tmp2 <= 12)) {				
					$month = $tmp2;
				} else {
					// somehing is wrong
					die('wrong request');
				}
			} else {
				$month = 0;
			}
		}
		
		return $month;
	}
	
	function getAccessTree() {
		static $_accessTree;
		
		if (!isset($_accessTree)) {
			$db 	= JFactory::getDBO();	
			$treeId = intval( self::getTreeId() );
			$userAccess = self::getUserAccess();
			
			if (isset($treeId) && !empty($treeId)) {
				// only execute this query when the tree is known
				$query = $db->getQuery(true);
				$query->select(' jte.access ');
				$query->from(  ' #__joaktree_trees  jte ');
				$query->where( ' jte.published = true ');
				$query->where( ' jte.id = ' . $treeId.' ');
									
				$db->setQuery( $query );
				$jte_access = $db->loadResult();
				
				if (isset($jte_access) && isset($userAccess) && in_array($jte_access, $userAccess) ) {
					$_accessTree = true;
				} else {
					$_accessTree = false;
				}		
				
			} else {
				// tree is not known
				$_accessTree = false;
			}
		}

		return $_accessTree;
	}
	
	function getDisplayAccess($public = false) {
		static $_displayAccess;
		
		if (!isset($_displayAccess)) {
			$db = JFactory::getDBO();
			if ($public) {
				$levels = '(1)';
			} else {
				$levels = JoaktreeHelper::getUserAccessLevels();
			}			
			
			// value 0: not shown to current user based on access
			// value 1: alternative text is shown to current user based on access
			// value 2: value is shown to current user based on access
			$query = $db->getQuery(true);

			$attribs = array();
			$attribs[] = 'code';
			$attribs[] = 'level';
			$concatTxt = $query->concatenate($attribs);	
			
			$query->select(' '.$concatTxt.' AS code ');
			$query->select(' IF( published '
						  .'   , IF( (access IN '.$levels.') '
						  .'       , 2 '
						  .'       , 0 '
						  .'       ) '
						  .'   , 0 '
						  .'   )  AS notLiving '
						  );
			$query->select(' IF( published '
						  .'   , IF( (accessLiving IN '.$levels.') '
						  .'       , 2 '
						  .'       , IF( (altLiving IN '.$levels.') '
						  .'           , 1 '
						  .'           , 0 '
						  .'           ) '
						  .'       ) '
						  .'   , 0 '
						  .'   )  AS living '
						  );
			$query->select(' code  AS gedcomtag ');
			$query->from(  ' #__joaktree_display_settings ');
			
			$db->setQuery($query);
			
			$_displayAccess = $db->loadObjectList('code');		
		}
		
		return $_displayAccess;
	}
	
	public function getSEF() {
		static $_sef;
		
		if (!isset($_sef)) {
			$config = JFactory::getConfig();
			$_sef = $config->get('sef');
		}
		
		return $_sef;
	}
	
	// stylesheets
	public function joaktreecss($theme = null) {		
		$ds = '/';
		if (empty($theme)) {
			return 'components'.$ds.'com_joaktree'.$ds.'assets'.$ds.'css'.$ds.'joaktree.css';
		} else {
			return 'components'.$ds.'com_joaktree'.$ds.'themes'.$ds.$theme.$ds.'theme.css';
		}
	}
	
	public function shadowboxcss() {		
		$ds = '/';
		return 'components'.$ds.'com_joaktree'.$ds.'assets'.$ds.'shadowbox'.$ds.'shadowbox.css';
	}
	
	public function briaskcss() {		
		$ds = '/';
		return 'components'.$ds.'com_joaktree'.$ds.'assets'.$ds.'css'.$ds.'mod_briaskISS.css';
	}
	
	// javascript
	public function joaktreejs($jtscript) {		
		$ds = '/';
		return JURI::base(true).$ds.'components'.$ds.'com_joaktree'.$ds.'assets'.$ds.'js'.$ds.$jtscript;
	}
	
	public function shadowboxjs() {		
		$ds = '/';
		return JURI::base(true).$ds.'components'.$ds.'com_joaktree'.$ds.'assets'.$ds.'shadowbox'.$ds.'shadowbox.js';		
	}
	
	
	public function getMenus($view) {
		$db = JFactory::getDBO();	
		
		// retrieve the menu item ids - if not done yet
		$levels	= self::getUserAccessLevels();
		
		$_menuTreeId 	= array();
		$query = $db->getQuery(true);
		$query->select(' id ');
		$query->from(  ' #__joaktree_trees ');
		$query->where( ' access IN '.$levels.' ');
					
		$db->setQuery( $query );
		$treeIds = $db->loadColumn();	
		
		foreach ($treeIds as $treeId) {
			$_menuTreeId [ $treeId ] = self::getMenuId( $treeId, $view);
		}

		return $_menuTreeId;
	}
	
	private function getMenuId( $tree_id, $view) {
		$menu 		= &JSite::getMenu();
		$component	= &JComponentHelper::getComponent('com_joaktree');
		$items		= $menu->getItems('component_id', $component->id);
		$itemFound	= false;
		$itemid		= array();	

		// Search for an appropriate menu item.
		if (is_array($items)) {			
			if ($view == 'joaktree') {
				// (1) search for view "joaktree" with the given tree_id
				if ($itemFound == false) {
					foreach ($items as $item) {
						if (   $menu->authorise($item->id)
						   and $item->query['view'] == 'joaktree'
						   and $item->params->get('treeId') == $tree_id 
						   ) {
							$itemFound = true;
							$itemid[]  = $item->id;
						}
					}
				}
			}
			
			if (($view == 'joaktree') or ($view == 'joaktreelist')){
				// Nothing found at step (1)
				// (2) search for view joaktreelist with the given tree_id
				if ($itemFound == false) {
					foreach ($items as $item) {
						if (   $menu->authorise($item->id)
						   and $item->query['view'] == 'joaktreelist'
						   and $item->params->get('treeId') == $tree_id 
						   ) {
							$itemFound = true;
							$itemid[]  = $item->id;
						}
					}
				}
			}
			
			// Nothing found at step (2)
			// (3) search for view "joaktreestart" with the given tree_id
			if ($itemFound == false) {
				foreach ($items as $item) {
					if (   $menu->authorise($item->id)
					   and $item->query['view'] == 'joaktreestart'
					   and $item->params->get('treeId') == $tree_id 
					   ) {
						$itemFound = true;
						$itemid[]  = $item->id;
					}
				}
			}

			// Nothing found at step (3)
			// (4) search for any view with the given tree_id
			if ($itemFound == false) {
				foreach ($items as $item) {
					if (   $menu->authorise($item->id)
					   and $item->params->get('treeId') == $tree_id 
					   ) {
						$itemFound = true;
						$itemid[]  = $item->id;
					}
				}
			}
			
			// No items for tree_id - continue search
			if ($view == 'joaktree') {
				// Nothing found at step (4)
				// (5) search for view "joaktree" with any tree_id
				if ($itemFound == false) {
					foreach ($items as $item) {
						if (   $menu->authorise($item->id)
						   and $item->query['view'] == 'joaktree'
						   ) {
							$itemFound = true;
							$itemid[]  = $item->id;
						}
					}
				}
			}
			
			if (($view == 'joaktree') or ($view == 'joaktreelist')){
				// Nothing found at step (5)
				// (6) search for view "joaktreelist" with any tree_id
				if ($itemFound == false) {
					foreach ($items as $item) {
						if (   $menu->authorise($item->id)
						   and $item->query['view'] == 'joaktreelist'
						   ) {
							$itemFound = true;
							$itemid[]  = $item->id;
						}
					}
				}
			}
				
			// Nothing found at step (6)
			// (7) search for view "joaktreestart" with any tree_id
			if ($itemFound == false) {
				foreach ($items as $item) {
					if (   $menu->authorise($item->id)
					   and $item->query['view'] == 'joaktreestart'
					   ) {
						$itemFound = true;
						$itemid[]  = $item->id;
					}
				}
			}
			
			// Nothing found at step (7)
			// (8) search for any view with any tree_id
			if ($itemFound == false) {
				foreach ($items as $item) {
					if (   $menu->authorise($item->id)
					   ) {
						$itemFound = true;
						$itemid[]  = $item->id;
					}
				}
			}
		}
		
		if ($itemFound) {
			// one or more items are found during the search
			// take the item with the lowest id.
			sort($itemid);
			//$tmp = $menu->setActive($itemid[0]);
			$menuItemId = $itemid[0]; //$tmp->id;
		} else {
			// No items are found during the search
			// continue with the active menu
			$menuItem   = &$menu->getActive();

			if (isset($menuItem)) {
				$menuItemId = $menuItem->id;
			} else {
				$menuItemId = null;
			}
		}

		return $menuItemId;
	}

	/* 
	** function for retrieving version number from config.xml
	*/
	private function getJoaktreeVersion() {
		// get the folder and xml-files
		$folder = JPATH_ADMINISTRATOR .DS. 'components'.DS.'com_joaktree';
		if (JFolder::exists($folder)) {
			$xmlFilesInDir = JFolder::files($folder, '.xml$');
		} else {
			$folder = JPATH_SITE .DS. 'components'.DS.'com_joaktree';
			if (JFolder::exists($folder)) {
				$xmlFilesInDir = JFolder::files($folder, '.xml$');
			} else {
				$xmlFilesInDir = null;
			}
		}
		
		// loop through the xml-files
		$xml_items = '';
		if (count($xmlFilesInDir))
		{
			foreach ($xmlFilesInDir as $xmlfile)
			{
				if ($data = JInstaller::parseXMLInstallFile($folder.DS.$xmlfile)) {
					foreach($data as $key => $value) {
						$xml_items[$key] = $value;
					}
				}
			}
		}
		
		// return the found version
		if (isset($xml_items['version']) && $xml_items['version'] != '' ) {
			return $xml_items['version'];
		} else {
			return '';
		}
	}
	
	/* 
	** function for retrieving copyright string
	*/
	function getJoaktreeCR() {
		static $crText;
		
		if (!isset($crText)) {
			$currentYear = strftime('%Y');
			$crText = '';
			
			$crText .= 'Joaktree ';
			$crText .= JoaktreeHelper::getJoaktreeVersion().' ';
			$crText .= '(2009-'.$currentYear.')';
		}
		
		return $crText; 
	}
	
	/* 
	** function for retrieving last update (general)
	*/
	function lastUpdateDateTime() {
		static $_lastUpdateDateTime;

		if (!isset($_lastUpdateDateTime)) {
			$db 	= JFactory::getDBO();	
			// Load the parameters.
			$params = self::getJTParams();	
			$showUpdate  = $params->get('show_update');
			
			if ( $showUpdate == 'N' ) {
				$_lastUpdateDateTime	= null;
			} else {
				$query = $db->getQuery(true);
				$query->select(' DATE_FORMAT( value, "%e %b %Y" ) ');
				$query->from(  ' #__joaktree_registry_items ');
				$query->where( ' regkey = "LAST_UPDATE_DATETIME" ');
								
				$db->setQuery( $query );
				$result = $db->loadResult();
				
				if ($result) {
					$_lastUpdateDateTime	= JText::_('JT_LASTUPDATED').': '.JoaktreeHelper::convertDateTime($result);
				} else {
					$_lastUpdateDateTime	= null;
				}
			}
		}
		
		return $_lastUpdateDateTime;
	}
	
	/* 
	** function for retrieving last update (general)
	*/
	function lastUpdateDateTimePerson($dateTime) {
		static $_lastUpdateDateTimePerson;

		if (!isset($_lastUpdateDateTimePerson)) {
			// Load the parameters.
			$params = self::getJTParams();	
			$showUpdate  = $params->get('show_update');
			
			if (( $showUpdate == 'N' ) or ($dateTime == null)) {
				$_lastUpdateDateTimePerson	= null;
			} else {								
				$_lastUpdateDateTimePerson	= JText::_('JT_LASTUPDATED').': '.JoaktreeHelper::convertDateTime($dateTime);
			}
		}
		
		return $_lastUpdateDateTimePerson;
	}
	
	function convertDateTime($dateTimeString) {
		$result = $dateTimeString;
		
		$result = str_replace ('Jan', JText::_('January')  , $result ); 
		$result = str_replace ('Feb', JText::_('February') , $result ); 
		$result = str_replace ('Mar', JText::_('March')    , $result ); 
		$result = str_replace ('Apr', JText::_('April')    , $result ); 
		$result = str_replace ('May', JText::_('May')      , $result ); 
		$result = str_replace ('Jun', JText::_('June')     , $result ); 
		$result = str_replace ('Jul', JText::_('July')     , $result ); 
		$result = str_replace ('Aug', JText::_('August')   , $result ); 
		$result = str_replace ('Sep', JText::_('September'), $result ); 
		$result = str_replace ('Oct', JText::_('October')  , $result ); 
		$result = str_replace ('Nov', JText::_('November') , $result ); 
		$result = str_replace ('Dec', JText::_('December') , $result ); 
		
		return $result;
	}

	function displayDate($dateString) {
		if ($dateString == JText::_('JT_ALTERNATIVE')) {
			return $dateString;
		}
		
		$result = strtoupper( $dateString );
		
		// Distinguish between BEF and BEFORE
		if (substr_count($result, 'BEFORE') == 0) {
			$result = str_replace ('BEF', JText::_('JT_BEFORE'), $result );
		} else { 
			$result = str_replace ('BEFORE', JText::_('JT_BEFORE'), $result );
		} 

		// Distinguish between BEF and BEFORE
		if (substr_count($result, 'AFTER') == 0) {
			$result = str_replace ('AFT', JText::_('JT_AFTER'), $result );
		} else { 
			$result = str_replace ('AFTER', JText::_('JT_AFTER'), $result );
		}
		 
		$result = str_replace ('ABT', JText::_('JT_ABOUT'), $result ); 
		$result = str_replace ('BET', JText::_('JT_BETWEEN'), $result ); 
		$result = str_replace ('AND', JText::_('JT_AND'), $result ); 
		$result = str_replace ('FROM', JText::_('JT_FROM'), $result ); 
		$result = str_replace ('TO',  JText::_('JT_TO'), $result ); 
		$result = str_replace ('JAN', JText::_('JT_JAN'), $result ); 
		$result = str_replace ('FEB', JText::_('JT_FEB'), $result ); 
		$result = str_replace ('MAR', JText::_('JT_MAR'), $result ); 
		$result = str_replace ('APR', JText::_('JT_APR'), $result ); 
		$result = str_replace ('MAY', JText::_('JT_MAY'), $result ); 
		$result = str_replace ('JUN', JText::_('JT_JUN'), $result ); 
		$result = str_replace ('JUL', JText::_('JT_JUL'), $result ); 
		$result = str_replace ('AUG', JText::_('JT_AUG'), $result ); 
		$result = str_replace ('SEP', JText::_('JT_SEP'), $result ); 
		$result = str_replace ('OCT', JText::_('JT_OCT'), $result ); 
		$result = str_replace ('NOV', JText::_('JT_NOV'), $result ); 
		$result = str_replace ('DEC', JText::_('JT_DEC'), $result ); 
		
		return $result;
	}
	
	function arabicToRomanNumeral($arabicNumeral) { 
	    //$table = array('M'=>1000, 'CM'=>900, 'D'=>500, 'CD'=>400, 'C'=>100, 'XC'=>90, 'L'=>50, 'XL'=>40, 'X'=>10, 'IX'=>9, 'V'=>5, 'IV'=>4, 'I'=>1); 
	    $table = array('L'=>50, 'XL'=>40, 'X'=>10, 'IX'=>9, 'V'=>5, 'IV'=>4, 'I'=>1); 
	    $romanNumeral = ''; 
	    $integer = (int) $arabicNumeral;
	    while($integer > 0) 
	    { 
	        foreach($table as $rom=>$arb) 
	        { 
	            if($integer >= $arb) 
	            { 
	                $integer 		-= $arb; 
	                $romanNumeral 	.= $rom; 
	                break; 
	            } 
	        } 
	    } 
	
	    return $romanNumeral; 
	} 
	
	function displayEnglishCounter($number) {
		$integer = (int) $number;
		
		switch($integer) {
			case  1:	$counter = 'JT_FIRST'; break;
			case  2:	$counter = 'JT_SECOND'; break;
			case  3:	$counter = 'JT_THIRD'; break;
			case  4:	$counter = 'JT_FOURTH'; break;
			case  5:	$counter = 'JT_FIFTH'; break;
			case  6:	$counter = 'JT_SIXTH'; break;
			case  7:	$counter = 'JT_SEVENTH'; break;
			case  8:	$counter = 'JT_EIGHTH'; break;
			case  9:	$counter = 'JT_NINTH'; break;
			case 10:	$counter = 'JT_TENTH'; break;
			case 11:	$counter = 'JT_ELEVENTH'; break;
			case 12:	$counter = 'JT_TWELFTH'; break;
			case 13:	$counter = 'JT_THIRTEENTH'; break;
			case 14:	$counter = 'JT_FOURTEENTH'; break;
			case 15:	$counter = 'JT_FIFTEENTH'; break;
			case 16:	$counter = 'JT_SIXTEENTH'; break;
			case 17:	$counter = 'JT_SEVENTEENTH'; break;
			case 18:	$counter = 'JT_EIGHTEENTH'; break;
			case 19:	$counter = 'JT_NINETEENTH'; break;
			case 20:	$counter = 'JT_TWENTIETH'; break;
			default:	$counter = 'JT_NEXT'; break;
		}
		
		return $counter;
		
	}
	
	public function getIndNotesTable($app_id) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select(' id ');
		$query->from(  ' #__joaktree_notes ');
		$query->where( ' app_id = '.(int) $app_id.' ');
		
		$db->setQuery($query, 0, 1);
		$tmp = $db->loadResult();
		
		return ((isset($tmp)) ? true : false);
	}
		
	public function getTheme($requestonly = false, $default = false) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select(' jth.name AS theme ');
		$query->select(' jth.params ');
		
		if ($default) {
			// retrieve the default theme
			$query->from(  ' #__joaktree_themes  jth ');
			$query->where( ' jth.home   = true ');						
			
		} else {
			$treeId = self::getTreeId(false, $requestonly);
			
			// retrieve the theme linked to the tree
			$query->from(  ' #__joaktree_trees   jte ');
			$query->innerJoin(' #__joaktree_themes  jth '
							 .' ON (jth.id = jte.theme_id) '
							 );
			$query->where( ' jte.id   = '.(int) $treeId.' ');
		}

		// retrieve the name
		$db->setQuery($query);
		$theme = $db->loadObject();

		$registry = new JRegistry;
		// load parameters into registry object
		$registry->loadString($theme->params, 'JSON');
		unset($theme->params);
		
		// load the rest of the object into registry object
		$registry->loadObject($theme);
		
		return $registry;
	}
	
	public function getTreeParam($requestonly = false) {
		$db = JFactory::getDBO();
		
		$treeId = self::getTreeId(false, $requestonly);
		$registry = new JRegistry;
		
		// retrieve the name
		if (empty($treeId)) {
			// nothing to return, but an empty registry
			return $registry;
			
		} else {	
			// retrieve the tree parameters
			$query = $db->getQuery(true);
			$query->select(' jte.name AS treeName ');
			$query->select(' jte.indPersonCount ');
			$query->select(' jte.indMarriageCount ');
			$query->select(' jte.robots AS treeRobots ');
			$query->from(  ' #__joaktree_trees   jte ');
			$query->where( ' jte.id   = '.(int) $treeId.' ');			
		}
		
		$db->setQuery($query);
		$tree = $db->loadObject();
		
		if (is_object($tree)) {
			// load the object into registry object
			$registry->loadObject($tree);
		}
				
		return $registry;
	}
	
	public function getGedCom($requestonly = false) {
		$db = JFactory::getDBO();
		$registry = new JRegistry;
		
		$appId = self::getApplicationId(false, $requestonly);
		
		if (empty($appId)) {
			// nothing to return, but an empty registry
			return $registry;
			
		} else {			
			// retrieve the params
			$query = $db->getQuery(true);
			$query->select(' japp.title AS gedcomName ');
			$query->select(' japp.params ');
			$query->from(  ' #__joaktree_applications   japp ');
			$query->where( ' japp.id   = '.(int) $appId.' ');
					
			$db->setQuery($query);
			$gedcom = $db->loadObject();
	
			if (is_object($gedcom)) {
				// load parameters into registry object
				$registry->loadString($gedcom->params, 'JSON');
				unset($gedcom->params);
				
				// load the rest of the object into registry object
				$registry->loadObject($gedcom);
			}
		}
				
		return $registry;
	}
	
	public function stringRobots($robot) {
		switch ($robot) {
			case 1: $return = 'index, follow';
					break;
			case 2: $return = 'noindex, follow';
					break;
			case 3: $return = 'index, nofollow';
					break;
			case 4: $return = 'noindex, nofollow';
					break;
			case 0: // continue
			default: $return = '';
					break;
		}
		
		return $return;
	}
	
	/* ======================== */
	public function getJoinAdminPersons($includeAltNames = true, $tab = 'jpn', $num = 0) {
		$displayAccess		= JoaktreeHelper::getDisplayAccess();

		switch ($num) {
			case 1: $jan = 'jan1';
					$person_id = 'person_id_1';
					break;
			case 2: $jan = 'jan2';
					$person_id = 'person_id_2';
					break;
			case 0:	// continue
			default:
					$jan = 'jan';
					if ($tab == 'jpn') {
						$person_id = 'id';
					} else {
						$person_id = 'person_id';
					}
					break;
		}
		
		$join = 
			 ' #__joaktree_admin_persons '.$jan.' '
			.' ON (   '.$jan.'.app_id    = '.$tab.'.app_id '
			.'    AND '.$jan.'.id        = '.$tab.'.'.$person_id.' '
			.'    AND '.$jan.'.published = true ';
			
		if ($includeAltNames) {
	        // privacy filter
			$join .= '    AND (  ('.$jan.'.living = false AND '.$displayAccess['NAMEname']->notLiving.' > 0 ) '
					.'        OR ('.$jan.'.living = true  AND '.$displayAccess['NAMEname']->living.'    > 0 ) '
					.'        ) '
					.'    ) ';
		} else {
			$join .= '    AND (  ('.$jan.'.living = false AND '.$displayAccess['NAMEname']->notLiving.' = 2 ) '
					.'        OR ('.$jan.'.living = true  AND '.$displayAccess['NAMEname']->living.'    = 2 ) '
					.'        ) '
					.'    ) ';
		}
		
		return $join;
	}
	
	private function _getConcatenatedName($attribs, $privacyeFilter = true) {
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$concat = $query->concatenate($attribs, ' ');
			
			if ($privacyeFilter) {
				$displayAccess		= JoaktreeHelper::getDisplayAccess();	
				$selectName = ' IF( (jan.living = true AND '.$displayAccess['NAMEname']->living.' = 1 ) '
							 .'    , '.$db->Quote( JText::_('JT_ALTERNATIVE') ).' '
							 .'    , '.$concat.' '
							 .'    ) ';
			} else {
				$selectName = $concat;
			}

			return $selectName;
	}
	
	public function getConcatenatedFamilyName($privacyeFilter = true) {
		static $concatTxt;
		
		if (empty($concatTxt)) {
			$attribs = array();
			$attribs[] = 'jpn.namePreposition';
			$attribs[] = 'jpn.familyName';
			$concatTxt = self::_getConcatenatedName($attribs, $privacyeFilter);	
		}
		
		return ' '.$concatTxt.' ';
	}
	
	public function getConcatenatedDutchFamilyName($privacyeFilter = true) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$attribs = array();
		$attribs[] = 'jpn.familyName';
		$attribs[] = 'jpn.namePreposition';
		$concat = $query->concatenate($attribs, ', ');
		
		if ($privacyeFilter) {
			$displayAccess		= JoaktreeHelper::getDisplayAccess();	
			$concatTxt  = ' IF( (jan.living = true AND '.$displayAccess['NAMEname']->living.' = 1 ) '
						 .'   , NULL '
						 .'   , IF( jpn.namePreposition = '.$db->Quote('').' '
						 .'       , jpn.familyName '
						 .'       , '.$concat.' '
						 .'       ) '
						 .'   ) ';
		} else {
			$concatTxt  = ' IF( jpn.namePreposition = '.$db->Quote('').' '
						 .'   , jpn.familyName '
						 .'   , '.$concat.' '
						 .'   ) ';
		}
		
		return ' '.$concatTxt.' ';
	}
	
	public function getConcatenatedFullName($privacyeFilter = true) {
		static $concatTxt;
		
		if (empty($concatTxt)) {
			$attribs = array();
			$attribs[] = 'jpn.firstName';
			$attribs[] = 'jpn.namePreposition';
			$attribs[] = 'jpn.familyName';
			$concatTxt = self::_getConcatenatedName($attribs, $privacyeFilter);	
		}
		
		return ' '.$concatTxt.' ';
	}
	
	public function getSelectFirstName($privacyeFilter = true) {
		if ($privacyeFilter) {
			//$db = JFactory::getDBO();
			$displayAccess		= JoaktreeHelper::getDisplayAccess();	
			$selectName = ' IF( (jan.living = true AND '.$displayAccess['NAMEname']->living.' = 1 ) '
						 .'   , NULL '
						 //.'   , '.$db->Quote( JText::_('JT_ALTERNATIVE') ).' '
						 .'   , jpn.firstName '
						 .'   ) ';
		} else {
			$selectName = ' jpn.firstName ';
		}

		return $selectName;
	}
	
	public function getSelectPatronym($privacyeFilter = true) {
		if ($privacyeFilter) {
			$displayAccess		= JoaktreeHelper::getDisplayAccess();	
			$selectName = ' IF( (jan.living = true AND '.$displayAccess['NAMEname']->living.' = 1 ) '
						 .'   , NULL '
						 .'   , jpn.patronym '
						 .'   ) ';
		} else {
			$selectName = ' jpn.patronym ';
		}

		return $selectName;
	}
	
	public function getSelectBirthYear($privacyeFilter = true) {
		if ($privacyeFilter) {
			$db 			= JFactory::getDBO();
			$displayAccess	= JoaktreeHelper::getDisplayAccess();	
			$column = ' IF( (jan.living = true AND '.$displayAccess['BIRTperson']->living.' = 1 ) '
					 .'   , '.$db->Quote( JText::_('JT_ALTERNATIVE') ).' '
					 .'   , SUBSTR( RTRIM(birth.eventDate), -4 ) '
					 .'   ) ';
		} else {
			$column = ' SUBSTR( RTRIM(birth.eventDate), -4 ) ';
		}

		return $column;
	}
	
	public function getSelectDeathYear($privacyeFilter = true) {
		if ($privacyeFilter) {
			$db 			= JFactory::getDBO();
			$displayAccess	= JoaktreeHelper::getDisplayAccess();	
			$column = ' IF( (jan.living = true AND '.$displayAccess['DEATperson']->living.' = 1 ) '
					 .'   , '.$db->Quote( JText::_('JT_ALTERNATIVE') ).' '
					 .'   , SUBSTR( RTRIM(death.eventDate), -4 ) '
					 .'   ) ';
		} else {
			$column = ' SUBSTR( RTRIM(death.eventDate), -4 ) ';
		}

		return $column;
	}
	
	public function getJoinBirth() {
		$db = JFactory::getDBO();
		$displayAccess		= JoaktreeHelper::getDisplayAccess();			
		
		$join = 
			 ' #__joaktree_person_events birth '
			.' ON (   birth.app_id    = jpn.app_id '
			.'    AND birth.person_id = jpn.id '
			.'    AND birth.code      = '.$db->Quote('BIRT').' '
			.'    AND (  (jan.living = false AND '.$displayAccess['BIRTperson']->notLiving.' > 0 ) '
			.'        OR (jan.living = true  AND '.$displayAccess['BIRTperson']->living.'    > 0 ) '
			.'        ) '
			.'    ) ';
			
		return $join;
	}

	public function getJoinDeath() {
		$db = JFactory::getDBO();
		$displayAccess		= JoaktreeHelper::getDisplayAccess();			
		
		$join = 
			 ' #__joaktree_person_events death '
			.' ON (   death.app_id    = jpn.app_id '
			.'    AND death.person_id = jpn.id '
			.'    AND death.code      = '.$db->Quote('DEAT').' '
			.'    AND (  (jan.living = false AND '.$displayAccess['DEATperson']->notLiving.' > 0 ) '
			.'        OR (jan.living = true  AND '.$displayAccess['DEATperson']->living.'    > 0 ) '
			.'        ) '
			.'    ) ';
			
		return $join;
	}
	
	
}
?>
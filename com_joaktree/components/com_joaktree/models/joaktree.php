<?php 
/**
 * Joomla! component Joaktree
 * file		front end joaktree model - joaktree.php
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
JLoader::import('helper.person', JPATH_COMPONENT);


class JoaktreeModelJoaktree extends JModelLegacy {
	
	function __construct() {
		parent::__construct();            		
	} 
	
	public function getUserAccess() {
		return JoaktreeHelper::getUserAccess();
	}
	
	public function getAction() {
		return JoaktreeHelper::getAction();
	}
	
	public function getApplicationId() {
		return JoaktreeHelper::getApplicationId();
 	}
	
	public function getTreeId() {
		return JoaktreeHelper::getTreeId();
 	}
	
	public function getPersonId() {
		return JoaktreeHelper::getPersonId();	
	}
	
	public function getTechnology() {
		return JoaktreeHelper::getTechnology();
	}

	public function getAccess() {
		return JoaktreeHelper::getAccess();
	}
	
	public function getPerson() {
		static $person;
		if (!isset($person)) {
			$id = array();
			$id[ 'app_id' ]		= $this->getApplicationId(); 
			$id[ 'tree_id' ]	= $this->getTreeId(); 
			$id[ 'person_id' ]	= $this->getPersonId(); 
			$person	=  new Person($id, 'full');		
		}
		
		return $person;
	}
	
	public function setCookie() {
		static $indOneTime;
		
		if (isset($indOneTime) && ($indOneTime)) {
			return true;
		}
		
		// set up cookie
		$params = JoaktreeHelper::getJTParams();
		$indCookie  = $params->get('indCookies', true);	
		if ($indCookie) {
			// we fetch the cookie
			$cookie = new JInputCookie();
			$tmp	= $cookie->get('jt_last_persons', '', 'string'); 
			
			// prepare the array
			if ($tmp) { 
				$personList = (array) json_decode(base64_decode($tmp));
			} else {
				$personList = array();
			}
			
			// check whether this person is already in array
			$person = & $this->getPerson();
			$value  = $person->app_id.'!'.$person->id.'!'.$person->tree_id;
			if (in_array($value, $personList)) {
				// loop through array and move person to first position
				$newList   = array();
				$newList[] = $value;
				foreach ($personList as $item) {
					if ($item != $value) {
						$newList[] = $item;
					}
				}
								
			} else {
				// place the first person to start of array
				$newList = $personList;
				array_unshift($newList, $value);				
			}
			
			// if the array is too big, remove the last person
			if (count($newList) > 10) { array_pop($newList); }
										
			// and store the new cookie
			//$expire = mktime().time()+60*60*24*180;
			$expire = time()+60*60*24*180;
			$cookie->set('jt_last_persons', base64_encode(json_encode($newList)), $expire, '/');
		}
		
		$indOneTime = true;
		return true;
	}
	
	public function getLayout() {
		if (!isset($this->_layout)) {
			$input = JFactory::getApplication()->input;
			$tmp   = $input->get('layout', null, 'word');
			
			if (isset($tmp)) {
				if (  $tmp == 'default'
				   or $tmp == '_children'
				   or $tmp == '_detailnotes'
				   or $tmp == '_detailsources'
				   or $tmp == '_grandchildren'
				   or $tmp == '_grandparents'
				   or $tmp == '_mainnotes'
				   or $tmp == '_mainsources'
				   or $tmp == '_names'
				   or $tmp == '_parents'
				   or $tmp == '_partnerevents'
				   or $tmp == '_partners'
				   or $tmp == '_personevents'
				   or $tmp == '_sourceornotebutton'
				   or $tmp == '_sourceornotetext'
				   or $tmp == '_information'
				   or $tmp == '_article'
				   ) {
					$this->_layout = $tmp;
				} else {
					$this->_layout = null;
				}
			} else {
				$this->_layout = null;
			}
		}
		
		return $this->_layout;
	}
	
	public function getJtType() {
		if (!isset($this->_jttype)) {
			$input = JFactory::getApplication()->input;
			$tmp   = $input->get('type', null, 'word');

			if (isset($tmp)) {
				if (  $tmp == 'person'
				   or $tmp == 'name'
				   or $tmp == 'relation'
				   or $tmp == 'note'
				   or $tmp == 'article'
				   ) {
					$this->_jttype = $tmp;
				} else {
					$this->_jttype = null;
				}
			} else {
				$this->_jttype = null;
			}
		}
		
		return $this->_jttype;
	}
	
	public function getJtSubType() {
		if (!isset($this->_jtsubtype)) {
			$input = JFactory::getApplication()->input;
			$tmp   = $input->get('subtype', null, 'word');

			if (isset($tmp)) {
				if (  $tmp == 'personAll'
				   or $tmp == 'person'
				   or $tmp == 'pevent'
				   or $tmp == 'name'
				   or $tmp == 'note'
				   or $tmp == 'relation'
				   or $tmp == 'revent'
				   ) {
					$this->_jtsubtype = $tmp;
				} else {
					$this->_jtsubtype = null;
				}
			} else {
				$this->_jtsubtype = null;
			}
		}
		
		return $this->_jtsubtype;
	}

	public function getOrderNumber() {
		if (!isset($this->_orderNumber)) {
			$input = JFactory::getApplication()->input;
			$tmp   = $input->get('orderNumber', 0, 'int');

			$this->_orderNumber = intval($tmp);

		}
		
		return $this->_orderNumber;
	}

	public function getRelationId() {
		return JoaktreeHelper::getRelationId();
	}
		
}
?>
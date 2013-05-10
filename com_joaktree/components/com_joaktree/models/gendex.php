<?php
/**
 * Joomla! component Joaktree
 * file		gendex model - gendex.php
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
jimport('joomla.html.parameter');

require_once(JPATH_SITE.DS.'components'.DS.'com_joaktree'.DS.'helper'.DS.'helper.php');


class JoaktreeModelGendex extends JModelLegacy { 
		
	function __construct() {
		parent::__construct();		
	}
			
	public function getItems() {
		// information is only selected for level: public
		$public = true;		

		$userAccessLevels = '(1)';
		$displayAccess    = JoaktreeHelper::getDisplayAccess($public);
						
		// retrieve persons
		$db				= $this->getDbo();
		$query			= $db->getQuery(true);
		
		// select the basics
		$query->select(' jpn.app_id ');
		$query->select(' jpn.id ');
		$query->select(JoaktreeHelper::getSelectFirstName().' AS firstName ');
		$query->select(JoaktreeHelper::getConcatenatedFamilyName().' AS familyName ');
		$query->from(  ' #__joaktree_persons  jpn ');
		
		// privacy filter
		$query->select(' jan.default_tree_id  AS treeId ');
		$query->innerJoin(JoaktreeHelper::getJoinAdminPersons(false));
		$query->innerJoin(' #__joaktree_trees  jte '
						 .' ON (   jte.app_id    = jan.app_id '
						 .'    AND jte.id        = jan.default_tree_id '
						 .'    AND jte.published = true '
						 .'    AND jte.access    IN '.$userAccessLevels.' '
						 // only trees with Gendex = yes (=2)
						 .'    AND jte.indGendex = 2 '
						 .'    ) '
						 );
		
		// birth info
		$query->select(' birth.eventDate  AS birthDate ');
		$query->select(' birth.location   AS birthPlace ');
		$query->leftJoin(' #__joaktree_person_events  birth '
						.' ON (   birth.app_id    = jpn.app_id '
						.'    AND birth.person_id = jpn.id '
						.'    AND birth.code      = '.$this->_db->Quote('BIRT').' '
						// no alternative text is shown 
						.'    AND (  (jan.living = false AND '.$displayAccess['BIRTperson']->notLiving.' = 2 ) '
						.'        OR (jan.living = true  AND '.$displayAccess['BIRTperson']->living.'    = 2 ) '
						.'        ) '
						.'    ) '
						);
		
		// death info
		$query->select(' death.eventDate  AS deathDate ');
		$query->select(' death.location   AS deathPlace ');
		$query->leftJoin(' #__joaktree_person_events  death '
						.' ON (   death.app_id    = jpn.app_id '
						.'    AND death.person_id = jpn.id '
						.'    AND death.code = '.$this->_db->Quote('DEAT').' '
						// no alternative text is shown 
						.'    AND (  (jan.living = false AND '.$displayAccess['DEATperson']->notLiving.' = 2 ) '
						.'        OR (jan.living = true  AND '.$displayAccess['DEATperson']->living.'    = 2 ) '
						.'        ) '
						.'    ) '
						);
		
						 
		//$query = 'SELECT    jpn.app_id '
		//		.',         jpn.id '
		//		.',         jan.default_tree_id      AS treeId '
		//		.',         jpn.firstName '
		//		.',         CONCAT_WS('.$this->_db->quote(' ').' '
		//		.'                   , jpn.namePreposition '
		//		.'                   , jpn.familyName '
		//		.'                   )               AS familyName '
		//		// no alternative text is shown 
		//		.',         birth.eventDate          AS birthDate '
		//		.',         birth.location           AS birthPlace '
		//		.',         death.eventDate          AS deathDate '
		//		.',         death.location           AS deathPlace '				
		//		.'FROM      #__joaktree_persons           jpn '
		//		.'JOIN      #__joaktree_admin_persons     jan '
		//		.'ON        (   jan.app_id    = jpn.app_id '
		//		.'          AND jan.id        = jpn.id '
		//		.'          AND jan.published = true '
	    //        // privacy filter
		//		.'          AND (  (jan.living = false AND '.$displayAccess['NAMEname']->notLiving.' = 2 ) '
		//		.'              OR (jan.living = true  AND '.$displayAccess['NAMEname']->living.'    = 2 ) '
		//		.'              ) '
		//		.'          ) '
		//		.'JOIN      #__joaktree_trees             jte '
		//		.'ON        (   jte.app_id    = jan.app_id '
		//		.'          AND jte.id        = jan.default_tree_id '
		//		.'          AND jte.published = true '
		//		.'          AND jte.access    IN '.$userAccessLevels.' '
		//		// only trees with Gendex = yes (=2)
		//		.'          AND jte.indGendex = 2 '
		//		.'          ) '
		//		.'LEFT JOIN #__joaktree_person_events birth '
		//		.'ON        (   birth.app_id    = jpn.app_id '
		//		.'          AND birth.person_id = jpn.id '
		//		.'          AND birth.code      = '.$this->_db->Quote('BIRT').' '
		//		// no alternative text is shown 
		//		.'          AND (  (jan.living = false AND '.$displayAccess['BIRTperson']->notLiving.' = 2 ) '
		//		.'              OR (jan.living = true  AND '.$displayAccess['BIRTperson']->living.'    = 2 ) '
		//		.'              ) '
		//		.'          ) '
		//		.'LEFT JOIN #__joaktree_person_events death '
		//		.'ON        (   death.app_id    = jpn.app_id '
		//		.'          AND death.person_id = jpn.id '
		//		.'          AND death.code = '.$this->_db->Quote('DEAT').' '
		//		// no alternative text is shown 
		//		.'          AND (  (jan.living = false AND '.$displayAccess['DEATperson']->notLiving.' = 2 ) '
		//		.'              OR (jan.living = true  AND '.$displayAccess['DEATperson']->living.'    = 2 ) '
		//		.'              ) '
		//		.'          ) ';

		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		
		return $result;
	}
}
?>

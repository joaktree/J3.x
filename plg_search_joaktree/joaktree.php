<?php
/**
 * Joomla! plugin Joaktree search
 *
 * @version	1.5.0
 * @author	Niels van Dantzig
 * @package	Joomla
 * @subpackage	Joaktree
 * @license	GNU/GPL
 *
 *
 */
 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
jimport('joomla.plugin.plugin');

require_once(JPATH_BASE.DS.'components'.DS.'com_joaktree'.DS.'helper'.DS.'helper.php');

  
//Then define a function to return an array of search areas. 
class plgSearchJoaktree extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
		$this->loadLanguage('com_joaktree.gedcom', JPATH_ADMINISTRATOR);		
	}
	
	function onContentSearchAreas()
	{
			static $areas;
			
			if (!isset($areas)) {
				
		    	//Get the parameters!  
	        	$notes		= $this->params->def('search_notes', 0);
	        	
	        	if ($notes == 1) {
					$areas = array(
						  'joaktreeName'    => JText::_('JTSRCH_GENEALOGY').':&nbsp;'.JText::_('JTSRCH_NAMES')
						, 'joaktreeNote'	=> JText::_('JTSRCH_GENEALOGY').':&nbsp;'.JText::_('JTSRCH_NOTES')
						);
	        	} else {
					$areas = array('joaktreeName' => JText::_('JTSRCH_GENEALOGY'));
	        	}
	        	
			}
		
	 	
		
	        return $areas;
	}
	 
	//Then the real function has to be created. The database connection should be made. 
	//The function will be closed with an } at the end of the file.
	//function plgSearchjoaktree( $text, $phrase='', $ordering='', $areas=null )
	function onContentSearch($text, $phrase='', $ordering='', $areas=null)
	{			
			$db  =& JFactory::getDBO();
			$linkBase	= 'index.php?option=com_joaktree&view=joaktree';
	 
	        //If the array is not correct, return it:
	        if (is_array( $areas )) {
	                if (!array_intersect( $areas, array_keys( $this->onContentSearchAreas() ) )) {
	                        return array();
	                }
	        }
	 	 
	        //And define the parameters. For example like this..
	        $limit	= $this->params->def('search_limit',		50);
	                
	        if (is_array( $areas )) {
	        	// check whether names are searched
				$names 	  = in_array( 'joaktreeName', $areas ) ? 1 : 0;
	        	$patronym = in_array( 'joaktreeName', $areas ) ? $this->params->def('search_patronyms', 0) : 0;
				
				// check whether notes are searched
				$notes = in_array( 'joaktreeNote', $areas ) ? $this->params->def( 'search_notes', 0 ) : 0;
				
	        } else {
	        	$names	= 1;
	        	$notes 	= $this->params->def( 'search_notes', 0 );
	        	$patronym	= $this->params->def('search_patronyms', 0);
	        }
	        
	        $linkType	= $this->params->def('link_option', 1);
	        
	 
	        //Use the function trim to delete spaces in front of or at the back of the searching terms
			$text = trim( $text );
	 
			//Return Array when nothing was filled in
			if ($text == '') {
	                return array();
	        }
	        
	        //replace joaktree
	        $searchJoaktree = JText::_( 'JTSRCH_GENEALOGY' );
	        $searchNotes    = JText::_( 'JTSRCH_NOTES' );
	        
	        // user access
	        $userAccessLevels	= JoaktreeHelper::getUserAccessLevels();        
	        $displayAccess 		= JoaktreeHelper::getDisplayAccess();
	        
	        //After this, you have to add the database part.
	        $wheres = array();
	        switch ($phrase) {
	 
	        		//search exact
	                case 'exact':
	                        $text          = $db->Quote( '%'.$db->escape( $text, true ).'%', false );
	                        
	                        // search in names table
	                        $whereNames    = array();
	                        $whereNames[]  = 'LOWER(CONCAT_WS('.$db->Quote(' ').',jpn.firstName,jpn.namePreposition,jpn.familyName)) LIKE '.$text;
	                        $whereNames[]  = 'LOWER(CONCAT_WS('.$db->Quote(' ').',jpn.firstName,jpn.patronym,jpn.namePreposition,jpn.familyName)) LIKE '.$text;
	                        $whereName = '(' . implode( (') OR ('), $whereNames ) . ')';
	                        
	                        // search in person-notes table
	                        $wherePerNote   = 'LOWER(jpe.value) LIKE '.$text.' OR LOWER(jne.value) LIKE '.$text;
	                        
	                        // search in person-notes table
	                        $whereRelNote   = 'LOWER(jre.value) LIKE '.$text.' OR LOWER(jne.value) LIKE '.$text;
	                        
	                        break;
	 
	                //search all or any
	                case 'all':
	                case 'any':
	 
	                //set default
	                default:
	                        $words         = explode( ' ', $text );
	                        $whereNames    = array();
	                        $whereNotes    = array();
	                        $wherePerNotes = array();
	                        $whereRelNotes = array();
	
	                        foreach ($words as $word)
	                        {
	                                $word            = $db->Quote( '%'.$db->escape( $word, true ).'%', false );
	                                
	                                if ($patronym == 1) {
		                                $whereNames[]    = 'LOWER(jpn.firstName) LIKE '.$word
		                                					.'OR LOWER(jpn.patronym) LIKE '.$word
		                                					.'OR LOWER(jpn.namePreposition) LIKE '.$word
		                                					.'OR LOWER(jpn.familyName) LIKE '.$word;
	                                } else {
		                                $whereNames[]    = 'LOWER(jpn.firstName) LIKE '.$word
		                                					.'OR LOWER(jpn.namePreposition) LIKE '.$word
		                                					.'OR LOWER(jpn.familyName) LIKE '.$word;
	                                }
	                                
	                                $wherePerNotes[] = 'LOWER(jpe.value) LIKE '.$word.' OR LOWER(jne.value) LIKE '.$word;
	                                $whereRelNotes[] = 'LOWER(jre.value) LIKE '.$word.' OR LOWER(jne.value) LIKE '.$word;
	                        }
	                        
	                        $whereName = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $whereNames ) . ')';
	                        $wherePerNote = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wherePerNotes ) . ')';
	                        $whereRelNote = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $whereRelNotes ) . ')';
	                        
	                        break;
	        }
	 
	        //ordering of the results
	        switch ( $ordering ) {
	 
	        	//alphabetic, ascending
	                case 'alpha':
	                        $order = 'jpn.familyName ASC, jpn.firstName ASC';
	                        break;
	 
	                //oldest first
	                case 'oldest':
	 
	                //popular first
	                case 'popular':
	 
	                //newest first
	                case 'newest':
	 
	                //default setting: alphabetic, ascending
	                default:
	                        $order = 'jpn.familyName ASC, jpn.firstName ASC';
	        }
	 
	        //the database query; 
	        $query = '';
	        
	        if (($names == 1) && ($notes == 1)) {
	        	$query .= '( ';
	        }
	        
	        if ($names == 1) {
		        $query .= 'SELECT   CONCAT_WS('.$db->Quote(' ').' '
			        	.'                   , jpn.firstName '
			        	.                    (($patronym == 1) ? ', jpn.patronym ' : '') 
			        	.'                   , jpn.namePreposition '
			        	.'                   , jpn.familyName '
			        	.'                   )                  AS title '
			        	.',         CONCAT_WS( " / " '
			        	.'                   , '. $db->Quote($searchJoaktree) .' '
			        	.'                   , jte.name '
			        	.'                   )                  AS section '
						.',         jpn.lastUpdateTimeStamp    	AS created '
			        	.',         "'.$linkType.'"             AS browsernav '
			        	.',         jpn.app_id                  AS app_id '
			        	.',         jpn.id                      AS person_id '
			        	.',         jte.id                      AS tree_id '
						.',         IF ( (jan.living = true  AND '.$displayAccess['BIRTperson']->living.'    = 1) ' 
						.'             , '.$db->Quote( JText::_('JTSRCH_ALTERNATIVE') ).' '
						.'             , birth.eventDate '
						.'             )                        AS birthDate '
						.',         IF ( (jan.living = true  AND '.$displayAccess['DEATperson']->living.'    = 1) ' 
						.'             , '.$db->Quote( JText::_('JTSRCH_ALTERNATIVE') ).' '
						.'             , death.eventDate '
						.'             )                        AS deathDate '
						.',         NULL                        AS value '
			        	.'FROM      #__joaktree_persons         AS jpn '
						.'JOIN      #__joaktree_admin_persons   AS jan '
						.'ON        (   jan.app_id    = jpn.app_id '
						.'          AND jan.id        = jpn.id '
						.'          AND jan.published = true '
						            // privacy filter
						.'          AND (  (jan.living = false AND '.$displayAccess['NAMEname']->notLiving.' = 2 ) '
						.'              OR (jan.living = true  AND '.$displayAccess['NAMEname']->living.'    = 2 ) '
						.'              ) '
						.'          ) '
						.'LEFT JOIN #__joaktree_trees           AS jte '
						.'ON        (   jte.app_id    = jan.app_id '
						.'          AND jte.id        = jan.default_tree_id '
						.'          AND jte.published = true '
						.'          AND jte.access    IN '. $userAccessLevels
						.'          ) '
						.'LEFT JOIN #__joaktree_person_events birth '
						.'ON        (   birth.app_id    = jpn.app_id '
						.'          AND birth.person_id = jpn.id '
						.'          AND birth.code      = '.$db->Quote('BIRT').' '
						.'          AND (  (jan.living = false AND '.$displayAccess['BIRTperson']->notLiving.' > 0 ) '
						.'              OR (jan.living = true  AND '.$displayAccess['BIRTperson']->living.'    > 0 ) '
						.'              ) '
						.'          ) '
						.'LEFT JOIN #__joaktree_person_events death '
						.'ON        (   death.app_id    = jpn.app_id ' 
						.'          AND death.person_id = jpn.id '
						.'          AND death.code      = '.$db->Quote('DEAT').' '
						.'          AND (  (jan.living = false AND '.$displayAccess['DEATperson']->notLiving.' > 0 ) '
						.'              OR (jan.living = true  AND '.$displayAccess['DEATperson']->living.'    > 0 ) '
						.'              ) '
						.'          ) '
						.'WHERE ( '. $whereName .' ) '
						.' ';
	        }
	       
	        if (($names == 1) && ($notes == 1)) {
	        	$query .= ') UNION ( ';
	        }
					
			if ($notes == 1) {				
				// person notes
		        $query .= 'SELECT   CONCAT_WS('.$db->Quote(' ').' '
			        	.'                   , jpn.firstName '
			        	.                    (($patronym == 1) ? ', jpn.patronym ' : '') 
			        	.'                   , jpn.namePreposition '
			        	.'                   , jpn.familyName '
			        	.'                   )                  AS title '
			        	.',         CONCAT_WS( " / " '
			        	.'                   , '. $db->Quote($searchJoaktree) .' '
			        	.'                   , jte.name '
						.'                   , CONCAT_WS('.$db->Quote(' ').' '
			        	.'                              , jpn.firstName '
			        	.                               (($patronym == 1) ? ', jpn.patronym ' : '') 
			        	.'                              , jpn.namePreposition '
			        	.'                              , jpn.familyName '
			        	.'                              ) '	        	
			        	.'                   , '. $db->Quote($searchNotes) .' '
			        	.'                   )                  AS section '
						.',         jpn.lastUpdateTimeStamp    	AS created '
			        	.',         "'.$linkType.'"             AS browsernav '
			        	.',         jpn.app_id                  AS app_id '
			        	.',         jpn.id                      AS person_id '
			        	.',         jte.id                      AS tree_id '
						.',         NULL                        AS birthDate '
						.',         NULL                        AS deathDate '
						.',         IF( jne.value IS NOT NULL '
						.'            , jne.value '
						.'            , jpe.value '
						.'            )                         AS value '
						.'FROM      #__joaktree_person_notes    AS jpe '
						.'JOIN      #__joaktree_persons         AS jpn '
						.'ON        (   jpn.app_id = jpe.app_id '
						.'          AND jpn.id     = jpe.person_id '
						.'          ) '
						.'JOIN      #__joaktree_admin_persons   AS jan '
						.'ON        (   jan.app_id    = jpn.app_id '
						.'          AND jan.id        = jpn.id '
						.'          AND jan.published = true '
						            // privacy filter
						.'          AND (  (   jan.living = false '
						.'                 AND '.$displayAccess['NAMEname']->notLiving.'   = 2 '
						.'                 AND '.$displayAccess['NOTEperson']->notLiving.' = 2 '
						.'                 ) '
						.'              OR (   jan.living = true  '
						.'                 AND '.$displayAccess['NAMEname']->living.'      = 2 '
						.'                 AND '.$displayAccess['NOTEperson']->living.'    = 2 '
						.'                 ) '
						.'              ) '
						.'          ) '
						.'JOIN      #__joaktree_trees           AS jte '
						.'ON        (   jte.app_id    = jan.app_id '
						.'          AND jte.id        = jan.default_tree_id '
						.'          AND jte.published = true '
						.'          AND jte.access    IN '. $userAccessLevels
						.'          ) '
						.'LEFT JOIN #__joaktree_notes           AS jne '
						.'ON        (   jne.app_id = jpe.app_id '
						.'          AND jne.id     = jpe.note_id '
						.'          ) '
						.'WHERE ( '. $wherePerNote .' ) ';
			}
							
			// Order by name				
	        if (($names == 1) && ($notes == 1)) {
	        	$query .= ') ';
	        }
			
	        $query .= 'ORDER BY title '; //. $order;       
	 
			//Set query
	        $db->setQuery( $query, 0, $limit );
	        $rows = $db->loadObjectList();
	        
	        $menuJoaktree	= JoaktreeHelper::getMenus('joaktree');
	        $menu 			= &JSite::getMenu();
	        $menuActive		= &$menu->getActive();
	        $menuActId		= (isset($menuActive)) ? $menuActive->id : null;

	        foreach($rows as $key => $row) {
				$tmp = '';
				
	        	//The 'output' of the displayed link
	        	if (!empty($rows[$key]->tree_id)) {
	                $rows[$key]->href = JRoute::_($linkBase
	                			.'&Itemid='.$menuJoaktree[ $rows[$key]->tree_id ]
	                			.'&treeId='.$rows[$key]->tree_id
	                			.'&personId='.$rows[$key]->app_id.'!'.$rows[$key]->person_id
	                			);             			
	        	} else {
	        		// look for first child
			        $query = 'SELECT    jrn.app_id                  AS app_id '
			        		.',         jrn.person_id_1             AS person_id '
				        	.',         jte.id                      AS tree_id '
				        	.',         jrn.type                    AS relation '
				        	.'FROM      #__joaktree_relations       AS jrn '
							.'JOIN      #__joaktree_admin_persons   AS jan '
							.'ON        (   jan.app_id    = jrn.app_id '
							.'          AND jan.id        = jrn.person_id_1 '
							.'          AND jan.published = true '
							            // privacy filter
							.'          AND (  (   jan.living = false '
							.'                 AND '.$displayAccess['NAMEname']->notLiving.'   = 2 '
							.'                 ) '
							.'              OR (   jan.living = true  '
							.'                 AND '.$displayAccess['NAMEname']->living.'      = 2 '
							.'                 ) '
							.'              ) '
							.'          ) '
							.'JOIN      #__joaktree_trees           AS jte '
							.'ON        (   jte.app_id    = jan.app_id '
							.'          AND jte.id = jan.default_tree_id '
							.'          AND jte.published = true '
							.'          AND jte.access  IN '. $userAccessLevels
							.'          ) '
							.'WHERE     jrn.app_id        = '.$rows[$key]->app_id.' '
							.'AND       jrn.person_id_2   = '.$db->Quote($rows[$key]->person_id).' '
							.'AND       jrn.orderNumber_2 = 1 ';		        		
					//Set query
			        $db->setQuery( $query );
			        $child = $db->loadObject();	
			        	
					if ($child) {
		                $rows[$key]->href = JRoute::_($linkBase
		                			.'&Itemid='.(!empty($child->tree_id) ? $menuJoaktree[ $child->tree_id ] : $menuActId)
		                			.'&treeId='.(!empty($child->tree_id) ? $child->tree_id : '')
		                			.'&personId='.$child->app_id.'!'.$child->person_id
		                			);
					} else {
						// situation that the child is still living ... 
						$rows[$key]->href = '#';
					}
	        	}
	        	                			
				if (!empty($rows[$key]->birthDate)) {
					$tmp .= JText::_('JTSRCH_BORN').':&nbsp;';
					$tmp .= JoaktreeHelper::displayDate($rows[$key]->birthDate).';&nbsp;';
				}          			
				if (!empty($rows[$key]->deathDate)) {
					$tmp .= JText::_('JTSRCH_DIED').':&nbsp;';
					$tmp .= JoaktreeHelper::displayDate($rows[$key]->deathDate).';&nbsp;';
				}
				if (!empty($rows[$key]->value)) {
					$tmp .= $rows[$key]->value;
				}
				$rows[$key]->text = $tmp;       			
	        }
	        
	        return $rows;
	}
}

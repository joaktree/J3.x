<?php
/**
 * Joomla! component Joaktree
 * file		jt_relations model - jt_relations.php
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

class jt_relations extends JObject {
	/*
	** Function to find for every person in table his relation indicators
	** indHasParent, indHasPartner, and indHasChild.
	*/
	public function setRelationIndicators($appId, $persons = null) {
		$db     	= & JFactory::getDBO(); 
		$ret		= true;
		$indSubset 	= false;
		
		if (isset($persons) && is_array($persons)) {
			$indSubset = true;
			$personIds = "( '".implode("', '", $persons)."' )";
		}
		
		// First set the indications to 0
		$query = 'UPDATE #__joaktree_persons  jpn0 ' 
				.'SET    jpn0.indHasParent  = 0 '
				.',      jpn0.indHasPartner = 0 '
				.',      jpn0.indHasChild   = 0 '
				.'WHERE  jpn0.app_id = '.$appId.' ';
				
		if ($indSubset) {
			$query .= 'AND jpn0.id IN '.$personIds.' ';
		}
		
		if ($ret) {
			$db->setQuery( $query );
			$ret = $db->query();
		}
		
		// set up query for setting parents and partners
		$query = 'UPDATE #__joaktree_persons  jpn0 ' 
				.',      ( SELECT   jrn1.person_id_1  AS id '
				.'       ,          SUM( CASE jrn1.type ' 
				.'                       WHEN '.$db->Quote( 'father' ).' THEN 1 ' 
				.'                       WHEN '.$db->Quote( 'mother' ).' THEN 1 ' 
				.'                       ELSE 0 ' 
				.'                       END '  
				.'                     )              AS indHasParent ' 
				.',                 SUM( CASE jrn1.type ' 
				.'                       WHEN '.$db->Quote( 'partner' ).' THEN 1 ' 
				.'                       ELSE 0 ' 
				.'                       END ' 
				.'                     )              AS indHasPartner '
				.'       FROM     #__joaktree_relations  jrn1 ' 
				.'       WHERE    jrn1.app_id = '.$appId.' '
				.'       GROUP BY jrn1.person_id_1 '
				.'       )  jrn_iv ' 
				.'SET    jpn0.indHasParent  = CASE ' 
				.'                            WHEN jrn_iv.indHasParent > 0 '
				.'                            THEN 1 '
				.'                            ELSE 0 '
				.'                            END '
				.',      jpn0.indHasPartner = CASE ' 
				.'                            WHEN jrn_iv.indHasPartner > 0 '
				.'                            THEN 1 '
				.'                            ELSE 0 '
				.'                            END ' 
				.',      jpn0.indHasChild   = 0 '
				.'WHERE  jpn0.app_id = '.$appId.' '
				.'AND    jpn0.id     = jrn_iv.id ';
				
		if ($indSubset) {
			$query .= 'AND jpn0.id IN '.$personIds.' ';
		}
		
		if ($ret) {
			$db->setQuery( $query );
			$ret = $db->query();
		}
		
		// set up 2nd query for setting children and partners
		$query = 'UPDATE #__joaktree_persons  jpn0 ' 
				.',      ( SELECT   jrn1.person_id_2  AS id '
				.'       ,          SUM( CASE jrn1.type ' 
				.'                       WHEN '.$db->Quote( 'father' ).' THEN 1 ' 
				.'                       WHEN '.$db->Quote( 'mother' ).' THEN 1 ' 
				.'                       ELSE 0 ' 
				.'                       END '  
				.'                     )              AS indHasChild ' 
				.',                 SUM( CASE jrn1.type ' 
				.'                       WHEN '.$db->Quote( 'partner' ).' THEN 1 ' 
				.'                       ELSE 0 ' 
				.'                       END ' 
				.'                     )              AS indHasPartner '
				.'       FROM     #__joaktree_relations  jrn1 ' 
				.'       WHERE    jrn1.app_id = '.$appId.' '
				.'       GROUP BY jrn1.person_id_2 '
				.'       )  jrn_iv ' 
				.'SET    jpn0.indHasParent  = jpn0.indHasParent '
				.',      jpn0.indHasPartner = CASE ' 
				.'                            WHEN (jpn0.indHasPartner + jrn_iv.indHasPartner) > 0 '
				.'                            THEN 1 '
				.'                            ELSE 0 '
				.'                            END ' 
				.',      jpn0.indHasChild   = CASE ' 
				.'                            WHEN jrn_iv.indHasChild > 0 '
				.'                            THEN 1 '
				.'                            ELSE 0 '
				.'                            END '
				.'WHERE  jpn0.app_id = '.$appId.' '
				.'AND    jpn0.id     = jrn_iv.id ';
		
		if ($indSubset) {
			$query .= 'AND jpn0.id IN '.$personIds.' ';
		}
		
		if ($ret) {
			$db->setQuery( $query );
			$ret = $db->query();
		}
										
		return $ret;	
	}
}
?>

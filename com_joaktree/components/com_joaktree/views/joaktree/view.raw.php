<?php
/**
 * Joomla! component Joaktree
 * file		view joaktree person - view.html.php
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
class JoaktreeViewJoaktree extends JViewLegacy {
	function display($tpl = null) {
		// what is the layout
		$layout = $this->get( 'layout' );
		
		// Person 
		$this->person			= $this->get( 'person' );  
		$this->lists['technology']	= $this->get( 'technology' );	
		
		if (($layout == '_detailnotes') or ($layout == '_mainnotes')) {
			$notes[ 'type' ] = 		$this->get( 'jttype' );
			$notes[ 'subtype' ] = 		$this->get( 'jtsubtype' );
			$notes[ 'orderNumber' ] = 	$this->get( 'orderNumber' );
			$notes[ 'relation_id' ] = 	$this->get( 'relationId' );
			
			$this->assignRef( 'notes',	$notes );
		}
		
		if (($layout == '_detailsources') or ($layout == '_mainsources')) {		
			$sources[ 'type' ] = 		$this->get( 'jttype' );
			$sources[ 'subtype' ] = 	$this->get( 'jtsubtype' );
			$sources[ 'orderNumber' ] = 	$this->get( 'orderNumber' );
			$sources[ 'relation_id' ] = 	$this->get( 'relationId' );
			
			$this->assignRef( 'sources',	$sources );
		}

		if ($layout == '_article') {
			$notes[ 'type' ] 		= $this->get( 'jttype' );
			$notes[ 'orderNumber' ] = $this->get( 'orderNumber' );	
			$notes[ 'app_id' ] 		= $this->person->app_id;	
			$notes[ 'person_id' ] 	= $this->person->id;	
			
			$this->assignRef( 'notes',	$notes );
		}
		
		$canDo		 	= null;
		$this->assignRef( 'canDo',	$canDo);
		parent::display($tpl);
	}
}
?>
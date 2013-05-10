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
class JoaktreeViewDescendants extends JViewLegacy {
	function display($tpl = null) {
		$params			= JoaktreeHelper::getJTParams();
		$document		= &JFactory::getDocument();
		
		// set up style sheets and javascript files
		JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
		JHTML::stylesheet( JoaktreeHelper::joaktreecss($params->get('theme')) );
														
		// Access
		$lists['userAccess'] 	= $this->get( 'access' );
		$lists['treeId'] 		= $this->get( 'treeId' );
		$lists['technology'] 	= $this->get( 'technology' );
		
		// Person + generations
		$personId	 			= array();
		$this->person			= $this->get( 'person' );
		$personId[]		 		= $this->person->id.'|1';
		$lists[ 'startGenNum' ]	= 1;
		$lists[ 'endGenNum' ]	= (int) $params->get('descendantlevel', 20);
		$lists[ 'app_id' ]		= $this->person->app_id;
		
		// last update
		$lists[ 'lastUpdate' ]	=JoaktreeHelper::lastUpdateDateTimePerson($this->person->lastUpdateDate);
			
		// copyright
		$lists[ 'CR' ]		= JoaktreeHelper::getJoaktreeCR();
				
		$this->assignRef( 'personId', $personId);
		$this->assignRef( 'lists',	  $lists);
		
		if ($lists['userAccess']) {
			// set title, meta title
			$document->setTitle($this->person->fullName);
			$document->setMetadata('title', $this->person->fullName);
			
			// set additional meta tags
			if ($params->get('menu-meta_description')) {
				$document->setDescription($params->get('menu-meta_description'));
			}

			if ($params->get('menu-meta_keywords')) {
				$document->setMetadata('keywords', $params->get('menu-meta_keywords'));
			}

			// robots
			if ($this->person->robots > 0) {
				$document->setMetadata('robots', JoaktreeHelper::stringRobots($this->person->robots));
			} else if ($params->get('robots')) {
				$document->setMetadata('robots', $params->get('robots'));
			}
		}
		
		parent::display($tpl);
	}
}
?>
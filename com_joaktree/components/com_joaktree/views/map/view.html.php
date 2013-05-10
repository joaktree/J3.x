<?php
/**
 * Joomla! component Joaktree
 * file		view maps - view.html.php
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
class JoaktreeViewMap extends JViewLegacy {
	function display($tpl = null) {
		$this->lists	= array();
		$app 			= JFactory::getApplication('site');
		$document 		= &JFactory::getDocument();
		
		// Load the parameters.
		$this->map 		= $this->get( 'map' );
		$this->params	= $app->getParams();
		$this->params->merge(JoaktreeHelper::getTheme(true, true));	


		// set up style sheets and javascript files
		JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
		JHTML::stylesheet( JoaktreeHelper::joaktreecss($this->params->get('theme')) );
			
		if ($this->map->params['service'] == 'staticmap') {	
			// Get data from the model
			$this->mapview 				= $this->get( 'mapView' );
			$this->lists['userAccess']	= ($this->mapview) ? true : false;
			
			if ($this->lists['userAccess']) {
				// set title, meta title
				if ($this->map->params['name']) {
					$title = $this->map->params['name'];
					$document->setTitle($title);
					$document->setMetadata('title', $title);
				}
				
				// set additional meta tags
				if ($this->params->get('menu-meta_description')) {
					$document->setDescription($this->params->get('menu-meta_description'));
				}
	
				if ($this->params->get('menu-meta_keywords')) {
					$document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
				}
	
				if ($this->params->get('robots')) {
					$document->setMetadata('robots', $this->params->get('robots'));
				}		
			}
		} 
		
		if ($this->map->params['service'] == 'interactivemap') {
			$this->lists[ 'href' ] = 'index.php?option=com_joaktree'
									  		  .'&tmpl=component'
									  		  .'&format=raw'
									  		  .'&view=interactivemap'
											  .'&mapId='.$this->map->params['id'];
		}	
					
		// user interaction
		$this->lists[ 'mapHtmlId' ] = 'jt-map-id';
		$this->lists[ 'uicontrol' ] = $this->map->getUIControl($this->lists[ 'mapHtmlId' ]);
		
		// copyright
		$this->lists[ 'CR' ]		 = JoaktreeHelper::getJoaktreeCR();			
			
		parent::display($tpl);
	}
	
}
?>
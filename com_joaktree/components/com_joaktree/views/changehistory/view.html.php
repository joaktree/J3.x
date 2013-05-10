<?php
/**
 * Joomla! component Joaktree
 * file		view changehistory - view.html.php
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
class JoaktreeViewChangehistory extends JViewLegacy {
	function display($tpl = null) {
		$app 			= JFactory::getApplication('site');
		$document 		= &JFactory::getDocument();
		
		// Load the parameters.
		$params	= $app->getParams();
		$params->merge(JoaktreeHelper::getTheme(true, true));
		
		// set up style sheets and javascript files
		JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
		JHTML::stylesheet( JoaktreeHelper::joaktreecss($params->get('theme')) );
										
		// get user info
		$userId			= $this->get( 'userId' );
		if(!$userId || $userId == 0) {
			$document->addScript( JoaktreeHelper::joaktreejs('jtform.js'));
		}
		
		// Logs
		$this->name			= $this->get('personName');
		$this->items 		= $this->get('items');
		$this->pagination	= $this->get('pagination' );
		
		// check display method
		$tmpl				= $this->get( 'tmpl' ); 
		if ($tmpl) {			
			//return
			$retObject		= $this->get( 'returnObject' ); 
			if (!is_object($retObject)) {
				$retObject			= new stdClass;
				$retObject->object		= 'prsn';
			}			
			$this->lists['link'] = 'index.php?option=com_joaktree'
								  .'&view=changehistory' 
								  .'&tmpl='.$tmpl
								  .'&retId='.base64_encode(json_encode($retObject));							  
		} else {
			$this->lists['link'] = 'index.php?option=com_joaktree'
								  .'&view=changehistory'; 
		}
		
		$this->lists[ 'CR' ]= JoaktreeHelper::getJoaktreeCR();
	
		if (count($this->items) > 0) {
			// set title, meta title
			$title = ($this->name) ? $this->name : JText::_('JT_CHANGEHISTORY');
			$document->setTitle($title);
			$document->setMetadata('title', $title);
			
			// set additional meta tags
			if ($params->get('menu-meta_description')) {
				$document->setDescription($params->get('menu-meta_description'));
			}

			if ($params->get('menu-meta_keywords')) {
				$document->setMetadata('keywords', $params->get('menu-meta_keywords'));
			}

			if ($params->get('robots')) {
				$document->setMetadata('robots', $params->get('robots'));
			}		
		}
		
		parent::display($tpl);
	}
}
?>
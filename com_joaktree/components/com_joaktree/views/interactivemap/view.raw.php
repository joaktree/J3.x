<?php
/**
 * Joomla! component Joaktree
 * file		view interactive map - view.html.php
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

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Joaktree component
 */
class JoaktreeViewInteractivemap extends JViewLegacy {
	function display($tpl = null) {
		// Load the parameters.
		$this->map 		= $this->get( 'map' );
		
		if ($this->map->params['service'] == 'interactivemap') {
			$this->lists	= array();
			$app 			= JFactory::getApplication('site');
			//$document 		= &JFactory::getDocument();
		
			$this->params	= $app->getParams();
			$this->params->merge(JoaktreeHelper::getTheme(true, true));			

			// set up style declaration
			//$document->addStyleDeclaration($this->map->getStyleDeclaration());
			
			// add javascript
			$this->script = $this->map->getMapScript();
			if ($this->script) {
				$this->toolkit = $this->map->getToolkit();
				//if ($toolkit) {
				//	$document->addScript($toolkit);
				//}
				
				//$document->addScriptDeclaration($script);
				$this->lists['userAccess']	= true;
			} else {
				$this->lists['userAccess']	= false;
			}
			
			parent::display($tpl);
		} 
	}
}
?>
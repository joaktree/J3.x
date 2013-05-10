<?php
/**
 * Joomla! component Joaktree
 * file		view repository - view.html.php
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
class JoaktreeViewRepository extends JViewLegacy {
	protected $form;
	protected $item;
	
	function display($tpl = null) {

		if ($tpl == null) {
			$this->lists = array();
			$app 			= JFactory::getApplication('site');
			$document = &JFactory::getDocument();
			
			// Load the parameters.
			$this->params	= $app->getParams();
			$this->params->merge(JoaktreeHelper::getGedCom());
			$this->params->merge(JoaktreeHelper::getTheme(true, true));
			
			if ($this->params->get('siteedit', 1)) {	
				$this->canDo	= JoaktreeHelper::getActions(false);	
			} else {
				$this->canDo	= null;
			}
						
			// set up style sheets and javascript files
			JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
			JHTML::stylesheet( JoaktreeHelper::joaktreecss($this->params->get('theme')) );
												
			// Initialiase variables.
			$this->form					= $this->get('Form');
			$this->item					= $this->get('Item');		
			$this->lists['userAccess']	= $this->get( 'access' );
			
			$this->lists['action']		= $this->get( 'action' ); 
			if ($this->lists['action'] == 'select') {
				$this->lists['link'] = 'index.php?option=com_joaktree'
									  .'&view=repository'
									  .'&tmpl=component'
									  .'&action='.$this->lists['action'];
			} else {
				$this->lists['link'] = 'index.php?option=com_joaktree'
									  .'&view=repository';
			}
			
			$this->lists[ 'CR' ] = JoaktreeHelper::getJoaktreeCR();
		}
						
		if ($this->lists['userAccess']) {
			// set title, meta title
			if ($this->params->get('gedcomName')) {
				$title = $this->params->get('gedcomName');
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
				$document->setMetadata('robots',$this->params->get('robots'));
			}		
		}
					
		parent::display($tpl);
	}
}
?>
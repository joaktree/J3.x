<?php
/**
 * Joomla! component Joaktree
 * file		administrator jt_application view - view.html.php
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
jimport( 'joomla.application.component.view');

class JoaktreeViewJt_application extends JViewLegacy {

	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{	
		JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
				
		// Initialiase variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');		
		$this->state	= $this->get('State');		

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));		
			return false;
		}
		
		// use cookies
		$params  			= JComponentHelper::getParams('com_joaktree') ;
		$this->indCookie	= $params->get('indCookies', true);
		
		$this->canDo	= JoaktreeHelper::getActions();
		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);
		
		$isNew		= ($this->item->id == 0);

		JToolBarHelper::title($isNew ? JText::_('JTAPPS_TITLE_NEW') : JText::_('JTAPPS_TITLE_EDIT'), 'application');

		// If not checked out, can save the item.
		if ($this->canDo->get('core.edit')) {
			JToolBarHelper::apply('apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('save', 'JTOOLBAR_SAVE');
		}

		if (empty($this->item->id))  {
			JToolBarHelper::cancel('application.cancel','JTOOLBAR_CANCEL');
		} else {
			JToolBarHelper::cancel('application.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help('JoaktreeManuel', true, 'http://joaktree.com/index.php/en/joaktree/manual');		
	}
}

<?php
/**
 * Joomla! component Joaktree
 * file		front end repository controller - repository.php
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

class JoaktreeControllerRepository extends JoaktreeController {
	function __construct() {
		// first check token
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// create an input object
		$this->input = JFactory::getApplication()->input;

		//Get View
		if($this->input->get('view') == '') {
			$this->input->set('view', 'repository');
		}
		
		parent::__construct();
	}

	function display() {
		$action = $this->input->get('action');
		
		if ($action == 'select') {
			$this->input->set('tmpl', 'component');
			$this->input->set('action', $action);
		}

		parent::display();
	}
	
	function edit() {
		$appId 	= $this->input->get('appId', null, 'int');
		$cids	= $this->input->get( 'cid', null, 'array' ); 
		$action = $this->input->get('action');
		
		$link =  'index.php?option=com_joaktree'
						.'&view=repository'
						.'&layout=form_repository'
						.'&appId='.$appId
						.'&repoId='.$cids[0];

		if ($action == 'select') {
			$link .= '&tmpl=component'
					.'&action='.$action;
		}
		
		$this->setRedirect(Jroute::_($link), $msg);
	}
	
	function cancel() {
		$appId 	= $this->input->get('appId', null, 'int');
		$action = $this->input->get('action');
		
		$link = 'index.php?option=com_joaktree'
						.'&view=repositories'
						.'&appId='.$appId;
						
		if ($action == 'select') {
			$link .= '&tmpl=component'
					.'&action='.$action;
		}
						
		$this->setRedirect(Jroute::_($link), $msg);
	}
	
	function save() {
		$model = $this->getModel('repository');
		
		$form   = $this->input->get( 'jform', null, 'array' );
		$appId 	= $this->input->get('appId', null, 'int');
		$action = $this->input->get('action');
				 
		$ret = $model->save($form);
		
		if ($ret) {
			$link =  'index.php?option=com_joaktree'
					.'&view=repositories'
					.'&appId='.$appId
					.'&retId='.$ret;
			
			if ($action == 'select') {
				$link .= '&tmpl=component'
						.'&action='.$action;
			}
								
			$msg = '';
		} else {
			$link =  'index.php?option=com_joaktree'
					.'&view=repositories'
					.'&appId='.$appId;
			$msg = JText::_('JT_NOTAUTHORISED');	
		}
		$this->setRedirect(Jroute::_($link), $msg);
	}
	
	function delete() {
		$model = $this->getModel('repository');
		
		$form   = $this->input->get( 'jform', null, 'array' );
		$appId 	= $this->input->get('appId', null, 'int');
		$cids	= $this->input->get( 'cid', null, 'array' );
		
		$msg = $model->delete($appId, $cids[0]);
		
		$link =  'index.php?option=com_joaktree'
				.'&view=repositories'
				.'&appId='.$appId;
		$this->setRedirect(Jroute::_($link), $msg);
	}	
}
?>
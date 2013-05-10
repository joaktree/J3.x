<?php
/**
 * Joomla! component Joaktree
 * file		front end personform controller - personform.php
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

class JoaktreeControllerPersonform extends JoaktreeController {
	function __construct() {
		// first check token
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// create an input object
		$this->input = JFactory::getApplication()->input;

		//Get View
		if($this->input->get('view') == '') {
			$this->input->set('view', 'personform');
		}
		
		parent::__construct();
	}
	
	function delete() {
		$model = $this->getModel('personform');
		
		$personId = $this->input->get('personId', null, 'string');
		$treeId   = $this->input->get('treeId', null, 'int');

		$msg = $model->delete($personId);
		
		$link =  'index.php?option=com_joaktree'
						.'&view=joaktreelist'
						.'&tech=a'
						.'&treeId='.$treeId;
				
		$this->setRedirect(Jroute::_($link), $msg);
	}
	
	function edit() {
		$personId  = $this->input->get('personId', null, 'string');
		$treeId    = $this->input->get('treeId', null, 'int');
		$object    = $this->input->get('object', null, 'string');
		
		$link =  'index.php?option=com_joaktree'
						.'&view=personform'
						.'&tech=a'
						.'&treeId='.$treeId;
						
		switch ($object) {
			case "names":		$link .= '&layout=form_names'
										.'&personId='.$personId;
								break;
			case "state":		$link .= '&layout=form_state'
										.'&personId='.$personId;
								break;
			case "medialist":	$link .= '&layout=form_medialist'
										.'&personId='.$personId;
								break;
			case "media":		$picture = $this->input->get('picture', null, 'string');
								$link .= '&layout=form_media'
										.'&personId='.$personId
										.'&picture='.$picture;		
								break;
			case "notes":		$link .= '&layout=form_notes'
										.'&personId='.$personId;
								break;
			case "references":	$link .= '&layout=form_references'
										.'&personId='.$personId;
								break;
			case "pictures":	$link .= '&layout=form_pictures'
										.'&personId='.$personId;
								break;
			case "parents":		$link .= '&layout=form_parents'
										.'&personId='.$personId;
								break;
			case "partners":	$link .= '&layout=form_partners'
										.'&personId='.$personId;
								break;
			case "partnerevents":	
								$relationId = $this->input->get('relationId', null, 'string');
								$link .= '&layout=form_partner_events'
										.'&personId='.$personId
										.'&relationId='.$relationId;
								break;						
			case "children":	$link .= '&layout=form_children'
										.'&personId='.$personId;
								break;
			case "newparent":	$tmp = explode('!', $personId);
								$personId = $tmp[0].'!';
								$relationId = $tmp[1];
								$link .= '&layout=default'
										.'&personId='.$personId
										.'&relationId='.$relationId
										.'&action=addparent';
								break;
			case "newpartner":	$tmp = explode('!', $personId);
								$personId = $tmp[0].'!';
								$relationId = $tmp[1];
								$link .= '&layout=default'
										.'&personId='.$personId
										.'&relationId='.$relationId
										.'&action=addpartner';
								break;
			case "newchild":	$tmp = explode('!', $personId);
								$personId = $tmp[0].'!';
								$relationId = $tmp[1];
								$link .= '&layout=default'
										.'&personId='.$personId
										.'&relationId='.$relationId
										.'&action=addchild';
								break;
			case "pevents":		// continue
			default:			$link .= '&layout=default'
										.'&personId='.$personId;
								break; 
      		}
		
		
		$this->setRedirect(Jroute::_($link), $msg);
	}
	
	function save() {
		$model = $this->getModel('personform');
		
		$form     	= $this->input->get('jform', null, 'array');
		$treeId   	= $this->input->get('treeId', null, 'int');
		
		$msg = $model->save($form);
		
		$link =  'index.php?option=com_joaktree'
						.'&tech=a'
						.'&treeId='.$treeId;
						
		switch ($form['type']) {
			case "media":	
					$link .=  '&view=personform'
							 .'&layout=form_medialist';
					break;
			default:		
					$link .= '&view=joaktree'
							.'&action=edit';
					break;
		}				
						
		switch ($form['action']) {
			case "addchild":	// continue
			case "addparent":	// continue
			case "addpartner":
					$link .= '&personId='.$form['person']['app_id'].'!'.$form['person']['relations']['id'][0];
					break;
			default:
					$link .= '&personId='.$form['person']['app_id'].'!'.$form['person']['id'];
					break;
		}
						
		$this->setRedirect(Jroute::_($link), $msg);
	}
	
	
	function select() {
		$treeId	= $this->input->get('treeId', null, 'int');		
		$form	= $this->input->get('jform', null, 'array');
		
		$link =  'index.php?option=com_joaktree'
						.'&view=joaktree'
						.'&tech=a'
						.'&action=edit'
						.'&treeId='.$treeId;
		
		if (empty($form['person']['relations']['id'][0])) {
			// It is a person without relationship .. just continue
			$link .=  '&personId='.$form['person']['app_id'].'!'.$form['person']['id'];
		} else {
			// It is a person with relationship .. save the relationship
			$model = $this->getModel('personform');
			$msg = $model->save($form);
							
			if (  ($form['action'] == 'addparent')
			   || ($form['action'] == 'addpartner')
			   || ($form['action'] == 'addchild')
			   ) {
				$link .= '&personId='.$form['person']['app_id'].'!'.$form['person']['relations']['id'][0];
			} else {
				$link .= '&personId='.$form['person']['app_id'].'!'.$form['person']['id'];
			}
		}
				
		$this->setRedirect(Jroute::_($link), $msg);
	}
	
	function cancel() {
		$form	= $this->input->get('jform', null, 'array');
		$treeId	= $this->input->get('treeId', null, 'int');			
		$link 	=  'index.php?option=com_joaktree'
						.'&tech=a'
						.'&treeId='.$treeId;
		
		switch ($form['type']) {
			case "parents":		// continue
			case "partners":	// continue
			case "children":	
							$link .= '&view=joaktree'
									.'&personId='.$form['person']['app_id'].'!'.$form['person']['id']
									.'&action=edit';
							break;
			case "media":	$link .= '&view=personform'
									.'&personId='.$form['person']['app_id'].'!'.$form['person']['id']
									.'&layout=form_medialist';
							break;
			default:		$personId = (!empty($form['person']['relations']['id'][0])) 
											? $form['person']['relations']['id'][0] 
											: $form['person']['id'];
							$link .= '&view=joaktree'
									.'&personId='.$form['person']['app_id'].'!'.$personId
									.'&action=edit';
							break;
		}
				
		$this->setRedirect(Jroute::_($link), $msg);
	}
}
?>
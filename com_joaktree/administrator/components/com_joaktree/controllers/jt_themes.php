<?php
/**
 * Joomla! component Joaktree
 * file		jt_themes
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

class JoaktreeControllerJt_themes extends JoaktreeController {
	function __construct() {
		// check token first
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		parent::__construct();
		
		//Get View
		if($this->input->getCmd('view') == '') {
			$this->input->set('view', 'jt_themes');
		}
				
		$this->registerTask( 'add', 'edit' );
		$this->registerTask( 'remove', 'delete' );		
		$this->registerTask( 'apply', 'save');
	}

	public function setDefault() {
		$cids	= $this->input->get('cid', array(), 'array');
		$model	= $this->getModel('jt_themes');
		$msg  	= JText::_('');
		
		$cid	= (int) $cids[0];
		$msg	= $msg . $model->setDefault($cid);
		
		$link	= 'index.php?option=com_joaktree&view=jt_themes';
		$this->setRedirect($link, $msg);
	}
		
	public function cancel() {
		$link = 'index.php?option=com_joaktree&view=jt_themes';
		$this->setRedirect($link, null);
	}
	
	public function edit() {
		$cids	= $this->input->get('cid', array(), 'array');
		$cid	= isset($cids[0]) ? (int) $cids[0] : '';
		$this->input->set('id', $cid);

		$this->input->set('view', 'jt_theme' );
		$this->input->set('layout', 'form'  );
		
		parent::display();
	}
	
	public function delete() {
		$cids	= $this->input->get('cid', array(), 'array');
		$model 	= $this->getModel('jt_theme');
		$msgdeleted = false;
		$msgnotdeleted = false;
		
		foreach ($cids as $cid_num => $cid) {
			$id  = (int) $cid;
			$ret = $model->delete($id);
			
			if (!$ret) {
				$msgnotdeleted = true;
			} else {
				$msgdeleted = true;
			}
		}
		
		if ($msgdeleted) {
			$msg .= JText::_('JTTHEME_MESSAGE_DELETED').'; ';
		}
		
		if ($msgnotdeleted) {
			$msg .= JText::_('JTTHEME_MESSAGE_NOTDELETED');
		}
		
		$link = 'index.php?option=com_joaktree&view=jt_themes';
		$this->setRedirect($link, $msg);
	}
	
	public function save() {
		$form	= $this->input->get( 'jform', array(0), 'post', 'array' );
		
		$model = $this->getModel('jt_theme');
		$msg = $model->save($form);
		
		// Set the redirect based on the task.
		switch ($this->getTask())
		{
			case 'apply':
				$cids	= $this->input->get('cid', array(), 'array');
				$cid  	= (int) $cids[0];
				$caller	= $this->input->get( 'caller' );
				$link 	= 'index.php?option=com_joaktree&view=jt_theme&layout='.$caller.'&id='.$cid;
				break;

			case 'save':
			default:
				$link = 'index.php?option=com_joaktree&view=jt_themes';
				break;
		}
		
		$this->setRedirect($link, null);
	}
	
	public function edit_css() {
		$cids	= $this->input->get('cid', array(), 'array');
		$cid  = (int) $cids[0];
		$this->input->set( 'id', $cid  );
		
		$this->input->set( 'view', 'jt_theme' );
		$this->input->set( 'layout', 'editcss'  );
				
		parent::display();
	}
	
}
?>
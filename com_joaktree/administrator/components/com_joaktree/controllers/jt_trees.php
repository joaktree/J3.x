<?php
/**
 * Joomla! component Joaktree
 * file		jt_trees
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

class JoaktreeControllerJt_trees extends JoaktreeController {
	function __construct() {
		// check token first
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
			
		// create an input object
		$this->input = JFactory::getApplication()->input;

		//Get View
		if($this->input->get('view') == '') {
			$this->input->set('view', 'jt_trees');
		}
		
		parent::__construct();
		
		$this->registerTask( 'saveassign'      , 'saveassign' );
		$this->registerTask( 'unpublish'       , 'publish' );
		$this->registerTask( 'add'             , 'edit' );
		$this->registerTask( 'remove'          , 'delete' );		
		$this->registerTask( 'apply'           , 'save');
	}

	public function saveassign() {
		$form   = $this->input->get('jform', null, 'array' );

		$model = $this->getModel('jt_tree');
		$msg = $model->save($form);
				
		$link = 'index.php?option=com_joaktree&view=jt_trees&action=assign&treeId='.$form['id'];
		$this->setRedirect($link, $msg);		
	}

	public function publish() {
		$model = $this->getModel('jt_trees');
		
		$msg = $model->publish();
		
		$link = 'index.php?option=com_joaktree&view=jt_trees';
		$this->setRedirect($link, $msg);
	}

	public function edit() {
		$cids	= $this->input->get( 'cid', null, 'array' );

		$cid  = (int) $cids[0];
		$this->input->set( 'id', $cid  );
		
		$this->input->set( 'view', 'jt_tree' );
		$this->input->set( 'layout', 'form'  );
				
		parent::display();
	}

	public function delete() {
		$cids	= $this->input->get( 'cid', null, 'array' );
		$model = $this->getModel('jt_tree');
		
		$msg 	= $model->delete($cids);
		
		$link = 'index.php?option=com_joaktree&view=jt_trees';
		$this->setRedirect($link, $msg);
	}

	public function save() {
		$form   = $this->input->get('jform', null, 'array' );
		
		$model = $this->getModel('jt_tree');
		$msg = $model->save($form);
		
		// Set the redirect based on the task.
		switch ($this->getTask())
		{
			case 'apply':
				$cids = $this->input->get( 'cid', null, 'array' );
				$cid  = (int) $cids[0];
				$link = 'index.php?option=com_joaktree&view=jt_tree&layout=form&id='.$cid;
				break;

			case 'save':
			default:
				$link = 'index.php?option=com_joaktree&view=jt_trees';
				break;
		}

		$this->setRedirect($link, $msg);
	}

}
?>
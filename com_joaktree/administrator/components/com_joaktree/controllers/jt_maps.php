<?php
/**
 * Joomla! component Joaktree
 * file		jt_maps
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

class JoaktreeControllerJt_maps extends JoaktreeController {
	function __construct() {
		// check token first
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// create an input object
		$this->input = JFactory::getApplication()->input;
		
		//Get View
		if($this->input->get('view') == '') {
			$this->input->set('view', 'jt_maps');
		}
		
		parent::__construct();
		
		$this->registerTask( 'add'             , 'edit' );
		$this->registerTask( 'remove'          , 'delete' );		
	}

	public function edit() {
		$cids	= $this->input->get( 'cid', null, 'array' );
		$cid  = (int) $cids[0];
		$this->input->set( 'id', $cid  );
		
		$this->input->set( 'view', 'jt_map' );
		$this->input->set( 'layout', 'form'  );
				
		parent::display();
	}

	public function delete() {
		$cids	= $this->input->get( 'cid', null, 'array' );
		$model = $this->getModel('jt_map');
		
		$msg 	= $model->delete($cids);
		
		$link = 'index.php?option=com_joaktree&view=jt_maps';
		$this->setRedirect($link, $msg);
	}

	public function apply() {
		$form   = $this->input->get( 'jform', null, 'array' );
		
		$model = $this->getModel('jt_map');
		$msg = $model->save($form);
		
		$link = 'index.php?option=com_joaktree&view=jt_map&layout=form&id='.$form['id'];
		$this->setRedirect($link, $msg);
	}
	
	public function save() {
		$form   = $this->input->get( 'jform', null, 'array' );
		
		$model = $this->getModel('jt_map');
		$msg = $model->save($form);
		
		$link = 'index.php?option=com_joaktree&view=jt_maps';
		$this->setRedirect($link, $msg);
	}

	public function cancel() {
		$link = 'index.php?option=com_joaktree&view=jt_maps';
		$this->setRedirect($link);
	}
	
	public function locations() {
		$link = 'index.php?option=com_joaktree&view=jt_locations';
		$this->setRedirect($link);
	}
}
?>
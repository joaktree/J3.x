<?php
/**
 * Joomla! component Joaktree
 * file		jt_locations
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

class JoaktreeControllerJt_locations extends JoaktreeController {
	function __construct() {
		// check token first
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// create an input object
		$this->input = JFactory::getApplication()->input;

		//Get View
		if($this->input->get('view') == '') {
			$this->input->set('view', 'jt_locations');
		}
		
		parent::__construct();
		
		$this->registerTask( 'apply'           , 'save');		
		$this->registerTask( 'purgelocations', 'purge' );
	}

	public function edit() {
		$cids	= $this->input->get( 'cid', null, 'array' );
		$cid  = (int) $cids[0];
		$this->input->set( 'id', $cid  );
		
		$this->input->set( 'view', 'jt_location' );
		$this->input->set( 'layout', 'form'  );
				
		parent::display();
	}
		
	public function save() {
		$form   = $this->input->get( 'jform', null, 'array' );
		
		$model = $this->getModel('jt_location');
		$msg = $model->save($form);
		$link 	= 'index.php?option=com_joaktree&view=jt_locations';			
		
		$this->setRedirect($link, $msg);
	}
	
	public function purge() {		
		$model 	= $this->getModel('jt_locations');

		$msg 	= $model->purgeLocations();
		$link 	= 'index.php?option=com_joaktree&view=jt_locations';			
		
		$this->setRedirect($link, $msg);
	}
	
	public function geocode() {		
		$model 	= $this->getModel('jt_locations');

		$msg 	= $model->geocode();
		$link 	= 'index.php?option=com_joaktree&view=jt_locations';			
		
		$this->setRedirect($link, $msg);
	}
	
	public function resetlocation() {		
		$model 	= $this->getModel('jt_locations');

		$msg 	= $model->resetlocation();
		$link 	= 'index.php?option=com_joaktree&view=jt_locations';			
		
		$this->setRedirect($link, $msg);
	}
	
	
}
?>
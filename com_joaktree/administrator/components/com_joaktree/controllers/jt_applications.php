<?php
/**
 * Joomla! component Joaktree
 * file		jt_applications
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

class JoaktreeControllerJt_applications extends JoaktreeController {
	function __construct() {
		// check token first
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// create an input object
		$this->input = JFactory::getApplication()->input;

		//Get View
		if($this->input->get('view') == '') {
			$this->input->set('view', 'jt_applications');
		}
		
		parent::__construct();
		
		$this->registerTask( 'add'             , 'edit' );
		$this->registerTask( 'remove'          , 'delete' );
		$this->registerTask( 'apply'           , 'save');
		
		$this->registerTask( 'processGedCom', 'import' );
		$this->registerTask( 'exportGedCom',  'export' );
		$this->registerTask( 'clearGedCom',   'clearGedCom' );
		$this->registerTask( 'deleteGedCom',  'deleteGedCom' );
	}

	public function edit() {
		$cids	= $this->input->get( 'cid', null, 'array' );
		$cid  	= (int) $cids[0];
		$this->input->set( 'id', $cid  );
		
		$this->input->set( 'view', 'jt_application' );
		$this->input->set( 'layout', 'form'  );
				
		parent::display();
	}
	
	public function delete() {
		// first: delete the GedCom data
		$model1 = $this->getModel('jt_applications');
		$msg = $model1->deleteGedCom();		
		
		// second: delete the records
		$cids	= $this->input->get( 'cid', null, 'array' );
		$model 	= $this->getModel('jt_application');
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
			$msg .= '<br />'.JText::_('JTAPPS_MESSAGE_DELETED').'; ';
		}
		
		if ($msgnotdeleted) {
			$msg .= '<br />'.JText::_('JTAPPS_MESSAGE_NOTDELETED');
		}
		
		$link = 'index.php?option=com_joaktree&view=jt_applications';
		$this->setRedirect($link, $msg);
	}
	
	public function save() {
		$form   = $this->input->get( 'jform', null, 'array' );
		
		$model = $this->getModel('jt_application');
		$msg = $model->save($form);
		
		// Set the redirect based on the task.
		switch ($this->getTask())
		{
			case 'apply':
				$cids = $this->input->get( 'cid', null, 'array' );
				$cid  = (int) $cids[0];
				$link = 'index.php?option=com_joaktree&view=jt_application&layout=form&id='.$cid;
				break;

			case 'save':
			default:
				$link = 'index.php?option=com_joaktree&view=jt_applications';
				break;
		}
		$this->setRedirect($link, $msg);
	}
		
	public function import() {
//		$params = JComponentHelper::getParams('com_joaktree') ;
//		$procStep = $params->get('processStep', 1);
//		
//		if ($procStep != 1) {
//			$model 	= $this->getModel('jt_applications');			
//			$msg 	= $model->getGedcom();			
//			$link 	= 'index.php?option=com_joaktree&view=jt_applications';
//		} else {		
			$model 	= $this->getModel('jt_import_gedcom');
			$model->initialize();
			$msg 	= null;
			$link 	= 'index.php?option=com_joaktree&view=jt_import_gedcom';			
//		}
		
		$this->setRedirect($link, $msg);
	}
	
	public function export() {		
		$model 	= $this->getModel('jt_export_gedcom');
		$model->initialize();
		$msg 	= null;
		$link 	= 'index.php?option=com_joaktree&view=jt_export_gedcom';			
		
		$this->setRedirect($link, $msg);
	}
	
	public function clearGedCom() {		
		$model = $this->getModel('jt_applications');
		
		$msg = $model->clearGedCom();		
		
		$link = 'index.php?option=com_joaktree&view=jt_applications';
		$this->setRedirect($link, $msg);
	}
	
	public function deleteGedCom() {		
		$model = $this->getModel('jt_applications');
		
		$msg = $model->deleteGedCom();		
		
		$link = 'index.php?option=com_joaktree&view=jt_applications';
		$this->setRedirect($link, $msg);
	}	
}
?>
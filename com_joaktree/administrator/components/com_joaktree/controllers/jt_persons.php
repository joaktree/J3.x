<?php
/**
 * Joomla! component Joaktree
 * file		jt_persons.php
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

class JoaktreeControllerJt_persons extends JoaktreeController {
	function __construct() {
		// check token first
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// create an input object
		$this->input = JFactory::getApplication()->input;

		//Get View
		if($this->input->get('view') == '') {
			$this->input->set('view', 'jt_persons');
		}
		
		parent::__construct();
		
		$this->registerTask( 'save', 'save' );
		
		// three tasks for publishing
		$this->registerTask( 'unpublish', 'publish' );
		$this->registerTask( 'publishAll', 'publishAll' );
		$this->registerTask( 'unpublishAll', 'unpublishAll' );
		// three tasks for living
		$this->registerTask( 'updateLiving', 'living' );
		$this->registerTask( 'livingAll', 'livingAll' );
		$this->registerTask( 'notLivingAll', 'notLivingAll' );
		// three tasks for page switch
		$this->registerTask( 'updatePage', 'page' );
		$this->registerTask( 'pageAll', 'pageAll' );
		$this->registerTask( 'noPageAll', 'noPageAll' );		
		// three tasks for map switch
		$this->registerTask( 'mapStatAll', 'mapStatAll' );
		$this->registerTask( 'mapDynAll', 'mapDynAll' );
		$this->registerTask( 'noMapAll', 'noMapAll' );		
	}

	public function save() {
		$model = $this->getModel('jt_persons');		
		$msg = $model->save($post);		
		
		$link = 'index.php?option=com_joaktree&view=jt_persons';
		$this->setRedirect($link, $msg);
	}

	public function publish() {
		$model = $this->getModel('jt_persons');
		
		$msg = $model->publish();
		
		$link = 'index.php?option=com_joaktree&view=jt_persons';
		$this->setRedirect($link, $msg);
	}

	public function publishAll() {
		$model = $this->getModel('jt_persons');
		
		$msg = $model->publishAll();
		
		$link = 'index.php?option=com_joaktree&view=jt_persons';
		$this->setRedirect($link, $msg);
	}

	public function unpublishAll() {
		$model = $this->getModel('jt_persons');
		
		$msg = $model->unpublishAll();
		
		$link = 'index.php?option=com_joaktree&view=jt_persons';
		$this->setRedirect($link, $msg);
	}

	public function living() {
		$model = $this->getModel('jt_persons');
		
		$msg = $model->living();
		
		$link = 'index.php?option=com_joaktree&view=jt_persons';
		$this->setRedirect($link, $msg);
	}

	public function livingAll() {
		$model = $this->getModel('jt_persons');
		
		$msg = $model->livingAll();
		
		$link = 'index.php?option=com_joaktree&view=jt_persons';
		$this->setRedirect($link, $msg);
	}

	public function notLivingAll() {
		$model = $this->getModel('jt_persons');
		
		$msg = $model->notLivingAll();
		
		$link = 'index.php?option=com_joaktree&view=jt_persons';
		$this->setRedirect($link, $msg);
	}

	public function page() {
		$model = $this->getModel('jt_persons');
		
		$msg = $model->page();
		
		$link = 'index.php?option=com_joaktree&view=jt_persons';
		$this->setRedirect($link, $msg);
	}

	public function pageAll() {
		$model = $this->getModel('jt_persons');
		
		$msg = $model->pageAll();
		
		$link = 'index.php?option=com_joaktree&view=jt_persons';
		$this->setRedirect($link, $msg);
	}

	public function noPageAll() {
		$model = $this->getModel('jt_persons');
		
		$msg = $model->noPageAll();
		
		$link = 'index.php?option=com_joaktree&view=jt_persons';
		$this->setRedirect($link, $msg);
	}

	public function mapStatAll() {
		$model = $this->getModel('jt_persons');
		
		$msg = $model->mapStatAll();
		
		$link = 'index.php?option=com_joaktree&view=jt_persons';
		$this->setRedirect($link, $msg);
	}

	public function mapDynAll() {
		$model = $this->getModel('jt_persons');
		
		$msg = $model->mapDynAll();
		
		$link = 'index.php?option=com_joaktree&view=jt_persons';
		$this->setRedirect($link, $msg);
	}

	public function noMapAll() {
		$model = $this->getModel('jt_persons');
		
		$msg = $model->noMapAll();
		
		$link = 'index.php?option=com_joaktree&view=jt_persons';
		$this->setRedirect($link, $msg);
	}
}
?>
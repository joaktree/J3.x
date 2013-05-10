<?php
/**
 * Joomla! component Joaktree
 * file		jt_settings
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

class JoaktreeControllerJt_settings extends JoaktreeController {
	function __construct() {
		// first check token
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// create an input object
		$this->input = JFactory::getApplication()->input;

		//Get View
		if($this->input->get('view') == '') {
			$this->input->set('view', 'jt_settings');
		}
		
		parent::__construct();
		
		$this->registerTask( 'unpublish'       , 'publish' );		
	}

	public function publish() {
		$layout = $this->input->get('layout');
		$model = $this->getModel('jt_settings');
		
		$msg = $model->publish();
		
		$link = 'index.php?option=com_joaktree&view=jt_settings&layout='.$layout;
		$this->setRedirect($link);
	}
	
	public function save() {
		$layout = $this->input->get('layout');
		$model 	= $this->getModel('jt_settings');
		
		$msg 	= $model->save($layout);
		
		$link 	= 'index.php?option=com_joaktree&view=jt_settings&layout='.$layout;
		$this->setRedirect($link);
		
		
//		//$post	= $this->input->get('post', null, 'array' );
//		$model	= $this->getModel('jt_settings');
//		$retmsg = '';
//				
//		// retrieve id
//		$cids	= $this->input->get( 'cid', null, 'array' );
//		
//		// We are only doing it when we have items to be updated
//		if ((count( $cids ) > 0) and ($cids[0] > 0)) {			
//			for ($i=0, $n=count( $cids ); $i < $n; $i++) {
//				$post['id'] 	        = intval( $cids[$i] );
//				
//				$tmp = $this->input->get( 'access'.$post['id'], null, 'string' );
//				$tmp = (int) substr($tmp, 0, 3);
//				$post['access']	= $tmp;
//				
//				$tmp = $this->input->get( 'accessLiving'.$post['id'], null, 'string' );
//				$tmp = (int) substr($tmp, 0, 3);
//				$post['accessLiving']	= $tmp;
//				
//				$tmp = $this->input->get( 'altLiving'.$post['id'], null, 'string' );
//				$tmp = (int) substr($tmp, 0, 3);
//				$post['altLiving']	= $tmp;
//							
//				$code = $this->input->get( 'code'.$post['id'], null, 'string' );		
//							
//				$retmsg .= $model->store($post, $code).';&nbsp;';
//			}
//		}
//		
//		if (strlen($retmsg) > 0) {
//			$msg = JText::_( 'JTSETTINGS_SAVE_ACCESSLEVEL' ).':&nbsp;'.$retmsg;
//		} else {
//			$msg = JText::_( 'JTSETTINGS_NO_SAVE_ACCESSLEVEL' );
//		}
//				
//		$link = 'index.php?option=com_joaktree&view=jt_settings&layout='.$layout;
//		$this->setRedirect($link, $msg);
		
	}
	
//	public function setOrder() {	
//		$layout = $this->input->get('layout');
//		$model = $this->getModel('jt_settings');
//		
//		$msg = $model->setOrder($layout);
//		
//		$link = 'index.php?option=com_joaktree&view=jt_settings&layout='.$layout;
//		$this->setRedirect($link);
//	}
}
?>
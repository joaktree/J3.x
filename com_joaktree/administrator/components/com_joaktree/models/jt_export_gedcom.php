<?php
/**
 * Joomla! component Joaktree
 * file		jt_export_gedcom model - jt_export_gedcom.php
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

require_once JPATH_COMPONENT.DS.'helpers'.DS.'jt_gedcomexport2.php';

// Import Joomla! libraries
jimport('joomla.application.component.model');

class processObject {
	var $id			= null;
	var $start		= null;
	var $current	= null;
	var $end		= null;
	var $cursor		= 0;
	var $persons	= 0;
	var $families	= 0;
	var $sources	= 0;
	var $repos		= 0;
	var $notes		= 0;
	var $docs		= 0;
	var $unknown	= 0;
	var $japp_ids	= null;
	var $status		= 'new';
	var $msg		= null;			
}

class JoaktreeModelJt_export_gedcom extends JModelLegacy {

	var $_data;
	var $_pagination 	= null;
	var $_total         = null;

	function __construct() {
		parent::__construct();	

		$this->jt_registry	= & JTable::getInstance('joaktree_registry_items', 'Table');
	}

	private function _buildQuery() {
		$procObject = $this->getProcessObject();
		$cids = $procObject->japp_ids;
		array_unshift($cids, $procObject->id);
				
		if (count($cids) == 0) {
			// no applications are selected
			return false;
			
		} else {
			// make sure the input consists of integers
			for($i=0;$i<count($cids);$i++) {
				$cids[$i] = (int) $cids[$i];
				
				if ($cids[$i] == 0) {
					die('wrong request');
				}
			}
			
			$query = $this->_db->getQuery(true);
			$query->select(' japp.* ');
			$query->from(  ' #__joaktree_applications  japp ');
			$query->where( ' japp.id IN ('.implode(",", $cids).') ');
			$query->order( ' japp.id ');
			
			return $query;
		}					
	}
	
	public function getData() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			$query = $this->_buildQuery();
			$this->_data = $this->_getList( $query );
		}
		
		return $this->_data;
	}
	
	/* 
	** function for processing the gedcom file
	*/
	public function initialize() {
		$cids	= JFactory::getApplication()->input->get( 'cid', null, 'array' );
				
		// make sure the input consists of integers
		for($i=0;$i<count($cids);$i++) {
			$cids[$i] = (int) $cids[$i];
			
			if ($cids[$i] == 0) {
				die('wrong request');
			}
		}
		
		// store first empty object
		$this->initObject ($cids);	
	}
	
	private function initObject ($cids) {		
		// store first empty object
		$newObject 				= new processObject();
		$newObject->id 			= array_shift($cids);
		$newObject->japp_ids 	= $cids;

		if (!$newObject->id) {
			$newObject->status = 'close';
		}
		
		$this->setProcessObject($newObject);
	}	
	
	private function setProcessObject($procObject) {
		// create a registry item	
		if (isset($procObject->msg)) {
			$procObject->msg 		= substr($procObject->msg, 0, 1500);
		} 
		$this->jt_registry->regkey 	= 'EXPORT_OBJECT';
		$this->jt_registry->value  	= json_encode($procObject);
		$this->jt_registry->storeUK();		
	}
	
	private function getProcessObject() {
		static $procObject;
		
		// retrieve registry item
		$this->jt_registry->loadUK('EXPORT_OBJECT');	
		$procObject = json_decode($this->jt_registry->value);
		unset($procObject->msg);			
		
		return $procObject;
	}
	
	/* 
	** function for processing the gedcom file
	*/
	public function getGedcom() {
		$canDo	= JoaktreeHelper::getActions();
		$procObject = $this->getProcessObject();
		
		if (($canDo->get('core.create')) && ($canDo->get('core.edit'))) {		

			if ($procObject->status == 'new') {
				$procObject->start = strftime('%H:%M:%S');
				$procObject->msg = JText::sprintf('JTPROCESS_START_MSG', $procObject->id);
				
			} 

			if ($procObject->status == 'end') {
				// store first empty object
				$appId = $procObject->id;
				$this->initObject ($procObject->japp_ids);		
				$newObject = $this->getProcessObject();
				$newObject->msg = JText::sprintf('JTPROCESS_END_MSG', $appId);
				return json_encode($newObject);
			}	
				
			$gedcomfile = new jt_gedcomexport2($procObject);
			$resObject 	= $gedcomfile->process();
		
			$resObject->current = strftime('%H:%M:%S');
			$this->setProcessObject($resObject);
								
			$return = json_encode($resObject);

		} else {
			
			$procObject->status = 'error';
			$procObject->msg    = JText::_('JT_NOTAUTHORISED');
			
			$return = json_encode($procObject);
		}
		
		return $return;
	}		
}
?>
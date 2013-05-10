<?php
/**
 * Joomla! component Joaktree
 * file		jt_import_gedcom model - jt_import_gedcom.php
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

require_once JPATH_COMPONENT.DS.'helpers'.DS.'jt_names.php';
require_once JPATH_COMPONENT.DS.'helpers'.DS.'jt_relations.php';
require_once JPATH_COMPONENT.DS.'helpers'.DS.'jt_trees.php';
require_once JPATH_COMPONENT.DS.'tables'.DS.'JMFPKtable.php';

require_once JPATH_COMPONENT.DS.'helpers'.DS.'jt_gedcomfile2.php';
require_once JPATH_COMPONENT.DS.'helpers'.DS.'jt_gedcompersons2.php';
require_once JPATH_COMPONENT.DS.'helpers'.DS.'jt_gedcomsources2.php';
require_once JPATH_COMPONENT.DS.'helpers'.DS.'jt_gedcomrepos2.php';
require_once JPATH_COMPONENT.DS.'helpers'.DS.'jt_gedcomnotes2.php';
require_once JPATH_COMPONENT.DS.'helpers'.DS.'jt_gedcomdocuments2.php';

JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');
JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'models');

// Import Joomla! libraries
jimport('joomla.application.component.modellist');

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

class JoaktreeModelJt_import_gedcom extends JModelLegacy {

	var $_data;
	var $_pagination 	= null;
	var $_total         = null;

	function __construct() {
		parent::__construct();	

		$this->jt_registry	= & JTable::getInstance('joaktree_registry_items', 'Table');
	}

	private function _buildQuery() {
		// Get the WHERE and ORDER BY clauses for the query
		$wheres      =  $this->_buildContentWhere();
		
		if (($wheres) &&(is_array($wheres))) {
			$query = $this->_db->getQuery(true);
			$query->select(' japp.* ');
			$query->from(  ' #__joaktree_applications  japp ');
			foreach ($wheres as $where) {
				$query->where(' '.$where.' ');
			}
			$query->order(' japp.id ');
				
		} else {
			// if there is no where statement, there are no applications selected.
			unset($query);
		}
				
		return $query;
	}

	private function _buildContentWhere() {
		$wheres = array();
		
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
			
			// create a string
			$japp_ids = '('.implode(",", $cids).')';
			
			// create where
			$wheres[] = 'japp.id IN '.$japp_ids;
			
		}
								
		return $wheres;
	}
	
	public function getData() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
                     $query = $this->_buildQuery();
                     $this->_data = $this->_getList( $query );
		}
		
		return $this->_data;
	}
	
	/* 
	** function for processing the gedcom file
	*/
	public function initialize() {
		$cids = JFactory::getApplication()->input->get( 'cid', null, 'array' );
				
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
			$newObject->status = 'stop';
		}
		
		$this->setProcessObject($newObject);
	}
	
	
	private function setProcessObject($procObject) {
		// create a registry item	
		if (isset($procObject->msg)) {
			$procObject->msg 		= substr($procObject->msg, 0, 1500);
		} 
		$this->jt_registry->regkey 	= 'PROCESS_OBJECT';
		$this->jt_registry->value  	= json_encode($procObject);
		$this->jt_registry->storeUK();		
	}
	
	private function getProcessObject() {
		static $procObject;
		
		// retrieve registry item
		$this->jt_registry->loadUK('PROCESS_OBJECT');	
		$procObject = json_decode($this->jt_registry->value);
		unset($procObject->msg);			
		
		return $procObject;
	}
	
	/* 
	** function for processing the gedcom file
	** status: new			- New process. Nothing has happened yet.
	**         progress		- Reading through the GedCom file
	**         endload		- Finished loading GedCom file
	**         endpat		- Finished setting patronyms
	**         endrel		- Finished setting relation indicators
	**         start		- Start assigning family trees
	**         starttree	- Start assigning one tree
	**         progtree		- Processing family trees (setting up link between persons and trees)
	**         endtree		- Finished assigning family trees
	**         treedef_1 	- Finished setting up default trees 1 (1 tree per person)
	**         treedef_2 	- Finished setting up default trees 2 (1 tree per person)
	**         treedef_3 	- Finished setting up default trees 3 (1 father tree per person)
	**         treedef_4 	- Finished setting up default trees 4 (1 mother tree per person)
	**         treedef_5 	- Finished setting up default trees 5 (1 partner tree per person)
	**         treedef_6 	- Finished setting up default trees 6 (lowest tree)
	**         endtreedef 	- Finished setting up default trees 7 (lowest tree)
	**         end			- Finished full process
	**         error		- An error has occured
	*/
	public function getGedcom() {
		$canDo	= JoaktreeHelper::getActions();
		$procObject = $this->getProcessObject();
		
		if (($canDo->get('core.create')) && ($canDo->get('core.edit'))) {	

			switch ($procObject->status) {
				case 'new':
					$procObject->start = strftime('%H:%M:%S');
					$procObject->msg = JText::sprintf('JTPROCESS_START_MSG', $procObject->id);					
				case 'progress':	// continue
				case 'endload':		// continue
				case 'endpat':		// continue
					$gedcomfile = new jt_gedcomfile2($procObject);
					$resObject 	= $gedcomfile->process('all');
		
					if ($resObject->status == 'endrel') {
						$msg = jt_gedcomfile2::clear_gedcom();
						if ($msg) {
							$resObject->msg .= $msg.'<br />';
						}
					}			
							
					$resObject->current = strftime('%H:%M:%S');
					$this->setProcessObject($resObject);
					$return = json_encode($resObject);
					break;
				case 'endrel':
					// Start loop throuth the assign FT
					$procObject->status = 'start';
				// Addition for processing tree-persons
				case 'start':		// continue
				case 'starttree':	// continue
				case 'progtree':	// continue
				case 'endtree':		// continue
				case 'treedef_1':	// continue
				case 'treedef_2':	// continue
				case 'treedef_3':	// continue
				case 'treedef_4':	// continue
				case 'treedef_5':	// continue
				case 'treedef_6':	// continue
					$familyTree = new jt_trees($procObject);
					$resObject 	= $familyTree->assignFamilyTree();
					
					$resObject->current = strftime('%H:%M:%S');
					$this->setProcessObject($resObject);
					$return = json_encode($resObject);
					break;
				case 'endtreedef':
					// we are done
					$procObject->status  = 'end';
					$procObject->current = strftime('%H:%M:%S');
					$procObject->end 	 = $procObject->current;
					$this->setLastUpdateDateTime();
					$this->setInitialChar();
					
					$this->setProcessObject($procObject);
					$return = json_encode($procObject);
					break;
				// End: Addition for processing tree-persons
				case 'end':
					// store first empty object
					$appId = $procObject->id;
					$this->initObject ($procObject->japp_ids);		
					$newObject = $this->getProcessObject();
					$newObject->msg = JText::sprintf('JTPROCESS_END_MSG', $appId);

					$return = json_encode($newObject);
					break;
				case 'error':	// continue
				default:		// continue
					break;
			}			
		} else {
			
			$procObject->status = 'error';
			$procObject->msg    = JText::_('JT_NOTAUTHORISED');
			
			$return = json_encode($procObject);
		}
		
		return $return;
	}	
	
	private function setLastUpdateDateTime() {
		$query = $this->_db->getQuery(true);
		$query->update(' #__joaktree_registry_items ');
		$query->set(   ' value  = NOW() ');
		$query->where( ' regkey = '.$this->_db->quote( 'LAST_UPDATE_DATETIME' ).' ');
		
		$this->_db->setQuery( $query );
		$this->_db->query();		
	}

	private function setInitialChar() {
		// update register with 0, meaning NO "initial character" present 	
		$query = $this->_db->getQuery(true);
		$query->update(' #__joaktree_registry_items ');
		$query->set(   ' value  = '.$this->_db->quote('0').' ');
		$query->where( ' regkey = '.$this->_db->quote( 'INITIAL_CHAR' ).' ');
		
		$this->_db->setQuery( $query );
		$this->_db->query();			
	}
}
?>
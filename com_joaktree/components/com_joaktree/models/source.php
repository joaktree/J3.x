<?php
/**
 * Joomla! component Joaktree
 * file		front end source model - source.php
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

// Import Joomla! libraries
jimport('joomla.application.component.modelform');

class JoaktreeModelSource extends JModelForm {

	public function getApplicationId() {
		return JoaktreeHelper::getApplicationId();
	}

	public function getSourceId() {
		return JoaktreeHelper::getSourceId();
	}

	public function getAction() {
 		return JoaktreeHelper::getAction();
	}
	
	public static function getAccess() {
		return JoaktreeHelper::getAccessGedCom();
	}
	
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_joaktree.source', 'source', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {		
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_joaktree.edit.source.data', array());

		if (empty($data)) {
			$data = $this->getItem();		
		}

		return $data;
	}
		
	public function getItem() {
		$query = $this->_db->getQuery(true);
		
		// select from sources
		$query->select(' jse.app_id ');
		$query->select(' jse.id ');
		$query->select(' jse.repo_id ');
		$query->select(' jse.title ');
		$query->select(' jse.author ');
		$query->select(' jse.publication ');
		$query->select(' jse.information ');
		$query->from(  ' #__joaktree_sources jse ');
						
		// Get the WHERE, GROUP BY and ORDER BY clauses for the query
		$wheres      	= $this->_buildContentWhere();
		foreach ($wheres as $where) {
			$query->where(' '.$where.' ');
		}
							
		$this->_db->setQuery($query);			
		$item = $this->_db->loadObject();
			
		if (!is_object($item)) {
			$item = new stdClass;
			$item->app_id = intval( $this->getApplicationId() );
		} else {			
			$item->title		= htmlspecialchars_decode($item->title      , ENT_QUOTES);
			$item->author		= htmlspecialchars_decode($item->author     , ENT_QUOTES);
			$item->publication	= htmlspecialchars_decode($item->publication, ENT_QUOTES);
			$item->information	= htmlspecialchars_decode($item->information, ENT_QUOTES);
		}
		
		$item->app_repo_id = $item->app_id.'!'.((isset($item->repo_id)) ? $item->repo_id : null);
		return $item;
	}

	private function _buildContentWhere() {
		$appId     	= intval( $this->getApplicationId() );
		$sourceId     = $this->getSourceId();
		
		$where = array();
		
		if ($appId) {
			$where[] = ' jse.app_id = '.$appId.' ';
		}
		 
		if ($sourceId) {
			$where[] = ' jse.id = '.$this->_db->quote($sourceId).' ';
		}
		
		return $where;
	}
	
	public function save($form) {
		// Load the parameters.
		$params	= JComponentHelper::getParams('com_joaktree');
		if ($params->get('siteedit', 1)) {	
			$canDo	= JoaktreeHelper::getActions(false);	
		}
		
		if ( (is_object($canDo)) && 
				(  $canDo->get('core.create')
				|| $canDo->get('core.edit')
				) 
		   ) {

		   	$prefix = 'Table'; 
		   	$config = array();
			$table	= JTable::getInstance('joaktree_sources', $prefix, $config);;		
						
			// Bind the form fields to the table
			if (empty($form['id'])) {
				// retreive ID and double check that it is not used
				$continue = true;
				$i = 0;
				while ($continue) {
					$tmpId = JoaktreeHelper::generateJTId();
					$i++;
					
					if ($this->check($tmpId)) {
						$form['id'] = $tmpId;
						$continue = false;
						break;
					}
					if ($i > 100) {
						$continue = false;
						return false;
					}
				}
				
				$status		= 'new';
				$crud		= 'C';
			} else {
				$status		= 'changed';
				$crud		= 'U';
			}
				
			$table->id			= $form['id'];
			$table->app_id		= $form['app_id'];
			$table->title		= htmlspecialchars($form['title'], ENT_QUOTES, 'UTF-8');
			$table->author		= htmlspecialchars($form['author'], ENT_QUOTES, 'UTF-8');
			$table->publication	= htmlspecialchars($form['publication'], ENT_QUOTES, 'UTF-8');
			$table->information	= htmlspecialchars($form['information'], ENT_QUOTES, 'UTF-8');
			
			// repo id
			$tmp = explode('!', $form['app_repo_id']);			
			if (count($tmp) == 2) {
				$table->repo_id = $tmp[1];
			} else {
				$table->repo_id = null;
			}
			
			// Make sure the data is valid
			if (!$table->check()) {
				return false;
			}
			
			// Store the table to the database
			if (!$table->store(true)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			// log
			$log	= JTable::getInstance('joaktree_logs', $prefix, $config);
			$log->app_id		= $form['app_id'];
			$log->object_id		= $form['id'];
			$log->object		= 'sour';
			$log->log($crud); 
			
			//return
			$ret				= new stdClass;
			$ret->object		= 'source';
			$ret->app_id		= $form['app_id'];
			$ret->object_id		= $form['id'];
			$ret->status		= $status;
			$statusObj			= $this->get( 'returnObject');
			$ret->action		= $statusObj->action;
			$return				= base64_encode(json_encode($ret));
			
		} else {
			$return = false;
		}
		
		return $return;
	}
	
	public function delete($appId, $sourceId) {
		// Load the parameters.
		$params	= JComponentHelper::getParams('com_joaktree');
		if ($params->get('siteedit', 1)) {	
			$canDo	= JoaktreeHelper::getActions(false);	
		}
		
		if ( (is_object($canDo)) && ($canDo->get('core.delete')) ) {

		   	$prefix = 'Table'; 
		   	$config = array();
			$table	= JTable::getInstance('joaktree_repositories', $prefix, $config);;
			$table->id         = $repoId;
			$table->app_id     = $appId;

			// retrieve row - for display later on
			if (!$table->load()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}			
			
			// Delete the row from the database
			if (!$table->delete()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}			
													
			// log
			$log	= JTable::getInstance('joaktree_logs', $prefix, $config);
			$log->app_id		= $appId;
			$log->object_id		= $sourceId;
			$log->object		= 'sour';
			$log->log('D'); 
			$logDel	= JTable::getInstance('joaktree_logremovals', $prefix, $config);
			$logDel->app_id		= $appId;
			$logDel->object_id	= $sourceId;
			$logDel->object		= 'sour';
			$logDel->description= $table->title;
			if ($logDel->check()) {
				$logDel->store();
			}
			
			//return
			$return				= $table->title;
						
		} else {
			$return = false;
		}
		
		return $return;
	}
	
	private function check($tmpId) {
		$query = $this->_db->getQuery(true);
		$query->select(' 1 ');
		$query->from(  ' #__joaktree_sources ');
		$query->where( ' id   = '.$this->_db->quote($tmpId).' ');
		
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();
		
		// ID is alreadey used -> return false
		// ID is not used in the selected table -> return true
		return ($result) ? false : true;
	}
}
?>
<?php
/**
 * Joomla! component Joaktree
 * file		front end repository model - repository.php
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

class JoaktreeModelRepository extends JModelForm {

	public function getApplicationId() {
		return JoaktreeHelper::getApplicationId();
	}

	public function getRepoId() {
		return JoaktreeHelper::getRepoId();
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
		$form = $this->loadForm('com_joaktree.repository', 'repository', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_joaktree.edit.repository.data', array());

		if (empty($data)) {
			$data = $this->getItem();		
		}

		return $data;
	}
		
	public function getItem() {
		$query = $this->_db->getQuery(true);
		
		// select from repositories
		$query->select(' jry.app_id ');
		$query->select(' jry.id ');
		$query->select(' jry.name ');
		$query->select(' jry.website ');
		$query->from(  ' #__joaktree_repositories jry ');
		
		// select from sources
		$query->select(' count(jse.id) AS indSource ');		
		$query->leftJoin(' #__joaktree_sources  jse ' 
					 	.' ON (   jse.app_id  = jry.app_id '
						.'    AND jse.repo_id = jry.id '
						.'    ) '
						);
		
		// Get the WHERE, GROUP BY and ORDER BY clauses for the query
		$wheres      	= $this->_buildContentWhere();
		foreach ($wheres as $where) {
			$query->where(' '.$where.' ');
		}
		$query->group(' jry.name ');
		$query->group(' jry.website ');
		$query->group(' jry.id ');
		$query->group(' jry.app_id ');
							
		$this->_db->setQuery($query);			
		$item = $this->_db->loadObject();
		
		if (!is_object($item)) {
			$item = new stdClass;
			$item->app_id = intval( $this->getApplicationId() );
		} else {
			$item->name		= htmlspecialchars_decode($item->name   , ENT_QUOTES);
			$item->website	= htmlspecialchars_decode($item->website, ENT_QUOTES);
		}
		
		return $item;
	}

	private function _buildContentWhere() {
		$appId     	= intval( $this->getApplicationId() );
		$repoId     = $this->getRepoId();
		
		$where = array();
		
		if ($appId) {
			$where[] = ' jry.app_id = '.$appId.' ';
		}
		 
		if ($repoId) {
			$where[] = ' jry.id = '.$this->_db->quote($repoId).' ';
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
			$table	= JTable::getInstance('joaktree_repositories', $prefix, $config);;		
						
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
			
			$table->id         = $form['id'];
			$table->app_id     = $form['app_id'];
			$table->name       = htmlspecialchars($form['name'], ENT_QUOTES, 'UTF-8');
			$table->website    = htmlspecialchars($form['website'], ENT_QUOTES, 'UTF-8');
						
			// Make sure the data is valid
			if (!$table->check()) {
				return false;
			}
			
			// Store the table to the database
			if (!$table->store(false)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}			
			
			// log
			$log	= JTable::getInstance('joaktree_logs', $prefix, $config);
			$log->app_id		= $form['app_id'];
			$log->object_id		= $form['id'];
			$log->object		= 'repo';
			$log->log($crud); 
			
			//return
			$ret				= new stdClass;
			$ret->object		= 'repo';
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
	
	public function delete($appId, $repoId) {
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
				$this->setError('Error loading repository: '.$table->getError());
			}			
			
			// Delete the row from the database
			if (!$table->delete()) {
				$this->setError('Error deleting repository: '.$table->getError());
			}			
													
			// log
			$log	= JTable::getInstance('joaktree_logs', $prefix, $config);
			$log->app_id		= $appId;
			$log->object_id		= $repoId;
			$log->object		= 'repo';
			$log->log('D'); 
			$logDel	= JTable::getInstance('joaktree_logremovals', $prefix, $config);
			$logDel->app_id		= $appId;
			$logDel->object_id	= $repoId;
			$logDel->object		= 'repo';
			$logDel->description= $table->name;
			if ($logDel->check()) {
				if (!$logDel->store()) {
					$this->setError('Error storing log: '.$logDel->getError());
				}
			}
			
			// Deal with errors
			$errors = $this->getErrors();
			if (count($errors) > 0) {
				$return = JText::_(implode(' ##### ', $errors ));
			} else {
				$return = JText::sprintf('JT_DELETED', $table->name);
			}
		} else {
			$return = JText::_('JT_NOTAUTHORISED');
		}
		
		return $return;
	}
	
	private function check($tmpId) {
		$query = $this->_db->getQuery(true);
		$query->select(' 1 ');
		$query->from(  ' #__joaktree_repositories ');
		$query->where( ' id   = '.$this->_db->quote($tmpId).' ');
		
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();
		
		// ID is alreadey used -> return false
		// ID is not used in the selected table -> return true
		return ($result) ? false : true;
	}
}
?>
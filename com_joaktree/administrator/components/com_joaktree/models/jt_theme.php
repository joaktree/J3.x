<?php
/**
 * Joomla! component Joaktree
 * file		jt_theme modelAdmin - jt_theme.php
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

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class JoaktreeModelJt_theme extends JModelAdmin
{
	protected function canDelete($record)
	{
		if ($record->home) {
			// default record cannot be deleted
			return false;
		} else {
			return parent::canDelete($record);
		}
	}

	public function delete($id)
	{
		// retrieve the default value
		$query = $this->_db->getQuery(true);
		$query->select(' home ');
		$query->from(  ' #__joaktree_themes ');
		$query->where( ' id   = '.(int) $id.' ');
		$query->where( ' home = 1 ');
		
		$this->_db->setQuery($query);
		$ret = $this->_db->loadResult();
		
		if ($ret) {
			// Value is default
			return false;
		} else {
			$ret = $this->deleteSource($id);
			return parent::delete($id);
		}
		
	}
	
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'joaktree_themes', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
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
		$form = $this->loadForm('com_joaktree.theme', 'jt_theme', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_joaktree.edit.theme.data', array());

		if (empty($data)) {		
			$data = $this->getItem();
		}

		return $data;
	}
	
	public function getItem() {
		$item = parent::getItem();
		
		if ($item->id) {
			$item->sourcepath	= $this->getSourcePath($item->id);
			$item->source 		= $this->getSource($item->sourcepath);
		}
				
		return $item;	
	}
	
	public function save($form) {
		$canDo	= JoaktreeHelper::getActions();
		
		if ($canDo->get('core.create') || $canDo->get('core.edit')) {
			if (!isset($form['name'])) {
				$form['name'] = isset($form['newname'])? $form['newname'] : null;	
			}
			
			if (isset($form['source'])) {
				return $this->saveSource($form);	
			} else {
				
				if (isset($form['theme'])) {
					$this->createSource($form);						
				}
				
				return parent::save($form);		
			}
		}

	}
	
	private function getSourceBase() {
		$base = JPATH_SITE.DS.'components'.DS.'com_joaktree'.DS.'themes'.DS;
		return $base;
	}
	
	private function getSourcePath($id) {
		$theme = JoaktreeHelper::getThemeName($id);
		
		$filePath	= JPath::clean($this->getSourceBase().$theme.DS.'theme.css');
		
		if (JFile::exists($filePath)) {
			return $filePath;
		} else {
			return null;
		}
	}
	
	public function getSource($filePath) {		
		return JFile::read($filePath);
	}
	
	public function saveSource($form) {
		// Try to make the file writeable.
		if (JPath::isOwner($form['sourcepath']) && !JPath::setPermissions($form['sourcepath'], '0755')) {
			$this->setError(JText::_('JTTHEME_ERROR_SOURCE_FILE_NOT_WRITABLE'));
			return false;
		}
		
		$ret = JFile::write($form['sourcepath'], $form['source']);
		
		// Try to make the file unwriteable.
		if (JPath::isOwner($form['sourcepath']) && !JPath::setPermissions($form['sourcepath'], '0555')) {
			$this->setError(JText::_('JTTHEME_ERROR_SOURCE_FILE_NOT_UNWRITABLE'));
			return false;
		} else if (!$ret) {
			$this->setError(JText::sprintf('JTTHEME_ERROR_FAILED_TO_SAVE_FILENAME', $form['sourcepath']));
			return false;
		}
		
		return true;
	}
	
	public function createSource($form) {
		$theme 		 = JoaktreeHelper::getThemeName($form['theme']);
		$source		 = JPath::clean($this->getSourceBase().$theme);
		$destination = JPath::clean($this->getSourceBase().$form['newname']);

		return JFolder::copy($source, $destination);		
	}
	
	public function deleteSource($id) {
		$theme 		 = JoaktreeHelper::getThemeName($id);
		$source		 = JPath::clean($this->getSourceBase().$theme);

		return JFolder::delete($source);		
	}
}

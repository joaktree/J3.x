<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_tree_persons.php
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

 defined('_JEXEC') or die('Restricted access');
jimport('joomla.filter.input');

class TableJoaktree_tree_persons extends JTable
{
	var $id			= null;
	var $app_id		= null;
	var $tree_id	= null;
	var $person_id	= null;
	var $type 		= null;
	var $lineage 	= null;

	function __construct( &$db) {
		parent::__construct('#__joaktree_tree_persons', 'id', $db);
	}

	function truncate($app_id = null) { 
		if ($app_id) {
			$query = $this->_db->getQuery(true);
			$query->delete(' '.$this->_db->quoteName($this->_tbl).' ');
			$query->where( ' app_id = '.(int) $app_id.' ');			
		} else {
			$query = 'TRUNCATE ' . $this->_tbl;
		}
		
		$this->_db->setQuery( $query );
		$this->_db->query();		

		return true;
	}
	
	/**
	 * Method to store a row in the database from the JTable instance properties.
	 * If a primary key value is set the row with that primary key value will be
	 * updated with the instance property values.  If no primary key value is set
	 * a new row will be inserted into the database with the properties from the
	 * JTable instance.
	 *
	 * @param	boolean True to update fields even if they are null.
	 * @return	boolean	True on success.
	 * @since	1.0
	 * @link	http://docs.joomla.org/JTable/store
	 */
	public function store($updateNulls = false)
	{		
		// always an insert		
		$stored = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);

		// If the store failed return false.
		if (!$stored)
		{
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			return false;
		}

		// If the table is not set to track assets return true.
		if (!$this->_trackAssets) {
			return true;
		}

		if ($this->_locked) {
			$this->_unlock();
		}

		//
		// Asset Tracking
		//

		$parentId	= $this->_getAssetParentId();
		$name		= $this->_getAssetName();
		$title		= $this->_getAssetTitle();

		$asset	= JTable::getInstance('Asset');
		$asset->loadByName($name);

		// Check for an error.
		if ($error = $asset->getError())
		{
			$this->setError($error);
			return false;
		}

		// Specify how a new or moved node asset is inserted into the tree.
		if (empty($this->asset_id) || $asset->parent_id != $parentId) {
			$asset->setLocation($parentId, 'last-child');
		}

		// Prepare the asset to be stored.
		$asset->parent_id	= $parentId;
		$asset->name		= $name;
		$asset->title		= $title;
		if ($this->_rules instanceof JRules) {
			$asset->rules = (string) $this->_rules;
		}

		if (!$asset->check() || !$asset->store($updateNulls))
		{
			$this->setError($asset->getError());
			return false;
		}

		if (empty($this->asset_id))
		{
			// Update the asset_id field in this table.
			$this->asset_id = (int) $asset->id;

			$query = $this->_db->getQuery(true);
			$query->update($this->_db->quoteName($this->_tbl));
			$query->set('asset_id = '.(int) $this->asset_id);
			$query->where($this->_db->quoteName($k).' = '.(int) $this->$k);
			$this->_db->setQuery($query);

			if (!$this->_db->query())
			{
				$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $this->_db->getErrorMsg()));
				$this->setError($e);
				return false;
			}
		}

		return true;
	}	
}
?>
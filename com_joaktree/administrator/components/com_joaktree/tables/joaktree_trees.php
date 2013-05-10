<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_trees.php
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

class TableJoaktree_trees extends JTable
{
	var $id 				= null;
	var $app_id				= null;
	var $asset_id           = null;
	var $root_person_id		= null;
	var $published 			= null;
	var $name 				= null;
	var $theme_id			= null;
	var $indGendex			= null;
	var $indPersonCount		= null;
	var $indMarriageCount	= null;
	var $access				= null;
	var $holds				= null;
	var $robots				= null;
	var $catid          	= null;
	
	function __construct( &$db) {
		parent::__construct('#__joaktree_trees', 'id', $db);
	}
	
	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form `table_name.id`
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 */
	protected function _getAssetName() {
		return 'com_joaktree.application.'.(int) $this->app_id.'.tree.'.(int) $this->id;
	}

	/**
	 * Method to get the parent asset under which to register this one.
	 * By default, all assets are registered to the ROOT node with ID 1.
	 * The extended class can define a table and id to lookup.  If the
	 * asset does not exist it will be created.
	 *
	 * @param   JTable	A JTable object for the asset parent.
	 *
	 * @return  integer
	 */
	protected function _getAssetParentId($table = null, $id = null) {
		$asset	= JTable::getInstance('Asset');
		$asset->loadByName('com_joaktree.application.'.(int) $this->app_id);
		$parentId = empty($asset->id)?1:$asset->id;			
		return $parentId;
	}
	
}
?>
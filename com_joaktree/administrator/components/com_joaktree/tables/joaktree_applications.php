<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_applications.php
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

class TableJoaktree_applications extends JTable
{
	var $id 						= null;
	var $asset_id           		= null;
	var $title						= null;
	var $description				= null;
	var $programName				= null;
	var $params						= null;
	
	function __construct( &$db) {
		parent::__construct('#__joaktree_applications', 'id', $db);
	}
	
	/**
	 * Overloaded bind function
	 *
	 * @param	array		$hash named array
	 * @return	null|string	null is operation was satisfactory, otherwise returns an error
	 * @see JTable:bind
	 * @since 1.5
	 */
	public function bind($array, $ignore = array())
	{	
		if (isset($array['params']) && is_array($array['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);

			$array['params'] = (string)$registry;
		}

		// Bind the rules.
		if (isset($array['rules']) && is_array($array['rules'])) {
			$actions = array();
			$tmp 	 = array();
			$tmp[0]  = '';
			
			foreach ($array['rules'] as $action => $identities) {
				$identities = array_diff($identities, $tmp);
				$actions[$action] = $identities;					
			}
		
			$rules = new JRules($actions);		
			$this->setRules($rules);
		}
		
		return parent::bind($array, $ignore);
	}
	
	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form `table_name.id`
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 */
	protected function _getAssetName()
	{
		return 'com_joaktree.application.'.(int) $this->id;
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
	protected function _getAssetParentId($table = null, $id = null)
	{
		$asset	= JTable::getInstance('Asset');
		$asset->loadByName('com_joaktree');
		$parentId = empty($asset->id)?1:$asset->id;			
		return $parentId;
	}
	
}
?>
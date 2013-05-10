<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_maps.php
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

class TableJoaktree_maps extends JTable
{
	var $id 				= null;
	var $name				= null;
	var $selection			= null;
	var $service			= null;	
	var $app_id				= null;
	var $tree_id	 		= null;
	var $person_id	 		= null;
	var $subject	 		= null;
	var $relations          = null;
	var $period_start 		= null;
	var $period_end	 		= null;
	var $excludePersonEvents   = null;
	var $excludeRelationEvents = null;
	var $params				= null;	
	
	function __construct( &$db) {
		parent::__construct('#__joaktree_maps', 'id', $db);
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

		return parent::bind($array, $ignore);
	}
	
	public function check() {
		// mandatory fields
		if (empty($this->app_id)) {
			return false;
		}
		if (empty($this->name)) {
			return false;
		}
		if (empty($this->selection)) {
			return false;
		}
		if (empty($this->service)) {
			return false;
		}
		
		if (!empty($this->period_start) && ((int) $this->period_start > 9999)) {
			return false;
		} else {
			$this->period_start = (int) $this->period_start;
		}
		if (!empty($this->period_end) && ((int) $this->period_end > 9999)) {
			return false;
		} else {
			$this->period_end = (int) $this->period_end;
		}
		
		return true;
	}
	
	
}
?>
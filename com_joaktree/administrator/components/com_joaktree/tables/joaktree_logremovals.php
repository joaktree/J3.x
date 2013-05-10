<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_logremovals.php
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

class TableJoaktree_logremovals extends JTable
{
	var $id 				= null;
	var $app_id				= null;
	var $object_id			= null;
	var $object				= null;
	var $description		= null;
	
	function __construct( &$db) {
		parent::__construct('#__joaktree_logremovals', 'id', $db);
	}
	
	public function store() {
		$params = JoaktreeHelper::getJTParams($this->app_id);
		$indLogging = $params->get('indLogging');
		
		if ($indLogging) {
			// Logging is switched on
			$ret = parent::store();
		} else {
			// Logging is switched off
			$ret = true;	
		}
		
		return $ret;
	}
	
	public function check() {
		// mandatory fields
		if (empty($this->app_id)) {
			return false;
		}
		if (empty($this->object_id)) {
			return false;
		}
		if (empty($this->object)) {
			return false;
		}
		
		if (empty($this->description)) {
			return false;
		}
		
		return true;
	}
	
}
?>
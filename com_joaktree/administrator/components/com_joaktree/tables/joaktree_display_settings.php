<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_display_settings.php
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

class TableJoaktree_display_settings extends JTable
{
	var $id 			= null;
	var $code			= null;
	var $level 			= null;
	var $ordering		= null;
	var $published		= null;
	var $access 		= null;
	var $accessLiving	= null;
	var $altLiving		= null;

	function __construct( &$db) {
		parent::__construct('#__joaktree_display_settings', 'id', $db);
	}
}
?>
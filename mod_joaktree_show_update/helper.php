<?php
/**
 * Joomla! module Joaktree show update
 * file		JoaktreeHelper - helper.php
 *
 * @version	1.5.0
 * @author	Niels van Dantzig
 * @package	Joomla
 * @subpackage	Joaktree
 * @license	GNU/GPL
 *
 * Module linking articles to persons in Joaktree component
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class modJoaktreeShowUpdateHelper 
{
	public function getUpdate() {
		$db 	= JFactory::getDBO();	
	
		$query = $db->getQuery(true);
		$query->select(' DATE_FORMAT( value, "%e %b %Y" ) ');
		$query->from('#__joaktree_registry_items');
		$query->where('regkey = "LAST_UPDATE_DATETIME"');
						
		$db->setQuery( $query );
		$result = $db->loadResult();
		
		$result = str_replace ('Jan', JText::_('January')  , $result ); 
		$result = str_replace ('Feb', JText::_('February') , $result ); 
		$result = str_replace ('Mar', JText::_('March')    , $result ); 
		$result = str_replace ('Apr', JText::_('April')    , $result ); 
		$result = str_replace ('May', JText::_('May')      , $result ); 
		$result = str_replace ('Jun', JText::_('June')     , $result ); 
		$result = str_replace ('Jul', JText::_('July')     , $result ); 
		$result = str_replace ('Aug', JText::_('August')   , $result ); 
		$result = str_replace ('Sep', JText::_('September'), $result ); 
		$result = str_replace ('Oct', JText::_('October')  , $result ); 
		$result = str_replace ('Nov', JText::_('November') , $result ); 
		$result = str_replace ('Dec', JText::_('December') , $result ); 
			
		return $result;
	}
}


?>
<?php
/**
 * @version		$Id: table.php 11646 2009-03-01 19:34:56Z ian $
 * @package		Joomla.Framework
 * @subpackage	Table
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
//defined('_JEXEC') or die('Restricted access');

/**
 * Abstract Table class for tables with multiple field primary key
 *
 * Parent classes to all tables.
 *
 * @abstract
 * @package 	
 * @subpackage	Table
 * @since		1.0
 * @tutorial	
 */
class JMFPKTable extends JObject
{
	/**
	 * Name of the table in the db schema relating to child class
	 *
	 * @var 	string
	 * @access	protected
	 */
	var $_tbl		= '';

	/**
	 * Name of the primary key field in the table
	 *
	 * @var		array
	 * @access	protected
	 */
	var $_tbl_key	= array();

	/**
	 * Database connector
	 *
	 * @var		JDatabase
	 * @access	protected
	 */
	var $_db		= null;

	/**
	 * Object constructor to set table and key field
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @access protected
	 * @param string $table name of the table in the db schema relating to child class
	 * @param string $key: array containing names of the primary key fields in the table
	 * @param object $db JDatabase object
	 */
	function __construct( $table, $key, &$db )
	{
		$this->_tbl		= $table;
		$this->_tbl_key		= $key;
		$this->_db		=& $db;
	}

	/**
	 * Returns a reference to the a Table object, always creating it
	 *
	 * @param type 		$type 	 The table type to instantiate
	 * @param string 	$prefix	 A prefix for the table class name. Optional.
	 * @param array		$options Configuration array for model. Optional.
	 * @return database A database object
	 * @since 1.5
	*/
	function &getInstance( $type, $prefix = 'JMFPKTable', $config = array() )
	{
		$false = false;
		
		$type = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
		$tableClass = $prefix.ucfirst($type);
		
		if (!class_exists( $tableClass ))
		{

			jimport('joomla.filesystem.path');
			if($path = JPath::find(JMFPKTable::addIncludePath(), strtolower($type).'.php'))
			{
				require_once $path;
				
				if (!class_exists( $tableClass ))
				{
					JError::raiseWarning( 0, 'Table class ' . $tableClass . ' not found in file.' );
					return $false;
				}
			}
			else
			{
				JError::raiseWarning( 0, 'Table ' . $type . ' not supported. File not found.' );
				return $false;
			}
		}
		
		//Make sure we are returning a DBO object
		if (array_key_exists('dbo', $config))  {
			$db =& $config['dbo'];
		} else {
			$db = & JFactory::getDBO();
		}

		$instance = new $tableClass($db);

		return $instance;
	}

	/**
	 * Get the internal database object
	 *
	 * @return object A JDatabase based object
	 */
	function &getDBO()
	{
		return $this->_db;
	}

	/**
	 * Set the internal database object
	 *
	 * @param	object	$db	A JDatabase based object
	 * @return	void
	 */
	function setDBO(&$db)
	{
		$this->_db =& $db;
	}

	/**
	 * Gets the internal table name for the object
	 *
	 * @return string
	 * @since 1.5
	 */
	function getTableName()
	{
		return $this->_tbl;
	}

	/**
	 * Gets the internal primary key name
	 *
	 * @return array
	 * @since 1.5
	 */
	function getKeyName()
	{
		return $this->_tbl_key;
	}

	/**
	 * Resets the default properties
	 * @return	void
	 */
	function reset()
	{
		$k = $this->_tbl_key;
		foreach ($this->getProperties() as $name => $value)
		{
			if ( !in_array($name, $k) )
			{
				$this->$name	= $value;
			}
		}
	}

	/**
	 * Set all fields excluding PK to null
	 * @return	void
	 */
	function clear() {
		$k = $this->_tbl_key;
		foreach ($this->getProperties() as $name => $value)
		{
			if( is_array($value) or is_object($value) or $name[0] == '_' ) { // internal or NA field
				continue;
			}
			
			if( in_array($name, $k) ) { // PK not to be updated
				continue;
			}
			
			$this->$name	= null;
		}
	}

	/**
	 * Set all fields including PK to null
	 * @return	void
	 */
	function loadEmpty() {
		foreach ($this->getProperties() as $name => $value)
		{
			if( is_array($value) or is_object($value) or $name[0] == '_' ) { // internal or NA field
				continue;
			}
			
			$this->$name	= null;
		}
	}


	
	/**
	 * Binds a named array/hash to this object
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @access	public
	 * @param	$from	mixed	An associative array or object
	 * @param	$ignore	mixed	An array or space separated list of fields not to bind
	 * @return	boolean
	 */
	function bind( $from, $ignore=array() )
	{
		$fromArray	= is_array( $from );
		$fromObject	= is_object( $from );

		if (!$fromArray && !$fromObject)
		{
			$this->setError( get_class( $this ).'::bind failed. Invalid from argument' );
			return false;
		}
		if (!is_array( $ignore )) {
			$ignore = explode( ' ', $ignore );
		}
		foreach ($this->getProperties() as $k => $v)
		{
			// internal attributes of an object are ignored
			if (!in_array( $k, $ignore ))
			{
				if ($fromArray && isset( $from[$k] )) {
					$this->$k = $from[$k];
				} else if ($fromObject && isset( $from->$k )) {
					$this->$k = $from->$k;
				}
			}
		}
		return true;
	}

	/**
	 * Loads a row from the database and binds the fields to the object properties
	 *
	 * @access	public
	 * @param	mixed	Optional primary key.  If not specifed, the value of current key is used
	 * @return	boolean	True if successful
	 */
	function load()
	{
		$k = $this->_tbl_key;
		$where_pk = array();

		$this->reset();
		
		$query = $this->_db->getQuery(true);
		$query->select(' * ');
		$query->from(  ' '.$this->_db->quoteName($this->_tbl).' ');
		
		// check whether all fields are filled and build where statement
		for($i = 0; $i < count( $k ); $i++) {
			if ($this->$k[$i] === null) {
				return false;
			} else {
				$query->where(' '.$this->_db->quoteName($k[$i]).' = '.$this->_db->Quote($this->$k[$i]).' ');
			}
		}
		
		$this->_db->setQuery( $query );
		
		if ($result = $this->_db->loadAssoc( )) {
			return $this->bind($result);
		}
		else
		{
			if ($error = $this->_db->getErrorMsg()) {
				throw new JException($error);
			}		
			return false;
		}
	}

	/**
	 * Generic check method
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @access public
	 * @return boolean True if the object is ok
	 */
	function check()
	{
		return true;
	}

	/**
	 * Inserts a new row if id is zero or updates an existing row in the database table
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @access public
	 * @param boolean If false, null object variables are not updated
	 * @return null|string null if successful otherwise returns and error message
	 */
	function store( $updateNulls=true )
	{
		$k = $this->_tbl_key;
		$wheres = array();
		
		// execute query and retrieve result
		$query	= $this->_db->getQuery(true);
		$query->select(' 1 ');
		$query->from(  ' '.$this->_db->quoteName($this->_tbl).' ');		

		// check whether all fields are filled and build where statement
		for($i = 0; $i < count( $k ); $i++) {
			if ($this->$k[$i] === null) {
				$this->setError(get_class( $this ).'::store failed - primary key '.$this->$k.' is empty (null)');
				return false;
			} else {
				$wheres[] = ' '.$this->_db->quoteName($k[$i]).' = '.$this->_db->Quote($this->$k[$i]).' ';
			}
		}
		
		foreach ($wheres as $where) {
				$query->where($where);
		}
		
		$this->_db->setQuery( $query );
		$result = $this->_db->loadResult();		

		// if query has no result, record does not exists yet and is added here
		// if query has result, record exists and is updated here
		if (!$result) {
			$ret = $this->_db->insertObject( $this->_tbl, $this );
		} else {
			$query->clear();			
			$query->update($this->_db->quoteName($this->_tbl));
				
			$sets = array();
			
			foreach ($this->getProperties() as $name => $value) {			
				if( is_array($value) or is_object($value) or $name[0] == '_' ) { // internal or NA field					
					continue;
				}
				
				if( in_array($name, $k) ) { // PK not to be updated					
					continue;
				}
				
				if ($value === null) {					
					if ($updateNulls) {
						$val = 'NULL';
					} else {
						continue;
					}
				} else {
					//$val = $this->_db->isQuoted( $name ) ? $this->_db->Quote( $value ) : (int) $value;
					$val = $this->_db->Quote( $value );
				}
				
				$sets[] = $this->_db->quoteName( $name ) . '=' . $val;
			}			
			
			if (count($sets) > 0) {
				foreach ($sets as $set) {
					$query->set($set);
				}
				foreach ($wheres as $where) {
						$query->where($where);
				}
				
				$this->_db->setQuery($query);			
				$ret = $this->_db->query();
			} else {
				$ret = true;
			}
		}
		
		if( !$ret )
		{
			$this->setError(get_class( $this ).'::store failed - '.$this->_db->getErrorMsg());
			return false;
		}
		else
		{
			return true;
		}
	}


	/**
	 * Default delete method
	 *
	 * can be overloaded/supplemented by the child class
	 *
	 * @access public
	 * @return true if successful otherwise returns and error message
	 */
	function delete()
	{
		$k = $this->_tbl_key;
		
		$query	= $this->_db->getQuery(true);
		$query->delete($this->_db->quoteName($this->_tbl));
		
		// check whether all fields are filled and build where statement
		for($i = 0; $i < count( $k ); $i++) {
			if ($this->$k[$i] === null) {
				return false;
			} else {
				$query->where(' '.$this->_db->quoteName($k[$i]).' = '.$this->_db->Quote($this->$k[$i]).' ');
			}
		}

		$this->_db->setQuery( $query );

		if ($this->_db->query())
		{
			return true;
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Default truncate method
	 *
	 * can be overloaded/supplemented by the child class
	 *
	 * @access public
	 * @return true if successful otherwise returns and error message
	 */
	function truncate()
	{
		$query = 'TRUNCATE '.$this->_db->quoteName( $this->_tbl );
		$this->_db->setQuery( $query );

		if ($this->_db->query())
		{
			return true;
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}
	
	/**
	 * Description
	 *
	 * @access public
	 * @param $oid
	 * @param $log
	 */
	function hit()
	{
		if (!in_array( 'hits', array_keys($this->getProperties()) )) {
			return;
		}

		$k = $this->_tbl_key;
		
		$query 	= $this->_db->getQuery(true);
		$query->update($this->_db->quoteName($this->_tbl));
		$query->set(' hits = ( hits + 1 ) ');
				
		// check whether all fields are filled and build where statement
		for($i = 0; $i < count( $k ); $i++) {
			if ($this->$k[$i] === null) {
				return false;
			} else {
				$query->where(' '.$this->_db->quoteName($k[$i]).' = '.$this->_db->Quote($this->$k[$i]).' ');
			}
		}

		$this->_db->setQuery( $query );
		$this->_db->query();
		$this->hits++;
	}

	/**
	 * Generic save function
	 *
	 * @access	public
	 * @param	array	Source array for binding to class vars
	 * @param	string	Filter for the order updating
	 * @param	mixed	An array or space separated list of fields not to bind
	 * @returns TRUE if completely successful, FALSE if partially or not succesful.
	 */
	function save( $source, $order_filter='', $ignore='' )
	{
		if (!$this->bind( $source, $ignore )) {
			return false;
		}
		if (!$this->check()) {
			return false;
		}
		if (!$this->store()) {
			return false;
		}
		if (!$this->checkin()) {
			return false;
		}
		if ($order_filter)
		{
			$filter_value = $this->$order_filter;
			$this->reorder( $order_filter ? $this->_db->quoteName( $order_filter ).' = '.$this->_db->Quote( $filter_value ) : '' );
		}
		$this->setError('');
		return true;
	}

	/**
	 * Export item list to xml
	 *
	 * @access public
	 * @param boolean Map foreign keys to text values
	 */
	function toXML( $mapKeysToText=false )
	{
		$xml = '<record table="' . $this->_tbl . '"';

		if ($mapKeysToText)
		{
			$xml .= ' mapkeystotext="true"';
		}
		$xml .= '>';
		foreach (get_object_vars( $this ) as $k => $v)
		{
			if (is_array($v) or is_object($v) or $v === NULL)
			{
				continue;
			}
			if ($k[0] == '_')
			{ // internal field
				continue;
			}
			$xml .= '<' . $k . '><![CDATA[' . $v . ']]></' . $k . '>';
		}
		$xml .= '</record>';

		return $xml;
	}

	/**
	 * Add a directory where JMFPKTable should search for table types. You may
	 * either pass a string or an array of directories.
	 *
	 * @access	public
	 * @param	string	A path to search.
	 * @return	array	An array with directory elements
	 * @since 1.5
	 */
	function addIncludePath( $path=null )
	{
		static $paths;

		if (!isset($paths)) {
			$paths = array( dirname( __FILE__ ) );
		}

		// just force path to array
		settype($path, 'array');

		if (!empty( $path ) && !in_array( $path, $paths ))
		{
			// loop through the path directories
			foreach ($path as $dir)
			{
				// no surrounding spaces allowed!
				$dir = trim($dir);

				// add to the top of the search dirs
				// so that custom paths are searched before core paths
				array_unshift($paths, $dir);
			}
		}
		return $paths;
	}
}

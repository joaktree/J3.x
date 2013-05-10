<?php
/**
 * @package     MapsByJoaktree
 * @subpackage  Service
 *
 * @copyright   Joaktree.com
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

JLoader::register('MBJProvider', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'services'.DS.'provider.php');

/**
 * Provider class.
 *
 * @package     MapsByJoaktree
 * @subpackage  Service
 * @since       1.0
 */
class MBJProviderYahoo extends MBJProvider {
	/**
	 * The name of the service provider.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected static $name = 'Yahoo';
	
	/**
	 * The indication whether API key is needed for services.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected static $needsAPIkey = true;
	
	/**
	 * The copyright.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected static $copyright = 'Map Copyright;&nbsp;&copy;&nbsp;Yahoo! Inc. 2008, All Rights Reserved';
		
	/**
	 * The license for services.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected static $license = '';
	
	/**
	 * Test to see if this provider is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   1.0
	 */
	public static function test()
	{
		return false;
	}
	
	/**
	 * Constructor.
	 *
	 * @param   array  $options  List of options used to configure the connection
	 *
	 * @since   11.1
	 */
	public function __construct($options)
	{
		$keys = self::getKeys();
		
		// Initialise object variables.
		$this->APIkey = (isset($keys->yahooAPIkey)) ? $keys->yahooAPIkey : '';
		parent::__construct($options);
	}
	
	public function getName() {
		return self::$name;
	}
	
	public function getLicense() {
		return self::$license;
	}
	
}
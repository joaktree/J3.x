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
class MBJProviderMapquest extends MBJProvider {
	/**
	 * The name of the service provider.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected static $name = 'MapQuest';
	
	/**
	 * The indication whether API key is needed for services.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected static $needsAPIkey = false;
	
	/**
	 * The copyright.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected static $copyright = '';
		
	/**
	 * The license for services.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected static $license = 'http://developer.mapquest.com/web/info/terms-of-use';
	
	/**
	 * Test to see if this provider is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   1.0
	 */
	public static function test()
	{
		return true;
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
		parent::__construct($options);
	}
		
	/**
	 * Needs API key for services.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public function needsAPIkey() {
		return self::$needsAPIkey;
	}

	public function getName() {
		return self::$name;
	}
	
	public function getLicense() {
		return self::$license;
	}
	
}
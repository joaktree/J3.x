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

JLoader::register('MBJService', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'services'.DS.'service.php');

/**
 * Service connector class.
 *
 * @package     MapsByJoaktree
 * @subpackage  Service
 * @since       1.0
 */
class MBJServiceGeocode extends MBJService {
	/**
	 * The name of the service.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected static $service = 'geocode';
	
	/**
	 * The max size of calls per set.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected static $maxLoadSize;
	
	protected static $resultSet = array();
	
	/**
	 * Test to see if service exists.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   1.0
	 */
	public static function test() {
		return false;
	}
		
	public static function getInstance($options = array()) {
		// Sanitize the service connector options.
		$options['service']  = (isset($options['service'])) ?  $options['service'] : self::$service;
		return parent::getInstance($options);
	}
	
	/**
	 * Constructor.
	 *
	 * @param   array  $options  List of options used to configure the object
	 *
	 * @since   1.0
	 */
	protected function __construct($options) {
		$keys = $this->getKeys();
		
		// Initialise object variables.
		self::$maxLoadSize = (isset($options['size'])) 
							 ? $options['size'] 
							 : ( ( $keys->maxloadsize )
							 	 ? $keys->maxloadsize
								 : 100
								 );	
		parent::__construct($options);
	}
	
	public function findLocationBulk(&$data) {
		if (!is_array($data) || !count($data)) {
			// no object
			return false;
		}
		
		foreach((array) $data as $item) {
			$status = $this->findLocation($item);
		}
		
		return true;
	}
		
	public function findLocation(&$data) {

		if (!is_object($data)) {
			// no object
			return false;
		}
		
		// set the parameters
		static $delay 		= 0;
		$geocode_pending = true;
		while ($geocode_pending) {
			$request_url = $this->getUrl($data);

			// Try to fetch a response from the service.
			//$xml = simplexml_load_file($request_url) or die($this->service.": url not loading");
			if (!($xml = simplexml_load_file($request_url))) {
				// it is not a file - perhaps a string 
				if (!($xml = simplexml_load_string($request_url))) {
					// it is not a string ... we stop
					throw new MBJServiceException(JText::sprintf('MBJ_SERVICE_URL_NOT_LOADING', $this->provider->provider, $request_url));
				}
			}
			$this->count++;	
			
			$status = $this->getStatus($xml);
		    if (strcmp($status, "found") == 0) {
		      // Successful geocode
		      $geocode_pending = false;
		      
		      // Format coordinatest: Longitude, Latitude, Altitude
		      $coordinates = $this->getCoordinates($xml);
		      $data->longitude = $coordinates['lon'];
		      $data->latitude  = $coordinates['lat'];
		      $data->results   = $this->getNumberOfHits($xml);
		      $data->result_address = $this->getResultAddress($xml);
		      
		      $this->resultSet = $this->setResultSet($xml);
		      
		    } else if (strcmp($status, "wait") == 0) {
		      // sent geocodes too fast
		      $delay += 100000;
		    } else {
		      // failure to geocode
		      $geocode_pending = false;
		      $this->errorNum++;		      
		      $this->log[] = JText::sprintf('JT_GEOCODE_FAILED', $data->value, $status);
		      $data->results   = 0;
		      $data->result_address = null;

		      if (isset($this->resultSet) && is_array($this->resultSet)) {
		      	$this->resultSet = array_slice($this->resultSet, 0, 0);
		      } else {
		      	$this->resultSet = array();
		      }
		    }
		    usleep($delay);				
		}
		
		$data->indServerProcessed = 1;
		return $status;	
	}
	
	public function getResultSet() {
		return $this->resultSet;
	}
	
	/**
	 * Mazime size of calls in a set
	 *
	 * @return  size  
	 *
	 * @since   1.0
	 */
	public static function getMaxLoadSize() {
		return self::$maxLoadSize;
	}
	
}

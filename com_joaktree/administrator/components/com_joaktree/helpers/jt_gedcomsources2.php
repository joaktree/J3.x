<?php
/**
 * Joomla! component Joaktree
 * file		jt_gedcomsources model - jt_gedcomsources.php
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

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries

class jt_gedcomsources2 extends JObject {
	function __construct($app_id) {
		$this->application = JFactory::getApplication();
		
		// initialize table
		$this->sources = & JMFPKTable::getInstance('joaktree_sources', 'Table');
		$this->logs	   = & JTable::getInstance('joaktree_logs', 'Table');
		
		// set the application id for these tables
		$this->app_id = $app_id;
		$this->sources->set('app_id', $app_id);
		
		// logs
		$this->logs->set('app_id', $app_id);
		$this->logs->set('object', 'sour');
	}
	

	/*
	** Main function to process all sources from gedcom file.
	*/
	public function process( &$source_id, &$row_lines) {
		static $teller;
		$teller++;
		
		// start every loop with empty source record
		$this->sources->loadEmpty();
		$this->sources->set( 'app_id', $this->app_id );		
		$this->sources->set( 'id', $source_id );
				
		// logs
		$this->logs->set('object_id', $source_id);
		
		// loop through lines related to the source
		foreach ($row_lines as $row_line_num => $row_line) {
			switch ($row_line['level']) {
				case "1": switch ($row_line['tag']) {
						case "AUTH": 	
							  $this->sources->set( 'author', $row_line['value']);
							  break;
						case "TITL":	
							  $this->sources->set( 'title', $row_line['value']);
							  break;
						case "PUBL":	
							  $this->sources->set( 'publication', $row_line['value']);
							  break;
						case "TEXT":	
							  $this->sources->set( 'information', $row_line['value']);
							  break;
						case "REPO":	
							  $this->sources->set( 'repo_id', rtrim(ltrim( $row_line['value'], '@'), '@') );
							  break;
						default: 	
							  break;
					 }
					 break;
				default: break;
			} // end of level switch
		} // end of loop throuth source lines
			
		// store record
		$ret = $this->sources->store(); 
		
		// log update
		if ($ret) {	$ret = $this->logs->logChangeDateTime(); }
		
		// if insert or update went ok, continue with next source; else stop
		if ( !$ret ) { 
			$this->application->enqueueMessage( JText::sprintf( 'JTGEDCOM_MESSAGE_NOSUCSOURCE', $source_id ), 'notice' ) ;
		}
		
		return $ret;
	}
}
?>
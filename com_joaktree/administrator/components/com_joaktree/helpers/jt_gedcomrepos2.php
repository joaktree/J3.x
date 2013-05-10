<?php
/**
 * Joomla! component Joaktree
 * file		jt_gedcomrepos model - jt_gedcomrepos.php
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


class jt_gedcomrepos2 extends JObject {
	function __construct($app_id) {
		$this->application = JFactory::getApplication();
		
		// initialize table
		$this->repos = & JMFPKTable::getInstance('joaktree_repositories', 'Table');	
		$this->logs				= & JTable::getInstance('joaktree_logs', 'Table');
		
		// set the application id for these tables
		$this->app_id = $app_id;
		$this->repos->set('app_id', $app_id);
		
		// logs
		$this->logs->set('app_id', $app_id);
		$this->logs->set('object', 'repo');
	}	
	
	/*
	** Main function to process all repositories from gedcom file.
	*/
	public function process( &$repo_id, &$row_lines) {
		// start every loop with empty repository record
		$this->repos->loadEmpty();
		$this->repos->set( 'app_id', $this->app_id );
		$this->repos->set( 'id', $repo_id );
		
		// logs
		$this->logs->set('object_id', $repo_id);
		
		// loop through lines related to the repository
		foreach ($row_lines as $row_line_num => $row_line) {
			switch ($row_line['level']) {
				case "1": switch ($row_line['tag']) {
						case "NAME":	
							$this->repos->set( 'name', $row_line['value']);
							break;
						case "WWW":	
							$this->repos->set( 'website', $row_line['value']);
							break;
						default:	
							break;
					  }
					  break;
				default:  break;
			} // end of level switch
		} // end of loop throuth repository lines
		
		// store  record
		$ret = $this->repos->store();
		
		// log update
		if ($ret) {	$ret = $this->logs->logChangeDateTime(); }
		
		// if insert or update went ok, continue with next source; else stop
		if ( !$ret ) { 
			$this->application->enqueueMessage( JText::sprintf( 'JTGEDCOM_MESSAGE_NOSUCREPO', $repo_id ), 'notice' ) ;
		}
		
		return $ret;
	}
}
?>
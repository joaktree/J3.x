<?php
/**
 * Joomla! component Joaktree
 * file		jt_gedcomdocuments model - jt_gedcomdocuments.php
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

class jt_gedcomdocuments2 extends JObject {
	function __construct($app_id) {
		$this->application = JFactory::getApplication();
		
		// initialize table
		$this->documents = & JMFPKTable::getInstance('joaktree_documents', 'Table');
		
		// set the application id for these tables
		$this->app_id = $app_id;
		$this->documents->set('app_id', $app_id);
	}
	
	/*
	** Main function to process all documents from gedcom file.
	*/
	public function process( &$document_id, &$row_lines) {
			
		// start every loop with empty document record
		$this->documents->loadEmpty();
		$this->documents->set( 'app_id', $this->app_id );
		$this->documents->set( 'id', $document_id );
								
		// loop through lines related to the document
		foreach ($row_lines as $row_line_num => $row_line) {
			switch ($row_line['tag']) {
				case "FORM": 	$this->documents->set('fileformat', $row_line['value'] );
								break;
								
				case "FILE":	$this->documents->set('file', $row_line['value'] );
								break;
								
				case "TITL":	$this->documents->set('title', $row_line['value'] );
								break;
								
				case "NOTE":	// try to strip @ and save in local value
								$tmpValue = trim($row_line['value'], '@');
								if ($row_line['value'] != $tmpValue) {
									// @ are stripped and local value is therefore different than value
									// this is an note_id
									$this->documents->set('note_id', $tmpValue );
								} else {
									// stripping had no effect. values are identical
									// this is a real note
									$this->documents->set('note', $row_line['value'] );
								}					  								
								break;
								
				case "SOUR":	$this->documents->set('indCitation', true );
								break;
								
				default:		// no action
								break;
			}
		} // end of loop through document lines

		// store record
		$ret = $this->documents->store(); 		

		// if insert or update went ok, continue with next document; else stop
		if ( !$ret ) { 
			$this->application->enqueueMessage( JText::sprintf('JTGEDCOM_MESSAGE_NOSUCDOC', $document_id), 'notice' ) ;
		}
		
		return $ret;
	}
}
?>
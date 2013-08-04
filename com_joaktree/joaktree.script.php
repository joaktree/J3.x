<?php
/**
 * Joomla! component Joaktree
 * file		script.php
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

defined('_JEXEC') or die;

/* 
 * An optional script file (PHP code that is run before, during and/or after installation, 
 * uninstallation and upgrading) can be defined using a <scriptfile> element. 
 * This file should contain a class named "<element_name>IntallerScript" where <element_name> 
 * is the name of your extension (e.g. com_componentname, mod_modulename, etc.). 
 * Plugins requires to state the group (e.g. plgsystempluginname). 
 * 
 * The structure of the class is as follows:
 */

class com_joaktreeInstallerScript
{
        /**
         * Called on installation
         *
         * @param   JAdapterInstance  $adapter  The object responsible for running this script
         *
         * @return  boolean  True on success
         */
        public function install($installer){
			// New installation
			$version = (string) JInstaller::getInstance()->getManifest()->version;
			
			// Initialize the database
			$db 			= JFactory::getDBO();
        	$update_queries = array();
			$application 	= JFactory::getApplication();
									
			// Table joaktree_admin_persons
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_admin_persons '
			   .'( app_id           tinyint(4)            NOT NULL '
			   .', id               varchar(20)           NOT NULL '
			   .', default_tree_id  int(11)      default  NULL '
			   .', published        tinyint(1)            NOT NULL '
			   .', access           int(11)      unsigned NOT NULL '
			   .', living           tinyint(1)            NOT NULL '
			   .', page             tinyint(1)            NOT NULL '
			   .', robots           tinyint(2)            NOT NULL default 0 ' 
			   .', map              tinyint(1)            NOT NULL default 0 ' 
			   .', PRIMARY KEY  (app_id, id) '
			   .') '
			   .'COMMENT="'.$version.'" ';
			// end: joaktree_admin_persons 
			   
			// Table joaktree_applications
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_applications '
			   .'( id               tinyint(4)  unsigned  NOT NULL auto_increment '
			   .', asset_id         int(10)      unsigned NOT NULL '
			   .', title            varchar(30)           NOT NULL '
			   .', description      varchar(100)          NOT NULL '
			   .', programName      varchar(30)           NOT NULL '
			   .', params           varchar(2048)         NOT NULL '
			   .', PRIMARY KEY  (id) '
			   .') '
			   .'COMMENT="'.$version.'" ';
			// end: joaktree_applications 
			
			// Table joaktree_citations
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_citations '
			   .'( objectType       enum( ' . $db->Quote( 'person' )
			                          .', ' . $db->Quote( 'personName' )
			                          .', ' . $db->Quote( 'personEvent' )
			                          .', ' . $db->Quote( 'personNote' )
			                          .', ' . $db->Quote( 'personDocument' )
			                          .', ' . $db->Quote( 'relation' )
			                          .', ' . $db->Quote( 'relationEvent' )
			                          .', ' . $db->Quote( 'relationNote' )
						  .')                 NOT NULL '
			   .', objectOrderNumber smallint(2)          NOT NULL default 0 '
			   .', app_id           tinyint(4)            NOT NULL '
			   .', person_id_1      varchar(20)           NOT NULL '
			   .', person_id_2      varchar(20)           NOT NULL default '. $db->Quote( 'EMPTY' ).' '
			   .', source_id        varchar(20)           NOT NULL '
			   .', orderNumber      smallint(2)           NOT NULL '
			   .', dataQuality      tinyint(2)                NULL '
			   .', page             varchar(250)  default     NULL '
			   .', quotation        varchar(250)  default     NULL '
			   .', note             varchar(250)  default     NULL '
			   .', PRIMARY KEY  (objectType,objectOrderNumber,app_id,person_id_1,person_id_2,source_id,orderNumber) '
			   .', KEY person_id (app_id,person_id_1) '
			   .') '
			   .'COMMENT="'.$version.'" ';   
			// end: joaktree_citations
		
			// Table joaktree_display_settings
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_display_settings '
			   .'( id               int(11)      unsigned NOT NULL auto_increment '
			   .', code             varchar(4)            NOT NULL '
			   .', level            enum( ' . $db->Quote( 'person' )
			                          .', ' . $db->Quote( 'name' )
			                          .', ' . $db->Quote( 'relation' )
						  .')                 NOT NULL '
			   .', ordering         tinyint(3)            NOT NULL '
			   .', published        tinyint(1)   unsigned NOT NULL default 0 '
			   .', access           tinyint(3)   unsigned NOT NULL default 0 '
			   .', accessLiving     tinyint(3)   unsigned NOT NULL default 0 '
			   .', altLiving        tinyint(3)            NOT NULL default 0 '
			   .', PRIMARY KEY  (id) '
			   .', UNIQUE KEY UK_CODE_LEVEL (code, level) '
			   .') '
			   .'COMMENT="'.$version.'" ';
			   
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("ENGA", "relation", 1, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("MARB", "relation", 2, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("MARC", "relation", 3, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("MARS", "relation", 4, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("MARL", "relation", 5, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("MARR", "relation", 6, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("ANUL", "relation", 7, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("DIV",  "relation", 8, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("NCHI", "relation", 9, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("EVEN", "relation", 10, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("ENOT", "relation", 11, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("ESOU", "relation", 12, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("CAST", "person", 1, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("TITL", "person", 2, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("BIRT", "person", 3, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("BAPM", "person", 4, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("BRTM", "person", 5, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("CHR",  "person", 6, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("BLES", "person", 7, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("BARM", "person", 8, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("BASM", "person", 9, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("CONF", "person", 10, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("ADOP", "person", 11, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("CHRA", "person", 12, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("DEAT", "person", 13, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("BURI", "person", 14, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("CREM", "person", 15, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("YART", "person", 16, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("FCOM", "person", 17, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("EDUC", "person", 18, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("GRAD", "person", 19, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("OCCU", "person", 20, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("RETI", "person", 21, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("EMIG", "person", 22, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("IMMI", "person", 23, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("NATU", "person", 24, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("NATI", "person", 25, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("RESI", "person", 26, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("RELI", "person", 27, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("DSCR", "person", 28, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("EVEN", "person", 29, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("NOTE", "person", 30, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("ENOT", "person", 31, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("SOUR", "person", 32, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("ESOU", "person", 33, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("NAME", "name", 1, 1, 1, 1, 1)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("GIVN", "name", 2, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("NICK", "name", 3, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("ADPN", "name", 4, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("AKA",  "name", 5, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("BIRN", "name", 6, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("CENN", "name", 7, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("CURN", "name", 8, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("FRKA", "name", 9, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("HEBN", "name", 10, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("INDG", "name", 11, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("MARN", "name", 12, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("RELN", "name", 13, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("OTHN", "name", 14, 0, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("NOTE", "name", 15, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("SOUR", "name", 16, 1, 1, 3, 0)';
			$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("SURN", "name", 17, 0, 1, 3, 0)';
			// end: joaktree_display_settings
			   
			// Table joaktree_documents
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_documents '
			   .'( app_id           tinyint(4)            NOT NULL '
			   .', id               varchar(20)           NOT NULL '
			   .', file             varchar(200)          NOT NULL '
			   .', fileformat       varchar(10)           NOT NULL '
			   .', indCitation      tinyint(1)   unsigned NOT NULL default 0 '
			   .', note_id          varchar(20)               NULL '
			   .', title            varchar(100)              NULL '
			   .', note             text         default      NULL '
			   .', PRIMARY KEY  (app_id,id) '
			   .') '
			   .'COMMENT="'.$version.'" ';
			// end: joaktree_documents
			   
			// Table joaktree_gedcom_objectlines
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_gedcom_objectlines '
			   .'( id               int(11)      unsigned NOT NULL auto_increment '
			   .', object_id        varchar(20)           NOT NULL '
			   .', order_nr         int(11)               NOT NULL '
			   .', level            int(11)               NOT NULL '
			   .', tag              varchar(20)           NOT NULL '
			   .', value            text         default      NULL '
			   .', subtype          enum( '   . $db->Quote( 'spouse' )
			                          	.', ' . $db->Quote( 'partner' )
						  				.', ' . $db->Quote( 'natural' )
						  				.', ' . $db->Quote( 'adopted' )
						  				.', ' . $db->Quote( 'step' )
						  				.', ' . $db->Quote( 'foster' )
						  				.', ' . $db->Quote( 'legal' )
						  				.')                   NULL '
			   .', PRIMARY KEY  (id) '
			   .') '
			   .'COMMENT="'.$version.'" ';
			// end: joaktree_gedcom_objectlines
			   
			// Table joaktree_gedcom_objects   
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_gedcom_objects '
			   .'( id               int(11)      unsigned NOT NULL auto_increment '
			   .', tag              varchar(4)            NOT NULL '
			   .', value            varchar(50)  default  NULL '
			   .', PRIMARY KEY  (id) '
			   .') '
			   .'COMMENT="'.$version.'" ';
			// end: joaktree_gedcom_objects
			
			// Table joaktree_locations   
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_locations '
			   .'( id               int(11)      unsigned NOT NULL AUTO_INCREMENT '
			   .', indexLoc         varchar(1)            NOT NULL '
			   .', value            varchar(75)           NOT NULL '
			   .', latitude         decimal(10,7)             NULL '
			   .', longitude        decimal(10,7)             NULL '
			   .', indServerProcessed tinyint(1) unsigned NOT NULL default 0 '
			   .', indDeleted       tinyint(1)   unsigned NOT NULL default 0 '
			   .', results          tinyint(2)   unsigned     NULL '
			   .', resultValue      varchar(100)              NULL '
			   .', PRIMARY KEY  (id) '
			   .', KEY indexLoc (indexLoc) '
			   .') '
			   .'COMMENT="'.$version.'" ';	   
			// end: joaktree_locations
			   
			// Table joaktree_logremovals   
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_logremovals '
			   .'( id               int(10)      unsigned NOT NULL AUTO_INCREMENT '
			   .', app_id           tinyint(4)   unsigned NOT NULL '
			   .', object_id        varchar(20)           NOT NULL '
			   .', object           enum( ' . $db->Quote( 'prsn' )
			                          .', ' . $db->Quote( 'sour' )
			                          .', ' . $db->Quote( 'repo' )
			                          .', ' . $db->Quote( 'docu' )
			                          .', ' . $db->Quote( 'note' )
			                          .')                 NOT NULL '
			   .', description      varchar(100)          NOT NULL '
			   .', PRIMARY KEY  (id) '
			   .', KEY objectIndex2 (app_id,object_id) '
			   .') '
			   .'COMMENT="'.$version.'" ';	   
			// end: joaktree_logremovals
		    
			// Table joaktree_logs   
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_logs '
			   .'( id               int(10)      unsigned NOT NULL AUTO_INCREMENT '
			   .', app_id           tinyint(4)   unsigned NOT NULL '
			   .', object_id        varchar(20)           NOT NULL '
			   .', object           enum( ' . $db->Quote( 'prsn' )
			                          .', ' . $db->Quote( 'sour' )
			                          .', ' . $db->Quote( 'repo' )
			                          .', ' . $db->Quote( 'docu' )
			                          .', ' . $db->Quote( 'note' )
			                          .')                 NOT NULL '
			   .', changeDateTime   datetime              NOT NULL '
			   .', logevent         varchar(9)            NOT NULL '
			   .', user_id          int(11)               NOT NULL '
			   .', PRIMARY KEY  (id) '
			   .', KEY objectIndex1 (app_id,object_id) '
			   .') '
			   .'COMMENT="'.$version.'" ';
		    // end: joaktree_logs
		
			// Table joaktree_maps   
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_maps '
			   .'( id               int(11)      unsigned NOT NULL AUTO_INCREMENT '
			   .', name             varchar(50)           NOT NULL '
			   .', selection		enum( ' . $db->Quote( 'tree' )
			                          .', ' . $db->Quote( 'person' )
			   						  .', ' . $db->Quote( 'location' )
			                          .')                 NOT NULL '
			   .', service          varchar(20)           NOT NULL default '.$db->Quote( 'staticmap' ).' '
			   .', app_id           tinyint(4)   unsigned NOT NULL '
			   .', relations        tinyint(1)   unsigned NOT NULL default 0 '
			   .', params           varchar(2048)         NOT NULL '
			   .', tree_id          tinyint(4)   unsigned     NULL '
			   .', person_id        varchar(20)               NULL '
			   .', subject          varchar(50)               NULL '
			   .', period_start     int(11)      unsigned     NULL '
			   .', period_end       int(11)      unsigned     NULL '
			   .', excludePersonEvents   varchar(200)         NULL '
			   .', excludeRelationEvents varchar(200)         NULL '
			   .', PRIMARY KEY  (id) '
			   .') '
			   .'COMMENT="'.$version.'" ';	   
			// end: joaktree_maps
			   
			// Table joaktree_notes   
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_notes '
			   .'( app_id           tinyint(4)            NOT NULL '
			   .', id               varchar(20)           NOT NULL '
			   .', value            text                      NULL '
			   .', PRIMARY KEY  (app_id,id) '
			   .') '
			   .'COMMENT="'.$version.'" ';
			// end: joaktree_notes
			   
			// Table joaktree_persons   
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_persons '
			   .'( app_id           tinyint(4)            NOT NULL '
			   .', id               varchar(20)           NOT NULL '
			   .', indexNam         varchar(1)            NOT NULL '
			   .', firstName        varchar(50)           NOT NULL '
			   .', patronym         varchar(50)  default      NULL '
			   .', namePreposition  varchar(15)  default      NULL '
			   .', familyName       varchar(50)           NOT NULL '
			   .', sex              char(1)               NOT NULL '
			   .', indNote          tinyint(1)   unsigned NOT NULL default 0 '
			   .', indCitation      tinyint(1)   unsigned NOT NULL default 0 '
			   .', indHasParent     tinyint(1)   unsigned NOT NULL default 0 '
			   .', indHasPartner    tinyint(1)   unsigned NOT NULL default 0 '
			   .', indHasChild      tinyint(1)   unsigned NOT NULL default 0 '
			   .', lastUpdateTimeStamp '
			   .'                   timestamp             NOT NULL '
			   .'                   DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP '
			   .', indIsWitness     tinyint(1)   unsigned NOT NULL default 0 '
			   .', prefix           varchar(20)               NULL '
			   .', suffix           varchar(20)               NULL '
			   .', PRIMARY KEY  (app_id,id) '
			   .', KEY IndexNam (indexNam) '
			   .') '
			   .'COMMENT="'.$version.'" ';	   
			// end: joaktree_persons
		
			// Table joaktree_person_documents   
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_person_documents '
			   .'( app_id           tinyint(4)            NOT NULL '
			   .', person_id        varchar(20)           NOT NULL '
			   .', document_id      varchar(20)           NOT NULL '
			   .', PRIMARY KEY  (app_id,person_id,document_id) '
			   .') '
			   .'COMMENT="'.$version.'" ';
			// end: joaktree_person_documents
				   
			// Table joaktree_person_events
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_person_events '
			   .'( app_id           tinyint(4)            NOT NULL '
			   .', person_id        varchar(20)           NOT NULL '
			   .', orderNumber      smallint(2)           NOT NULL '
			   .', code             varchar(4)            NOT NULL '
			   .', indNote          tinyint(1)   unsigned NOT NULL default 0 '
			   .', indCitation      tinyint(1)   unsigned NOT NULL default 0 '
			   .', type             varchar(30)  default      NULL '
			   .', eventDate        varchar(40)  default      NULL '
			   .', loc_id           int(11)      default      NULL '
			   .', location         varchar(75)  default      NULL '
			   .', value            varchar(100) default      NULL '
			   .', PRIMARY KEY  (app_id,person_id,orderNumber) '
			   .', KEY LOC1     (location) '
			   .', KEY LOI1     (loc_id) '
			   .') '
			   .'COMMENT="'.$version.'" ';
			// end: joaktree_person_events
			   
			// Table joaktree_person_names
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_person_names '
			   .'( app_id           tinyint(4)            NOT NULL '
			   .', person_id        varchar(20)           NOT NULL '
			   .', orderNumber      smallint(2)           NOT NULL '
			   .', code             varchar(4)            NOT NULL '
			   .', indNote          tinyint(1)   unsigned NOT NULL default 0 '
			   .', indCitation      tinyint(1)   unsigned NOT NULL default 0 '
			   .', eventDate        varchar(40)  default      NULL '
			   .', value            varchar(100) default      NULL '
			   .', PRIMARY KEY  (app_id,person_id,orderNumber) '
			   .') '
			   .'COMMENT="'.$version.'" ';   
			// end: joaktree_person_names
			   
			// Table joaktree_person_notes
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_person_notes '
			   .'( app_id           tinyint(4)            NOT NULL '
			   .', person_id        varchar(20)           NOT NULL '
			   .', orderNumber      smallint(2)           NOT NULL '
			   .', indCitation      tinyint(1)   unsigned NOT NULL default 0 '
			   .', nameOrderNumber  smallint(2)  default      NULL '
			   .', eventOrderNumber smallint(2)  default      NULL '
			   .', note_id          varchar(20)               NULL '
			   .', value            text '
			   .', PRIMARY KEY  (app_id,person_id,orderNumber) '
			   .') '
			   .'COMMENT="'.$version.'" ';
			// end: joaktree_person_notes
			   
			// Table joaktree_registry_items
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_registry_items '
			   .'( id               int(11)      unsigned NOT NULL auto_increment '
			   .', regkey           varchar(255)          NOT NULL '
			   .', value            varchar(2048)         NOT NULL '
			   .', PRIMARY KEY  (id) '
			   .', UNIQUE KEY UK_KEY (regkey) '
			   .') '
			   .'COMMENT="'.$version.'" ';
			   
			$update_queries[] = 'INSERT INTO #__joaktree_registry_items (regkey, value) VALUES ("LAST_UPDATE_DATETIME", NOW() )';
			$update_queries[] = 'INSERT INTO #__joaktree_registry_items (regkey, value) VALUES ("INITIAL_CHAR", "0" )';
			$update_queries[] = 'INSERT INTO #__joaktree_registry_items (regkey, value) VALUES ("VERSION", "'.$version.'" )';
			// end: joaktree_registry_items
			   
			// Table joaktree_relations
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_relations '
			   .'( app_id           tinyint(4)            NOT NULL '
			   .', person_id_1      varchar(20)           NOT NULL '
			   .', person_id_2      varchar(20)           NOT NULL '
			   .', type             enum( '   . $db->Quote( 'partner' )
			                          	.', ' . $db->Quote( 'father' )
						  			 	.', ' . $db->Quote( 'mother' )
						  				.')                 NOT NULL '
			   .', subtype          enum( '   . $db->Quote( 'spouse' )
			                          	.', ' . $db->Quote( 'partner' )
						  				.', ' . $db->Quote( 'natural' )
						  				.', ' . $db->Quote( 'adopted' )
						  				.', ' . $db->Quote( 'step' )
						  				.', ' . $db->Quote( 'foster' )
						  				.', ' . $db->Quote( 'legal' )
						  				.')                 NULL '
			   .', family_id        varchar(20)           NOT NULL '
			   .', indNote          tinyint(1)   unsigned NOT NULL default 0 '
			   .', indCitation      tinyint(1)   unsigned NOT NULL default 0 '
			   .', orderNumber_1    smallint(2)  default      NULL '
			   .', orderNumber_2    smallint(2)  default      NULL '
			   .', PRIMARY KEY  (app_id,person_id_1,person_id_2) '
			   .', KEY person_id (app_id,person_id_1) '
			   .', KEY to_person_id (app_id,person_id_2) '
			   .') '
			   .'COMMENT="'.$version.'" ';
			// end: joaktree_relations
		
			// Table joaktree_relation_events
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_relation_events '
			   .'( app_id           tinyint(4)            NOT NULL '
			   .', person_id_1      varchar(20)           NOT NULL '
			   .', person_id_2      varchar(20)           NOT NULL '
			   .', orderNumber      smallint(2)           NOT NULL '
			   .', code             varchar(4)            NOT NULL '
			   .', indNote          tinyint(1)   unsigned NOT NULL default 0 '
			   .', indCitation      tinyint(1)   unsigned NOT NULL default 0 '
			   .', type             varchar(30)  default      NULL '
			   .', eventDate        varchar(40)  default      NULL '
			   .', loc_id           int(11)      default      NULL '
			   .', location         varchar(75)  default      NULL '
			   .', value            varchar(100) default      NULL '
			   .', PRIMARY KEY  (app_id,person_id_1,person_id_2,orderNumber) '
			   .', KEY LOC2     (location) '
			   .', KEY LOI2     (loc_id) '
			   .') '
			   .'COMMENT="'.$version.'" ';
			// end: joaktree_relation_events
			   
			// Table joaktree_relation_notes
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_relation_notes '
			   .'( app_id           tinyint(4)            NOT NULL '
			   .', person_id_1      varchar(20)           NOT NULL '
			   .', person_id_2      varchar(20)           NOT NULL '
			   .', orderNumber      smallint(2)           NOT NULL '
			   .', indCitation      tinyint(1)   unsigned NOT NULL default 0 '
			   .', eventOrderNumber smallint(2)  default      NULL '
			   .', note_id          varchar(20)               NULL '
			   .', value            text '
			   .', PRIMARY KEY  (app_id,person_id_1,person_id_2,orderNumber) '
			   .') '
			   .'COMMENT="'.$version.'" ';   
			// end: joaktree_relation_notes
			   
			// Table joaktree_repositories
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_repositories '
			   .'( app_id           tinyint(4)            NOT NULL '
			   .', id               varchar(20)           NOT NULL '
			   .', name             varchar(50)           NOT NULL '
			   .', website          varchar(100) default  NULL '
			   .', PRIMARY KEY  (app_id,id) '
			   .') '
			   .'COMMENT="'.$version.'" ';
			// end: joaktree_repositories
			
			// Table joaktree_sources
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_sources '
			   .'( app_id           tinyint(4)            NOT NULL '
			   .', id               varchar(20)           NOT NULL '
			   .', repo_id          varchar(20)  default  NULL '
			   .', title            varchar(250) default  NULL '
			   .', author           varchar(250) default  NULL '
			   .', publication      varchar(250) default  NULL '
			   .', information      text         default  NULL '
			   .', PRIMARY KEY  (app_id,id) '
			   .') '
			   .'COMMENT="'.$version.'" ';
			// end: joaktree_sources
		
			// Table joaktree_themes
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_themes '
			   .'( id               smallint(6)  unsigned NOT NULL auto_increment '
			   .', name             varchar(25)  default  NULL '
			   .', home             tinyint(1)   unsigned NOT NULL default 0 '
			   .', params           varchar(2048)         NOT NULL '
			   .', PRIMARY KEY  (id) '
			   .', UNIQUE KEY UKNAME (name) '
			   .') '
			   .'COMMENT="'.$version.'" ';
			   
			$params = '{"search_width":"120"' 
					 .',"show_update":"Y"'
					 .',"columns":"3"'
					 .',"groupCount":"3"'
					 .',"abbreviation":""'
					 .',"lineage":"3"'
					 .',"Directory":"images\\/joaktree"'
					 .',"pxHeight":"135"'
					 .',"pxWidth":"325"'
					 .',"transDelay":"50"'
					 .',"nextDelay":"5000"'
					 .',"TitleSlideshow":"A Genealogy Slideshow"'
					 .',"Sequence":"3"'
					 .',"pxMapWidth":"700"'
					 .',"statMarkerColor":""'
					 .',"descendantchart":"1"'
					 .',"descendantlevel":"20"'
					 .',"ancestorchart":"1"'
					 .',"ancestorlevel":"1"'
					 .',"ancestordates":"1"'
					 .',"indTabBehavior":"1"'
					 .',"notetitlelength":"30"'
					 .'}'; 
			$update_queries[] = 'INSERT INTO #__joaktree_themes (name, home, params) VALUES (\'Joaktree\', 1, \''.$params.'\')';
			$update_queries[] = 'INSERT INTO #__joaktree_themes (name, home, params) VALUES (\'Blue\', 0, \''.$params.'\')';
			$update_queries[] = 'INSERT INTO #__joaktree_themes (name, home, params) VALUES (\'Green\', 0, \''.$params.'\')';
			$update_queries[] = 'INSERT INTO #__joaktree_themes (name, home, params) VALUES (\'Red\', 0, \''.$params.'\')';
			// end: joaktree_themes
		
			// Table joaktree_trees
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_trees '
			   .'( id               int(10)      unsigned NOT NULL auto_increment '
			   .', app_id           tinyint(4)            NOT NULL '
			   .', asset_id         int(10)      unsigned NOT NULL '
			   .', holds            enum( \'all\' '
			   .'                       , \'descendants\' '
			   .'                       )                 NOT NULL default \'all\' '
			   .', published        tinyint(1)            NOT NULL default 1 '
			   .', access           int(11)      unsigned NOT NULL default 1 '
			   .', name             varchar(250)          NOT NULL '
			   .', theme_id         int(11)               NOT NULL '
			   .', indGendex        tinyint(1)   unsigned NOT NULL default 0 '
			   .', indPersonCount   tinyint(1)   unsigned NOT NULL default 0 '
			   .', indMarriageCount tinyint(1)   unsigned NOT NULL default 0 '
			   .', robots           tinyint(2)            NOT NULL default 0 ' 
			   .', root_person_id   varchar(20)               NULL '
			   .', catid            int(11)                   NULL '
			   .', PRIMARY KEY  (id) '
			   .') '
			   .'COMMENT="'.$version.'" ';
			// end: joaktree_trees
			   
			// Table joaktree_tree_persons
			$update_queries[] = 
			    'CREATE TABLE IF NOT EXISTS '
			   .'#__joaktree_tree_persons '
			   .'( id               varchar(31)           NOT NULL '
			   .', app_id           tinyint(4)            NOT NULL '
			   .', tree_id          int(11)               NOT NULL '
			   .', person_id        varchar(20)           NOT NULL '
			   .', type             enum( ' . $db->Quote( 'R' )
			                          	.', ' . $db->Quote( 'P' )
						  				.', ' . $db->Quote( 'C' )
						  				.')                 NOT NULL '
			   .', lineage          varchar(250) default  NULL '
			   .', PRIMARY KEY  (id) '
			   .', KEY person_id (app_id,person_id) '
			   .') '
			   .'COMMENT="'.$version.'" ';
			// end: joaktree_tree_persons
        	
			// Perform all queries
			foreach( $update_queries as $query ) {
			    $db->setQuery( $query );
			    $db->query();
			}

			$application->enqueueMessage( 'Database installation script is finished for version '.$version, 'notice' ) ;			
        }
 
        /**
         * Called on update
         *
         * @param   JAdapterInstance  $adapter  The object responsible for running this script
         *
         * @return  boolean  True on success
         */
        public function update($installer) {
        	// upgrade
			$new_version = (string) JInstaller::getInstance()->getManifest()->version;
        	
			// Initialize the database
			$db 			= JFactory::getDBO();
        	$update_queries = array();
			$application 	= JFactory::getApplication();
			
			// current version in database
			$query 			= $db->getQuery(true);
			$query->select(' value ');
			$query->from(  ' #__joaktree_registry_items ');
			$query->where( ' regkey = '.$db->quote('VERSION').' ');			
			$db->setQuery($query);			
			$old_version = $db->loadResult();
			
			if (empty($old_version)) {
				$old_version = 'unknown';
				$update_queries[] = 'INSERT INTO #__joaktree_registry_items (regkey, value) VALUES ("VERSION", "'.$new_version.'" )';
			} else {
				$update_queries[] = 'UPDATE #__joaktree_registry_items SET value = "'.$new_version.'" WHERE regkey = "VERSION" ';
			}
			
			
			switch ($old_version) {
				case '1.5.0.beta.1' :
							switch ($new_version) {
								case '1.5.0.beta.2': // continue
								case '1.5.0.beta.3': // continue
								case '1.5.0': 		 // continue
									 	// Table joaktree_trees
										$update_queries[] = 
										    'ALTER IGNORE TABLE '
										   .'#__joaktree_trees '
										   .'ADD catid           int(11)                   NULL '
										   .'AFTER  root_person_id ';	   
										// end: joaktree_trees
										break;
								default: break;								
							}
							break;
							
				default:	// continue
							break;
			}
				
			// Perform all queries
			foreach( $update_queries as $query ) {
			    $db->setQuery( $query );
			    $result = $db->execute();
			} 
			
			$application->enqueueMessage( 'Database update script is finished for version '.$new_version, 'notice' ) ;
        }
 
        /**
         * Called on uninstallation
         *
         * @param   JAdapterInstance  $adapter  The object responsible for running this script
         */
        public function uninstall($installer) {
			// Un-installation
			
			// Initialize the database
			$db 			= JFactory::getDBO();
        	$update_queries = array();
			$application 	= JFactory::getApplication();

			// Do not drop tables, because they contain user settings
			// $update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_admin_persons ';
			// $update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_applications ';
			// $update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_display_settings ';
			// $update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_themes ';
			// $update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_trees ';
			
			// Drop the following tables
			$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_citations ';
			$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_documents ';
			$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_gedcom_objectlines ';
			$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_gedcom_objects ';
			$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_locations ';
			$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_logremovals ';
			$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_logs ';
			$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_maps ';
			$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_notes ';
			$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_persons ';
			$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_person_documents ';
			$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_person_events ';
			$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_person_names ';
			$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_person_notes ';
			$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_registry_items ';
			$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_relations ';
			$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_relation_events ';
			$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_relation_notes ';
			$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_repositories ';
			$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_sources ';
			$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_tree_persons ';

			// Perform all queries - we don't care if it fails
			foreach( $update_queries as $query ) {
			    $db->setQuery( $query );
			    $db->query();
			}
			
			// Set a simple message
			$application->enqueueMessage( JText::_( 'NOTE: Five database tables were NOT removed to allow for upgrades' ), 'notice' ) ;
			
        }

}

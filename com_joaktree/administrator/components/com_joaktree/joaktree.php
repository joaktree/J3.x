<?php
/**
 * Joomla! component Joaktree
 * file		joaktree.php
 *
 * @version	1.5.0
 * @author	Niels van Dantzig
 * @package	Joomla
 * @subpackage	Joaktree
 * @license	GNU/GPL
 *
 * Component for genealogy in Joomla!
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
 * Define constants for all pages
 */
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
define( 'COM_JOAKTREE_DIR', 'images'.DS.'joaktree'.DS );
define( 'COM_JOAKTREE_BASE', JPATH_ROOT.DS.COM_JOAKTREE_DIR );
define( 'COM_JOAKTREE_BASEURL', JURI::root().str_replace( DS, '/', COM_JOAKTREE_DIR ));

// Require the base controller
require_once JPATH_COMPONENT.DS.'controller.php';
require_once JPATH_COMPONENT.DS.'helpers'.DS.'helper.php';

//load the GedCom language file
$lang 	= JFactory::getLanguage();
$lang->load('com_joaktree.gedcom');

// create an input object
$input = JFactory::getApplication()->input;

// Require specific controller if requested
if($controller = $input->get('controller', null, 'word')) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (JFile::exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

// Create the controller
$classname    = 'JoaktreeController'.$controller;
$controller   = new $classname( );

// Perform the Request task
$controller->execute( $input->get('task'));
$controller->redirect();


?>
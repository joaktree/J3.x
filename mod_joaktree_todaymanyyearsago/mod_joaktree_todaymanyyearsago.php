<?php
/**
 * Joomla! module Joaktree Today Many Years Ago
 *
 * @version	1.5.0
 * @author	Niels van Dantzig
 * @package	Joomla
 * @subpackage	Joaktree
 * @license	GNU/GPL
 *
 * Module showing events in the past
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
require_once (JPATH_BASE.DS.'components'.DS.'com_joaktree'.DS.'models'.DS.'todaymanyyearsago.php');
$modHelper = new JoaktreeModelTodaymanyyearsago();

// include stylesheets and javascript
//JHTML::stylesheet( 'joaktree.css', 'components/com_joaktree/assets/css/' );
$theme = $modHelper->getThemeName();
JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
JHTML::stylesheet( JoaktreeHelper::joaktreecss($theme) );

// scripts
$document = &JFactory::getDocument();
$document->addScript(JURI::base(true).'/components/com_joaktree/assets/js/toggle.js');
$document->addScript(JURI::base(true).'/components/com_joaktree/assets/js/jtajax.js');	

// Include language file from Joaktree component;
$jtlang = JFactory::getLanguage();
$jtlang->load('com_joaktree');
$jtlang->load('com_joaktree.gedcom', JPATH_ADMINISTRATOR);	


$day    	= $modHelper->getDay();
$days   	= $modHelper->getDays();
$month  	= $modHelper->getMonth();
$months 	= $modHelper->getMonths();
$jtlist 	= $modHelper->getList($module->id);
$title  	= $modHelper->getTitle();
$sorting 	= $modHelper->getSorting();
$buttonText	= $modHelper->getButtonText();


require(JModuleHelper::getLayoutPath('mod_joaktree_todaymanyyearsago'));

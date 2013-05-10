<?php
/**
 * Joomla! module Joaktree last persons viewed 
 * file		JoaktreeHelper - helper.php
 *
 * @version	1.5.0
 * @author	Niels van Dantzig
 * @package	Joomla
 * @subpackage	Joaktree
 * @license	GNU/GPL
 *
 * Module showing list of persons last viewed by user
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

$modHelper 		= new modJoaktreeLastPersonsViewedHelper();
$numberInList 	= $params->get('numberInList', 3);

$personlist = $modHelper->getList($numberInList);

require(JModuleHelper::getLayoutPath('mod_joaktree_lastpersonsviewed'));

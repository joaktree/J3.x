<?php 

/** Module showing last update of Joaktree Family
* mod_joaktree_show_update
*/
// no direct access
defined('_JEXEC') or die('Restricted access'); 
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

require_once (dirname(__FILE__).DS.'helper.php');

$modHelper = new modJoaktreeShowUpdateHelper();

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$result = $modHelper->getUpdate();

require JModuleHelper::getLayoutPath('mod_joaktree_show_update', $params->get('layout', 'default'));

?>

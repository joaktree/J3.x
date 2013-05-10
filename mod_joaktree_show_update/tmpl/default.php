<?php 

/** Module showing last update of Joaktree Family
* mod_joaktree_show_update
*/
// no direct access
defined('_JEXEC') or die('Restricted access'); 

?>

<div class="jt-mod-update-container">
	<div class="jt-mod-update-title">
		<?php echo JText::_('JT_MODUPDATETITLE'); ?>
	</div>
	<div class="jt-mod-update-date">
		<?php echo $result; ?>
	</div>
</div>

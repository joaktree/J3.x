<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<?php if(count($arlist) > 0) { ?><h4><?php echo JText::_('JTRELITEMS_ARTICLES'); ?></h4><?php } ?>
<ul>
<?php foreach ($arlist as $item) :	?>
<li>
	<a href="<?php echo $item->route; ?>" <?php echo $item->robot; ?>>
		<?php if ($showDate) echo $item->created . " - "; ?>
		<?php echo $item->title; ?></a>
</li>
<?php endforeach; ?>
</ul>

<?php if(count($jtlist) > 0) { ?><h4><?php echo JText::_('JTRELITEMS_PERSONS'); ?></h4><?php } ?>
<ul>
<?php foreach ($jtlist as $item) :	?>
<li>
	<a href="<?php echo $item->route; ?>" <?php echo $item->robot; ?>>
		<?php if ($showDate) echo $item->created . " - "; ?>
		<?php echo $item->title; ?></a>
</li>
<?php endforeach; ?>
</ul>
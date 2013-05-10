<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>


<?php if (!is_array($personlist)) { ?>
	<p><?php echo JText::_('MOD_JTLPV_NOCOOKIES'); ?></p> 

<?php } else if (count($personlist)) { ?>
	<ol>
	<?php foreach ($personlist as $person) {	?>
		<li><a href="<?php echo $person->route; ?>" <?php echo $person->robot; ?>>
			<?php echo $person->fullName; ?>
		</a></li>
	<?php } ?>
	</ol>
<?php } else { ?>
	<p><?php echo JText::_('MOD_JTLPV_NOPERSONS'); ?></p> 
<?php } ?>



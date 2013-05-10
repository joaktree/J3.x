<?php 
// no direct access
defined('_JEXEC') or die('Restricted access');  

?>
<!-- ?php echo $this->map->getStyleDeclaration(); ? -->

<?php if ($this->lists['userAccess']) { ?> 	
	<!-- A reference to a map is found -->
	<!-- toolkit -->
	<?php if ($this->toolkit) { ?>
		<script src="<?php echo $this->toolkit; ?>" type="text/javascript"></script>
	<?php } ?>
	
	<script type="text/javascript">
		<?php echo $this->script; ?>
	</script>

	<div id="map_canvas" style="width:100%; height:100%"></div>	 
<?php } else { ?>
	<!-- No reference is found - so user has NO access to information -->
	<div class="jt-content-th" >
		<div class="jt-noaccess"><?php echo JText::_( 'JT_NOACCESS' ); ?></div>
	</div>
<?php } ?>

<?php echo JHtml::_('form.token'); ?>	
	
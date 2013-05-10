<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php 
// Button for editing (only active with AJAX)
if (($this->lists['technology'] != 'b') && ($this->lists['technology'] != 'j')) {
	if (is_object($this->canDo)) {
?>
		<div class="jt-edit-2" style="text-align: right;">
<?php 
		$docsFromGedcom	= (int) $this->params->get('indDocuments', 0); 
		if (  $this->canDo->get('media.create')
		   || ($this->canDo->get('core.edit') && ($docsFromGedcom))
		   ) {
?>	   	
			<a href="#" onclick="jtsubmitbutton('edit', 'medialist');" >
			<?php echo JText::_('JT_EDITPICTURES'); ?>
			</a>
<?php 	} else { ?>
			<span class="jt-edit-nolink" title="<?php echo JText::_('JT_NOPERMISSION_DESC'); ?>" >
			<?php echo JText::_('JT_EDITPICTURES'); ?>
			</span>
			
<?php 	} ?>
		&nbsp;|			
		</div>
<?php 	   		
	}	
}
?>

<?php echo $this->Html[ 'pictures' ]; ?>




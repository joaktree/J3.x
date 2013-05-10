<?php
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// import component libraries
JLoader::import('helper.formhelper', JPATH_COMPONENT);
?>

<script type="text/javascript">
	function jtsubmitbutton(task)
	{
		if (task == 'cancel' || document.formvalidator.isValid(document.id('stateForm'))) {
			Joomla.submitform(task, document.getElementById('stateForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JT_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<div id="jt-form"> 
<form action="<?php echo JRoute::_('index.php?option=com_joaktree'); ?>" method="post" name="stateForm" id="stateForm" class="form-validate">
<?php echo $this->form->getInput('type', null, 'state'); ?>

<?php if (($this->lists['userAccess'])
			&& (is_object($this->item)) 
			&& (is_object($this->canDo)) && 
			(  $this->canDo->get('core.edit.state')
			)
		  ) { 
?> 
<!-- user has access to information -->
<div class="fltlft">
	<div class="jt-content-th" >
		<div class="jt-h3-th">
			<?php echo JText::_( 'JT_EDIT_RECORD' ).':&nbsp;'.$this->item->firstName.'&nbsp;'.$this->item->familyName; ?>
			<span style="float: right;">
				<?php echo (($this->lists['indLiving']) ? JText::_( 'JT_LIVING' ) : JText::_( 'JT_NOTLIVING' )); ?>
			</span>
		</div>
	</div>

	<fieldset class="joaktreeform">
		<legend><?php echo JText::_('JT_EDITSTATE'); ?></legend>
		
		<!-- Save + cancel buttons -->
		<?php echo FormHelper::getButtons(1) ;?>							
		<!-- End save + cancel buttons -->		

		<!-- Person state -->
		<?php echo $this->form->getInput('lineEnd', null, $this->lists['lineEnd']); ?>
		<?php echo $this->form->getInput('id', 'person', $this->item->id); ?>
		<?php echo $this->form->getInput('app_id', 'person', $this->lists['appId']); ?>
		<?php echo $this->form->getInput('living', 'person', $this->lists['indLiving']); ?>
		<?php echo $this->form->getInput('status', 'person', 'loaded'); ?>

		<ul class="joaktreeformlist">
			<li>
				<?php echo $this->form->getLabel('published', 'person'); ?>
				<?php echo $this->form->getInput('published', 'person', $this->item->published); ?>			
			</li>
			<li>
				<?php echo $this->form->getLabel('livingnew', 'person'); ?>
				<?php echo $this->form->getInput('livingnew', 'person', $this->lists['indLiving']); ?>			
			</li>
			<li>
				<?php echo $this->form->getLabel('page', 'person'); ?>
				<?php echo $this->form->getInput('page', 'person', $this->item->page); ?>			
			</li>
			<li>
				<?php echo $this->form->getLabel('map', 'person'); ?>
				<?php echo $this->form->getInput('map', 'person', $this->item->map); ?>			
			</li>
			<li>&nbsp;</li>

		</ul>
		<!-- End: Person state -->
			
		<div class="jt-clearfix"></div>
		<!-- Save + cancel buttons -->
		<?php echo FormHelper::getButtons(2) ;?>							
		<!-- End save + cancel buttons -->		
	</fieldset>

	<input type="hidden" name="treeId" value="<?php echo $this->lists['treeId']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="personform" />
	<?php echo JHtml::_('form.token'); ?>

</div>

<div class="clr"></div>

<?php } else { ?>
<!-- user has NO access to information -->
	<div class="jt-content-th" >
		<div class="jt-noaccess"><?php echo JText::_( 'JT_NOACCESS' ); ?></div>
	</div>
<?php } ?>

<div class="jt-stamp">
	<?php echo $this->lists[ 'CR' ]; ?>
</div>

</form>
</div>
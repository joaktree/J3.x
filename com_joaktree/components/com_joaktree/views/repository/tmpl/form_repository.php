<?php
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>

<script type="text/javascript">
	function jtsubmitbutton(task)
	{
		if (task == 'cancel' || document.formvalidator.isValid(document.id('repoForm'))) {
			Joomla.submitform(task, document.getElementById('repoForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JT_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_($this->lists['link']); ?>" method="post" name="repoForm" id="repoForm" class="form-validate">

<?php if (  ($this->lists['userAccess']) 
		 && (is_object($this->canDo)) 
		 && (  $this->canDo->get('core.create')
			|| $this->canDo->get('core.edit')
			)
		 ) { 
?> 
<!-- user has access to information -->

<div class="fltlft">
	<div class="jt-content-th" >
		<div class="jt-h3-th">
			<?php if (empty($this->item->id)) {
					echo JText::_( 'JT_NEW_RECORD' );
				  } else {
				  	echo JText::_( 'JT_EDIT_RECORD' ).':&nbsp;'.$this->item->name;
				  }
			 ?>
		</div>
	</div>

	<fieldset class="joaktreeform">
		<legend><?php echo JText::_('JT_REPOSITORY'); ?></legend>
		
		<!-- Save + cancel buttons -->
		<div class="jt-buttonbar" style="margin-left: 10px;">
			<a 	href="#" 
				id="save"
				class="jt-button-closed jt-buttonlabel" 
				title="<?php echo JText::_('JSAVE'); ?>" 
				onclick="jtsubmitbutton('save');"
			>
				<?php echo JText::_('JSAVE'); ?>
			</a>
			&nbsp;
			<a 	href="#" 
				id="cancel"
				class="jt-button-closed jt-buttonlabel" 
				title="<?php echo JText::_('JCANCEL'); ?>" 
				onclick="jtsubmitbutton('cancel');"
			>
				<?php echo JText::_('JCANCEL'); ?>
			</a>
									
		</div>
		<div class="clearfix"></div>
		<!-- End save + cancel buttons -->
		
		<div>
			<ul class="joaktreeformlist">
				<li>
					<?php echo $this->form->getLabel('name'); ?>
					<?php echo $this->form->getInput('name'); ?>			
				</li>
				<li>
					<?php echo $this->form->getLabel('website'); ?>
					<?php echo $this->form->getInput('website'); ?>			
				</li>
			</ul>
			<?php echo $this->form->getInput('id'); ?>
			<?php echo $this->form->getInput('app_id'); ?>
		</div>
	</fieldset>

	<input type="hidden" name="appId" value="<?php echo $this->item->app_id; ?>" />
	<input type="hidden" name="action" value="<?php echo $this->lists['action']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="repository" />
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

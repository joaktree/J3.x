<?php
defined('_JEXEC') or die;

// are these needed
JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
// The chosen selector doesn't work properly with the map-icons selection box.
// It is disabled here.
//JHtml::_('formbehavior.chosen', 'select');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'theme.cancel' || document.formvalidator.isValid(document.id('theme-form'))) {
			Joomla.submitform(task, document.getElementById('theme-form'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form 
	action="<?php echo JRoute::_('index.php?option=com_joaktree&view=jt_themes'); ?>" 
	method="post" 
	name="adminForm" 
	id="theme-form" 
	class="form-validate form-horizontal"
>
<div class="span10 form-horizontal">
	<fieldset>
		<ul class="nav nav-tabs">
			<li class="<?php echo (empty($this->item->id) ? 'active' : ''); ?>">
				<a href="#details" data-toggle="tab">
				<?php echo empty($this->item->id) ? JText::_('JTTHEME_TITLE_NEWNAME') : JText::sprintf('JTTHEME_TITLE_EDITNAME', ucfirst($this->item->name)); ?>
				</a>
			</li>
			<li class="<?php echo (empty($this->item->id) ? '' : 'active'); ?>">
				<a href="#params" data-toggle="tab">
				<?php echo JText::_('JTTHEME_TITLE_PARAMS'); ?>
				</a>
			</li>
		</ul>
		
		<!-- content starts here -->
		<div class="tab-content">
			<div class="tab-pane <?php echo (empty($this->item->id) ? 'active' : ''); ?>" id="details">
				<div class="control-group">
					<div class="control-label">
						<?php echo (empty($this->item->id) 
									? $this->form->getLabel('newname') 
									: $this->form->getLabel('name')); 
						?>
					</div>
					<div class="controls">
						<?php echo (empty($this->item->id) 
									? $this->form->getInput('newname') 
									: $this->form->getInput('name')); 
						?>
					</div>
				</div>
				
				<?php if (empty($this->item->id)) { ?>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('theme'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('theme'); ?>
						</div>
					</div>
				<?php } ?>
				
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('id'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('id'); ?>
					</div>
				</div>
			</div>
			
			<div class="tab-pane <?php echo (empty($this->item->id) ? '' : 'active'); ?>" id="params">
				<?php foreach($this->form->getFieldset('settings') as $field): ?>
					<div class="control-group">
						<?php if (!$field->hidden): ?>
							<div class="control-label">
								<?php echo $field->label; ?>
							</div>
						<?php endif; ?>
						<div class="controls">
							<?php echo $field->input; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>	
		</div>
	
	</fieldset>
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="controller" value="jt_themes" />
	<input type="hidden" name="caller" value="form" />
	<?php echo JHtml::_('form.token'); ?>

</div>
</form>
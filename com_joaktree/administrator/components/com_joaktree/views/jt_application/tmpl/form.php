<?php
defined('_JEXEC') or die;

// are these needed
JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'application.cancel' || document.formvalidator.isValid(document.id('application-form'))) {
			Joomla.submitform(task, document.getElementById('application-form'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form 
	action="<?php echo JRoute::_('index.php?option=com_joaktree'); ?>" 
	method="post" 
	name="adminForm" 
	id="application-form" 
	class="form-validate form-horizontal"
>
<div class="span10 form-horizontal">
	<fieldset>
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#details" data-toggle="tab">
				<?php echo empty($this->item->id) ? JText::_('JTAPPS_TITLE_NEWNAME') : JText::sprintf('JTAPPS_TITLE_EDITNAME', ucfirst($this->item->title)); ?>
				</a>
			</li>
			<li>
				<a href="#params" data-toggle="tab">
				<?php echo JText::_('JTAPPS_TITLE_PARAMS'); ?>
				</a>
			</li>
			<?php if ($this->canDo->get('core.admin')): ?>
				<li>
					<a href="#permissions" data-toggle="tab">
					<?php echo JText::_('JTAPPS_PERMISSIONS');?>
					</a>
				</li>
			<?php endif; ?>
		</ul>
		
		<!-- content starts here -->
		<div class="tab-content">
			<div class="tab-pane active" id="details">
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('title'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('title'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('description'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('description'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('programName'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('programName'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('id'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('id'); ?>
					</div>
				</div>
			</div>

			<div class="tab-pane" id="params">
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
			
			<?php if ($this->canDo->get('core.admin')): ?>
				<div class="tab-pane" id="permissions">
					<?php echo JHtml::_('sliders.start','permissions-sliders-'.(!empty($this->item->id) ? $this->item->id : null), array('useCookie'=>$this->indCookie)); ?>
			
					<?php echo JHtml::_('sliders.panel',JText::_('JTAPPS_PERMISSIONS'), 'access-rules'); ?>
						<fieldset class="panelform">
							<?php echo $this->form->getLabel('rules'); ?>
							<?php echo $this->form->getInput('rules'); ?>
						</fieldset>
			
					<?php echo JHtml::_('sliders.end'); ?>
				</div>
			<?php endif; ?>		
					
		</div>

	</fieldset>
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="controller" value="jt_applications" />
	<?php echo JHtml::_('form.token'); ?>
	
</div>
</form>

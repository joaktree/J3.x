<?php
defined('_JEXEC') or die;

// are these needed
JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
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
	action="<?php echo JRoute::_('index.php?option=com_joaktree'); ?>" 
	method="post" 
	name="adminForm" 
	id="theme-form" 
	class="form-validate form-horizontal"
>
<div class="span10 form-horizontal">
	<fieldset>
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#details" data-toggle="tab">
				<?php echo JText::sprintf('JTTHEME_TITLE_EDITCSS', ucfirst($this->item->name)); ?>
				</a>
			</li>
		</ul>
		
		<!-- content starts here -->
		<div class="tab-content">
			<div class="tab-pane active" id="details">
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('source'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('source'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('sourcepath'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('sourcepath'); ?>
					</div>
				</div>
			</div>
		</div>

	</fieldset>
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="controller" value="jt_themes" />
	<input type="hidden" name="caller" value="editcss" />
	<?php echo JHtml::_('form.token'); ?>

</div>
</form>

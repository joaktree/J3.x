<?php
defined('_JEXEC') or die;

// are these needed
JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

// 
JHTML::_('behavior.modal', 'a.modal'); 
$name 		= 'personId'; 
$linkPerson = 'index.php?option=com_joaktree&amp;view=jt_persons&amp;layout=element&amp;task=element&amp;tmpl=component&amp;object='.$name;
$clrPerson  = 'window.parent.jClearPerson();'; 	

?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'tree.cancel' || document.formvalidator.isValid(document.id('tree-form'))) {
			Joomla.submitform(task, document.getElementById('tree-form'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form 
	action="<?php echo JRoute::_('index.php?option=com_joaktree'); ?>" 
	method="post" 
	name="adminForm" 
	id="tree-form" 
	class="form-validate form-horizontal"
>
<div class="span10 form-horizontal">
	<fieldset>
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#details" data-toggle="tab">
				<?php echo empty($this->item->id) ? JText::_('JTTREE_TITLE_NEWNAME') : JText::sprintf('JTTREE_TITLE_EDITNAME', ucfirst($this->item->name)); ?>
				</a>
			</li>
			<?php if ($this->canDo->get('core.admin')): ?>
				<li>
					<a href="#permissions" data-toggle="tab">
					<?php echo JText::_('JTTREE_PERMISSIONS');?>
					</a>
				</li>
			<?php endif; ?>
		</ul>

		<!-- content starts here -->
		<div class="tab-content">
			<div class="tab-pane active" id="details">
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('name'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('name'); ?>
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
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('app_id'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('app_id'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('holds'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('holds'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('personName'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('personName', null, (is_object($this->item)) ? $this->item->rootPersonName : null); ?>
						<div class="btn btn-small">
							<a class="modal" title="<?php echo JText::_('JTFIELD_PERSON_BUTTONDESC_PERSON'); ?>"  href="<?php echo $linkPerson; ?>" rel="{handler: 'iframe', size: {x: 650, y: 375}}" >
								<?php echo JText::_('JTFIELD_PERSON_BUTTON_PERSON'); ?>
							</a>
						</div>
						<div class="btn btn-small">
							<a title="<?php echo JText::_('JTTREE_TOOLTIP_CLEAR'); ?>"  onclick="<?php echo $clrPerson; ?>" >
								<?php echo JText::_('JTTREE_LABEL_CLEAR'); ?>
							</a>
						</div>	
						
					</div>
					<div class="control-label">
						<?php echo $this->form->getLabel('root_person_id'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('root_person_id'); ?>
					</div>

				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('access'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('access'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('theme_id'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('theme_id'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('catid'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('catid'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('indGendex'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('indGendex'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('indPersonCount'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('indPersonCount'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('indMarriageCount'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('indMarriageCount'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('robots'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('robots'); ?>
					</div>
				</div>


			
			</div>
			
			<?php if ($this->canDo->get('core.admin')): ?>
				<div class="tab-pane" id="permissions">
					<?php echo JHtml::_('sliders.start','permissions-sliders-'.(!empty($this->item->id) ? $this->item->id : null), array('useCookie'=>$this->indCookie)); ?>
			
					<?php echo JHtml::_('sliders.panel',JText::_('JTTREE_PERMISSIONS'), 'access-rules'); ?>
						<fieldset class="panelform">
							<!-- ?php echo $this->form->getLabel('rules'); ?  -->
							<?php echo $this->form->getInput('rules'); ?>
						</fieldset>
			
					<?php echo JHtml::_('sliders.end'); ?>
				</div>
			<?php endif; ?>		
		</div>

	</fieldset>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="cid[]" value="<?php echo (!empty($this->item->id) ? $this->item->id : null); ?>" />
	<input type="hidden" name="controller" value="jt_trees" />
	<?php echo JHtml::_('form.token'); ?>
	
</div>
</form>

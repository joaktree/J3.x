<?php
// no direct access
defined('_JEXEC') or die;

// are these needed
JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHTML::_('behavior.modal', 'a.modal'); 
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

$linkPerson = 'index.php?option=com_joaktree&amp;view=jt_persons&amp;layout=element&amp;task=element&amp;tmpl=component&amp;object=personId';
$clrPerson  = 'window.parent.jClearPerson();'; 	

?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'map.cancel' || document.formvalidator.isValid(document.id('location-form'))) {
			Joomla.submitform(task, document.getElementById('location-form'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form 
	action="<?php echo JRoute::_('index.php?option=com_joaktree'); ?>" 
	method="post" 
	name="adminForm" 
	id="location-form" 
	class="form-validate form-horizontal"
>

<div class="span10 form-horizontal">
	<fieldset>
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#details" data-toggle="tab">
				<?php echo  ((!is_object($this->item)) || ((is_object($this->item)) && (!$this->item->id)))
						? JText::_('JTMAP_TITLE_NEWNAME')
						: JText::sprintf('JTMAP_TITLE_EDITNAME', ucfirst($this->item->name));
				?>
				</a>
			</li>
			<li>
				<a href="#map-params" data-toggle="tab">
				<?php echo JText::_('JTMAP_TITLE_PARAMS');?>
				</a>
			</li>
			<li>
				<a href="#map-adv-params" data-toggle="tab">
				<?php echo JText::_('JTMAP_TITLE_ADVPARAMS');?>
				</a>
			</li>
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
						<?php echo $this->form->getLabel('selection'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('selection'); ?>
					</div>
				</div>
				
				<?php switch ($this->item->selection) {
					    case "person"	  : $classPerson     = 'jt-show';
					    					$classTree 	     = 'jt-hide';
					    					$classLocation   = 'jt-hide';
					    					break;
					    case "location"	  : $classPerson     = 'jt-hide';
					    					$classTree 	     = 'jt-show';
					    					$classLocation   = 'jt-show';
					    					break;
					    case "tree"		  : 
						default			  : $classPerson     = 'jt-hide';
											$classTree 	     = 'jt-show';
					    					$classLocation   = 'jt-hide';
											break; 
					  }
				?>

				<div id="tree" class="control-group <?php echo $classTree; ?>">
					<div class="control-label">
						<?php echo $this->form->getLabel('tree'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('tree', null, (is_object($this->item)) ? $this->item->tree_id : null); ?>
					</div>
				</div>
				<div id="descendants" class="control-group <?php echo $classTree; ?>">
					<div class="control-label">
						<?php echo $this->form->getLabel('descendants'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('descendants'); ?>
					</div>
				</div>
				<div id="familyName" class="control-group <?php echo $classTree; ?>">
					<div class="control-label">
						<?php echo $this->form->getLabel('familyName'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('familyName', null, (is_object($this->item)) ? $this->item->subject : null); ?>
					</div>
				</div>
				
				<!--  person  -->
				<div id="person1" class="control-group <?php echo $classPerson; ?>">
					<div class="control-label">
						<?php echo $this->form->getLabel('personName'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('personName', null, (is_object($this->item)) ? $this->item->personName : null); ?>
						<?php echo $this->form->getInput('root_person_id', null, (is_object($this->item)) ? $this->item->person_id : null); ?>
						<?php echo $this->form->getInput('app_id'); ?>

						<div class="btn btn-small">
							<a class="modal" title="<?php echo JText::_('JTFIELD_PERSON_BUTTONDESC_PERSON'); ?>"  href="<?php echo $linkPerson; ?>" rel="{handler: 'iframe', size: {x: 650, y: 375}}" >
								<?php echo JText::_('JTFIELD_PERSON_BUTTON_PERSON'); ?>
							</a>
						</div>
						<div class="btn btn-small">
							<a title="<?php echo JText::_(''); ?>"  onclick="<?php echo $clrPerson; ?>" >
								<?php echo JText::_('JGLOBAL_SELECTION_NONE'); ?>
							</a>
						</div>	
						
					</div>
				</div>
				<div id="relations" class="control-group <?php echo $classPerson; ?>">
					<div class="control-label">
						<?php echo $this->form->getLabel('person_relations'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('person_relations'); ?>
					</div>
				</div>
				<!--  End person  -->	

				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('period_start'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('period_start'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('period_end'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('period_end'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('events'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('events'); ?>
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

			<div class="tab-pane" id="map-params">
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('service'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('service'); ?>
					</div>
				</div>
				
				<?php foreach($this->form->getFieldset('settings') as $field): ?>
					<div <?php echo (($field->id == 'jform_params_distance') ? 'id="distance" class="control-group '.$classLocation.'"' : 'class="control-group"'); ?> >
						<div class="control-label">
							<?php if (!$field->hidden): ?>
								<?php echo $field->label; ?>
							<?php endif; ?>
						</div>
						<div class="controls">
							<?php echo $field->input; ?>
						</div>
					</div>			
				<?php endforeach; ?>
			</div>

			<div class="tab-pane" id="map-adv-params">			
				<?php foreach($this->form->getFieldset('adv-settings') as $field): ?>
					<div>
						<div class="control-label">
							<?php if (!$field->hidden): ?>
								<?php echo $field->label; ?>
							<?php endif; ?>
						</div>
						<div class="controls">
							<?php echo $field->input; ?>
						</div>
					</div>			
				<?php endforeach; ?>
			</div>			
		</div>
	</fieldset>
</div>

<input type="hidden" name="task" value="" />
<input type="hidden" name="cid[]" value="<?php echo (!empty($this->item->id) ? $this->item->id : null); ?>" />
<input type="hidden" name="controller" value="jt_maps" />
<?php echo JHtml::_('form.token'); ?>

</form>

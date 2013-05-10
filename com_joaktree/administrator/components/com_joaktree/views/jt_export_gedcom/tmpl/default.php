<?php defined('_JEXEC') or die('Restricted access'); 

	JHtml::_('behavior.tooltip');
	echo JHTML::_( 'form.token' ); 
 ?>

<script>
	window.addEvent('domready', function() {
		exportGedcom();
	});
</script>

<div id="j-main-container">
	<div id="cpanel" >
		<div id="head_process" style="display: inline;">
			<div style="float: left; height: 114px;">
				<h1><?php echo JText::_('JTPROCGEDCOM_PROC'); ?></h1>
				<?php echo JText::_('JTPROCGEDCOM_PROC_TXT'); ?>
			</div>
			<div style="float: right">
				<div class="jt-icon">
					<img src="components/com_joaktree/assets/images/ajax-loader.gif" />
					<br />
					<span><?php echo JText::_('JT_LOADING'); ?></span>
				</div>
			</div>
		</div>
		<div id="head_finished" style="display: none;">
			<div style="float: left">
				<h1><?php echo JText::_('JTPROCGEDCOM_HFINISHED'); ?></h1>
			</div>
			<div style="float: right">
				<div class="jt-icon">
					<a href="index.php?option=com_joaktree&view=jt_applications">
						<img src="components/com_joaktree/assets/images/icon-48-app.png" />
						<br />
						<span><?php echo JText::_('JT_SUBMENU_APPLICATIONS'); ?></span>
					</a>
				</div>
				<div class="jt-icon">
					<a href="index.php?option=com_joaktree&view=jt_trees">
						<img src="components/com_joaktree/assets/images/icon-48-familytree.png" />
						<br />
						<span><?php echo JText::_('JT_SUBMENU_FAMILYTREES'); ?></span>
					</a>
				</div>
				<div class="jt-icon">
					<a href="index.php?option=com_joaktree&view=jt_persons">
						<img src="components/com_joaktree/assets/images/icon-48-person.png" />
						<br />
						<span><?php echo JText::_('JT_SUBMENU_PERSONS'); ?></span>
					</a>
				</div>
			</div>
		</div>
		<div id="head_error" style="display: none;">
			<div style="float: left">
				<h1><?php echo JText::_('JTPROCGEDCOM_HERROR'); ?></h1>
			</div>
			<div style="float: right">
				<div class="jt-icon">
					<a href="index.php?option=com_joaktree&view=jt_applications">
						<img src="components/com_joaktree/assets/images/icon-48-app.png" />
						<br />
						<span><?php echo JText::_('JT_SUBMENU_APPLICATIONS'); ?></span>
					</a>
				</div>
			</div>
		</div>

	</div>
</div>
<div style="clear: both;"></div>

<div style="float: right; width: 50%;">
	<fieldset>
		<legend><?php echo JText::_('JTPROCESS_MSG'); ?></legend>
		<div id="procmsg"></div>
		
	</fieldset>
</div>

<?php foreach($this->items as $item) { ?>

<div class="form-horizontal" style="width: 40%;">
	<fieldset>
		<legend><?php echo $item->title; ?></legend>

		<div class="tab-content">
			<div class="tab-pane active">
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_('JT_HEADING_ID'); ?>
					</div>
					<div class="controls">
						<input 
							type="text" 
							id="id_<?php echo $item->id; ?>" 
							class="readonly"
							value="<?php echo $item->id; ?>"
							readonly="readonly"
						/>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_('JTPROCESS_START'); ?>
					</div>
					<div class="controls">
						<input 
							type="text" 
							id="start_<?php echo $item->id; ?>" 
							class="readonly"
							value=""
							readonly="readonly"
						/>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_('JTPROCESS_CURRENT'); ?>
					</div>
					<div class="controls">
						<input 
							type="text" 
							id="current_<?php echo $item->id; ?>" 
							class="readonly"
							value=""
							readonly="readonly"
						/>
					</div>
				</div>
				<div class="control-group" id="l_persons_<?php echo $item->id; ?>" style="display: none;">
					<div class="control-label">
						<?php echo JText::sprintf('JTGEDCOM_MESSAGE_PERSONS', null); ?>
					</div>
					<div class="controls">
						<input 
							type="text" 
							id="persons_<?php echo $item->id; ?>" 
							class="readonly"
							value="0"
							readonly="readonly"
						/>
					</div>
				</div>
				<div class="control-group" id="l_families_<?php echo $item->id; ?>"style="display: none;">
					<div class="control-label">
						<?php echo JText::sprintf('JTGEDCOM_MESSAGE_FAMILIES', null); ?>
					</div>
					<div class="controls">
						<input 
							type="text" 
							id="families_<?php echo $item->id; ?>" 
							class="readonly"
							value="0"
							readonly="readonly"
						/>
					</div>
				</div>
				<div class="control-group" id="l_sources_<?php echo $item->id; ?>"style="display: none;">
					<div class="control-label">
						<?php echo JText::sprintf('JTGEDCOM_MESSAGE_SOURCES', null); ?>
					</div>
					<div class="controls">
						<input 
							type="text" 
							id="sources_<?php echo $item->id; ?>" 
							class="readonly"
							value="0"
							readonly="readonly"
						/>
					</div>
				</div>
				<div class="control-group" id="l_repos_<?php echo $item->id; ?>"style="display: none;">
					<div class="control-label">
						<?php echo JText::sprintf('JTGEDCOM_MESSAGE_REPOS', null); ?>
					</div>
					<div class="controls">
						<input 
							type="text" 
							id="repos_<?php echo $item->id; ?>" 
							class="readonly"
							value="0"
							readonly="readonly"
						/>
					</div>
				</div>
				<div class="control-group" id="l_notes_<?php echo $item->id; ?>"style="display: none;">
					<div class="control-label">
						<?php echo JText::sprintf('JTGEDCOM_MESSAGE_NOTES', null); ?>
					</div>
					<div class="controls">
						<input 
							type="text" 
							id="notes_<?php echo $item->id; ?>" 
							class="readonly"
							value="0"
							readonly="readonly"
						/>
					</div>
				</div>
				<div class="control-group" id="l_unknown_<?php echo $item->id; ?>"style="display: none;">
					<div class="control-label">
						<?php echo JText::sprintf('JTGEDCOM_MESSAGE_UNKNOWN', null); ?>
					</div>
					<div class="controls">
						<input 
							type="text" 
							id="unknown_<?php echo $item->id; ?>" 
							class="readonly"
							value="0"
							readonly="readonly"
						/>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_('JTPROCESS_END'); ?>
					</div>
					<div class="controls">
						<input 
							type="text" 
							id="end_<?php echo $item->id; ?>" 
							class="readonly"
							value=""
							readonly="readonly"
						/>
					</div>
				</div>

			</div>
		</div>
		
		<div class="clr"> </div>
	</fieldset>	
</div>

<?php } ?>



<?php
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
JHtml::_('formbehavior.chosen', 'select');

$sortFields = $this->getSortFields();

//$staticmapAPIkey  = (isset($this->mapSettings->staticmap)) ? $this->mapSettings->staticmap.'APIkey' : '';
//$interactivemapAPIkey = (isset($this->mapSettings->interactivemap)) ? $this->mapSettings->interactivemap.'APIkey' : '';
$geocodeAPIkey    = (isset($this->mapSettings->geocode)) ? $this->mapSettings->geocode.'APIkey' : '';
?>

<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $this->lists['order']; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>

<form 
	action="<?php echo JRoute::_('index.php?option=com_joaktree'); ?>" 
	method="post" 
	id="adminForm" 
	name="adminForm"
>

<?php if(!empty( $this->sidebar)) { ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<?php  $divClassSpan = 'span10'; ?>
<?php } else { ?>
	<?php  $divClassSpan = ''; ?>	
<?php } ?>
	<div id="j-main-container" class="<?php echo $divClassSpan; ?>">

		<!-- ========= Showing map and service settings ========= -->
		<?php if (  (empty($this->mapSettings->geocode)) 
		 		 || ((!empty($this->mapSettings->geocode))   && isset($this->mapSettings->$geocodeAPIkey) && empty($this->mapSettings->$geocodeAPIkey))		 
		 		 ) {
		?>
			<fieldset class="adminform">
				<legend><?php echo JText::_('JTMAP_TITLE_PARAMS');?></legend>
				<table >
					<tr class="row0">
						<th class="pull-left" style="padding-right: 20px;"><?php echo JText::_('MBJ_GEOCODE'); ?></th>
					    <td><?php echo (empty($this->mapSettings->geocode))
		    						? '<strong style="color: red">'.JText::_('JNO').'</strong>'
		    						: ucfirst($this->mapSettings->geocode); ?>
						</td>
						<th class="pull-left" style="padding-right: 20px; padding-left: 35px;"><?php echo JText::_('COM_JOAKTREE_API_LABEL'); ?></th>
					    <td><?php echo (isset($this->mapSettings->$geocodeAPIkey) && !empty($this->mapSettings->$geocodeAPIkey)) 
					    			? JText::_('JYES')
					    			: ( (!isset($this->mapSettings->$geocodeAPIkey))
					    			  ? JText::_('...')
					    			  : '<strong style="color: red">'.JText::_('JNO').'</strong>'
					    			);?>
						</td>
						<th class="pull-left" style="padding-right: 20px; padding-left: 35px;"><?php echo JText::_('MBJ_LABEL_LOADSIZE'); ?></th>
					    <td><?php echo (isset($this->mapSettings->maxloadsize)) 
					    			? $this->mapSettings->maxloadsize 
					    			: ''; ?>
						</td>
					</tr>			
					<tr class="row1">
						<th class="pull-left" style="padding-right: 20px;"><?php echo JText::_('JT_NUM_LOCATIONS'); ?></th>
					    <td><?php echo $this->mapSettings->total; ?></td>
						<th class="pull-left" style="padding-right: 20px; padding-left: 35px;"><?php echo JText::_('JT_NUM_INVALIDLOCATIONS'); ?></th>
					    <td><?php if ($this->mapSettings->invalidpc > 20) { ?>
		    					<strong style="color: red"><?php } ?>	    
		    					<?php echo $this->mapSettings->invalid.'&nbsp;('.$this->mapSettings->invalidpc.'%)'; ?>
		    					<?php if ($this->mapSettings->invalidpc > 20) { ?>
		    					</strong><?php } ?>
					    </td>
						<th class="pull-left" style="padding-right: 20px; padding-left: 35px;"><?php echo JText::_('MBJ_LABEL_INDHTTPS'); ?></th>
					    <td><?php echo (isset($this->mapSettings->indHttps) && $this->mapSettings->indHttps) ? JText::_('JYES') : JText::_('JNO'); ?></td>
					</tr>			
				
				</table>
			</fieldset>
			<div>&nbsp;<br />&nbsp;</div>
		<?php } ?>
		<!-- ========= END Showing map and service settings ========= -->
		
		<!--  filter row -->
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="search" class="element-invisible"><?php echo JText::_('JT_LABEL_FILTER');?></label>
				<input 
					type="text" 
					name="search" 
					id="search"
					placeholder="<?php echo JText::_('JT_LABEL_FILTER');?>"  
					value="<?php echo $this->escape($this->lists['search']); ?>" 
				/>
			</div>
			<div class="btn-group pull-left">
				<button 
					type="button" 
					class="btn hasTooltip" 
					title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"
					onclick="this.form.submit();">
					<i class="icon-search"></i>
				</button>
				<button 
					type="button" 
					class="btn hasTooltip" 
					title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" 
					onclick="document.id('search').value='';this.form.submit();">
					<i class="icon-remove"></i>
				</button>
			</div>

			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
					<option value="asc" <?php if ($this->lists['order_Dir'] == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
					<option value="desc" <?php if ($this->lists['order_Dir'] == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
				</select>
			</div>
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
					<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $this->lists['order']);?>
				</select>
			</div>

		</div>
		<div class="clearfix"> </div>
		
		<!--  table -->
		<table class="table table-striped" id="articleList">
			<thead>
				<tr>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JText::_( 'JT_HEADING_NUMBER' ); ?>
					</th>
					<th width="1%" class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>		
					<th class="nowrap hidden-phone">
						<?php echo JHTML::_('grid.sort', 'JT_LABEL_LOCATION', 'jln.value', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					</th>
					<th class="nowrap hidden-phone">
						<?php echo JHTML::_('grid.sort', 'JT_LABEL_GEOCODELOCATION', 'jln.resultValue', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					</th>
					<th class="nowrap hidden-phone">
						<?php echo JHTML::_('grid.sort', 'JT_LABEL_LATITUDE', 'jln.latitude', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					</th>
					<th class="nowrap hidden-phone">
						<?php echo JHTML::_('grid.sort', 'JT_LABEL_LONGITUDE', 'jln.longitude', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					</th>
					<th class="nowrap hidden-phone"> 
						<?php echo JText::_( 'JT_LABEL_SERVER' ); ?>
					</th>	
					<th class="nowrap hidden-phone">
						<?php echo JHTML::_('grid.sort', 'JT_LABEL_RESULTS', 'jln.results', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					</th>
					<th width="2%" class="nowrap center hidden-phone">
						<?php echo JText::_( 'JT_HEADING_ID' ); ?>
					</th>
				</tr>		
			</thead>
			<tfoot>
				<tr>
					<td colspan="9">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach ($this->items as $i => $row) {
					$click  = 'return listItemTask(\'cb'.$i.'\', \'edit\')';
					if ($row->indServerProcessed) {
						$server = JHtml::_('image', 'admin/' . 'tick.png', JText::_( 'JYES' ), null, true);
					} else {
						$server = JHtml::_('image', 'admin/' . 'publish_r.png', JText::_( 'JNO' ), null, true);
					}
					
					?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="nowrap center hidden-phone">
							<?php echo $this->pagination->getRowOffset( $i ); ?>
						</td>
						<td class="center hidden-phone">
							<?php echo JHTML::_('grid.id',   $i, $row->id ); ?>
						</td>
						<td class="nowrap hidden-phone">
							<?php if ($this->canDo->get('core.edit')) { ?>
								<a href="javascript:void(0);" onclick="<?php echo $click; ?>" title="<?php echo JText::_( 'JTTHEMES_TOOLTIP_EDIT' ); ?>">
									<?php echo $this->escape($row->value); ?>
								</a>
							<?php } else { ?>
									<?php echo $this->escape($row->value); ?>
							<?php } ?>
						</td>
						<td class="small hidden-phone">
							<?php echo $this->escape($row->resultValue);?>
						</td>						
						<td class="small hidden-phone">
							<?php echo $this->escape($row->latitude);?>
						</td>						
						<td class="small hidden-phone">
							<?php echo $this->escape($row->longitude);?>
						</td>						
						<td class="center small hidden-phone">
							<?php echo $server;?>
						</td>
						<td class="center small hidden-phone">
							<?php echo $row->results;?>
						</td>
						<td class="center small hidden-phone">
							<?php echo $row->id; ?>
						</td>
					</tr>
				<?php } ?>

			
			</tbody>		
		</table>
		
		<input type="hidden" name="option" value="com_joaktree" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="jt_locations" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>



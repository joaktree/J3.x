<?php defined('_JEXEC') or die('Restricted access'); ?>


<?php if ($this->lists['userAccess']) { ?> 
<!-- user has access to information -->

	<div id="jt-content">
		<table>
			<!-- header -->
			<thead>
				
				<tr>
					<th class="jt-content-th" width="5" align="center">
						<?php echo JText::_( 'JT_NUM' ); ?>
					</th>
					<th class="jt-content-th">
						<div class="jt-h3-list">
							<?php echo JText::_( 'JT_LABEL_NAME' ); ?>
						</div>
					</th>
					<th class="jt-content-th" align="center">
						<div class="jt-h3-list">
							<?php echo JText::_( 'BIRT' ); ?>
						</div>
					</th>
					<th class="jt-content-th" align="center">
						<div class="jt-h3-list">
							<?php echo JText::_( 'DEAT' ); ?>
						</div>
					</th>
				</tr>
			</thead>
			<!-- table body -->
			<tbody>
			<?php
			$k = 2;
			for ($i=0, $n=count( $this->personlist ); $i < $n; $i++)	{
				$row = &$this->personlist[$i];
				$link = JRoute::_('index.php?option=com_joaktree&view=joaktree'
						 .'&tech='.$this->lists['technology']
						 .'&Itemid='.$this->lists['menuItemId']
						 .'&treeId='.$this->lists['tree_id']
						 .'&personId='.$row->app_id.'!'.$row->id
						 );
				$robot = ($this->lists['technology'] == 'a') ? '' : 'rel="noindex, nofollow"';
				
				$rowclass = 'jt-table-entry' . $k;
			?>
				<tr class="<?php echo $rowclass; ?>" >
					<td align="center"><?php echo $this->pagination->getRowOffset( $i ); ?></td>
					<td>
						<a href="<?php echo $link; ?>" target="_top" <?php echo $robot; ?>>
							<?php echo $row->firstName.' '.$row->familyName; ?>
						</a>
					</td>
					<td><?php echo $row->birthDate; ?></td>
					<td><?php echo $row->deathDate; ?></td>
				</tr>
				<?php
			       $k = 3 - $k;
			}
			?>
			</tbody>
		</table>
	</div> <!-- jt-content -->
	
<?php } else { ?>
<!-- user has NO access to information -->
	<div class="jt-content-th" >
		<div class="jt-noaccess"><?php echo JText::_( 'JT_NOACCESS' ); ?></div>
	</div>
<?php } ?>

<?php echo JHTML::_( 'form.token' ); ?>
<!-- 
<input type="hidden" name="option" value="com_joaktree" />
<input type="hidden" name="treeId" value="< ?php echo $this->lists['tree_id']; ?>" />
<input type="hidden" name="view" value="joaktreelist" />
<input type="hidden" name="itemId" value="< ?php echo $this->lists['menuItemId2']; ?>" />
<input type="hidden" name="filter_order" value="< ?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
 -->


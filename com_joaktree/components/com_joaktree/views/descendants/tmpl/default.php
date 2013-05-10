<?php defined('_JEXEC') or die('Restricted access'); ?>

<div id="jt-content">

<?php if ($this->lists['userAccess']) { ?> 
<!-- user has access to information -->
	<div class="jt-h1">
		<?php echo JText::_('JT_DESCENDANTS').'&nbsp;'.JText::_('JT_OF') ?>
		<?php 
			$link = JRoute::_('index.php?option=com_joaktree&view=joaktree'
							 .'&tech='.$this->lists['technology']
							 .'&Itemid='.$this->person->menuItemId
							 .'&treeId='.$this->lists['treeId']
							 .'&personId='.$this->person->app_id.'!'.$this->person->id
							 );
			$robot = ($this->lists['technology'] == 'a') ? '' : 'rel="noindex, nofollow"';
		?>
		<a href="<?php echo $link; ?>" <?php echo $robot; ?>><?php echo $this->person->fullName; ?></a>
	</div>
	<hr width="100%" size="2" />
		
	<?php 
		$layout = $this->setLayout(null);
		$this->display('generation');
		$this->setLayout($layout);
	?>
			
<?php } else { ?>
<!-- user has NO access to information -->
	<div class="jt-content-th" >
		<div class="jt-noaccess"><?php echo JText::_( 'JT_NOACCESS' ); ?></div>
	</div>
<?php } ?>

<div class="jt-clearfix jt-update">
	<?php echo $this->lists[ 'lastUpdate' ]; ?>
</div>
<div class="jt-stamp">
	<?php echo $this->lists[ 'CR' ]; ?>
</div>

</div><!-- jt-content -->

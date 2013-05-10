<?php 
// no direct access
defined('_JEXEC') or die('Restricted access'); 

// Load mooTools
JHtml::_('behavior.framework', true);

// import component libraries
JLoader::import('helper.formhelper', JPATH_COMPONENT);
?>

<script>
window.addEvent('domready', function() {
    set_jtTabs();
});
</script>

<?php // script for domready
  if ( (is_object($this->canDo)) && 
		(  $this->canDo->get('core.create')
		|| $this->canDo->get('media.create')
		|| $this->canDo->get('core.edit')
		|| $this->canDo->get('core.edit.state')
		|| $this->canDo->get('core.delete')
		)
	 && ($this->lists['edit'])
   ) {
   		$script = array();
		$script[] = '<script>';
		$script[] = 'window.addEvent(\'domready\', function() {';
		$script[] = 'toggleAjaxTabs(5, \'0\'); return false;';
		$script[] = '});';			
		$script[] = '</script>';
		echo implode("\n", $script);
	}
?>

<!--  shadowbox -->
<script type="text/javascript">
	Shadowbox.init({
	    continuous:     true,
	    handleOversize: "resize",
	    slideshowDelay: <?php echo $this->lists['nextDelay']; ?>,
	    fadeDuration:   <?php echo $this->lists['transDelay']; ?>
	});	
</script>

<?php if ( (is_object($this->canDo)) && (  $this->canDo->get('core.create')
										|| $this->canDo->get('media.create')
										|| $this->canDo->get('core.edit')
										|| $this->canDo->get('core.edit.state')
										|| $this->canDo->get('core.delete')
										) 
		 ) 
	  {
		 	echo FormHelper::getSubmitScript(trim($this->person->firstName).' '.trim($this->person->familyName));
	  }
?>	
 
<div id="jt-content">
<?php if ($this->lists['userAccess']) { ?> 
<form action="<?php echo JRoute::_('index.php?option=com_joaktree'); ?>" method="post" name="joaktreeForm" id="joaktreeForm" class="form-validate">
<!-- user has access to information -->

	<!-- no Javascript :: show link to switch to basic view (not using Javascript) -->
	<noscript>
		<?php 
		if ($this->lists['technology'] != 'b') {
			$link1 = JRoute::_( 'index.php?option=com_joaktree&view=joaktree&tech=b&treeId='.$this->person->tree_id.'&personId='.$this->person->app_id.'!'.$this->person->id );
		?>
			<div class="jt-content-th">
				<?php echo JText::_( 'JT_NOJAVASCRIPT' ); ?>
				<br/><a href="<?php echo $link1; ?>" rel="noindex, nofollow"><?php echo JText::_( 'JT_NOJAVASCRIPT_LINK' ); ?></a>
			</div>
		<?php } ?>
	</noscript>
	
	<!-- no AJAX :: show link to switch to view using only Javascript -->
	<div id="noajaxid" class="jt-hide">
		<div class="jt-content-th">
		<?php 
			$link2 = JRoute::_( 'index.php?option=com_joaktree&view=joaktree&tech=j&treeId='.$this->person->tree_id.'&personId='.$this->person->app_id.'!'.$this->person->id );
		?>
		<?php echo JText::_( 'JT_NOAJAX' ); ?>
		<br/><a href="<?php echo $link2; ?>" rel="noindex, nofollow"><?php echo JText::_( 'JT_NOAJAX_LINK' ); ?></a>
		</div>
	</div>

	<!-- Show lineage -->
	<?php echo $this->Html[ 'lineage' ]; ?>
	
	<?php 
		$layout = $this->setLayout(null);
		$this->display('names');
		$this->setLayout($layout);
	?>
	
	<!-- tabs only active with AJAX -->
	<?php 
	if (($this->lists['technology'] != 'b') && ($this->lists['technology'] != 'j')) {
		$html = '';
		
		if ( ( ($this->person->indHasParent == true) && ($this->lists['showAncestors'] == 1) )
		   ||( ($this->person->indHasChild == true) && ($this->lists['showDescendants'] == 1) )
		   ||(  $this->person->indNote == true )
		   ||(  $this->lists['numberArticles'] > 0 )
		   ||( (is_object($this->canDo)) && 
					(  $this->canDo->get('core.create')
					|| $this->canDo->get('media.create')
					|| $this->canDo->get('core.edit')
					|| $this->canDo->get('core.edit.state')
					|| $this->canDo->get('core.delete')
					) 
			 )
		   ) {
		   	$html .= '<div id="jt-tabbar" class="jt-clearfix">';
		   			   	
		   	$html .= '<span class="jt-tabline">&nbsp;</span>';
		   	
		   	$link = '';
		   	$html .= '<a href="#" id="jt1tabid" ';
	   		$html .= 'class="jt-tab-active jt-tablabel-active" ';		   	
		   	$html .= 'style="position: relative;" ';
			$html .= $this->lists[ 'action' ].'="toggleAjaxTabs(1, \''.$link.'\'); return false;">';
		   	$html .= JText::_('JT_DETAILS');
		   	$html .= '</a>';
		   	
			if (($this->person->indNote == true) || (  $this->lists['numberArticles'] > 0 ))  {
		   		$html .= '<span class="jt-tabline">&nbsp;</span>';
				$link =  JRoute::_('index.php?format=raw&option=com_joaktree'
						.'&view=joaktree&layout=_information'
						.'&personId='.$this->person->app_id.'!'.$this->person->id
						.'&treeId='.$this->person->tree_id
						.'&technology='.$this->lists['technology']
						);
			
				$html .= '<a href="#" id="jt2tabid" class="jt-tab-inactive jt-tablabel-inactive" style="position: relative;" ';
				$html .= 'title="'.JText::_('JT_SHOW').' '.JText::_('JT_INFORMATION').'" ';
				$html .= $this->lists[ 'action' ].'="toggleAjaxTabs(2, \''.$link.'\'); return false;" >';
				$html .= JText::_('JT_INFORMATION');
				$html .= '</a>';
			}
			
		   	if ( ($this->person->indHasParent == true) && ($this->lists['showAncestors'] == 1) ) {
		   		$html .= '<span class="jt-tabline">&nbsp;</span>';
				$link =  JRoute::_('index.php?format=raw&option=com_joaktree'
						.'&view=ancestors&layout=_generation'
						.'&personId='.$this->person->app_id.'!'.$this->person->id
						.'&treeId='.$this->person->tree_id
						.'&technology='.$this->lists['technology']
						);
						
				$html .= '<a href="#" id="jt3tabid" class="jt-tab-inactive jt-tablabel-inactive" style="position: relative;" ';
				$html .= 'title="'.JText::_('JT_SHOW').' '.JText::_('JT_ANCESTORS').'" ';
				$html .= $this->lists[ 'action' ].'="toggleAjaxTabs(3, \''.$link.'\'); return false;" >';
				$html .= JText::_('JT_ANCESTORS');
				$html .= '</a>';
			}
			
			if ( ($this->person->indHasChild == true) && ($this->lists['showDescendants'] == 1) ) {
				$html .= '<span class="jt-tabline">&nbsp;</span>';
				$link =  Jroute::_('index.php?format=raw&option=com_joaktree'
						.'&view=descendants&layout=_generation'
						.'&personId='.$this->person->app_id.'!'.$this->person->id
						.'&treeId='.$this->person->tree_id
						.'&technology='.$this->lists['technology']
						);
						
				$html .= '<a href="#" id="jt4tabid" class="jt-tab-inactive jt-tablabel-inactive" style="position: relative;" ';
				$html .= 'title="'.JText::_('JT_SHOW').' '.JText::_('JT_DESCENDANTS').'" ';
				$html .= $this->lists[ 'action' ].'="toggleAjaxTabs(4, \''.$link.'\'); return false;" >';
				$html .= JText::_('JT_DESCENDANTS');
				$html .= '</a>';
					
			}
							
			if ( (is_object($this->canDo)) && 
					(  $this->canDo->get('core.create')
					|| $this->canDo->get('media.create')
					|| $this->canDo->get('core.edit')
					|| $this->canDo->get('core.edit.state')
					|| $this->canDo->get('core.delete')
					) 
			   ) {
				$html .= '<span class="jt-tabline" style="float: right;">&nbsp;</span>';
				$link = 0;						
				$html .= '<a href="#" id="jt5tabid" ';
		   		$html .= 'class="jt-tab-edit jt-tablabel-inactive" ';
			   	$html .= 'style="position: relative;" ';

				$html .= 'title="'.JText::_('JACTION_EDIT').'" ';
				$html .= $this->lists[ 'action' ].'="toggleAjaxTabs(5, \''.$link.'\'); return false;" >';
				$html .= JText::_('JACTION_EDIT');
				$html .= '</a>';	
				
			}
			
			$html .= '<span class="jt-tabline">&nbsp;</span>';
		   	
		   	$html .= '</div>';
		}
		
		echo $html;
	}
	?>	
	
	<div id="jt1tabpageid" class="jt-show-block">
		<!-- two columns for basic information and picture -->
		<div class="jt-clearfix">
			<div class="jt-person-info">
				<div style="min-height: 12em;">
				<!-- Show person -->
				<?php 
					$layout = $this->setLayout(null);
					$this->display('personevents');
					$this->setLayout($layout);
				?>
				</div>
				
				<?php 
					$layout = $this->setLayout(null);
					$this->display('sourceornotebutton');
					$this->setLayout($layout);
				?>
				
			</div> <!-- end float left -->
			
			<div class="jt-picture">
				<!-- Show picture -->			
				<?php 
					$layout = $this->setLayout(null);
					$this->display('pictures');
					$this->setLayout($layout);
				?>
			</div> <!-- end float right -->
		</div> <!-- end clearfix -->
		
		<div class="jt-clearfix">
			<!-- source text is shown below the two columns -->
			<?php 
				$layout = $this->setLayout(null);
				$this->display('sourceornotetext');
				$this->setLayout($layout);
			?>
			
			<!-- show static map -->
			<?php if (($this->person->map == 1) && ($this->lists['indStaticMap'])) { ?>						
				<img src="<?php echo $this->Html[ 'staticmap' ]; ?>" />	  
			<?php } ?>
			
			<!-- show interactive map -->
			<?php if (($this->person->map == 2) && ($this->lists['indInteractiveMap'])) { 
					$height = 'height: '.((int) $this->lists[ 'pxHeightMap']+ 0).'px; ';
			?>						
				<div style="<?php echo $height; ?>">
					<iframe 
						src="<?php echo $this->Html[ 'interactivemap' ]; ?>" 
						height="<?php echo (int) $this->lists[ 'pxHeightMap'];?>px"
						style="border:1px solid #dddddd;"
					>
					</iframe>
				</div>	
				<div class="jt-clearfix"></div>  
			<?php } ?>

			<hr width="100%" size="2" />
			
			<?php 
				$layout = $this->setLayout(null);
				$this->display('parents');
				$this->setLayout($layout);
				
				$layout = $this->setLayout(null);
				$this->display('partners');
				$this->setLayout($layout);

				$layout = $this->setLayout(null);
				$this->display('children');
				$this->setLayout($layout);
			?>
		
		</div> <!-- clearfix -->
	</div>
	
	<!-- tabs only active with AJAX -->
	<?php 
	if (($this->lists['technology'] != 'b') && ($this->lists['technology'] != 'j')) {
	?>		
		<div id="jt2tabpageid" class="jt-ajax">
			<div class="jt-ajax-loader"><?php  echo JText::_('JT_LOADING').'&nbsp;'.JText::_('JT_INFORMATION'); ?></div>
		</div>
		<div id="jt3tabpageid" class="jt-ajax">
			<div class="jt-ajax-loader"><?php  echo JText::_('JT_LOADING').'&nbsp;'.JText::_('JT_ANCESTORS'); ?></div>
		</div>
		<div id="jt4tabpageid" class="jt-ajax">
			<div class="jt-ajax-loader"><?php  echo JText::_('JT_LOADING').'&nbsp;'.JText::_('JT_DESCENDANTS'); ?></div>
		</div>
	<?php 
	}
	?>
	
	<input type="hidden" name="personId" value="<?php echo $this->person->app_id.'!'.$this->person->id; ?>" />
	<input type="hidden" name="relationId" value="" id="jform_person_relation_id"/ >
	<input type="hidden" name="treeId" value="<?php echo $this->person->tree_id; ?>" />
	<input type="hidden" name="tech" value="<?php echo $this->lists['technology']; ?>" />
	<input type="hidden" name="object" value="" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="personform" />
	<?php echo JHtml::_('form.token'); ?>	
</form>
<?php } else { ?>
<!-- user has NO access to information -->
	<div class="jt-content-th" >
		<div class="jt-noaccess"><?php echo JText::_( 'JT_NOACCESS' ); ?></div>
	</div>
<?php } ?>
	<div class="jt-clearfix jt-update">
	<?php 
		if ($this->lists[ 'showUpdate '] != 'N') {
			echo $this->lists[ 'lastUpdate' ];
			if ($this->lists[ 'showchange' ] == 1) {			
				$link =  Jroute::_('index.php?&option=com_joaktree'
									.(($this->lists['technology'] != 'b') ? '&tmpl=component' : '')
									.'&view=changehistory'
									.'&retId='.$this->lists[ 'retId' ]
									.'&treeId='.$this->person->tree_id
									.'&technology='.$this->lists['technology']
									);
				$properties = ($this->lists['technology'] != 'b') 
					? 'class="modal"  rel="{handler: \'iframe\', size: {x: 875, y: 460}, onClose: function() {}}"'
					: 'rel="noindex, nofollow"';
	?>
				&nbsp;|&nbsp;
				<a href="<?php echo $link; ?>" <?php echo $properties; ?>>
					<?php echo JText::_('JT_CHANGEHISTORY'); ?>
				</a>
	<?php	} 
		}
	?>
	</div>
	<div class="jt-stamp">
		<?php echo $this->lists[ 'CR' ]; ?>
	</div>
	
</div><!-- jt-content -->
	

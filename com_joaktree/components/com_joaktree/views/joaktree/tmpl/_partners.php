<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php		
$html = '';
$linkBase = 'index.php?option=com_joaktree&view=joaktree&tech='.$this->lists['technology'].'';
$linkBaseRaw = 'index.php?format=raw&tmpl=component&option=com_joaktree&view=joaktree&tech='.$this->lists['technology'].'';
$robot = ($this->lists['technology'] == 'a') ? '' : 'rel="noindex, nofollow"';

$partners = $this->person->getPartners('full');

// Button for editing (only active with AJAX)
if (($this->lists['technology'] != 'b') && ($this->lists['technology'] != 'j')) {
	if (is_object($this->canDo)) {
   		$html .= '<div class="jt-clearfix"></div>';
   		$html .= '<div class="jt-edit-2" style="text-align: right;">';
		if ($this->canDo->get('core.create')) {
			$html .= '<a href="#" onclick="jtsubmitbutton(\'edit\', \'newpartner\');" >';
			$html .= JText::_('JT_ADDPARTNER');
			$html .= '</a>';
   		} else {
			$html .= '<span class="jt-edit-nolink" title="'.JText::_('JT_NOPERMISSION_DESC').'" >';
   			$html .= JText::_('JT_ADDPARTNER');
   			$html .= '</span>';
   		}
   		
   		$html .= '&nbsp;|';
   		 
   		if (count($partners) > 0) {
   			if ($this->canDo->get('core.edit')) {
				$html .= '&nbsp;<a href="#" onclick="jtsubmitbutton(\'edit\', \'partners\');" >';
				$html .= JText::_('JT_EDITPARTNERS');
				$html .= '</a>';
   			} else {
				$html .= '&nbsp;<span class="jt-edit-nolink" title="'.JText::_('JT_NOPERMISSION_DESC').'" >';
	   			$html .= JText::_('JT_EDITPARTNERS');
   				$html .= '</span>';
	   		$html .= '&nbsp;|';
   			}
   		}
   		$html .= '</div>';
   }	
}

if (count( $partners ) > 0) {
	if (count( $partners ) == 1) {
		switch ( $partners[0]->sex ) {
			case "M":	$label = ($partners[0]->relationtype == 'partner') 
									? JText::_('JT_PARTNER') : JText::_('JT_HUSBAND');
					BREAK;
			case "F": 	$label = ($partners[0]->relationtype == 'partner') 
									? JText::_('JT_PARTNER') : JText::_('JT_WIFE');
					BREAK;
			default: 	$label = JText::_('JT_PARTNER');
					BREAK;
		}
	} else {
		switch ( $partners[0]->sex ) {
			case "M":	$label = ($partners[0]->relationtype == 'partner') 
									? JText::_('JT_PARTNERS') : JText::_('JT_HUSBANDS');
					BREAK;
			case "F": 	$label = ($partners[0]->relationtype == 'partner') 
									? JText::_('JT_PARTNERS') : JText::_('JT_WIFES');
					BREAK;
			default: 	$label = JText::_('JT_PARTNERS');
					BREAK;
		}
	}

	$html .= '<div class="jt-clearfix">';
	$html .= '<span class="jt-left-col-label jt-h3">' . $label . '</span>';
	if (($this->lists['technology'] != 'b') and ($this->lists['technology'] != 'j')) {
		$html .= '<span class="jt-detail-col-label jt-h3">&nbsp;</span>';
	}
	$html .= '<span class="jt-right-col-label jt-h3">' . JText::_('JT_BORN') . '</span>';
	$html .= '<span class="jt-right-col-label jt-h3">' . JText::_('JT_DIED') . '</span>';
	$html .= '</div>';
	
	foreach ($partners as $partner) {
		// Button for editing (only active with AJAX)
		if (($this->lists['technology'] != 'b') && ($this->lists['technology'] != 'j')) {
			if (is_object($this->canDo)) {
			   	$indliving = (($this->person->living) && ($partner->living)) ? true : false;
			   	$display = FormHelper::checkDisplay('relation', $indliving);
			   	
			   	If ($display) {
					$html .= '<div class="jt-clearfix"></div>';
				   	$html .= '<div class="jt-edit-2" style="text-align: right;">';
				   	
					if ($this->canDo->get('core.edit'))  {
						$html .= '<a href="#" ';
						$html .= '   title="'.JText::sprintf('JT_EDITPARTNEREVENTS_DESC', $partner->firstName).'"';
						$html .= '   onclick="jtsetrelation(\''.$partner->id.'\'); jtsubmitbutton(\'edit\', \'partnerevents\');" >';
						$html .= JText::_('JT_EDITPARTNEREVENTS');
						$html .= '</a>';
					} else {
						$html .= '<span class="jt-edit-nolink" title="'.JText::_('JT_NOPERMISSION_DESC').'" >';
			   			$html .= JText::_('JT_EDITPARTNEREVENTS');
		   				$html .= '</span>';
					}
		   			$html .= '&nbsp;|';
					$html .= '</div>';
			   	}
			}	
		}		
		
		$divid 	= $this->person->id.$partner->id;
		$link  = JRoute::_( $linkBase.'&Itemid='.$partner->menuItemId.'&treeId='.$partner->tree_id.'&personId='.$partner->app_id.'!'.$partner->id);
		
		$html .= '<div class="jt-clearfix">';
		
		// name of person
		$html .= '<span class="jt-table-row jt-left-col-label">';
		if ($partner->indHasPage) { 
			$html .= '<a href="' . $link . '" '.$robot.' >';
		}
		$html .= $partner->firstNamePatronym . " " . $partner->familyName;
		if ($partner->indHasPage) { 
			$html .= '</a>';
		}
		$html .= '</span>';
		
		// links to details
		if (($this->lists['technology'] != 'b') and ($this->lists['technology'] != 'j')) {
			$html .= '<span class="jt-detail-col-label">';
			
			$link =  JRoute::_($linkBaseRaw.'&layout=_personevents'
				.'&Itemid='.$partner->menuItemId.'&treeId='.$partner->tree_id.'&personId='.$partner->app_id.'!'.$partner->id);
			$html .= '<a href="#" id="bev'.$divid.'"class="jt-button-closed jt-buttonlabel" ';
			$html .= 'title="'.JText::_('JT_SHOW').' '.JText::_('JT_DETAILS').'" ';
			$html .= 'onclick="drilldownAjaxDetail(\'bev'.$divid.'\', \'event'.$divid.'\', \''.$link.'\');return false;">';
			$html .= JText::_('JT_DETAILS') . '</a>&nbsp;';
			
			$html .= '</span>';
		}
		
		// basic information
		$html .= '<span class="jt-right-col-label">' . $partner->birthDate . '&nbsp;</span>';
		$html .= '<span class="jt-right-col-label">' . $partner->deathDate . '&nbsp;</span>';
		
		$html .= '</div>';
		
		// show relation events
		$partner2[ 'id' ]     	= $partner->id;
		$partner2[ 'living' ]	= $partner->living;
		$this->assignRef( 'partner',	$partner2);
		
		// show template
		$layout = $this->setLayout(null);
		$html .= $this->loadTemplate('partnerevents');
		$this->setLayout($layout);

		// block with details is shown below the person
		if (($this->lists['technology'] != 'b') and ($this->lists['technology'] != 'j')) {
			$html .= '<div id="event'.$divid.'" class="jt-clearfix jt-person-drilldown1-info jt-ajax">';
			$html .= '<div class="jt-high-row jt-ajax-loader">&nbsp;</div></div>';
		}
	}
}

		

echo $html;
?>


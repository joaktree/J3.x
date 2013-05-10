<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php		
$html = '';

$indEvents = false;
$events = $this->person->getPersonEvents();

// Button for editing (only active with AJAX)
if (($this->lists['technology'] != 'b') && ($this->lists['technology'] != 'j')) {
	if (is_object($this->canDo)) {
	   	$display = FormHelper::checkDisplay('person', $this->person->living);
			   	
	   	If ($display) {
			$html .= '<div class="jt-edit-2" style="text-align: right;">';
			if ($this->canDo->get('core.edit')) {
				$html .= '<a href="#" onclick="jtsubmitbutton(\'edit\', \'pevents\');" >';
				$html .= JText::_('JT_EDITEVENTS');
				$html .= '</a>';	
			} else {
				$html .= '<span class="jt-edit-nolink" title="'.JText::_('JT_NOPERMISSION_DESC').'" >';
	   			$html .= JText::_('JT_EDITEVENTS');
	   			$html .= '</span>';
			}
	   		$html .= '&nbsp;|';
			$html .= '</div>';
	   	}
	}
}

$i=0;
foreach ($events as $event) {
	$html .= '<div class="jt-clearfix">';
	if (  ($event->eventDate   != null) 
	   or ($event->location    != null) 
	   or ($event->value       != null)
	   or ($event->indNote     == true)
	   or ($event->indCitation == true)
	   ) {
		$indEvents = true;
		$html .= '<span class="jt-high-row jt-label">' . JText::_($event->code);
		if ($event->type) {
			$tmpType = str_replace(' ', '+', $event->type);
			$html .= ' - ' . JText::_($tmpType);
		}
		$html .=': </span>';
		
		$html .= '<span class="jt-high-row jt-iconlabel">';
		
		if ($event->indNote == true) {
			$njtid1 = 'jt1notpev'.$i.$this->person->id;
			$njtid2 = 'jt2notpev'.$i.$this->person->id;
			if ($this->lists['technology'] != 'b') {
				
				$html .= '<a href="#" id="'.$njtid1.'" class="jt-notes-icon"';
				if (($this->lists['technology'] == 'j') or ($event->indAltNote == true)) {
					$html .= 'onMouseOver="ShowPopup(\''.$njtid1.'\', \''.$njtid2.'\', 0, 0);return false;"';
				} else {
					$link =  JRoute::_('index.php?format=raw&option=com_joaktree'
						.'&view=joaktree&layout=_detailnotes'
						.'&tmpl=component&type=person&subtype=pevent&orderNumber='.$event->orderNumber
						.'&personId='.$this->person->app_id.'!'.$this->person->id
						.'&treeId='.$this->person->tree_id
						);
					$html .= 'onMouseOver="ShowAjaxPopup(\''.$njtid1.'\', \''.$njtid2.'\', \''.$link.'\');return false;"';
				}
				
				$html .= 'onMouseOut="HidePopup(\''.$njtid2.'\');return false;">';
				$html .= '&nbsp;</a>';
			}
		} else {
			$html .=  '<span class="jt-empty-icon">&nbsp;</span>';
		}
		
		if ($event->indCitation == true) {
			$sjtid1 = 'jt1srcpev'.$i.$this->person->id;
			$sjtid2 = 'jt2srcpev'.$i.$this->person->id;
			if ($this->lists['technology'] != 'b') {
				$html .=  '<a href="#" id="'.$sjtid1.'" class="jt-sources-icon"';

				if (($this->lists['technology'] == 'j') or ($event->indAltSource == true)) {
					$html .= 'onMouseOver="ShowPopup(\''.$sjtid1.'\', \''.$sjtid2.'\', 0, 0);return false;"';
				} else {
					$link =  JRoute::_('index.php?format=raw&option=com_joaktree'
						.'&view=joaktree&layout=_detailsources'
						.'&tmpl=component&type=person&subtype=pevent&orderNumber='.$event->orderNumber
						.'&personId='.$this->person->app_id.'!'.$this->person->id
						.'&treeId='.$this->person->tree_id
						);
					$html .= 'onMouseOver="ShowAjaxPopup(\''.$sjtid1.'\', \''.$sjtid2.'\', \''.$link.'\');return false;"';
				}
				$html .= 'onMouseOut="HidePopup(\''.$sjtid2.'\');return false;">';
				$html .= '&nbsp;</a>';
			}
		} else {
			$html .= '<span class="jt-empty-icon">&nbsp;</span>';
		}
		
		$html .= '</span>'; // end of jt-iconlabel
		
		// show actual value
		$html .= '<span class="jt-high-row jt-valuelabel">';

		if ($event->value != null) {
			$html .= $event->value.'&nbsp;';
		}
		
		if (($event->value != null) and ( ($event->eventDate != null) or ($event->location != null) )){
			$html .= '(&nbsp;';
		}
		
		$html .= JoaktreeHelper::displayDate( $event->eventDate ).'&nbsp;';
		
		if ($event->location != null) {
			$html .= JText::_('JT_IN') . '&nbsp;' . $event->location . '&nbsp;';
		}
	
		if (($event->value != null) and ( ($event->eventDate != null) or ($event->location != null) )){
			$html .= ')&nbsp;';
		}
		
		$html .= '</span>'; // end of jt-valuelabel
	}
	$html .= '</div>';
	
	if ($event->indNote == true) {
		if ($this->lists['technology'] != 'b') {
			if (($this->lists['technology'] == 'j') or ($event->indAltNote == true)) {
				$html .= '<div id="'.$njtid2.'" class="jt-hide" style="position: absolute; z-index: 50;">';
			} else {
				$html .= '<div id="'.$njtid2.'" class="jt-ajax" style="position: absolute; z-index: 50;">';
			} 
			
			if ($event->indAltNote == true) {
				$html .= '<div class="jt-source">'.JText::_('JT_ALTERNATIVE').'</div>';
			} else if (($this->lists['technology'] != 'b') and($this->lists['technology'] != 'j')) {
				$html .=  '<div class="jt-ajax-loader">'.JText::_('JT_LOADING_NOTES').'</div>';
			} else {
				// prepare for template
				$notes[ 'type' ]     	= 'person';
				$notes[ 'subtype' ]  	= 'pevent';
				$notes[ 'orderNumber' ]	= $event->orderNumber;
				$notes[ 'relation_id' ]	= null;
				$this->assignRef( 'notes',	$notes);
			
				// show template
				$layout = $this->setLayout(null);
				$html .= $this->loadTemplate('detailnotes');
				$this->setLayout($layout);
			}
			$html .= '</div>';
		}
	}
	
	if ($event->indCitation == true) {
		if ($this->lists['technology'] != 'b') {
			if (($this->lists['technology'] == 'j') or ($event->indAltSource == true)) {
				$html .= '<div id="'.$sjtid2.'" class="jt-hide" style="position: absolute; z-index: 50;">';
			} else {
				$html .= '<div id="'.$sjtid2.'" class="jt-ajax" style="position: absolute; z-index: 50;">';
			} 
			
			if ($event->indAltSource == true) {
				$html .= '<div class="jt-source">'.JText::_('JT_ALTERNATIVE').'</div>';
			} else if (($this->lists['technology'] != 'b') and ($this->lists['technology'] != 'j')) {
				$html .= '<div class="jt-ajax-loader">'.JText::_('JT_LOADING_SOURCES').'</div>';
			} else {
				// prepare for template
				$sources[ 'type' ]     		= 'person';
				$sources[ 'subtype' ]  		= 'pevent';
				$sources[ 'orderNumber' ]	= $event->orderNumber;
				$sources[ 'relation_id' ]	= null;
				$this->assignRef( 'sources',	$sources);
	
				// show template
				$layout = $this->setLayout(null);
				$html .= $this->loadTemplate('detailsources');
				$this->setLayout($layout);
			}
			$html .= '</div>';
		}
	}
	$i++;
}

if ($indEvents == false) {
	$html .= '<div class="jt-clearfix">';
	$html .= '<span class="jt-high-row">' . JText::_('JT_NODATA').'</span>';
	$html .= '</div>';
}


echo $html;
?>


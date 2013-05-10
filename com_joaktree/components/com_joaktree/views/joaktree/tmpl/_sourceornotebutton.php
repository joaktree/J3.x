<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php 
$html = '';

$html .= '<div class="jt-clearfix jt-buttonbar">';

if ($this->person->indNote == true) {
	if ($this->lists['technology'] == 'j') {
		$html .= '<a href="#" id="jt1notesid" class="jt-button-closed jt-buttonlabel" ';
		$html .= 'title="'.JText::_('JT_SHOW').' '.JText::_('JT_NOTES').'" ';
		
		if ($this->lists['technology'] == 'j') {
			$html .= 'onclick="toggleNotesSources(1, \'jt1notesid\', \'jt2notesid\', \'jt1sourcesid\', \'jt2sourcesid\');';
		} else {
			// old code - kept here for future re-use
			$link =  JRoute::_('index.php?format=raw&option=com_joaktree'
				.'&view=joaktree&layout=_mainnotes'
				.'&tmpl=component&type=person&subtype=person'
				.'&personId='.$this->person->app_id.'!'.$this->person->id
				.'&treeId='.$this->person->tree_id
				);
				
			$html .= 'onclick="toggleAjaxNotesSources(1, \'jt1notesid\', \'jt2notesid\', \'jt1sourcesid\', \'jt2sourcesid\', \''.$link.'\');';
		}
		
		$html .= 'return false;">';
		$html .= JText::_('JT_NOTES') . '</a>&nbsp;&nbsp;&nbsp;';
	}
}

if ($this->person->indCitation == true) {
	if ($this->lists['technology'] != 'b') {
		$html .= '<a href="#" id="jt1sourcesid" class="jt-button-closed jt-buttonlabel" ';
		$html .= 'title="'.JText::_('JT_SHOW').' '.JText::_('JT_SOURCES').'" ';
		
		if ($this->lists['technology'] == 'j') {
			$html .= 'onclick="toggleNotesSources(2, \'jt1notesid\', \'jt2notesid\', \'jt1sourcesid\', \'jt2sourcesid\');';
		} else {
			$link =  Jroute::_('index.php?format=raw&option=com_joaktree'
				.'&view=joaktree&layout=_mainsources'
				.'&tmpl=component&type=person&subtype=personAll'
				.'&personId='.$this->person->app_id.'!'.$this->person->id
				.'&treeId='.$this->person->tree_id
				);
				
			$html .= 'onclick="toggleAjaxNotesSources(2, \'jt1notesid\', \'jt2notesid\', \'jt1sourcesid\', \'jt2sourcesid\', \''.$link.'\');';
			
		}
		
		$html .= 'return false;">';
		$html .= JText::_('JT_SOURCES') . '</a>&nbsp;&nbsp;&nbsp;';
	}
	
}

$html .= '</div>';

echo $html
?>

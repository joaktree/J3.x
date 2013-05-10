<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
$html = '';
$width = 100;

$lines = $this->person->getNotes($this->notes[ 'subtype' ], $this->notes[ 'orderNumber' ], $this->notes[ 'relation_id' ]);

$html .= '<div class="jt-source">';
$html .= '<div class="jt-h3">';
$html .= '<span style="float: left;">'.JText::_('JT_NOTES').'</span>';
$html .= '</div>';
$html .= '<div class="jt-clearfix"></div>';


// loop through notes
$i=0;
$n=count( $lines );

foreach ($lines as $line) {
	// if more than 1 note, a number is shown
	if ($n > 1) {
		$i++;
		$html .= $i.'.&nbsp;';
	}
	
	$text = str_replace('&#10;&#13;', '<br />', $line->text);
	$text = wordwrap($text, $width, '<br />');
	$html .= $text;			
} // end of loop

$html .= '</div>';
echo $html;
?>



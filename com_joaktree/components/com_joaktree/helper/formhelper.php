<?php
/**
 * Joomla! component Joaktree
 * file		front end form-helper object - formhelper.php
 *
 * @version	1.5.0
 * @author	Niels van Dantzig
 * @package	Joomla
 * @subpackage	Joaktree
 * @license	GNU/GPL
 *
 * Component for genealogy in Joomla!
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class FormHelper extends JObject { 
	
	public function getNameEventRow($indPHP, $type, &$form, $item, $object, $appId = null, $relationId = null) {
		$html = array();
		$rowMainId      = ($indPHP) ? 'rN_'.$object->orderNumber : 'rN_\'+orderNumber+\'';
		$rowMainRefId	= ($indPHP) ? $rowMainId.'_ref' : 'rN_\'+orderNumber+\'_ref';
		$rowMainNotId	= ($indPHP) ? $rowMainId.'_not' : 'rN_\'+orderNumber+\'_not';
		$rowMainEDId	= ($indPHP) ? $object->orderNumber  : '\'+orderNumber+\'';
		$tabRefId       = ($indPHP) ? 'tbref_'.$object->orderNumber : 'tbref_\'+orderNumber+\'';
		$tabNotId  		= ($indPHP) ? 'tbnot_'.$object->orderNumber : 'tbnot_\'+orderNumber+\'';
		$rowclass		= 'jt-table-entry4';
		
		switch ($type) {
			case "personName": 		$formRecord = 'person.names';
									break;
			case "personEvent": 				
			case "relationEvent": 	
			default:				$formRecord = 'person.events';
									break;				
		}
		
		//<!-- Row for one existing additional name / person event / relation event -->
		$html[] = '<tr id="'.$rowMainId.'" class="'.$rowclass.'" >';
		$html[] = $form->getInput('orderNumber', $formRecord,  (($indPHP) ? $object->orderNumber : '\'+orderNumber+\''));
		$html[] = $form->getInput('status', $formRecord, $rowMainId.'!'.(($indPHP) ? 'loaded' : 'new'));
		
		if ($type == 'personName') {
			$html[] = '<td>'.$form->getInput('code', $formRecord,  (($indPHP) ? $object->code  : null)).'</td>';
			$html[] = '<td>'.$form->getInput('value', $formRecord, (($indPHP) 
																	? htmlspecialchars_decode($object->value, ENT_QUOTES) 
																	: null
																	)).'</td>';
		} else {
			$html[] = '<td>';
			if ($type == 'relationEvent') {
				$html[] = $form->getLabel('relcode', $formRecord);
				$html[] = $form->getInput('relcode', $formRecord, (($indPHP) ? $object->code : null));
			} else {
				$html[] = $form->getLabel('code', $formRecord);
				$html[] = $form->getInput('code', $formRecord, (($indPHP) ? $object->code : null));
			}
			$html[] = $form->getLabel('type', $formRecord);
			$html[] = $form->getInput('type', $formRecord, (($indPHP) 
															? htmlspecialchars_decode($object->type, ENT_QUOTES) 
															: null
															));
			
			$htmlEventdate = self::getEventDateHTML((($indPHP) 
													? htmlspecialchars_decode($object->eventDate, ENT_QUOTES)  
													: null
													), $rowMainEDId, $formRecord, $form);
			if (is_array($htmlEventdate)) { 
				$html = array_merge($html, $htmlEventdate);
			}
			
			$html[] = $form->getLabel('location', $formRecord);
			$html[] = $form->getInput('location', $formRecord, (($indPHP) 
																? htmlspecialchars_decode($object->location, ENT_QUOTES) 
																: null
																));
			$html[] = $form->getLabel('value', $formRecord);
			$html[] = $form->getInput('value', $formRecord, (($indPHP) 
															? htmlspecialchars_decode($object->value, ENT_QUOTES) 
															: null
															));
			$html[] = '</td>';
		}

		$html[] = '<td>';
		$html[] = '   <span class="jt-edit">';
		$html[] = '      <a	href="#" ';
		if ($indPHP) {
			$html[] = '		onclick="return jtrefnot(\''.$rowMainNotId.'\');"';
		} else {
			$html[] = '		onclick="return jtrefnot(\\\''.$rowMainNotId.'\\\');"';
		}
		$html[] = '         title="'.JText::_('JT_NOTES').'" >';
		$html[] =           JText::_('JT_NOTES');
		$html[] = '      </a>';
		$html[] = '   </span>';
		$html[] = '   &nbsp;|&nbsp;';
		$html[] = '   <span class="jt-edit">';
		$html[] = '      <a	href="#" ';
		if ($indPHP) {
			$html[] = '		onclick="return jtrefnot(\''.$rowMainRefId.'\');"';
		} else {
			$html[] = '		onclick="return jtrefnot(\\\''.$rowMainRefId.'\\\');"';
		}
		$html[] = '         title="'.JText::_('JT_REFERENCES').'" >';
		$html[] =           JText::_('JT_REFERENCES');
		$html[] = '      </a>';
		$html[] = '   </span>';
		$html[] = '   &nbsp;|&nbsp;';
		$html[] = '   <span class="jt-edit">';
		$html[] = '      <a	href="#"';
		if ($indPHP) {
			$html[] = '      	onclick="remove_row(\''.$rowMainId.'\'); return false;"';
		} else {
			$html[] = '      	onclick="remove_row(\\\''.$rowMainId.'\\\'); return false;"';
		}
		$html[] = '      	title="'.JText::_('JT_DELETE_DESC').'"';
		$html[] = '      >';
		$html[] = 	     	JText::_('JT_DELETE');
		$html[] = '      </a>';
		$html[] = '   </span>';
		$html[] = '</td>';		
		$html[] = '</tr><!-- end row -->';
		//<!-- End: Row for one existing additional name / person event / relation event -->
		
		//<!-- Row with references for one additional name / person event / relation event -->
		$html_ref = self::getReferenceBlock( $indPHP			, $rowMainRefId		, $tabRefId
										   , $rowMainId			, $appId			, $type
										   , (($indPHP) ? $object->orderNumber : '\'+orderNumber+\'')
										   , $form				, $item				, $relationId
										   );
		$html = array_merge($html, $html_ref);
		//<!-- End: Row with references for one additional name / person event / relation event -->
		
		//<!-- Row with notes for one additional name / person event / relation event -->
		$html_ref = self::getNoteBlock( $indPHP			, $rowMainNotId		, $tabNotId
								   	  , $rowMainId		, $appId			, $type
								   	  , (($indPHP) ? $object->orderNumber : '\'+orderNumber+\'')
								   	  , $form			, $item				, $relationId
								   	  );
		$html = array_merge($html, $html_ref);
		//<!-- End: Row with notes for one additional name / person event / relation event -->
		
		return implode("\n", $html);
	}
	

	public function getRelationRow($object, $type, &$form) {
		$html = array();
		$rowMainId      = 're_'.$object->orderNumber;
		$rowclass		= 'jt-table-entry4';
		
		$html[] = '<tr id="'.$rowMainId.'" class="'.$rowclass.'" >';
		$html[] = $form->getInput('id', 'person.relations', $object->id );
		$html[] = $form->getInput('status', 'person.relations', $rowMainId.'!'.'loaded' );
		$html[] = $form->getInput('familyid', 'person.relations', $object->family_id );
		
		if ($type == 'children') {
			$html[] = $form->getInput('parentid', 'person.relations', $object->secondParent_id );
		}
		
		if ($type == 'parents') {
			$html[] = '<td>'.(($object->sex == 'M') ? JText::_('JT_FATHER') : (($object->sex == 'F') ? JText::_('JT_MOTHER') : null)).'</td>';
		} else if ($type == 'partners') {
			$html[] = '<td>'.(($object->sex == 'M') ? JText::_('JT_HUSBAND') : (($object->sex == 'F') ? JText::_('JT_WIFE') : JText::_('JT_PARTNER'))).'</td>';
		} 
		$html[] = '<td>'.$object->fullName.'</td>';
		$html[] = '<td>'.$object->birthDate.'</td>';
		$html[] = '<td>'.$object->deathDate.'</td>';
		if (($type == 'children') || ($type == 'parents')) {
			$html[] = '<td>'.$form->getInput('relationtype', 'person.relations', $object->relationtype ).'</td>';
		}
		if ($type == 'partners') {
			$html[] = '<td>'.$form->getInput('partnertype', 'person.relations', $object->relationtype ).'</td>';
		}
		$html[] = '<td>';
		$html[] = '   <span class="jt-edit">';
		$html[] = '      <a	href="#"';
		$html[] = '      	onclick="move_row(\''.$rowMainId.'\', \'up\'); return false;"';
		$html[] = '      	title="'.JText::_('JT_UP_DESC').'"';
		$html[] = '      >';
		$html[] = 	     	JText::_('JT_UP');
		$html[] = '      </a>';
		$html[] = '      &nbsp;|&nbsp;';
		$html[] = '      <a	href="#"';
		$html[] = '      	onclick="move_row(\''.$rowMainId.'\', \'down\'); return false;"';
		$html[] = '      	title="'.JText::_('JT_DOWN_DESC').'"';
		$html[] = '      >';
		$html[] = 	     	JText::_('JT_DOWN');
		$html[] = '      </a>';
		$html[] = '      &nbsp;|&nbsp;';
		$html[] = '      <a	href="#"';
		$html[] = '      	onclick="remove_row(\''.$rowMainId.'\'); return false;"';
		$html[] = '      	title="'.JText::_('JT_DELETE_DESC').'"';
		$html[] = '      >';
		$html[] = 	     	JText::_('JT_DELETE');
		$html[] = '      </a>';
		$html[] = '   </span>';
		$html[] = '</td>';		
		$html[] = '</tr><!-- end row -->';
		
		return implode("\n", $html);
	}
	
	public function getPictureRow(&$form, $picture, $docsFromGedcom) {
		$html = array();		
		$rowMainId      = 'rPic_'.$picture->orderNumber;
		$rowclass		= 'jt-table-entry4';
				
		$html[] = '<tr id="'.$rowMainId.'" class="'.$rowclass.'" >';
		$html[] = $form->getInput('id', 'person.media', $picture->id);
		$html[] = $form->getInput('status', 'person.media', $rowMainId.'!loaded' );
		
		// retrieve size of picture
		if (JFile::exists($picture->file)) {
			$imagedata   = GetImageSize($picture->file);
			$imageWidth  = $imagedata[0];
			$imageHeight = $imagedata[1];
			$maxpixels	 = ($imageWidth > $imageHeight) ? $imageWidth : $imageHeight;
			$factor		 = ($maxpixels > 300) ? 300/$maxpixels : 1;
			$showWidth 	 = (int) ($imageWidth * $factor);  //(100/$imageWidth) * $imageWidth * $factor;
			$showHeigth  = (int) ($imageHeight * $factor); //(100/$imageWidth) * $imageHeight * $factor;
			$html[] = '<td><img src="'.$picture->file.'" height="'.$showHeigth.'" width="'. $showWidth.'" /></td>';
		} else {
			$html[] = '<td>'.$picture->file.'</td>';
		}
		$html[] = '<td>'.$picture->title.'</td>';
		
		// show the file + path		
		$html[] = '<td>'.wordwrap($picture->file, 30, "<br />\n", true).'</td>';
		
		// Actions
		if ($docsFromGedcom) {
			$html[] = '<td>';
			$html[] = '   <span class="jt-edit">';
			$html[] = '      <a	href="#"';
			$html[] = '      	onclick="document.getElementById(\'picture\').value=\''
								.base64_encode(json_encode($picture))
								.'\';jtsubmitbutton(\'edit\');"';
			$html[] = '      	title="'.JText::_('JT_EDIT_DESC').'"';
			$html[] = '      >';
			$html[] = 	     	JText::_('JT_EDIT');
			$html[] = '      </a>';
			$html[] = '   </span>&nbsp;|&nbsp;';
			$html[] = '   <span class="jt-edit">';
			$html[] = '      <a	href="#"';
			$html[] = '      	onclick="remove_row(\''.$rowMainId.'\'); return false;"';
			$html[] = '      	title="'.JText::_('JT_DELETE_DESC').'"';
			$html[] = '      >';
			$html[] = 	     	JText::_('JT_DELETE');
			$html[] = '      </a>';
			$html[] = '   </span>';
			$html[] = '</td>';	
		}
			
		$html[] = '</tr><!-- end row -->';
		
		return implode("\n", $html);
	}

	private function getReferenceBlock( $indPHP		, $rowMainRefId	, $tabRefId	
									  , $rowId		, $appId		, $type
									  , $orderNumber, &$form		, &$item
									  , $relationId
									  ) {
		$html = array();
		
		$html[] = '<tr id="'.$rowMainRefId.'" class="jt-edit-2 jt-table-entry5" >';
		$html[] = '<td colspan="3">';
		$html[] = '<a href="#" onclick="return jtrefnot();" class="jt-btn-close"></a>';
	 	//        <!-- Additional name-references -->
	 	$html[] = '<table style="margin: 0;">';
	 	//			  <!-- header for additional name-references -->
		$html[] = '   <thead>';
		$html[] = '   <tr>';
		$html[] = '      <th class="jt-content-th">'.JText::_('JT_REFERENCES').'</th>';
		$html[] = '      <th class="jt-content-th">'.JText::_('JT_ACTIONS').'</th>';
		$html[] = '   </tr>';
		$html[] = '   </thead>';
		//			  <!-- header for references -->
		//			  <!-- table body for references -->
		$html[] = '   <tbody id="'.$tabRefId.'">';
		//				 <!-- Add row for new reference -->
		$html[] = '      <tr>';
		$html[] = '      <td style="padding: 2px 5px;">&nbsp;</td>';
		$html[] = '      <td style="padding: 2px 5px;">';
		$html[] = '         <span class="jt-edit">';
		$html[] = '            <a href="#" ';
		if ($indPHP) {
			$html[] = '        onclick="inject_refrow(\''.$tabRefId
									.'\', \''.$rowId
									.'\', \''.$appId
									.'\', \''.$type
									.'\', \''.$orderNumber
									.'\'); return false;"';
		} else {
			$html[] = '        onclick="inject_refrow(\\\''.$tabRefId
									.'\\\', \\\''.$rowId
									.'\\\', \\\''.$appId
									.'\\\', \\\''.$type
									.'\\\', \\\'\'+orderNumber+\''
									.'\\\'); return false;"';
		}
		$html[] = '               title="'.JText::_('JTADD_DESC').'" >';
		$html[] =                 JText::_('JTADD');
		$html[] = '            </a>';
		$html[] = '         </span>';
		$html[] = '      </td>';
		$html[] = '      </tr>';			
		//				 <!-- End: Add row for new reference -->
		if ($indPHP) {
			//		     <!-- List of existing references -->
			switch ($type) {
			 	case "personName":		$sourceType = 'name';
			 							break;
			 	case "personNote":		$sourceType = 'note';
			 							break;
			 	case "personEvent":		$sourceType = 'pevent';
			 							break;
			 	case "relationEvent":	$sourceType = 'revent';
			 							break;
			 	default:		  		$sourceType = 'pevent';
			 							break;
			}
			$refs = $item->getSources($sourceType, $orderNumber, $relationId);
			if (count($refs)) {
				foreach ($refs as $ref) {
					$rowRefId	= $rowId.'_r_'.$ref->orderNumber;
					$html[] = '         <tr id="'.$rowRefId.'" >';
					$html[] = 				self::getReferenceRow($indPHP, $form, $ref, $rowRefId);
					$html[] = '         </tr>';
				}
			}		  								
			//			<!-- End: List of existing references -->
		}
		$html[] = '   </tbody>';
		//			  <!-- End: table body for references -->
	 	$html[] = '</table>';
		//		  <!-- End: References -->
		$html[] = '</td>';		
		$html[] = '</tr><!-- end row -->';
		
		return $html;
	}
	
	
	public function getReferenceRow($indPHP, &$form, $ref, $rowRefId = null, $appId = null) {
		$html = array();
		
		// setup counter
		if ($indPHP) {
			$form->setValue('counter', null, $ref->orderNumber);
			$html[] = $form->getInput('counter', null, $ref->orderNumber);
		}
		
		$html[] = $form->getInput('objectType', 'person.references', (($indPHP) ? $ref->objectType : '\'+obj_type+\''));
		$html[] = $form->getInput('objectOrderNumber', 'person.references', (($indPHP) ? $ref->objectOrderNumber : '\'+obj_number+\''));
		$html[] = $form->getInput('orderNumber', 'person.references', (($indPHP) ? $ref->orderNumber : '\'+orderNumber+\''));
		$html[] = $form->getInput('status', 'person.references', (($indPHP) ? $rowRefId : '\'+rowref+\'').'!'.(($indPHP) ? 'loaded' : 'new'));		
		$html[] = '<td>';
		$html[] = '   <ul class="joaktreeformlist">';
		$html[] = '      <li>';
		$html[] =           $form->getLabel('app_source_id', 'person.references');
		$html[] = 			$form->getInput('app_source_id', 'person.references', (($indPHP) ? $ref->app_id.'!'.$ref->source_id : $appId));
		$html[] = '      </li>';
		$html[] = '      <li>';
		$html[] =           $form->getLabel('page', 'person.references');
		$html[] = 			$form->getInput('page', 'person.references', (($indPHP) 
																			? htmlspecialchars_decode($ref->page, ENT_QUOTES) 
																			: null
																			));
		$html[] = '      </li>';
		$html[] = '      <li>';
		$html[] =           $form->getLabel('quotation', 'person.references');
		$html[] = 			$form->getInput('quotation', 'person.references', (($indPHP) 
																				? htmlspecialchars_decode($ref->quotation, ENT_QUOTES) 
																				: null
																				));
		$html[] = '      </li>';
		$html[] = '      <li>';
		$html[] =           $form->getLabel('note', 'person.references');
		$html[] = 			$form->getInput('note', 'person.references', (($indPHP) 
																			? htmlspecialchars_decode($ref->note, ENT_QUOTES) 
																			: null
																			));
		$html[] = '      </li>';
		$html[] = '      <li>';
		$html[] =           $form->getLabel('dataQuality', 'person.references');
		$html[] = 			$form->getInput('dataQuality', 'person.references', (($indPHP) ? $ref->dataQuality : null));
		$html[] = '      </li>';
		$html[] = '   </ul>';
		$html[] = '</td>';		
		$html[] = '<td>';
		$html[] = '   <span class="jt-edit">';
		$html[] = '      <a	href="#"';
		$html[] = '      	onclick="remove_row(\''.(($indPHP) ? $rowRefId : '+\'\\\'\'+rowref+\'\\\'\'+').'\'); return false;"';
		$html[] = '      	title="'.JText::_('JT_DELETE_DESC').'"';
		$html[] = '      >';
		$html[] = 	     	JText::_('JT_DELETE');
		$html[] = '      </a>';
		$html[] = '   </span>';
		$html[] = '</td>';		
		
		return implode("\n", $html);
	}

	private function getNoteBlock( $indPHP		, $rowMainNotId	, $tabNotId	
								 , $rowId		, $appId		, $type
							  	 , $orderNumber	, &$form		, &$item
							  	 , $relationId
								 ) {
		$html = array();
		
		$html[] = '<tr id="'.$rowMainNotId.'" class="jt-edit-2 jt-table-entry5" >';
		$html[] = '<td colspan="3">';
		$html[] = '<a href="#" onclick="return jtrefnot();" class="jt-btn-close"></a>';
		//		  <!-- notes -->
		$html[] = '<table style="margin: 0;">';
		//		      <!-- header for notes -->
		$html[] = '   <thead>';
		$html[] = '   <tr>';
		$html[] = '      <th class="jt-content-th">'.JText::_('JT_NOTES').'</th>';
		$html[] = '      <th class="jt-content-th">'.JText::_('JT_ACTIONS').'</th>';
		$html[] = '   </tr>';
		$html[] = '   </thead>';
		//			  <!-- header for notes -->
		//			  <!-- table body for notes -->
		$html[] = '   <tbody id="'.$tabNotId.'">';
		//				 <!-- Add row for new note -->
		$html[] = '      <tr>';
		$html[] = '      <td style="padding: 2px 5px;">&nbsp;</td>';
		$html[] = '      <td style="padding: 2px 5px;">';
		$html[] = '         <span class="jt-edit">';
		$html[] = '            <a href="#"';
		if ($indPHP) {
			$html[] = '        onclick="inject_notrow(\''.$tabNotId
									.'\', \''.$rowId
									.'\', \''.$appId
									.'\', \'personName'
									.'\', \''.$orderNumber
									.'\'); return false;"';
		} else {
			$html[] = '        onclick="inject_notrow(\\\''.$tabNotId
									.'\\\', \\\''.$rowId
									.'\\\', \\\''.$appId
									.'\\\', \\\'personName'
									.'\\\', \\\'\'+orderNumber+\''
									.'\\\'); return false;"';
		}
		$html[] = '               title="'.JText::_('JTADD_DESC').'" >';
		$html[] =                 JText::_('JTADD');
		$html[] = '            </a>';
		$html[] = '         </span>';
		$html[] = '      </td>';
		$html[] = '      </tr>';			
		//				 <!-- End: Add row for new note -->
		if ($indPHP) {
			//		     <!-- List of existing notes -->
			switch ($type) {
			 	case "personName":		$sourceType = 'name';
			 							break;
			 	case "personEvent":		$sourceType = 'pevent';
			 							break;
			 	case "relationEvent":	$sourceType = 'revent';
			 							break;
			 	default:		  		$sourceType = 'pevent';
			 							break;
			}
			
			$nots = $item->getNotes($sourceType, $orderNumber, $relationId);
			if (count($nots)) {
				foreach ($nots as $not) {
					$rowNotId	= $rowId.'_n_'.$not->orderNumber;
					$html[] 	= self::getNoteRow($indPHP, false, $form, $not, $rowNotId, null, $item);
				}
			}		  								
			//			<!-- End: List of existing notes -->
		}
		$html[] = '   </tbody>';
		//			  <!-- End: table body for notes -->
	 	$html[] = '</table>';
		//		  <!-- End: Notes -->
		$html[] = '</td>';		
		$html[] = '</tr><!-- end row -->';
		
		return $html;
	}
	
	public function getNoteRow($indPHP, $indRef, &$form, $not, $rowNotId = null, $appId = null, $item) {
		static $_eol;

		if (!isset($_eol)) {
			$document	= &JFactory::getDocument();
			$_eol 		= $document->_getLineEnd(); 
		}
		
		$html = array();
		$rowNoteRefId	= ($indPHP) ? $rowNotId.'_ref' : 'rN_\'+orderNumber+\'_ref';
		
		if ($indPHP) {
			$html[] = '<tr id="'.$rowNotId.'" class="jt-table-entry4">';	
			$noteText = htmlspecialchars_decode(str_replace("&#10;&#13;", $_eol, $not->text), ENT_QUOTES);
		} else {
			$html[] = '<tr id="\'+rownot+\'" class="jt-table-entry3">';
			$noteText = null;
		}
			
		$html[] = $form->getInput('note_id', 'person.notes', (($indPHP) ? $not->note_id : null) );
		$html[] = $form->getInput('objectOrderNumber', 'person.notes', (($indPHP) ? $not->objectOrderNumber : '\'+obj_number+\''));
		$html[] = $form->getInput('orderNumber', 'person.notes', (($indPHP) ? $not->orderNumber : '\'+orderNumber+\''));
		$html[] = $form->getInput('status', 'person.notes', (($indPHP) ? $rowNotId : '\'+rownot+\'').'!'.(($indPHP) ? 'loaded' : 'new'));
		$html[] = '<td>';
		$html[] = '   <ul class="joaktreeformlist">';
		$html[] = '      <li>';
		$html[] = 			$form->getInput('text', 'person.notes', $noteText);
		$html[] = '      </li>';
		$html[] = '   </ul>';
		$html[] = '</td>';		
		$html[] = '<td>';
		
		if ($indRef) {
			$html[] = '   <span class="jt-edit">';
			$html[] = '      <a	href="#" ';
			
			if ($indPHP) {
				$html[] = '		onclick="return jtrefnot(\''.$rowNoteRefId.'\');"';
			} else {
				$html[] = '		onclick="return jtrefnot(\\\''.$rowNoteRefId.'\\\');"';
			}
			
			$html[] = '         title="'.JText::_('JT_REFERENCES').'" >';
			$html[] =           JText::_('JT_REFERENCES');
			$html[] = '      </a>';
			$html[] = '   </span>';
			$html[] = '   &nbsp;|&nbsp;';
		}
		
		$html[] = '   <span class="jt-edit">';
		$html[] = '      <a	href="#"';
		$html[] = '      	onclick="remove_row(\''.(($indPHP) ? $rowNotId : '+\'\\\'\'+rownot+\'\\\'\'+').'\'); return false;"';
		$html[] = '      	title="'.JText::_('JT_DELETE_DESC').'"';
		$html[] = '      >';
		$html[] = 	     	JText::_('JT_DELETE');
		$html[] = '      </a>';
		$html[] = '   </span>';
		$html[] = '</td>';
		$html[] = '</tr><!-- end row -->';
		
		if ($indRef) {
			$tabRefId       = ($indPHP) ? 'tbref_'.$not->orderNumber : 'tbref_\'+orderNumber+\'';
			
			//<!-- Row with references for one additional name -->
			$html_ref = self::getReferenceBlock( $indPHP			, $rowNoteRefId	, $tabRefId
											   , $rowNotId			, $appId		, 'personNote'
											   , (($indPHP) ? $not->orderNumber : '\'+orderNumber+\'')	
											   , $form				, $item			, null
											   );
			$html = array_merge($html, $html_ref);
			//<!-- End: Row with references for one additional name -->
		}
		
		return implode("\n", $html);
	}
	
	public function getNameEventRowScript($type, &$form, $appId) {
		$script = array();

		// function for adding new row
		$script[] = "function inject_namevtrow(table_body, appid){";
		
		$script[] = "   var htmlToElements = function(str){";
		$script[] = "      return new Element('div', {html: '<table><tbody>' + str + '</tbody></table>'}).getElement('tr');";
		$script[] = "   }"; 
		
		$script[] = "   var el = document.getElementById(table_body);";
		$script[] = "   var orderNumber = parseInt(document.getElementById('namevtcounter').value) + 1;";
		$script[] = "   document.getElementById('namevtcounter').value = orderNumber;";
		$script[] = "   var rownam = 'rN_' + orderNumber;";
		
		// create a table row as a string
		$script[] = "   var row_str = '';";
		$script[] = "   var newRow  = '';";
		
		$tmp   = self::getNameEventRow(false, $type, $form, null, null, $appId, null);
		$htmls = explode("\n", $tmp);
		
		foreach ($htmls as $html) {
			$script[] = "   row_str += '".$html."';";
			
			if (strpos($html, '<!-- end row -->')) {			
				// convert string to table wrapped in a div element
				$script[] = "	newRow = htmlToElements( row_str );";
				// inject the new row into the table body 
				$script[] = "	newRow.inject( el );";
				$script[] = "   row_str = '';";
			}
		}
		
		$script[] = "}";
		$script[] = " ";

		// function for switching between date types
		$script[] = "function switch_datetype(orderNumber){";
		$script[] = "   var nwType =  document.getElementById('select_datetype_'+orderNumber).value; ";
		$script[] = "   var elL1   =  document.getElementById('ed_l1_'+orderNumber); ";  // extended only
		$script[] = "   var elD1   =  document.getElementById('ed_d1_'+orderNumber); ";  // simple + extended
		$script[] = "   var elD2   =  document.getElementById('ed_d2_'+orderNumber); ";  // extended only
		$script[] = "   var elDesc =  document.getElementById('ed_desc_'+orderNumber); ";// description only
		
		$script[] = "   if (nwType == 'simple') { ";
		$script[] = "      if(isClassInElement(elL1,   'jt-show')) swapClassInElement(elL1,'jt-show','jt-hide'); ";
		$script[] = "      if(isClassInElement(elD2,   'jt-show')) swapClassInElement(elD2,'jt-show','jt-hide'); ";
		$script[] = "      if(isClassInElement(elDesc, 'jt-show')) swapClassInElement(elDesc,'jt-show','jt-hide'); ";
		$script[] = "      if(isClassInElement(elD1,   'jt-hide')) swapClassInElement(elD1,'jt-hide','jt-show'); ";
		$script[] = "   } else if (nwType == 'extended') { ";
		$script[] = "      if(isClassInElement(elDesc, 'jt-show')) swapClassInElement(elDesc,'jt-show','jt-hide'); ";
		$script[] = "      if(isClassInElement(elL1,   'jt-hide')) swapClassInElement(elL1,'jt-hide','jt-show'); ";
		$script[] = "      if(isClassInElement(elD1,   'jt-hide')) swapClassInElement(elD1,'jt-hide','jt-show'); ";
		$script[] = "      if(isClassInElement(elD2,   'jt-hide')) swapClassInElement(elD2,'jt-hide','jt-show'); ";
		$script[] = "   } else if (nwType == 'description') { ";
		$script[] = "      if(isClassInElement(elL1,   'jt-show')) swapClassInElement(elL1,'jt-show','jt-hide'); ";
		$script[] = "      if(isClassInElement(elD1,   'jt-show')) swapClassInElement(elD1,'jt-show','jt-hide'); ";
		$script[] = "      if(isClassInElement(elD2,   'jt-show')) swapClassInElement(elD2,'jt-show','jt-hide'); ";
		$script[] = "      if(isClassInElement(elDesc, 'jt-hide')) swapClassInElement(elDesc,'jt-hide','jt-show'); ";
		$script[] = "   } "; 
		
		$script[] = "}";
		$script[] = " ";
		
		
		return implode("\n", $script);	
	}
		
	public function getReferenceRowScript(&$form, $appId) {
		$script = array();
		
		$script[] = "function jtSelectSource(idid, titleid, id, title) {";
		$script[] = "		var old_id = document.getElementById(idid).value;";
		$script[] = "		if (old_id != id) {";
		$script[] = "			document.getElementById(idid).value = id;";
		$script[] = "			document.getElementById(titleid).value = title;";
		$script[] = "		}";
		$script[] = "		SqueezeBox.close();";
		$script[] = "	}";

		$script[] = "function inject_refrow(table_body, table_row, appid, obj_type, obj_number){";
		
		$script[] = "   var htmlToElements = function(str){";
		$script[] = "      return new Element('div', {html: '<table><tbody>' + str + '</tbody></table>'}).getElement('tr');";
		$script[] = "   }"; 
		
		$script[] = "   var el = document.getElementById(table_body);";
		$script[] = "   var orderNumber = parseInt(document.getElementById('refcounter').value) + 1;";
		$script[] = "   document.getElementById('refcounter').value = orderNumber;";
		$script[] = "   var rowref = table_row + '_r_' + orderNumber;";
		
		// create a table row as a string
		$script[] = "   var row_str = '<tr id=\"'+rowref+'\" class=\"jt-table-entry3\">';";
		$tmp   = self::getReferenceRow(false, $form, null, null, $appId);
		$htmls = explode("\n", $tmp);
		
		foreach ($htmls as $html) {
			$script[] = "   row_str += '".$html."';";
		}
		$script[] = "	row_str += '</tr>';";
		
		// convert string to table wrapped in a div element
		$script[] = "	var newRow = htmlToElements( row_str );";
		// inject the new row into the table body 
		$script[] = "	newRow.inject( el );";
		
		// setup modal
		$script[] = "   SqueezeBox.assign($$('a.modal_src_'+orderNumber), { parse: 'rel' });";
		$script[] = "   var url=document.getElementById('modalid_'+orderNumber).href;";
		if (JoaktreeHelper::getSEF() == 1) {
			$script[] = "   document.getElementById('modalid_'+orderNumber).href = url+'?counter='+orderNumber;";
		} else {
			$script[] = "   document.getElementById('modalid_'+orderNumber).href = url+'&counter='+orderNumber;";
		}
		
		$script[] = "}";
		$script[] = " ";

		return implode("\n", $script);	
	}
		
	public function getNoteRowScript($indRef, &$form, $appId) {
		$script = array();
		
		$script[] = "function inject_notrow(table_body, table_row, appid, obj_type, obj_number){";
		
		$script[] = "   var htmlToElements = function(str){";
		$script[] = "      return new Element('div', {html: '<table><tbody>' + str + '</tbody></table>'}).getElement('tr');";
		$script[] = "   }"; 
		
		$script[] = "   var el = document.getElementById(table_body);";
		$script[] = "   var orderNumber = parseInt(document.getElementById('notcounter').value) + 1;";
		$script[] = "   document.getElementById('notcounter').value = orderNumber;";
		$script[] = "   var rownot = table_row + '_n_' + orderNumber;";
		
		// create a table row as a string
		//$script[] = "   var row_str = '<tr id=\"'+rownot+'\" class=\"jt-table-entry3\">';";
		$script[] = "   var row_str = '';";
		$tmp   = self::getNoteRow(false, $indRef, $form, null, null, $appId, null);
		$htmls = explode("\n", $tmp);
		
		foreach ($htmls as $html) {
			$script[] = "   row_str += '".$html."';";
			
			if (strpos($html, '<!-- end row -->')) {			
				// convert string to table wrapped in a div element
				$script[] = "	newRow = htmlToElements( row_str );";
				// inject the new row into the table body 
				$script[] = "	newRow.inject( el );";
				$script[] = "   row_str = '';";
			}
		}
				
		$script[] = "}";
		$script[] = " ";
		
		return implode("\n", $script);	
	}

	public function getGeneralRowScript() {
		$script = array();
		$script[] = " ";
		$script[] = "function remove_row(table_row){";
		$script[] = "   var row, stat, i, el, elements; ";
		
		// set status
		$script[] = "   stat = document.getElementById('stat_' + table_row).value;";
		$script[] = "   document.getElementById('stat_' + table_row).value = stat+'_deleted';";
		
		// remove main row by hiding it + setting all input element: not required
		$script[] = "   row = document.getElementById(table_row);";
		$script[] = "   row.set('class', 'jt-hide');";
		$script[] = "   elements = row.getElements('input');";
		$script[] = "   for (i=0; i < elements.length; i++ ) {";
		$script[] = "      el = $(elements[i]);";
		$script[] = "      if (el.hasClass('required')) {";
		$script[] = "         el.removeClass('required');";
		$script[] = "      }";
		$script[] = "   }";
		
		// remove ref row by hiding it + setting all input element: not required
		$script[] = "   row = document.getElementById(table_row+'_ref');";
		$script[] = "   if (row != null) {";
		$script[] = "      row.set('class', 'jt-hide');";
		$script[] = "      elements = row.getElements('input');";
		$script[] = "      for (i=0; i < elements.length; i++ ) {";
		$script[] = "         el = $(elements[i]);";
		$script[] = "         if (el.hasClass('required')) {";
		$script[] = "            el.removeClass('required');";
		$script[] = "         }";
		$script[] = "      }";
		$script[] = "   }";
		
		// remove not row by hiding it + setting all input element: not required
		$script[] = "   row = document.getElementById(table_row+'_not');";
		$script[] = "   if (row != null) {";
		$script[] = "      row.set('class', 'jt-hide');";
		$script[] = "      elements = row.getElements('input');";
		$script[] = "      for (i=0; i < elements.length; i++ ) {";
		$script[] = "         el = $(elements[i]);";
		$script[] = "         if (el.hasClass('required')) {";
		$script[] = "            el.removeClass('required');";
		$script[] = "         }";
		$script[] = "      }";
		$script[] = "   }";
		
		$script[] = "}";
		$script[] = " ";
		
		$script[] = " ";
		$script[] = "function move_row(table_row, direction){";
		$script[] = "   var clicked = document.getElementById(table_row);";
		$script[] = "   var table   = clicked.parentNode;";
		$script[] = "   var clickedIndex = clicked.rowIndex;";
		$script[] = "   var maxrindex = table.getElementsByTagName('tr').length;";
		
		$script[] = "   if(clickedIndex == '1' && direction=='up') {";
		$script[] = "      alert('".JText::_('JT_UPPERROW_MESSAGE')."'); return false; }";
		
		$script[] = "   if(clickedIndex == maxrindex && direction=='down') {";
		$script[] = "      alert('".JText::_('JT_LOWERROW_MESSAGE')."'); return false; }";
		
		$script[] = "   if (direction=='up')   { adjacentIndex = clickedIndex - 1; }";
		$script[] = "   if (direction=='down') { adjacentIndex = clickedIndex + 1; }";
		$script[] = "   var adjacnt = table.getElementsByTagName('tr')[adjacentIndex-1];";
		
		//Once that we have established references to both the rows that should change their position, we should clone each of them
		$script[] = "   click_clone = clicked.cloneNode(true);";
		$script[] = "   adjac_clone = adjacnt.cloneNode(true);";
		//both the cloned nodes remain ‘invisible’ to the user.
		
		//now replace the nodes.
		//The below replaceChild() function will replace the adjacentrow with ‘clone of the clicked row’ and then remove the clone on the clicked row.
		$script[] = "   table.replaceChild(adjac_clone, clicked);";
		$script[] = "   table.replaceChild(click_clone, adjacnt);";
		// the clones of two rows that we made above were automatically removed by the replaceChild() function.
		
		$script[] = "}";
		$script[] = " ";
		
		return implode("\n", $script);	
	}
	
	public function getSubmitScript($personName) {
		$script = array();
		$script[] = " ";
		$script[] = "<script type=\"text/javascript\"> ";
		$script[] = "function jtsetrelation(id) { ";
		$script[] = "   document.getElementById('jform_person_relation_id').value = id; "; 
		$script[] = "} ";								
		$script[] = " ";
		$script[] = "function jtsubmitbutton(task, object) { ";
		$script[] = "   f = document.getElementById('joaktreeForm'); ";
		$script[] = "   f.object.value = object;  ";
		$script[] = "   if (task == 'delete') { ";
		$script[] = "      if (confirm(\"".JText::sprintf('JT_CONFIRMDELETE_PERSON', $personName)."\")) { ";
		$script[] = "         Joomla.submitform(task, f); } ";
		$script[] = "   } else { Joomla.submitform(task, f); } ";
		$script[] = "} ";								
		$script[] = "</script> ";
		$script[] = " ";
		
		return implode("\n", $script);	
	}
	
	public function getButtons($counter, $but = array( 'save' => true
													 , 'cancel' => true
													 , 'check' => false
													 , 'done' => false
													 , 'add' => false
													 )
							  , $indParent1 = false
							  ) {
		$html = array();
		
		if ($counter == 1) {
			$html[] = '<div class="jt-buttonbar" style="margin-left: 10px;">';
		} else {
			$html[] = '<div class="jt-buttonbar" style="margin-left: 10px; margin-top: 10px;">';
		}
		
		if (($but['save']) && (!$but['check'])) {			
			$html[] = '	<a 	href="#" ';
			$html[] = '		id="save'.$counter.'"';
			$html[] = '		class="jt-button-closed jt-buttonlabel"'; 
			$html[] = '		title="'.JText::_('JSAVE').'" ';
			$html[] = '		onclick="jtsubmitbutton(\'save\');"';
			$html[] = '	>';
			$html[] =       JText::_('JSAVE');
			$html[] = '	</a>';
			$html[] = '&nbsp;';
		}
		
		if (($but['save']) && ($but['check'])) {			
			$html[] = '	<a 	href="#" ';
			$html[] = '		id="save'.$counter.'"';
			$html[] = '		class="modal_check jt-button-closed jt-buttonlabel" ';
			$html[] = '		title="'.JText::_('JSAVE').'" ';
			$html[] = '     rel="{handler: \'iframe\', size: {x: 800, y: 500}}" ';
			$html[] = '	>';
			$html[] =       JText::_('JSAVE');
			$html[] = '	</a>';
			$html[] = '&nbsp;';
		}
		
		if ($but['done']) {
			$html[] = '	<a 	href="#" ';
			$html[] = '		id="done'.$counter.'"';
			$html[] = '		class="jt-button-closed jt-buttonlabel" ';
			$html[] = '		title="'.JText::_('JT_DONE').'" ';
			$html[] = '		onclick="jtsubmitbutton(\'cancel\');"';
			$html[] = '	>';
			$html[] =       JText::_('JT_DONE');
			$html[] = '	</a>';										
			$html[] = '	&nbsp;';
		}	
				
		if ($but['add']) {
			$html[] = '	<a 	href="#" ';
			$html[] = '		id="add'.$counter.'"';
			$html[] = '		class="jt-button-closed jt-buttonlabel" ';
			$html[] = '		title="'.JText::_('JTADD_DESC').'" ';
			$html[] = '		onclick="document.getElementById(\'mediaForm\').object.value=\'media\'; jtsubmitbutton(\'edit\');"';
			$html[] = '	>';
			$html[] =       JText::_('JTADD');
			$html[] = '	</a>';										
			$html[] = '	&nbsp;';
		}	
				
		if ($but['cancel']) {
			$html[] = '	<a 	href="#" ';
			$html[] = '		id="cancel'.$counter.'"';
			$html[] = '		class="jt-button-closed jt-buttonlabel" ';
			$html[] = '		title="'.JText::_('JCANCEL').'" ';
			$html[] = '		onclick="jtsubmitbutton(\'cancel\');"';
			$html[] = '	>';
			$html[] =       JText::_('JCANCEL');
			$html[] = '	</a>';										
			$html[] = '	&nbsp;';
		}			
		
		if (($but['check']) && ($counter == 1)) {
			$params	= JoaktreeHelper::getJTParams(true);
			$patronym = $params->get('patronym', 0);
			$router = JSite::getRouter();
			
			$link1  = 'index.php?option=com_joaktree'
					 .'&amp;view=joaktreelist'
					 .'&amp;layout=check'
					 .'&amp;tmpl=component'
					 .'&amp;treeId='.JoaktreeHelper::getTreeId()
					 .'&amp;action=select';
					
			$link2  = 'index.php?option=com_joaktree'
					 .'&amp;view=joaktreelist'
					 .'&amp;layout=check'
					 .'&amp;tmpl=component'
					 .'&amp;treeId='.JoaktreeHelper::getTreeId()
					 .'&amp;action='.(($indParent1) ?  'saveparent1' : 'save');
					 
//			$relId = JoaktreeHelper::getRelationId();
//			if ($relId) {
//				$link1 .= '&amp;relationId='.$relId;
//				$link2 .= '&amp;relationId='.$relId;
//			}
			
			
			// Build the script.
			$script = array();
			$script[] = 'function setCheckValue() {';
			$script[] = '   var link1 = "'.JRoute::_($link1).'";'; 
			$script[] = '   var link2 = "'.JRoute::_($link2).'";';
			$script[] = '   var link  = "";'; 
			$script[] = '	var search1 = document.getElementById("jform_person_firstName").value;';
			if ($patronym) {
				$script[] = '	var search2 = document.getElementById("jform_person_patronym").value;';
			}
			$script[] = '	var search3 = document.getElementById("jform_person_rawFamilyName").value;';
						
			if ($patronym) {
				$script[] = '   if ((search1 == "") && (search2 == "") && (search3 == "")) {';
				$script[] = '       link1 = "#"; link2 = "#";' ;
				$script[] = '   } else {'; 
				if ($router->getMode() == JROUTER_MODE_SEF) {
					$script[] = '    if (search1 != "") { link = link + "/f-" + search1; } ';
					$script[] = '    if (search2 != "") { link = link + "/s-" + search2; } ';
					$script[] = '    if (search3 != "") { link = link + "/n-" + search3; } ';
				} else {
					$script[] = '    if (search1 != "") { link = link + "&amp;search1=" + search1; } ';
					$script[] = '    if (search2 != "") { link = link + "&amp;search2=" + search2; } ';
					$script[] = '    if (search3 != "") { link = link + "&amp;search3=" + search3; } ';
				}
				$script[] = '   }';
			} else {
				// no search2 (= patronym)
				$script[] = '   if ((search1 == "") && (search3 == "")) {';
				$script[] = '       link1 = "#"; link2 = "#";' ;
				$script[] = '   } else {';     
				if ($router->getMode() == JROUTER_MODE_SEF) {
					$script[] = '    if (search1 != "") { link = link + "/f-" + search1; } ';
					$script[] = '    if (search3 != "") { link = link + "/n-" + search3; } ';
				} else {
					$script[] = '    if (search1 != "") { link = link + "&amp;search1=" + search1; } ';
					$script[] = '    if (search3 != "") { link = link + "&amp;search3=" + search3; } ';
				}
				$script[] = '   }';
			}
			
			$script[] = '   document.getElementById("check1").setProperty("href", link1 + link);';
			$script[] = '   document.getElementById("save1").setProperty("href",  link2 + link);';
			$script[] = '   document.getElementById("save2").setProperty("href",  link2 + link);';
			$script[] = '}';
			
			$script[] = 'function jtNewPerson() {';
			$script[] = '   SqueezeBox.close();';
			$script[] = '	document.getElementById("newstatus").value="checked";';
			$script[] = '   var el1 = document.getElementById("save1");';
			$script[] = '   el1.setProperty("href",  "'.Jroute::_('index.php?option=com_joaktree&amp;view=close').'");';
			$script[] = '   el1.removeProperty("rel");';
			$script[] = '   el1.setProperty("onclick", "jtsubmitbutton(\'save\');");';
			$script[] = '   var el2 = document.getElementById("save2");';
			$script[] = '   el2.setProperty("href",  "'.Jroute::_('index.php?option=com_joaktree&amp;view=close').'");';
			$script[] = '   el2.removeProperty("rel");';
			$script[] = '   el2.setProperty("onclick", "jtsubmitbutton(\'save\');");';
			$script[] = '}';
			if ($indParent1) {
				$script[] = 'function jtSelectPerson(appId, personId, relationId, familyId) {';
				$script[] = '   SqueezeBox.close();'; 
				$script[] = '   var fam = new Element("option", {value: relationId + "!" + familyId}); ';
				$script[] = '   fam.inject(document.getElementById("jform_person_relations_family"));';
				$script[] = '   document.getElementById("jform_person_relations_family").value = relationId + "!" + familyId;';
				$script[] = '   document.getElementById("jform_person_id").value = personId;';
				$script[] = '   document.getElementById("jform_person_status").value = "relation";';
				$script[] = '	jtsubmitbutton("select");';
				$script[] = '}'; 
			} else {
				$script[] = 'function jtSelectPerson(appId, personId) {';
				$script[] = '   SqueezeBox.close();';
				$script[] = '   document.getElementById("jform_person_id").value = personId;';
				$script[] = '   document.getElementById("jform_person_status").value = "relation";';
				$script[] = '	jtsubmitbutton("select");';
				$script[] = '}';
			}
			$script[] = 'function jtSavePerson() {';
			$script[] = '   SqueezeBox.close();';
			$script[] = '	jtsubmitbutton("save");';
			$script[] = '}';
			// Add the script to the document head.
			JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));		
			
			// Load the modal behavior script.
			$html[] = JHtml::_('behavior.modal', 'a.modal_check');
			$html[] = '<input type="hidden" id="newstatus" value="unchecked" />';
			
			$html[] = '<span id="cp_label" class="jt-edit-2">';
			$html[] = '	<a 	href="#" ';
			$html[] = '		id="check'.$counter.'"';
			$html[] = '		class="modal_check jt-button-closed jt-buttonlabel" ';
			$html[] = '		title="'.JText::_('JT_CHECK').'" ';
			$html[] = '     rel="{handler: \'iframe\', size: {x: 800, y: 500}}" ';
			$html[] = '	>';
			$html[] =       JText::_('JT_CHECK');
			$html[] = '	</a>';	
			$html[] = '</span>';									
			$html[] = '&nbsp;';
		}			
				
		$html[] = '</div>';
		$html[] = '<div class="clearfix"></div>';
		
		return implode("\n", $html);	
	}
	
	private function getEventDateHTML($eventDateString, $counter, $formRecord, &$form) {		
		// initialize
		$html = array();
		$ed = array();		
		$ed['type'] 		= 'simple';
		$ed['fullString'] 	= $eventDateString;
		$ed['Label1']		= null;
		$ed['Label2']		= null;
		$ed['M1']			= null;
		$ed['D1']			= null;
		$ed['Y1']			= null;
		$ed['M2']			= null;
		$ed['D2']			= null;
		$ed['Y2']			= null;
		
		// evaluate string
		$elements = explode(' ', $eventDateString);
		foreach ($elements as $element) {
			switch ($element) {
				case "ABT"		:	// continute
				case "ABOUT"	:	$ed['Label1'] = 'ABT';
									$ed['type']   = 'extended';
									break;
				case "BEF"		: 	// continue
				case "BEFORE"	: 	$ed['Label1'] = 'BEF'; 
									$ed['type']   = 'extended';
									break;
				case "AFT"		:	// continute
				case "AFTER"	:	$ed['Label1'] = 'AFT';
									$ed['type']   = 'extended';
									break;
				case "BET"		:	// continute
				case "BETWEEN"	:	$ed['Label1'] = 'BET';
									$ed['type']   = 'extended';
									break;
				case "FROM"		:	$ed['Label1'] = 'FROM';
									$ed['type']   = 'extended';
									break;
				case "AND"		:	$ed['Label2'] = 'AND';
									$ed['type']   = 'extended';
									break;
				case "TO"		:	$ed['Label2'] = 'TO';
									$ed['type']   = 'extended';
									break;
				case "JAN"		:	// continue
				case "FEB"		:	// continue
				case "MAR"		:	// continue
				case "APR"		:	// continue
				case "MAY"		:	// continue
				case "JUN"		:	// continue			
				case "JUL"		:	// continue
				case "AUG"		:	// continue
				case "SEP"		:	// continue
				case "OCT"		:	// continue
				case "NOV"		:	// continue
				case "DEC"		:	if (!isset($ed['M1'])) { 
										$ed['M1'] = $element; 
									} else { 
										$ed['M2'] = $element;
										$ed['type']   = 'extended';
									}
									break;	
				case ""			:	// empty -> just continue with the next element
									break;
				default			:   // check whether this is a day or a year
									$tmp = (int) $element;
									if (   ($tmp >= 0) 
									   and ($tmp <= 31) 
									   and ($element == (string) $tmp)
									   ) {
										// This is a day
										if (!isset($ed['D1'])) { 
											$ed['D1'] = $element;  
										} else { 
											$ed['D2']	= $element;
											$ed['type']	= 'extended';
										}
									 } else if (   ($tmp > 900) 
									   		   and ($tmp < 10000) 
									   		   and ($element == (string) $tmp)
									   		   ) {
										// This is a year
										if (!isset($ed['Y1'])) { 
											$ed['Y1'] = $element;  
										} else { 
											$ed['Y2']	= $element;
											$ed['type']	= 'extended';
										}
									} else {
										$ed['type']	= 'description';					
									}
					
									break;	
			}
			
		}
		
		// setup classes
		switch ($ed['type']) {
			case "simple"		:	$classSimpleExtended	= 'jt-show';
									$classExtended			= 'jt-hide';
									$classDescription		= 'jt-hide';
									break;
			case "extended"		:	$classSimpleExtended	= 'jt-show';
									$classExtended			= 'jt-show';
									$classDescription		= 'jt-hide';									
									break;
			case "description"	:	// continue
			default:				$classSimpleExtended	= 'jt-hide';
									$classExtended			= 'jt-hide';
									$classDescription		= 'jt-show';		
									break;
		}
		
		// start with the fields -> all situations
		$html[] = $form->getLabel('eventDateType', $formRecord);
		$html[] = $form->getInput('eventDateType', $formRecord, $ed['type'].'!'.$counter);		
		$html[] = '<label for="jform_person_events_ed" id="jform_person_events_ed-lbl">&nbsp;</label>';
		
		// First line
		// Extended only
		$html[] = '<span id="ed_l1_'.$counter.'" class="'.$classExtended.'">';
		$html[] = $form->getInput('eventDateLabel1', $formRecord, $ed['Label1']);
		$html[] = '</span>';
		
		// Simple + extended
		$html[] = '<span id="ed_d1_'.$counter.'" class="'.$classSimpleExtended.'">';
		$html[] = $form->getInput('eventDateDay1',   $formRecord, $ed['D1']);
		$html[] = $form->getInput('eventDateMonth1', $formRecord, $ed['M1']);
		$html[] = $form->getInput('eventDateYear1',  $formRecord, $ed['Y1']);
		$html[] = '</span>';
				
		// Description only
		$html[] = '<span id="ed_desc_'.$counter.'" class="'.$classDescription.'">';
		$html[] = $form->getInput('eventDate', $formRecord, $ed['fullString']);
		$html[] = '</span>';
			
		// Second line
		// Extended only
		$html[] = '<span id="ed_d2_'.$counter.'" class="'.$classExtended.'">';
		$html[] = $form->getLabel('eventDateLabel2', $formRecord);
		$html[] = $form->getInput('eventDateLabel2', $formRecord, $ed['Label2']);
		$html[] = $form->getInput('eventDateDay2',   $formRecord, $ed['D2']);
		$html[] = $form->getInput('eventDateMonth2', $formRecord, $ed['M2']);
		$html[] = $form->getInput('eventDateYear2',  $formRecord, $ed['Y2']);
		$html[] = '</span>';
				  
		return $html; 
	}
	
	public function checkDisplay($gedcomtype = 'person', $indLiving, $code = null) {
		// Get the database object.
		$db = JFactory::getDBO();
		$query	= $db->getQuery(true);
		$levels  = JoaktreeHelper::getUserAccessLevels();
		$indLiving = (empty($indLiving)) ? false : $indLiving;
		
		$query->select(' count(code) ');
		$query->from(  ' #__joaktree_display_settings ');
		$query->where( ' level = '.$db->quote($gedcomtype).' ');
		$query->where( ' published = true ');
		
		if (!empty($code)) {
			$query->where( ' code = '.$db->quote($code).' ');
		} else {			
			$query->where( ' code NOT IN ('
								.$db->quote('NAME').', '
								.$db->quote('NOTE').', '
								.$db->quote('ENOT').', '
								.$db->quote('SOUR').', '
								.$db->quote('ESOU')
								.') ');
		}
		
		if ($indLiving == false) {
			$query->where( ' access IN '.$levels.' ');
		} else {
			$query->where( ' accessLiving IN '.$levels.' ');
		}
		
		// Set the query and get the result list.
		$db->setQuery($query);
		$result = $db->loadResult();
		$count = (int) $result;
		
		if ($count > 0) {
			return true;
		} else {
			return false;
		}		
	}
}
?>
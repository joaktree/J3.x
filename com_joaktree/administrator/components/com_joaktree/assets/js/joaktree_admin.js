function jtsaveaccess(id){
	var form = document.adminForm;
    var cb = form[id];

    form.task.value = 'save';

	if (cb) {
		for (var i = 0; true; i++) {
			var cbx = form['cb'+i];
			if (!cbx)
				break;
			cbx.checked = false;
		} // for
		cb.checked = true;
		form.boxchecked.value = 1;
		form.submit();
	}

}

function changeAccessLevel(id) {
    var form = document.adminForm;
    var cb = form[id];
    if (cb) {
        cb.checked = true;
        form.boxchecked.value = 1;
    }
    return false;
}

//jt_tree + jt_map
function jSelectPerson(id, title, appid, apptitle, treeid) {
	var El1   = document.getElementById('jform_personId');
	var El2   = document.getElementById('jform_root_person_id');
	var El3   = document.getElementById('jform_personName');
	var El4   = document.getElementById('jform_app_id');
	var El5   = document.getElementById('jform_appTitle');
	var El6   = document.getElementById('jform_params_treeId');
	var El7   = document.getElementById('jform_tree');
	
	if (El1 != null) { El1.value = appid + '!' + id; }
	if (El2 != null) { El2.value = id; }
	if (El3 != null) { El3.value = title; }
	if (El4 != null) { El4.value = appid; }
	if (El5 != null) { El5.value = apptitle; }
	if (El6 != null) { El6.value = treeid; }
	if (El7 != null) { El7.value = treeid; }
	SqueezeBox.close();
}

//jt_tree
function jClearPerson() {
	var El1   = document.getElementById('jform_personId');
	var El2   = document.getElementById('jform_root_person_id');
	var El3   = document.getElementById('jform_personName');
	
	if (El1 != null) {
		El1.value = null;
	}
	
	if (El2 != null) {
		El2.value = null;
	}

	if (El3 != null) {
		El3.value = null;
	}
}

// jt_map
function toggleMapSetting() {
	var El1   = document.getElementById('jform_selection');
	var El2   = document.getElementById('tree');
	var El3   = document.getElementById('person1');
	var El4   = document.getElementById('person2');
	var El5   = document.getElementById('familyName');
	var El6   = document.getElementById('descendants');
	var El7   = document.getElementById('relations');
	var El8   = document.getElementById('distance');
	
	if ((El1.value == 'tree') || (El1.value == 'location')) {
		if (El2.hasClass('jt-hide')) {
			El2.removeClass('jt-hide');
			El2.addClass('jt-show');
		}
		
		if (El3.hasClass('jt-show')) {
			El3.removeClass('jt-show');
			El3.addClass('jt-hide');
		}
		
		if (El4.hasClass('jt-show')) {
			El4.removeClass('jt-show');
			El4.addClass('jt-hide');
		}
		
		if (El5.hasClass('jt-hide')) {
			El5.removeClass('jt-hide');
			El5.addClass('jt-show');
		}

		if (El6.hasClass('jt-hide')) {
			El6.removeClass('jt-hide');
			El6.addClass('jt-show');
		}
		
		if (El7.hasClass('jt-show')) {
			El7.removeClass('jt-show');
			El7.addClass('jt-hide');
		}
	}

	if (El1.value == 'tree') {
		if (El8.hasClass('jt-show')) {
			El8.removeClass('jt-show');
			El8.addClass('jt-hide');
		}
	}
	if (El1.value == 'location') {
		if (El8.hasClass('jt-hide')) {
			El8.removeClass('jt-hide');
			El8.addClass('jt-show');
		}
	}
	
	if (El1.value == 'person') {
		if (El2.hasClass('jt-show')) {
			El2.removeClass('jt-show');
			El2.addClass('jt-hide');
		}
		
		if (El3.hasClass('jt-hide')) {
			El3.removeClass('jt-hide');
			El3.addClass('jt-show');
		}
		
		if (El4.hasClass('jt-hide')) {
			El4.removeClass('jt-hide');
			El4.addClass('jt-show');
		}
		
		if (El5.hasClass('jt-show')) {
			El5.removeClass('jt-show');
			El5.addClass('jt-hide');
		}
		
		if (El6.hasClass('jt-show')) {
			El6.removeClass('jt-show');
			El6.addClass('jt-hide');
		}

		if (El7.hasClass('jt-hide')) {
			El7.removeClass('jt-hide');
			El7.addClass('jt-show');
		}
		
		if (El8.hasClass('jt-show')) {
			El8.removeClass('jt-show');
			El8.addClass('jt-hide');
		}
	}	
}

function importGedcom() {
	
	var myRequest = new Request({
	    url: 'index.php?option=com_joaktree&view=jt_import_gedcom&format=raw&tmpl=component',
	    method: 'get',
		onFailure: function(xhr) {
			alert('Error occured for url: ' + url);
		},
		onComplete: function(response) {
	    		HandleResponseGedcom('import', response);	    		
		}
	}).send();
}

function exportGedcom() {
	
	var myRequest = new Request({
	    url: 'index.php?option=com_joaktree&view=jt_export_gedcom&format=raw&tmpl=component',
	    method: 'get',
		onFailure: function(xhr) {
			alert('Error occured for url: ' + url);
		},
		onComplete: function(response) {
			HandleResponseGedcom('export', response);	    		
		}
	}).send();
}

function HandleResponseGedcom(type, response) {
	var curmsg = document.getElementById('procmsg').innerHTML;
	
	try { var r = JSON.decode(response); }
	catch(err) { 		
		document.getElementById('procmsg').innerHTML = curmsg + '<br />' + response;
		alert('An error occured while processing GedCom.');
	}	
	if ((r) && (r.status)) {
		if (r.msg != null) {
			document.getElementById('procmsg').innerHTML = curmsg + '<br />' + r.msg;
		}
		
		if (r.status == 'stop') {			
			document.getElementById('head_process').style.display  = 'none';
			document.getElementById('head_finished').style.display = 'block';
		}

		if (r.status == 'error') {
			document.getElementById('head_process').style.display  = 'none';
			document.getElementById('head_error').style.display    = 'block';
		}

		if (r.status != 'stop') {
			if (r.start) {
				document.getElementById('start_' + r.id).value = r.start;
			}
			if (r.current) {
				document.getElementById('current_' + r.id).value = r.current;
			}
			
			if (r.persons > 0) {
				document.getElementById('l_persons_' + r.id).style.display = 'block';
				document.getElementById('persons_' + r.id).value = r.persons;
			}
			
			if (r.families > 0) {
				document.getElementById('l_families_' + r.id).style.display = 'block';
				document.getElementById('families_' + r.id).value = r.families;
			}

			if (r.sources > 0) {
				document.getElementById('l_sources_' + r.id).style.display = 'block';
				document.getElementById('sources_' + r.id).value = r.sources;
			}

			if (r.repos > 0) {
				document.getElementById('l_repos_' + r.id).style.display = 'block';
				document.getElementById('repos_' + r.id).value = r.repos;
			}

			if (r.notes > 0) {
				document.getElementById('l_notes_' + r.id).style.display = 'block';
				document.getElementById('notes_' + r.id).value = r.notes;
			}

			if (r.unknown > 0) {
				document.getElementById('l_unknown_' + r.id).style.display = 'block';
				document.getElementById('unknown_' + r.id).value = r.unknown;
			}
			
			if (r.end) {
				document.getElementById('end_' + r.id).value = r.end;
			}
		}
		
		if ((r.status != 'stop') && (r.status != 'error')) {
			if (type == 'import') {
				importGedcom();
			}
			if (type == 'export') {
				exportGedcom();
			}
		}		
	}
}

function assignFT(url1) {
	
	if (!url1) { url1 = 'index.php?option=com_joaktree&view=jt_trees&format=raw&tmpl=component&init=0'; }
	
	var myRequest = new Request({
	    url: url1,
	    method: 'get',
		onFailure: function(xhr) {
			alert('Error occured for url: ' + url);
		},
		onComplete: function(response) {
	    		HandleResponseAssignFT(response);	    		
		}
	}).send();
}

function HandleResponseAssignFT(response) {
	var curmsg = document.getElementById('procmsg').innerHTML;
	
	try { var r = JSON.decode(response); }
	catch(err) { 		
		document.getElementById('procmsg').innerHTML = curmsg + '<br />' + response;
		alert('An error occured while assigning family trees to persons.');
	}	
	if ((r) && (r.status)) {
		if (r.msg != null) {
			document.getElementById('procmsg').innerHTML = curmsg + '<br />' + r.msg;
		}
		if (r.start)   { document.getElementById('start').value = r.start; }
		if (r.current) { document.getElementById('current').value = r.current; }
		if (r.end)     { document.getElementById('end').value = r.end; }
		if ((r.status != 'end') && (r.status != 'error')) { assignFT(); }	
		if (r.status == 'end') { document.getElementById('butprocmsg').removeProperty('disabled'); }
	}
}


<!--
//script by http://www.yvoschaap.com

var changing = false;

function fieldEnter(campo,evt,idfld) {
	evt = (evt) ? evt : window.event;
	if (evt.keyCode == 13) {
		if (campo.value=="") { campo.value='{{blank}}'; }
		elem = $j(idfld);
		noLight(elem); //remove glow
		elem.html(campo.value);
		changing = false;
		return false;
	} else {
		return true;
	}


}

function fieldBlur(campo,idfld) {
	if (campo.value=="") { campo.value='{{blank}}'; }
	
	$j('#' + idfld).html(campo.value);
	$j('#i_' + idfld).val(campo.value.replace('{{blank}}', ''));

	changing = false;
	return false;
}

//edit field created
function editBox(actual) {
	if(!changing){
		$j(actual).html('<textarea name="textarea" id="' + $j(actual).attr('id') + '_field" '
			+ 'style="min-width: 5em; width: ' + $j(actual).width() + 'px;" '
			+ 'oninput="textarea_expand(this, 0, 0)" '
			+ 'onfocus="highLight(this); textarea_expand(this, 0, 0)" '
			+ 'onblur="noLight(this); return fieldBlur(this,\'' + $j(actual).attr('id') + '\');">'
			+ $j(actual).html() + '</textarea>');

		changing = true;
	}
	$j('#' + $j(actual).attr('id') + '_field').focus();
}


//find all span tags with class editText and id as fieldname parsed to update script. add onclick function
function editbox_init(){
	$j('span.editText').each( function(i) {
		var spanid = $j(this).attr('id');
		
		if ($j('#i_' + spanid).length == 0) {
			if ($j(this).html() == '' || $j(this).html() == '&nbsp;') { $j(this).html('{{blank}}'); }
		
			$j(this).click( function() { editBox($j(this)); } ).css('cursor', 'pointer');
			
			var thisValue = $j(this).html().replace(/\{\{\w+\}\}/, ''); // remove anything in double brackets

			var hid = '<input type="hidden" name="i_' + spanid + '" id="i_' + spanid + '" value="' + thisValue + '" />';
			$j(this).parent().prepend(hid);
	
			$j(this).click().find('textarea').blur();
		}
	});
		
}

function highLight(span){
	span.style.border = "1px solid red";          
}

function noLight(span){
	span.style.border = "0px";   
}

$j(function() { editbox_init(); });
-->
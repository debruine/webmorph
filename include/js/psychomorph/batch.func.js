//====================================
// !BATCH FUNCTIONS
//====================================

function batchWatch(files, scriptName, theData) {  console.log('batchWatch(' + files.length + ' file, ' + scriptName + ', ' + theData + ')');
    // takes file list files, sends each separately to scriptName with data from theData
    
    if (files.length === 0) {
        growl('No files were selected', 1500);
        return false;
    }
    
    $.each(files, function(i, filename) {
        var myData;
        
        // theData is either the same for all files (an object) or one for each file (an array)
        if (theData.length !== undefined) {
            myData = $.extend(true, {}, theData[i]);
        } else { 
            myData = $.extend(true, {}, theData);
        }
        
        // each file is added to myData.img
        myData.img = filename;
        
        // if **DIRECTORY** is in subfolder, replace with the image directory
        if (myData.subfolder.indexOf('**DIRECTORY**') > -1) {
	        var cd = currentDir(); //.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
	        var regex = new RegExp('^' + cd + '(.+)\/[^\/]+$');
	        var imgdir = filename.match(regex);	// matches 
	        console.log('imgdir (' + filename + ') = ' + imgdir[1]);
	        myData.subfolder = myData.subfolder.replace('**DIRECTORY**', imgdir[1]);
	    }
        
        // process is added to the queue
        var q = new queueItem({
            url: scriptName,
            ajaxdata: myData,
            msg: scriptName.replace(/^img/,'') + ': ' + urlToName(filename),
        });
    });
}

function nameIsAvailable(name) {
    //if (name.substr(-4,1) !== '.') { name = name + '.jpg'; }
    var available = ($finder.find('li.file[url="'+name+'"]').length === 0);
    console.log('checking for ' + name + ' : ' + available);
    return available;
}

function batchToggle(toggle) {
	var theClass = toggle.className.replace('toggle_', '');
	
	//console.debug('toggle ' + theClass);
	
	var $bn = $(toggle).closest('div.batch_name');
	var $n = $bn.find('.batch_' + theClass);
	var mb = ($finder.find('li.folder.selected').length > 1); // part of a multibatch
	var cd = urlToName(currentDir());
	
	if ($(toggle).prop('checked')) {
		$n.show();

		if (theClass == 'subfolder' && mb) {
			$bn.find('input.toggle_superfolder').prop('checked', false).change(); 
		} else if (theClass == 'superfolder' && mb) {
			$bn.find('input.toggle_subfolder').prop('checked', false).change(); 
			$bn.find('.multibatch').html('**DIRECTORY**');
		}
		
		if ($n.html() == cd || $n.html() == '') {
			var defaultName = $bn.attr('default');
			if (theClass == 'superfolder') {
				$n.html(cd + defaultName + '/');
			} else if (theClass == 'subfolder') {
				$n.html( (mb ? '/' : cd) + defaultName + '/');
			} else if (theClass == 'prefix') {
				$n.html(defaultName + '_');
			} else if (theClass == 'suffix') {
				$n.html('_' + defaultName);
			}
		}
	} else if (!mb && theClass == 'subfolder' ) {
		$n.show().html(cd);	
	} else if (mb && theClass == 'superfolder' ) {
		$bn.find('.multibatch').html(cd + '**DIRECTORY**');
		$n.hide();
	} else {
		$n.hide();
	}
}

function batchNewName(theDialog, theType) {
    var bn = $('#batch_names').val();
    
    if (typeof theType !== 'string') theType = $(theDialog).find('.batch_name').attr('default');
    
    if ($(theDialog).find('.batch_name code').length == 0) {
	 	var bnn_interface = "<code>\n" + 
			"	<span class='batch_superfolder'></span>" + 
				"<span class='multibatch'>**DIRECTORY**</span>" + 
				"<span class='batch_subfolder'></span>" + 
				"<span class='batch_prefix'></span>" + 
				"**IMAGE**" + 
				"<span class='batch_suffix'></span>." + 
				"<select class='batch_ext'>\n" + 
			"		<option value='jpg'>jpg</option>\n" + 
			"		<option value='png'>png</option>\n" + 
			"		<option value='gif'>gif</option>\n" + 
			"	</select>\n" + 
			"</code><br>\n" + 
			"<label><input type='checkbox' class='toggle_superfolder'> Superfolder</label>\n" + 
			"<label><input type='checkbox' class='toggle_subfolder'> Subfolder</label>\n" + 
			"<label><input type='checkbox' class='toggle_prefix'> Prefix</label>\n" + 
			"<label><input type='checkbox' class='toggle_suffix'> Suffix</label>\n";
			
		$(theDialog).find('.batch_name').append(bnn_interface);
	}
	
	var $tp = $(theDialog).find('.toggle_prefix');
	var $ts = $(theDialog).find('.toggle_suffix');
	var $tsub = $(theDialog).find('.toggle_subfolder');
	var $tsup = $(theDialog).find('.toggle_superfolder');

	// toggle startup
    $tp.prop('checked', (bn == 'prefix')).change();
    $ts.prop('checked', (bn == 'suffix')).change();
	$tsub.prop('checked', (bn == 'folder')).change();
    
    if ($finder.find('li.folder.selected').length > 1) {
	    $tsup.parent().show();
	    $(theDialog).find('.batch_superfolder').show();
	    $tsup.prop('checked', (bn == 'folder')).change();
	    $(theDialog).find('.multibatch').show();
	} else {
		$tsup.parent().hide();
		$(theDialog).find('.batch_superfolder').hide();
		$(theDialog).find('.multibatch').hide();
	}
}

function batchNewNameGet(theDialog) {
    var d = $(theDialog);
    var mb = ($finder.find('li.folder.selected').length > 1);
    
    var sup 	= d.find('.toggle_superfolder').prop('checked') 	? d.find('.batch_superfolder').html() : '';
    var sub 	= d.find('.toggle_subfolder').prop('checked') 		? d.find('.batch_subfolder').html() : '';
    var pre 	= d.find('.toggle_prefix').prop('checked') 			? d.find('.batch_prefix').html() 	: '';
    var suf 	= d.find('.toggle_suffix').prop('checked') 			? d.find('.batch_suffix').html() 	: '';
    var dir     = mb												? d.find('.multibatch').html() 		: '';
    
    var subfolder = PM.project + '/' + sup + '/' + dir + '/' + sub;
    subfolder = subfolder.replace(/[\/]+/g, '/').replace(/\/$/, ''); // remove  multiple slashes and trailing slash
    
    var name = {
        subfolder: subfolder,
        prefix: pre,
        suffix: suf,
        ext: d.find('.batch_ext').val()
    };
    
    //console.debug(subfolder);
    
    return name;
}

function batchRenameChecks() {
    var repcheck = $('#replacecheck:checked').length > 0;
    var precheck = $('#prefixcheck:checked').length > 0;
    var sufcheck = $('#suffixcheck:checked').length > 0;
    var indexcheck = $('#indexcheck:checked').length > 0;

    var search = $('#batchRenameFind').val().split(';');
    var rep = $('#batchRenameReplace').val().split(';');
    var prefix = $('#batchRenamePrefix').val();
    var suffix = $('#batchRenameSuffix').val();
    var indexpos = $('#batchRenameIndex').val();
    var $table = $('#batchRenameDialog .batchList table tbody > tr');
    var indexpad = $table.length.toString().length;
    var index = 0;
    
    $table.each( function(i) {
        var $orig = $(this).find('td:first');
        var $new = $(this).find('td:last');
        var newName = $orig.text();
        
        if (repcheck) { 
            if (rep.length == 1) {
                if (search.length == 1) {
                    var s = new RegExp(search[0], 'g');
                    newName = newName.replace(s, rep[0]); 
                } else {
                    $.each(search, function(i, se) {
                        var s = new RegExp(se, 'g');
                        newName = newName.replace(s, rep[0]); 
                    });
                }
            } else if (rep.length == search.length) {
                $.each(search, function(i, se) {
                    var s = new RegExp(se, 'g');
                    newName = newName.replace(s, rep[i]); 
                });
            } else {
                // won't work
            }
        }
        if (precheck) { newName = prefix + newName; }
        if (sufcheck) { newName = newName.replace(/(\.[^\.]*)?$/, suffix + '$1'); }
        if (indexcheck) { 
            if (newName.substr(-4) !== '.tem') {
                index++;
            }
            if (indexpos == 'after') {
                newName = newName.replace(/(\.[^\.]*)?$/, pad(index, indexpad, '0') + '$1'); 
            } else if (indexpos == 'before') {
                newName = pad(index, indexpad, '0') + newName;
            }
        }
        
        $new.text(newName);
    });
}

function batchColorCalibrate() {
    // color-calibrate all selected images -- DOES NOT WORK YET!!
    var files = filesGetSelected('.image');
    if (files.length === 0) { 
        growl('No files were selected', 1000);
        return false; 
    }    

    batchNewName('#colorCalibrateDialog', 'color_calibrated');
    
    $('#colorCalibrateDialog').dialog({
        title: 'Batch color Calibrate ' + files.length + ' File' + ((files.length == 1) ? '' : 's'),
        buttons: {
            Cancel: function() { $(this).dialog("close"); },
            "Calibrate": {
                text: 'Calibrate',
                class: 'ui-state-focus',
                click: function() {
                    $(this).dialog("close");
                    $('#colorCalibrateDialog input[type!=checkbox]').blur(); // make sure all inputs are blurred so batch name is valid
                    var theData = batchNewNameGet('#colorCalibrateDialog');
                    theData.img = null;

                    batchWatch(files, 'imgcolorCalibrate', theData);
                }
            }
        }
    });
}

function batchModDelin() {
    // put all tem files in a list
    var files = filesGetSelected('.tem');
    if (files.length === 0) { 
        growl('No tem files were selected', 1000);
        return false; 
    }

    batchNewName('#modifyDelineation', 'newdelin');
    // get delineation from first tem file
    $.ajax({
        url: 'scripts/imgDelin',
        data: { img: files[0] },
        success: function(data) {
            //alert(JSON.stringify(data));
            var $temlist = $('#modDelinPoints');
            $temlist.html('');
            if (data.temPoints !== null) {
                autoLoadTem(data.temPoints, data.lineVectors);    // set defaults for this delineation
                
                $.each(data.temPoints, function(i, v) {
                    var $li = $('<li />').html('<input type="checkbox" tem="' + i + '" /> ' + i);
                    if (PM.default_tem[i].name !== undefined) {
                        $li.append(' (' + PM.default_tem[i].name + ')');
                    }
                    $temlist.append($li);
                });
                $('#modDelinLines').val(data.lineVectors.join("\n"));
            } else {
                alert('The template is not readable.');
            }
        }
    });
        
    $('#modifyDelineation').dialog({
        title: 'Batch Modify ' + files.length + ' Delineation' + ((files.length == 1) ? '' : 's'),
        buttons: {
            Cancel: function() {
                $(this).dialog('close');
            },
            "Modify": {
                text: "Modify",
                class: "ui-state-focus",
                click: function() {
                    // get points to delete
                    var dp = [];
                    $('#modDelinPoints input:checked').each(function(i) {
                        dp.push($(this).attr('tem'));
                    });
                    $('#modifyDelineation input[type!=checkbox]').blur(); // make sure all inputs are blurred so batch name is valid
                    
                    var theData = batchNewNameGet('#modifyDelineation');
                    theData.img = null;
                    theData.deletePoints = dp;
                    theData.newLines = $('#modDelinLines').val();
                    
                    batchWatch(files, 'temModify', theData);
                    $(this).dialog('close');
                }
            }
        }
    });
}

function batchTemConvert(old_tem, new_tem) {
    var files = filesGetSelected('.image');
    if (files.length === 0) { 
        growl('No files were selected', 1000);
        return false; 
    }
    
    var newTemName = $('#default_template option[value=' + new_tem + ']').text();
        
    batchNewName('#temConvertDialog', newTemName);
    
    $('#temConvertDialog').dialog({
        title: 'Batch Convert to ' + newTemName + ': ' + files.length + ' Image' + ((files.length == 1) ? '' : 's'),
        buttons: {
            Cancel: function() { $(this).dialog("close"); },
            "Convert": {
                text: "Convert",
                class: 'ui-state-focus',
                click: function() {
                    $(this).dialog("close");
                    var theData = batchNewNameGet('#temConvertDialog');
                    theData.img = null;
                    theData.tem_id = PM.default_tem_id;
                    theData.old_tem = old_tem;
                    theData.new_tem = new_tem;
                        
                    batchWatch(files, 'temConvert', theData);
                }
            },
        }
    });
}

function batchMirror() {
    var files = filesGetSelected('.image');
    if (files.length === 0) { 
        growl('No files were selected', 1000);
        return false; 
    }
        
    batchNewName('#mirrorDialog', 'mirror');
    $('#mirrorDialog').dialog({
        title: 'Batch Mirror ' + files.length + ' Image' + ((files.length == 1) ? '' : 's'),
        buttons: {
            Cancel: function() { $(this).dialog("close"); },
            "Mirror": {
                text: "Mirror",
                class: 'ui-state-focus',
                click: function() {
                    $(this).dialog("close");
                    var theData = batchNewNameGet('#mirrorDialog');
                    theData.img = null;
                    theData.tem_id = PM.default_tem_id;

                    batchWatch(files, 'imgMirror', theData);
                }
            },
        }
    });
}

function batchSymmetrise() {
    var files = filesGetSelected('.image.hasTem');
    if (files.length === 0) { 
        growl('No files were selected', 1000);
        return false; 
    }
        
    batchNewName('#symDialog', 'sym');
    $('#symDialog').dialog({
        title: 'Batch Symmetrise ' + files.length + ' Image' + ((files.length == 1) ? '' : 's'),
        buttons: {
            Cancel: function() { $(this).dialog("close"); },
            "Symmetrise": {
                text: "Symmetrise",
                class: 'ui-state-focus',
                click: function() {
                    $(this).dialog("close");
                    var theData = batchNewNameGet('#symDialog');
                    theData.img = null;
                    theData.tem_id = PM.default_tem_id;
                    theData.shape = ($('#sym_shape').prop('checked')) ? 'true' : 'false';
                    theData.color = ($('#sym_color').prop('checked')) ? 'true' : 'false';
                    theData.sym = $('#custom_sym').val();
                    
                    batchWatch(files, 'imgSym', theData);
                }
            },
        }
    });
}

function batchRename() {
    var files = filesGetSelected();
    if (files.length === 0) { 
        growl('No files were selected', 1500);
        return false; 
    }
    
    $('#batchRenameDialog .batchList').html("<table><thead><tr><th>Original Name</th><th>New Name</th></tr></thead><tbody></tbody></table>");
    var $table = $('#batchRenameDialog .batchList table tbody');
    $.each(files, function(i, f) {
        var name = f.split('/');
        $table.append("<tr><td>" + name[name.length - 1] + "</td><td>" + name[name.length - 1] + "</td></tr>");
    });
    $('#batchRenameDialog .batchList').stripe();
    batchRenameChecks();
    
    $('#batchRenameDialog').dialog({
        title: 'Batch Rename ' + files.length + ' File' + ((files.length == 1) ? '' : 's'),
        buttons: {
            Cancel: function() { $(this).dialog("close"); },
            "Rename": {
                text: "Rename",
                class: 'ui-state-focus',
                click: function() {
                    $(this).dialog("close");
                    
                    var theData = [];
                    var theFiles = [];
                    var j = 0;
                    
                    $table.find('>tr').each( function(i) {
                        var oldUrl = files[i];
                        var oldName = $(this).find('td:first').text();
                        var newName = $(this).find('td:last').text();
                        
                        if (oldName !== newName) {
                            theData[j] = {
                                oldurl: oldUrl,
                                newname: newName,
                                nochangetem: true
                            };
                            theFiles[j] = files[i];
                            j++;
                        }
                    });
                    
                    batchWatch(theFiles, 'fileRename', theData); 
                }
            }
        }
    });
}

function batchFacialmetrics() {
    // put all tem files in a list
    var files = filesGetSelected('.tem');
    if (files.length === 0) { 
        growl('No tem files were selected', 1000);
        return false; 
    }
    
    var fm_list = [];
    $('#fm_results').html('<table></table>').hide(); // reset the results list
        
    $('#facialmetricEQ').dialog({
        title: 'Batch Calculate Facialmetrics ' + files.length + ' Delineation' + ((files.length == 1) ? '' : 's'),
        modal: false,
        open: function(e,ui) {
            // set max-height of fm_resuts so that the dialog is never taller than the finder
            var h = $(this).closest('div.ui-dialog').outerHeight();
            var f = $finder.height();
            var newmax = (f-h)>100 ? f-h : 100;
            $('#fm_results').css('max-height', newmax + 'px');
        },
        buttons: {
            "Clear": function() {
                $('#fm_results table').empty().hide();
            },
            "All XY": function() {
	            var eq = $('#fm_equation').val();
                var eqname = $('#fm_name').val();
                if ($('#fm_results tr').length === 0) {
                    $('#fm_results table').empty().append('<thead><tr><th>Image</th></tr></thead>').show();
                    fm_list[0] = 'name';
                    $.each(files, function(i, url) {
                        var name = urlToName(url).replace(/\.tem$/, '');
                        $('#fm_results table').append('<tr id="image' + i + '"><td>' + name + '</td></tr>');
                        fm_list[i + 1] = name;
                    });
                    $('#fm_results').stripe();
                }
                $.ajax({
                    async: false,
                    data: {
                        tems: files,
                        eq: 'allXY'
                    },
                    url: "scripts/fmCalculate",
                    success: function(data) {
	                    $.each(data.xy, function(j, d) {
		                    $('#fm_results thead tr').append('<th>x<sub>' + j + '</sub></th><th>y<sub>' + j + '</sub></th>');
	                        fm_list[0] = fm_list[0] + ',x' + j + ',y' + j;
	                        $.each(d, function(i, xy) {
	                            $('#fm_results tr#image' + i).append('<td>' + xy.x + '</td>');
	                            $('#fm_results tr#image' + i).append('<td>' + xy.y + '</td>');
	                            fm_list[i + 1] = fm_list[i + 1] + "," + xy.x + "," + xy.y;
	                        });
                        });
                        $('#fm_results').show();
                    }
                });
            },
            "Calculate": function() {
                var eq = $('#fm_equation').val();
                var eqname = $('#fm_name').val();
                if ($('#fm_results tr').length === 0) {
                    $('#fm_results table').empty().append('<thead><tr><th>Image</th><th>' + eqname + '</th></tr></thead>').show();
                    fm_list[0] = 'name';
                    $.each(files, function(i, url) {
                        var name = urlToName(url).replace(/\.tem$/, '');
                        $('#fm_results table').append('<tr id="image' + i + '"><td>' + name + '</td></tr>');
                        fm_list[i + 1] = name;
                    });
                    $('#fm_results').stripe();
                } else {
                    $('#fm_results thead tr').append('<th>' + $('#fm_name').val() + '</th>');
                }
                $.ajax({
                    async: false,
                    data: {
                        tems: files,
                        eq: eq
                    },
                    url: "scripts/fmCalculate",
                    success: function(data) {
                        fm_list[0] = fm_list[0] + "," + eqname;
                        $.each(data.eq, function(i, v) {
                            $('#fm_results tr#image' + i).append('<td>' + v + '</td>');
                            var ob = {};
                            ob[eqname] = v;
                            fm_list[i + 1] = fm_list[i + 1] + "," + v;
                        });
                        $('#fm_results').show();
                    }
                });
            },
            "Download": function() {
                //alert(JSON.stringify(fm_list));
                if (fm_list.length > 0) {
                    var contents = fm_list.join("\n");
                    postIt("scripts/fileCSV", {file: contents, name: 'facialmetrics'});
                } else {
                    growl('There is no data to download. Please click &ldquo;Calculate&rdquo; to create data to download.');
                }
            },
            /*
            "Save": function() {
                if (fm_list.length > 0) {
                    $.ajax({
                        data: {
                            text: fmlist,
                            savename: ''
                        },
                        url: "scripts/txtSave",
                        success: function(data) {
                            
                        }
                    });
                } else {
                    growl('There is no data to save. Please click &ldquo;Calculate&rdquo; to create data to save.');
                }
            }
            */
        }
    });    
}

function batchCrop() {
    var files = filesGetSelected('.image');
    if (files.length === 0) { 
        growl('No files were selected', 1000);
        return false; 
    }    

    batchNewName('#cropDialog', 'cropped');

    // get image dimensions
    $.ajax({
        url: 'scripts/imgDimensions',
        type: 'GET',
        data: { img: files },
        success: function(data) {
            if (data.error) { 
                growl( JSON.stringify(data.errorText) ); 
                return false;
            }
            
            $dimList = $('<table><thead><tr><th>Image</th><th colspan="2">original w x h</th><th colspan="2">new w x h</th></tr></thead><tbody></tbody></table>');
            $dimListBody = $dimList.find('tbody');
            $.each(data.w, function(i, w) {
                $dimListBody.append('<tr><td>' + urlToName(files[i]) + '</td><td>' + w + '</td><td>' + data.h[i] + '</td><td>' + w + '</td><td>' + data.h[i] + '</td></tr>');
            });
            $dimList.stripe();
            
            $('#cropDialog .batchList').empty().append($dimList);
            
            $('#cropBoxHeight').attr('orig', data.h[0]);
            $('#cropBoxWidth').attr('orig', data.w[0]);
            $('#cropDialog input[name=top]').trigger('keyup').focus().select();
        }
    });
    
    
    $('#cropDialog').dialog({
        title: 'Batch Crop ' + files.length + ' Image' + ((files.length == 1) ? '' : 's'),
        buttons: {
            Cancel: function() { $(this).dialog("close"); },
            "Crop": {
                text: "Crop",
                class: 'ui-state-focus',
                click: function() {
                    $(this).dialog("close");
                    
                    var theData = batchNewNameGet('#cropDialog');
                    theData.img = null;
                    theData.t = $('#cropDialog input[name=top]').val();
                    theData.r = $('#cropDialog input[name=right]').val();
                    theData.b = $('#cropDialog input[name=bottom]').val();
                    theData.l = $('#cropDialog input[name=left]').val();
                    theData.x = $('#cropDialog input[name=x]').val();
                    theData.y = $('#cropDialog input[name=y]').val();
                    theData.w = $('#cropDialog input[name=width]').val();
                    theData.h = $('#cropDialog input[name=height]').val();
                    theData.rgb = $('#crop_color').slider('values');

                    batchWatch(files, 'imgCrop', theData);
                }
            }
        }
    });
}


function batchAlign() {
    // align all selected images
    files = filesGetSelected('.image.hasTem');
    if (files.length === 0) { 
        growl('No files were selected', 1000);
        return false; 
    }
    
    //set up defaults
    if ($('#alignDialog input[name=width]').val() === '') {
        alignEyesReset();
    }
    
    batchNewName('#alignDialog', 'aligned');
    
    $('#alignDialog').dialog({
        title: 'Batch Align ' + files.length + ' Image' + ((files.length == 1) ? '' : 's'),
        buttons: {
            Cancel: function() { $(this).dialog("close"); },
            'Reset': function() { alignEyesReset(); },
            "Align": {
                text: 'Align',
                class: 'ui-state-focus',
                click: function() {
                    $(this).dialog("close");
                    $('#alignDialog input[type!=checkbox]').blur(); // make sure all inputs are blurred so batch name is valid
                    
                    var theData = batchNewNameGet('#alignDialog');
                    theData.img = null;
                    theData.rgb = $('#align_color').slider('values');
                    theData.pt1 = $('#custom_align input[name="pt1"]').val();
                    theData.pt2 = $('#custom_align input[name="pt2"]').val();
                    theData.x1 = $('#custom_align input[name="x1"]').val();
                    theData.y1 = $('#custom_align input[name="y1"]').val();
                    theData.x2 = $('#custom_align input[name="x2"]').val();
                    theData.y2 = $('#custom_align input[name="y2"]').val();
                    theData.width = $('#custom_align input[name="width"]').val();
                    theData.height = $('#custom_align input[name="height"]').val();
                        
                    batchWatch(files, 'imgAlign', theData);
                }
            },
            
        }
    });
}

function alignEyesReset() {
    $('#alignDialog input[name=pt1]').val( $('#align_pt1').val() );
    $('#alignDialog input[name=pt2]').val( $('#align_pt2').val() );
    $('#alignDialog input[name=x1]').val( $('#align_x1').val() );
    $('#alignDialog input[name=y1]').val( $('#align_y1').val() );
    $('#alignDialog input[name=x2]').val( $('#align_x2').val() );
    $('#alignDialog input[name=y2]').val( $('#align_y2').val() );
    $('#alignDialog input[name=width]').val( $('#align_w').val() );
    $('#alignDialog input[name=height]').val( $('#align_h').val() );
}

function batchResize() {
    // resize all selected images
    var files = filesGetSelected('.image');
    if (files.length === 0) { 
        growl('No image files were selected', 1500);
        return false; 
    }
    
    var $rsd = $('#resizeDialog');
    
    // get image dimensions
    $.ajax({
        url: 'scripts/imgDimensions',
        type: 'GET',
        data: { img: files },
        success: function(data) {
            if (data.error) { 
                growl( JSON.stringify(data.errorText) ); 
                return false;
            }
            
            $dimList = $('<table><thead><tr><th>Image</th><th colspan="2">original w x h</th><th colspan="2">new w x h</th></tr></thead><tbody></tbody></table>');
            $dimListBody = $dimList.find('tbody');
            $.each(data.w, function(i, w) {
                $dimListBody.append('<tr><td>' + urlToName(files[i]) + '</td><td>' + w + '</td><td>' + data.h[i] + '</td><td>' + w + '</td><td>' + data.h[i] + '</td></tr>');
            });
            $dimList.stripe();

            $rsd.find('.batchList').empty().append($dimList);
            calcNewSizes();
        }
    });
    
    batchNewName('#resizeDialog', 'resized');
    
    $rsd.dialog({
        title: "Batch Resize " + files.length + ' Image' + ((files.length == 1) ? '' : 's'),
        buttons: {
            Cancel: function() { $(this).dialog("close"); },
            "Resize": {
                text: "Resize",
                class: "ui-state-focus",
                click: function() {
                    
                    $rsd.find('input[type!=checkbox]').blur(); // make sure all inputs are blurred so batch name is valid
                    var theData = batchNewNameGet('#resizeDialog');
                    theData.img = null;
                    theData.x = $rsd.find('input[name=x]').val();
                    theData.y = $rsd.find('input[name=y]').val();
                    theData.w = $rsd.find('input[name=w]').val();
                    theData.h = $rsd.find('input[name=h]').val();
                    
                    if (theData.x || theData.y || theData.w || theData.h) {
                    	$(this).dialog("close");
                    	batchWatch(files, 'imgResize', theData);
                    } else {
	                    growl('Please specify at least one dimension to resize.', 2000);
	                }
                }
            }
        }
    });
}

function calcNewSizes(name) {
    var $rsd = $('#resizeDialog');
    var $inputs = $rsd.find('input');
    var $w = $inputs.filter('[name=w]');
    var $h = $inputs.filter('[name=h]');
    var $x = $inputs.filter('[name=x]');
    var $y = $inputs.filter('[name=y]');
    
    if (name !== undefined && $inputs.filter('[name='+name+']').val() !== '') {
        if (name == 'x' || name == 'y') {
            $w.val('');
            $h.val('');
        } else if (name == 'w' || name == 'h') {
            $x.val('');
            $y.val('');
        }
    }
    
    var w = parseFloat($w.val());
    var h = parseFloat($h.val());
    var x = parseFloat($x.val());
    var y = parseFloat($y.val());
    
    if (!isNaN(w) || !isNaN(h)) {
        $rsd.find('.batchList table tbody tr').each( function() {
            var $td = $(this).find('td');
            var origW = parseInt($td.eq(1).text()); 
            var origH = parseInt($td.eq(2).text());
            var newH = (isNaN(h)) ? (w/origW) * origH : h;
            var newW = (isNaN(w)) ? (h/origH) * origW : w;
        
            $td.eq(3).text(Math.round(newW));
            $td.eq(4).text(Math.round(newH));
        });
    } else if (!isNaN(x) || !isNaN(y)) {
        if (isNaN(x)) { x = y; } else if (isNaN(y)) { y = x; }
    
        $rsd.find('.batchList table tbody tr').each( function() {
            var $td = $(this).find('td');
            
            var origW = parseInt($td.eq(1).text());
            var origH = parseInt($td.eq(2).text());
            var newW = (x/100) * origW;
            var newH = (y/100) * origH;
            
            $td.eq(3).text(Math.round(newW));
            $td.eq(4).text(Math.round(newH));
        });
    } else {
        // all are blank
        $rsd.find('.batchList table tbody tr').each( function() {
            var $td = $(this).find('td');
            $td.eq(3).text($td.eq(1).text());
            $td.eq(4).text($td.eq(2).text());
        });
    }
}

function batchRotate() {
    // resize all selected images
    var files = filesGetSelected('.image');
    if (files.length === 0) { 
        growl('No image files were selected', 1500);
        return false; 
    }
    
    var $rsd = $('#rotateDialog');
    
    batchNewName('#rotateDialog', 'rotated');
    
    $rsd.dialog({
        title: "Batch Rotate " + files.length + ' Image' + ((files.length == 1) ? '' : 's'),
        buttons: {
            Cancel: function() { $(this).dialog("close"); },
            "Rotate": {
                text: "Rotate",
                class: "ui-state-focus",
                click: function() {
                    $(this).dialog("close");
                    $rsd.find('input[type!=checkbox]').blur(); // make sure all inputs are blurred so batch name is valid
                    var theData = batchNewNameGet('#rotateDialog');
                    theData.img = null;
                    theData.rgb = $('#rotate_color').slider('values');
                    theData.degrees = $rsd.find('input[name=degrees]').val();
                    batchWatch(files, 'imgRotate', theData);
                }
            }
        }
    });
}

function batchConvert() {
    var files = filesGetSelected('.image');
    if (files.length === 0) {
        growl('No image files were selected', 1500);
        return false;
    }
    
    var nFiles = files.length;
    var fileList = $('<ul class="file" />').css('max-height', '15em');
    if (nFiles) {
        $.each(files, function(i, url) {
            fileList.append('<li>' + url + '</li>');
        });
    }
    
    var title = (files.length==1) ? "Convert this image?" : "Convert these " + files.length + " images?";
    
    $('#convertDialog').html(title).append(fileList).dialog({
        title: "Batch Convert",
        buttons: {
            Cancel: function() {
                $(this).dialog("close");
            },
            "JPG": function() {
                $(this).dialog("close");
                var theData = {'to': 'jpg'};
                batchWatch(files, 'imgConvert', theData); 
            },
            "PNG": function() {
                $(this).dialog("close");
                var theData = {'to': 'png'};
                batchWatch(files, 'imgConvert', theData); 
            },
            "GIF": function() {
                $(this).dialog("close");
                var theData = {'to': 'gif'};
                batchWatch(files, 'imgConvert', theData); 
            }
        }
    });
}

function batchMask() {
    var files = filesGetSelected('.image.hasTem');
    if (files.length === 0) { 
        growl('No files were selected', 1000);
        return false; 
    }

    // trigger change event on all checkboxes to update image and background color
    // prevents bugs when the browser autofills on refresh
    if ($('#maskDialog ul input[type=checkbox]:checked').length === 0) {
        $('#mask_face').prop('checked', true);
    }
    
    $('#maskDialog ul input[type=checkbox]').change(); 

    batchNewName('#maskDialog', 'masked');
    $('#maskDialog').dialog({
        title: 'Batch Mask ' + files.length + ' Image' + ((files.length == 1) ? '' : 's'),
        buttons: {
            Cancel: function() { $(this).dialog("close"); },
            "Mask": {
                text: "Mask",
                class: 'ui-state-focus',
                click: function() {
                    $(this).dialog("close");
                    maskImages();
                }
            },

            "Custom Mask": function() {
	    
                var cm = $('#custom_mask').val().replace(/\s/g, '');
                console.log('custom mask: ' + $('#custom_mask').val());
                var cm_match = cm.match(/^(\d{1,3},)+\d{1,3}(;(\d{1,3},)+\d{1,3})*(:(\d{1,3},)+\d{1,3}(;(\d{1,3},)+\d{1,3})*)*$/);
                // check if custom mask is properly formatted
                if ($('#custom_mask_box:visible').length === 0) {
                    $('#custom_mask_box').show();
                } else if (cm === '' || cm_match === null || cm_match.length === 0 || cm_match[0] != cm) {
                    $('#custom_mask_box p').html('Your custom mask is not properly formatted. You need to include each point number in a line, separated by commas, each line separated by semicolons, and each separate mask separated by a colon (e.g., <code>18,19,20,21,22 ; 22,30,29,28,18 : 23,24,25,26,27 ; 27,33,32,31,23</code>). Make sure that the last point in each line is the same as the first point in the next line and the last point in the last line is the same as the first point in the first line. For a simple, one-line mask, just put all the points in a line, separated by commas, and make the first point the same as the last point.');
                } else {
                    $( this ).dialog( "close" ); 
                    maskImages('custom', cm);
                }
            }

        }
    });
}

function maskImages(masktype, custom) {  console.log('maskImages(' + masktype + ')');
    //get mask color

    if (masktype == null) {
        var checked_mask = [];
        if ($('#mask_oval').prop('checked')) {
            checked_mask.push('oval');
            // no need to check others if oval is on
        } else {
            if ($('#mask_face').prop('checked')) {
                checked_mask.push('face');
                // no need to check eyes and mouth if face is on
            } else {
                if ($('#mask_eyes').prop('checked')) checked_mask.push('left_eye', 'right_eye');
                if ($('#mask_brows').prop('checked')) checked_mask.push('left_brow', 'right_brow');
                if ($('#mask_nose').prop('checked')) checked_mask.push('nose');
                if ($('#mask_mouth').prop('checked')) {
                    checked_mask.push('mouth');
                    // no need to check teeth if mouth is on
                } else {
                    if ($('#mask_teeth').prop('checked')) checked_mask.push('teeth');
                }
            }
            if ($('#mask_ears').prop('checked')) checked_mask.push('left_ear', 'right_ear');
            if ($('#mask_neck').prop('checked')) checked_mask.push('neck');
        }
        masktype = checked_mask.join(',');
    }
    $('#maskDialog input[type!=checkbox]').blur(); // make sure all inputs are blurred so batch name is valid
    var theData = batchNewNameGet('#maskDialog');
    theData.img = null;
    theData.rgb = $('#batch_mask_color').slider('values');
    theData.blur = $('#maskDialog input[name=blur]').val();
    theData.transparent = $('#mask_trans').prop('checked');
    theData.mask = masktype;
    theData.custom = custom;
    
    files = filesGetSelected('.image');
    batchWatch(files, 'imgMask', theData);
}

function maskViewCheck(type, checked) {
    if (type == 'trans') {
        var bgcolor = (checked) ? 'transparent' : $('#maskDialog .colorcheck').css('background-color');
        $('#maskExample').css('background-color', bgcolor);
    } else {
        if (checked) {
            $('#mask_demo_' + type).show();
        } else {
            $('#mask_demo_' + type).hide();
        }
    }
    var fList = [];
    if (type == 'oval') {
        fList = ['face', 'neck', 'ears', 'eyes', 'brows', 'mouth', 'teeth', 'nose'];
    } else if (type == 'face') {
        fList = ['eyes', 'brows', 'mouth', 'teeth', 'nose'];
    } else if (type == 'mouth') {
        fList = ['teeth'];
    }
    if (checked) { 
        maskToggle(fList, 'hide');
    } else {
        maskToggle(fList, 'show');
    }
    
    function maskToggle(featureList, vis) {
        $.each(featureList, function(i, f) {
            if (vis == "hide") {
                $('#mask_' + f).hide();
            } else {
                $('#mask_' + f).show();
            }
        });
    }
}

function batchTransform() {
    var $sbt = $('#SBTdialog');
    var $tbody = $sbt.find('table tbody').empty();
    var SBTdata = [];
    
    $sbt.find('textarea').val('').show();
    $sbt.find('p').html('Paste your batch file from Excel into the box below.');

    $sbt.find('div.progressBox').hide();
    $sbt.find('ol').html('');
    
    $sbt.dialog({
        title: 'Batch Transform',
        modal: false,
        buttons: {
            Cancel: function() {
                $(this).dialog("close");
            },
            "Reset": function() {
                //$('#tagnone').click();
                $sbt.find('textarea').val('').show();
                $tbody.empty();
                $sbt.find('div.progressBox').hide();
                $sbt.find('ol').html('');
                $sbt.find('p').html('Paste your batch file from Excel into the box below.');
                SBTdata = [];
            },
            "Transform": makeBatchTransform
        }
    });
    
    function makeBatchTransform() {
        $tbody.empty();
        var rows = $sbt.find('textarea').val().trim().split('\n');
        var errors = 0;
        var outnames = [];
        $.each(rows, function(i, r) {
            var row = $('<tr />');
            var cols = $.trim(r).split('\t');
            if (cols.length != 6) {
                row.css('background-color', '#fef1ec');
            }
            $.each(cols, function(j, c) {
                cols[j] = $.trim(cols[j]);
                row.append('<td>' + cols[j] + '</td>');
            });
            SBTdata[i] = {
                'transimage': (cols[0]),
                'fromimage': (cols[1]),
                'toimage': (cols[2]),
                'shapePcnt': parseFloat(cols[3]),
                'colorPcnt': parseFloat(cols[4]),
                'texturePcnt': parseFloat(cols[5]),
                'outname': (cols[6])
            };
            
            // checks for all data
            if ($finder.find('li.image.hasTem[url="' + PM.project + SBTdata[i].transimage + '"]').length === 0) {
                // image does not exist
                if ($.inArray(SBTdata[i].transimage, outnames) == -1) {
                    // not in previous rows' outnames either (not a newly created image)
                    row.find('td:eq(0)').attr('title', 'Trans-image missing.').addClass('ui-state-error');
                    errors++;
                }
            }
            if ($finder.find('li.image.hasTem[url="' + PM.project + SBTdata[i].fromimage + '"]').length === 0) {
                if ($.inArray(SBTdata[i].fromimage, outnames) == -1) {
                    row.find('td:eq(1)')
                        .attr('title', 'From-image missing.')
                        .addClass('ui-state-error');
                    errors++;
                }
            }
            if ($finder.find('li.image.hasTem[url="' + PM.project + SBTdata[i].toimage + '"]').length === 0) {
                if ($.inArray(SBTdata[i].toimage, outnames) == -1) {
                    row.find('td:eq(2)')
                        .attr('title', 'To-image missing.')
                        .addClass('ui-state-error');
                    errors++;
                }
            }
            if (!$.isNumeric(cols[3]) || SBTdata[i].shapePcnt < -300 || SBTdata[i].shapePcnt > 300) {
                row.find('td:eq(3)')
                    .attr('title', 'The shape value must be a number between -300 and +300')
                    .addClass('ui-state-error');
                errors++;
            }
            if (!$.isNumeric(cols[4]) || SBTdata[i].colorPcnt < -300 || SBTdata[i].colorPcnt > 300) {
                row.find('td:eq(4)')
                    .attr('title', 'The color value must be a number between -300 and +300')
                    .addClass('ui-state-error');
                errors++;
            }
            if (!$.isNumeric(cols[5]) || SBTdata[i].texturePcnt < -300 || SBTdata[i].texturePcnt > 300) {
                row.find('td:eq(5)')
                    .attr('title', 'The texture value must be a number between -300 and +300')
                    .addClass('ui-state-error');
                errors++;
            }
            if (SBTdata[i].outname === '' || $.inArray(SBTdata[i].outname, outnames) > -1) {
                row.find('td:eq(6)')
                    .attr('title', 'You must give each image a unique outname.')
                    .addClass('ui-state-error');
                errors++;
            } else if ($finder.find('li.image[url="' + PM.project + SBTdata[i].outname + '"]').length) {
                row.find('td:eq(6)')
                    .attr('title', 'This image already exists, please give it a different name.')
                    .addClass('ui-state-error');
                errors++;
            }
            outnames.push(SBTdata[i].outname);
            
            $('#SBTdialog table').append(row);
        });
        $('#SBTdialog textarea').hide();
        if (errors > 0) {
            $sbt.find('p').html(errors + ' errors were found. Hover over the highlighted boxes for more information.');
        } else {
            var savedImages = 0;
            $sbt.dialog('close');
/*
/nicol.jpg    /_male.jpg    /_female.jpg    100    0    0    /newtrans/nicol.jpg    
/oleg.jpg    /_male.jpg    /_female.jpg    100    100    100    /newtrans/oleg.jpg    
/perla.jpg    /_male.jpg    /_female.jpg    100    0    0    /newtrans/perla.jpg    
/mojmir.jpg    /_male.jpg    /_female.jpg    50    50    50    /newtrans/mojmir.jpg
*/                    
            $.each(SBTdata, function(i, d) {    
                getTransform(d, true);
            });
        }
    }
}

function batchAverage() {
    //$('#tagnone').click();
    var BAdata = [];
    $('#BAdialog textarea').val('').show();
    $('#BAdialog table tbody').empty();
    $('#BAdialog table').show();
    $('#BAdialog p').html('Paste your batch file from Excel into the box below. Put the name of each average on the first row and the images in the average in the rows below. Put each average in a new column.');
    $('#BAdialog').dialog({
        title: 'Batch Average',
        buttons: {
            Cancel: function() { $(this).dialog("close"); },
            "Reset": function() {
                $('#tagnone').click();
                $('#BAdialog textarea').val('').show();
                $('#BAdialog table tbody').empty();
                $('#BAdialog table').show();
                $('#BAdialog p').html('Paste your batch file from Excel into the box below. Put the name of each average on the first row and the images in the average in the rows below. Put each average in a new column.');
                var BAdata = [];
            },
            "Make Averages": {
                text: "Make Averages",
                class: 'ui-state-focus',
                click: makeBatchAverages
            }
        }
    });
    
    function makeBatchAverages() {
        // set up table and check for errors in the average file
        $('#BAdialog table tbody').empty();
        $('#BAdialog textarea').hide();
        
        var rows = $('#BAdialog textarea').val().split('\n');
        var errors = 0;
        var outnames = [];
        var hasDependencies = false;
        
        $.each(rows, function(i, r) {
            var row = $('<tr />');
            var cols = r.split('\t');
            $.each(cols, function(j, c) {
                cols[j] = $.trim(cols[j]);
                if (i === 0) {
                    row.append('<th>' + cols[j] + '</th>');
                    if (cols[j] === '' || $.inArray(cols[j], outnames) > -1) {
                        row.find('th:eq(' + j + ')')
                            .attr('title', 'You must give each image a unique outname.')
                            .addClass('ui-state-error');
                        errors++;
                    } else if ($finder.find('li.image[url="' + PM.project + cols[j] + '"]').length > 0) {
                        row.find('th:eq(' + j + ')')
                            .attr('title', 'This image already exists, please give it a different name.')
                            .addClass('ui-state-error');
                        errors++;
                    }
                    outnames.push(cols[j]);
                    BAdata[j] = {
                        outname: cols[j],
                        images: []
                    };
                } else {
                    row.append('<td>' + cols[j] + '</td>');
                    if (cols[j] !== '') {
                        BAdata[j].images.push(cols[j]);
                        if ($.inArray(cols[j], outnames) == -1) {
                            var theImg = $finder.find('li.image[url="' + PM.project + cols[j] + '"]');
                            if (theImg.length === 0) {
                                row.find('td:eq(' + j + ')')
                                    .attr('title', 'This image does not exist.')
                                    .addClass('ui-state-error');
                                errors++;
                            } else if (!theImg.hasClass('hasTem')) {
                                row.find('td:eq(' + j + ')')
                                    .attr('title', 'This image does not have a tem.')
                                    .addClass('ui-state-error');
                                errors++;
                            }
                        } else {
                            hasDependencies = true;
                        }
                    }
                }
            });
            $('#BAdialog table').append(row);
        }); // end $.each(rows, function(i, r)
    
        // notify and stop if error are found
        if (errors > 0) {
            $('#BAdialog p').html(errors + ' errors were found. Hover over the highlighted boxes for more information.');
            return false;
        }
        
        $('#footer').html('Your batch file was successfully validated.');
        $('#BAdialog').dialog('close');
        
        $.each(BAdata, function(i, d) {
            getAverage(d, true);
        });
    }
}

function queue(items) {
    this.itemList = [];
    
    if (Array.isArray(items)) {
        var thisQueue = this;
        $.each(items, function(i, item) { thisQueue.add(item); });
    }

    this.add = function(item) {
        if (item instanceof queueItem) {
            this.itemList.push(item);
            $queue.append(item.menuItem);
            this.next();
            
            return true;
        }
        return false;
    };
    
    this.getCount = function() {
        return this.itemList.length;
    };
    
    this.getStatusCount = function() {
        var n = this.itemList.length;
        var statusCount = {
            'complete': [],
            'waiting': [],
            'paused': [],
            'active': []    
        };
        
        // check contents of queue
        for (var i = 0; i < n; i++) {
            var item = this.itemList[i];
            statusCount[item.status].push(item);
        }
        
        return statusCount;
    };
    
    this.next = function() {
        var statusCount = this.getStatusCount();
        
        if (statusCount.active.length === 0) {
            if (statusCount.waiting.length > 0) {    // start next waiting item
                var nextItem = statusCount.waiting[0];
                nextItem.start();
                this.queueCountUpdate();
            } else if (statusCount.complete.length) {
                this.queueCountUpdate(statusCount.complete[statusCount.complete.length-1].returnData.newfilename);
            } else {
                this.queueCountUpdate('/');
            }
        } else { // at least one item is still active
            this.queueCountUpdate();
        }
    };
    
    this.queueCountUpdate = function(loadImg) {
        var statusCount = this.getStatusCount();
        var a = statusCount.active.length;
        var w = statusCount.waiting.length;
        var p = statusCount.paused.length;
        var c = statusCount.complete.length;
        
        var n = a + w + p;
        $queue_n.text(n).show();
        if (n === 0) { $queue_n.hide(); }
        
        if (a + w === 0 && typeof loadImg === 'string') {
            growl('Batch processing has finished. <a class="loadFiles">Click here</a> to load new files in the Finder.');
            $('div.growl a.loadFiles').click( function() { loadFiles(loadImg); $('div.growl').remove(); } );
        }
        
        if (c === 0) {
            $('#clearComplete').addClass('disabled');
        } else {
            $('#clearComplete').removeClass('disabled');
        }
        
        if (c+a+w+p === 0) {
            $('#clearAll').addClass('disabled');
        } else {
            $('#clearAll').removeClass('disabled');
        }
        
        if (w === 0) {
            $('#pauseQueue').addClass('disabled');
        } else {
            $('#pauseQueue').removeClass('disabled');
        }
        
        if (p === 0) {
            $('#restartQueue').addClass('disabled');
        } else {
            $('#restartQueue').removeClass('disabled');
        }
    };
    
    this.clear = function(filter) {
        var n = this.itemList.length;
        var newList = [];
        for (var i = 0; i < n; i++) {
            var item = this.itemList[i];
            if (item.status != 'active' && (filter === undefined || item.status == filter)) {
                item.destroy();
            } else {
                newList.push(item);
            }
        }
        this.itemList = newList;
    };
    
    this.restartAll = function() {
        var n = this.itemList.length;
        for (var i = 0; i < n; i++) {
            var theItem = this.itemList[i];
            if (theItem.status == 'paused') {
                theItem.wait();
                console.log('wait ' + theItem.msg);
            }
        }
    };
    
    this.pauseAll = function() {
        var n = this.itemList.length;
        for (var i = 0; i < n; i++) {
            var theItem = this.itemList[i];
            if (theItem.status == 'waiting') {
                theItem.pause();
                console.log('pause ' + theItem.msg);
            }
        }
    };
}

function queueItem(data) {
    var thisItem = this;
    thisItem.url = (typeof data.url == 'string') ? data.url : '';
    thisItem.async = (typeof data.async == 'boolean') ? data.async : true;
    thisItem.ajaxdata = (typeof data.ajaxdata == 'object') ? data.ajaxdata : {};
    thisItem.msg = (typeof data.msg == 'string') ? data.msg : '';
    thisItem.completefunc = (typeof data.completefunc == 'function') ? data.completefunc : function() {};
    thisItem.status = 'waiting';
    thisItem.returnData = {};
    thisItem.error = false;
    thisItem.errorText = '';
    thisItem.completed = false;
    
    thisItem.menuItem = $('<li class="queueItem waiting" />').text(this.msg)
                            .append('<span class="shortcut">&nbsp;</span>')
                            .prepend('<span class="status">&nbsp;</span>');
    thisItem.menuItem.data('obj', thisItem);
    
    thisItem.start = function() {
        // only waiting queueItems can be started
        if (thisItem.status !== 'waiting') { return false; }
        
        thisItem.status = 'active';
        thisItem.menuItem.addClass('active').removeClass('waiting');
        var startTime = new Date();
        
        $.ajax({
            url: 'scripts/' + thisItem.url,
            async: thisItem.async,
            data: thisItem.ajaxdata,
            success: function(data) {
                if (data.error) {
                    thisItem.menuItem.addClass('ui-state-error');
                    thisItem.error = true;
                    thisItem.errorText = data.errorText;
                    thisItem.menuItem.prop('title', data.errorText);
                } else {
                    thisItem.returnData = data;
                        if (data.newfilename !== undefined) {
                            // ![FIX] add file to finder
                            //var $newfile = fileNew(data.newfilename, $finder);
                        }
                }
            },
            error: function(xmlReq, txtStatus, errThrown){
                growl(xmlReq.responseText);
            },
            complete: function() {
                if (typeof thisItem.completefunc == 'function') { 
                    thisItem.completefunc(thisItem.returnData); 
                }
                var endTime = new Date();
                var elapsedTime = Math.round((endTime.getTime() - startTime.getTime())/100)/10;
                thisItem.status = 'complete';
                thisItem.menuItem    .addClass('complete')
                                    .removeClass('active')
                                    .find('span.shortcut')
                                    .html(elapsedTime + 's');
                
                PM.queue.next();
            }
        });
    };
    
    thisItem.pause = function() {
        if (thisItem.status == 'waiting') {
            thisItem.status = 'paused';
            thisItem.menuItem.addClass('paused').removeClass('waiting');
            return true;
        }
        return false;
    };
    
    thisItem.wait = function() {
        if (thisItem.status == 'paused') {
            thisItem.status = 'waiting';
            thisItem.menuItem.addClass('waiting').removeClass('paused');
            PM.queue.next();
            return true;
        }
        return false;
    };
    
    thisItem.destroy = function() {
        if (thisItem.status == 'active') { return false; }
        
        thisItem.menuItem.remove();
        thisItem.status = 'complete';
        PM.queue.queueCountUpdate();
        return true;
    };
    
    PM.queue.add(thisItem);
}
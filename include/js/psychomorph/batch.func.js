//====================================
// !BATCH FUNCTIONS
//====================================

function batchWatch(files, scriptName, theData) {  console.log('batchWatch(' + files.length + ' file, ' + scriptName + ')');
    // takes file list files, sends each separately to scriptName with data from theData

    if (files.length === 0) {
        growl('No files were selected', 1500);
        return false;
    }

    $.each(files, function(i, filename) {
        var myData,
            q;

        // theData is either the same for all files (an object) or one for each file (an array)
        if (theData.length !== undefined) {
            myData = $.extend(true, {}, theData[i]);
        } else {
            myData = $.extend(true, {}, theData);
        }

        // each file is added to myData.img
        myData.img = filename;

        // if **DIRECTORY** is in subfolder, replace with the image directory
        if (myData.hasOwnProperty('subfolder') && myData.subfolder.indexOf('**DIRECTORY**') > -1) {
            var regex,
                imgdir;

            regex = new RegExp('^' + currentDir() + '(.+)\/[^\/]+$');
            imgdir = filename.match(regex);    // matches
            console.log('imgdir (' + filename + ') = ' + imgdir[1]);
            myData.subfolder = myData.subfolder.replace('**DIRECTORY**', imgdir[1]);
        }

        // process is added to the queue
        q = new queueItem({
            url: scriptName,
            ajaxdata: myData,
            msg: scriptName.replace(/^img/,'') + ': ' + urlToName(filename),
        });
    });
}

function nameIsAvailable(name) {
    //if (name.substr(-4,1) !== '.') { name = name + '.jpg'; }
    var available;

    available = ($finder.find('li.file[url="'+name+'"]').length === 0);
    console.log('checking for ' + name + ' : ' + available);
    return available;
}

function batchToggle(toggle) {
    var theClass,
        $bn,
        $n,
        mb,
        cd,
        defaultName;

    theClass = toggle.className.replace('toggle_', '');
    $bn = $(toggle).closest('div.batch_name');
    $n = $bn.find('.batch_' + theClass);
    mb = ($finder.find('li.folder.selected').length > 1); // part of a multibatch
    cd = urlToName(currentDir());

    if ($(toggle).prop('checked')) {
        $n.show();

        if (theClass == 'subfolder' && mb) {
            $bn.find('input.toggle_superfolder').prop('checked', false).change();
        } else if (theClass == 'superfolder' && mb) {
            $bn.find('input.toggle_subfolder').prop('checked', false).change();
            $bn.find('.multibatch').html('**DIRECTORY**');
        }

        if ($n.html() == cd || $n.html() == '') {
            defaultName = $bn.attr('default');
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

function batchNewName(theDialog, theType, theExts) {
    var $d, bn, $tp, $ts, $tsub, $tsup;
    
    if (theExts == undefined) {
        theExts = ['jpg', 'png', 'gif'];
    }

    $d = $(theDialog);

    bn = $('#batch_names').val();

    if (typeof theType !== 'string') theType = $d.find('.batch_name').attr('default');

    if ($d.find('.batch_name code').length == 0) {
        var bnn_interface = "<code>\n" +
            "    <span class='batch_superfolder'></span>" +
                "<span class='multibatch'>**DIRECTORY**</span>" +
                "<span class='batch_subfolder'></span>" +
                "<span class='batch_prefix'></span>" +
                "**IMAGE**" +
                "<span class='batch_suffix'></span>.";
        
        if (theExts.length == 1) {
            bnn_interface +=  "<input type='hidden' class='batch_ext' value='"+theExts[0]+"'>" + theExts[0];
        } else {
            bnn_interface += "<select class='batch_ext'>\n";
            $.each(theExts, function(i, ext) {
                 bnn_interface +=  "        <option value='"+ext+"'>"+ext+"</option>\n";
            });
            bnn_interface += "    </select>\n";
        }
        
        bnn_interface += "</code><br>\n" +
            "<label><input type='checkbox' class='toggle_superfolder'> Superfolder</label>\n" +
            "<label><input type='checkbox' class='toggle_subfolder'> Subfolder</label>\n" +
            "<label><input type='checkbox' class='toggle_prefix'> Prefix</label>\n" +
            "<label><input type='checkbox' class='toggle_suffix'> Suffix</label>\n";

        $d.find('.batch_name').append(bnn_interface);
    }

    $tp = $d.find('.toggle_prefix');
    $ts = $d.find('.toggle_suffix');
    $tsub = $d.find('.toggle_subfolder');
    $tsup = $d.find('.toggle_superfolder');

    // toggle startup
    $tp.prop('checked', (bn == 'prefix')).change();
    $ts.prop('checked', (bn == 'suffix')).change();
    $tsub.prop('checked', (bn == 'folder')).change();

    if ($finder.find('li.folder.selected').length > 1) {
        $tsup.parent().show();
        $d.find('.batch_superfolder').show();
        $tsup.prop('checked', (bn == 'folder')).change();
        $d.find('.multibatch').show();
    } else {
        $tsup.parent().hide();
        $d.find('.batch_superfolder').hide();
        $d.find('.multibatch').hide();
    }
}

function batchNewNameGet(theDialog) {
    var $d,
        mb,
        sup = '',
        sub = '',
        dir = '',
        subfolder = '',
        name = {};

    $d = $(theDialog);
    mb = ($finder.find('li.folder.selected').length > 1);

    name.prefix = $d.find('.toggle_prefix').prop('checked') ? $d.find('.batch_prefix').html() : '';
    name.suffix = $d.find('.toggle_suffix').prop('checked') ? $d.find('.batch_suffix').html() : '';
    name.ext = $d.find('.batch_ext').val();

    if (mb) {
        sup = $d.find('.toggle_superfolder').prop('checked') ? $d.find('.batch_superfolder').html() : '';
        sub = $d.find('.toggle_subfolder').prop('checked') ? $d.find('.batch_subfolder').html() : '';
        dir = $d.find('.multibatch').html();
    } else {
        sub = $d.find('.batch_subfolder').html()
    }

    name.subfolder = WM.project.id + '/' + sup + '/' + dir + '/' + sub;
    name.subfolder = name.subfolder.replace(/[\/]+/g, '/').replace(/\/$/, ''); // remove  multiple slashes and trailing slash

    return name;
}

function batchRenameChecks() {
    var $tablerows,
        repcheck,
        precheck,
        sufcheck,
        indexcheck,
        search,
        rep,
        prefix,
        suffix,
        indexpos,
        index,
        fileN,
        imgN,
        temN,
        maxN,
        indexpad;
    
    $tablerows = $('#batchRenameDialog .batchList table tbody > tr');
    repcheck = $('#replacecheck:checked').length > 0;
    precheck = $('#prefixcheck:checked').length > 0;
    sufcheck = $('#suffixcheck:checked').length > 0;
    indexcheck = $('#indexcheck:checked').length > 0;

    search = $('#batchRenameFind').val().split(';');
    rep = $('#batchRenameReplace').val().split(';');
    prefix = $('#batchRenamePrefix').val();
    suffix = $('#batchRenameSuffix').val();
    indexpos = $('#batchRenameIndex').val();
    index = 0;
    
    // get padding length
    if (indexcheck) {
        fileN = filesGetSelected().length;
        imgN = filesGetSelected('.image.hasTem').length;
        temN = filesGetSelected('.tem.hasImg').length;
        maxN = (fileN == imgN) + temN ? imgN : fileN;
        indexpad = maxN.toString().length;
    }

    $tablerows.each( function(i) {
        var $orig,
            $new,
            newName,
            s;
        
        $orig = $(this).find('td:first');
        $new = $(this).find('td:last');
        newName = $orig.text();

        if (repcheck) {
            if (rep.length == 1) {
                if (search.length == 1) {
                    s = new RegExp(search[0], 'g');
                    newName = newName.replace(s, rep[0]);
                } else {
                    $.each(search, function(i, se) {
                        s = new RegExp(se, 'g');
                        newName = newName.replace(s, rep[0]);
                    });
                }
            } else if (rep.length == search.length) {
                $.each(search, function(i, se) {
                    s = new RegExp(se, 'g');
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

function batchTemVis() {
    // put all tem files in a list
    var files = filesGetSelected('.tem');
    if (files.length === 0) {
        growl('No tem files were selected', 1000);
        return false;
    }

    batchNewName('#temVisDialog', 'tem', ['png', 'svg']);
    
    $('#temVisDialog').dialog({
        title: 'Batch Modify ' + files.length + ' Delineation' + ((files.length == 1) ? '' : 's'),
        buttons: {
            Cancel: function() {
                $(this).dialog('close');
            },
            "Visualize Templates": {
                text: "Visualize Templates",
                class: "ui-state-focus",
                click: function() {
                    // get points to delete
                    var dp = [],
                        theData;

                    theData = batchNewNameGet('#temVisDialog');
                    theData.img = null;
                    if ($('#tem_point_style').val() !== 'none') {
                        theData.points = {
                            style: $('#tem_point_style').val(),
                            color: array2rgb($('#tem_point_color').slider('values')),
                            fill: array2rgb($('#tem_point_fill').slider('values')),
                            strokewidth: $('#tem_point_strokewidth').val(),
                            radius: $('#tem_point_radius').val()
                        };
                    }
                    if ($('#tem_line_strokewidth').val() > 0) {
                        theData.lines = {
                            color: array2rgb($('#tem_line_color').slider('values')),
                            strokewidth: $('#tem_line_strokewidth').val(),
                        };
                    }
                    
                    if ($('#tem_image').prop('checked')) {
                        theData.image = true;
                    }

                    batchWatch(files, 'temVis', theData);
                    $(this).dialog('close');
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
                    if (WM.delin.tem[i].name !== undefined) {
                        $li.append(' (' + WM.delin.tem[i].name + ')');
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
                    var dp = [],
                        theData;

                    $('#modDelinPoints input:checked').each(function(i) {
                        dp.push($(this).attr('tem'));
                    });
                    $('#modifyDelineation input[type!=checkbox]').blur(); // make sure all inputs are blurred so batch name is valid

                    theData = batchNewNameGet('#modifyDelineation');
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
    var files,
        newTemName;

    files = filesGetSelected('.tem');
    if (files.length === 0) {
        growl('No files were selected', 1000);
        return false;
    }

    newTemName = $('#defaultTemplate option[value=' + new_tem + ']').text();

    batchNewName('#temConvertDialog', newTemName);

    $('#temConvertDialog').dialog({
        title: 'Batch Convert to ' + newTemName + ': ' + files.length + ' Template' + ((files.length == 1) ? '' : 's'),
        buttons: {
            Cancel: function() { $(this).dialog("close"); },
            "Convert": {
                text: "Convert",
                class: 'ui-state-focus',
                click: function() {
                    var theData;

                    $(this).dialog("close");
                    theData = batchNewNameGet('#temConvertDialog');
                    theData.img = null;
                    theData.tem_id = WM.delin.temId;
                    theData.old_tem = old_tem;
                    theData.new_tem = new_tem;

                    batchWatch(files, 'temConvert', theData);
                }
            },
        }
    });
}

$('#scrambleExample').bind("mousedown", function(evt) {
    evt.metaKey = true;
}).selectable();

function resetGrids() {
    var $se,
        origW,
        origH,
        gridSize,
        xOffset,
        yOffset,
        maxVal,
        chosen = [], // has form of chosen[y][x]
        ratio,
        xgrids,
        ygrids,
        x,
        y,
        newGridBox,
        theClass,
        displayGridSize;

    $se = $('#scrambleExample');

    xOffset = $('#scramble_x_offset').val();
    yOffset = $('#scramble_y_offset').val();
    origW = $se.data('origW');
    origH = $se.data('origH');
    ratio = $se.data('ratio');
    gridSize = $('#grid_size').val();

    if (xOffset >= origW || xOffset < 0) {
        xOffset = 0;
    }
    $('#scramble_x_offset').val(xOffset);
    $se.css('padding-left', xOffset*ratio + 'px');

    if (yOffset >= origH || yOffset < 0) {
        yOffset = 0;
    }
    $('#scramble_y_offset').val(yOffset);
    $se.css('padding-top', yOffset*ratio + 'px');

    maxVal = Math.floor( Math.min(origW-xOffset, origH-yOffset) / 2 );

    if (gridSize > maxVal || gridSize < 1) {
        gridSize = maxVal;
        $('#grid_size').val(gridSize);
    }

    if (gridSize < 5) {
        $se.find('div').remove();
        growl('With a grid size less than 5 pixels, you can only scramble all boxes with no grid lines.');
        $('#grid_lines').prop('checked', false);
        return false;
    }

    xgrids = Math.floor( (origW - xOffset) / gridSize );
    ygrids = Math.floor( (origH - yOffset) / gridSize );
    
    $se.data('xgrids', xgrids);
    $se.data('ygrids', ygrids);

    $se.find('div').each( function() {
        var $this;

        $this = $(this);

        if ($this.data('x') == 0) {
            chosen.push([]);
        }

        chosen[$this.data('y')].push( $this.hasClass('ui-selected') );
    });

    $se.find('div').remove();

    for (y = 0; y < ygrids; y++) {
        for (x = 0; x < xgrids; x++) {
            theClass = '';

            if (x === 0) {
                theClass += 'rowstart';
            }
            if (chosen[y] !== undefined && chosen[y][x] !== undefined && chosen[y][x]) {
                theClass += ' ui-selected';
            }

            newGridBox = '<div class="' + theClass + '" data-x="' + x + '" data-y="' + y + '" title="' + x + ',' + y + '"></div>';

            $se.append(newGridBox);
        }
    }

    displayGridSize = Math.round(gridSize*ratio*10)/10;
    $se.find('div').css({
        width: displayGridSize + 'px',
        height: displayGridSize + 'px'
    });
    
    $('#grid_lines').trigger('change');
};

function batchScramble() {
    var files = filesGetSelected('.image');
    if (files.length === 0) {
        growl('No files were selected', 1000);
        return false;
    }

    // get image dimensions
    $.ajax({
        url: 'scripts/imgDimensions',
        type: 'GET',
        data: { img: files[0] },
        success: function(data) {
            var ratio;

            if (data.w <= 6000) {
                ratio = 1;
            } else {
                ratio = 6000 / data.w;
            }

            $('#scrambleExample').css({
                'background-image': 'url(' + fileAccess(files[0]) + ')',
                'width': data.w * ratio,
                'height': data.h * ratio
            }).data({
                origW: data.w,
                origH: data.h,
                ratio: ratio
            });

            $('#scramble_orig_width').html(data.w);
            $('#scramble_orig_height').html(data.h);

            resetGrids();
        }
    });


    batchNewName('#scrambleDialog', 'scramble');

    $('#scrambleDialog').dialog({
        width: $finder.width(),
        height: 'auto',
        maxWidth: $finder.width(),
        maxHeight: $finder.height(),
        title: 'Batch Scramble ' + files.length + ' Image' + ((files.length == 1) ? '' : 's'),
        buttons: {
            Cancel: function() { $(this).dialog("close"); },
            "Reset Grid": {
                text: "Reset Grid",
                click: function() {
                    $('#scrambleExample div').removeClass('ui-selected');
                    resetGrids();
                }
            },
            "Select All": {
                text: "Select All",
                click: function() {
                    $('#scrambleExample div').addClass('ui-selected');
                }
            },
            "Scramble": {
                text: "Scramble",
                class: 'ui-state-focus',
                click: function() {
                    var theData,
                        rows,
                        chosen = [],
                        means = [],
                        canSym = true;

                    theData = batchNewNameGet('#scrambleDialog');
                    theData.img = null;
                    theData.grid = $('#grid_size').val();
                    theData.x = $('#scramble_x_offset').val();
                    theData.y = $('#scramble_y_offset').val();
                    theData.sym = $('#scramble_sym').prop('checked');

                    if (theData.grid < 5) {
                        theData.chosen = 'all';
                    } else {
                        rows = $('#scrambleExample').data('ygrids');
                        for (r = 0; r <= rows; r++) {
                            chosen[r] = [];
                        }
                        
                        $('#scrambleExample div.ui-selected').each( function() {
                            chosen[$(this).data('y')].push($(this).data('x'));
                        });
                        
                        theData.chosen = [];
                        $.each(chosen, function(i) {
                            if (chosen[i].length) {
                                theData.chosen[i] = chosen[i].join();
                                means.push(array_mean(chosen[i]));
                            }
                        });
                        
                        // check if all row means the same for symmetric scramble
                        if ($('#scramble_sym').prop('checked')) {
                            $.each(means, function(i,v) {
                                if (v !== means[0]) {
                                    canSym = canSym && false;
                                }
                            });
                            console.log("canSym: " + canSym);

                            if (!canSym) {
                                growl('The selected squares are not vertically symmetric.');
                                return false;
                            }
                        }
                    }

                    if ($('#grid_lines').prop('checked')) {
                        theData.line_color = $('#grid_line_color').slider('values');
                    }
                    
                    $(this).dialog("close");

                    batchWatch(files, 'imgScramble', theData);
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
                    var theData;

                    $(this).dialog("close");
                    theData = batchNewNameGet('#mirrorDialog');
                    theData.img = null;
                    theData.tem_id = WM.delin.temId;

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
                    var theData;

                    $(this).dialog("close");
                    theData = batchNewNameGet('#symDialog');
                    theData.img = null;
                    theData.tem_id = WM.delin.temId;
                    theData.shape = ($('#sym_shape').prop('checked')) ? 'true' : 'false';
                    theData.color = ($('#sym_color').prop('checked')) ? 'true' : 'false';
                    theData.sym = $('#custom_sym').val();

                    batchWatch(files, 'imgSym', theData);
                }
            },
        }
    });
}

function batchPixels() {
    var files = filesGetSelected('.image');
    if (files.length === 0) {
        growl('No files were selected', 1000);
        return false;
    }
    
    $('#pixelsDialog').dialog({
        title: 'Batch Pixel ' + files.length + ' Image' + ((files.length == 1) ? '' : 's'),
        buttons: {
            Cancel: function() { $(this).dialog("close"); },
            "Get Pixels": {
                text: "Get Pixels",
                class: 'ui-state-focus',
                click: function() {
                    var theData = {};

                    $(this).dialog("close");
                    theData.img = null;
                    theData.ignore_mask = $('#ignore_mask').prop('checked');

                    batchWatch(files, 'imgPixels', theData);
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
                    var theData = [],
                        theFiles = [],
                        j = 0;

                    $(this).dialog("close");

                    $table.find('>tr').each( function(i) {
                        var oldURL, oldName, newName;

                        oldUrl = files[i];
                        oldName = $(this).find('td:first').text();
                        newName = $(this).find('td:last').text();

                        if (oldName !== newName) {
                            theData[j] = {
                                oldurl: oldUrl,
                                newname: newName,
                                nochangetem: true
                            };
                            $finder.find('li.file[url="' + files[i] + '"]').remove();
                            
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

function fmAddEquation() {
    // validate data
    var error = false;

    if ($('#fm_name').val() == '') {
        error = true;
        $('#fm_name').addClass('error').focus().select();
    } else {
        $('#fm_name').removeClass('error');
    }

    if ($('#fm_equation').val() == '') {
        error = true;
        $('#fm_equation').addClass('error').focus().select();
    } else {
        $('#fm_equation').removeClass('error');
    }

    if (!error) {
        $.ajax({
            url: "scripts/fmAddEquation",
            data: {
                name: $('#fm_name').val(),
                eq: $('#fm_equation').val(),
                desc: 'Description'
            },
            success: function(data) {
                var $newEQ = $('<li/>').attr({
                    'title': data.desc,
                    'data-equation': data.eq,
                }).text(data.name);
                $('#fmButtons').append($newEQ);
            }
        });
    }
}

function batchFacialmetrics() {
    // put all tem files in a list
    var files, fm_list;

    files = filesGetSelected('.tem');
    if (files.length === 0) {
        growl('No tem files were selected', 1000);
        return false;
    }

    fm_list = [];
    $('#fm_results').html('<table></table>').hide(); // reset the results list

    $('#facialmetricEQ').dialog({
        title: 'Batch Calculate Facialmetrics ' + files.length + ' Delineation' + ((files.length == 1) ? '' : 's'),
        modal: false,
        open: function(e,ui) {
            // set max-height of fm_resuts so that the dialog is never taller than the finder
            var h, f, newmax;

            h = $(this).closest('div.ui-dialog').outerHeight();
            f = $finder.height();
            newmax = (f-h)>100 ? f-h : 100;
            $('#fm_results').css('max-height', newmax + 'px');
        },
        buttons: {
            "Clear": function() {
                $('#fm_results table').empty().hide();
            },
            "All XY": function() {
                var eq, eqname;

                eq = $('#fm_equation').val();
                eqname = $('#fm_name').val();

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
                            var ob = {};

                            $('#fm_results tr#image' + i).append('<td>' + v + '</td>');
                            ob[eqname] = v;
                            fm_list[i + 1] = fm_list[i + 1] + "," + v;
                        });
                        $('#fm_results').show();
                    },
                    error: function() {
                        $('#footer-text').html('Error calculating facialmetrics');
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
            var $dimList,
                $dimListBody;

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
                    var theData;

                    $(this).dialog("close");

                    theData = batchNewNameGet('#cropDialog');
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

function batchTemFromEmbedded() {
    // create tem files for all jpegs with embedded tem
    var files = filesGetSelected('.image.jpg');
    if (files.length === 0) {
        growl('No JPG files were selected (only JPG have embedded tems)', 1000);
        return false;
    }
    
    theData = {img: null};
    
    batchWatch(files, 'imgEmbeddedTem', theData);
}


function batchAlign() {
    // align all selected images
    var files = filesGetSelected('.image.hasTem');
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
                    var theData;

                    $(this).dialog("close");
                    $('#alignDialog input[type!=checkbox]').blur(); // make sure all inputs are blurred so batch name is valid

                    theData = batchNewNameGet('#alignDialog');
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
    var files, $rsd;

    files = filesGetSelected('.image');
    if (files.length === 0) {
        growl('No image files were selected', 1500);
        return false;
    }

    $rsd = $('#resizeDialog');

    // get image dimensions
    $.ajax({
        url: 'scripts/imgDimensions',
        type: 'GET',
        data: { img: files },
        success: function(data) {
            var $dimList, $dimListBody;

            if (data.error) {
                growl( JSON.stringify(data.errorText) );
                return false;
            }

            $dimList = $('<table><thead><tr><th>Image</th>' +
                         '<th colspan="2">original w x h</th>' +
                         '<th colspan="2">new w x h</th></tr>' +
                         '</thead><tbody></tbody></table>');
            $dimListBody = $dimList.find('tbody');
            $.each(data.w, function(i, w) {
                $dimListBody.append('<tr><td>' + urlToName(files[i]) +
                                    '</td><td>' + w + '</td><td>' + data.h[i] +
                                    '</td><td>' + w + '</td><td>' + data.h[i] +
                                    '</td></tr>');
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
                    var theData;

                    $rsd.find('input[type!=checkbox]').blur(); // make sure all inputs are blurred so batch name is valid
                    theData = batchNewNameGet('#resizeDialog');
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
    var $rsd, $inputs, $w, $h, $x, $y, w, h, x, y;

    $rsd = $('#resizeDialog');
    $inputs = $rsd.find('input');
    $w = $inputs.filter('[name=w]');
    $h = $inputs.filter('[name=h]');
    $x = $inputs.filter('[name=x]');
    $y = $inputs.filter('[name=y]');

    if (name !== undefined && $inputs.filter('[name='+name+']').val() !== '') {
        if (name == 'x' || name == 'y') {
            $w.val('');
            $h.val('');
        } else if (name == 'w' || name == 'h') {
            $x.val('');
            $y.val('');
        }
    }

    w = parseFloat($w.val());
    h = parseFloat($h.val());
    x = parseFloat($x.val());
    y = parseFloat($y.val());

    if (!isNaN(w) || !isNaN(h)) {
        $rsd.find('.batchList table tbody tr').each( function() {
            var $td, origW, origH, newW, newH;

            $td = $(this).find('td');
            origW = parseInt($td.eq(1).text());
            origH = parseInt($td.eq(2).text());
            newH = (isNaN(h)) ? (w/origW) * origH : h;
            newW = (isNaN(w)) ? (h/origH) * origW : w;

            $td.eq(3).text(Math.round(newW));
            $td.eq(4).text(Math.round(newH));
        });
    } else if (!isNaN(x) || !isNaN(y)) {
        if (isNaN(x)) { x = y; } else if (isNaN(y)) { y = x; }

        $rsd.find('.batchList table tbody tr').each( function() {
            var $td, origW, origH, newW, newH;

            $td = $(this).find('td');

            origW = parseInt($td.eq(1).text());
            origH = parseInt($td.eq(2).text());
            newW = (x/100) * origW;
            newH = (y/100) * origH;

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
    theData.reverse = $('#mask_reverse').prop('checked');
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

function batchEdit() {
    var $batchDialog = $('#batchEditDialog');
    var $tbody = $batchDialog.find('table tbody').empty();
    var batchData = [];

    $batchDialog.find('p.warning').hide();
    $batchDialog.find('textarea').val('').show().focus();

    $batchDialog.dialog({
        title: 'Batch Edit',
        modal: false,
        buttons: {
            Cancel: function() {
                $(this).dialog("close");
            },
            "Reset": function() {
                //$('#tagnone').click();
                $batchDialog.find('textarea').val('').show().focus();
                $tbody.empty();
                $batchDialog.find('p.warning').hide();
                batchData = [];
            },
            "Edit": makeBatchEdit
        }
    });
    
    function makeBatchEdit() {
        var header = "image	align	resize	rotate	crop	mask	sym	mirror	order	outname";
        //var header = "image\talign\tresize\trotate\tcrop\tmask\tsym\tmirror\tordert\outname";
        var rows = $batchDialog.find('textarea').val().replace(header,'').trim().split('\n');
        var errors = 0;
        var outnames = [];
        var theTitle = '';
        
        $tbody.empty();
        
        $.each(rows, function(i, r) {
            var row = $('<tr />');
            var cols = r.replace(/ /g,'').split('\t');

            if (cols.length != 10) {
                row.css('background-color', '#fef1ec');
            }
            $.each(cols, function(j, c) {
                cols[j] = $.trim(cols[j]);
                row.append('<td>' + cols[j] + '</td>');
            });

            batchData[i] = {
                'image': WM.project.id + cols[0],
                'align': cols[1],
                'resize': cols[2],
                'rotate': cols[3],
                'crop': cols[4],
                'mask': cols[5],
                'sym': cols[6],
                'mirror': cols[7],
                'order': cols[8],
                'outname': cols[9]
            };

            // checks for all data
            var $theImage = $finder.find('li.image[url="' + WM.project.id + cols[0] + '"]');
            if ($theImage.length === 0) {
                // image does not exist
                if ($.inArray(cols[0], outnames) == -1) {
                    // not in previous rows' outnames either (not a newly created image)
                    row.find('td:eq(0)').attr('title', 'Image missing.').addClass('ui-state-error');
                    errors++;
                }
            } else {
                var hasTem = $theImage.hasClass('hasTem');
                batchData[i].hasTem = true;
            }

            // check align
            if (cols[1].length != 0 && cols[1].toLowerCase() != 'false') {
                var align = cols[1].match(/^\d{1,3},\d{1,3},\d{1,4}(\.\d+)?,\d{1,4}(\.\d+)?,\d{1,4}(\.\d+)?,\d{1,4}(\.\d+)?,\d{1,5},\d{1,5}(rgb\(\d{1,3},\d{1,3},\d{1,3}\))?$/i);
                theTitle = '';
                
                if (cols[1].toLowerCase() !== 'default' && cols[1].toLowerCase() !== 'frl' && !align) {
                    theTitle = 'Invalid format';
                } else if (!hasTem) {
                    theTitle = 'The image needs a template file to align';
                }
                
                if (theTitle != '') {
                    row.find('td:eq(1)')
                        .attr('title', theTitle)
                        .addClass('ui-state-error');
                    errors++;
                }
            }
            
            // check resize
            if (cols[2].length != 0 && cols[2].toLowerCase() != 'false') {
                var resize = cols[2].split(',');
                theTitle = '';
                if (resize.length == 1) {
                    if ( !resize[0].match(/^\d+(\.\d+)?\%$/) ) {
                        theTitle = 'A single value must be a percentage';
                    }
                } else if (resize.length == 2) {
                    if ( resize[0].match(/^\d+(\.\d+)?\%$/)) {
                        if ( !resize[1].match(/^(\d+(\.\d+)?\%|null)$/) ) {
                            theTitle = 'The height must be the same type as the width (or null)';
                        }
                    } else if ( resize[0].match(/^\d+(\.\d+)?px$/) ) {
                        if ( !resize[1].match(/^(\d+(\.\d+)?px|null)$/) ) {
                            theTitle = 'The height must be the same type as the width (or null)';
                        }
                    } else if ( resize[0].match(/^null$/) ) {
                        if ( !resize[1].match(/^\d+(\.\d+)?(\%|px)$/) ) {
                            theTitle = 'The height must be px or % if the width is null';
                        }
                    } else {
                        theTitle = 'The width must be in px, % or null';
                    }
                } else {
                    theTitle = "Too many values (w,h)";
                }
                
                if (theTitle != '') {
                    row.find('td:eq(2)')
                        .attr('title', theTitle)
                        .addClass('ui-state-error');
                    errors++;
                }
            }
            
            // check rotate
            if (cols[3].length != 0 && cols[3].toLowerCase() != 'false') {
                if (!cols[3].match(/^(-?\d+(?:\.\d+)?)(?:,rgb\((\d{1,3},\d{1,3},\d{1,3})\))?$/i)) {
                    row.find('td:eq(3)')
                        .attr('title', 'Invalid format')
                        .addClass('ui-state-error');
                    errors++;
                }
            }
            
            // check crop
            if (cols[4].length != 0 && cols[4].toLowerCase() != 'false') {
               if (!cols[4].match(/^(-?\d+,-?\d+,-?\d+,-?\d+)(?:,rgb\((\d{1,3},\d{1,3},\d{1,3})\))?$/i)) {
                    row.find('td:eq(4)')
                        .attr('title', 'Invalid format')
                        .addClass('ui-state-error');
                    errors++;
                }
            }
            
            // check mask
            if (cols[5].length != 0 && cols[5].toLowerCase() != 'false') {
               if (!cols[5].match(/^\(([^\(\)]+)\),(\d{1,2})(?:,(?:(transparent)|rgb\((\d{1,3},\d{1,3},\d{1,3})\)))?$/i)) {
                    row.find('td:eq(5)')
                        .attr('title', 'Invalid format')
                        .addClass('ui-state-error');
                    errors++;
                }
            }
            
            // check sym
            if (cols[6].length != 0 && cols[6].toLowerCase() != 'false') {
                theTitle = '';
                
                if (!cols[6].match(/^(shape|color|colour)(?:,(shape|color|colour))?$/i)) {
                    theTitle = 'Invalid format';
                } else if (!hasTem) {
                    theTitle = 'The image needs a template file to symmetrise';
                }
                
                if (theTitle != '') {
                    row.find('td:eq(6)')
                        .attr('title', theTitle)
                        .addClass('ui-state-error');
                    errors++;
                }
            }
            
            // check mirror
            if (cols[7].length != 0 && cols[7].toLowerCase() != 'false') {
                if (cols[7].toLowerCase() != 'true') {
                    row.find('td:eq(7)')
                        .attr('title', 'mirror can only be TRUE, FALSE or blank')
                        .addClass('ui-state-error');
                    errors++;
                }
            }
            
            // check order
            if (cols[8].length != 0 && cols[8].toLowerCase() != 'false') {
                var order = cols[8].split(',');
                theTitle = '';
                
                if (order.length > 7) {
                    theTitle = 'You can only have up to 7 items';
                } else {
                    $.each(order, function() {
                        if (!this.match(/^(align|resize|rotate|crop|mask|sym|mirror)$/)) {
                            theTitle += '"' + this + '" is not a valid type '
                        } else if (batchData[i][this].length == 0 || batchData[i][this].toLowerCase() == 'false') {
                            theTitle += '"' + this + '" is not set '
                        }
                    });
                }
                
                if (theTitle != '') {
                    row.find('td:eq(8)')
                        .attr('title', theTitle)
                        .addClass('ui-state-error');
                    errors++;
                }
            }

            // check outname
            if (cols[9] === '' || $.inArray(cols[9], outnames) > -1) {
                row.find('td:eq(9)')
                    .attr('title', 'You must give each image a unique outname.')
                    .addClass('ui-state-error');
                errors++;
            } else if ($finder.find('li.image[url="' + WM.project.id + cols[9] + '"]').length) {
                row.find('td:eq(9)')
                    .attr('title', 'This image already exists, please give it a different name.')
                    .addClass('ui-state-error');
                errors++;
            }
            outnames.push(batchData[i].outname);

            $batchDialog.find('table').append(row);
        });
        $batchDialog.find('textarea').hide();
        
        if (errors > 0) {
            $batchDialog.find('p.warning').html(errors + ' errors were found. Hover over the highlighted boxes for more information.').show();
        } else {
            var savedImages = 0;
            $batchDialog.dialog('close');
            $.each(batchData, function(i, d) {
                var q = new queueItem({
                    url: 'imgEdit',
                    ajaxdata: { theData: d, outname: WM.project.id + d.outname },
                    msg: 'Edit: ' + d.outname,
                }); 
            });
        }
    }
}

function getEdit(tVars) {
      
}

function batchTransform() {
    var $batchDialog = $('#batchTransDialog');
    var $tbody = $batchDialog.find('table tbody').empty();
    var batchData = [];

    $batchDialog.find('textarea').val('').show().focus();
    $batchDialog.find('p.warning').hide();

    $batchDialog.dialog({
        title: 'Batch Transform',
        modal: false,
        buttons: {
            Cancel: function() {
                $(this).dialog("close");
            },
            "Reset": function() {
                //$('#tagnone').click();
                $batchDialog.find('textarea').val('').show().focus();
                $tbody.empty();
                $batchDialog.find('p.warning').hide();
                batchData = [];
            },
            "Transform": makeBatchTransform
        }
    });

    function makeBatchTransform() {
        var header = "trans-img\tfrom-img\tto-img\tshape\tcolor\ttexture\toutname"
        var rows = $batchDialog.find('textarea').val().replace(header,'').trim().split('\n');
        var errors = 0;
        var outnames = [];
        var relativeDir = currentDir().replace(/^\d+/, '');
        
        $tbody.empty();
        
        $.each(rows, function(i, r) {
            var row = $('<tr />');
            var cols = $.trim(r).split(',');
            // check if tab-delimited
            if (cols.length ==1) {
                cols = $.trim(r).split('\t');
            }
            if (cols.length != 6) {
                row.css('background-color', '#fef1ec');
            }
            // /darjal.jpg, _female_avg.jpg, /_male_avg.jpg, 100%, 0,0, trans6.jpg
            $.each(cols, function(j, c) {
                cols[j] = $.trim(cols[j].replace('%',''));
                if ([0,1,2,6].indexOf(j) != -1 && cols[j].substr(0, 1) != "/") {
                    cols[j] = relativeDir + cols[j];
                }
                row.append('<td>' + cols[j] + '</td>');
            });
            batchData[i] = {
                'transimage': (cols[0]),
                'fromimage': (cols[1]),
                'toimage': (cols[2]),
                'shapePcnt': parseFloat(cols[3]),
                'colorPcnt': parseFloat(cols[4]),
                'texturePcnt': parseFloat(cols[5]),
                'outname': (cols[6])
            };

            // checks for all data
            if ($finder.find('li.image.hasTem[url="' + WM.project.id + batchData[i].transimage + '"]').length === 0) {
                // image does not exist
                if ($.inArray(batchData[i].transimage, outnames) == -1) {
                    // not in previous rows' outnames either (not a newly created image)
                    row.find('td:eq(0)').attr('title', 'Trans-image missing.').addClass('ui-state-error');
                    errors++;
                }
            }
            if ($finder.find('li.image.hasTem[url="' + WM.project.id + batchData[i].fromimage + '"]').length === 0) {
                if ($.inArray(batchData[i].fromimage, outnames) == -1) {
                    row.find('td:eq(1)')
                        .attr('title', 'From-image missing.')
                        .addClass('ui-state-error');
                    errors++;
                }
            }
            if ($finder.find('li.image.hasTem[url="' + WM.project.id + batchData[i].toimage + '"]').length === 0) {
                if ($.inArray(batchData[i].toimage, outnames) == -1) {
                    row.find('td:eq(2)')
                        .attr('title', 'To-image missing.')
                        .addClass('ui-state-error');
                    errors++;
                }
            }
            if (!$.isNumeric(cols[3]) || batchData[i].shapePcnt < -300 || batchData[i].shapePcnt > 300) {
                row.find('td:eq(3)')
                    .attr('title', 'The shape value must be a number between -300 and +300')
                    .addClass('ui-state-error');
                errors++;
            }
            if (!$.isNumeric(cols[4]) || batchData[i].colorPcnt < -300 || batchData[i].colorPcnt > 300) {
                row.find('td:eq(4)')
                    .attr('title', 'The color value must be a number between -300 and +300')
                    .addClass('ui-state-error');
                errors++;
            }
            if (!$.isNumeric(cols[5]) || batchData[i].texturePcnt < -300 || batchData[i].texturePcnt > 300) {
                row.find('td:eq(5)')
                    .attr('title', 'The texture value must be a number between -300 and +300')
                    .addClass('ui-state-error');
                errors++;
            }
            if (batchData[i].outname === '' || $.inArray(batchData[i].outname, outnames) > -1) {
                row.find('td:eq(6)')
                    .attr('title', 'You must give each image a unique outname.')
                    .addClass('ui-state-error');
                errors++;
            } else if ($finder.find('li.image[url="' + WM.project.id + batchData[i].outname + '"]').length) {
                row.find('td:eq(6)')
                    .attr('title', 'This image already exists, please give it a different name.')
                    .addClass('ui-state-error');
                errors++;
            }
            outnames.push(batchData[i].outname);

            $('#batchTransDialog table').append(row);
        });
        $('#batchTransDialog textarea').hide();
        if (errors > 0) {
            $batchDialog.find('p.warning').html(errors + ' errors were found. Hover over the highlighted boxes for more information.').show();
        } else {
            var savedImages = 0;
            $batchDialog.dialog('close');

            $.each(batchData, function(i, d) {
                getTransform(d, true);
            });
        }
    }
}

function batchAverage() {
    //$('#tagnone').click();
    var $batchDialog = $('#batchAvgDialog');
    var $tbody = $batchDialog.find('table tbody').empty();
    var batchData = [];
    
    $batchDialog.find('table').show();
    $batchDialog.find('p.warning').hide();
    $batchDialog.find('textarea').val('').show().focus();
    
    $batchDialog.dialog({
        title: 'Batch Average',
        buttons: {
            Cancel: function() { $(this).dialog("close"); },
            "Reset": function() {
                //$('#tagnone').click();
                $batchDialog.find('textarea').val('').show().focus();
                $tbody.empty();
                $batchDialog.find('p.warning').hide();
                batchData = [];
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
        $tbody.empty();
        $batchDialog.find('textarea').hide();

        var rows = $('#batchAvgDialog textarea').val().split('\n');
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
                    } else if ($finder.find('li.image[url="' + WM.project.id + cols[j] + '"]').length > 0) {
                        row.find('th:eq(' + j + ')')
                            .attr('title', 'This image already exists, please give it a different name.')
                            .addClass('ui-state-error');
                        errors++;
                    }
                    outnames.push(cols[j]);
                    batchData[j] = {
                        outname: cols[j],
                        images: []
                    };
                } else {
                    row.append('<td>' + cols[j] + '</td>');
                    if (cols[j] !== '') {
                        batchData[j].images.push(cols[j]);
                        if ($.inArray(cols[j], outnames) == -1) {
                            var theImg = $finder.find('li.image[url="' + WM.project.id + cols[j] + '"]');
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
            $batchDialog.find('table').append(row);
        }); // end $.each(rows, function(i, r)

        // notify and stop if error are found
        if (errors > 0) {
            $batchDialog.find('p.warning').html(errors + ' errors were found. Hover over the highlighted boxes for more information.').show();
            return false;
        }

        $('#footer-text').html('Your batch file was successfully validated.');
        $batchDialog.dialog('close');

        $.each(batchData, function(i, d) {
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
                if (statusCount.complete[0].returnData.hasOwnProperty('newFileName')) {
                    this.queueCountUpdate(statusCount.complete[0].returnData.newFileName);
                } else {
                    this.queueCountUpdate('/');
                }
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
        $('#queue_n').text(n).show();
        if (n === 0) { $('#queue_n').hide(); }

        if (a + w === 0 && typeof loadImg === 'string') {
            //growl('Batch processing has finished. <a class="loadFiles">Click here</a> to load new files in the Finder.');
            //$('div.growl a.loadFiles').click( function() { loadFiles(loadImg); $('div.growl').remove(); } );
            
            growl('Batch processing has finished.', 1000);
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
        var startTime, showTem;
        // only waiting queueItems can be started
        if (thisItem.status !== 'waiting') { return false; }

        thisItem.status = 'active';
        thisItem.menuItem.addClass('active').removeClass('waiting');
        startTime = new Date();

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
                    if (data.newFileName !== undefined) {
                        // add file to finder
                        if ($.isArray(data.newFileName)) {
                            $.each(data.newFileName, function(i, f) {
                                WM.finder.addFile(f);
                            })
                        } else {
                            WM.finder.addFile(data.newFileName);
                        }
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

                WM.queue.next();
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
            WM.queue.next();
            return true;
        }
        return false;
    };

    thisItem.destroy = function() {
        if (thisItem.status == 'active') { return false; }

        thisItem.menuItem.remove();
        thisItem.status = 'complete';
        WM.queue.queueCountUpdate();
        return true;
    };

    WM.queue.add(thisItem);
}
//====================================
// !AVERAGE AND TRANSFORM FUNCTIONS
//====================================

function addToRecents(data) {
    //console.log('addToRecents(' + JSON.stringify(data) + ')');
    if (typeof data !== 'object' || typeof data.img !== 'string' || typeof data.tem !== 'string') {
        console.log('Cannot add to Recently Created Images');
        return false;
    }

    $.post('/scripts/log', data); // post info to the log

    data.savefolder = WM.project.id + "/.tmp/";

    var theImg = "/scripts/fileAccess?file=" + data.savefolder + data.img;
    var theTem = "/scripts/fileAccess?file=" + data.savefolder + data.tem;

    var $newimage = $('<img />').addClass('tcimage').attr('src', theImg).data(data).removeData('error errorText');
    $newimage.insertAfter($("#recent_creations h2"));
    $newimage.click(); // click to load into main image window

    $newimage.draggable({
        helper: 'clone',
        opacity: 0.7,
        revert: true
    });

    // enable save button
    $('#save-button').filter(':visible').button({ disabled: false });
    $('#trans-save-button').filter(':visible').button({ disabled: false });
}


function imgSave() {
    var $imgBox,
        tem,
        img;

    if (WM.appWindow == 'average') {
        $imgBox = $('#average');
    } else if (WM.appWindow == 'transform') {
        $imgBox = $('#transform');
    } else {
        return false;
    }

    $imgBox.data('savefolder', WM.project.id + "/.tmp/");
    tem = $imgBox.data('savefolder') + $imgBox.data('tem');
    img = $imgBox.data('savefolder') + $imgBox.data('img');
    $('<div />').html('Name: <input type="text" />').dialog({
        title: "Save image with name...",
        open: function(e, ui) {
            $(this).find('input').focus().val(urlToName(currentDir()));
        },
        buttons: {
            Cancel: function() { $(this).dialog("close"); },
            'Save': {
                text: 'Save',
                class: 'ui-state-focus',
                click: function() {
                    var savename;

                    $(this).dialog("close");
                    savename = WM.project.id + '/' + $(this).find('input').val();
                    savename.replace(/\/\//g, '/');
                    $.ajax({
                        url: 'scripts/fileSave2',
                        data: {
                            tags: 'average',
                            img: img,
                            tem: tem,
                            name: savename,
                            desc: getDesc()
                        },
                        success: function(data) {
                            if (!data.error[0]) {
                                $('#footer').html(data.newFileName + ' saved');
                                loadFiles(data.newFileName);
                            } else {
                                $('<div title="Problem Saving Image" />').html(data.errorText).dialog();
                            }

                        }
                    });
                }
            }
        }
    });
}

function saveImage(data) { console.log('saveImage(' + (typeof data === 'object' ? JSON.stringify(data) : '') + ')');
    // requires data.outname
    // requires data.data or (data.img & data.tem)
    // optional data.desc (defaults to ''), data.async (deaults to true)
    // option complete (function)

    var errorReport = {
        error: false,
        errrorText: ''
    };

    if (typeof data !== 'object') {
        errorReport = { error: true, errorText: 'No data passed' };
        return errorReport;
    }

    data.async = (typeof data.async === 'boolean') ? data.async : true;
    data.desc = (typeof data.desc === 'string') ? data.desc : '';
    data.img = (typeof data.img === 'string') ? data.img : '';
    data.tem = (typeof data.tem === 'string') ? data.tem : '';

    if (typeof data.data === 'object') {
        data.img = (typeof data.data.img === 'string') ? WM.project.id + '/.tmp/' + data.data.img : '';
        data.tem = (typeof data.data.tem === 'string') ? WM.project.id + '/.tmp/' + data.data.tem : '';

        delete data.data.img;
        delete data.data.tem;
        delete data.data.error;
        delete data.data.errorText;
        data.desc = JSON.stringify(data.data);
    }

    // check that required attributes are there
    if (typeof data.outname !== 'string') {
        errorReport = { error: true, errorText: 'The outname was not defined' };
        return errorReport;
    } else if (data.img.length === 0 || data.tem.length === 0) {
        errorReport = { error: true, errorText: 'The image or tem to save were not defined' };
        return errorReport;
    }

    // no problems, so save!
    $.ajax({
        url: 'scripts/fileSave2',
        async: data.async,
        data: {
            img: data.img,
            tem: data.tem,
            name: data.outname,
            desc: data.desc
        },
        success: function(data2) {
            errorReport = data2;
        },
        error: function(xmlReq, txtStatus, errThrown){
            errorReport = { error: true, errorText: xmlReq.responseText };
        },
        complete: function() {
            if (typeof data.complete === 'function') { data.complete(errorReport); }
        }
    });

    return errorReport;
}

function loadRecentCreation(img) {
    // show image in main window when clicked
    var $imgBox;
    if (WM.appWindow == 'average') {
        $imgBox = $('#average');
        $imgBox.css('background-image', "url(" + $(img).attr('src') + ")");
        $('#save-button').button({
            disabled: false
        });
    } else if (WM.appWindow == 'transform') {
        $imgBox = $('#transform');
        $imgBox.attr('src', $(img).attr('src'));
        $('#trans-save-button').button({
            disabled: false
        });
    } else {
        return false;
    }

    $imgBox.removeData(); // remove all previously-stored data
    $imgBox.data($(img).data()); // add all data for this image

    if ($(img).attr('averaged') != null) {
        if (WM.appWindow == 'transform') {
            // blank the transform interface
            $("#transimage").attr('src', WM.blankImg);
            $("#toimage").attr('src', WM.blankImg);
            $("#fromimage").attr('src', WM.blankImg);
        }
    } else if ($(img).attr('transimage') != null) {

        if (WM.appWindow == 'transform') {
            // set up transform interface
            $("#transimage").attr('src', fileAccess($(img).data('transimg')));
            $("#toimage").attr('src', fileAccess($(img).data('toimg')));
            $("#fromimage").attr('src', fileAccess($(img).data('fromimg')));
            $("#shapePcnt0").val($(img).data('shape')*100);
            $("#colorPcnt0").val($(img).data('color')*100);
            $("#texturePcnt0").val($(img).data('texture')*100);
        }
    }
    $('#save-button').button({
        disabled: false
    });
}

function averageListCheck() {
    var $avgList = $('#average-list li');

    $avgList.each( function() {
        var url = $(this).data('url'); console.debug(url);
        var $dup =  $('#average-list li[data-url="'+url+'"]'); console.debug($dup.length);

        if ($dup.length > 1) {
            $(this).addClass('dupavg');
        } else {
            $(this).removeClass('dupavg');
        }
    });

    var l = $avgList.length;
    $('#footer').html(l + " file" + (l==1 ? '' : 's') + " in average");

    checkAvgAbility();
}

function checkAvgAbility() {
    var canDo = true;

    canDo = canDo && (filesGetSelected().length > 1 ||  $('#average-list li').length > 1);

    $('#view-average-button').button({ disabled: !canDo });
}

function getAverage(tVars, addToQueue) {
    //console.debug('getAverage(' + (typeof tVars === 'object' ? JSON.stringify(tVars) : '') + ', ' + addToQueue + ')');

    var errorReport = {
        error: false,
        errrorText: ''
    };

    if (typeof tVars === 'undefined') { tVars = {}; }

    // get variables from tVars if defined, or from interface
    tVars.async = (typeof tVars.async === 'boolean') ? tVars.async : true;

    var sampleContours = (typeof tVars.sampleContours === 'boolean')
        ? tVars.sampleContours
        : ($('#sample_contours').prop('checked')) ? "true" : "false";
    var texture = (typeof tVars.texture === 'boolean')
        ? tVars.texture
        : ($('#texture').prop('checked') == 1) ? "true" : "false";
    var norm = (typeof tVars.norm === 'string')
        ? tVars.norm
        : $('#normalisation').val();

    var theData = {
        subfolder: WM.project.id,
        savefolder: '/.tmp/',
        count: 1,
        texture0: texture,
        norm0: norm,
        normPoint0_0: $('#align_pt1').val(),
        normPoint1_0: $('#align_pt2').val(),
        format0: $('#default_imageformat').val(),
        images0: []
    };
    var $average = $('#average');

    if (typeof tVars.images === 'object') {
        theData.images0 = tVars.images;
    } else {
        // get images from average-list or (if empty) selected files in finder
        $('#average-list li').each(function() {
            theData.images0.push(urlToName($(this).data('url')));
        });

        if (!theData.images0.length) {
            var imgList = {};

            $finder.find('li.file.image.selected').each(function() {
                var name = $(this).attr('url').replace('.tem', '').replace('.jpg', '').replace('.gif', '').replace('.png', '');
                var theJPG = $finder.find('li.jpg[url="'+name+'.jpg"]');
                var thePNG = $finder.find('li.png[url="'+name+'.png"]');
                var theGIF = $finder.find('li.gif[url="'+name+'.gif"]');
                var theTEM = $finder.find('li.tem[url="'+name+'.tem"]');
                if ((theJPG.length || thePNG.length || theGIF.length) && theTEM.length) {
                    imgList[urlToName($(this).attr('url'))] = true;
                }
            });

            theData.images0 = Object.keys(imgList);
        }
    }

    if (theData.images0.length === 0) {
        growl("No images with tems were selected", 1500);
        return false;
    } else if (theData.images0.length == 1) {
        growl("You've only selected one image", 1500);
        return false;
    }

    $('#view-average-button, #save-button').button({ disabled: true });

    var min = Math.min($average.width(), $average.height());
    var max = Math.max($average.width(), $average.height());
    var $spinner = spinner({
        'font-size':  min * 0.85,
        'margin-top': ($average.height() - (min * 0.85) ) / 2
    });
    $average.hide().after($spinner);
    $('#average-list').hide();

    // add to queue or create average now
    if (typeof addToQueue == 'boolean' && addToQueue === true) {
        var q = new queueItem({
            url: 'tcAverage',
            ajaxdata: { theData: theData, outname: tVars.outname },
            msg: 'Average ' + theData.images0.length + ' images: ' + tVars.outname
        });
    } else {
        // get first image dimensions and estimate average time
        var avgTimer = null;

        $.ajax({
            url: 'scripts/imgDimensions',
            type: 'GET',
            data: { img: WM.project.id + theData.images0[0] },
            success: function(data) {
                if (data.w > 0 && data.h > 0) {
                    var loadtime = 1.0;
                    var px = data.w * data.h;
                    var n = theData.images0.length;
                    var tx = (theData.texture0 == 'true') ? 1 : 0;
                    var mt = 7.39     + (px * n * 0.000401)
                                    + (n * tx * -622.1)
                                    + (px * n * tx * 0.0005446);
                    var mtsec = loadtime + Math.round(mt/1000);

                    var d = new Date();
                    var startAvgTime = d.getTime();

                    avgTimer = setInterval(function() {
                        var nowTime = new Date();
                        var avgInterval = Math.round((nowTime.getTime() - startAvgTime)/1000);
                        $('#footer').html('This average will take about ' + mtsec + ' seconds (' + avgInterval + ')');
                    }, 1000);

                }
            }
        });

        $.ajax({
            url: '/tomcat/psychomorph/avg',
            async: tVars.async,
            data: $.param(theData, true),
            success: function(data) {
                //alert(JSON.stringify(data));
                if (data[0].error) {
                    $average.css('background-image', WM.blankBG);
                    $('<div title="There was an error with your average" />').html(data[0].errorText).dialog();
                } else {
                    addToRecents(data[0]);
                    $('#average-list li').remove();

                    if (typeof tVars.outname === 'string') {
                        var saveData = {
                            data: data[0],
                            outname: tVars.outname,
                            async: tVars.async,
                        };
                        if (typeof tVars.completeSave === 'function') { saveData.complete = tVars.completeSave; }
                        saveImage(saveData);
                    }
                }
            },
            error: function(xmlReq, txtStatus, errThrown){
                growl(xmlReq.responseText);
                $average.css('background-image', WM.blankBG);
            },
            complete: function() {
                clearInterval(avgTimer);
                $('#footer').html("Average complete");
                if (typeof tVars.completeAvg === 'function') { tVars.completeAvg(errorReport); }
                $spinner.remove();
                $average.show();
            }
        });
    }
}

function checkTransAbility() {
    var canDo = true;
    canDo = canDo && ($('#transimg').attr('src') != WM.blankImg);
    canDo = canDo && ($('#fromimage').attr('src') != WM.blankImg);
    canDo = canDo && ($('#toimage').attr('src') != WM.blankImg);
    //canDo = canDo && $("#shapePcnt0").val() !== '';

    $('#transButton').button({ disabled: !canDo });
}

function getTransform(tVars, addToQueue) {
    //console.debug('getTransform(' + (typeof tVars === 'object' ? JSON.stringify(tVars) : '') + ')');
    $('#footer').html("Starting Transform...");

    var errorReport = {
        error: false,
        errrorText: ''
    };

    if (typeof tVars === 'undefined') { tVars = {}; }

    // get variables from tVars if defined, or from interface
    tVars.async = (typeof tVars.async === 'boolean') ? tVars.async : true;

    var transimage = (typeof tVars.transimage === 'string')
        ? tVars.transimage
        : urlToName($("#transimage").attr('src'));
    var fromimage = (typeof tVars.fromimage === 'string')
        ? tVars.fromimage
        : urlToName($("#fromimage").attr('src'));
    var toimage = (typeof tVars.toimage === 'string')
        ? tVars.toimage
        : urlToName($("#toimage").attr('src'));
    var shapePcnt = (typeof tVars.shapePcnt === 'number')
        ? tVars.shapePcnt/100
        : parseFloat($("#shapePcnt0").val()) / 100;
    var startShapePcnt = (typeof tVars.startShapePcnt === 'number')
        ? tVars.startShapePcnt/100
        : parseFloat($("#startShapePcnt").val()) / 100;
    var endShapePcnt = (typeof tVars.endShapePcnt === 'number')
        ? tVars.endShapePcnt/100
        : parseFloat($("#endShapePcnt").val()) / 100;
    var colorPcnt = (typeof tVars.colorPcnt === 'number')
        ? tVars.colorPcnt/100
        : parseFloat($("#colorPcnt0").val()) / 100;
    var startColorPcnt = (typeof tVars.startColorPcnt === 'number')
        ? tVars.startColorPcnt/100
        : parseFloat($("#startColorPcnt").val()) / 100;
    var endColorPcnt = (typeof tVars.endColorPcnt === 'number')
        ? tVars.endColorPcnt/100
        : parseFloat($("#endColorPcnt").val()) / 100;
    var texturePcnt = (typeof tVars.texturePcnt === 'number')
        ? tVars.texturePcnt/100
        : parseFloat($("#texturePcnt0").val()) / 100;
    var startTexturePcnt = (typeof tVars.startTexturePcnt === 'number')
        ? tVars.startTexturePcnt/100
        : parseFloat($("#startTexturePcnt").val()) / 100;
    var endTexturePcnt = (typeof tVars.endTexturePcnt === 'number')
        ? tVars.endTexturePcnt/100
        : parseFloat($("#endTexturePcnt").val()) / 100;
    var sampleContours = (typeof tVars.sampleContours === 'boolean')
        ? tVars.sampleContours
        : ($('#sample_contours').prop('checked')) ? "true" : "false";
    var steps = (typeof tVars.steps === 'number')
        ? parseInt(tVars.steps)
        : ($('#transMovieSteps').filter(':visible').length) ? parseInt($("#transMovieSteps").val()) : 0;

    if (transimage == "" || fromimage == "" || toimage == "") {
        growl("You must drag images to each of the first three boxes");
    } else if (steps > 0 && endShapePcnt == startShapePcnt && startColorPcnt == endColorPcnt) {
        $('#footer').html("The start and end percent of either shape or color transformation must be different");
        $("#endShapePcnt").focus().select();
    } else if (steps === 0 && isNaN(shapePcnt)) {
        $('#footer').html('Please set the shape transformation. Set it to 0 if you do not want shape to alter.');
        $("#shapePcnt0").focus().select();
    } else if (steps === 0 && isNaN(colorPcnt)) {
        $('#footer').html('Please set the color transformation. Set it to 0 if you do not want color to alter.');
        $("#colorPcnt0").focus().select();
    } else if (steps === 0 && isNaN(texturePcnt)) {
        $('#footer').html('Please set the texture transformation. Set it to 0 if you do not want texture to alter.');
        $("#texturePcnt0").focus().select();
    } else {
        var theData = {
            subfolder: WM.project.id,
            savefolder: '/.tmp/',
            count: 1
        };
        var images = []; // array for image list if movie
        var imagelength = 0;
        var framename = $('#transMovieFileName').val();
        if (framename.substr(0,1) !== "/") { framename = "/" + framename; }


        for (var i = 0; i <= steps; i++) {
            var tnumber = (steps>0) ? " " + (i+1) : "";
            $('#footer').html("Starting Transform" + tnumber + "...");

            newShapePcnt = (steps === 0) ? shapePcnt : startShapePcnt + (i * (endShapePcnt - startShapePcnt) / steps);
            newColorPcnt = (steps === 0) ? colorPcnt : startColorPcnt + (i * (endColorPcnt - startColorPcnt) / steps);
            newTexturePcnt = (steps === 0) ? texturePcnt : startTexturePcnt + (i * (endTexturePcnt - startTexturePcnt) / steps);
            n = '0';
            theData["shape" + n] = newShapePcnt;
            theData["color" + n] = newColorPcnt;
            theData["texture" + n] = newTexturePcnt;
            theData["sampleContours" + n] = sampleContours;
            theData["transimage" + n] = transimage;
            theData["fromimage" + n] = fromimage;
            theData["toimage" + n] = toimage;
            theData["norm" + n] = $('#normalisation').val();
            theData["warp" + n] = $('#warp').val();
            theData['normPoint0_' + n] = $('#align_pt1').val();
            theData['normPoint1_' + n] = $('#align_pt2').val();
            theData['format' + n] = $('#default_imageformat').val();

            // create transform asynchronously
            $('#transButton, #trans-save-button').button({ disabled: true });

            var thisStep = i;

            // continuum setting
            if (steps > 0) {
                //tVars.async = false;
                addToQueue = true;
                if (framename.length) {
                    tVars.outname = framename + '_' +
                                    pad(thisStep, steps.toString().length, '0') + '.' +
                                    $('#default_imageformat').val();
                } else {
                    // don't save individual files if transMovieFileName is blank
                    tVars.outname = null;
                }
            }

            // add to queue or run now
            if (typeof addToQueue == 'boolean' && addToQueue === true) {
                var thisData = $.extend(true, {}, theData);
                var q = new queueItem({
                    url: 'tcTransform',
                    ajaxdata: { theData: thisData, outname: tVars.outname },
                    msg: 'Transform: ' + tVars.outname,
                });

                if (i == steps) {
                    $('#footer').html("Continuum queued");
                    $('#transButton').button({ disabled: false });
                }
            } else {
                // get image dimensions and estimate transform time
                var transTimer = null;
                var $transform = $("#transform");
                var min = Math.min($transform.width(), $transform.height());
                var max = Math.max($transform.width(), $transform.height());
                var spinSize = min * 0.85;
                var $spinner = spinner({
                    'font-size':  spinSize,
                    'margin-left': ($transform.width() - spinSize ) / 2,
                    'margin-top': ($transform.height() - spinSize ) / 2,
                    'margin-bottom': ($transform.height() - spinSize ) / 2
                });
                $transform.hide().after($spinner);

                $.ajax({
                    url: 'scripts/imgDimensions',
                    type: 'GET',
                    data: { img: theData.transimage0 },
                    success: function(data) {
                        if (data.w > 0 && data.h > 0) {
                            var loadtime = 0.5;
                            var px = data.w * data.h;
                            var sh = (theData.shape0 == 0) ? 0 : 1;
                            var co = (theData.color0 == 0) ? 0 : 1;
                            var tx = (theData.texture0 == 0) ? 0 : 1;
                            var sc = (theData.sampleContours0 == 'true') ? 1 : 0;
                            var mt = -87.9  + (px * 0.000636)
                                            + (px * co * 0.000841)
                                            + (px * co * tx * 0.00159);
                            var mtsec = loadtime + Math.round(mt/1000);

                            var d = new Date();
                            var startTransTime = d.getTime();

                            transTimer = setInterval(function() {
                                var nowTime = new Date();
                                var transInterval = Math.round((nowTime.getTime() - startTransTime)/1000);
                                $('#footer').html('This transform will take about ' + mtsec + ' seconds (' + transInterval + ')');
                            }, 1000);
                        }
                    }
                });

                $.ajax({
                    url: '/tomcat/psychomorph/trans',
                    async: tVars.async,
                    data: theData,
                    success: function(data) {
                        //alert(JSON.stringify(data));
                        var d = data[0];

                        if (d.error) {
                            $transform.attr("src", WM.blankImg);
                            $("#transtem").val('');
                            //$('<div title="There was an error with your transform" />').html(data.errorText).dialog();
                            errorReport.error = true;
                            errorReport.errorText = d.errorText;
                            return false;
                        }

                        addToRecents(d);

                        if (steps > 0) {
                            imagelength++;
                            $('#footer').html(imagelength + ' of ' + (steps+1) + ' images made');
                        }

                        // if an outname is set, save the image
                        if (typeof tVars.outname === 'string') {
                            saveImage({
                                data: d,
                                outname: tVars.outname,
                                async: tVars.async
                            });
                        }

                        // success callback or error reporting
                        if (typeof tVars.success === 'function') {
                            tVars.success(errorReport);
                        } else if (errorReport.error) {
                            $('<div title="There was an error with your transform" />').html(errorReport.errorText).dialog();
                        }
                    },
                    error: function(xmlReq, txtStatus, errThrown){
                        growl(xmlReq.responseText);
                        $transform.attr("src", WM.blankImg);
                    },
                    complete: function() {
                        clearInterval(transTimer);
                        $('#footer').html("Transform complete");
                        $transform.show();
                        $spinner.remove();
                        $('#transButton').button({ disabled: false });
                    }
                });
            }
        }
    }
}

$('#sanalysisfilename').blur( function() {
    $('#pca_csv span').text($(this).val());
});
$('#smodelfilename').blur( function() {
    $('#pca_files span').text($(this).val());
    $('#sanalysisfilename').blur();
});

$('#pca_analysis input:radio[name=pcatype]').change(function() {
    var v = $(this).filter(':checked').val();
    if (v == 'oldpca') {
        $('#pca_analysis .pcaopts').show();
        $('#pca_analysis .pcaopts.oldmodel').css('visibility', 'visible');
        $('#pca_analysis .pcaopts.newname').css('visibility', 'hidden');
        $('#pca_files li:not(#pca_csv)').hide();
    } else if (v == 'newpca') {
        $('#pca_analysis .pcaopts').show();
        $('#pca_analysis .pcaopts.oldmodel').css('visibility', 'hidden');
        $('#pca_analysis .pcaopts.newname').css('visibility', 'visible');
        $('#pca_files li:not(#pca_csv)').show();
    } else if (v == 'skippca') {
        $('#pca_analysis .pcaopts').hide();
    }
    $('#pca_analysis').stripe();
});

$('#canalysisfilename').blur( function() {
    $('#pci_csv span').text($(this).val());
});
$('#cmodelfilename').blur( function() {
    $('#pci_files span').text($(this).val());
    $('#canalysisfilename').blur();
});

$('#pca_analysis input:radio[name=pcitype]').change(function() {
    var v = $(this).filter(':checked').val();
    if (v == 'oldpci') {
        $('#pca_analysis .pciopts').show();
        $('#pca_analysis .pciopts.oldmodel').css('visibility', 'visible');
        $('#pca_analysis .pciopts.newname').css('visibility', 'hidden');
        $('#pca_files li:not(#pci_csv)').hide();
    } else if (v == 'newpci') {
        $('#pca_analysis .pciopts').show();
        $('#pca_analysis .pciopts.oldmodel').css('visibility', 'hidden');
        $('#pca_analysis .pciopts.newname').css('visibility', 'visible');
        $('#pca_files li:not(#pci_csv)').show();
    } else if (v == 'skippci') {
        $('#pca_analysis .pciopts').hide();
    }
    $('#pca_analysis').stripe();
});

$('#analysePCA').change(function() {
    if ($(this).prop('checked')) {
        $('#sanalysisfile').css('visibility', 'visible');
        $('#pca_csv').show();
    } else {
        $('#sanalysisfile').css('visibility', 'hidden');
        $('#pca_csv').hide();
    }
});

$('#analysePCI').change(function() {
    if ($(this).prop('checked')) {
        $('#canalysisfile').css('visibility', 'visible');
        $('#pci_csv').show();
    } else {
        $('#canalysisfile').css('visibility', 'hidden');
        $('#pci_csv').hide();
    }
});

function getPCvis() {
    var imgfiles = filesGetSelected('.image');
    var pcafiles = filesGetSelected('.pca');

    if (imgfiles.length == 1 && pcafiles.length == 1) {
        $('#pcVisDialog textarea').val(currentDir() + "newFileName.jpg, " + pcafiles[0] + ", " + imgfiles[0] + ", 1.0");
    }

    $('#pcVisDialog p.warning').remove();

    $('#pcVisDialog').dialog({
        title: 'Visualise Principal Components',
        buttons: {
            Cancel: function() {
                $('#footer').html("");
                $(this).dialog("close");
            },
            "Create": {
                text: 'Create',
                class: 'ui-state-focus',
                click: function() {
                    $('#pcVisDialog input').blur(); // make sure all inputs are blurred so file names are valid
                    $('#pcVisDialog p.warning').remove();

                    var rows = $('#pcVisDialog textarea').val().split("\n");

                    // TODO: add error checking
                    $.each(rows, function(i, r) {
                        var cols = r.split(/[\s,]+/);

                        var ws = [];
                        for (j = 3; j < cols.length; j++) {
                            if (cols[j].trim().length === 0) { break; }
                            ws.push(cols[j] * 100.0);
                        }

                        var d = {
                            subfolder: '', // WM.user.id,
                            pcafile: cols[1],
                            avgfile: cols[2],
                            pc_weights: ws.join(",")
                        };

                        var q = new queueItem({
                            url: 'tcPCvis',
                            ajaxdata: { theData: d, outname: cols[0] },
                            msg: 'Visualise PCs: ' + cols[0]
                        });

                    });

                    $('#pcVisDialog').dialog("close");
                }
            }
        }
    });
}

function getBatchPCA() {  console.log('getBatchPCA()');
    $('#batchPcaDialog textarea').val('').show();
    $('#batchPcaDialog table tbody').empty();
    $('#batchPcaDialog table').show();

    var BAdata = [];

    $('#batchPcaDialog').dialog({
        title: 'Batch PCA',
        buttons: {
            Cancel: function() {
                $('#footer').html("");
                $(this).dialog("close");
            },
            "Reset": function() {
                $('#batchPcaDialog textarea').val('').show();
                $('#batchPcaDialog p.warning').remove();
                $('#batchPcaDialog table tbody').empty();
                $('#batchPcaDialog table').show();
                BAdata = [];
            },
            "PCA": {
                text: 'PCA',
                class: 'ui-state-focus',
                click: function() {
                    $('#footer').html("Checking PCA batch file");
                    $('#batchPcaDialog p.warning').remove();

                    var rows = $('#batchPcaDialog textarea').val().split('\n');
                    var errors = 0;
                    var modelnames = [];
                    var hasDependencies = false;

                    $('#batchPcaDialog textarea').hide();

                    $.each(rows, function(i, r) {
                        var row = $('<tr />');
                        var cols = r.split('\t');
                        var b = false;
                        $.each(cols, function(j, c) {
                            cols[j] = $.trim(cols[j]);
                            if (i === 0) {
                                row.append('<th>' + cols[j] + '</th>');
                                if (cols[j] === '' || $.inArray(cols[j], modelnames) > -1) {
                                    row.find('th:eq(' + j + ')')
                                        .attr('title', 'You must give each model a unique outname.')
                                        .addClass('ui-state-error');
                                    errors++;
                                } else if ($finder.find('li.file[url="' + cols[j] + '"]').length > 0) {
                                    row.find('th:eq(' + j + ')')
                                        .attr('title', 'This model already exists, please give it a different name.')
                                        .addClass('ui-state-error');
                                    errors++;
                                }
                                modelnames.push(cols[j]);
                                BAdata[j] = {
                                    modelname: cols[j],
                                    pca: false,
                                    pci: false,
                                    mask: '',
                                    images: []
                                };
                            } else if (i == 1) { // create shape model? (T/F)
                                row.append('<td>' + cols[j] + '</td>');

                                var createShapeModel = cols[j].toLowerCase();

                                b = false;
                                b = b || (createShapeModel == 'true');
                                b = b || (createShapeModel == 't');
                                b = b || (createShapeModel == '1');

                                BAdata[j].pca = b;
                            } else if (i == 2) { // create colour model? (T/F)
                                row.append('<td>' + cols[j] + '</td>');

                                var createColorModel = cols[j].toLowerCase();

                                b = false;
                                b = b || (createColorModel == 'true');
                                b = b || (createColorModel == 't');
                                b = b || (createColorModel == '1');

                                BAdata[j].pci = b;
                            } else if (i == 3) { // mask name (for colour model
                                row.append('<td>' + cols[j] + '</td>');
                                var possibleMasks = ['frl_face', 'frl_face_neck', 'frl_face_ears', 'frl_face_neck_ears', 'pl_no_ears', 'pl_with_ears'];

                                BAdata[j].mask = cols[j];
                            } else {
                                row.append('<td>' + cols[j] + '</td>');
                                if (cols[j] !== '') {
                                    BAdata[j].images.push(cols[j]);
                                    if ($.inArray(cols[j], modelnames) == -1) {
                                        var theImg = $finder.find('li.image[url="' + cols[j] + '"]');
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
                        $('#batchPcaDialog table').append(row);
                    }); // end $.each(rows, function(i, r)

                    // notify and stop if error are found
                    if (errors > 0) {
                        var errorText = $('<p class="warning" />').html(errors + ' errors were found. Hover over the highlighted boxes for more information.');
                        $('#batchPcaDialog p').insertAfter(errorText);
                        return false;
                    }

                    $('#footer').html('Your batch file was successfully validated.');
                    $('#batchPcaDialog').dialog('close');

                    var texture = ($('#texture').prop('checked') == 1) ? true : false;

                    $.each(BAdata, function(i, d) {
                        var theData = {
                            pca:            d.pca,
                            usepca:            false,
                            pcafile:        d.modelname,
                            analysepca:        false,
                            sanalysisfile:    '',
                            pci:            d.pci,
                            usepci:            false,
                            pcifile:        d.modelname,
                            analysepci:        false,
                            canalysisfile:    '',
                            images:            d.images,
                            texture:        texture,
                            mask:            d.mask
                        };

                        var q = new queueItem({
                            url: 'tcPCA',
                            ajaxdata: { theData: theData, outname: theData.pcafile },
                            msg: 'Create PCA'
                        });
                    });
                }
            }
        }
    });
}


function getPCA() {  console.log('getPCA()');
    var imgfiles = filesGetSelected('.image.hasTem');

    if (imgfiles.length === 0) {
        growl('No template files were selected', 1000);
        return false;
    } else if (imgfiles.length == 1) {
        growl('You need to choose more than 1 image file to create a new PCA', 1000);
    }
    $('#footer').html("Procesing " + imgfiles.length + " images.");

    // populate pca file names
    $('#pcafilename').html('');
    var $existing_models = $finder.find('li.pca');

    if ($existing_models.length > 0) {
        $existing_models.each( function() {
            var mname = $(this).attr('url').replace(/\.pca$/, '');
            var $existing_model = $('<option />').html(mname).val(mname);
            $('#pcafilename').append($existing_model);
        });
        $('#use_existing_pca').show();
        $('#usePCA').prop('checked', true).change();
    } else {
        // no existing models, so remove this option
        $('#use_existing_pca').hide();
        $('#createPCA').prop('checked', true).change();
    }

    // populate pci file names
    $('#pcifilename').html('');
    $existing_models = $finder.find('li.pci');
    if ($existing_models.length > 0) {
        $existing_models.each( function() {
            var mname = $(this).attr('url').replace(/\.pci$/, '');
            var $existing_model = $('<option />').html(mname).val(mname);
            $('#pcifilename').append($existing_model);
        });
        $('#use_existing_pci').show();
        $('#usePCI').prop('checked', true).change();
    } else {
        // no existing models, so remove this option
        $('#use_existing_pci').hide();
        $('#createPCI').prop('checked', true).change();
    }

    $('#smodelfilename, #cmodelfilename').val(currentDir() + "_model").blur();
    $('#sanalysisfilename, #canalysisfilename').val(currentDir() + "_analysis").blur();

    $('#pcaDialog p.warning').remove();
    $('#pcaDialog').dialog({
        title: 'PCA ' + imgfiles.length + ' File' + ((imgfiles.length == 1) ? '' : 's'),
        buttons: {
            Cancel: function() {
                $('#footer').html("");
                $(this).dialog("close");
            },
            "PCA": {
                text: 'PCA',
                class: 'ui-state-focus',
                click: function() {
                    $('#footer').html("Starting PCA");
                    $('#pcaDialog input').blur(); // make sure all inputs are blurred so file names are valid
                    $('#pcaDialog p.warning').remove();

                    var pcafile = $('#usePCA').prop('checked') ? $('#pcafilename').val() : $('#smodelfilename').val().replace(/\.pca$/, '');
                    var pcifile = $('#usePCI').prop('checked') ? $('#pcifilename').val() : $('#cmodelfilename').val().replace(/\.pci$/, '');

                    var analysepca = $('#skipPCA').prop('checked') ? false : $('#analysePCA').prop('checked');
                    var analysepci = $('#skipPCI').prop('checked') ? false : $('#analysePCI').prop('checked');

                    var theData = {
                        pca:            !$('#skipPCA').prop('checked'),
                        usepca:         $('#usePCA').prop('checked'),
                        pcafile:        pcafile,
                        analysepca:     analysepca,
                        sanalysisfile:  $('#sanalysisfilename').val().replace(/\.shape\.csv$/, '').replace(/\.csv$/, ''),
                        pci:            !$('#skipPCI').prop('checked'),
                        usepci:         $('#usePCI').prop('checked'),
                        pcifile:        pcifile,
                        analysepci:     analysepci,
                        canalysisfile:  $('#canalysisfilename').val().replace(/\.color\.csv$/, '').replace(/\.csv$/, ''),
                        images:         imgfiles,
                        texture:        ($('#texture').prop('checked') == 1) ? true : false,
                        mask:           $('#pci_mask').val()
                    };

                    var q = new queueItem({
                        url: 'tcPCA',
                        ajaxdata: { theData: theData, outname: theData.pcafile },
                        msg: 'Create PCA'
                    });

                    $('#pcaDialog').dialog("close");
                }
            }
        }
    });

}

function createContinua() {
    // check data
    var imgList = [];
    $('#continua-imgs img:visible').each( function(i, v) {
        var imgname = urlToName($(this).attr('src'));
        if (imgname != WM.blankImg) {
            imgList.push(imgname);
        }
    });

    if (imgList.length < 2) {
        growl("You need to specify at least 2 images.");
        return false;
    }

    var nImgs = imgList.length;
    var csteps = parseInt($('#csteps').val());
    if (csteps < 1) {
        growl("You need at least 2 steps per continuum.");
        return false;
    } else if (csteps > 101) {
        growl("You cannot have more than 101 steps per continuum.");
        return false;
    }

    var savedir = $('#continuaSaveDir').val();
    if (savedir.substr(0, 1) != '/') savedir = '/' + savedir;
    if (savedir.substr(-1) == '/') savedir = savedir.substr(0, savedir.length - 1);

    var shape = ($('#continua-shape').val() == 'on') ? 1 : 0;
    var color = ($('#continua-color').val() == 'on') ? 1 : 0;
    var texture = ($('#continua-texture').val() == 'on') ? 1 : 0;



    var cData = [];
    $.each(imgList, function(j) {

        var fromImg = imgList[j];
        var toImg = imgList[(j+1)%nImgs];

        for (var i = 0; i < csteps; i++) {
            var pcnt = i * 100 / (csteps - 1);
            // top row
            var name = savedir + '/' + pad(j,2) + '_' + pad(i, 2) + '.jpg';
            var tVars = {
                transimage: fromImg,
                fromimage: fromImg,
                toimage: toImg,
                shapePcnt: pcnt * shape,
                colorPcnt: pcnt * color,
                texturePcnt: pcnt * texture,
                outname: name
            };
            getTransform(tVars, true);
        }
    });
}

function createGrid() {
    // check data
    var hsteps = parseInt($('#hsteps').val());
    var vsteps = parseInt($('#vsteps').val());
    var hdim = $('#hdim').val();
    var vdim = $('#vdim').val();

    var topL = urlToName($('#topleft').attr('src'));
    var topR = urlToName($('#topright').attr('src'));
    var botL = urlToName($('#bottomleft').attr('src'));
    var botR = urlToName($('#bottomright').attr('src'));

    var shape = ($('#grid-shape').val() == 'on') ? 1 : 0;
    var color = ($('#grid-color').val() == 'on') ? 1 : 0;
    var texture = ($('#grid-texture').val() == 'on') ? 1 : 0;

    var savedir = $('#gridSaveDir').val();

    // make sure savedir is valid
    if (savedir.substr(0, 1) != '/') savedir = '/' + savedir;
    if (savedir.substr(-1) == '/') savedir = savedir.substr(0, savedir.length - 1);

    // make sure steps are sensible
    if (hsteps < 3 && vsteps < 3) {
        growl('At least one dimension must have 3 or more steps');
        return false;
    } else if (hsteps > 101 || vsteps > 101) {
        growl('Both dimensions must have less than 102 steps');
        return false;
    }
    var savedImages = 0;
    var gridData = [];
    var gridNames = new Array(vsteps);
    $.each(gridNames, function(i) {
        gridNames[i] = [];
    });
    // create top and bottom row bases
    for (var i = 0; i < hsteps; i++) {
        var pcnt = i * 100 / (hsteps - 1);
        // top row
        var name = savedir + '/' + vdim + '00_' + hdim + pad(i, 2);
        gridNames[0].push(name);
        gridData.push({
            'transimage': topL,
            'fromimage': topL,
            'toimage': topR,
            'shapePcnt': pcnt * shape,
            'colorPcnt': pcnt * color,
            'texturePcnt': pcnt * texture,
            'outname': WM.project.id + name
        });
        if (vsteps > 1) {
            //bottom row
            name = savedir + '/' + vdim + pad(vsteps - 1, 2) + '_' + hdim + pad(i, 2);
            gridNames[vsteps - 1].push(name);
            gridData.push({
                'transimage': botL,
                'fromimage': botL,
                'toimage': botR,
                'shapePcnt': pcnt * shape,
                'colorPcnt': pcnt * color,
                'texturePcnt': pcnt * texture,
                'outname': WM.project.id + name
            });
        }
    }
    // create vertical columns
    $.each(gridNames[0], function(i, t) {
        for (var j = 1; j < vsteps - 1; j++) {
            var pcnt = j * 100 / (vsteps - 1);
            var name = savedir + '/' + vdim + pad(j, 2) + '_' + hdim + pad(i, 2);
            gridNames[j].push(name);
            gridData.push({
                'transimage': t,
                'fromimage': t,
                'toimage': gridNames[vsteps - 1][i],
                'shapePcnt': pcnt * shape,
                'colorPcnt': pcnt * color,
                'texturePcnt': pcnt * texture,
                'outname': WM.project.id + name
            });
        }
    });

    // set up progress bar
    var batchTotal = gridData.length;
    var $progressUpdate = $('<p />').html('0 of ' + batchTotal + ' images processed').show();
    var $errorList = $('<ol />').html('').css('clear', 'both').css('max-height', '10em').css('overflow', 'auto').show();
    var $progressBar = $('<div />').addClass('progressBar').css('width', '0');

    var $updatebox = $('<div />');
    $updatebox.append($progressUpdate)
                .append('<div class="progressBox"></div>')
                .find('.progressBox')
                .append($progressBar)
                .append($errorList);
    $updatebox.dialog({
        title: 'Creating Grid',
    });

    $.each(gridData, function(i, d) {
        d.async = false;
        d.success = function(data) {
            savedImages++;
            $progressUpdate.html('Saved ' + savedImages + ' of ' + batchTotal + ' images.');
            $progressBar.css('width', (100 * savedImages / batchTotal) + '%');
        };
        getTransform(d);

        if (i == gridData.length - 1) {
            $progressUpdate.html('Concatenating ' + gridData.length + ' images...');
            // concatenate all images
            $.ajax({
                async: false,
                url: '/scripts/imgConcat',
                data: {
                    project: WM.project.id,
                    gridNames: gridNames,
                    savedir: savedir,
                    topL: topL,
                    topR: topR,
                    botL: botL,
                    botR: botR
                },
                success: function(data) {
                    if (data.error) {
                        $updatebox.html(JSON.stringify(data));
                    } else {
                        $updatebox.html('<h2>' + savedImages + ' images saved</h2><img src="' + fileAccess(data.newFileName) + '" style="width: 300px" />');
                        $updatebox.dialog('option', 'position', 'center');
                        loadFiles(data.newFileName);
                    }
                }
            });
        }
    });
}

function getDesc() {
    var $imgBox;
    if (WM.appWindow == 'transform') {
        $imgBox = $('#transform');
    } else if (WM.appWindow == 'average') {
        $imgBox = $('#average');
    } else {
        return '';
    }

    // copy data over to a new json object
    var data = {};
    $.each($imgBox.data(),function(name,value) {
        if (typeof value === 'string') {
            data[name] = value;
        }
    });

    // remove unneeded data items
    delete data.img;
    delete data.tem;

    var desc = JSON.stringify(data);

    return desc;
}

function movingGif() {
    var files,
    incImage,
        imgN = 0,
        $mbox;

    files = filesGetSelected('.image');
    if (files.length === 0) {
        growl('No image files were selected', 1000);
        return false;
    }

    if ($('#movieRev').prop('checked')) {
        $('#moviePauseSection').show();
    } else {
        $('#moviePauseSection').hide();
    }
    calcMovieLen();
    $mbox = $('#movieBox');
    $mbox.attr("src", fileAccess(files[0]));

    incImage = setInterval(function() {
        var idx,
            modFL,
            modFL2;

        // handle image reversal
        imgN++;
        modFL = imgN%files.length;
        modFL2 = imgN%(2*files.length);

        if ($('#movieRev').prop('checked') && modFL !== modFL2) {
            idx = modFL2 - (modFL*2) - 1;
        } else {
            idx = modFL;
        }

        $mbox.attr("src", fileAccess(files[idx]));
    }, 500);

    $('#movieFileName').val(urlToName(currentDir()) + '_movie');

    $('#movieDialog').dialog({
        title: "Make Moving Gif",
        beforeClose: function() {
            clearInterval(incImage);
            $('#refresh').click();
        },
        buttons: {
            Cancel: function() {
                $(this).dialog("close");
            },
            "Make Movie": function() {
                var $spinner,
                    newFileName;

                clearInterval(incImage);
                $('#footer').html('Making movie...');

                $spinner = spinner({
                    'font-size':  $mbox.height(),
                    'margin': 'auto'
                });
                $mbox.hide().after($spinner);

                // clean the filename
                newFileName = $('#movieFileName').val();
                if (!newFileName.length) {
                    newFileName = currentDir() + '_movie';
                    $('#movieFileName').val(urlToName(currentDir()) + '_movie');
                } else if (newFileName[0] !== '/') {
                    newFileName = WM.project.id + '/' + newFileName;
                    $('#movieFileName').val('/' + newFileName);
                } else {
                    newFileName = WM.project.id + newFileName;
                }
                newFileName = newFileName.replace(/\.(gif|png|jpg)$/, '');

                $.ajax({
                    url: 'scripts/img2Movie',
                    data: {
                        images: files,
                        speed: $('#movieSpeed').slider('value') / 10,
                        pause: $('#moviePause').slider('value') / 10,
                        loops: 0,
                        new_height: $('#movieHeight').slider('value'),
                        rev: ($('#movieRev').prop('checked') ? 'true' : 'false'),
                        newFileName: newFileName
                    },
                    success: function(data) {
                        $mbox.attr("src", fileAccess(data.gif)).show();
                        $spinner.remove();
                        loadFiles(data.gif);
                        $('#footer').html('Movie created');
                    },
                    error: function() {
                        $spinner.remove();
                        $('#footer').html('Error creating movie');
                    }
                });
            },
        }
    });
}
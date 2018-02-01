// functions that should happen ONLOAD

if (navigator.userAgent.indexOf('Mac OS X') != -1) {                            // change cmd-key image for mac/pc
    $("body").addClass("mac");
} else {
    $("body").addClass("pc");
}

$.Finger.pressDuration == 1000;

$('div.modal').on('change', 'input.patchcolor', function() {
    var checked = $(this).is(':checked');
    console.log(checked);
    $(this).closest('div.modal').find('div.patchcolor').toggle(checked);
    $(this).closest('div.modal').find('.rgb_chooser, .rgba_text').toggle(!checked);
});

$(document).on('dblclick', '.ui-dialog-titlebar', function() { 
    console.log('dblclicky');
    var $mydialog = $(this).next('.ui-dialog-content');
    var $buttons = $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane');
    var myheight = $mydialog.dialog("option", "height"); 
    if (myheight == 0) {
        $mydialog.dialog("option", "height", $mydialog.data("myheight"));
        $buttons.show();
    } else {
        $mydialog.dialog("option", "height", 0);
        $mydialog.data("myheight", myheight);
        $buttons.hide();
    }
});

// ! ****** Contextmenu ******
$(document).on('contextmenu', '#finder *, #delin, #threeD', function(e) {
    e.preventDefault();
}).on('contextmenu', '#trash > span', function(e) {                             // contextmenus for trash
    var item_info = [];

    e.stopPropagation();

    item_info = [
        {
            name: 'Empty Trash',
            readOnly: true,
            func: function() {
                emptyTrash();
                $('.context_menu').remove();
            }
        },
        {
            name: 'Hide',
            func: function() {
                $('#toggletrash').click();
                $('.context_menu').remove();
            }
        }
    ];

    context_menu(item_info, e);
}).on('contextmenu', '#finder li.folder:not(#trash)', function(e) {             // contextmenus for folders
    var folder_name,
        item_info = [],
        upload = {},
        cutlist = 0,
        pasteName;

    e.stopPropagation();

    folder_name = $(this).find('> span').click().text();

    if ($finder.find('>ul> li.folder:eq(0) > span').text() == folder_name) {
        // folder is base directory
        folder_name = 'Base Folder';
    } else {
        item_info.push({
            name: 'Rename',
            readOnly: true,
            func: function() {
                folderRename();
                $('.context_menu').remove();
            }
        });

        item_info.push({
            name: 'Copy to Project',
            func: function() {
                folderMoveProject();
                $('.context_menu').remove();
            }
        });

        item_info.push('break');
    }

    if (WM.pasteBoard.length) {
        $.each(WM.pasteBoard, function(i, v) {
            if ($('li.to_cut[url="' + v + '"]').length) {
                cutlist++;
            }
        });

        pasteName = (WM.pasteBoard.length == cutlist) ? 'Move ' : 'Copy ';
        pasteName += WM.pasteBoard.length + ' file';
        pasteName += (WM.pasteBoard.length>1 ? 's' : '');
        pasteName += ' to ' + folder_name;

        item_info.push({
            name: pasteName,
            readOnly: true,
            func: function() {
                $('#pasteItems').click();
                $('.context_menu').remove();
            }
        });
        item_info.push('break');
    }

    item_info.push({
        name: 'Upload to ' + folder_name,
        readOnly: true,
        func: function() {
            $('#upload').click();
            $('.context_menu').remove();
        }
    });

    context_menu(item_info, e);
}).on('contextmenu', '#finder li.file > span', function(e) {                    // contextmenus for files
    var $file,
        tf,
        total_files,
        func = {},
        item_info = [];

    e.stopPropagation();

    $file = $(this).closest('li.file').addClass('selected');
    WM.finder.updateSelectedFiles();
    tf = $finder.find('li.file.selected').length;
    total_files = (tf > 1) ? ' ' + tf + ' Files' : '';

    func.delin = {
        name: 'Delineate',
        func: function() {
            $file.dblclick();
            $('.context_menu').remove();
        }
    };

    func.rename = {
        name: 'Rename',
        readOnly: true,
        func: function() {
            fileRename();
            $('.context_menu').remove();
        }
    };

    func.copy = {
        name: 'Copy' + total_files,
        readOnly: true,
        func: function() {
            $('#copyItems').click();
            $('.context_menu').remove();
        }
    };

    func.cut = {
        name: 'Cut' + total_files,
        readOnly: true,
        func: function() {
            $('#cutItems').click();
            $('.context_menu').remove();
        }
    };

    func.del = {
        name: 'Move' + total_files + ' to Trash',
        readOnly: true,
        func: function() {
            $('#deleteItems').click();
            $('.context_menu').remove();
        }
    };

    func.download = {
        name: 'Download' + total_files,
        func: function() {
            $('#download').click();
            $('.context_menu').remove();
        }
    };


    if ($file.hasClass('image') || $file.hasClass('tem')) {
        if (tf == 1) {
            item_info = [
                func.delin, 'break',
                func.copy, func.cut, func.del, 'break',
                func.rename, 'break',
                func.download
            ];
        } else {
            item_info = [
                func.copy, func.cut, func.del, 'break',
                func.download
            ];
        }
    } else {
        if (tf == 1) {
            item_info = [
                func.copy, func.cut, func.del, 'break',
                func.rename, 'break',
                func.download
            ];
        } else {
            item_info = [
                func.copy, func.cut, func.del, 'break',
                func.download
            ];
        }
    }

    context_menu(item_info, e);
}).on('mouseleave', '.context_menu', function(e) {                              // close contextmenus on mouseleave
    $(this).remove();
});

// !#project_list
$('#project_list').on('click', '.go_to_project', function() {                   // go to project from projectlist
    var proj_id;

    $.xhrPool.abortAll();
    proj_id = $(this).closest('tr').data('id');
    projectSet(proj_id);
}).on('click', '.delete_project', function() {                                  // delete a project
    var proj_id;

    proj_id = $(this).closest('tr').data('id');
    projectDelete(proj_id);
}).on('click', 'tr[data-perm=all] .projectOwnerDelete', function() {            // delete a project owner (only perm-all project)
    var proj_id;

    proj_id = $(this).closest('tr').data('id');
    projectOwnerDelete(proj_id, $(this).data('id'));
}).on('click', '.project_owners_toggle', function() {                           // toggle project owner visibility
    $(this).toggleClass('vis').siblings('.project_owners').toggle();
}).on('keydown', 'tr[data-perm=all] .projectOwnerAdd', function(e) {
    if (e.which == KEYCODE.enter) { projectOwnerAdd(this); }
}).on('doubletap', 'tr[data-perm=all] td.project_name', function() {            // edit project name (only perm-all project)
    projectEdit(this, "name");
}).on('doubletap', 'tr[data-perm=all] td.project_desc', function() {            // edit project description (only perm-all project)
    projectEdit(this, "notes");
}).on('click', '.ownerPermToggle', projectOwnerPermToggle);                     // toggle project member permissions

// ! ***** Window events *****
$(window).bind('resize', sizeToViewport)
.blur(function() {
    // removes growl notifications when download window is ready
    $('div.growl').remove();
}).on('hashchange', hashChange);                                                // functions when hash changes (in functions.js file)

// show and hide menubar
$('#menubar:visible li.menucategory').mouseleave(function() {
    $(this).find('>ul').hide();
}).mouseenter(function() {
    $(this).find('>ul').show();
}).find('>ul>li').click(function() {
    if (!$(this).hasClass('disabled')) {
        // log all menu function calls
        // console.debug('menu: #' + this.id + '.click()');
        $(this).parent('ul').hide();
        $('#toggleMenu:visible').click();
    } else {
       // console.debug('menu: #' + this.id + '.click() (disabled)');
    }
}).mouseenter(function() {
    if (!$(this).hasClass('disabled')) {
        $(this).find('ul.submenu').show();
    }
}).mouseleave(function() {
    // hide after a brief timeout
    var $this = $(this);

    setTimeout(function() {
        if ($this.find('ul.submenu:hover').length === 0) {
            $this.find('ul.submenu').hide();
        }
    }, 300);
});
$('#menubar li.menucategory > span').click(function() {
    var vis = $(this).parent().find('>ul:visible').length;
    $('#menubar li.menucategory > ul').hide();
    if (!vis) {
        $(this).parent().find('>ul').show();
    }
    
    // [FIX] remove sticky hover class after clicking on touchscreen
});
$('#menu_username').click( function() {
    $('#prefs').click();
});
$('#register-button').button().click(function(e) {
    userRegister(e);
});
$('#reset-password-button').button().click( userPasswordReset );
$('#login_password').keyup( function(e) {
    if (e.which === KEYCODE.enter) {
        $('#login-button').click();
    }
});
$('#login-button').button().click( userLogin );
$('#logout').not('.disabled').click( userLogout );

$('#destimages').resizable({
    handles: "e",
    resize: function(e, ui) {
        sizeToViewport();
    },
    minWidth: 200
});
$('#avg_image_box').resizable({
    handles: "e, s, se",
    resize: function(e, ui) {
        sizeToViewport();
    },
    minWidth: 150,
    minHeight: 200
});

$('#average-list').on('click', 'li', function() {
    $(this).toggleClass('selected');
    $finder.find('li.file').removeClass('selected');
});


$('#avg_image_box').droppable({
    hoverClass: 'hoverdrag',
    tolerance: "pointer",
    drop: function(event, ui) {
        var $files = ui.helper.find('li.file.image.hasTem');

        $files.each( function() {
            var url,
                $li;

            url = $(this).attr('url');
            $li = $('<li />').text(urlToName(url))
                            .attr('data-url', url)
                            .css('background-image', 'url(' + fileAccess(url, true) + ')');
            $('#average-list').append($li).show();
            $('#average').hide();
        });

        averageListCheck();
    }
}).on("doubletap", function() {
    // load selected images by double-clicking on average image box
    var files = filesGetSelected('.image.hasTem', WM.project.id);
    
    $.each(files, function(i, filename) {
        var url,
            $li;
            
        url = WM.project.id + filename;

        $li = $('<li />').text(filename)
                        .attr('data-url', url)
                        .css('background-image', 'url(' + fileAccess(url, true) + ')');
        $('#average-list').append($li).show();
        $('#average').hide();
    });

    averageListCheck();
});

$('#destimages img:not(.nodrop), #grid img').droppable({
    hoverClass: 'hoverdrag',
    tolerance: "pointer",
    drop: function(event, ui) {
        this.src = fileAccess($(ui.draggable).attr('url'));
        setTimeout(function() {
            var $ti = $('#transimage');

            $('#transform').width( $ti.width() )
                           .height( $ti.height() );
        },500);
        checkTransAbility();
    }
}).on('doubletap', function() {
// load selected image by double-clicking on transform window image
    var list = filesGetSelected('.image.hasTem', WM.project.id);
    
    if (list.length == 1) {
        this.src = fileAccess(WM.project.id + list[0]);
        setTimeout(function() {
            var $ti = $('#transimage');

            $('#transform').width( $ti.width() )
                           .height( $ti.height() );
        },500);
        checkTransAbility();
    }
});

/*
$('#transform, #average').draggable({
    helper: 'clone',
    opacity: 0.7,
    stack: 'img',
    start: function(event, ui) {
        ui.helper.addClass('imghelper');
    },
    cursorAt: { left: 5, top: 5 }
});
*/

$('#cancel-grid').button().click(function() {
    $('#grid').hide();
    $('#destimages').show();
});
$('#cancel-continua').button().click(function() {
    $('#continua').hide();
    $('#destimages').show();
});
$('#view-average-button').button().click( getAverage );
$('#clear-average-button').button().click(function() {
    $('#average-list').hide().find('li').remove();
    $('#average').show().css('background-image', WM.blankBG).attr({
        'averaged': ''
    });
    clearInterval(WM.timer);
    $('#footer-text').html('');
});
// ! save buttons
$('#save-button, #trans-save-button').button({
    disabled: true
}).click(imgSave);

// remove growl and message notifications on double-click
$('body').on('doubletap', '.growl', function() {
    $(this).remove();
}).on('doubletap', '.msg', function() {
    msgGet($(this).data('msg_id'));
});

$('.msg').append('<br><span style="float: right; font-size:60%;">[double-click this notice to permanently close it]</span>');

$('#filepreview').click( function(e) {
    e.stopPropagation();
});

// ! ***** Finder *****
WM.finder = new finder();
$('#recent_creations').draggable().resizable();
//$finder.draggable().resizable();
$finder.on('click', function() {
    $('.context_menu').remove();
}).on('doubletap', 'li.file.image:not(.svg):not(.bmp), li.file.tem', function() {         // open image/tem in delineator on double-click
    delinImage($(this).attr('url'));
}).on('doubletap', 'li.file.obj', function() {                                  // open obj 3D in delineator on double-click
    var objURL = $(this).attr('url');
    
    // get associated image
    var imgURL = null;
    var $theImg = $('li.file.image[url^="'+ objURL.replace(/obj$/, '') + '"]');
    if ($theImg.length) { imgURL = $theImg.attr('url'); }
    
    d3_load_image(objURL, imgURL, $d3);
    $('.twoD').hide();
    $('.threeD').show();
}).on('doubletap', 'li.file.bmp', function() {                                  // open BMP's OBJ in 3D delineator
    var imgURL = $(this).attr('url');
    
    // get associated image
    var objURL = null;
    var $theOBJ = $('li.file.obj[url="'+ imgURL.replace(/bmp$/, 'obj') + '"]');
    if ($theOBJ.length) { 
        objURL = $theOBJ.attr('url'); 
        d3_load_image(objURL, imgURL);
    }
}).on('click', '> ul > li.folder ul', function(e) {                             // return to base directory when clicking under files
    $(this).closest('li.folder').find('> span').click();
}).on('click', '> ul > li.folder > ul li', function(e) {                        // cancel return to base directory when clicking on an item
    e.stopPropagation();
}).on('click', 'li.file', function(e) {                                         // click on a file icon
    $('input:visible').blur(); // blur focus on any inputs
    // manage selection of files on click or meta-click
    if (!(e.ctrlKey || e.metaKey || e.shiftKey)) {
        // unselect other files if ctrl/cmd/shift NOT held down
        $('li.file.selected').removeClass('selected');
    } else if (e.shiftKey) {
        var $prevUnSel,
            $nextUnSel,
            $prevAll,
            $nextAll;

        // select all files between this one and the nearest selected one
        $prevUnSel = $(this).prevUntil('li.file.selected');
        $nextUnSel = $(this).nextUntil('li.file.selected');
        $prevAll = $(this).prevAll('li.file');
        $nextAll = $(this).nextAll('li.file');
        if ($prevUnSel.length < $prevAll.length) {
            $prevUnSel.addClass('selected');
        } else if ($nextUnSel.length < $nextAll.length) {
            $nextUnSel.addClass('selected');
        }
    }
    $(this).toggleClass('selected');
    
    // close and unselect sibling folders
    $(this).siblings('li.folder')
           .addClass('closed')
           .removeClass('selected')
           .find('li.folder')
           .addClass('closed');

    WM.finder.updateSelectedFiles();
    
    // hide and move the filepreview
    $('#filepreview img, #history, #fileinfo').hide();
    $(this).append(
        $('#filepreview').css('margin-left', $(this).width())
    );
    $finder.scrollLeft($finder.width());
    
    $('#filepreview').offset({'top': $finder.position().top + 1});
    
}).on('click', 'li.file.image', function(e) {                                   // show image in imgbox on click
    var theURL,
        $theImg,
        $this = $(this);

    if (!WM.filepreview || $finder.hasClass('image-view')) { return false; }

    theURL = $this.attr('url');
    $theImg = $('#filepreview img');
    if ($theImg.filter(':visible').attr('src') != fileAccess(theURL)) {
        $('#fileinfo, #history, #selectedTem').hide().find('div').html('');

        $theImg.attr('src', WM.loadImg).attr('src', fileAccess(theURL)).show();

        if ($this.data('info') === undefined) {
            $.ajax({
                type: 'GET',
                url: 'scripts/imgReadExif',
                data: { img: theURL },
                success: function(data) {
                    if (data.error) {
                        growl(data.errorText);
                    } else {
                        $this.data('info', data.info);
                        $this.data('history', data.history);
                        $('#history').show().find('div').html($this.data('history'));
                        $('#fileinfo').show().find('div').html($this.data('info'));
                    }
                }
            });
        } else {
            $('#history').show().find('div').html($this.data('history'));
            $('#fileinfo').show().find('div').html($this.data('info'));
        }
    }
}).on('click', 'li.obj', function(e) {                                  // handle obj files (3D)
    if ($finder.hasClass('image-view')) { return false; }

    var objURL = $(this).attr('url');
    
    // get associated image
    var imgURL = '/include/images/finder/objicon.php';
    var $theImg = $('li.file.image[url^="'+ objURL.replace(/obj$/, '') + '"]');
    if ($theImg.length) { imgURL = fileAccess($theImg.attr('url')); }
    
    $('#filepreview img').attr('src', imgURL).show();
    $('#fileinfo').show().find('div')
        .html("OBJ files are human-readable, but too long to show. "
            + "Double-click to open this in the viewer.");
}).on('click', 'li.pca, li.fimg', function(e) {                                 // handle files not human readable (pca/fimg)
    if ($finder.hasClass('image-view')) { return false; }
    $('#filepreview img').attr('src', '/include/images/finder/pcaicon.php').show();
    $('#fileinfo').show().find('div').html("PCA files are not human-readable. "
        + "This file format is what the desktop version of Psychomorph uses. "
        + "To see a human-readable version of this file, look at the "
        + $(this).text() + ".txt file.");
}).on('doubletap', 'li.txt, li.csv', function(e) {                              // open text files in batch (txt/csv)
    var text = $(this).data('text').trim();

    if (typeof text === "undefined" || text === "") return false;
    
    // re-write to use regex to detect probable batch files
    var rows = text.split(/\s*[\r\n]+\s*/g);
    if (rows.length == 0) return false;
    var cols = rows[0].trim().split('\t');
    if (cols.length == 1) cols = rows[0].trim().split(',');
    
    if (cols.length == 10) {
    //if (text.match(/^(?:[^\t]+\t){9}[^\t]+\t*$/mg)) {
        batchEdit();
        $('#batchEditDialog table textarea').eq(0).val(text).blur();
    } else if (cols.length == 7) {
    //} else if (text.match(/^(?:[^\t]+\t){6}[^\t]+\t*$/mg)) {
        batchTrans();
        $('#batchTransDialog table textarea').eq(0).val(text).blur();
    } else if (rows.length > 2) {
        batchAverage();
        $('#batchAvgDialog textarea').eq(0).val(text);
    }
}).on('click', 'li.txt, li.csv, li.pci', function(e) {                          // display text files (txt/csv/pci)
    if ($finder.hasClass('image-view')) { return false; }

    var $this = $(this);
    var text = $this.data('text');

    if (typeof text !== "undefined" && text !== "") {
        $('#selectedTem').val(text).show();
    } else {
        $.ajax({
            url: 'scripts/fileRead',
            data: { url: $this.attr('url') },
            success: function(data) {
                if (data.error) {
                    $('<div />').html(data.errorText).dialog({
                        title: 'Error Reading File <code>' + theURL + '</code>',
                    });
                } else {
                    $this.data('text', data.text);
                    $('#selectedTem').val(data.text).show();
                }
            }
        });
    }
}).on('click', 'li.tem', function(e) {                                          // show text of tem file in imgbox on click
    if (!WM.filepreview || $finder.hasClass('image-view')) { return false; }

    var $this = $(this);
    var theURL = $this.attr('url');
    
    $('#fileinfo, #history').hide().find('div').html('');

    if ($this.data('tem') === undefined) {
        $.ajax({
            type: 'GET',
            url: 'scripts/temRead',
            data: { url: theURL },
            success: function(data) {
                if (data.error) {
                    $('<div />').html(data.errorText).dialog({
                        title: 'Error Reading Tem File <code>' + theURL + '</code>',
                    });
                } else {
                    $this.data('tem', data.tem);
                    $this.data('info', data.info);
                    $this.data('history', data.history);
                    $('#selectedTem').val($this.data('tem')).show();
                    $('#history').show().find('div').html($this.data('history'));
                    $('#fileinfo').show().find('div').html($this.data('info'));
                    
                }
            }
        });
    } else {
        $('#selectedTem').val($this.data('tem')).show();
        $('#history').show().find('div').html($this.data('history'));
        $('#fileinfo').show().find('div').html($this.data('info'));
    }
    $('#filepreview img').hide();
}).on('click', 'li.folder > span', function(e) {                                // click on a folder
    $finder.find('input').blur();                                               // blur any open inputs for folder name changes

    $theFolder = $(this).parent('li.folder');                                   // folder to open
    $theFolder.find('li.folder:not(.closed)').addClass('closed');               // close all folders below this level
    $theFolder.parents('li.folder.selected').removeClass('selected');           // unselect all folders above this level
    
    if (e.shiftKey) {
        $theFolder.addClass('closed');
        // select all folders between this one and the nearest selected one
        var $prevUnSel = $theFolder.prevUntil('li.folder.selected');
        var $nextUnSel = $theFolder.nextUntil('li.folder.selected');
        var $prevAll = $theFolder.prevAll('li.folder');
        var $nextAll = $theFolder.nextAll('li.folder');
        if ($prevUnSel.length < $prevAll.length) {
            $prevUnSel.addClass('selected');
        } else if ($nextUnSel.length < $nextAll.length) {
            $nextUnSel.addClass('selected');
        }
    } else if (e.ctrlKey || e.metaKey) {
        $theFolder.addClass('closed');
    } else {
        // keep other selected folders if ctrl/cmd/shift held down
        $finder.find('li.folder').removeClass('selected');
        $theFolder.removeClass('closed');
    }

    $theFolder.toggleClass('selected');
    $theFolder.siblings('li.folder:not(.closed)').addClass('closed');           // close sibling folders

    var $selFolders = $finder.find('li.folder.selected');
    if ($selFolders.length == 1) {
        $selFolders.removeClass('closed');                                      // if 1 remaining selected folder, open it
        $finder.find('li.file.selected').removeClass('selected');               // unselect all files

        // add draggable functions to files
        var $dragFiles = $selFolders.find('>ul>li.file:not(.ui-draggable)');
        $dragFiles.trigger('DOMNodeInserted');

        $selFolders.find('>ul').css('margin-left', $selFolders.width());
    }

    if ($finder.hasClass('image-view')) {
        $finder.find('ul').css({
            'width': '0'
        });
        $theFolder.find('> ul').css('width', $finder.width());
    }

    $finder.scrollLeft($finder.width());                                        // scroll all the way to the right
    WM.finder.updateSelectedFiles();
});

// ! ***** Menu Items *****
// !#imageview
$('#imageview').click( function() {
    if ($(this).hasClass('disabled')) { return false; }

    $finder.toggleClass('image-view');

    if ($finder.hasClass('image-view') ) {
        $(this).text('Column View');
    } else {
        $(this).text('Icon View');
    }
    $('#filepreview').hide();
    $('#refresh').click();
    $finder.find('ul').css('width', 'auto');
    $finder.find('ul').css('margin-left', (-1 * $finder.find('> ul').width()) - 1);
});
// !#selectedImage
$('#selectedImage').click(function() {
    if ($(this).css('width') == 'auto') {
        $(this).css('width', '60px');
    } else {
        $(this).css('width', 'auto');
    }
});

$('#loginBox thead').on('tap', function() {
    $('#loginBox tbody').toggle();
});

// !#upload
$('#uploadFiles').click(function() { $('#upload').click(); });
$('#upload').change(fileUpload);
// !#showProjects
$('#showProjects').not('.disabled').click({appWindow: 'project'}, interfaceChange);
// !#showFinder
$('#showFinder').not('.disabled').click({appWindow: 'finder'}, interfaceChange);
// !#showDelineate
$('#showDelineate').not('.disabled').click({appWindow: 'delineate'}, interfaceChange);
// !#showAverage
$('#showAverage').not('.disabled').click({appWindow: 'average'}, interfaceChange);
// !#showTransform
$('#showTransform').not('.disabled').click({appWindow: 'transform'}, interfaceChange);
// !#aboutWebmorph
$('#aboutWebmorph').click(function() { $('#aboutDialog').dialog(); });
$('#citation').click(function() { $('#aboutDialog').dialog(); });
// !#prefs
$('#prefDialog').tabs();
$('#prefs').not('.disabled').click(function() {
    // open the prefs dialog
    $('#prefDialog').dialog({
        close: prefGet,
        buttons: {
            Cancel: function() { $(this).dialog("close"); },
            'Save': {
                text: 'Save',
                class: 'ui-state-focus',
                click: prefSet
            }
        }
    });
});

function hue_change($chooser, ui) {
    var hue = ui.value;
    $chooser.css('background-image', "none");
    var hsl = 'hsl(' + hue + ', 100%, 30%)';
    if (hue === 0) {
        hsl = 'hsl(200, 0%, 30%)';
    } else if (hue == 361) {
        hsl = 'hsl(200, 0%, 0%)';
        hue = 'B';
    }
    $chooser.css('background-color', hsl);
    $chooser.find('.ui-slider-handle').css('border-color', hsl).text(hue);
}

$('.hue_chooser').slider({
    value: 200,
    min: 0,
    max: 361,
    step: 1,
    slide: function(event, ui) {
        hue_change($(this), ui);
    },
    change: function(event, ui) {
        hue_change($(this), ui);
    }
});

function rgba_change($chooser, $rgba, ui) {
    var r = ui.values[0];
    var g = ui.values[1];
    var b = ui.values[2];

    $rgba.find('span.r').text(r);
    $rgba.find('span.g').text(g);
    $rgba.find('span.b').text(b);

    if (ui.values.length == 4) {
        var a = round(ui.values[3]/255, 2);
        $chooser.css('background-color', 
                     'rgba(' + r + ', ' + g + ', ' + b + ',' + a + ')');
        $rgba.find('span.a').text(a);
    } else {
        $chooser.css('background-color', 
                     'rgb(' + r + ', ' + g + ', ' + b + ')');
    }
}

$('.rgb_chooser').each( function() {
    var $rgba = $('<span class="rgba_text" />');
    $rgba.html('rgb(<span class="r"></span>, <span class="g"></span>, <span class="b"></span></span>)');
    $(this).before($rgba);
    

    $(this).slider({
        values: [127, 127, 127],
        min: 0,
        max: 255,
        step: 1,
        slide: function(event, ui) {
            rgba_change($(this), $rgba, ui);
        },
        change: function(event, ui) {
            rgba_change($(this), $rgba, ui);
        }
    }).slider('values', [127, 127, 127]);
    
    $(this).after('<input type="checkbox" class="patchcolor"> Select color from patch<br>\n' +
        '<div class="patchcolor">\n' +
        '   Patch coordinates (top left of the image is 0, 0) <br>\n' +
        '   top left: <input type="number" step="1" min="0" value="0" name="startx" maxlength="4" />, \n' +
        '   <input type="number" step="1" min="0" value="0" name="starty" maxlength="4" /> \n' +
        '   bottom right: <input type="number" step="1" min="0" value="10" name="endx" maxlength="4" />, \n' +
        '   <input type="number" step="1" min="0" value="10" name="endy" maxlength="4" />\n' +
        '</div>');
});

$('.rgba_chooser').each( function() {
    var $rgba = $('<span class="rgba_text" />');
    var $wrap = $('<div class="rgba_chooser_bg" />');
    $rgba.html(' rgba(<span class="r"></span>, <span class="g"></span>, <span class="b"></span>, <span class="a"></span>)');
    $(this).wrap($wrap);
    $(this).parent(".rgba_chooser_bg").before($rgba);

    $(this).slider({
        values: [127, 127, 127, 255],
        min: 0,
        max: 255,
        step: 1,
        slide: function(event, ui) {
            rgba_change($(this), $rgba, ui);
        },
        change: function(event, ui) {
            rgba_change($(this), $rgba, ui);
        }
    }).slider('values', [127, 127, 127, 255]);
});

$('#grid_line_color').slider('values', [127,127,127]);

$('#grid_lines').change( function() {
    var line_color;

    if ($(this).prop('checked')) {
        $('#scrambleExample div').css({
            'border-top-style': 'solid',
            'border-left-style': 'solid'
        });
        $('#grid_line_color').show().slider('value', $('#grid_line_color').slider('value'));
    } else {
        $('#grid_line_color').hide();
        $('#scrambleExample div').css({
            'border-color': 'rgba(0,0,0,0.75)',
            'border-top-style': 'dotted',
            'border-left-style': 'dotted'
        });
    }
});

$('#grid_line_color').on( "slidechange", function(e, ui) {
    var r, g, b;

    r = ui.values[0];
    g = ui.values[1];
    b = ui.values[2];

    $('#scrambleExample div').css({
        'border-color': 'rgb(' + r + ',' + g + ',' + b + ')'
    });
});

$('#grid_size').change( function() {
    resetGrids();
});

$('#scramble_x_offset').change( function() {
    resetGrids();
});

$('#scramble_y_offset').change( function() {
    resetGrids();
});

$('#batch_mask_color').on("slide", function(event, ui) {
    var r = ui.values[0];
    var g = ui.values[1];
    var b = ui.values[2];
    $('#maskExample').css('background-color', 'rgb(' + r + ', ' + g + ', ' + b + ')');
}).on("change", function(event, ui) {
    var r = ui.values[0];
    var g = ui.values[1];
    var b = ui.values[2];
    $('#maskExample').css('background-color', 'rgb(' + r + ', ' + g + ', ' + b + ')');
    $(this).css('background-color', 'rgba(' + r + ', ' + g + ', ' + b + ')');
});

// !#fileListGet
$('#fileListGet').not('.disabled').click(function() {
    fileListGet();
});
// !#getInfo
$('#getInfo').not('.disabled').click(function() {
    $.ajax({
        type: 'GET',
        url: 'scripts/imgReadExif',
        data: { img: WM.faceimg },
        success: function(data) {
            if (data.error) {
                growl(data.errorText);
            } else {
                $('<div />').attr('title', WM.faceimg).html(data.desc).dialog({
                    width: 500,
                    height: 400,
                });
            }
        }
    });
});

// !#save
$('#save').not('.disabled').click(function() {
    if (WM.appWindow == 'delineate') {
        saveTem();
    } else if (WM.appWindow == 'average') {
        $('#save-button').click();
    } else if (WM.appWindow == 'transform') {
        $('#trans-save-button').click();
    }
});
// !#saveAs
/*
$('#saveAs').not('.disabled').click(function() {

    if (WM.appWindow == 'delineate') {
        $('<div />').html('Name: <input type="text" />').dialog({
            title: 'Save As',
            buttons: {
                Cancel: function() {
                    $(this).dialog('close');
                },
                Save: function() {
                    growl($(this).find('input').val());
                }
            }
        });
    }
});
*/
// !#convert
$('#convert').not('.disabled').click(function() {
    batchConvert();
});
// !#download
$('#download').not('.disabled').click(function() {
    var cDir,
        files;

    cDir = currentDir();
    files = filesGetSelected();

    if (files.length > 0) {
        growl('<p>Downloading ' + files.length + ' file' + (files.length==1?'':'s') + '.</p>' +
        '<p>This may take a few seconds, depending on how many files need to be compressed.</p>');
        postIt('scripts/fileZip', {
            'files': files
        });
    } else if (typeof cDir == 'string' || cDir.length > 0) {
        growl('<p>Downloading directory <code>' + urlToName(cDir) + '</code></p>' +
        '<p>This may take a few seconds, depending on how many files need to be compressed.</p>');
        postIt('scripts/fileZip', {
            'directory': cDir
        });
    } else {
        growl('You have not selected any files.', 1500);
    }
});

$('.download_file').click(function(e) {
    postIt('scripts/fileDownload', {
        'file': $(this).data('src')
    });
});

// !#toggle_delintoolbar
$('#toggle_delintoolbar').not('.disabled').click(function() {
    var $dt = $('#delin_toolbar');

    if ($dt.filter(':visible').length) {
        $dt.hide();
        $('#toolbar_switcher').show();
        $(this).find('span.checkmark').hide();
    } else {
        $dt.show();
        $('#toolbar_switcher').hide();
        $(this).find('span.checkmark').show();
    }
});
// !#toggletem
$('#toggletem').not('.disabled').click(function() {

    if (WM.showTem) {
        // hide delineation
        $(this).find('span.checkmark').hide();
        $('#template, .pt').hide();
        WM.showTem = false;
    } else {
        // show delineation
        $(this).find('span.checkmark').show();
        WM.showTem = true;
        $('#template, .pt').show();
        drawTem();
    }
});
// !#emptyTrash
$('#emptyTrash').not('.disabled').click( function() { emptyTrash(); });
// !#toggletrash
$('#toggletrash').not('.disabled').click(function() {

    if ($('#trash:visible').length) {
        $(this).find('span.checkmark').hide();
        $('#trash').hide();
    } else {
        $(this).find('span.checkmark').show();
        $('#trash').show();
    }
});

// !#toggle_preview
$('#toggle_preview').not('.disabled').click(function() {
    var $cm = $(this).find('span.checkmark');
    if (WM.filepreview) {
        $cm.hide();
        WM.filepreview = false;
        $('#filepreview').hide();
    } else {
        $cm.show();
        WM.filepreview = true;
    }
});

// !#toggle_lightTable
$('#toggle_lightTable').not('.disabled').click(function() {

    var $cm = $(this).find('span.checkmark');
    if ($('#lightTable').filter(':visible').length) {
        $('#lightTable').dialog('close');
    } else {
        $cm.show();
        $('#lightTable').dialog({
            title: "Light Table",
            resizable: true,
            modal: false,
            beforeClose: function() { $('#toggle_lightTable span.checkmark').hide(); },
            height: 425,
            width: 620,
            position: {my: 'top', at: 'bottom+20', of: $('#menubar')},
            maxHeight:$finder.height()
        });
        $('#lightTable img').css('height','800');
        lightTableResize();
    }
});
// !#lightTable
$('#lightTable').on('dblclick', 'img', function() {
    $(this).remove();
    if ($('#lightTable img').css('height','800').length === 0) {
        $('#lightTable div').show();
        $('#lightTable').dialog({
            height: 425,
            width: 620
        });
    }
    lightTableResize();
}).droppable({
    accept: 'li.file.image',
    tolerance: "pointer",
    drop: function(event, ui) {
        var $files = ui.helper.find('li.file.image');
        if ($('#lightTable img').length === 0) {
            $('#lightTable div').hide();
        }

        $files.each( function() {
            var url = $(this).attr('url');
            var $img = $('<img />').attr('src',fileAccess(url));
            $img.attr('title', urlToName(url));
            $('#lightTable').append($img);
        });

        $(ui.helper).remove();

        $('#lightTable img').css({ height: 800 });
        lightTableResize();
    }
}).on('dialogresize', function(event, ui) {
    if (ui.size.width > ui.originalSize.width || ui.size.height > ui.originalSize.height) {
        $('#lightTable img').css('height',$('#lightTable img').height()+10);
    }
    lightTableResize();
}).on('dialogresizestop', function(event, ui) {
    $('#lightTable img').css('height','800');
    lightTableResize();
    //console.log($('#lightTable').prop('scrollHeight') + ', ' + $('#lightTable').innerHeight());
}).sortable().disableSelection();

function lightTableResize() {
    var h = $('#lightTable').innerHeight();
    var $divs = $('#lightTable img');
    var x = $divs.height();
    var scrollHeight = $('#lightTable').prop('scrollHeight');

    if (scrollHeight > h && x > 25) {
        x -= 10;
        $divs.css({height: x});
        lightTableResize();
    }
}

$('#toggle_lightTable span.checkmark').hide();
// !#size_value
$('#size_value').click(function() {
    if ($('#imgsize:visible').length) {
        $('#imgsize').hide();
    } else {
        $('#imgsize').css('display', 'inline-block');
    }
});

// !#imgsize
$('#imgsize').slider({
    value: $delin.height(),
    min: 100,
    max: 4000,
    step: 1,
    slide: function(event, ui) {
        var h = ui.value;
        var w = Math.round(WM.originalWidth * h / WM.originalHeight);
        $('#size_value').html(w + 'x' + h);
    },
    change: function(event, ui) {
        var h = ui.value;
        var w = Math.round(WM.originalWidth * h / WM.originalHeight);
        WM.temRatio = h / WM.originalHeight;

        $('#size_value').html(w + 'x' + h);

        $delin.css({
            height: h + 'px',
            width: w + 'px'
        });
        $('#template').css({
            height: h + 'px',
            width: w + 'px'
        });
        var canvas = document.getElementById("template");
        canvas.width = w;
        canvas.height = h;

        drawTem();
        updateUndoList();

        if (WM.delinfunc == 'fit') {
            WM.delinContext.clearRect (0, 0, $('#template').width(), $('#template').height());
            cursor('crosshair');
        } else {
            drawTem();
        }
    }
});

// !#fitsize
$('#fitsize').not('.disabled').click(function() {
    if ($d3.filter(':visible').length && WM.d3) {
        WM.d3.fitsize();
    } if (WM.appWindow == 'delineate') {
        delin_fitsize();
    } 
});
// !#zoomin
$("#zoomin").not('.disabled').click(function() {
    if ($d3.filter(':visible').length && WM.d3) {
        //WM.d3.scale(0.05);
        WM.d3.changeZoom(+0.05);
    } else if (WM.appWindow == 'delineate') {
        var resize = $('#imgsize').slider('value') + 100;
        if (resize > $('#imgsize').slider('option', 'max')) {
            resize = $('#imgsize').slider('option', 'max');
        }
        $('#imgsize').slider('value', resize);
    } else if (WM.appWindow == 'transform') {
        var w = $('#destimages').width() + 100;
        $('#destimages').css('width', w + 'px');
        $('#transform').width($('#transimage').width()).height($('#transimage').height());
        $('#individual_image_box').css("padding-left", $('#destimages').outerWidth(true) + 20);
    }
});
// !#zoomout
$("#zoomout").not('.disabled').click(function() {
    if ($d3.filter(':visible').length && WM.d3) {
        //WM.d3.scale(-0.05);
        WM.d3.changeZoom(-0.05);
    } else if (WM.appWindow == 'delineate') {
        var resize = $('#imgsize').slider('value') - 100;
        if (resize < $('#imgsize').slider('option', 'min')) {
            resize = $('#imgsize').slider('option', 'min');
        }
        $('#imgsize').slider('value', resize);
    } else if (WM.appWindow == 'transform') {
        var w = $('#destimages').width() - 100;
        if (w < 200) {
            w = 200;
        }
        $('#destimages').css('width', w + 'px');
        $('#transform').width($('#transimage').width()).height($('#transimage').height());
        $('#individual_image_box').css("padding-left", $('#destimages').outerWidth(true) + 20);
    }
});
// !#zoomoriginal
$('#zoomoriginal').not('.disabled').click(function() {
    if ($d3.filter(':visible').length && WM.d3) {
        WM.d3.zoomoriginal();
    } else if (WM.appWindow == 'delineate') {
        var resize = WM.originalHeight;

        // adjust slider min and max if out of range
        if (resize < $('#imgsize').slider('option', 'min')) {
            $('#imgsize').slider('option', 'min', resize);
        } else if (resize > $('#imgsize').slider('option', 'max')) {
            $('#imgsize').slider('option', 'max', resize);
        }
        $('#imgsize').slider('value', resize);
    } else if (WM.appWindow == 'transform') {
        $('#destimages').css('width', '310px');
        sizeToViewport();
    } else if (WM.appWindow == 'average') {
        $('#avg_image_box').css('width', '310px').css('height', '450px');
        sizeToViewport();
    }
});
// !#cutItems
$('#cutItems').not('.disabled').click(function() {
    WM.pasteBoard = filesGetSelected();
    $finder.find('li.file').removeClass('to_cut'); // clear all other to_cut files
    $finder.find('li.file.selected:visible').each(function(i, v) {
        $(this).addClass('to_cut');
    });
    $('#footer-text').html(WM.pasteBoard.length + ' files cut');
});
// !#copyItems
$('#copyItems').not('.disabled').click(function() {

    if (WM.appWindow == 'delineate') {
        WM.pasteBoard = [];
        $.each(WM.selectedPts, function(i, v) {
            if (v) {
                WM.pasteBoard.push({
                    n: i,
                    x: WM.current.tem[i].x,
                    y: WM.current.tem[i].y
                });
            }
        });

        $('#footer-text').html(WM.pasteBoard.length + ' points copied');
    } else {
        WM.pasteBoard = filesGetSelected();
        $finder.find('li.file').removeClass('to_cut'); // clear all to_cut files
        $('#footer-text').html(WM.pasteBoard.length + ' files copied');
    }
});
// !#pasteItems
$('#pasteItems').not('.disabled').click(function() {

    if (WM.appWindow == 'delineate') {
        temPaste();
    } else {
        filePaste();
    }
});

$('#moveFolderToProject').click( function() {
    if ($(this).hasClass('disabled')) { return false; }

    folderMoveProject();
});
// !#find
$('#find').not('.disabled').click(function() {
    var $sb;
    if ($finder.filter(':visible').length) {
        $sb = $('#searchbar');
    } else if (WM.appWindow == 'project') {
        $sb = $('#projectsearchbar');
    }

    $sb.toggle().val('').focus().trigger('keyup');
    sizeToViewport();
});
// !#searchbar
$('#searchbar').keyup(function() {
    var searchtext = $(this).val();
    var $allFiles = $finder.find('li.file');

    $allFiles.show();
    if (searchtext !== '') {
        $allFiles.not(':contains("' + searchtext + '")').hide();
    }
    WM.finder.updateSelectedFiles();
}).hide();

$('#projectsearchbar').keyup(function() {
    var searchtext = $(this).val();
    var $allProjects = $('#project_list tbody tr');

    $allProjects.show();
    if (searchtext !== '') {
        $allProjects.not(':contains("' + searchtext + '")').hide();
    }
}).hide();

// !#deleteItems
$('#deleteItems').not('.disabled').click(function() {

    if ($finder.filter(':visible').length) {
        fileDelete(true);
    } else if ($d3.filter(':visible').length && WM.d3) {
        WM.d3.remove();
        $('#holocancel').trigger('doubletap');
    } else if (WM.appWindow == 'delineate') {
        var ptArray = getSelPts();
        if (ptArray.length) {
            removeTemPoints(ptArray);
        }
    }
});
// !#admin
$('#admin').click(function() {
    window.open("/admin");
});

$('#reload_scripts').click(function() {
    $.getScript('/include/js/psychomorph/functions.js');
    $.getScript('/include/js/psychomorph/batch.func.js');
    $.getScript('/include/js/psychomorph/finder.func.js');
    $.getScript('/include/js/psychomorph/delin.func.js');
    $.getScript('/include/js/psychomorph/morph.func.js');
    $.getScript('/include/js/psychomorph/project.func.js');
    $.getScript('/include/js/psychomorph/user.func.js');
    $.getScript('/include/js/psychomorph/three.func.js');
    $.getScript('/include/js/psychomorph/keyboard.js');
});

// !#newProject
/*$('#newProject').not('.disabled').click(function() {
    projectNew();
});*/
// !#newFolder
$('#newFolder').not('.disabled').click(function() {
    if (WM.appWindow == 'project') {
        projectNew();
    } else {
        folderNew();
    }
});
// !#dbCleanup
$('#dbCleanup').not('.disabled').click(function() {
    $.ajax({
        url: 'scripts/dbCleanup?project=' + WM.project.id,
        success: function(data) {
            if (data.error) {
                $('<div title="Error Cleaning Database" />').html(data.errorText).dialog();
            } else {
                var added =  data.added.length;
                var deleted = data.deleted.length;
                $('#refresh').click();
                var text = added + ' file' + (added == 1 ? '' : 's') + ' added<br>'
                        + deleted + ' file' + (deleted == 1 ? '' : 's') + ' deleted';
                $('<div title="Database Clean" />').html(text).dialog();
            }
        }
    });
});
// !#refresh
$('#refresh').click(function() { console.log('refresh()');
    if ($(this).hasClass('disabled')) { return false; }

    if ($finder.filter(':visible').length) {
        $finder.html('');
        loadFiles(currentDir());
    }

    if (WM.appWindow == 'delineate') {
        delinImage(WM.faceimg);
        WM.delinfunc = 'move';
        $('#pointer, #leftEye, #rightEye, #mouth').hide();
    } else if (WM.appWindow == 'average') {
        $('#clear-average-button').click();
        loadFiles(currentDir());
    } else if (WM.appWindow == 'transform') {
        $('#fromimage, #toimage, #transimage').attr('src', WM.blankImg);

        $("#transform").attr({
            'src': WM.blankImg,
            'tem': '',
            'transimage': '',
            'fromimage': '',
            'toimage': '',
            'shape': 50,
            'color': 50,
            'texture': 50
        });

    } else if (WM.appWindow == 'project') {
        projectList();
    }
    cursor('auto');
    sizeToViewport();
});

$('#autoDelineate').click(function() {
    skyBio(WM.faceimg);
});

// !#maskBuilder
$('#maskBuilder').click(function() {
    $mbb = $('#mask_builder_box');
    if (WM.delinfunc == 'mask') {
        WM.delinfunc = 'move';
        $mbb.hide();
    } else {
        WM.delinfunc = 'mask';
        $mbb.show();
    }
});

// !#fitTemplate
$('#fitTemplate').not('.disabled').click(function() {

    clickPt(0);
    WM.delinfunc = 'fit';
    $('.pt').remove();

    if ($finder.filter(':visible').length) {
        // fit all selected images
        files = [];
        $('li.file.image.selected').each(function(i, v) {
            files[i] = $(this).attr('url');
        });
        if (files.length > 0) {
            $('#footer-text').html('0 of ' + files.length + ' image' + 
                 ((files.length == 1) ? '' : 's') + ' fitted to template <code>' + 
                 $('#currentTem_name').text() + '</code>');
            WM.selectedFile = 0;
            var url = files[0];
            var name = WM.project.id + urlToName(url);
            delinImage(name, false);
            WM.eyeClicks = [];
            $('#template').hide();
            WM.delinfunc = '3pt';
            cursor('crosshair');
        } else {
            growl('You need to select files to fit templates.', 1500);
            WM.delinfunc = 'move';
            quickhelp();
        }
    } else if (WM.appWindow == 'delineate') {
        $('#footer-text').html('Fitting template <code>' + $('#currentTem_name').text() + '</code>');
        WM.eyeClicks = [];
        $('#template').hide();
        WM.delinfunc = '3pt';
        cursor('crosshair');
    }
});

// use scroll wheel to resize in delineation window; this annoys Iris
/*
$('#template').get(0).addEventListener('wheel', function(e) {
    e.preventDefault();
    
    var resize = $('#imgsize').slider('value') - e.deltaY;
    if (resize > $('#imgsize').slider('option', 'max')) {
        resize = $('#imgsize').slider('option', 'max');
    }
    $('#imgsize').slider('value', resize);
}, false);
*/

// !#newLine
$('#newLine').click(function() {
    if (WM.delinfunc != 'lineadd') {
        WM.delinfunc = 'lineadd';
        quickhelp('New line: press &lsquo;enter&rsquo; to end');
        cursor('lineadd');
        WM.current.lines.push([]);
        var line = WM.current.lines.length - 1;
        WM.delin.lineColors[line] = 'red';
        $('#footer-text').html('Starting new line');
    }
    updateUndoList();
    drawTem(); // adds or deletes new line object
});
// !#deleteLine
$('#deleteLine').click(function() {
    if (WM.delinfunc != 'linesub') {
        WM.delinfunc = 'linesub';
        quickhelp('Click a point to delete the attached line (enter to stop)');
        cursor('linesub');
    }

    drawTem();
});
// !#closeMouth
$('#closeMouth').click(function() {
    if (!($(this).hasClass('disabled'))) {
        for (var i = 94; i < 99; i++) {
            var fromPt = i + 5;
            var toPt = i;
            WM.current.tem[toPt].x = WM.current.tem[fromPt].x;
            WM.current.tem[toPt].y = WM.current.tem[fromPt].y;
        }
        drawTem();
    }
});
// !#batchPCA
$('#batchPCA').click( function() {
    if ($(this).hasClass('disabled')) { return false; }
    getBatchPCA();
});
// !#singlePCA
$('#singlePCA').click( function() {
    if ($(this).hasClass('disabled')) { return false; }
    getPCA();
});
$('#PCvis').click( function() {
    if ($(this).hasClass('disabled')) { return false; }
    getPCvis();
});
// !#batchRename
$('#batchRename').click( function() {
    if ($(this).hasClass('disabled')) { return false; }
    batchRename();
});
$('#batchRenameDialog input[type=text]').keyup( function() {
    batchRenameChecks();
});
$('#batchRenameDialog input, #batchRenameDialog select').change( function() {
    batchRenameChecks();
});
// !#resize
$('#resize').not('.disabled').click(function() {
    batchResize();
});
// !#pixels
$('#pixels').not('.disabled').click(function() {
    batchPixels();
});
$('#resizeDialog input').blur( function() {
    if ($(this).hasClass('disabled')) { return false; }
    calcNewSizes(this.name);
});
// !#rotate
$('#rotate').not('.disabled').click(function() {
    batchRotate();
});
// !#alignEyes
$('#alignEyes').not('.disabled').click(function() {
    batchAlign();
});

// !#temFromEmbedded
$('#temFromEmbedded').not('.disabled').click(function() {
    batchTemFromEmbedded();
});

//!.batch_name toggles
$('.batch_name').on('change', 'input.toggle_superfolder, input.toggle_subfolder, input.toggle_prefix, input.toggle_suffix', function() {
    batchToggle(this);
}).on('click', 'span:not(.multibatch)', function() {
    var $this = $(this);
    var w = $this.width();
    var current_val = $this.html();
    var $input = $('<input type="text" />').val(current_val).width(w + 10).keydown(function(e) {
        if (e.which == KEYCODE.enter) $(this).blur();
    }).blur(function() {
        var val = $(this).val();

        if ($this.hasClass('batch_superfolder')) {
            if (val.length === 0) {
                $(this).closest('div.batch_name')
                       .find('.toggle_superfolder')
                       .prop('checked', false);
            } else {
                if (val.substr(0, 1) != '/') val = '/' + val;
                if (val.substr(-1) != '/') val = val + '/';
            }
        } else if ($this.hasClass('batch_subfolder')) {
            if (val.length === 0) {
                $(this).closest('div.batch_name')
                       .find('.toggle_subfolder')
                       .prop('checked', false);
            } else {
                if (val.substr(0, 1) != '/') val = '/' + val;
                if (val.substr(-1) != '/') val = val + '/';
            }
        } else if ($this.hasClass('batch_prefix')) {
            val = val.replace(/\//g, '');
            if (val.length === 0) {
                $(this).closest('div.batch_name')
                       .find('.toggle_prefix')
                       .prop('checked', false);
            }
        } else if ($this.hasClass('batch_suffix')) {
            val = val.replace(/\//g, '');
            if (val.length === 0) {
                $(this).closest('div.batch_name')
                       .find('.toggle_suffix')
                       .prop('checked', false);
            }
        }
        $this.html(val).show();
        $(this).remove();
    });
    $this.hide().after($input);
    $input.focus();
});

//!#colorCalibrate
$('#colorCalibrate').click( function() {
    if ($(this).hasClass('disabled')) { return false; }
    batchColorCalibrate();
});
// !#crop
$('#crop').not('.disabled').click(function() {
    batchCrop();
});
$('#cropDialog input').keyup(function() {
    var origh = parseInt($('#cropBoxHeight').attr('orig'));
    var origw = parseInt($('#cropBoxWidth').attr('orig'));
    var t = parseInt($('#cropDialog input[name=top]').val());
    var b = parseInt($('#cropDialog input[name=bottom]').val());
    var l = parseInt($('#cropDialog input[name=left]').val());
    var r = parseInt($('#cropDialog input[name=right]').val());

    origh = (isNaN(origh)) ? 0 : origh;
    origw = (isNaN(origw)) ? 0 : origw;
    t = (isNaN(t)) ? 0 : t;
    b = (isNaN(b)) ? 0 : b;
    l = (isNaN(l)) ? 0 : l;
    r = (isNaN(r)) ? 0 : r;

    var neww = origw + l + r;
    var newh = origh + t + b;
    $('#cropBoxWidth').text(neww + 'px');
    $('#cropBoxHeight').text(newh + 'px');

    $('#cropDialog .batchList table tbody tr').each( function() {
        var $td = $(this).find('td');

        var origW = parseInt($td.eq(1).text());
        var origH = parseInt($td.eq(2).text());
        var newW = origW + l + r;
        var newH = origH + t + b;

        $td.eq(3).text(Math.round(newW));
        $td.eq(4).text(Math.round(newH));
    });
}).keydown(function(e) {
    // change default order of tabbing to go clockwise
    if (e.which == KEYCODE.tab) {
        e.preventDefault();

        var n = $(this).attr('name');
        if (n == 'top') {
            $('#cropDialog input[name=right]').focus().select();
        } else if (n == 'right') {
            $('#cropDialog input[name=bottom]').focus().select();
        } else if (n == 'bottom') {
            $('#cropDialog input[name=left]').focus().select();
        } else if (n == 'left') {
            $('#cropDialog input[name=top]').focus().select();
        }
    }
});
// !#convert_template_menu
$('#convert_template_menu li').not('.disabled').click(function() {
    var from_tem = parseInt($(this).data('from'));
    var to_tem = parseInt($(this).data('to'));

    if (from_tem > 0 && to_tem > 0) {
        batchTemConvert(from_tem, to_tem);
    }
});

/*
$('#scrambleExample').on('click', 'div', function(e) {
    var $scram,
        $theDiv,
        thisSel,
        lastSel,
        x,
        y,
        minx,
        miny,
        maxx,
        maxy;

    $theDiv = $(this);
    $theDiv.toggleClass('selected');
    $scram = $('#scrambleExample');
    thisSel = $theDiv.data();

    if (e.ctrlKey || e.metaKey || e.shiftKey) {

        lastSel = $scram.data('lastSel');

        if (lastSel !== undefined && lastSel.hasOwnProperty('x')) {
            minx = Math.min(thisSel.x, lastSel.x);
            maxx = Math.max(thisSel.x, lastSel.x);
            miny = Math.min(thisSel.y, lastSel.y);
            maxy = Math.max(thisSel.y, lastSel.y);
            $scramDivs = $scram.find('div');

            for (x = minx; x <= maxx; x++) {
                for (y = miny; y <= maxy; y++) {
                    $scramDivs.filterByData('x', x).filterByData('y', y).addClass('selected');
                    console.log(x + ' - ' + y);
                }
            }
        }
    }

    if ($theDiv.hasClass('selected')) {
        $scram.data('lastSel', thisSel);
    }
});
*/

// !#scramble
$('#scramble').not('.disabled').click( function() {
    batchScramble();
});

// !#mask
$('#mask').not('.disabled').click(function() {
    batchMask();
});
// !#mirror
$('#mirror').not('.disabled').click( function() {
    batchMirror();
});
// !#symmetrise
$('#symmetrise').not('.disabled').click(function() {
    batchSymmetrise();
});
// ! ***** Movies *****
$('#transMovieSteps').change( function() {
    var value = parseInt($(this).val());
    $('#transMovieStepsDisplay').html("(" + (value+1) + " images in " + 
                                       (Math.round(1000/value)/10) + "% steps)");
});
$('#movieHeight').slider({
    value: 200,
    min: 20,
    max: 1000,
    step: 1,
    slide: function(event, ui) {
        var value = ui.value;
        $('#movieBox').css({
            height: Math.min(value, 300) + 'px',
            width: 'auto',
        });
        $('#movieHeightDisplay').html(value);
    }
});
$('#movieOriginalHeight').click( function() {
    $.ajax({
        url: 'scripts/imgDimensions',
        type: 'GET',
        data: { img: WM.project.id + urlToName($('#movieBox').attr('src')) },
        success: function(data) {
            $('#movieHeight').slider('value', data.h);
            $('#movieBox').css({
                height: Math.min(300, data.h) + 'px',
                width: 'auto',
            });
            $('#movieHeightDisplay').html(data.h);
        }
    });
});


function calcMovieLen() {
    //  update movieLength
    var speed = $('#movieSpeed').slider('value');
    var pause = $('#moviePause').slider('value');
    var files = filesGetSelected('.image');

    var len = speed * files.length;

    if ($('#movieRev').prop('checked')) {
        len = 2 * (len - speed + pause);
    }

    $('#movieLength').html(Math.round(len / 100) / 10);
}

$('#movieRev').change( function() {
    calcMovieLen();

    if ($('#movieRev').prop('checked')) {
        $('#moviePauseSection').show();
    } else {
        $('#moviePauseSection').hide();
    }
});
$('#movieSpeed').slider({
    value: 50,
    min: 10,
    max: 1000,
    step: 1,
    slide: function(event, ui) {
        var value = ui.value;
        $('#movieSpeedDisplay').html(value);
        calcMovieLen();
    }
});
$('#moviePause').slider({
    value: 0,
    min: 0,
    max: 5000,
    step: 10,
    slide: function(event, ui) {
        var value = ui.value;
        $('#moviePauseDisplay').html(value);
        calcMovieLen();
    }
});
// !webcam functions
$('#webcamPhoto').click( function() {
    if ($(this).hasClass('disabled')) { return false; }
    webcamPhoto();
});

// !#movingGif
$('#movingGif').not('.disabled').click(function() {
    movingGif();
});

// !#batchEdit
$('#batchEdit').not('.disabled').click(function() {
    batchEdit();
});

// !#batchAverage
$('#batchAverage').not('.disabled').click(function() {
    batchAverage();
});

// !#batchTrans
$('#batchTrans').not('.disabled').click(function() {
    batchTrans();
});

// !#temVis
$('#temVis').not('.disabled').click(function() {
    batchTemVis();
});
// !#tem_point_style
$('#tem_point_style').change(function() {
    var theStyle = $(this).val();
    var $fill = $('#tem_point_fill').closest("li");
    var $color = $('#tem_point_color').closest('li');
    var $width = $('#tem_point_strokewidth').closest('li');
    var $radius = $('#tem_point_radius').closest('li');

    if (theStyle == 'none') {
        $fill.hide();
        $color.hide();
        $width.hide();
        $radius.hide();
    } else if (theStyle == 'numbers') {
        $fill.hide();
        $color.show();
        $width.hide();
        $radius.hide();
    } else if (theStyle == 'cross') {
        $fill.hide();
        $color.show();
        $width.show();
        $radius.show();
    } else {
        $fill.show();
        $color.show();
        $width.show();
        $radius.show();
    }
});


$('#tem_point_style').trigger('change');
// !#batch*Dialog
$('.batchDialog table thead th, .batchDialog table thead th').resizable({
    handles: "e",
    minHeight: 30,
    maxHeight: 30,
    minWidth: 20
});

$('.batchDialog table').on('keydown', 'textarea', function(e) {
    if (e.which == KEYCODE.enter) {
        e.preventDefault();
        $(this).blur();
        
        var start_col   = $(this).closest('td').index();
        console.log(start_col);
        
        $(this).closest('tr').next('tr').find('td').eq(start_col).find('textarea').focus();
        
    }
});

$('#batchEditDialog table').on('blur', 'textarea', function() {
    batchTableText(this, "Edit");
});

$('#batchTransDialog table').on('blur', 'textarea', function() {
    batchTableText(this, "Trans");
});

$('#batchAvgDialog').delegate('textarea', 'keydown', function(e) {
    if (e.which == KEYCODE.tab || e.which == KEYCODE.enter) {
        e.preventDefault();
        var start = $(this).get(0).selectionStart;
        var end = $(this).get(0).selectionEnd;
        var theChar = (e.which == KEYCODE.tab) ? "\t" : "\n";

        // set textarea value to: text before caret + tab + text after caret
        $(this).val($(this).val().substring(0, start)
                    + theChar
                    + $(this).val().substring(end));

        // put caret at right position again
        $(this).get(0).selectionStart = $(this).get(0).selectionEnd = start + 1;
    }
});

// !#gridFaces
$('#gridFaces').not('.disabled').click(function() {
    $('#showTransform').click();
    $('#destimages, #continua').hide();
    $('#grid').show();
});
//$('#grid-options').buttonset();
$('#createGrid').button().click( function() {
    createGrid();
});

//!#multiContinua
$('#multiContinua').not('.disabled').click(function() {
    $('#showTransform').click();
    $('#destimages, #grid').hide();
    $('#continua').show();
});
$('#createContinua').button().click( function() {
    createContinua();
});
$('#cimgs').change(function() {
    var c = $('#cimgs').val();
    if (c < 2) { c = 2; $('#cimgs').val(2); }
    if (c > 30) { c = 30; $('#cimgs').val(30); }
    $('#continua-imgs img').hide().slice(0,c).show();
});
$('#cimgs').val(4).change();
$('#continua-imgs img').droppable({
    hoverClass: 'hoverdrag',
    tolerance: "pointer",
    drop: function(event, ui) {
        // ! [FIX] png and gif images don't show up in transform drop
        this.src = fileAccess($(ui.draggable).attr('url').replace('.tem', '.jpg'));
    }
});

//!#batchModDelin
$('#batchModDelin').not('.disabled').click(function() {
    batchModDelin();
});
//! ***** FacialMetrics *****
$('#fmButtons').on('click', 'li', function() {
    $('#fm_name').val($(this).text());
    $('#fm_equation').val($(this).attr('data-equation'));
}).sortable({
    items: 'li',
    scope: 'fm',
    containment: '#facialmetricEQ'
});
$('#fm_delete').droppable({
    drop: function( event, ui ) {
        var $eq_to_remove = ui.draggable;
        
        $.ajax({
            data: {
                name: $eq_to_remove.text()
            },
            url: "scripts/fmDeleteEquation",
            success: function(data) {
                if (data.error) {
                    growl(data.errorText, 1000);
                } else {
                    $eq_to_remove.remove();
                }
            }
        });
    },
    scope: 'fm',
    hoverClass: "hover",
    tolerance: 'touch'
});
$('#fm_results').on('doubletap', 'thead th+th', function() {
    // delete column when double-clicking header
    var column_n = $('#fm_results thead th').index($(this));

    $("#fm_results tbody tr").each(function() {
        $(this).find("td:eq("+column_n+")").remove();
    });

    $(this).remove();
});
$('#fm_new').click(function() {
    fmAddEquation();
});
$('#facialMetrics').not('.disabled').click(function() {
    batchFacialmetrics();
});
// !#undo
$('#undo').not('.disabled').click(function() {
    if (WM.appWindow == 'delineate' && $delin.filter(':visible').length) {
        if (WM.delinfunc == 'move') {
            WM.undo.level = Math.max(0, WM.undo.level - 1);
            WM.current.tem = $.extend(true, [], WM.undo.tem[WM.undo.level]);
            WM.current.lines = $.extend(true, [], WM.undo.lines[WM.undo.level]);
            if (WM.undo.level == 0) $('#delin_save').removeClass('unsaved');
            drawTem();
        } else if (WM.delinfunc == 'sym' && WM.symPts.n > 0) {
            WM.symPts.n = WM.symPts.order.pop();
            nextSymPt('start');
        } else if (WM.delinfunc == '3pt') {
            // remove last item in eyeClicks
            var n = WM.eyeClicks.length;
            if (n == 1) {
                $('#leftEye').hide();
                WM.eyeClicks.pop();
                clickPt(0);
            } else if (n == 2 ) {
                $('#rightEye').hide();
                WM.eyeClicks.pop();
                clickPt(1);
            }
        }
    }
});
// !#redo
$('#redo').not('.disabled').click(function() {
    if (WM.appWindow == 'delineate' && $delin.filter(':visible').length) {
        if (WM.delinfunc == 'move') {
            WM.undo.level = Math.min(WM.undo.level + 1, WM.undo.tem.length - 1);
            WM.current.tem = $.extend(true, [], WM.undo.tem[WM.undo.level]);
            WM.current.lines = $.extend(true, [], WM.undo.lines[WM.undo.level]);
            if (WM.undo.level > 0) $('#delin_save').addClass('unsaved');
            drawTem();
        }
    }
});
// !#select
$('#select').not('.disabled').click(function() {
    if ($finder.filter(':visible').length) {
        // (un)select all files in the open folder
        var $openFolder = $finder.find('li.folder')
                               .filter(':not(.closed)')
                               .filter(':last');
        // unselect selected folders
        $openFolder.find('li.folder.selected').removeClass('selected'); 

        var $allfiles = $openFolder.find('> ul > li.file')
                               .filter(':visible:not(.nosearch)');
        if ($allfiles.length == $allfiles.filter('.selected').length) {
            // all files are already selected, so unselect instead
            $finder.find('li.file').removeClass('selected');
        } else {
            $allfiles.addClass('selected');
        }
        WM.finder.updateSelectedFiles();
    } else if (WM.appWindow == 'delineate') {
        if ($('.pt').length == $('.pt.selected').length) {
            // unselect all delineation points
            $('.pt').removeClass('selected');
        } else {
            // select all delineation points
            $('.pt').addClass('selected');
        }
    }
});
// !#createTransform
$('#createTransform').not('.disabled').click(function() {
    if (WM.appWindow == 'average') {
        getAverage();
    } else if (WM.appWindow == 'transform') {
        getTransform({async:true});
    } else if (WM.appWindow == 'finder') {
        var regex = new RegExp("(.tem$)", "g");
        var selTems = filesGetSelected('.tem', regex);

        if (selTems.length > 1) {
            $('#showAverage').click();
        } else {
            growl("Select more than 1 image to average", 1000);
        }
    }
});
// !#currentTem
$('#currentTem').on('click', 'li', function() {
    setCurrentTem($(this).attr('data-id'));
});
// !#currentProject
$('#currentProject').on('click', 'li', function() {
    projectSet($(this).attr('data-id'));
});
// !#isNewTem
$('#isNewTem').click( function() {
    var $button = $('#addTemDialog').parent().find('.default_button .ui-button-text');
    if ($(this).prop('checked')) {
        $button.text('Add');
    } else {
        $button.text('Edit');
    }
});
// !#editTemplate
$('#editTemplate').not('.disabled').click(function() {
    editTemplate();
});
// !#setPointLabels
$('#setPointLabels').not('.disabled').click(function() {
    setPointLabels();
});
$('#labelDialog ol').on('focus', 'input', function() {
    // point to and highlight the corresponding point when the label is in focus
    var n = parseInt($(this).attr('name'));
    nextPointLabel(n)
});

// !#setSymPoints
$('#setSymPoints').not('.disabled').click(function() {
    setSymPoints();
});
// !#whatsnew
$('#whatsnew').click( function() {
    $('#whatsnewDialog').dialog({ height: 500 });
});
// !#menuhelp
$('#menuhelp').click(function() {
    $('#help').dialog({ height: 500 });
});
// !#tinyhelp
$('.tinyhelp').click(function() {
    var topic = $(this).data('topic');
    var scrollTo = $('#help *[data-topic="' + topic + '"]');
    $('#help').dialog({ height: 500 }).scrollTop(
        scrollTo.offset().top -  $('#help').offset().top + $('#help').scrollTop()
    );
});
// !#emailLisa
$('#emailLisa').click(function() {
    WM.noOnBeforeUnload = true;
    location.assign("mailto:lisa.debruine@glasgow.ac.uk?subject=Online PsychoMorph");
    WM.noOnBeforeUnload = false;
});
// !#show_thumbs
$('#show_thumbs').change(function() {
    if ($(this).prop('checked')) {
        $finder.find('li.image').each (function(i) {
            $(this).css('background-image', 
                        'url(/scripts/fileAccess?thumb&file=' + 
                        $(this).attr('url') + ')');
        });
    } else {
        $finder.find('li.image')
               .css('background-image', 
                    'url(/include/images/finder/imgicon.php)');
    }
});
// !input[type=number]
// error-checking for all integer-type text boxes
$('input[type=number]').bind('keyup change blur', function(e) {
    var regex = /^(-|\+)?[0-9]{0,4}(\.[0-9]{1,3})?$/;
    if (!regex.test(this.value)) {
        $(this).addClass('error');
    } else {
        $(this).removeClass('error');
    }
});
// !#mask_trans
$('#mask_trans').change(function() {
    if ($(this).prop('checked')) {
        $('#maskExample').addClass('trans');
    } else {
        $('#maskExample').removeClass('trans');
    }
});

// ! interactive masking
$('#maskDialog ul input[type=checkbox]').change(function() {
    var type = $(this).attr('id').replace('mask_', '');
    var checked = $(this).prop('checked');
    maskViewCheck(type, checked);
});
// !#masktest
$('#masktest').click( function() {
    //var m = drawMask("145,146,147,148,149,150,151,152,153,154,155,156,157;157,184,183,145"); // halo
    var m = drawMask("111,186,110,185,109,134,135,136,137,138,139,140,141,142," +
                     "143,144,112,187,113,188,114;114,133,132,131,130,129,128," +
                     "127,126,125,111 : 112,120,121,122,123,124,114 ; 114,188," +
                     "113,187,112 : 111,186,110,185,109 ; 109,115,116,117,118," +
                     "119,111", 5); // face ears

    var $maskDialog = $('<div title="Masked Image" />').append(m);
    m.css({'width': '100%', 'max-width': WM.originalWidth + 'px', 'height': 'auto'});
    $maskDialog.dialog({
        position: { my: 'right top', at: 'right bottom', of: $('#menubar') },
        width: 450
    });
});

// !#transButton
$('#transButton').button().click(function() {
    var list = filesGetSelected('.image.hasTem', WM.project.id);

    if (list.length > 1) {
        var fromimage = urlToName($("#fromimage").attr('src'));
        var toimage = urlToName($("#toimage").attr('src'));
        var shape = parseFloat($("#shapePcnt0").val());
        var color = parseFloat($("#colorPcnt0").val());
        var texture = parseFloat($("#texturePcnt0").val());

        var batchText = '';
        $.each(list, function(i, img) {
            var newimg = '/trans' + img;
            batchText += img + "\t" + fromimage + "\t" + toimage + "\t" + 
                         shape + "\t" + color + "\t" + texture + "\t" + newimg + "\n";
        });

        $('#batchTrans').click();
        $('#batchTransDialog textarea').val(batchText.trim()).show();
    } else {
        getTransform({async: true});
    }
});
// !#continuum
$('#continuum').click(function() {
    if ($(".movie_settings:visible").length === 0) {
        $("#trans_settings").hide();
        $(".movie_settings").show();
        $('#continuum').html('Hide Continuum Settings');
    } else {
        $("#trans_settings").show();
        $(".movie_settings").hide();
        $('#continuum').html('Show Continuum Settings');
    }
    sizeToViewport();
});
// !#recent_creations
$("#recent_creations").on('click', 'img', function() {
    loadRecentCreation(this);
});
// !#clear_recent_creations
$('#clear_recent_creations').button().click(function() {
    $("#recent_creations img").remove();
});
// !#toggle_recent
$('#toggle_recent').click(function() {
    if ($("#recent_creations:visible").length) {
        $("#recent_creations").hide();
        $(this).find('span.checkmark').hide();
    } else {
        $("#recent_creations").show();
        $(this).find('span.checkmark').show();
    }
});

// ! ***** Delineation interface *****

$delin.click(function(e) {
    if (e.shiftKey && (e.metaKey || e.altKey)) {
        newDelinPoint(e);
    } else if (WM.eyeClicks.length < 3) {
        threePtDelin(e);
    }
}).on('doubletap', function() {
    if (WM.delinfunc == 'move') {
        // unselect all delineation points
        $('.pt.selected').removeClass('selected');
        // and reset the selectBox
        $('#selectBox').hide();
        drawTem();
    }
}).mousedown(function(e) {
    if (e.shiftKey) {
        $('#selectBox').prop({
            x: e.pageX,
            y: e.pageY
        }).css({
            top: e.pageY + 'px',
            width: '0px',
            left: e.pageX + 'px',
            right: '0px'
        });
    }
}).mousemove(function(e) {
    if ($('#selectBox').prop('x') !== false && 
        e.shiftKey && WM.pageEvents.mousebutton[1]) {
        boxHover(e);
    } else {
        resetSelectBox();
    }
});
$('#selectBox').mouseup(function(e) {
    if ($('#selectBox').prop('x') !== false) {
        boxSelect(e);
    }
}).mousemove(function(e) {
    if ($('#selectBox').prop('x') !== false && 
        e.shiftKey && WM.pageEvents.mousebutton[1]) {
        boxHover(e);
    } else {
        resetSelectBox();
    }
});

$delin.on("click", ".pt", function(e) {
    if (e.shiftKey) {
        $(this).toggleClass("selected");
    } else if (e.metaKey || e.ctrlKey) {
        if (WM.delinfunc == 'sym') {
            nextSymPt($(this).attr('n'));
        } else {
            var connectedPoints = $(this).data('connectedPoints');
            if ($(this).hasClass("selected")) {
                $.each(connectedPoints, function(j, pt) {
                    WM.pts[pt].removeClass('selected');
                });
            } else {
                $.each(connectedPoints, function(j, pt) {
                    WM.pts[pt].addClass('selected');
                });
            }
        }
        drawTem();
    }
}).on("doubletap", ".pt", function(e) {
    e.stopPropagation();
    var connectedPoints = $(this).data('connectedPoints');
    if ($(this).hasClass("selected")) {
        $.each(connectedPoints, function(j, pt) {
            WM.pts[pt].removeClass('selected');
        });
    } else {
        $.each(connectedPoints, function(j, pt) {
            WM.pts[pt].addClass('selected');
        });
    }
    drawTem();
}).on("mouseenter", ".pt", function(e) {
    if (WM.delinfunc == 'lineadd') {
        cursor('lineadd');
    } else if (WM.delinfunc == 'label') {
        return false;
    } else {
        //cursor('pointer');
    }

    var i = parseInt($(this).attr('n'));

    var pointName = (WM.delin.tem[i] !== undefined) ? WM.delin.tem[i].name : 'undefined';

    var thisx = round(WM.current.tem[i].x, 1);
    var thisy = round(WM.current.tem[i].y, 1);

    var footertext = '[' + i + '] ' + pointName +
                     ' x=<span class="x">' + thisx + '</span>; ' +
                     'y=<span class="y">' + thisy + '</span>';
    $('#footer-text').prop('data-persistent', $('#footer-text').html()).html(footertext);

    if ((e.metaKey || e.ctrlKey || WM.delinfunc == 'linesub') && WM.delinfunc != 'sym') {
        var conPts = $(this).data('connectedPoints');
        $.each(conPts, function(i,pt) {
            WM.pts[pt].addClass('couldselect');
        });
    }
    drawTem();
}).on("mouseup", ".pt", function(e) {
    if (WM.delinfunc == 'label') { return false; }

    if (e.shiftKey && (e.metaKey || e.ctrlKey)) {
        cursor('crosshair');
    } else if (WM.delinfunc == 'lineadd') {
        cursor('lineadd');
    } else {
        //cursor('auto');
    }

    $('.pt').removeClass('couldselect');

    updateUndoList();
    drawTem();
}).on("mouseout", ".pt", function(e) {
    // remove point name and replace with whatever is in data-persistent
    $('#footer-text').html($('#footer-text').prop('data-persistent'));
    
    if (e.metaKey || e.ctrlKey || WM.delinfunc == 'linesub') {
        var conPts = $(this).data('connectedPoints');
        $.each(conPts, function(i,pt) {
            WM.pts[pt].removeClass('couldselect');
        });
        drawTem();
    }
});

$delin.on("mousedown", ".pt", function(e) {
    if (WM.delinfunc == 'label') {
        return false;
    } else if (WM.delinfunc == 'lineadd') {
        var line = WM.current.lines.length - 1;
        var i = parseInt($(this).attr('n'));
        // check if last point if the same as this one
        var lastPt = WM.current.lines[line][WM.current.lines[line].length - 1];
        if (lastPt === undefined || lastPt != i) {
            WM.current.lines[line].push(i);
            var t = 'Added a point to the new line [' + WM.current.lines[line].join() + ']';
            $('#footer-text').html(t).prop('data-persistent', t);
        }
    } else if (WM.delinfunc == 'linesub') {
        var conLines = $(this).data('connectedLines');
        $.each(conLines, function(idx, line) {
            //var nlines = WM.current.lines.length;
            //WM.current.lines = WM.current.lines.splice(line, 1);
            WM.current.lines[line] = [];
        });
        removeTemPoints([]);
        
    /*} else if (WM.delinfunc == 'sym') {
        if (e.metaKey || e.ctrlKey) {
            nextSymPt($(this).attr('n'));
        }*/
    } else if (WM.delinfunc == "mask") {
        if (e.metaKey || e.ctrlKey) {
            var conLines = $(this).data('connectedLines');

            $.each(conLines, function(idx, line) {
                addToCustomMask(';');
                $.each(WM.current.lines[line], function(idx2, pt) {
                    addToCustomMask(pt);
                });
            });
        } else {
            addToCustomMask($(this).attr('n'));
        }
    }
    return false;
});

$('#delin_toolbar').draggable().resizable({
    minHeight:30,
    minWidth:30
});

$('#pointer').draggable();
$('#toolbar_switcher').click(function() {
    $('#delin_toolbar').show();
    $(this).hide();
});
$('#obj_switcher').on('click', 'button', function(e) {
    var $this = $(this);
    if (e.metaKey || e.ctrlKey) {
        $this.data('object').remove();
    } else if ($this.hasClass('selected')) {
        $this.data('object').select(false);
    } else {
        $this.data('object').select(true);
        $('#footer-text').html($this.attr('title'));
    }
});
$('#d3_debug').button({
    text: false,
    icons: { primary: "wm-bug-icon" }
}).click( function() {
    $(WM.d3.stats.domElement).toggle(); 
});
$('#d3_morph').button({
    text: false,
    icons: { primary: "wm-morph-icon" }
}).click( function() {
    var $theSlider = WM.d3.morph();
    $theSlider.css({
        position: 'absolute',
        top: '1em',
        right: '1em',
        width: '15em',
        color: 'white'
    }).appendTo('#threeD'); 
});
$('#d3_lock_x').button({
    text: false,
    icons: { primary: "wm-up-down-icon" }
}).click(function() {
    WM.d3.toggle_lock('x');
});
$('#d3_lock_y').button({
    text: false,
    icons: { primary: "wm-left-right-icon" }
}).click(function() {
    WM.d3.toggle_lock('y');
});
$('#d3_lock_z').click(function() {
    WM.d3.toggle_lock('z');
});

$('#d3_light').button({
    text: false,
    icons: { primary: "wm-lightbulb-icon" }
}).click(function() {
    WM.d3.toggle_light();
});
$('#d3_spin').button({
    text: false,
    icons: { primary: "wm-color-icon" }
}).click( function() {
    WM.d3.spin = !WM.d3.spin;
});
$('#d3_hologram').button({
    text: false,
    icons: { primary: "wm-hologram-icon" }
}).click( function(e) {
    WM.d3.hologram(e)
});
$('#delin_delete').button({
    text: false,
    icons: { primary: "wm-delete-icon" }
}).click(function() {
    $('#deleteItems').click();
});
$('#d3_sethue').button({
    text: false,
    icons: { primary: 'wm-color-icon' }
}).click(function() {
    WM.d3.set_hue();
});
$('#d3_wireframe').button({
    text: false,
    icons: { primary: "wm-wireframe-icon" }
}).click(function() {
    WM.d3.toggle_wireframe();
});
$('#d3_texture').button({
    text: false,
    icons: { primary: "wm-skin-icon" }
}).click(function() {
    WM.d3.toggle_texture();
});

// !delin_hide

$("#delin_close").button({
    text: false,
    icons: { primary: "ui-icon-close" }
}).click(function() {
    $('#toggle_delintoolbar').click();
});
// !#delin_undo
$("#delin_undo").button({
    text: false,
    icons: { primary: "ui-icon-arrowreturn-1-s" }
}).click(function() {
    $('#undo').click();
});
// !#delin_redo
$("#delin_redo").button({
    text: false,
    icons: { primary: "ui-icon-arrowreturn-1-n" }
}).click(function() {
    $('#redo').click();
});
// !#delin_fitsize
$("#delin_fitsize").button({
    text: false,
    icons: { primary: "wm-fitsize-icon" }
}).click(function() {
    $('#fitsize').click();
});
// !#delin_zoomin
$("#delin_zoomin").button({
    text: false,
    icons: { primary: "wm-zoomin-icon" }
}).click(function() {
    $('#zoomin').click();
});
// !#delin_zoomout
$("#delin_zoomout").button({
    text: false,
    icons: { primary: "wm-zoomout-icon" }
}).click(function() {
    $('#zoomout').click();
});
// !#delin_center
$("#delin_center").button({
    text: false,
    icons: { primary: "wm-center-icon" }
}).click(function() {
    if (WM.d3) { WM.d3.center(); }
});
// !#delin_zoomoriginal
$("#delin_zoomoriginal").button({
    text: false,
    icons: { primary: "wm-origsize-icon" }
}).click(function() {
    $('#zoomoriginal').click();
});
// !#delin_save
$("#delin_save").button({
    text: false,
    icons: { primary: "ui-icon-disk" }
}).click(function() {
    $('#save').click();
});
$("#delin_refresh").button({
    text: false,
    icons: { primary: "ui-icon-refresh" }
}).click(function() {
    $('#refresh').click();
});
// !#delin_next
$("#delin_next").button({
    text: false,
    icons: { primary: "ui-icon-seek-next" }
}).click(function() {
    nextImg();
});
$("#delin_prev").button({
    text: false,
    icons: { primary: "ui-icon-seek-prev" }
}).click(function() {
    prevImg();
});
$("#showDelinHelp").button({
    text: false,
    icons: { primary: "ui-icon-help" }
}).click(function() {
    $('#delinHelp').dialog({
        position: { my: 'right top', at: 'right bottom', of: $('#delin_toolbar') },
        width: 450,
        modal: false
    });
});
$("#showDelinExample").button({
    text: false,
    icons: { primary: "ui-icon-help" }
}).click(function() {
    $('#delinExample').dialog({
        position: { my: 'right top', at: 'right bottom', of: $('#delin_toolbar') },
        width: 330,
        modal: false
    });
});
$('.buttonset').buttonset();

$('#quickSwitch li').click( function(e) {
    var w = $(this).data('window');
    e.stopPropagation();
    
    if (w == 'menu') {
        $('#menubar').show();
        $('#menubar ul').hide();
        $('#quickSwitch').hide();
    } else {
        interfaceChange(w);
    }
    $('#quickSwitch').hide();
});

$('#toggleMenu:visible').click( function(e) {
    e.stopPropagation();
    if ($('#menubar:visible').length || $('#quickSwitch:visible').length) {
        $('#menubar').hide();
        $('#quickSwitch').hide();
    } else {
        $('#quickSwitch').show();
    }
});



// ! ***** Queue *****
WM.queue = new queue();
WM.queue.queueCountUpdate();

$('#clearQueue').click( function() { WM.queue.clear(); });

$('#clearComplete').click( function() { WM.queue.clear('complete');    });

$('#pauseQueue').click( function() { WM.queue.pauseAll(); });

$('#restartQueue').click( function() { WM.queue.restartAll(); });

$queue.on('click', 'li.queueItem:not(.active)', function(e) {
    if (e.which == KEYCODE.ctrl || e.which == KEYCODE.cmd) {
        $(this).data('obj').destroy();
    }
}).on('click', 'li.queueItem.complete:not(.ui-state-error)', function() {
    var file = $(this).data('obj').returnData.newFileName;
    console.log('fileShow(' + file + ')');
    fileShow(file);
}).on('click', 'li.queueItem.paused', function(){
    $(this).data('obj').wait();
}).on('click', 'li.queueItem.waiting', function(){
    $(this).data('obj').pause();
}).on('click', 'li.queueItem.active', function(){
    growl('You cannot pause or delete an active process', 1000);
}).on('click', 'li.queueItem.ui-state-error', function() {
    growl($(this).data('obj').errorText);
});

// ! ***** Hide Things *****

$('#queue_n').hide();
$d3.hide();

// ! ***** Done Loading *****

$("body").removeClass("loading");

sizeToViewport();

if (WM.user.id) {
    $('#menu_window li').removeClass('disabled');
    userLoad();
} else {
    interfaceChange('login');
}
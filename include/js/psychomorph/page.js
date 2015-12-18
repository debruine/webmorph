// functions that should happen ONLOAD

if (navigator.userAgent.indexOf('Mac OS X') != -1) {
    $("body").addClass("mac");
} else {
    $("body").addClass("pc");
}

// !contextmenu functions
$(document).on('contextmenu', '*', function(e) {
    e.preventDefault();
}).on('contextmenu', '#trash > span', function(e) {
    e.stopPropagation();
    
    var item_info = [
        {
            name: 'Empty Trash',
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
}).on('contextmenu', '#finder li.folder:not(#trash)', function(e) {
    e.stopPropagation();
    
    var folder = $(this);
    var folder_name = $(this).find('> span').click().text();
    var item_info = [];
    
    if ($finder.find('>ul> li.folder:eq(0) > span').text() == folder_name) {
        // folder is base directory
        folder_name = 'Base Folder';
    } else {
        item_info.push({
            name: 'Rename',
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
    
    var upload = {
        name: 'Upload to ' + folder_name,
        func: function() {
            $('#upload').click();
            $('.context_menu').remove(); 
        }
    };
    
    if (PM.pasteBoard.length) {
        var cutlist = 0;
        $.each(PM.pasteBoard, function(i, v) {
            if ($('li.to_cut[url="' + v + '"]').length) cutlist++;
        });
        
        var pasteName = (PM.pasteBoard.length == cutlist) ? 'Move ' : 'Copy ';
        pasteName += PM.pasteBoard.length + ' file';
        pasteName += (PM.pasteBoard.length>1 ? 's' : '');
        pasteName += ' to ' + folder_name
            
        item_info.push({
            name: pasteName,
            func: function() {
                $('#pasteItems').click();
                $('.context_menu').remove(); 
            }
        });
        item_info.push('break');
    }
    
    item_info.push(upload);
    
    context_menu(item_info, e);
}).on('contextmenu', '#finder li.file > span', function(e) {
    e.stopPropagation();
    
    var file = $(this).closest('li.file');
    file.addClass('selected');
    updateSelectedFiles();
    var tf = $finder.find('li.file.selected').filter(':visible').length;
    var total_files = (tf > 1) ? ' ' + tf + ' Files' : '';    
    
    var delin = {
        name: 'Delineate',
        func: function() {
            file.dblclick(); 
            $('.context_menu').remove(); 
        }
    };
    
    var rename = {
        name: 'Rename',
        func: function() {
            fileRename();
            $('.context_menu').remove(); 
        }
    };
    
    var copy = {
        name: 'Copy' + total_files,
        func: function() {
            $('#copyItems').click(); 
            $('.context_menu').remove(); 
        }
    };
    
    var cut = {
        name: 'Cut' + total_files,
        func: function() {
            $('#cutItems').click(); 
            $('.context_menu').remove(); 
        }
    };
    
    var del = {
        name: 'Move' + total_files + ' to Trash',
        func: function() {
            $('#deleteItems').click(); 
            $('.context_menu').remove(); 
        }
    };
    
    var download = {
        name: 'Download' + total_files,
        func: function() {
            $('#download').click(); 
            $('.context_menu').remove(); 
        }
    };
    
    if (file.hasClass('image') || file.hasClass('tem')) {
        if (tf == 1) {
            var item_info = [
                delin, 'break', 
                copy, cut, del, 'break', 
                rename, 'break', 
                download
            ];
        } else {
            var item_info = [
                copy, cut, del, 'break', 
                download
            ];
        }
    } else {
        if (tf == 1) {
            var item_info = [
                copy, cut, del, 'break', 
                rename, 'break', 
                download
            ];
        } else {
            var item_info = [
                copy, cut, del, 'break', 
                download
            ];
        }
    }
    context_menu(item_info, e);
}).on('mouseleave', '.context_menu', function(e) { $(this).remove(); });

// ! project_list functions
$('#project_list').on('click', '.projectOwnerDelete', function() {
    var proj_id = $(this).closest('tr').data('id');
    projectOwnerDelete(proj_id, $(this).data('id'));
}).on('keydown', '.projectOwnerAdd', function(e) {
    if (e.which == KEYCODE.enter) { projectOwnerAdd(this); }
}).on('click', '.go_to_project', function() {
    var proj_id = $(this).closest('tr').data('id');
    PM.project = proj_id;
    $('#currentProject li span.checkmark').hide();
    $('#currentProject li[data-id=' + proj_id + '] span.checkmark').show();
    $('#showFinder').click();
}).on('dblclick', 'td:nth-child(2)', function() {
    projectEdit(this, "name");
}).on('dblclick', 'td:nth-child(3)', function() {
    projectEdit(this, "notes");
});

// ! window functions
$(window).bind('resize', sizeToViewport)
.blur(function() {
    // removes growl notifications when download window is ready
    $('div.growl').remove();
}).bind('beforeunload', function() {
    // confirm leaving the app
    // the back button doesn't work as most would assume.
    if (!PM.no_onbeforeunload && PM.interface !== 'login') {
        return "Do you want to leave PsychoMorph?";
    } else {
        PM.no_onbeforeunload = false;
    }
});
         
// !document functions
// functions to keep track of mouse and key events
$(document).mousedown(function(e) {
    PM.pageEvents.mousebutton[e.which] = true;
}).mouseup(function(e) {
    PM.pageEvents.mousebutton[e.which] = false;
}).keydown(function(e) {
    PM.pageEvents.key[e.which] = true;
}).keyup(function(e) {
    PM.pageEvents.key[e.which] = false;
}).ajaxError(function() {
    //growl("Sorry, there was an error with this function.", 1000);
}).delegate('.ui-dialog', 'keyup', function(e) {
    // pressing the enter key selects default button on dialogs
    var tagName = e.target.tagName.toLowerCase();
    tagName = (tagName === 'input' && e.target.type === 'button') ? 'button' : tagName;

    if (e.which === $.ui.keyCode.ENTER 
        && tagName !== 'textarea' 
        && tagName !== 'select' 
        && tagName !== 'button') {
        $(this).find('.ui-dialog-buttonset button.ui-state-focus').eq(0).trigger('click');
        return false;
    }
}).bind('drop dragover', function(e) {
    // drag and drop upload
    e.preventDefault();
}).keyup(function(e) {
    // ! ***** keyboard shortcuts  *****
    // e.which: a=>65 ... z=>90, 0=>48 ... 9=>57
    // lookup at http://api.jquery.com/event.which/
    if (PM.interface == 'delineate' && !((e.ctrlKey || e.metaKey) 
        && e.shiftKey) && PM.delinfunc == 'move') {
        cursor('auto');
        quickhelp();
    }
    if (e.which == KEYCODE.ctrl || e.which == KEYCODE.cmd) {
        // cursor is hovering over a delin point
        $('.pt').removeClass('couldselect');
        drawTem();
    }
}).keydown(function(e) {
    // list of keycodes for navigation (except when in input boxes)
    var navKeys = [
        KEYCODE.left_arrow, 
        KEYCODE.right_arrow, 
        KEYCODE.down_arrow, 
        KEYCODE.up_arrow, 
        KEYCODE.delete, 
        KEYCODE.backspace
    ];
    
    // list of keycodes for text functions (except when in input boxes)
    var funcKeys = [
        KEYCODE.x,
        KEYCODE.c,
        KEYCODE.v,
        KEYCODE.a
    ];

    if (    (    $('.ui-dialog:visible').length
                 || (PM.interface == 'login')
                 || $('input:focus').length
                 || $('textarea:focus').length
            ) && 
            (    ((e.ctrlKey || e.metaKey) && ( funcKeys.indexOf(e.which) !== -1 )) 
                || (navKeys.indexOf(e.which) !== -1) 
            )
            
        ) {
        // do not override cut/paste/copy/select all keyboard shortcuts 
        // and delete/arrow functions when dialog windows are open
        // or on the login page or an input/textarea is focussed
        return true;
    } else if (e.altKey) {
        // ! alt-Key shortcuts
        if (e.which == KEYCODE.a) {
            $('#createTransform').click();           // alt-A
        } else if (e.which == KEYCODE.c) {
            $('#closeMouth').click();                // alt-C
        } else if (e.which == KEYCODE.d) {
            $('#autoDelineate').click();             // alt-D
        } else if (e.which == KEYCODE.f) {
            $('#fitTemplate').click();               // alt-F
        } else if (e.which == KEYCODE.l) {
            if (e.shiftKey) {
                $('#deleteLine').click();            // shift-alt-L
            } else {
                $('#newLine').click();               // alt-L
            }
        } else if (PM.interface == 'delineate' 
                &&   (e.which == KEYCODE.plus 
                   || e.which == KEYCODE.add 
                   || e.which == KEYCODE.equal_sign)) {
            temSizeChange(1);                        // alt-plus
        } else if (PM.interface == 'delineate' 
                &&       (e.which == KEYCODE.minus 
                    || e.which == KEYCODE.subtract 
                    || e.which == KEYCODE.dash)) {
            temSizeChange(-1);                       // alt-minus
        } else {
            return true;
        }
    } else if (e.ctrlKey || e.metaKey) {
        // ! shift-cmd shortcuts
        if (e.shiftKey) {
            if (e.which == KEYCODE.a) {
                $('#batchAverage').click();          // shift-cmd-A
            } else if (e.which == KEYCODE.d) {
                $('#batchModDelin').click();         // shift-cmd-D
            } else if (e.which == KEYCODE.e) {
                $('#alignEyes').click();             // shift-cmd-E
            } else if (e.which == KEYCODE.f) {
                $('#facialMetrics').click();         // shift-cmd-F
            } else if (e.which == KEYCODE.g) {
                $('#gridFaces').click();             // shift-cmd-G
            } else if (e.which == KEYCODE.i) {
                $('#mirror').click();                // shift-cmd-I
            } else if (e.which == KEYCODE.k) {
                $('#crop').click();                  // shift-cmd-K
            } else if (e.which == KEYCODE.l) {
                $('#rotate').click();                // shift-cmd-L
            } else if (e.which == KEYCODE.m) {
                $('#mask').click();                  // shift-cmd-M
            } else if (e.which == KEYCODE.n) {
                $('#batchRename').click();           // shift-cmd-N
            } else if (e.which == KEYCODE.o) {
                $('#movingGif').click();             // shift-cmd-O
            } else if (e.which == KEYCODE.p) {
                $('#singlePCA').click();             // shift-cmd-P
            } else if (e.which == KEYCODE.r) {
                $('#resize').click();                // shift-cmd-R        
            } else if (e.which == KEYCODE.s) {
                $('#saveAs').click();                // shift-cmd-S
            } else if (e.which == KEYCODE.t) {
                $('#batchTransform').click();        // shift-cmd-T
            } else if (e.which == KEYCODE.v) {
                $('#PCvis').click();                 // shift-cmd-V
            } else if (e.which == KEYCODE.w) {
                $('#webcamPhoto').click();           // shift-cmd-W
            } else if (e.which == KEYCODE.y) {
                $('#symmetrise').click();            // shift-cmd-Y
            } else if (e.which == KEYCODE.z) {
                $('#redo').click();                  // shift-cmd-Z    
            } else if (PM.interface == 'delineate' && PM.delinfunc == 'move') {
                setTimeout(function() {
                    // delay quickhelp so it doesn't show every time you use a shift-cmd shortcut
                    if ((PM.pageEvents.key[KEYCODE.cmd] || PM.pageEvents.key[KEYCODE.ctrl]) 
                         && PM.pageEvents.key[KEYCODE.shift]) {
                        cursor('crosshair');
                        quickhelp('Click to add a point');
                    }
                }, 500);
            } else {
                return true;
            }
        } else {
            // ! cmd-key shortcuts
            if (e.which == KEYCODE.backspace || e.which == KEYCODE.delete) {
                $('#deleteItems').click();             // cmd-delete or cmd-backspace
            } else if (e.which == KEYCODE.right_arrow) {
                nextImg();                             // cmd-right
            } else if (e.which == KEYCODE.left_arrow) {
                prevImg();                             // cmd-left
            } else if (e.which == KEYCODE['0'] || e.which == KEYCODE['0n']) {
                $('#zoomoriginal').click();            // cmd-0
            } else if (e.which == KEYCODE['1'] || e.which == KEYCODE['1n']) {
                $('#showFinder').click();              // cmd-1
            } else if (e.which == KEYCODE['2'] || e.which == KEYCODE['2n']) {
                $('#showDelineate').click();           // cmd-2
            } else if (e.which == KEYCODE['3'] || e.which == KEYCODE['3n']) {
                $('#showAverage').click();             // cmd-3
            } else if (e.which == KEYCODE['4'] || e.which == KEYCODE['4n']) {
                $('#showTransform').click();          // cmd-4
            } else if (e.which == KEYCODE['5'] || e.which == KEYCODE['5n']) {
                $('#showProjects').click();           // cmd-5
            } else if (e.which == KEYCODE.a) {
                $('#select').click();                 // cmd-a
            } else if (e.which == KEYCODE.c) {
                $('#copyItems').click();              // cmd-c
            } else if (e.which == KEYCODE.d) {
                $('#download').click();               // cmd-d
            } else if (e.which == KEYCODE.f) {
                $('#find').click();                   // cmd-f
            } else if (e.which == KEYCODE.g) {
                $('#fileListGet').click();            // cmd-g
            } else if (e.which == KEYCODE.i) {
                $('#getInfo').click();                // cmd-i
            } else if (e.which == KEYCODE.m) {
                $('#fitsize').click();                // cmd-m
            } else if (e.which == KEYCODE.n) {
                $('#newFolder').click();              // cmd-n
            } else if (e.which == KEYCODE.p) {
                $('#newProject').click();             // cmd-p
            } else if (e.which == KEYCODE.q) {
                $('#logout').click();                 // cmd-q
            } else if (e.which == KEYCODE.r) {
                $('#refresh').click();                // cmd-r
            } else if (e.which == KEYCODE.s) {
                $('#save').click();                   // cmd-s
            } else if (e.which == KEYCODE.t) {
                $('#toggletem').click();              // cmd-t
            } else if (e.which == KEYCODE.u) {
                $('#uploadFiles').click();            // cmd-u
            } else if (e.which == KEYCODE.v) {
                $('#pasteItems').click();             // cmd-v
            } else if (e.which == KEYCODE.w) {
                $(".modal:visible").dialog("close");  // cmd-w
            } else if (e.which == KEYCODE.x) {
                $('#cutItems').click();               // cmd-x        
            } else if (e.which == KEYCODE.z) {
                $('#undo').click();                   // cmd-z    
            } else if (e.which == KEYCODE.plus 
                    || e.which == KEYCODE.add 
                    || e.which == KEYCODE.equal_sign) {
                $('#zoomin').click();                 // cmd-plus (+)
            } else if (e.which == KEYCODE.minus 
                    || e.which == KEYCODE.subtract 
                    || e.which == KEYCODE.dash) {
                $('#zoomout').click();                 // cmd-minus (-)
            } else if (e.which == KEYCODE.comma) {
                $('#prefs').click();                 // cmd-comma (,)
            } else if ($('.pt:hover').length) {
                // cursor is hovering over a delin point
                var n = $('.pt:hover').attr('n');
                var conPts = PM.pts[n].data('connectedPoints');
                $.each(conPts, function(i,pt) {
                    PM.pts[pt].addClass('couldselect');
                });
                drawTem();
            } else {
                return true;
            }
        }
        return false;
    } else if ((e.which == KEYCODE.backspace | e.which == KEYCODE.delete) 
                && $('#average_list li.selected').length) {
        // delete selected items for average list
        $('#average_list li.selected').remove();
        averageListCheck();
    } else if (    e.which == KEYCODE.backspace 
                && ($("input:focus").length === 0) 
                && ($("textarea:focus").length === 0)
              ) {
        // pressed backspace and no inputs are focussed
        // do nothing, just prevents accidental page back in FireFox
    } else if (e.which == KEYCODE.enter && PM.delinfunc == 'lineadd') {
        // end new line
        PM.delinfunc = 'move';
        cursor('auto');
        quickhelp();
        var line = PM.current_lines.length - 1;
        PM.line_colors[line] = 'default';
        if (PM.current_lines[line].length < 2) { 
            $('#footer').html('New line cancelled');
            PM.current_lines.pop();
        } else {
            drawTem();
            var t = 'New line finished [' + PM.current_lines[line].join() + ']';
            $('#footer').html(t).prop('data-persistent', t);
        }	    
    } else if (e.which == KEYCODE.enter 
                && $finder.filter(':visible').length 
                && $finder.find('li.file.selected').length == 1) {
        // pressed return and finder is visible and one file is selected
        fileRename();
    } else if (e.which == KEYCODE.enter 
                && $finder.filter(':visible').length 
                && $finder.find('li.file.selected').length === 0 
                && $finder.find('li.folder').not('.closed').length > 0) {
        // pressed return and finder is visible and one folder is selected
        folderRename();
    } else if (e.which == KEYCODE.enter 
                && $('button.ui-state-focus:visible').length == 1) {
        // pressed enter and only 1 visible button is focussed
        $('button.ui-state-focus:visible').click();
    } else if (e.which == KEYCODE.left_arrow) { // pressed left
        if (PM.interface == 'delineate') {
            nudge(-1, 0);
        } else if ($finder.find('.imageView:visible').length) {
            // go to previous image
            $finder.find('li.file.selected, li.folder:not(.closed)')
                   .last().prevAll('li:visible').first().find('> span').click();
        } else if ($finder.filter(':visible').length) {
            // go up a directory
            if ($finder.find('li.file.selected').length === 0) {
                var $lastOpen = $finder.find('li.folder:not(.closed)[path!=""]:last').parents('li.folder').eq(0);
                console.log($lastOpen.attr('path'));
                $lastOpen.find('> span').click();
            }
            $finder.find('li.file.selected').removeClass('selected');
            updateSelectedFiles();
        }
    } else if (e.which == KEYCODE.up_arrow) { // pressed up
        if (PM.interface == 'delineate') {
            nudge(0, -1);
        } else if ($finder.find('.imageView:visible').length) {
            // go up a directory
            if ($finder.find('li.file.selected').length === 0) {
                var $lastOpen = $finder.find('li.folder:not(.closed)[path!=""]:last')
                                       .parents('li.folder').eq(0);
                console.log($lastOpen.attr('path'));
                $lastOpen.find('> span').click();
            }
            $finder.find('li.file.selected').removeClass('selected');
            updateSelectedFiles();
        } else if ($finder.filter(':visible').length) {
            $finder.find('li.file.selected, li.folder:not(.closed)')
                   .last().prevAll('li:visible').first().find('> span').click();
        }
    } else if (e.which == KEYCODE.right_arrow) { // pressed right
        if (PM.interface == 'delineate') {
            nudge(1, 0);
        } else if ($finder.find('.imageView:visible').length) {
            // go to next image
            $finder.find('li.file.selected, li.folder:not(.closed)')
                   .last().nextAll('li:visible')
                   .first().find('> span').click();
        } else if ($finder.filter(':visible').length) {
            // go down a directory
            $nextOpen = $finder.find('li.folder:not(.closed)')
                                .last().find('li.folder')
                                .first().find('>span');
            if ($nextOpen.length) { 
                $nextOpen.click();
            } else {
                $finder.find('li.folder:not(.closed)')
                       .last().find('li.file')
                       .first().click();
            }
        }
    } else if (e.which == KEYCODE.down_arrow) { // pressed down 
        if (PM.interface == 'delineate') {
            nudge(0, 1);
        } else if ($finder.find('.imageView:visible').length) {
            return false;
        } else if ($finder.filter(':visible').length) {
            // go to next image
            $finder.find('li.file.selected, li.folder:not(.closed)')
                   .last().nextAll('li:visible')
                   .first().find('> span').click();
        }
    } else if (e.which == KEYCODE.esc) { // pressed escape 
        $('#refresh').click();
    } else {
        return true;
    }
    e.preventDefault();
});

// show and hide menubar
$('ul.menubar li.menucategory').mouseleave(function() {
    $(this).find('>ul').hide();
}).mouseenter(function() {
    $(this).find('>ul').show();
}).find('>ul>li').click(function() { 
    if (!$(this).hasClass('disabled')) {
        // log all menu function calls
        console.debug('menu: #' + $(this).attr('id') + '.click()'); 
        $(this).parent('ul').hide();
    } else {
        console.debug('menu: #' + $(this).attr('id') + '.click() (disabled)');
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
$('#menu_username').click( function() {
    $('#prefs').click();
});
$('#register-button').button().click(function() {
    if ($('#loginInterface .reg_item:visible').length) {
        // check validity
        var error = false;
        $('#login_error').html('');
        
        if ($('#login_email').val().length < 7) {
            error = true;
            $('#login_error').append('<li>Your email address needs to be an email address</li>');
            $('#login_email').addClass('error').focus().select();
        } else {
            $('#login_email').removeClass('error');
        }
    
        if ($('#login_auth').val().length != 7) {
            error = true;
            $('#login_error').append('<li>Please enter the correct invite code. Access to online psychomorph is currently restricted. Ask Lisa for an invite code if you would like to be an alpha tester.</li>');
            $('#login_auth').addClass('error').focus().select();
        } else {
            $('#login_auth').removeClass('error');
        }
        
        if (error) { return false; }
        
        $('#login_error').append('<li>Checking your registration details...</li>');
        
        registerUser();
    } else {
        $('#loginInterface .reg_item').show();
        $('#loginInterface .login_item').hide();
        $('#register-button').addClass('ui-state-focus');
        $('#login-button').removeClass('ui-state-focus');
        $('#loginBox thead th').html('Register for an Account');
    }
});
$('#reset-password-button').button().click( function() {
    var email = $('#login_email').val();
    
    if (email == '') {
        $('#login_error').html("<li>Please fill in your email address first.</li>");
        return false;
    }
    $('#login_error').html("<li>Checking for your account...</li>");

    $.ajax({
        url: 'scripts/userPasswordReset',
        data: { email: email },
        success: function(data) {
            if (data.error) {
                $('#login_error').html('<li>' + data.errorText + '</li>');
            } else {
                $('#login_error').html("<li>Check your email for the new password.</li>");
            }
        }
    });
});
$('#login_password').keyup( function(e) {
    if (e.which === KEYCODE.enter) {
        $('#login-button').click();
    }
});
$('#login-button').button().click(function() {
    loginUser();
});
$('#logout').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    logoutUser();
});
// set events for tag list
/*
$('#taglist').on('click', 'a', function() {
    var theTag = $(this).html();
    if (theTag == 'ALL') {
        $('#my_images div').show();
    } else if (theTag == 'NONE') {
        $('#my_images div').hide();
    } else {
        $('#my_images img:not([title*=";' + theTag + ';"])').closest('div').hide();
    }
    resizeTags();
});
*/
//$finder.resizable({
//    handles: "s"
//});
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

$('#average_list').on('click', 'li', function() {
    $(this).toggleClass('selected');
    $finder.find('li.file').removeClass('selected');
});
$('#avg_image_box').droppable({
    hoverClass: 'hoverdrag',
    tolerance: "pointer",
    drop: function(event, ui) {
        var $files = ui.helper.find('li.file');
        
        $files.each( function() {
            var url = $(this).attr('url');
            var $li = $('<li />').text(urlToName(url))
                                 .attr('data-url', url)
                                 .css('background-image', 'url(' + fileAccess(url, true) + ')');
            $('#average_list').append($li).show();
            $('#average').hide();
        });

        averageListCheck();
    }
});
$('#destimages img:not(.nodrop), #grid img').droppable({
    hoverClass: 'hoverdrag',
    tolerance: "pointer",
    drop: function(event, ui) {
        // ! [FIX] png and gif images don't show up in transform drop
        this.src = fileAccess($(ui.draggable).attr('url').replace('.tem', '.jpg'));
        setTimeout("$('#transform').width($('#transimage').width()).height($('#transimage').height());",500);
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
$('#view-average-button').button().click(function() {
    getAverage();
});
$('#clear-average-button').button().click(function() {
    $('#average_list').hide().find('li').remove();
    $('#average').show().css('background-image', PM.blankBG).attr({
        'averaged': ''
    });
    $('#footer').html('');
});
// ! save buttons
$('#save-button, #trans-save-button').button({
    disabled: true
}).click(function() {
    if (PM.interface == 'average') {
        var $imgBox = $('#average');
    } else if (PM.interface == 'transform') {
        var $imgBox = $('#transform');
    }
    
    $imgBox.data('savefolder', PM.project + "/.tmp/");
    var tem = $imgBox.data('savefolder') + $imgBox.data('tem');
    var img = $imgBox.data('savefolder') + $imgBox.data('img');
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
                    $(this).dialog("close");
                    var savename = PM.project + '/' + $(this).find('input').val();
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
                                $('#footer').html(data.newfilename + ' saved');
                                loadFiles(data.newfilename);
                            } else {
                                $('<div title="Problem Saving Image" />').html(data.errorText).dialog();
                            }
                            
                        }
                    });
                }
            }
        }
    });
});

// remove growl notifications on double-click
$('body').on('dblclick', '.growl', function() {
    $(this).remove();
});

$('#imagebox').click( function(e) { 
	e.stopPropagation();
});

// ! ***** finder functions *****
$finder.on('dblclick', 'li.file.image, li.file.tem', function() { 
    // open in delineator on double-click
    delinImage($(this).attr('url'));
}).on('click', '> ul > li.folder ul', function(e) {
    // return to base directory when clicking under it
    var folderup = $(this).closest('li.folder');
    folderup.find('> span').click();
}).on('click', '> ul > li.folder > ul li', function(e) {
    // cancel return to base directory when clicking on an item
    e.stopPropagation();
}).on('click', 'li.file', function(e) {
    $('input:visible').blur(); // blur focus on any inputs
    // manage selection of files on click or meta-click
    if (!(e.ctrlKey || e.metaKey || e.shiftKey)) {
        // unselect other files if ctrl/cmd/shift NOT held down
        $('li.file.selected').removeClass('selected');
    } else if (e.shiftKey) {
        // select all files between this one and the nearest selected one
        var $prevUnSel = $(this).prevUntil('li.selected');
        var $nextUnSel = $(this).nextUntil('li.selected');
        var $prevAll = $(this).prevAll('li');
        var $nextAll = $(this).nextAll('li');
        if ($prevUnSel.length < $prevAll.length) {
            $prevUnSel.addClass('selected');
        } else if ($nextUnSel.length < $nextAll.length) {
            $nextUnSel.addClass('selected');
        }
    }
    $(this).toggleClass('selected');
    $(this).siblings('li.folder').addClass('closed').find('li.folder').addClass('closed');
    updateSelectedFiles();
}).on('click', 'li.file.image', function(e) {
    // show image in imgbox on click
    if ($finder.hasClass('imageView')) { return false; }
    
    var theURL = $(this).attr('url');
    var $theImg = $imagebox.find('img');
    if ($theImg.filter(':visible').attr('src') != fileAccess(theURL)) {
        $theImg.attr('src', PM.loadImg).attr('src', fileAccess(theURL)).show();
        $('#selectedTem').hide();
        
        var exif = $(this).data('exif');
        var $this = $(this);
        if (exif === undefined) {
            $.ajax({
                type: 'GET',
                url: 'scripts/imgReadExif',
                data: { img: theURL },
                dataType: 'html', 
                success: function(data) {
                    $this.data('exif', data);
                    $('#imagedesc').html(data).show();
                }
            });
        } else {
            $('#imagedesc').html(exif).show();
        }
        
        $(this).append(
            $imagebox.css('margin-left', $(this).width())
        );
    }
}).on('click', 'li.pca, li.fimg', function(e) {
    if ($finder.hasClass('imageView')) { return false; }
    
    $('#selectedTem').val("PCA files are not human-readable. " 
        + "This file format is what the desktop version of Psychomorph uses. " 
        + "To see a human-readable version of this file, look at the " 
        + $(this).text() + ".txt file.").show();
    
    $('#imagebox img, #imagedesc').hide();
    $(this).append(
        $imagebox.css('margin-left', $(this).width())
    );
}).on('click', 'li.txt, li.csv, li.pci', function(e) { 
    if ($finder.hasClass('imageView')) { return false; }
    
    var theURL = $(this).attr('url');
    $.ajax({
        url: 'scripts/fileRead',
        data: { url: theURL },
        success: function(data) {
            if (data.error) {
                $('<div />').html(data.errorText).dialog({
                    title: 'Error Reading File <code>' + theURL + '</code>',
                });
            } else {
                $('#selectedTem').val(data.text).show();
            }
        }
    });
    
    $('#imagebox img, #imagedesc').hide();
    $(this).append(
        $imagebox.css('margin-left', $(this).width())
    );
}).on('click', 'li.tem', function(e) {
    // show text of file in imgbox on click
    if ($finder.hasClass('imageView')) { return false; }

    var $this = $(this);
    var theURL = $this.attr('url');
    var theTem = $this.data('tem');
    
    if (theTem === undefined) {
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
                    $('#selectedTem').val(data.tem).show();
                    $this.data('tem', data.tem);
                }
            }
        });
    } else {
        $('#selectedTem').val(theTem).show();
    }
    $('#imagebox img, #imagedesc').hide();
    
    $(this).append(
        $imagebox.css('margin-left', $(this).width())
    );
}).on('click', 'li.folder > span', function(e) { 
    $theFolder = $(this).parent('li.folder').removeClass('closed')         // open this folder on click
    $theFolder.siblings('li.folder:not(.closed)').addClass('closed');     // close sibling folders 
    $finder.find('input').blur();                                         // blur any open inputs for folder name changes
    $theFolder.find('li.folder:not(.closed)').addClass('closed');         // close all folders below this level
    $finder.find('li.file.selected').removeClass('selected');             // unselect all files
    
    if ($finder.hasClass('imageView')) {
        $finder.find('ul').css({
            'width': '0'
        });
        $theFolder.find('> ul').css('width', $finder.width());
    }
    
    $finder.scrollLeft($finder.width());    // scroll all the way to the right
    updateSelectedFiles();
});

// ! ***** menu item functions *****
// !#imageview
$('#imageview').click( function() {
    if ($(this).hasClass('disabled')) { return false; }
    
    $finder.toggleClass('imageView');
    
    if ($finder.hasClass('imageView') ) {
        $(this).text('Column View');
    } else {
        $(this).text('Icon View');
    }
    $imagebox.hide();
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

// !#upload
$('#uploadFiles').click(function() {
    $('#upload').click();
});
$('#upload').change(function() { fileUpload(); });
// !#showProjects
$('#showProjects').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    
    var $spinner = bodySpinner();
    
    $('.interface:visible').not('#projectInterface').hide('fade', {}, 300, function() { 
        $('#menu_window .checkmark').hide();
        $(this).find('.checkmark').show();
        menubar('project');
        $('#projectInterface').show('fade', 300, function() {}); 
        projectList();
        $spinner.remove();
    });
});
// !#showFinder
$('#showFinder').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    
    $('#menu_window .checkmark').hide();
    $(this).find('.checkmark').show();

    $('.interface:visible').not('#finderInterface').hide('fade', {}, 300, function() { 
        menubar('finder');
        $finder.insertAfter($('#uploadbar'));
        $('#recent_creations').hide();
        $('#finderInterface').show('fade', 300, function() { 
            $('#refresh').click(); 
        }); 
        $finder.find('li.file').show().draggable('option', 'containment', '#finder');
    });
});
// !#showDelineate
$('#showDelineate').click(function() {
    if ($(this).hasClass('disabled')) { return false; }

    $('#menu_window .checkmark').hide();
    $(this).find('.checkmark').show();

    $('.interface:visible').not('#delineationInterface').hide('fade', {}, 300, function() {
        menubar('delineate');
        $('#recent_creations').hide();
        $('#delineationInterface').show('fade', 300, function() { });
    });
    
});
// !#showAverage
$('#showAverage').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    
    $('#menu_window .checkmark').hide();
    $(this).find('.checkmark').show();

    $('.interface:visible').not('#averageInterface').hide('fade', {}, 300, function() { 
        menubar('average');
        loadFiles(currentDir());
        var padwidth = $('#avg_image_box').outerWidth(true) + 20;
        $('#individual_image_box').insertAfter($('#avg_image_box')).css("padding-left", padwidth);
        $finder.appendTo($('#individual_image_box'));
        $finder.find('li.file').hide().filter('.image.hasTem').show().draggable('option', 'containment', 'window');
        $('#recent_creations').insertAfter($('#individual_image_box')).show();
        $('#averageInterface').show('fade', 300, function() { sizeToViewport(); }); 
        
        $('#view-average-button, #save-button').button({ disabled: true });
        checkAvgAbility();
    });
});
// !#showTransform
$('#showTransform').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    
    $('#menu_window .checkmark').hide();
    $(this).find('.checkmark').show();
    
    $('.interface:visible').not('#transformInterface').hide('fade', {}, 300, function() { 
        menubar('transform');
        loadFiles(currentDir()); 
        var padwidth = $('#destimages').outerWidth(true) + 20;
        $('#individual_image_box').insertAfter($('#grid')).css("padding-left", padwidth);
        $finder.appendTo($('#individual_image_box'));
        $finder.find('li.file').filter('.image.hasTem').show().draggable('option', 'containment', 'window');
        $('#recent_creations').insertAfter($('#individual_image_box')).show();
        $('#transformInterface').show('fade', 300, function() { sizeToViewport(); }); 
        
        $('#transButton, #trans-save-button').button({ disabled: true });
        checkTransAbility();
    });
});
// !#aboutPsychomoprh
$('#aboutPsychomorph').click(function() {
    $('#aboutDialog').dialog();
});
// !#prefs
$('#prefDialog').tabs();
$('#prefs').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    
    // make sure the prefs interface is up to date with the database
    //prefGet( function () {
        // then open the prefs dialog
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
    //});
});
$('.hue_chooser').slider({
    value: 200,
    min: 0,
    max: 361,
    step: 1,
    slide: function(event, ui) {
        var hue = ui.value;
        $(this).css('background-image', "none");
        var hsl = 'hsl(' + hue + ', 100%, 30%)';
        if (hue === 0) {
            hsl = 'hsl(200, 0%, 30%)';
        } else if (hue == 361) {
            hsl = 'hsl(200, 0%, 0%)';
            hue = 'B';
        }
        $(this).css('background-color', hsl);
        $(this).find('.ui-slider-handle').css('border-color', hsl).text(hue);
    },
    change: function(event, ui) {
        var hue = ui.value;
        $(this).css('background-image', "none");
        var hsl = 'hsl(' + hue + ', 100%, 30%)';
        if (hue === 0) {
            hsl = 'hsl(200, 0%, 30%)';
        } else if (hue == 361) {
            hsl = 'hsl(200, 0%, 0%)';
            hue = 'B';
        }
        $(this).css('background-color', hsl);
        $(this).find('.ui-slider-handle').css('border-color', hsl).text(hue);
    }
});

$('.rgb_chooser').each( function() {
    $(this).slider({
        values: [127, 127, 127],
        min: 0,
        max: 255,
        step: 1,
        slide: function(event, ui) {
            var r = ui.values[0];
            var g = ui.values[1];
            var b = ui.values[2];
            $(this).css('background-color', 'rgb(' + r + ', ' + g + ', ' + b + ')');
            var handles = $(this).find('.ui-slider-handle');
            handles.eq(0).text(r);
            handles.eq(1).text(g);
            handles.eq(2).text(b);
        },
        change: function(event, ui) {
            var r = ui.values[0];
            var g = ui.values[1];
            var b = ui.values[2];
            $(this).css('background-color', 'rgb(' + r + ', ' + g + ', ' + b + ')');
            var handles = $(this).find('.ui-slider-handle');
            handles.eq(0).text(r);
            handles.eq(1).text(g);
            handles.eq(2).text(b);
        }
    });
});

$('#batch_mask_color').slider({
    slide: function(event, ui) {
        var r = ui.values[0];
        var g = ui.values[1];
        var b = ui.values[2];
        $('#maskExample').css('background-color', 'rgb(' + r + ', ' + g + ', ' + b + ')');
        $(this).css('background-color', 'rgb(' + r + ', ' + g + ', ' + b + ')');
        var handles = $(this).find('.ui-slider-handle');
        handles.eq(0).text(r);
        handles.eq(1).text(g);
        handles.eq(2).text(b);
    },
    change: function(event, ui) {
        var r = ui.values[0];
        var g = ui.values[1];
        var b = ui.values[2];
        $('#maskExample').css('background-color', 'rgb(' + r + ', ' + g + ', ' + b + ')');
        $(this).css('background-color', 'rgb(' + r + ', ' + g + ', ' + b + ')');
        var handles = $(this).find('.ui-slider-handle');
        handles.eq(0).text(r);
        handles.eq(1).text(g);
        handles.eq(2).text(b);
    }
});

// !#fileListGet
$('#fileListGet').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    fileListGet();
});
// !#getInfo
$('#getInfo').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    
    $.ajax({
        type: 'GET',
        url: 'scripts/imgReadExif',
        data: { img: PM.faceimg },
        dataType: 'html', 
        success: function(data) {
            $('<div />').attr('title', PM.faceimg).html(data).dialog({
                width: 500,
                height: 400,
            });
        }
    });
});

// !#save
$('#save').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    if (PM.interface == 'delineate') {
        saveTem();
    } else if (PM.interface == 'average') {
        $('#save-button').click();
    } else if (PM.interface == 'transform') {
        $('#trans-save-button').click();
    }
});
// !saveAs
/*
$('#saveAs').click(function() {
    if ($(this).hasClass('disabled')) { return false; }    
    
    if (PM.interface == 'delineate') {
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
$('#convert').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    batchConvert();
});
// !#download
$('#download').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    var cDir = currentDir();
    var files = filesGetSelected();
    if (files.length > 0) {
        growl('<p>Downloading ' + files.length + ' file' + (files.length==1?'':'s') + '.</p>' +
        '<p>This may take a few seconds, depending on how many files need to be compressed.</p>');
        postIt('scripts/fileZip', {
            'files': files
        });
    } else if (cDir != '') {
        growl('<p>Downloading directory <code>' + cDir + '</code></p>' +
        '<p>This may take a few seconds, depending on how many files need to be compressed.</p>');
        postIt('scripts/fileZip', {
            'directory': cDir
        });
    } else {
        growl('You have not selected any files.', 1500);
    }
});
// !#toggletem
$('#toggletem').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    
    if (PM.showTem) {
        // hide delineation
        $(this).find('span.checkmark').hide();
        $('#template, .pt').hide();
        PM.showTem = false;
    } else {
        // show delineation
        $(this).find('span.checkmark').show();
        PM.showTem = true;
        $('#template, .pt').show();
        drawTem();
    }
});
$('#emptyTrash').click( function() { emptyTrash(); });
// !#toggletrash
$('#toggletrash').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    
    if ($('#trash:visible').length) {
        $(this).find('span.checkmark').hide();
        $('#trash').hide();
    } else {
        $(this).find('span.checkmark').show();
        $('#trash').show();
    }
});
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
        var w = Math.round(PM.originalWidth * h / PM.originalHeight);
        $('#size_value').html(w + 'x' + h);
    },
    change: function(event, ui) {
        var h = ui.value;
        var w = Math.round(PM.originalWidth * h / PM.originalHeight);
        PM.temRatio = h / PM.originalHeight;
        
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
        
        if (PM.delinfunc == 'fit') {
            PM.dcontext.clearRect (0, 0, $('#template').width(), $('#template').height());
            cursor('crosshair');
        } else {
            drawTem();
        }
    }
});
// !#fitsize
$('#fitsize').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    var availableWidth = $('#delin_toolbar').width();
    var availableHeight = $(window).height() - $delin.offset().top - $('#footer').height() - 20;
    
    if (availableWidth*PM.originalHeight/PM.originalWidth < availableHeight) {
        // fit to available width
        var resize = availableWidth*PM.originalHeight/PM.originalWidth;
    } else {
        // fit to available height
        var resize = availableHeight;
    }
    
    $('#imgsize').slider('value', resize);
});
// !#zoomin
$("#zoomin").click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    if (PM.interface == 'delineate') {
        var resize = $('#imgsize').slider('value') + 100;
        if (resize > $('#imgsize').slider('option', 'max')) {
            resize = $('#imgsize').slider('option', 'max');
        }
        $('#imgsize').slider('value', resize);
    } else if (PM.interface == 'transform') {
        var w = $('#destimages').width() + 100;
        $('#destimages').css('width', w + 'px');
        $('#transform').width($('#transimage').width()).height($('#transimage').height());
        $('#individual_image_box').css("padding-left", $('#destimages').outerWidth(true) + 20);
    }
});
// !#zoomout
$("#zoomout").click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    if (PM.interface == 'delineate') {
        var resize = $('#imgsize').slider('value') - 100;
        if (resize < $('#imgsize').slider('option', 'min')) {
            resize = $('#imgsize').slider('option', 'min');
        }
        $('#imgsize').slider('value', resize);
    } else if (PM.interface == 'transform') {
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
$('#zoomoriginal').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    if (PM.interface == 'delineate') {
        var resize = PM.originalHeight;
        
        // adjust slider min and max if out of range
        if (resize < $('#imgsize').slider('option', 'min')) {
            $('#imgsize').slider('option', 'min', resize);
        } else if (resize > $('#imgsize').slider('option', 'max')) {
            $('#imgsize').slider('option', 'max', resize);
        }
        $('#imgsize').slider('value', resize);
    } else if (PM.interface == 'transform') {
        $('#destimages').css('width', '310px');
        sizeToViewport();
    } else if (PM.interface == 'average') {
        $('#average').css('width', '310px');
        sizeToViewport();
    }
});
// !#cutItems
$('#cutItems').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    PM.pasteBoard = filesGetSelected();
    $finder.find('li.file').removeClass('to_cut'); // clear all other to_cut files
    $finder.find('li.file.selected:visible').each(function(i, v) {
        $(this).addClass('to_cut');
    });
    $('#footer').html(PM.pasteBoard.length + ' files cut');
});
// !#copyItems
$('#copyItems').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    
    if (PM.interface == 'delineate') {
        PM.pasteBoard = [];
        $.each(PM.selected_pts, function(i, v) {
            if (v) {
                PM.pasteBoard.push({
                    n: i,
                    x: PM.current_tem[i].x,
                    y: PM.current_tem[i].y
                });
            }
        });
        
        $('#footer').html(PM.pasteBoard.length + ' points copied');
    } else {
        PM.pasteBoard = filesGetSelected();
        $finder.find('li.file').removeClass('to_cut'); // clear all to_cut files
        $('#footer').html(PM.pasteBoard.length + ' files copied');
    }
});
// !#pasteItems
$('#pasteItems').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    
    if (PM.interface == 'delineate') {
        if (PM.pasteBoard.length) {
            $.each(PM.pasteBoard, function(i, v) {
                PM.current_tem[v.n].x = v.x;
                PM.current_tem[v.n].y = v.y;
            });
            
            updateUndoList();
            drawTem();
        } else {
            $('#footer').html('No points were copied');
        }
    } else {
        nImages = PM.pasteBoard.length;
        if (nImages) {
            var toDir = currentDir();
            var $fileList = $('<ul />').css('max-height', '200px');
            var cutlist = 0;
            $.each(PM.pasteBoard, function(i, v) {
                $fileList.append('<li>' + urlToName(v) + '</li>');
                if ($('li.to_cut[url="' + v + '"]').length) cutlist++;
            });
            var action = (nImages == cutlist) ? 'move' : 'copy';
            $.ajax({
                url: 'scripts/fileCopy',
                data: {
                    toDir: toDir,
                    files: PM.pasteBoard,
                    action: action
                },
                success: function(data) {
                    if (data.error) {
                        $('<div />').html(data.errorText).dialog({
                            title: 'Error Pasting Files',
                        });
                        loadFiles(toDir);
                    } else {
                        $finder.find('li.folder').addClass('closed');
                        $finder.find('li.file').removeClass('selected');
                        if (action == 'move') {
                            PM.pasteBoard = [];
                        } // clear PM.pasteBoard if moved, not if copied
                        loadFiles(toDir);
                        $('#footer').html(nImages + ' files pasted to <code>' + toDir + '</code>');
                    }
                }
            });
        }
    }
});

$('#moveFolderToProject').click( function() {
	if ($(this).hasClass('disabled')) { return false; }
	
	folderMoveProject();
});
// !#find
$('#find').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    
    $('#searchbar').toggle().val('').focus();
    sizeToViewport();
    
    if ($('#searchbar:visible').length === 0) {
        $finder.find('li.file').show();
    }    

});
// !#searchbar
$('#searchbar').keyup(function() {
    var searchtext = $(this).val();
    var $allFiles = $finder.find('li.file');
    $allFiles.show();
    if (searchtext != '') {
        $allFiles.not(':contains("' + searchtext + '")').hide();
    }
    updateSelectedFiles();
});
// !#deleteItems
$('#deleteItems').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    
    if ($finder.filter(':visible').length) {
        fileDelete();
    } else if (PM.interface == 'delineate') {
        var ptArray = getSelPts();
        if (ptArray.length) {
            removeTemPoints(ptArray);
        }
    }
});
// !#debug
$('#debug').click(function() {
	window.open("/debug/");
});

// !#newProject
$('#newProject').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    projectNew();
});
// !#newFolder
$('#newFolder').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    folderNew();
});
// !#dbCleanup
$('#dbCleanup').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    $.ajax({
        url: 'scripts/dbCleanup?project=' + PM.project,
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
    
    if (PM.interface == 'delineate') {
        delinImage(PM.faceimg);
        PM.delinfunc = 'move';
        $('#pointer, #leftEye, #rightEye, #mouth').hide();
    } else if (PM.interface == 'average') {
        $('#clear-average-button').click();
        loadFiles(currentDir());
    } else if (PM.interface == 'transform') {
        $('#fromimage, #toimage, #transimage').attr('src', PM.blankImg);

        $("#transform").attr({
            'src': PM.blankImg,
            'tem': '',
            'transimage': '',
            'fromimage': '',
            'toimage': '',
            'shape': 50,
            'color': 50,
            'texture': 50
        });
        
    } else if (PM.interface == 'project') {
        projectList();
    }
    cursor('auto');
    sizeToViewport();
});

$('#autoDelineate').click(function() {
    skyBio(PM.faceimg);
});

// !#maskBuilder
$('#maskBuilder').click(function() {
    $mbb = $('#mask_builder_box');
    if (PM.delinfunc == 'mask') {
        PM.delinfunc = 'move';
        $mbb.hide();
    } else {
        PM.delinfunc = 'mask';
        $mbb.show();
    }
});

// !#fitTemplate
$('#fitTemplate').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    
    clickPt(0);
    PM.delinfunc = 'fit';
    $('.pt').remove();

    if ($finder.filter(':visible').length) {
        // fit all selected images
        files = [];
        $('li.file.image.selected').each(function(i, v) {
            files[i] = $(this).attr('url');
        });
        if (files.length > 0) {
            $('#footer').html('0 of ' + files.length + ' image' + ((files.length == 1) ? '' : 's') + ' fitted to template <code>' + $('#current_tem_name').text() + '</code>');
            PM.selectedFile = 0;
            var url = files[0];
            var name = urlToName(url);
            delinImage(name, false);
            PM.eye_clicks = [];
            $('#template').hide();
            PM.delinfunc = '3pt';
            cursor('crosshair');
        } else {
            growl('You need to select files to fit templates.', 1500);
            PM.delinfunc = 'move';
            quickhelp();
        }
    } else if (PM.interface == 'delineate') {
        $('#footer').html('Fitting template <code>' + $('#current_tem_name').text() + '</code>');
        PM.eye_clicks = [];
        $('#template').hide();
        PM.delinfunc = '3pt';
        cursor('crosshair');
    }
});

// !#newLine
$('#newLine').click(function() {
    if (PM.delinfunc != 'lineadd') {
        PM.delinfunc = 'lineadd';
        quickhelp('New line: press &lsquo;enter&rsquo; to end');
        cursor('lineadd');
        PM.current_lines.push([]);
        var line = PM.current_lines.length - 1;
        PM.line_colors[line] = 'red';
        $('#footer').html('Starting new line');
    }
    updateUndoList();
    drawTem(); // adds or deletes new line object
});
// !#deleteLine
$('#deleteLine').click(function() {
    if (PM.delinfunc != 'linesub') {
        PM.delinfunc = 'linesub';
        quickhelp('Click a line to delete it');
        cursor('linesub');
    }
    
    PM.lineWidth = 5;
    drawTem();
});
// !#closeMouth
$('#closeMouth').click(function() {
    if (!($(this).hasClass('disabled'))) {
        for (var i = 94; i < 99; i++) {
            var fromPt = i + 5;
            var toPt = i;
            PM.current_tem[toPt].x = PM.current_tem[fromPt].x;
            PM.current_tem[toPt].y = PM.current_tem[fromPt].y;
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
$('#resize').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    batchResize();
});
$('#resizeDialog input').blur( function() {
    if ($(this).hasClass('disabled')) { return false; }
    calcNewSizes(this.name);
});
// !#rotate
$('#rotate').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    batchRotate();
});
// !#alignEyes
$('#alignEyes').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    batchAlign();
});
$('.batch_name span').click(function() {
    var $this = $(this);
    var w = $this.width();
    var current_val = $this.html();
    if (current_val.substr(0, 4) == '[no ') current_val = '';
    var $input = $('<input type="text" />').val(current_val).width(w + 10).keydown(function(e) {
        if (e.which == KEYCODE.enter) $(this).blur();
    }).blur(function() {
        var val = $(this).val();
        if ($this.hasClass('batch_subfolder')) {
            if (val.length === 0) {
                val = '[no subfolder]';
            } else {
                if (val.substr(0, 1) != '/') val = '/' + val;
                if (val.substr(-1) == '/') val = val.substr(0, val.length - 1);
            }
        } else if ($this.hasClass('batch_prefix')) {
            if (val.length === 0) val = '[no prefix]';
        } else if ($this.hasClass('batch_suffix')) {
            if (val.length === 0) val = '[no suffix]';
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
$('#crop').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
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
// !#tem2ModSkyBio
$('#convert_template_menu li').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    var from_tem = parseInt($(this).data('from'));
    var to_tem = parseInt($(this).data('to'));
    
    
    if (from_tem > 0 && to_tem > 0) {
   		batchTemConvert(from_tem, to_tem);
   	}
});



// !#mask
$('#mask').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    batchMask();
});
// !#mirror
$('#mirror').click( function() {
    if ($(this).hasClass('disabled')) { return false; }
    batchMirror();
});
// !#symmetrise
$('#symmetrise').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    batchSymmetrise();
});
// !movie functions 
$('#transMovieSteps').change( function () {
    var value = parseInt($(this).val());
    $('#transMovieStepsDisplay').html("(" + (value+1) + " images in " + (Math.round(1000/value)/10) + "% steps)");
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
        data: { img: urlToName($('#movieBox').attr('src')) },
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
$('#movingGif').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    
    movingGif();
});

// !#batchAverage
$('#batchAverage').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    
    batchAverage();
});

// !batchTransform
$('#batchTransform').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    
    batchTransform();
});

$('#SBTdialog').delegate('textarea', 'keydown', function(e) {
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

//!gridFaces
$('#gridFaces').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    $('#showTransform').click();
    $('#destimages').hide();
    $('#grid').show();
});
//$('#grid-options').buttonset();
$('#createGrid').button().click( function() {
    createGrid();
});
// !batchTag
$('#batchTag').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    // put all img files in a list
    var regex = new RegExp('(^/images|\.jpg$)', 'g');
    var files = filesGetSelected('.jpg', regex);
    if (files.length > 0) {
        $('<div />').html('Tag(s) (separate tags with semi-colons):' + '<input type="text" />').dialog({
            title: 'Add Tags', 
            buttons: {
                Cancel: function() { $(this).dialog("close"); },
                'Delete': {
                    text: 'Delete Tags',
                    click: function() {
                        var theTags = $(this).find('input').val();
                        $(this).dialog("close");
                        $.ajax({
                            url: 'scripts/fileUntag',
                            data: {
                                files: files,
                                tags: theTags
                            },
                            success: function(data) {
                                if (data.error) {
                                    $('<div title="Error" />').html(data.errorText).dialog();
                                } else {
                                    growl('Tags deleted from ' + files.length + ' images.', 2000);
                                }
                            }
                        });
                    }
                },
                'Add': {
                    text: 'Add Tags',
                    class: 'ui-state-focus',
                    click: function() {
                        var theTags = $(this).find('input').val();
                        $(this).dialog("close");
                        $.ajax({
                            url: 'scripts/fileTag',
                            data: {
                                files: files,
                                tags: theTags
                            },
                            success: function(data) {
                                if (data.error) {
                                    $('<div title="Error" />').html(data.errorText).dialog();
                                } else {
                                    growl('Tags added to ' + files.length + ' images.', 2000);
                                }
                            }
                        });
                    }
                }
            }
        });
    }
});
//!batchModDelin
$('#batchModDelin').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    batchModDelin();
});
//!facialMetrics
$('#fmButtons').on('click', 'li', function() {
    $('#fm_name').val($(this).text());
    $('#fm_equation').val($(this).attr('data-equation'));
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
$('#fm_results').on('dblclick', 'thead th+th', function() {
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
$('#facialMetrics').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    
    batchFacialmetrics();
});
// !undo
$('#undo').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    
    if (PM.interface == 'delineate') {
        if (PM.delinfunc == 'move') {
            PM.undo_level = Math.max(0, PM.undo_level - 1);
            PM.current_tem = $.extend(true, [], PM.undo_tem[PM.undo_level]);
            PM.current_lines = $.extend(true, [], PM.undo_lines[PM.undo_level]);
            if (PM.undo_level == 0) $('#delin_save').removeClass('unsaved');
            drawTem();
        } else if (PM.delinfunc == 'sym' && PM.symPts.n > 0) {
            PM.symPts.n = PM.symPts.order.pop();
            nextSymPt('start');
        } else if (PM.delinfunc == '3pt') {
            // remove last item in eye_clicks
            var n = PM.eye_clicks.length;
            if (n == 1) {
                $('#leftEye').hide();
                PM.eye_clicks.pop();
                clickPt(0);
            } else if (n == 2 ) {
                $('#rightEye').hide();
                PM.eye_clicks.pop();
                clickPt(1);
            }
        }
    }
});
// !redo
$('#redo').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    if (PM.interface == 'delineate') {
        if (PM.delinfunc == 'move') {
            PM.undo_level = Math.min(PM.undo_level + 1, PM.undo_tem.length - 1);
            PM.current_tem = $.extend(true, [], PM.undo_tem[PM.undo_level]);
            PM.current_lines = $.extend(true, [], PM.undo_lines[PM.undo_level]);
            if (PM.undo_level > 0) $('#delin_save').addClass('unsaved');
            drawTem();
        }
    }
});
// !#select
$('#select').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    if ($finder.filter(':visible').length) {
        // (un)select all files in the open folder
        var $allfiles = $finder.find('li.folder')
                               .filter(':not(.closed)')
                               .filter(':last')
                               .find('> ul > li.file')
                               .filter(':visible:not(.nosearch)');
        if ($allfiles.length == $allfiles.filter('.selected').length) {
            // all files are already selected, so unselect instead
            $finder.find('li.file').removeClass('selected');
        } else {
            $allfiles.addClass('selected');
            $imagebox.hide().appendTo($allfiles.eq(0));
        }
        updateSelectedFiles();
    } else if (PM.interface == 'delineate') {
        if ($('.pt').length == $('.pt.selected').length) {
            // unselect all delineation points
            $('.pt').removeClass('selected');
        } else {
            // select all delineation points
            $('.pt').addClass('selected');
        }
    }
});
// !createTransform
$('#createTransform').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    if (PM.interface == 'average') {
        getAverage();
    } else if (PM.interface == 'transform') {
        getTransform({async:true});
    } else if (PM.interface == 'finder') {
        var regex = new RegExp("(.tem$)", "g");
        PM.selected_images = filesGetSelected('.tem', regex);
        
        if (PM.selected_images.length > 1) {
            $('#showAverage').click();
        } else {
            growl("Select more than 1 image to average", 1000);
        }
    }
});
// !currentTem
$('#currentTem').on('click', 'li', function() {
    setCurrentTem($(this).attr('data-id'));
});
// !currentProject
$('#currentProject').on('click', 'li', function() {
    projectSet($(this).attr('data-id'));
});
// !isNewTem
$('#isNewTem').click( function() {
    var $button = $('#addTemDialog').parent().find('.default_button .ui-button-text');
    if ($(this).prop('checked')) {
        $button.text('Add');
    } else {
        $button.text('Edit');
    }
});
// !editTemplate
$('#editTemplate').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    
    editTemplate();
});
$('#setPointLabels').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    
    if (PM.default_tem.length != PM.current_tem.length) {
        growl('The current template does not match the template <code>' + $('#current_tem_name').text() + '</code>');
    } else {
        // check if the current user has access to edit this template
        $.ajax({
            url: '/scripts/userCheckAccess',
            data: { table: 'tem', id: PM.default_tem_id },
            success: function(data) {
                if (data.error) {
                    growl(data.errorText);
                } else {
                    setPointLabels();
                }
            }
        });
    }
});
$('#labelDialog ol').on('focus', 'input', function() {
    // point to and highlight the corresponding point when the label is in focus
    var n = parseInt($(this).attr('name'));
    
    // unselect all points first
    $('.pt').removeClass('selected');
    var pt = PM.pts[n];
    pt.addClass('selected');

    var imgoffset = $delin.offset();
    $('#pointer').css({
        left: pt.position.left + imgoffset.left - 7 - $('#pointer').width(),
        top: pt.position.top + imgoffset.top + 1 - $('#pointer').height()/2
    }).show();

});

// !setSymPoints
$('#setSymPoints').click(function() {
    if ($(this).hasClass('disabled')) { return false; }
    setSymPoints();
});
// !whatsnew
$('#whatsnew').click( function() { 
    $('#whatsnewDialog').dialog({ height: 500 });
});
// !menuhelp
$('#menuhelp').click(function() {
    $('#help').dialog({ height: 500 });
});
// !tinyhelp
$('.tinyhelp').click(function() {
    var topic = $(this).data('topic');
    var scrollTo = $('#help *[data-topic="' + topic + '"]');
    $('#help').dialog({ height: 500 }).scrollTop(
        scrollTo.offset().top -  $('#help').offset().top + $('#help').scrollTop()
    );
});
// !emailLisa
$('#emailLisa').click(function() {
    PM.no_onbeforeunload = true;
    location.assign("mailto:lisa.debruine@glasgow.ac.uk?subject=Online PsychoMorph");
    PM.no_onbeforeunload = false;
});
// !show_thumbs
$('#show_thumbs').change(function() {
    if ($(this).prop('checked')) {
        $finder.find('li.image').each (function(i) {
            $(this).css('background-image', 'url(/scripts/fileAccess?thumb&file=' + $(this).attr('url') + ')');
        });
    } else {
        $finder.find('li.image').css('background-image', 'url(/include/images/finder/imgicon.svg)');
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
// ! mask_trans
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
// !#transButton
$('#transButton').button().click(function() {
    var list = filesGetSelected('.image.hasTem', PM.project);
    
    if (list.length > 1) {
        var fromimage = urlToName($("#fromimage").attr('src'));
        var toimage = urlToName($("#toimage").attr('src'));
        var shape = parseFloat($("#shapePcnt0").val());
        var color = parseFloat($("#colorPcnt0").val());
        var texture = parseFloat($("#texturePcnt0").val());
    
        var batchText = '';
        $.each(list, function(i, img) {
            var newimg = '/trans' + img;
            batchText += img + "\t" + fromimage + "\t" + toimage + "\t" + shape + "\t" + color + "\t" + texture + "\t" + newimg + "\n";
        });
    
        $('#batchTransform').click();
        $('#SBTdialog textarea').val(batchText.trim()).show();
    } else {
        getTransform({async: true});
    }
});
// !#continuum
$('#continuum').click(function() {
    if ($(".movie_settings:visible").length === 0) {
        $("#trans_settings").hide()
        $(".movie_settings").show()
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

// !***** delineation interface functions *****

$delin.click(function(e) {
    if (e.shiftKey && (e.metaKey || e.altKey)) {
        newDelinPoint(e);
    } else if (PM.eye_clicks.length < 3) {
        threePtDelin(e);
    }
}).dblclick(function() {
    if (PM.delinfunc == 'move') {
        $('.pt.selected').removeClass('selected'); // unselect all delineation points
        $('#selectBox').hide(); // and reset the selectBox
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
    if ($('#selectBox').prop('x') !== false && e.shiftKey && PM.pageEvents.mousebutton[1]) {
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
    if ($('#selectBox').prop('x') !== false && e.shiftKey && PM.pageEvents.mousebutton[1]) {
        boxHover(e);
    } else {
        resetSelectBox();
    }
});

$delin.on("click", ".pt", function(e) {
    if (e.shiftKey) {
        $(this).toggleClass("selected");
    } else if (e.metaKey || e.ctrlKey) {
        var connectedPoints = $(this).data('connectedPoints');
        if ($(this).hasClass("selected")) {
            $.each(connectedPoints, function(j, pt) {
                PM.pts[pt].removeClass('selected');
            });    
        } else {
            $.each(connectedPoints, function(j, pt) {
                PM.pts[pt].addClass('selected');
            });    
        }
    }
    drawTem();
});

$delin.on("mouseenter", ".pt", function(e) {
    if (PM.delinfunc == 'lineadd') {
        cursor('lineadd');
    } else if (PM.delinfunc == 'label') { 
        return false;
    } else {
        //cursor('pointer');
    }
    
    var i = parseInt($(this).attr('n'));
    
    var pointName = (PM.default_tem[i] !== undefined) ? PM.default_tem[i].name : 'undefined';
    
    var thisx = round(PM.current_tem[i].x, 1);
    var thisy = round(PM.current_tem[i].y, 1);
    
    var footertext = '[' + i + '] ' + pointName + 
                     ' x=<span class="x">' + thisx + '</span>; ' + 
                     'y=<span class="y">' + thisy + '</span>';
    $('#footer').prop('data-persistent', $('#footer').html()).html(footertext);
    
    if (e.metaKey || e.ctrlKey) {
        var conPts = $(this).data('connectedPoints');
        $.each(conPts, function(i,pt) {
            PM.pts[pt].addClass('couldselect');
        });
    }
    drawTem();
});

$delin.on("mouseup mouseout", ".pt", function(e) {
    if (PM.delinfunc == 'label') { return false; }
    
    if (e.shiftKey && (e.metaKey || e.ctrlKey)) {
        cursor('crosshair');
    } else if (PM.delinfunc == 'lineadd') {
        cursor('lineadd');
    } else {
        //cursor('auto');
    }
    
    $('.pt').removeClass('couldselect');
    
    updateUndoList();
    // remove point name and replace with whatever is in data-persistent
    $('#footer').html($('#footer').prop('data-persistent')); 
    drawTem();
});

$delin.on("mousedown", ".pt", function(e) {
    if (PM.delinfunc == 'label') { 
        return false;
    } else if (PM.delinfunc == 'lineadd') {
        var line = PM.current_lines.length - 1;
        var i = parseInt($(this).attr('n'));
        // check if last point if the same as this one
        var lastPt = PM.current_lines[line][PM.current_lines[line].length - 1];
        if (lastPt === undefined || lastPt != i) {
            PM.current_lines[line].push(i);
            var t = 'Added a point to the new line [' + PM.current_lines[line].join() + ']';
            $('#footer').html(t).prop('data-persistent', t);
        }
    } else if (PM.delinfunc == 'sym') {
        nextSymPt($(this).attr('n'));
    } else if (PM.delinfunc == "mask") {
        if (e.metaKey || e.ctrlKey) {
            var conLines = $(this).data('connectedLines');
            
            $.each(conLines, function(idx, line) {
                addToCustomMask(';');
                $.each(PM.current_lines[line], function(idx2, pt) {
                    addToCustomMask(pt);
                });
            });
        } else {
            addToCustomMask($(this).attr('n'));
        }
    }
    return false;
});

$('#pointer').draggable();
// !#delin_fitsize
$("#delin_fitsize").button({
    text: false,
    icons: {
        primary: "ui-icon-arrow-4-diag"
    }
}).click(function() {
    $('#fitsize').click();
});
// !#delin_zoomin
$("#delin_zoomin").button({
    text: false,
    icons: {
        primary: "ui-icon-zoomin"
    }
}).click(function() {
    $('#zoomin').click();
});
// !#delin_zoomout
$("#delin_zoomout").button({
    text: false,
    icons: {
        primary: "ui-icon-zoomout"
    }
}).click(function() {
    $('#zoomout').click();
});
// !#delin_zoomoriginal
$("#delin_zoomoriginal").button({
    text: false,
    icons: {
        primary: "ui-icon-document-b"
    }
}).click(function() {
    $('#zoomoriginal').click();
});
// !#delin_save
$("#delin_save").button({
    text: false,
    icons: {
        primary: "ui-icon-disk"
    }
}).click(function() {
    $('#save').click();
});
$("#delin_refresh").button({
    text: false,
    icons: {
        primary: "ui-icon-refresh"
    }
}).click(function() {
    $('#refresh').click();
});
// !#delin_next
$("#delin_next").button({
    text: false,
    icons: {
        primary: "ui-icon-seek-next"
    }
}).click(function() {
    nextImg();
});
$("#delin_prev").button({
    text: false,
    icons: {
        primary: "ui-icon-seek-prev"
    }
}).click(function() {
    prevImg();
});
$("#showDelinHelp").button({
    text: false,
    icons: {
        primary: "ui-icon-help"
    }
}).click(function() {
    $('#delinHelp').dialog({
        position: { my: 'right top', at: 'right bottom', of: $('#delin_toolbar') },
        width: 450
    });
});
$('.buttonset').buttonset();

// ! queue setup
$queue_n.hide();
PM.queue = new queue();
PM.queue.queueCountUpdate();

$('#clearQueue').click( function() { PM.queue.clear(); });

$('#clearComplete').click( function() { PM.queue.clear('complete');    });

$('#pauseQueue').click( function() { PM.queue.pauseAll(); });

$('#restartQueue').click( function() { PM.queue.restartAll(); });

$queue.on('click', 'li.queueItem:not(.active)', function(e) {
    if (e.which == KEYCODE.ctrl || e.which == KEYCODE.cmd) {
        $(this).data('obj').destroy();
    }
}).on('click', 'li.queueItem.complete:not(.ui-state-error)', function() {
    fileShow($(this).data('obj').returnData.newfilename);
}).on('click', 'li.queueItem.paused', function(){
    $(this).data('obj').wait();
}).on('click', 'li.queueItem.waiting', function(){        
    $(this).data('obj').pause();
}).on('click', 'li.queueItem.active', function(){        
    growl('You cannot pause or delete an active process', 1000);
}).on('click', 'li.queueItem.ui-state-error', function() {
    growl($(this).data('obj').errorText);
});

$('#masktest').click( function() {
    //var m = drawMask("145,146,147,148,149,150,151,152,153,154,155,156,157;157,184,183,145"); // halo
    var m = drawMask("111,186,110,185,109,134,135,136,137,138,139,140,141,142,143,144,112,187,113,188,114;114,133,132,131,130,129,128,127,126,125,111 : 112,120,121,122,123,124,114 ; 114,188,113,187,112 : 111,186,110,185,109 ; 109,115,116,117,118,119,111", 5); // face ears
    
    var $maskDialog = $('<div title="Masked Image" />').append(m);
    m.css({'width': '100%', 'max-width': PM.originalWidth + 'px', 'height': 'auto'});
    $maskDialog.dialog({
        position: { my: 'right top', at: 'right bottom', of: $('#delin_toolbar') },
        width: 450
    });
});

sizeToViewport();

// !***** Done Loading *****

$("body").removeClass("loading");

if (PM.userid) {
    var $spinner = bodySpinner();
    $('#projectInterface').show();
    prefGet();
    projectList();
    menubar('project');
    $('#showProjects .checkmark').show();
    $spinner.remove();
} else {
    menubar('login');
    $('#loginInterface').show();
    $('#login_email').focus();
}


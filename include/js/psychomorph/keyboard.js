// ! ***** Document mouse & key events *****
$(document).mousedown(function(e) {                                             // log current mousedown
    WM.pageEvents.mousebutton[e.which] = true;
}).mouseup(function(e) {
    WM.pageEvents.mousebutton[e.which] = false;
}).keydown(function(e) {                                                        // log current keydown
    WM.pageEvents.key[e.which] = true;
}).keyup(function(e) {
    WM.pageEvents.key[e.which] = false;
}).ajaxError(function() {
    //growl("Sorry, there was an error with this function.", 1000);
}).delegate('.ui-dialog', 'keyup', function(e) {
    // pressing the enter key selects default button on dialogs
    if (e.which === $.ui.keyCode.ENTER) {
        var tagName;
    
        tagName = e.target.tagName.toLowerCase();
        tagName = (tagName === 'input' && e.target.type === 'button') ? 'button' : tagName;
    
        if (   tagName !== 'textarea'
            && tagName !== 'select'
            && tagName !== 'button') {
            console.log("Pressed ENTER on a " + tagName);
            $(this).find('.ui-dialog-buttonset button.ui-state-focus')
                   .eq(0).trigger('click');
            return false;
        }
    }
}).bind('drop dragover', function(e) {                                          // necessary for drag and drop upload
    e.preventDefault();
}).keyup(function(e) {                                                          // functions on keyup
    // (cancel some delineation visuals)
    // e.which: a=>65 ... z=>90, 0=>48 ... 9=>57
    // lookup at http://api.jquery.com/event.which/

    if (WM.appWindow == 'delineate') {
        if (!((e.ctrlKey || e.metaKey) && e.shiftKey) && WM.delinfunc == 'move') {
            cursor('auto');
            quickhelp();
        }
        if (e.which == KEYCODE.ctrl || e.which == KEYCODE.cmd) {
            // cursor is hovering over a delin point
            $('.pt').removeClass('couldselect');
            drawTem();
        }
    }
}).keydown(function(e) {
    var navKeys,  // list of keycodes for navigation (except when in input boxes)
        funcKeys; // list of keycodes for text functions (except when in input boxes)

    navKeys = [
        KEYCODE.left_arrow,
        KEYCODE.right_arrow,
        KEYCODE.down_arrow,
        KEYCODE.up_arrow,
        KEYCODE.delete,
        KEYCODE.backspace
    ];

    funcKeys = [
        KEYCODE.x,
        KEYCODE.c,
        KEYCODE.v,
        KEYCODE.a
    ];

    if (    (    $('.ui-dialog:visible').length
                 || (WM.appWindow == 'login')
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
    } else if (e.altKey) {                                                      // ! alt-Key shortcuts
        if (e.which == KEYCODE.a) {
            $('#createTransform').click();                                      // !alt-A
        } else if (e.which == KEYCODE.c) {
            $('#closeMouth').click();                                           // !alt-C
        } else if (e.which == KEYCODE.d) {
            $('#autoDelineate').click();                                        // !alt-D
        } else if (e.which == KEYCODE.f) {
            $('#fitTemplate').click();                                          // !alt-F
        } else if (e.shiftKey && e.which == KEYCODE.l) {                        // !shift-alt-L
            $('#deleteLine').click();
        } else if (e.which == KEYCODE.l) {                                      // !alt-L
            $('#newLine').click();
        } else if (WM.appWindow == 'delineate'
                &&   (e.which == KEYCODE.plus
                   || e.which == KEYCODE.add
                   || e.which == KEYCODE.equal_sign)) {
            temSizeChange(1);                                                   // !alt-plus
        } else if (WM.appWindow == 'delineate'
                &&    (e.which == KEYCODE.minus
                    || e.which == KEYCODE.subtract
                    || e.which == KEYCODE.dash)) {
            temSizeChange(-1);                                                  // !alt-minus
        } else if (e.which ==  KEYCODE.right_arrow) {                           // !alt-right arrow
            if (WM.appWindow == 'delineate') {
                temRotate(0.01745);
            }
        } else if (e.which ==  KEYCODE.left_arrow) {                            // !alt-left arrow
            if (WM.appWindow == 'delineate') {
                temRotate(-0.01745);
            }
        } else {
            return true;
        }
    } else if (e.ctrlKey || e.metaKey)  {                                       // ! cmd-key shortcuts
        if (e.which == KEYCODE.backspace || e.which == KEYCODE.delete) {        // !cmd-delete or cmd-backspace
            if (e.shiftKey && $finder.filter(':visible').length) {
                // delete with no confirm
                fileDelete(false);
            } else {
                $('#deleteItems').click();
            }
        } else if (e.which == KEYCODE.right_arrow) {                            // !cmd-right
            if ($d3.filter(':visible').length && WM.d3) { 
                WM.d3.move('right');
            } else if (WM.appWindow == 'delineate') {
                nextImg();
            }
        } else if (e.which == KEYCODE.left_arrow) {                             // !cmd-left
            if ($d3.filter(':visible').length && WM.d3) { 
                WM.d3.move('left');
            } else if (WM.appWindow == 'delineate') {
                prevImg();
            }
        } else if (e.which == KEYCODE.up_arrow) {                               // !cmd-up
            if ($d3.filter(':visible').length && WM.d3) { 
                WM.d3.move('up');
            }
        } else if (e.which == KEYCODE.down_arrow) {                             // !cmd-down
            if ($d3.filter(':visible').length && WM.d3) { 
                WM.d3.move('down');
            }
        } else if (e.which == KEYCODE['0'] || e.which == KEYCODE['0n']) {       // !cmd-0
            $('#zoomoriginal').click();
        } else if (e.which == KEYCODE['1'] || e.which == KEYCODE['1n']) {       // !cmd-1
            $('#showFinder').click();
        } else if (e.which == KEYCODE['2'] || e.which == KEYCODE['2n']) {       // !cmd-2
            $('#showDelineate').click();
        } else if (e.which == KEYCODE['3'] || e.which == KEYCODE['3n']) {       // !cmd-3
            $('#showAverage').click();
        } else if (e.which == KEYCODE['4'] || e.which == KEYCODE['4n']) {       // !cmd-4
            $('#showTransform').click();
        } else if (e.which == KEYCODE['5'] || e.which == KEYCODE['5n']) {       // !cmd-5
            $('#showProjects').click();
        } else if (e.which == KEYCODE['6'] || e.which == KEYCODE['6n']) {       // !cmd-6
            $('#showThreeD').click();
        } else if (e.shiftKey && e.which == KEYCODE.a) {                        // !shift-cmd-A
            $('#batchAverage').click(); 
        } else if (e.which == KEYCODE.a) {                                      // !cmd-a
            $('#select').click();
        } else if (e.which == KEYCODE.c) {                                      // !cmd-c
            $('#copyItems').click();
        } else if (e.which == KEYCODE.d) {                                      // !cmd-d
            $('#download').click();
        } else if (e.shiftKey && e.which == KEYCODE.e) {                        // !shift-cmd-E
            $('#batchEdit').click();
        } else if (e.which == KEYCODE.f) {                                      // !cmd-f
            $('#find').click();
        } else if (e.which == KEYCODE.l) {                                      // !cmd-l
            $('#fileListGet').click(); 
        } else if (e.which == KEYCODE.i) {                                      // !cmd-i
            $('#getInfo').click();        
        } else if (e.which == KEYCODE.m) {                                      // !cmd-m
            $('#fitsize').click();
        } else if (e.shiftKey && e.which == KEYCODE.n) {                        // !shift-cmd-N
            $('#newFolder').click();  
        } else if (e.which == KEYCODE.r) {                                      // !cmd-r
            $('#refresh').click();        
        } else if (e.which == KEYCODE.s) {                                      // !cmd-s
            $('#save').click();
        } else if (e.shiftKey && e.which == KEYCODE.t) {                        // !shift-cmd-T
            $('#batchTransform').click();
        } else if (e.which == KEYCODE.t) {                                      // !cmd-t
            $('#toggletem').click();
        } else if (e.shiftKey && e.which == KEYCODE.u) {                        // !shift-cmd-U
            $('#webcamPhoto').click();
        } else if (e.which == KEYCODE.u) {                                      // !cmd-u
            $('#upload').click();         
        } else if (e.which == KEYCODE.v) {                                      // !cmd-v
            $('#pasteItems').click();     
        } else if (e.which == KEYCODE.w) {                                      // !cmd-w
            $(".modal:visible").dialog("close");
        } else if (e.which == KEYCODE.x) {                                      // !cmd-x
            $('#cutItems').click();
        } else if (e.shiftKey && e.which == KEYCODE.z) {                        // !shift-cmd-Z
            $('#redo').click();
        } else if (e.which == KEYCODE.z) {                                      // !cmd-z
            $('#undo').click();           
        } else if (e.which == KEYCODE.plus
                || e.which == KEYCODE.add
                || e.which == KEYCODE.equal_sign) {                             // !cmd-plus (+)
            $('#zoomin').click();
        } else if (e.which == KEYCODE.minus
                || e.which == KEYCODE.subtract
                || e.which == KEYCODE.dash) {                                   // !cmd-minus (-)
            $('#zoomout').click();
        } else if (e.which == KEYCODE.comma) {                                  // !cmd-comma (,)
            $('#prefs').click();
        } else if (e.shiftKey && WM.appWindow == 'delineate' && WM.delinfunc == 'move') {
            console.log(e.which);
            setTimeout(function() {
                // delay quickhelp so it doesn't show every time you use a shift-cmd shortcut
                if ((WM.pageEvents.key[KEYCODE.cmd] || WM.pageEvents.key[KEYCODE.ctrl])
                     && WM.pageEvents.key[KEYCODE.shift]) {
                    cursor('crosshair');
                    quickhelp('Click to add a point');
                }
            }, 500);
        } else if ($('.pt:hover').length) {                                     // !cmd-cursor is hovering over a delin point
            showHoverPoints();
        } else {
            return true;
        }
        return false;
    } else if (e.which == KEYCODE.esc) {
        $('body').removeClass('hologram');
    } else if ((e.which == KEYCODE.backspace || e.which == KEYCODE.delete) &&
                $('#average-list li.selected').length) {                        // !delete/backspace
        // delete selected items for average list
        $('#average-list li.selected').remove();
        averageListCheck();
    } else if (e.which == KEYCODE.backspace                                     // !backspace (no inputs focussed)
                && ($("input:focus").length === 0) 
                && ($("textarea:focus").length === 0)
              ) {
        // do nothing, just prevents accidental page back in FireFox
    } else if (e.which == KEYCODE.enter && WM.delinfunc == 'lineadd') {         // !enter (end new line)
        var line;

        WM.delinfunc = 'move';
        cursor('auto');
        quickhelp();
        line = WM.current.lines.length - 1;
        WM.delin.lineColors[line] = 'default';
        if (WM.current.lines[line].length < 2) {
            $('#footer-text').html('New line cancelled');
            WM.current.lines.pop();
        } else {
            var t;

            drawTem();
            t = 'New line finished [' + WM.current.lines[line].join() + ']';
            $('#footer-text').html(t).prop('data-persistent', t);
        }
    } else if (e.which == KEYCODE.enter
                && $finder.filter(':visible').length
                && $finder.find('li.file.selected').length == 1) {              // !enter (finder visible & 1 file selected)
        fileRename();
    } else if (e.which == KEYCODE.enter
                && $finder.filter(':visible').length
                && $finder.find('li.file.selected').length === 0
                && $finder.find('li.folder').not('.closed').length > 0) {       // !enter (finder visible & 1 folder selected)
        folderRename();
    } else if (e.which == KEYCODE.enter
                && $('button.ui-state-focus:visible').length == 1) {            // !enter (only 1 visible button focussed)
        $('button.ui-state-focus:visible').click();
    } else if (e.which == KEYCODE.left_arrow) {                                 // !left arrow
        if ($d3.filter(':visible').length && WM.d3) { 
            WM.d3.rotate('left');
        } else if (WM.appWindow == 'delineate') {
            nudge(-1, 0);
        } else if ($finder.find('.image-view:visible').length) {
            // go to previous image
            $finder.find('li.file.selected, li.folder:not(.closed)')
                   .last().prevAll('li:visible').first().find('> span').click();
        } else if ($finder.filter(':visible').length) {
            // go up a directory
            if ($finder.find('li.file.selected').length === 0) {
                var $lastOpen;

                $lastOpen = $finder.find('li.folder:not(.closed)[path!=""]:last')
                                   .parents('li.folder').eq(0);
                console.log($lastOpen.attr('path'));
                $lastOpen.find('> span').click();
            }
            $finder.find('li.file.selected').removeClass('selected');
            WM.finder.updateSelectedFiles();
        }
    } else if (e.which == KEYCODE.up_arrow) {                                   // !up arrow
        if ($d3.filter(':visible').length && WM.d3) { 
            WM.d3.rotate('up');
        } else if (WM.appWindow == 'delineate') {
            nudge(0, -1);
        } else if ($finder.find('.image-view:visible').length) {
            // go up a directory
            if ($finder.find('li.file.selected').length === 0) {
                var $lastOpen;

                $lastOpen = $finder.find('li.folder:not(.closed)[path!=""]:last')
                                       .parents('li.folder').eq(0);
                console.log($lastOpen.attr('path'));
                $lastOpen.find('> span').click();
            }
            $finder.find('li.file.selected').removeClass('selected');
            WM.finder.updateSelectedFiles();
        } else if ($finder.filter(':visible').length) {
            $finder.find('li.file.selected, li.folder:not(.closed)')
                   .last().prevAll('li:visible').first().find('> span').click();
        }
    } else if (e.which == KEYCODE.right_arrow) {                                // !right arrow
        if ($d3.filter(':visible').length && WM.d3) { 
            WM.d3.rotate('right');
        } else if (WM.appWindow == 'delineate') {
            nudge(1, 0);
        } else if ($finder.find('.image-view:visible').length) {
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
    } else if (e.which == KEYCODE.down_arrow) {                                 // !down arrow
        if ($d3.filter(':visible').length && WM.d3) { 
            WM.d3.rotate('down');
        } else if (WM.appWindow == 'delineate') {
            nudge(0, 1);
        } else if ($finder.find('.image-view:visible').length) {
            return false;
        } else if ($finder.filter(':visible').length) {
            // go to next image
            $finder.find('li.file.selected, li.folder:not(.closed)')
                   .last().nextAll('li:visible')
                   .first().find('> span').click();
        }
    } else if (e.which == KEYCODE.esc) {                                        // !escape
        $('#refresh').click();
    } else {
        return true;
    }
    e.preventDefault();
});
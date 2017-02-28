//====================================
// !GENERIC AND PAGE-LEVEL FUNCTIONS
//====================================

// remove console functions if they are undefined (old IE)
if (typeof console === "undefined") {
    console = {
        log: function() { },
        warn: function() { },
        time: function() { },
        timeEnd: function() { },
        debug: function() { },
    };
}

$.fn.reverse = [].reverse;

// select part of a text field
$.fn.selectRange = function(start, end) {
    return this.each(function() {
        if (this.setSelectionRange) {
            this.focus();
            this.setSelectionRange(start, end);
        } else if(this.createTextRange) {
            var range = this.createTextRange();
            range.collapse(true);
            range.moveEnd('character', end);
            range.moveStart('character', start);
            range.select();
        }
    });
};

// make :contains case-insensitive
$.expr[":"].contains = $.expr.createPseudo(function(arg) {
    return function( elem ) {
        return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
    };
});

// set defaults for dialog boxes
if ($(window).width() > 640) {
    $.extend($.ui.dialog.prototype.options, {
        modal: true,
        width: 600,
        maxHeight: 500,
        position: {my: 'top', at: 'bottom+20', of: $('#menubar')},
    });
} else {
    $.extend($.ui.dialog.prototype.options, {
        modal: true,
        width: $(window).width(),
        maxHeight: $(window).height(),
        position: {my: 'top', at: 'top', of: 'window'},
    });
}

// stripe a table or list
$.fn.stripe = function() {
    var $rows;

    $rows = $(this[0]).find('tbody > tr, > li');
    $rows.filter(':odd').addClass("odd");
    $rows.filter(':even').addClass("even");
};

// filter elements by data
$.fn.filterByData = function (prop, val) {
    var $self = this;

    if (typeof val === 'undefined') {
        return $self.filter(
            function () { return typeof $(this).data(prop) !== 'undefined'; }
        );
    }
    return $self.filter(
        function () { return $(this).data(prop) == val; }
    );
};

// set defaults for ajax
$.xhrPool = []; // array of uncompleted requests
$.xhrPool.abortAll = function() { // our abort function
    $(this).each(function(idx, jqXHR) {
        jqXHR.abort();
    });
    $.xhrPool.length = 0;
};

$.ajaxSetup({
    dataType: 'json',
    type: 'POST',
    beforeSend: function(jqXHR) { // before jQuery send the request we will push it to our array
        $.xhrPool.push(jqXHR);
    },
    complete: function(jqXHR) { // when some of the requests completed it will splice from the array
        var index;

        index = $.xhrPool.indexOf(jqXHR);
        if (index > -1) {
            $.xhrPool.splice(index, 1);
        }
    }
});


// check if an array a contains an item obj
function contains(a, obj) {
    for (var i = 0; i < a.length; i++) {
        if (a[i] === obj) {
            return true;
        }
    }
    return false;
}

function array_sum(a) {
    return a.reduce(function (a, b) { return a + b; }, 0);
}

function array_mean(a) {
    return array_sum(a) / a.length;
}

// pad a number with leading zeros (or other chararcter in 3rd argument
function pad(original, width, padder) {
    var len;

    padder = padder || '0';
    original = original + ''; // turn numbers into strings
    len = original.length;
    return (len >= width) ? original : new Array(width - len + 1).join(padder) + original;
}

// round a number in apa style
function round(original, decimals) {
    var apa_decimals = 0,
        v,
        mult,
        rounded;

    v = Math.abs(original);
    if (v < 0.0001) {
        apa_decimals = 5;
    } else if (v < 0.001) {
        apa_decimals = 4;
    } else if (v < 0.01) {
        apa_decimals = 3;
    } else if (v < 10) {
        apa_decimals = 2;
    } else if (v < 100) {
        apa_decimals = 1;
    }

    decimals = decimals || apa_decimals;

    mult = Math.pow(10, decimals);
    rounded = Math.round(original * mult) / mult;
    return rounded;
}

// sort array of numbers (javascript defaults to sort as string)
function sortNumber(a, b) {
    return a - b;
}

// timed small notifications
function growl(txt, interval, pos) {
    var $growlDialog;

    $growlDialog = $('<div />').addClass('growl').html(txt).draggable();

    $('body').prepend($growlDialog);
    if (interval >= 100) {
        setTimeout(function() { $growlDialog.remove(); }, interval);
    }
}

// download without leaving the page
function postIt(url, data) {
    $('#jQueryPostItForm').remove();
    $('body').append($('<form/>', {
        id: 'jQueryPostItForm',
        method: 'POST',
        action: url
    }));
    for (var i in data) {
        if (data.hasOwnProperty(i)) {
            $('#jQueryPostItForm').append($('<input/>', {
                type: 'hidden',
                name: i,
                value: data[i]
            }));
        }
    }
    WM.noOnBeforeUnload = true;
    $('#jQueryPostItForm').submit();
    WM.noOnBeforeUnload = false;
}

function cursor(curs) {
    if (curs == 'lineadd') curs = 'url("include/images/cursors/add_line.cur"), auto';
    if (curs == 'linesub') curs = 'url("include/images/cursors/sub_line.cur"), auto';
    $('body').css('cursor', curs);
}

// set up a contextual menu (right-click)
function context_menu(items, e) {
    // items = array of items, each an objects with a name (to display in the menu) and func (function when clicked)
    var $menu,
        $ul,
        filteredItems = [],
        itemN;

    // filter out read-only items
    if (WM.project.perm == 'read-only') {
        $.each(items, function(i, item) {
            if (item.readOnly !== true) {
                filteredItems.push(item);
            }
        });
    } else {
        filteredItems = items;
    }

    itemN = filteredItems.length;
    
    $('.context_menu').remove();

    $menu = $('<div class="context_menu" />');
    $ul = $('<ul />');
    $.each(filteredItems, function(i, item) {
        var $theItem = '';

        if (item == 'break' && i !== 0 && i !== itemN-1 & items[i-1] !== 'break') {
            $theItem = $('<li class="separator" />');
        } else {
            $theItem = $('<li />').html(item.name).click( item.func);
        }
        $ul.append($theItem);
    });

    $menu.append($ul);
    $menu.css({
        'left' : (e.pageX || e.x) - 10,
        'top' : (e.pageY || e.y) - 10
    });

    $('body').append($menu);
}

function urlToName(url) {
    var name,
        regex;

    name = url.replace(/^\s*(http:\/\/)?(webmorph\.org|webmorph\.test|test\.psychomorph|psychomorph\.facelab\.org)?/, '');
    name = name.replace(/^\/scripts\/fileAccess\?file=/, '');
    regex = new RegExp('^' + WM.project.id + '\/', 'g');
    name = name.replace(regex, '/');

    return name;
}

function spinner(css, $appendTo) {
    var $spinner;
    
    if (css === false) {
        $('.rainbow-loader').remove();
        return true;
    }

    $spinner = $('<div class="rainbow-loader">'
               + '<div><div></div></div>'
               + '<div><div></div></div>'
               + '<div><div></div></div>'
               + '<div><div></div></div>'
               + '<div><div></div></div>'
               + '<div><div></div></div>'
               + '</div>');


    if (typeof css == 'object') { 
        $spinner.css(css);
    }

    if ($appendTo !== false) {
        if ($appendTo === undefined) {
            // only one body loader animation at a time please
            $('.rainbow-loader').remove();
            $appendTo = $('body');
        }
        
        $appendTo.append($spinner);
    }

    return $spinner;
}

function rgbToArray(rgb) {
    var rgbarray = [127,127,127,255];
    if (typeof rgb !== 'string') return rgbarray;
    
    rgbarray = rgb.replace('rgb(', '')
                  .replace('rgba(', '')
                  .replace(')', '')
                  .split(',');
    
    if (rgbarray.length == 3) {
        rgbarray.push(255);
    } else if (rgbarray.length == 4) {
        rgbarray[3] = rgbarray[3]*255;
    }
    
    return rgbarray;
}

function array2rgb(arr) {
    var rgb = 'rgba(127,127,127,1)';
    
    if (arr.length == 3) {
        rgb = 'rgba(' + arr[0] + ',' + arr[1] + ',' + arr[2] + ', 1)';
    } else if (arr.length == 4) {
        rgb = 'rgba(' + arr[0] + ',' + arr[1] + ',' + arr[2] + ',' + round(arr[3]/255,2) + ')';
    }
    
    return rgb;
}

/*
function resizeTags() {  console.log('resizeTags()');
    // update all font sizes for visible tags
    var visTags = {},
        visImages;

    $('#my_images div').filter(':visible').find('img').each(function() {
        var thisTag = $(this).attr('title');
        var thisTagList = thisTag.split(';');

        $.each(thisTagList, function(i, v) {
            if (visTags[v]) {
                visTags[v] = visTags[v] + 1;
            } else {
                visTags[v] = 1;
            }
        });
    });

    visImages = $('#my_images div:visible img').length;

    $('#taglist a').each(function() {
        var $a,
            fontsize,
            n;

        $a = $(this);

        if ($a.html() == 'ALL') {
            if (visImages == $('#my_images img').length) {
                $a.hide();
            } else {
                $a.show();
            }
        } else if ($a.html() == 'NONE') {
            if (visImages === 0) {
                $a.hide();
            } else {
                $a.show();
            }
        } else {
            n = visTags[$a.html()];
            if (n > 0) {
                fontsize = 75 + ((n / visImages) * 75);
                $a.css('font-size', fontsize + '%').show();
            } else {
                $a.hide();
            }
        }
    });

    updateSelectedImages();
}
*/

function hashGet() {
    var hash,
        hsplit,
        data = {};

    hash = location.hash;

    hsplit = hash.match(/#([PFDATX]?)(\d*)?(\/\S+)?/);

    if (hsplit != null && hsplit.length == 4) {
        data.appWindow = hsplit[1];
        data.project_id = hsplit[2];
        data.file = hsplit[3];
    }

    return data;
}

function hashSet() { //console.log('hashSet()');
    var appWindow = '',
        file = '',
        proj = '',
        hash = '',
        $selFile;

    if (WM.appWindow && WM.appWindow != 'login') {
        appWindow = WM.appWindow[0].toUpperCase();
    }

    if (WM.project.id) {
        proj = WM.project.id;
    }
    
    if (WM.faceimg != '' && appWindow == 'D') {
        file = urlToName(WM.faceimg);
    } else if (appWindow == 'F') {
        $selFile = $finder.find('li.file.selected').filter(':visible');
        if ($selFile.length == 1) {
            file = urlToName($selFile.attr('url'));
        } else {
            file = urlToName(currentDir());
        }
    }
    
    // get rid of old project ID during projSet()
    file = file.replace(/^\d+/, '');

    hash = appWindow + proj + file;

    if (hash && '#' + hash !== location.hash) {
        location.hash = "#" + hash;
        //setCookie('hash', hash, 1);
    }
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}


function hashChange() { //console.log('hashchange: ' + location.hash);
    var hash,
        appWin;

    hash = hashGet();
    appWin = WM.appWindow[0].toUpperCase();

    if (!hash) return false;

    if (hash.appWindow == appWin) {
        if (hash.project_id != WM.project.id) {
            console.log("hash: " + hash.project_id + ", WM: " + WM.project.id);
            projectSet(hash.project_id);
        }
        if (WM.project.id + hash.file != WM.faceimg && hash.appWindow == 'D') {
            if (hash.file.substr(-4) == ".obj") {
                d3_load_image(WM.project.id  + hash.file);
            } else {
               delinImage(WM.project.id  + hash.file);
            }
        }
    } else {
        if (hash.project_id != WM.project.id) {
            projectSet(hash.project_id, hash.appWindow);
        } else if (hash.appWindow == 'P') {
            $('#showProjects').click();
        } else if (hash.appWindow == 'F') {
            $('#showFinder').click();
        } else if (hash.appWindow == 'D') {
            $('#showDelineate').click();
        } else if (hash.appWindow == 'A') {
            $('#showAverage').click();
        } else if (hash.appWindow == 'T') {
            $('#showTransform').click();
        } else if (hash.appWindow == 'X') {
            $('#showThreeD').click();
        }
    }
}

function interfaceChange(e) {
    var appWindow,
        interfaceList;
    
    // make sure you are changing to a valid interface
    interfaceList = [
        'login', 
        'project', 
        'finder', 
        'delineate', 
        'average', 
        'transform'
    ];
    
    if (e.hasOwnProperty('data') && 
        e.data.hasOwnProperty('appWindow') && 
        contains(interfaceList, e.data.appWindow)) {
        appWindow = e.data.appWindow;
    } else if (e != null && contains(interfaceList, e)) {
        appWindow = e;
    } else {
        console.debug(e);
        return false;
    }
    
    console.log('interfaceChange('+appWindow+')');
    WM.appWindow = appWindow;
    //setTimeout(function(){}, 500);

    // set menu options for this interface
    $('#menu_window .checkmark').hide().filter('.' + appWindow).show();
    $('.no-read-only').removeClass('disabled');
    $('#menubar').find('.average, .finder, .transform, .delineate, .project .threeD').addClass('disabled');
    $('#menubar .' + appWindow).removeClass('disabled');
    
    if (WM.project.perm == 'read-only') {
        $('.no-read-only').addClass('disabled');
    }
    
    // hide other interfaces and show this one
    $('.interface').not('#' + appWindow + 'Interface').hide();
    $('#' + appWindow + 'Interface').show();
    
    $('#newFolder').html('New Folder <span class="shortcut shiftcmd">N</span>');
    
    // interface-specific setup
    if (appWindow == 'project') {
        projectList();
        $('#newFolder').html('New Project <span class="shortcut shiftcmd">N</span>');
        $('#footer-text').html('Projects');
    } else if (appWindow == 'finder') {
        $finder.insertAfter($('#uploadbar'));
        $('#recent_creations').hide();
        if ($finder.html() == '') { loadFiles(); }
        $finder.find('li.file').show()
               .filter('ui-draggable')
               .draggable('option', 'containment', 'window'); // '#finder');
        $finder.find('li.folder.ui-draggable').draggable('enable');
        $('#footer-text').html('Finder');
    } else if (appWindow == 'delineate') {
        $('#recent_creations').hide();
        $('#footer-text').html('Delineate');
    } else if (appWindow == 'average') {
        if ($finder.html() == '') { loadFiles(); }
        var padwidth = $('#avg_image_box').outerWidth(true) + 20;
        $('#individual_image_box').insertAfter($('#avg_image_box'))
                                  .css("padding-left", padwidth);
        $finder.appendTo($('#individual_image_box'));
        $finder.find('li.file').hide()
               .filter('.image.hasTem').show()
               .filter('.ui-draggable')
               .draggable('option', 'containment', 'window');
        $finder.find('li.folder.ui-draggable').draggable('disable');
        $('#recent_creations').insertAfter($('#individual_image_box')).show();
        $('#view-average-button, #save-button').button({ disabled: true });
        checkAvgAbility();
        $('#imgname').html('');
        $('#footer-text').html('Average');
    } else if (appWindow == 'transform') {
        if ($finder.html() == '') { loadFiles(); }
        var padwidth = $('#destimages').outerWidth(true) + 20;
        $('#individual_image_box').insertAfter($('#continua'))
                                  .css("padding-left", padwidth);
        $finder.appendTo($('#individual_image_box'));
        $finder.find('li.file')
               .filter('.image.hasTem').show()
               .filter('.ui-draggable')
               .draggable('option', 'containment', 'window');
        $finder.find('li.folder.ui-draggable').draggable('disable');
        $('#recent_creations').insertAfter($('#individual_image_box')).show();
        $('#transButton, #trans-save-button').button({ disabled: true });
        checkTransAbility();
        $('#imgname').html('');
        $('#footer-text').html('Transform');
    } else if (appWindow == 'login') {
        $('#login_email').focus();
        $('#imgname').html('');
        $('#footer-text').html('WebMorph.org');
    }
    
    $('#menubar li.menucategory > ul.submenu').hide();

    sizeToViewport();
    hashSet(); 
}

function sizeToViewport() {
    var finderHeight;
    
    if ($(window).width() <= 768) {
        $('#menubar').hide();
        $('#toggleMenu').show();
    } else {
        $('#menubar').show();
        $('#toggleMenu').hide();
    }
    $('#quickSwitch').hide();

    if (WM.appWindow == 'finder') {
        finderHeight = $(window).height() - $finder.offset().top - $('#footer').height() - 20;
    } else if (WM.appWindow == 'average') {
        if ($(window).width() > 640) {
            finderHeight = $('#avg_image_box').outerHeight();
            $('#individual_image_box').css("padding-left", $('#avg_image_box').outerWidth(true) + 20);
        } else {
            finderHeight = ($(window).height() - $('#avg_image_box').offset().top - $('#footer').height() - 20)/2;
            $('#avg_image_box').css('height', finderHeight + 'px');
            $('#individual_image_box').css("padding-left", '0');
        }
        $('#average').height($('#avg_image_box').innerHeight()-50);
        $('#average').width($('#avg_image_box').innerWidth()-20);
        $('#average-list').height($('#average').innerHeight());
    } else if (WM.appWindow == 'transform') {
        if ($(window).width() > 640) {
            $('#trans_minus').appendTo($('#destimages li:nth-child(2)'));
            $('#trans_plus').appendTo($('#destimages li:nth-child(2)'));
            $('#transform').width($('#transimage').width()).height($('#transimage').height());
            $('#destimages').css('height', 'auto');
            $('#individual_image_box').css("padding-left", $('#destimages').outerWidth(true) + 20);
            finderHeight = $('#destimages').outerHeight();
            //finderHeight = ($(window).height() - $finder.offset().top - $('#footer').height());
        } else {
            finderHeight = ($(window).height() - $('#destimages').offset().top - $('#footer').height() - 20)/2;
            $('#trans_plus').closest('div').insertAfter($('#transimage').closest('div'));
            $('#trans_minus').insertAfter($('#transimage').closest('div'));
            $('#transform').width($('#transimage').width()).height($('#transimage').height());
            $('#destimages').css('height', 'auto');
            $('#individual_image_box').css("padding-left", '0');
        }
    } 
    
    if (WM.appWindow == 'delineate' && $d3.filter(':visible').length) {
        WM.d3.sizeToViewport();
    } else {
        // make sure finder is at least 300px tall
        $finder.height(Math.max(300, finderHeight));
    }

    

    $('#filepreview').offset({'top': $finder.position().top + 1});
}
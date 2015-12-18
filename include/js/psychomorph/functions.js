//====================================
// !GENERIC FUNCTIONS
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

// select part of a text field
$.fn.selectRange = function(start, end) {
    return this.each(function() {
        if(this.setSelectionRange) {
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

// set defaults for dialog boxes
$.extend($.ui.dialog.prototype.options, { 
    modal: true, 
    width: 600, 
    maxHeight: 500,
    position: {my: 'top', at: 'bottom+20', of: $('ul.menubar')},
});

// stripe a table or list
$.fn.stripe = function() {
    var $rows = $(this[0]).find('tbody > tr, > li');
    $rows.filter(':odd').addClass("odd");
    $rows.filter(':even').addClass("even");
};

// set defaults for ajax
$.ajaxSetup({
    dataType: 'json',
    type: 'POST',
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

// pad a number with leading zeros (or other chararcter in 3rd argument
function pad(original, width, padder) {
    padder = padder || '0';
    original = original + ''; // turn numbers into strings
    var len = original.length;
    return (len >= width) ? original : new Array(width - len + 1).join(padder) + original;
}

// round a number in apa style
function round(original, decimals) {
    var apa_decimals = 0;
    var v = Math.abs(original);
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
    
    var mult = Math.pow(10, decimals);
    var rounded = Math.round(original * mult) / mult;
    return rounded;
}

// sort array of numbers (javascript defaults to sort as string)
function sortNumber(a, b) {
    return a - b;
}

// timed small notifications
function growl(txt, interval, pos) {
    var $growlDialog = $('<div />').addClass('growl').html(txt).draggable();
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
        $('#jQueryPostItForm').append($('<input/>', {
            type: 'hidden',
            name: i,
            value: data[i]
        }));
    }
    PM.no_onbeforeunload = true;
    $('#jQueryPostItForm').submit();
    PM.no_onbeforeunload = false;
} 

function cursor(curs) {
    if (curs == 'lineadd') curs = 'url("include/images/cursors/add_line.cur"), auto';
    if (curs == 'linesub') curs = 'url("include/images/cursors/sub_line.cur"), auto';
    $('body').css('cursor', curs);
}

// set up a contextual menu (right-click)
function context_menu(items, e) {
    // items = array of items, each an objects with a name (to display in the menu) and func (function when clicked) 
    var $menu = $('<div class="context_menu" />');
    var $ul = $('<ul />');
    
    $.each(items, function(i,item) {
        var $theItem;
        if (item == 'break') {
            $theItem = $('<li class="separator" />');
        } else {
            $theItem = $('<li />').html(item.name).click( item.func);
        }
        $ul.append($theItem);
    });
    
    $menu.append($ul);
    $menu.css({
        'left' : e.pageX - 10,
        'top' : e.pageY - 10
    });
    
    $('body').append($menu);
}

function menubar(currentInterface) { console.log('menubar('+currentInterface+')');
    PM.interface = currentInterface;
    $('.menubar .average, .menubar .finder, .menubar .transform, .menubar .delineate, .menubar .project').addClass('disabled');
    $('.menubar .' + currentInterface).removeClass('disabled');
}

function urlToName(url) { //console.debug('urlToName(' + url + ')');
    var name = url.replace(/^\s*(http:\/\/)?(test\.psychomorph|psychomorph\.facelab\.org)?/, '');
    name = name.replace(/^\/scripts\/fileAccess\?file=/, '');
    name = name.replace('/scripts/fileAccess?file=', '');
    name = name.replace(PM.project + '/', '/');
    return name;
}

function spinner(css) {
	var $spinner = $('<div class="rainbow-loader">'
	               + '<div><div></div></div>'
	               + '<div><div></div></div>'
	               + '<div><div></div></div>'
	               + '<div><div></div></div>'
	               + '<div><div></div></div>'
	               + '<div><div></div></div>'
	               + '</div>');
	
	if (css !== "undefined") { $spinner.css(css); }
    
    return $spinner;
}

function bodySpinner() {
	var $spinner;

    $spinner = spinner({
        'font-size': '300px',
        'position': 'absolute'
    });
    /*
	$spinner = $('<div class="rainbow-spin"><div><div><div><div><div><div><div></div></div></div></div></div></div></div></div>').css({
        'font-size': '200px',
        'position': 'fixed',
        'top': '50%',
        'left': '50%',
        'margin-left': '-50px',
        'margin-top': '-50px'
    });
    
    $spinner = $('<div class="spinner" />').css({
        'font-size': '150px',
        'position': 'fixed',
        'top': '50%',
        'left': '50%',
        'margin-left': '-50px',
        'margin-top': '-50px'
    });
    */
    
    $('body').append($spinner);
    
    return $spinner;
}

function resizeTags() {  console.log('resizeTags()');
    // update all font sizes for visible tags
    var visTags = {};
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
    var visImages = $('#my_images div:visible img').length;
    $('#taglist a').each(function() {
        var $a = $(this);
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
            var n = visTags[$a.html()];
            if (n > 0) {
                var fontsize = 75 + ((n / visImages) * 75);
                $a.css('font-size', fontsize + '%').show();
            } else {
                $a.hide();
            }
        }
    });
    updateSelectedImages();
}

function sizeToViewport() { // console.log('sizeToViewport()');
    if (PM.interface == 'finder') {    
        var finderHeight = $(window).height() - $finder.offset().top - 44;
        $finder.height(finderHeight);
    } else if (PM.interface == 'average') {
        $finder.height($('#avg_image_box').outerHeight());
        //$('#avg_image_box').css('width', 'auto');
        $('#individual_image_box').css("padding-left", $('#avg_image_box').outerWidth(true) + 20);
        $('#average_list').height($('#average').outerHeight());
        
    } else if (PM.interface == 'transform') {
        $('#destimages').css('height', 'auto');
        $finder.height($('#destimages').outerHeight());
        $('#transform').width($('#transimage').width()).height($('#transimage').height());
        $('#individual_image_box').css("padding-left", $('#destimages').outerWidth(true) + 20);
    }
    
    $('#imagebox').css({
        'position': 'fixed',
        'top': $finder.position().top + 1
    }).height($finder.height()-1);
}
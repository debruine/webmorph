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

// set defaults for dialog boxes
$.extend($.ui.dialog.prototype.options, { 
    modal: true, 
    width: 600, 
    maxHeight: 500,
    position: {my: 'top', at: 'bottom+20', of: $('ul.menubar')},
});

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
    PM.noOnBeforeUnload = true;
    $('#jQueryPostItForm').submit();
    PM.noOnBeforeUnload = false;
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
    if (PM.project.perm == 'read-only') {
        $.each(items, function(i, item) {
            if (item.readOnly !== true) {
                filteredItems.push(item);
            }
        });
    } else {
        filteredItems = items;
    }
    
    itemN = filteredItems.length;
    
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
        'left' : e.pageX - 10,
        'top' : e.pageY - 10
    });
    
    $('body').append($menu);
}

function menubar(currentInterface) { console.log('menubar('+currentInterface+')');
    PM.interfaceWindow = currentInterface;
    $('.menubar .average, .menubar .finder, .menubar .transform, .menubar .delineate, .menubar .project').addClass('disabled');
    $('.menubar .' + currentInterface).removeClass('disabled');
    
    if (PM.project.perm == 'read-only') {
        $('.no-read-only').addClass('disabled');
    } else {
        $('.no-read-only').removeClass('disabled');
    }
}

function urlToName(url) {
    var name,
        regex;
    
    name = url.replace(/^\s*(http:\/\/)?(webmorph\.org|webmorph\.test|test\.psychomorph|psychomorph\.facelab\.org)?/, '');
    name = name.replace(/^\/scripts\/fileAccess\?file=/, '');
    regex = new RegExp('^' + PM.project.id + '\/', 'g');
    name = name.replace(regex, '/');
    
    return name;
}

function spinner(css) {
    var $spinner;
    
    $spinner = $('<div class="rainbow-loader">'
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
    
    $('body').append($spinner);
    
    return $spinner;
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

function sizeToViewport() { 
    var finderHeight;
    
    if (PM.interfaceWindow == 'finder') {    
        finderHeight = $(window).height() - $finder.offset().top - 44;
    } else if (PM.interfaceWindow == 'average') {
        finderHeight = $('#avg_image_box').outerHeight();
        $('#individual_image_box').css("padding-left", $('#avg_image_box').outerWidth(true) + 20);
        $('#average-list').height($('#average').innerHeight());
    } else if (PM.interfaceWindow == 'transform') {
        $('#destimages').css('height', 'auto');
        finderHeight = $('#destimages').outerHeight();
        $('#transform').width($('#transimage').width()).height($('#transimage').height());
        $('#individual_image_box').css("padding-left", $('#destimages').outerWidth(true) + 20);
    }
    
    $finder.height(finderHeight);
    
    $('#imagebox').css({
        'position': 'fixed',
        'top': $finder.position().top + 1
    }).height($finder.height()-1);
}
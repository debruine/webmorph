//====================================
// !DELINEATE FUNCTIONS
//====================================

function newDelinPoint(e) {
    var imgoffset = $delin.offset();
    
    var x = (e.pageX - imgoffset.left)/PM.temRatio;
    var y = (e.pageY - imgoffset.top)/PM.temRatio;
    var i = PM.current.tem.length;
    PM.current.tem.push({
        i: i,
        name: 'new point',
        x: x,
        y: y
    });
    makePoint(x,y,i);
    updateUndoList();
    drawTem();
}

function setSymPoints() {
    if (PM.delin.tem.length != PM.current.tem.length) {
        growl('The current template does not match the template <code>' 
              + $('#currentTem_name').text() + '</code>');
        return true;
    }
    
    $.ajax({
        url: '/scripts/userCheckAccess',
        data: { table: 'tem', id: PM.delin.temId },
        success: function(data) {
            if (data.error) {
                growl(data.errorText);
            } else {
                var theText = '<p>To set symmetry points, look for the '
                            + 'highlighted point and click on its corresponding '
                            + 'point on the other side. If a point is on the '
                            + 'midline, its corresponding point is itself. If you '
                            + 'make a mistake, click cmd-Z to go back a point.</p>';
                $('<div />').html(theText).dialog({
                    title: 'Set Symmetry Points for ' 
                            + $('#currentTem_name').text(),
                    modal: false,
                    buttons: {
                        Cancel: function() { $(this).dialog("close"); },
                        "Start": {
                            text: 'Start',
                            class: 'ui-state-focus',
                            click: function() {
                                $(this).dialog("close");
                                PM.symPts = {
                                    n: 0,
                                    order: [],
                                    sym: []
                                };
                                PM.delinfunc = 'sym';
                                nextSymPt('start');
                            }
                        }
                    }
                });
            }
        }
    });
}

function quickhelp(text, fadeout) {  console.log('quickhelp(' + text + ', ' + fadeout + ')');
    if (text === undefined || text.trim() === '') {
        $('#quickhelp').fadeOut();
    } else {
        $('#quickhelp').html(text).fadeIn();
    }
    
    if (fadeout !== undefined && fadeout > 100 && fadeout < 10000) {
        setTimeout(function() { $('#quickhelp').fadeOut(); }, fadeout);
    }
}

function threePtDelin(e) {
    var imgoffset = $delin.offset();
    PM.delinfunc = '3pt';
    var thePt;
    
    if (PM.eyeClicks.length === 0) {
        PM.eyeClicks[0] = {
            x: (e.pageX - imgoffset.left)/PM.temRatio,
            y: (e.pageY - imgoffset.top)/PM.temRatio
        };
        thePt = $('#leftEye');
        thePt.css('left', e.pageX - (thePt.width() / 2))
             .css('top', e.pageY - (thePt.height() / 2)).show();
        
        clickPt(1);
    } else if (PM.eyeClicks.length == 1) {
        PM.eyeClicks[1] = {
            x: (e.pageX - imgoffset.left)/PM.temRatio,
            y: (e.pageY - imgoffset.top)/PM.temRatio
        };
        thePt = $('#rightEye');
        thePt.css('left', e.pageX - (thePt.width() / 2))
             .css('top', e.pageY - (thePt.height() / 2)).show();
        
        clickPt(2);
    } else if (PM.eyeClicks.length == 2) {
        PM.eyeClicks[2] = {
            x: (e.pageX - imgoffset.left)/PM.temRatio,
            y: (e.pageY - imgoffset.top)/PM.temRatio
        };
        thePt = $('#mouth');
        thePt.css('left', e.pageX - (thePt.width() / 2))
             .css('top', e.pageY - (thePt.height() / 2)).show();

        quickhelp();
        cursor('auto');
        
        var selpts = $('.pt.selected');
        if (selpts.length == 3) {
            selpts.each( function(i) {
                newfitPoints[i] = $(this).attr('n');
            });
        } else {
            newfitPoints = PM.fitPoints;
        }
        
        var temPoints = {
            0: {
                x: PM.current.tem[newfitPoints[0]].x,
                y: PM.current.tem[newfitPoints[0]].y
            },
            1: {
                x: PM.current.tem[newfitPoints[1]].x,
                y: PM.current.tem[newfitPoints[1]].y
            },
            2: {
                x: PM.current.tem[newfitPoints[2]].x,
                y: PM.current.tem[newfitPoints[2]].y
            }
        };
        $.ajax({
            async: false,
            url: 'scripts/temFit',
            data: {
                'eyeclicks': PM.eyeClicks,
                'temPoints': temPoints
            },
            success: function(data) {
                $.each(PM.current.tem, function(i, v) {
                    var newx = (data.a * v.x) + (data.b * v.y) + data.c;
                    var newy = (data.d * v.x) + (data.e * v.y) + data.f;
                    if (newx < 0) { newx = 0; }
                    if (newy < 0) { newy = 0; }
                    if (newx > $delin.width()/PM.temRatio) { 
                        newx = $delin.width()/PM.temRatio; 
                    }
                    if (newy > $delin.height()/PM.temRatio) { 
                        newy = $delin.height()/PM.temRatio; 
                    }
                    PM.current.tem[i].x = newx;
                    PM.current.tem[i].y = newy;
                });
                makePoints(PM.current.tem);
            }
        });
        $('#template').show();
        $('#leftEye, #rightEye, #mouth').hide();
        drawTem();
        if (typeof files !== 'undefined' && files.length) {
            saveTem();
            PM.selectedFile++;
            if (PM.selectedFile >= files.length) {
                growl('Batch Fit Template finished. ' 
                      + files.length + ' files aligned.', 3000);
                $('#footer').html('');
                files = [];
                PM.selectedFile = 0;
                PM.delinfunc = 'move';
            } else {
                $('#footer').html(PM.selectedFile + ' of ' 
                                  + files.length + ' templates fitted.');
                var url = files[PM.selectedFile];
                var name = PM.project + urlToName(url);
                delinImage(name, false);
                PM.eyeClicks = [];
                PM.delinfunc = '3pt';
            }
        } else {
            //saveTem();
            PM.delinfunc = 'move';
        }
    }
}

function delinImage(name, async) { console.log('delinImage(' + name + ', ' + async + ')');
    $.ajax({
        async: (typeof async === 'boolean') ? async : true,
        url: 'scripts/imgDelin',
        data: { img: name },
        success: function(data) {
            var h = $delin.height();
            PM.originalHeight = data.originalHeight;
            PM.originalWidth = data.originalWidth;
            var w = Math.round((h * data.originalWidth) / PM.originalHeight);
            PM.temRatio = h / PM.originalHeight;
            $('#size_value').html(w + 'x' + h);
            $('#template').css({
                "width": w + "px ",
                "height": h + 'px'
            });
            PM.faceimg = data.imgname;

            $delin.css({
                "width": w + "px ",
                "background": "white 0 0 no-repeat url(" + fileAccess(PM.faceimg) + ")",
                "background-size": "100%"
            });
            $('#imgname').html(PM.faceimg).attr('title', PM.faceimg);
            $('#delin_save').removeClass('unsaved');
            var needsDelin = false;
            
            $delin.find('.pt').remove();

            if (data.temPoints !== null) {
                PM.delinfunc = 'move';
                autoLoadTem(data.temPoints, data.lineVectors);
                
                PM.current.tem = [];
                $.each(data.temPoints, function(i, v) {
                    PM.current.tem[i] = { x: v[0], y: v[1] };
                });
                PM.undo.tem = [$.extend(true, [], PM.current.tem)];
                PM.undo.level = 0;
                PM.current.lines = data.lineVectors;
                PM.undo.lines = [$.extend(true, [], PM.current.lines)];
            } else {
                PM.delinfunc = '3pt';
                PM.current.tem = [];
                $.each(PM.delin.tem, function(i, v) {
                    PM.current.tem[i] = { x: v.x, y: v.y };
                });
                PM.undo.tem = [$.extend(true, [], PM.current.tem)];
                PM.current.lines = $.extend(true, [], PM.delin.lines);
                PM.undo.lines = [$.extend(true, [], PM.current.lines)];
                PM.undo.level = 0;
            }
            
            if (PM.interfaceWindow != 'delineate') $('#showDelineate').click();
            
            if (PM.delinfunc == '3pt') {
                PM.eyeClicks = [];
                $('#template').hide();
                cursor('crosshair');
                clickPt(0);
            } else if (PM.showTem) {
                PM.eyeClicks = [PM.fitPoints[0], PM.fitPoints[1], PM.fitPoints[2]];
                $('#template').show();
                $('#imgsize').change();
                makePoints(PM.current.tem);
                drawTem();
            }
            
            // sets template to the correct size
            $('#imgsize').slider('value', $('#imgsize').slider('value'));
        }
    });
}

function nextImg() {
    // load next image belonging to that user
    $.ajax({
        url: 'scripts/imgScroll',
        data: { next: PM.faceimg },
        success: function(data) {
            checkSaveFirst(function() { 
                //delinImage(data.img); 
                PM.faceimg = data.img;
                $('#refresh').click();
            });
        }
    });
}

function prevImg() {
    // load next image belonging to that user
    $.ajax({
        url: 'scripts/imgScroll',
        data: { prev: PM.faceimg },
        success: function(data) {
            checkSaveFirst(function() {                 
                //delinImage(data.img); 
                PM.faceimg = data.img;
                $('#refresh').click();
            });
        }
    });
}

function nudge(xchange, ychange) {  console.log('nudge(' + xchange + ', ' + ychange + ')');
    if (PM.delinfunc == 'move') {
        $('.pt.selected').each( function(i) {
            var selpt = $(this).attr('n');
            
            PM.current.tem[selpt].x += xchange/PM.temRatio;
            PM.current.tem[selpt].y += ychange/PM.temRatio;
        });
        updateUndoList();
        drawTem();
    }
}

function temSizeChange(pcnt) {
    if (PM.interfaceWindow == 'delineate' && PM.delinfunc == 'move') {
        var pcntChange = (100 + pcnt)/100;
        var x=0,y=0,n=0;
        
        var selpts = getSelPts();
        
        $.each(selpts, function(i, selpt) {
            x += PM.current.tem[selpt].x;
            y += PM.current.tem[selpt].y;
            n++;
        });
        
        var offset = {
            x: (x/n) - (pcntChange*x/n), 
            y: (y/n) - (pcntChange*y/n)
        };
    
        $.each(selpts, function(i, selpt) {
            PM.current.tem[selpt].x *= pcntChange;
            PM.current.tem[selpt].y *= pcntChange;
            PM.current.tem[selpt].x += offset.x;
            PM.current.tem[selpt].y += offset.y;
        });
        updateUndoList();
        drawTem();
    }
}

function pythag(x1, x2, y1, y2) {
    var xlength = Math.abs(x1 - x2);
    var ylength = Math.abs(y1 - y2);
    var sumsquares = (xlength * xlength) + (ylength * ylength);
    return Math.sqrt(sumsquares);
}

function getSelPts() {
    var selPts = [];
    $delin.find('.pt.selected').each( function(i) {
        selPts.push($(this).attr('n'));
    });
    return selPts;
}

function getControlPoints(x0, y0, x1, y1, x2, y2) {
    var t, d01, d12, fa, fb, p1x, p1y, p2x, p2y;
    
    t = 0.3;
    d01 = Math.sqrt(Math.pow(x1 - x0, 2) + Math.pow(y1 - y0, 2));
    d12 = Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2));
    fa = t * d01 / (d01 + d12); // scaling factor for triangle Ta
    fb = t * d12 / (d01 + d12); // ditto for Tb, simplifies to fb=t-fa
    
    p1x = x1 - fa * (x2 - x0); // x2-x0 is the width of triangle T
    p1y = y1 - fa * (y2 - y0); // y2-y0 is the height of T
    p2x = x1 + fb * (x2 - x0);
    p2y = y1 + fb * (y2 - y0);
    
    return [p1x, p1y, p2x, p2y];
}

function drawBezier(ctx, v, begin) {
    var pts = [],
        cp = [], // array of control points, as x0,y0,x1,y1,...
        n,
        j;
        
    begin = begin || true;
    if (v.length < 2) {
        return false;
    }
    
    if (begin) { ctx.beginPath(); }
    
    if (v.length == 2) {
        // connect with straight line
        ctx.moveTo(v[0][0], v[0][1]);
        ctx.lineTo(v[1][0], v[1][1]);
        ctx.stroke();
        return false;
    }

    // connect with bezier curve
    
    for (j = 0; j < v.length; j++) {
        pts.push(v[j][0]);
        pts.push(v[j][1]);
    }

    n = pts.length;
    if (pts[0] == pts[n - 2] && pts[1] == pts[n - 1]) {
        // Draw a closed curve, connected at the ends
        // remove duplicate points and adjust n
        n = n - 2;
        pts.pop();
        pts.pop();
        // Append and prepend knots and control points to close the curve
        pts.push(pts[0], pts[1], pts[2], pts[3]);
        pts.unshift(pts[n - 1]);
        pts.unshift(pts[n - 1]);
        for (j = 0; j < n; j += 2) {
            cp = cp.concat(getControlPoints(pts[j], pts[j + 1], pts[j + 2], pts[j + 3], pts[j + 4], pts[j + 5]));
        }
        cp = cp.concat(cp[0], cp[1]);
        for (j = 2; j < n + 2; j += 2) {
            ctx.moveTo(pts[j], pts[j + 1]);
            ctx.bezierCurveTo(cp[2 * j - 2], cp[2 * j - 1], cp[2 * j], cp[2 * j + 1], pts[j + 2], pts[j + 3]);
        }
    } else {
        // Draw an open curve, not connected at the ends
        for (j = 0; j < n - 4; j += 2) {
            cp = cp.concat(getControlPoints(pts[j], pts[j + 1], pts[j + 2], pts[j + 3], pts[j + 4], pts[j + 5]));
        }
        ctx.moveTo(pts[0], pts[1]);
        ctx.quadraticCurveTo(cp[0], cp[1], pts[2], pts[3]); // first arc
        for (j = 2; j < n - 5; j += 2) {
            ctx.bezierCurveTo(cp[2 * j - 2], cp[2 * j - 1], cp[2 * j], cp[2 * j + 1], pts[j + 2], pts[j + 3]);
        }
        ctx.quadraticCurveTo(cp[2 * n - 10], cp[2 * n - 9], pts[n - 2], pts[n - 1]); // last arc
    }
    ctx.stroke();
}

function svgBezier(v, lineWidth, stkColor) {
    var pts = [],
        cp = [], // array of control points, as x0,y0,x1,y1,...
        n,
        j,
        ctx = {},
        path = '    <path d="';
        
    ctx = {
        moveTo: function(x, y) {
            path += 'M' + x + ' ' + y;
        },
        lineTo: function(x, y) {
            path += ' L ' + x + ' ' + y;
        },
        stroke: function() {
            if (typeof stkColor === 'undefined' && typeof lineWidth === 'undefined') {
                path += '"/>';
            } else if (typeof lineWidth === 'undefined') {
                path += '" stroke="' + stkColor + '"/>';
            } else if (typeof stkColor === 'undefined') {
                path += '" stroke-width="' + lineWidth + '"/>';
            } else {
                path += '" stroke="' + stkColor + ' stroke-width="' + lineWidth + '"/>';
            }
        },
        quadraticCurveTo: function(x1, y1, x, y) {
            path += ' Q ' + x1 + ' ' + y1 + ', ' + x + ' ' + y; 
        },
        bezierCurveTo: function(x1, y1, x2, y2, x, y) {
            path += ' C ' + x1 + ' ' + y1 + ', ' + x2 + ' ' + y2 + ', ' + x + ' ' + y;
        }
    };
    
    if (v.length == 2) {
        // connect with straight line
        ctx.moveTo(v[0][0], v[0][1]);
        ctx.lineTo(v[1][0], v[1][1]);
        ctx.stroke();
        return path;
    }

    // connect with bezier curve
    for (j = 0; j < v.length; j++) {
        pts.push(v[j][0]);
        pts.push(v[j][1]);
    }

    n = pts.length;
    if (pts[0] == pts[n - 2] && pts[1] == pts[n - 1]) {
        // Draw a closed curve, connected at the ends
        // remove duplicate points and adjust n
        n = n - 2;
        pts.pop();
        pts.pop();
        // Append and prepend knots and control points to close the curve
        pts.push(pts[0], pts[1], pts[2], pts[3]);
        pts.unshift(pts[n - 1]);
        pts.unshift(pts[n - 1]);
        for (j = 0; j < n; j += 2) {
            cp = cp.concat(getControlPoints(pts[j], pts[j + 1], pts[j + 2], pts[j + 3], pts[j + 4], pts[j + 5]));
        }
        cp = cp.concat(cp[0], cp[1]);
        for (j = 2; j < n + 2; j += 2) {
            ctx.moveTo(pts[j], pts[j + 1]);
            ctx.bezierCurveTo(cp[2 * j - 2], cp[2 * j - 1], cp[2 * j], cp[2 * j + 1], pts[j + 2], pts[j + 3]);
        }
    } else {
        // Draw an open curve, not connected at the ends
        for (j = 0; j < n - 4; j += 2) {
            cp = cp.concat(getControlPoints(pts[j], pts[j + 1], pts[j + 2], pts[j + 3], pts[j + 4], pts[j + 5]));
        }
        ctx.moveTo(pts[0], pts[1]);
        ctx.quadraticCurveTo(cp[0], cp[1], pts[2], pts[3]); // first arc
        for (j = 2; j < n - 5; j += 2) {
            ctx.bezierCurveTo(cp[2 * j - 2], cp[2 * j - 1], cp[2 * j], cp[2 * j + 1], pts[j + 2], pts[j + 3]);
        }
        ctx.quadraticCurveTo(cp[2 * n - 10], cp[2 * n - 9], pts[n - 2], pts[n - 1]); // last arc
    }
    ctx.stroke();
    return path;
}

function updateUndoList() {  //console.log('updateUndoList()');
    var last_tem,
        last_lines,
        changed;
    
    last_tem = PM.undo.tem[PM.undo.level];
    last_lines = PM.undo.lines[PM.undo.level];
    changed = false;
    
    // check if new tem is changed from last tem
    $.each(PM.current.tem, function(i, v) {
        if (last_tem[i] === undefined || (PM.current.tem[i].x != last_tem[i].x || PM.current.tem[i].y != last_tem[i].y)) {
            changed = true;
            return false;
        }
    });
    if (!changed) {
        // check if lines are changed (only check if points not changed)
        $.each(PM.current.lines, function(i, line) {
            $.each(line, function(j, pt) {
                if (last_lines[i] === undefined || last_lines[i][j] === undefined || PM.current.lines[i][j] != last_lines[i][j]) {
                    changed = true;
                    return false;
                }
            });
        });
    }
    // add to undo list if changed
    if (changed) {
        PM.undo.level++;
        PM.undo.tem[PM.undo.level] = $.extend(true, [], PM.current.tem);
        PM.undo.lines[PM.undo.level] = $.extend(true, [], PM.current.lines);
        if (PM.project.perm != 'read-only') {
            $('#delin_save').addClass('unsaved');
        }
    }
}

function makePoints(ptArray) { console.time("makePoints()");
    var n = ptArray.length;
    
    PM.pts = [];
    for (var i = 0; i < n; i++) {
        makePoint(ptArray[i].x, ptArray[i].y, i);
    }
    
    console.timeEnd('makePoints()');
}

function makePoint(x, y, i) {
    var $pt,
        connectedPoints = [],
        connectedLines = [];
    
    
    $pt = $('<div class="pt" n="' + i + '" />').appendTo('#delin');
    PM.pts[i] = $pt;
    
    // set connected points and lines
    
    $.each(PM.current.lines, function (line, linearray) {
        if (contains(linearray, i)) {
            connectedPoints = connectedPoints.concat(linearray);
            connectedLines.push(line);
        }
    });
    $pt.data({
        'connectedPoints': connectedPoints,
        'connectedLines': connectedLines
    });
    
    $pt.draggable({
        cursor: "none",
        start: function(e, ui) {
            if (e.ctrlKey || e.metaKey || e.altKey) {
                $.each(connectedPoints, function(j, pt) {
                    PM.pts[pt].addClass('selected');
                });    
            }
        },
        drag: function(e, ui) {
            var move = {
                x: (ui.position.left/PM.temRatio) - PM.current.tem[i].x,
                y: (ui.position.top/PM.temRatio) - PM.current.tem[i].y
            };
            
            PM.current.tem[i].x = ui.position.left/PM.temRatio;
            PM.current.tem[i].y = ui.position.top/PM.temRatio;
            
            $('#footer .x').text(round(PM.current.tem[i].x, 1));
            $('#footer .y').text(round(PM.current.tem[i].y, 1));
            
            $.each(PM.current.tem, function(j,pt) {
                if (i != j && PM.pts[j].hasClass('selected')) {
                    pt.x += move.x;
                    pt.y += move.y;
                }
            });
    
            drawTem();
        },
    }).css({
        top: y*PM.temRatio,
        left: x*PM.temRatio
    });
}

function drawMask(masks, blur) {
    var $masked_img,
        ctx,
        blurtans,
        m,
        image;
    
    blur = blur || 10;
    $masked_img = $("<canvas width='" + PM.originalWidth + "' height='" + PM.originalHeight + "' />");
    ctx = $masked_img.get(0).getContext('2d');
    
    ctx.fillStyle = 'rgb(255,255,255)';
    blurtrans = (blur===0) ? 0.5 : (0.5/blur);
    ctx.strokeStyle = "rgba(255,255,255,"+ blurtrans +")";
    ctx.fillRect (0, 0, $('#template').width(), $('#template').height());
    ctx.globalCompositeOperation = "destination-out"; // cut-out mode
    
    m = masks.split(":");
    
    // draw blur lines first
    for (var i=blur; i>0; i--) {
        ctx.lineWidth = i;
        $.each(m, function(i, mask) {
            var lines = mask.split(';');
            
            ctx.beginPath();
            $.each(lines, function(j, line) {
                var pts = line.split(',');
                var bez = [];
                $.each(pts, function(k, p) {
                    var pt = parseInt(p.trim());
                    var x = PM.current.tem[pt].x;
                    var y = PM.current.tem[pt].y;
                    bez.push([x, y]);
                });
                drawBezier(ctx, bez, false);
            });
            ctx.closePath();
            ctx.stroke();
        });
    }
    
    // fill with transparent
    $.each(m, function(i, mask) {
        var lines = mask.split(';');
        ctx.beginPath();
        $.each(lines, function(j, line) {
            var pts = line.split(',');
            var bez = [];
            $.each(pts, function(k, p) {
                var pt = parseInt(p.trim());
                var x = PM.current.tem[pt].x;
                var y = PM.current.tem[pt].y;
                bez.push([x, y]);
            });
            drawBezier(ctx, bez, false);
        });
        ctx.closePath();
        ctx.fill();
    });
    
    ctx.globalCompositeOperation = "destination-over"; // add face underneath mask
    image = new Image();
    image.src = fileAccess(PM.faceimg);
    ctx.drawImage(image, 0, 0, PM.originalWidth, PM.originalHeight, 
                         0, 0, PM.originalWidth, PM.originalHeight);

    return $masked_img;
}

function drawTem() {
    var nlines, i, j, x, y,
        defaultLineWidth,
        bez = [],
        npoints,
        sel = 0, 
        csel=0,
        lineWidth,
        stkColor;
        
    PM.delinContext.clearRect (0, 0, $('#template').width(), $('#template').height());
    
    if (PM.pts === undefined || PM.pts.length === 0) return false;
    
    nlines = PM.current.lines.length;
    defaultLineWidth = $('#defaultLineWidth').val();
    
    for (i = 0; i < nlines; i++) {
        bez = [],
        sel = 0, 
        csel=0,
        npoints = PM.current.lines[i].length;
        
        for (j = 0; j < npoints; j++) {
            x = PM.current.tem[PM.current.lines[i][j]].x*PM.temRatio;
            y = PM.current.tem[PM.current.lines[i][j]].y*PM.temRatio;
            
            bez.push([x, y]);
            
            if (PM.pts[PM.current.lines[i][j]].hasClass('selected')) { sel++; }
            if (PM.pts[PM.current.lines[i][j]].hasClass('couldselect')) { csel++; }
        }
        
        stkColor = (typeof PM.delin.lineColors[i]=='undefined' || PM.delin.lineColors[i] == 'default') ? 
                        PM.delin.lineColor : PM.delin.lineColors[i];
        if (sel == npoints) { 
            stkColor = 'rgb(255,255,127)'; 
        } else if (csel == npoints) {
            stkColor = 'rgba(255,255,127, 0.5)'; 
        }
        PM.delinContext.strokeStyle = stkColor;
        
        lineWidth = (typeof PM.delin.lineWidths[i]=='undefined' || PM.delin.lineWidths[i] == 'default') ? 
                        defaultLineWidth : PM.delin.lineWidths[i];
        PM.delinContext.lineWidth = lineWidth;                
        
        drawBezier(PM.delinContext, bez);
    }
    
    // move delineation points
    $.each(PM.current.tem, function(i, v) {
        PM.pts[i].css({
            top: v.y*PM.temRatio, 
            left: v.x*PM.temRatio
        });
    });
}

function temSVG(lines, points, image) {
    var nlines, i, j, x, y,
        defaultLineWidth,
        bez = [],
        npoints,
        lineWidth,
        stkColor,
        pointSize,
        paths = [],
        url;

    if (PM.pts === undefined || PM.pts.length === 0) return false;
    
    paths.push('<svg width="'+PM.originalWidth+'" height="'+PM.originalHeight+'" xmlns="http://www.w3.org/2000/svg">');
    
    // show image as background
    if (typeof image == "boolean" && image) {
        
    }
    
    defaultLineWidth = $('#defaultLineWidth').val();
    
    // show lines
    if (typeof lines == "boolean" && lines) {
        paths.push('<g id="lines" stroke="' + PM.delin.lineColor + '" stroke-width="' + defaultLineWidth + '" stroke-linecap="round" fill="transparent">');
        
        nlines = PM.current.lines.length;
        for (i = 0; i < nlines; i++) {
            bez = [],
            sel = 0, 
            csel=0,
            npoints = PM.current.lines[i].length;
            
            for (j = 0; j < npoints; j++) {
                x = PM.current.tem[PM.current.lines[i][j]].x;
                y = PM.current.tem[PM.current.lines[i][j]].y;
                
                bez.push([x, y]);
            }
    
            stkColor = (typeof PM.delin.lineColors[i]=='undefined' || PM.delin.lineColors[i] == 'default') ? 
                            undefined : PM.delin.lineColors[i];
    
            lineWidth = (typeof PM.delin.lineWidths[i]=='undefined' || PM.delin.lineWidths[i] == 'default') ? 
                            undefined : PM.delin.lineWidths[i];             
    
            paths.push(svgBezier(bez, lineWidth, stkColor));
        }
        
        paths.push('</g>');
    }
    
    // show points
    if (points) {
        pointSize = 5*defaultLineWidth;
        paths.push('<g id="points" stroke="rgb(0,255,0)" stroke-width="' + defaultLineWidth + '" stroke-linecap="round" fill="transparent">');
        
        npoints = PM.current.tem.length;
        
        for (i = 0; i < npoints; i++) {
            x = PM.current.tem[i].x;
            y = PM.current.tem[i].y;
            
            if (points == 'circle') {
                paths.push('    <circle id="pt' + i + '" name="' + PM.delin.tem[i].name + '" cx="' + x + '" cy="' + y + '" r="' + pointSize + '" />');
            } else if (points == 'numbers') {
                paths.push('    <circle id="pt' + i + '" name="' + PM.delin.tem[i].name + '" cx="' + x + '" cy="' + y + '" r="' + 1 + '" />');
                
            } else {
                paths.push('    <line name="' + PM.delin.tem[i].name + '" x1="' + x + '" y1="' + (y-pointSize) + '" x2="' + x + '" y2="' + (y+pointSize) + '" />');
                paths.push('    <line name="' + PM.delin.tem[i].name + '" x1="' + (x-pointSize) + '" y1="' + y + '" x2="' + (x+pointSize) + '" y2="' + y + '" />');
            }
        }
        
        paths.push('</g>');
        
        if (points == 'numbers') {
            paths.push('<g id="pointnumbers" fill="black" font-size="12" font-family="monospace">');
            for (i = 0; i < npoints; i++) {
                x = PM.current.tem[i].x;
                y = PM.current.tem[i].y;
                paths.push('    <text  id="n' + i + '" name="' + PM.delin.tem[i].name + '" x="' + x + '" y="' + y + '">' + i + '</text>');
            }
            paths.push('</g>');
        }
    }
    
    paths.push('</svg>');
    
    //convert svg source to URI data scheme.
    url = "data:image/svg+xml;charset=utf-8," + encodeURIComponent(paths.join("\r\n"));
    postIt('scripts/temDownload', {
        img: PM.faceimg,
        svg: paths.join("\r\n")
    });
}

function addToCustomMask(i) {
    var $cm = $('#custom_mask_builder');
    var v = $cm.val();
    var lv = v.trim().substr(v.trim().length - 1);
    if (i == ";" || i == ":") {
        if (v.length !== 0) { $cm.val(v + " " + i + " "); }
    } else if (v.length === 0 || lv == "," || lv == ";" || lv == ":") {
        $cm.val(v + i);
    } else {
        $cm.val(v + "," + i);
    }
}

function temPaste() {
    if (PM.pasteBoard.length && PM.pasteBoard[0].hasOwnProperty('x')) {
        $.each(PM.pasteBoard, function(i, v) {
            PM.current.tem[v.n].x = v.x;
            PM.current.tem[v.n].y = v.y;
        });
        
        updateUndoList();
        drawTem();
    } else {
        $('#footer').html('No points were copied');
    }   
}

function editTemplate() {
    
    // get current Tem info
    $('#defaultTemName').val($('#currentTem_name').text());
    $('#defaultTemNotes').text($('#currentTem li[data-id='+PM.delin.temId+']').attr('title'));
    
    // get tem points
    $('select.tempoints').html('');
    $.each(PM.current.tem, function(i, v) {
        var $opt = $('<option />').val(i).html(i);
        $('select.tempoints').append($opt);
    });
    
    // set to current tem fitPoints first
    $('select.tempoints').each(function(i) {
        $(this).val(PM.fitPoints[i]);
    });
    
    // then set to selected points if exactly 3 are selected
    var selpts = getSelPts();
    if (selpts.length == 3) {
        $('select.tempoints').each(function(i) {
            $(this).val(selpts[i]);
        });
    }
    
    $('#addTemDialog').dialog({
        modal: false,
        buttons: {
            Cancel: function() {
                $(this).dialog("close");
            },
            "Delete": {
                text: "Delete",
                click: function() {
                    var $this = $(this);
                    $.ajax({
                        url: 'scripts/temDelete',
                        data: {
                            id: PM.delin.temId,
                        },
                        success: function(data) {
                            if (data.error) {
                                $('#addTemDialog p.warning').html(data.errorText);
                            } else {
                                $this.dialog('close');
                                prefGet(function() {
                                    setCurrentTem(1);
                                });
                            }
                        }
                    });

                }
            },
            "Add/Edit": {
                text: "Edit",
                class: 'ui-state-focus default_button',
                click: function() {
                    var $this = $(this);
                    $.ajax({
                        url: 'scripts/temAdd',
                        data: {
                            id: $('#isNewTem').prop('checked') ? 'NULL' : PM.delin.temId,
                            name: $('#defaultTemName').val(),
                            notes: $('#defaultTemNotes').val(),
                            'public': ($('#defaultTemPublic').prop('checked') ? true : false),
                            delinPts: [$('#defaultTem3Pt1').val(), $('#defaultTem3Pt2').val(), $('#defaultTem3Pt3').val()],
                            tem: PM.current.tem,
                            lines: PM.current.lines,
                            width: PM.originalWidth,
                            height: PM.originalHeight
                        },
                        success: function(data) {
                            if (data.error) {
                                $('#addTemDialog p.warning').html(data.errorText);
                            } else {
                                $this.dialog('close');
                                prefGet(function() {
                                    setCurrentTem(data.tem_id);
                                });
                            }
                        }
                    });
                }
            },
        }
    });
}

function saveTem() { 
    if (PM.project.perm == 'read-only') { 
        growl('This project is read-only', 1000);
        return false; 
    }
    console.log('saveTem()');
    
    var tem = PM.current.tem.length + "\n";
    var resize = PM.originalHeight / $delin.height();
    $.each(PM.current.tem, function(i, v) {
        tem = tem + Math.round(v.x * 10) / 10 + "\t" + Math.round(v.y * 10) / 10 + "\n";
    });
    tem = tem + PM.current.lines.length + "\n";
    $.each(PM.current.lines, function(i, v) {
        tem = tem + "0\n" + v.length + "\n";
        $.each(v, function(i2, v2) {
            tem = tem + v2 + " ";
        });
        tem = tem + "\n";
    });
    tem = tem + (PM.current.lines.length - 1) + "\n";
    $.ajax({
        url: 'scripts/temSave',
        data: {
            name: escape(PM.faceimg),
            tem: tem
        },
        success: function(data) {
            if (data.error === false) {
                //growl(data, 500);
                var now = new Date();
                var theTime = pad(now.getHours(), 2, '0') + ':' + pad(now.getMinutes(), 2, '0') + ':' + pad(now.getSeconds(), 2, '0');
                $('#footer').html(urlToName(PM.faceimg) + ' saved (' + theTime + ')');
                $('#delin_save').removeClass('unsaved');
            } else {
                growl(data.errorText);
            }
        }
    });
}

function checkSaveFirst(otherwise) {
    if ($('#delin_save.unsaved').length && PM.project.perm != 'read-only') {
        $('<div />').dialog({
            title: "Save changed template?",
            buttons: {
                Cancel: {
                    text: "Don't Save",
                    click: function() {
                        $(this).dialog("close");
                        otherwise();
                    }
                },
                "Save": {
                    text: 'Save',
                    class: 'ui-state-focus',
                    click: function() {
                        $(this).dialog("close");
                        $('#save').click();
                        otherwise();
                    }
                },
                
            }
        });
    } else {
        otherwise();
    }
}

function removeTemPoints(ptArray) { console.log('removeTemPoints(' + ptArray.join() + ')');
    ptArray.sort(sortNumber).reverse();
    var tem_map = [];
    var newi = 0;
    $.each(PM.current.tem, function(i, v) {
        if ($.inArray(i, ptArray) > -1) {
            tem_map[i] = 'removed';
        } else {
            tem_map[i] = newi;
            newi++;
        }
    });
    // remove all tem points so they are re-generated at the end
    $('.pt').remove();
    $.each(ptArray, function(i, v) {
        PM.current.tem.splice(v, 1);
    });
    $.each(PM.current.tem, function(i, v) {
        PM.current.tem[i].i = i;
    });
    // remove points from lines and remap
    var ln = PM.current.lines.length;
    for (var i = ln - 1; i >= 0; i--) {
        var line = PM.current.lines[i];
        var n = line.length;
        for (var j = n - 1; j >= 0; j--) {
            if (tem_map[line[j]] == 'removed') {
                PM.current.lines[i].splice(j, 1);
            } else {
                PM.current.lines[i][j] = tem_map[line[j]];
            }
        }
        if (PM.current.lines[i].length < 2) {
            PM.current.lines.splice(i, 1);
        }
    }
    updateUndoList();
    makePoints(PM.current.tem);
    drawTem();
}

function nextSymPt(i) {
    var n = PM.symPts.n;
    if (i !== 'start') {
        PM.symPts.order.push(n); // add to order for undo
        PM.symPts.sym[n] = i;
        PM.symPts.sym[i] = n;
        while (PM.symPts.sym[PM.symPts.n] !== undefined) {
            PM.symPts.n++;
        }
        n = PM.symPts.n;
    }
    if (n >= PM.current.tem.length) {
        PM.delinfunc = 'move';
        $('#pointer').fadeOut().css({left: '-100px', top: '-100px'});
        
        $.ajax({
            url: 'scripts/temSetSym',
            data: {
                tem_id: PM.delin.temId,
                sym: PM.symPts.sym
            },
            success: function(data) {
                if (data.error) {
                    $('<div title="Error recording symmetry points" />').html(data.errorText).dialog();
                } else {
                    growl('Symmetry points recorded.', 1500);
                }
            }
        });
    }
    // unselect all points first
    $.each(PM.selectedPts, function(idx, val) {
        if (val) {
            var pt = PM.stage.get('#d' + idx)[0];
            pt.selected = false;
            pt.setImage(PM.cross);
        }
    });
    PM.selectedPts = [];
    var pt = PM.stage.get('#d' + n)[0];
    if (pt !== undefined) {
        PM.selectedPts[n] = true;
        pt.selected = true;
        pt.setImage(PM.hover_cross);

        var imgoffset = $delin.offset();
        $('#pointer').css({
            left: pt.getX() + imgoffset.left - 7 - $('#pointer').width(),
            top: pt.getY() + imgoffset.top + 1 - $('#pointer').height()/2
        }).show();
    }
    PM.temLayer.draw();
}

function boxHover(e) {
    var $s = $('#selectBox');
    var x = $s.prop('x');
    var y = $s.prop('y');
    var t = Math.min(y, e.pageY);
    var l = Math.min(x, e.pageX);
    var w = Math.abs(x - e.pageX);
    var h = Math.abs(y - e.pageY);
    $s.css({
        top: t + 'px',
        left: l + 'px',
        width: w + 'px',
        height: h + 'px'
    }).show();
    var imgoffset = $delin.offset();
    var mousedown = {
        x: x - imgoffset.left,
        y: y - imgoffset.top
    };
    var mouseup = {
        x: (e.pageX - imgoffset.left),
        y: (e.pageY - imgoffset.top)
    };
    $.each(PM.current.tem, function(i, v) {
        if (PM.stage.get('#d' + i).length) {
            var pt = PM.stage.get('#d' + i)[0];
            var ptX = pt.getX();
            var ptY = pt.getY();
            if (!pt.selected) {
                if (ptX >= Math.min(mousedown.x, mouseup.x) && ptX <= Math.max(mousedown.x, mouseup.x) && ptY >= Math.min(mousedown.y, mouseup.y) && ptY <= Math.max(mousedown.y, mouseup.y)) {
                    pt.setImage(PM.hover_cross);
                } else {
                    pt.setImage(PM.cross);
                }
            }
        }
    });
    PM.temLayer.draw();
}

function boxSelect(e) {
    var imgoffset = $delin.offset();
    var mousedown = {
        x: $('#selectBox').prop('x') - imgoffset.left,
        y: $('#selectBox').prop('y') - imgoffset.top
    };
    var mouseup = {
        x: (e.pageX - imgoffset.left),
        y: (e.pageY - imgoffset.top)
    };
    $.each(PM.current.tem, function(i, v) {
        var ptX = v.x * PM.temRatio;
        var ptY = v.y * PM.temRatio;
        if (ptX >= Math.min(mousedown.x, mouseup.x)) {
            if (ptX <= Math.max(mousedown.x, mouseup.x)) {
                if (ptY >= Math.min(mousedown.y, mouseup.y)) {
                    if (ptY <= Math.max(mousedown.y, mouseup.y)) {
                        PM.pts[i].addClass('selected');
                    }
                }
            }
        }
    });
    resetSelectBox();
    drawTem();
}

function resetSelectBox() {
    $('#selectBox').prop({
        x: false,
        y: false
    }).css({
        top: '0px',
        left: '0px',
        width: '0px',
        height: '0px'
    }).hide();
}

function setCurrentTem(temId) { console.log('setCurrentTem('+temId+')');
    $.ajax({
        url: 'scripts/temGet',
        type: 'POST',
        async: false,
        data: {
            tem_id: temId
        },
        success: function(data) {
            PM.delin.temId = temId;
            PM.delin.tem = data.defaultTem;
            PM.delin.lines = data.defaultLines;
            PM.fitPoints = data.fitPoints;
            PM.delin.lineColors = data.lineColors;
            $('#currentTem li span.checkmark').hide();
            $('#currentTem li[data-id=' + temId + '] span.checkmark').show();
            $('#currentTem_name').text(data.name);
            
            if (PM.interfaceWindow == 'delineate') {
                // check if new tem is compatible
                //if (PM.delin.tem.length != PM.current.tem.length || PM.delin.lines.length != PM.current.lines.length) {
                if (PM.current.tem.length === 0) {
                    PM.current.tem = data.defaultTem;
                    PM.current.lines = data.defaultLines;
                    if (temId !== 13) $('#fitTemplate').click();
                }
            }
        }
    });
}

function clickPt(pt) {
    var ptname = typeof PM.delin.tem[PM.fitPoints[pt]].name === "undefined" ? 
                                    PM.fitPoints[pt] : PM.delin.tem[PM.fitPoints[pt]].name;
    var n = pt + 1;
    var ordinal = {1: 'st', 2: 'nd', 3: 'rd', 4: 'th'};
    
    quickhelp('Click ' + n + '<sup>' + ordinal[n] + '</sup> Point (' + ptname + ')');
}

function setPointLabels() {
    var ptLabels = [];
    PM.delinfunc = 'label';
    
    // create labels for each point in the default tem, add current name
    var $ol = $('#labelDialog ol');
    $ol.html('');
    $.each(PM.delin.tem, function (i) {
        $ol.append('<li><input name="' + i + '" type="text" value="' + PM.delin.tem[i].name + '" /></li>');
    });
    
    $('#labelDialog').dialog({
        title: "Set Point Labels",
        modal: false,
        height: 500,
        position: { my: 'right top', at: 'right bottom', of: $('#delin_toolbar') },
        buttons: {
            Cancel: function() { $(this).dialog('close'); },
            "Save": function() {
                $('#pointer').fadeOut().css({left: '-100px', top: '-100px'});
            
                $('#labelDialog input').each( function() {
                    var i = parseInt($(this).attr('name'));
                    var name = $(this).val();
                    ptLabels[i] = name;
                });
                $.ajax({
                    url: 'scripts/temSetLabels',
                    data: {
                        tem_id: PM.delin.temId,
                        labels: ptLabels
                    },
                    success: function(data) {
                        if (data.error) {
                            $('<div title="Error recording point labels" />').html(data.errorText).dialog();
                        } else {
                            quickhelp('Point labels recorded.', 3000);
                            //update template labels in defaultTem
                            $.each(ptLabels, function(i, name) {
                                PM.delin.tem[i].name = name;
                            });
                        }
                        $('#labelDialog').dialog('close');
                    }
                });
            }
        },
        close: function() {
            $('#refresh').click();
        }
    }).find('p').html('Labels for template <code>' + $('#currentTem_name').text() + '</code>');
}

/*

SKY Biometry Photo Tags

{
    "status":"success",
    "photos":[{
        "url":"http://webmorph.org/scripts/skyBioAccess?file=/1/lisa.jpg",
        "pid":"F@02e5e0675e51462f6c37bf214fb0c7f5_35271850a738e",
        "width":640,"height":480,
        "tags":[{
            "uids":[],
            "label":null,
            "confirmed":false,
            "manual":false,
            "width":25.16,
            "height":33.54,
            "yaw":-16,
            "roll":2,
            "pitch":0,
            "attributes":{
                "face":{"value":"true","confidence":69}
            },
            "points":[
                {"x":49.84,"y":40,"confidence":50,"id":768},
                {"x":49.84,"y":47.5,"confidence":50,"id":769},
                {"x":50.62,"y":53.75,"confidence":50,"id":770},
                {"x":51.25,"y":59.38,"confidence":50,"id":771},
                {"x":52.81,"y":64.38,"confidence":50,"id":772},
                {"x":55.16,"y":68.33,"confidence":50,"id":773},
                {"x":58.75,"y":71.88,"confidence":50,"id":774},
                {"x":63.12,"y":73.12,"confidence":50,"id":775},
                {"x":67.19,"y":72.29,"confidence":50,"id":776},
                {"x":69.84,"y":69.38,"confidence":50,"id":777},
                {"x":72.03,"y":65.42,"confidence":50,"id":778},
                {"x":73.44,"y":61.25,"confidence":50,"id":779},
                {"x":74.69,"y":54.17,"confidence":50,"id":780},
                {"x":75.31,"y":47.71,"confidence":50,"id":781},
                {"x":75.31,"y":40.83,"confidence":50,"id":782},
                {"x":73.75,"y":36.67,"confidence":50,"id":783},
                {"x":71.25,"y":33.96,"confidence":50,"id":784},
                {"x":68.75,"y":33.96,"confidence":50,"id":785},
                {"x":66.25,"y":36.04,"confidence":50,"id":786},
                {"x":68.75,"y":36.04,"confidence":50,"id":787},
                {"x":71.25,"y":36.04,"confidence":50,"id":788},
                {"x":52.03,"y":36.67,"confidence":50,"id":789},
                {"x":54.53,"y":33.75,"confidence":50,"id":790},
                {"x":57.5,"y":33.54,"confidence":50,"id":791},
                {"x":60.31,"y":35.83,"confidence":50,"id":792},
                {"x":57.34,"y":35.62,"confidence":50,"id":793},
                {"x":54.69,"y":35.83,"confidence":50,"id":794},
                {"x":54.22,"y":40.21,"confidence":50,"id":795},
                {"x":56.88,"y":38.33,"confidence":50,"id":796},
                {"x":59.22,"y":40.42,"confidence":50,"id":797},
                {"x":56.72,"y":41.46,"confidence":50,"id":798},
                {"x":56.88,"y":39.79,"confidence":50,"id":799},
                {"x":72.34,"y":40.62,"confidence":50,"id":800},
                {"x":70.16,"y":38.54,"confidence":50,"id":801},
                {"x":67.66,"y":40.83,"confidence":50,"id":802},{"x":70.31,"y":41.88,"confidence":50,"id":803},{"x":70.31,"y":40.21,"confidence":50,"id":804},{"x":61.88,"y":40.62,"confidence":50,"id":805},
                {"x":61.25,"y":47.29,"confidence":50,"id":806},{"x":59.22,"y":51.04,"confidence":50,"id":807},{"x":60.47,"y":53.12,"confidence":50,"id":808},{"x":63.75,"y":53.96,"confidence":50,"id":809},
                {"x":66.56,"y":53.33,"confidence":50,"id":810},{"x":67.66,"y":51.46,"confidence":50,"id":811},{"x":65.47,"y":46.88,"confidence":50,"id":812},{"x":65,"y":40.42,"confidence":50,"id":813},
                {"x":61.56,"y":52.29,"confidence":50,"id":814},{"x":65.78,"y":52.29,"confidence":50,"id":815},{"x":58.44,"y":60.62,"confidence":50,"id":816},{"x":60.47,"y":58.54,"confidence":50,"id":817},
                {"x":62.5,"y":57.71,"confidence":50,"id":818},{"x":63.91,"y":58.12,"confidence":50,"id":819},{"x":64.84,"y":57.71,"confidence":50,"id":820},{"x":66.56,"y":58.54,"confidence":50,"id":821},
                {"x":68.12,"y":60.62,"confidence":50,"id":822},{"x":67.03,"y":62.71,"confidence":50,"id":823},{"x":65.62,"y":63.54,"confidence":50,"id":824},{"x":63.75,"y":63.96,"confidence":50,"id":825},
                {"x":61.56,"y":63.54,"confidence":50,"id":826},{"x":59.84,"y":62.5,"confidence":50,"id":827},{"x":61.41,"y":61.04,"confidence":50,"id":828},{"x":63.75,"y":61.25,"confidence":50,"id":829},
                {"x":65.94,"y":60.83,"confidence":50,"id":830},
                {"x":65.78,"y":60,"confidence":50,"id":831},
                {"x":63.75,"y":60,"confidence":50,"id":832},
                {"x":61.56,"y":60,"confidence":50,"id":833},
                {"x":63.75,"y":60.62,"confidence":50,"id":834},
                {"x":63.75,"y":50.21,"confidence":50,"id":835}
            ],
            "similarities":null,
            "tid":"TEMP_F@02e5e0675e51462f6c37bf21018500e9_35271850a738e_60.78_48.54_0_1",
            "recognizable":true,
            "center":{"x":60.78,"y":48.54},
            "eye_left":{"x":70.31,"y":40.21,"confidence":50,"id":449},
            "eye_right":{"x":56.88,"y":39.79,"confidence":50,"id":450},
            "mouth_center":{"x":63.75,"y":60.62,"confidence":50,"id":615},
            "nose":{"x":63.75,"y":50.21,"confidence":50,"id":403}
        }]
    }],
    "usage":{
        "used":14,
        "remaining":86,
        "limit":100,
        "reset_time":1405700111,
        "reset_time_text":"Fri, 18 July 2014 16:15:11 +0000"
    },
    "operation_id":"9c533182831349d98db6191acb2ba3bc"
}

*/

function FCClientJS(apiKey, apiSecret) {
    var _server = "http://api.skybiometry.com/fc/";
    var _format = "json";

    var _apiKey = null;
    var _apiSecret = null;

    if (isDefined(apiKey))
        _apiKey = apiKey;
    if (isDefined(apiSecret))
        _apiSecret = apiSecret;

    // Public methods

    this.facesDetect = function (urls, files, options, callback) {
        var method = "faces/detect";
        var params = {};

        if (isDefined(urls)) {
            params.urls = urls;
        }

        if (isDefined(options)) {
            if (isDefined(options.detector) && !isEmpty(options.detector)) params.detector = options.detector;
            if (isDefined(options.attributes) && !isEmpty(options.attributes)) params.attributes = options.attributes;
            if (isDefined(options.detect_all_feature_points) && !isEmpty(options.detect_all_feature_points)) params.detect_all_feature_points = options.detect_all_feature_points;
        }

        CallMethod(method, files, params, callback);
        return true;
    };

    this.facesGroup = function (userIds, urls, files, options, callback) {
        var method = "faces/group";
        var params = { uids: userIds };

        if (isDefined(urls)) {
            params.urls = urls;
        }

        if (isDefined(options)) {
            if (isDefined(options.namespace) && !isEmpty(options.namespace)) params.namespace = options.namespace;
            if (isDefined(options.detector) && !isEmpty(options.detector)) params.detector = options.detector;
            if (isDefined(options.attributes) && !isEmpty(options.attributes)) params.attributes = options.attributes;
            if (isDefined(options.threshold) && !isEmpty(options.threshold)) params.threshold = options.threshold;
            if (isDefined(options.limit) && !isEmpty(options.limit)) params.limit = options.limit;
            if (isDefined(options.returnSimilarities) && !isEmpty(options.returnSimilarities)) params.returnSimilarities = options.returnSimilarities;
            if (isDefined(options.detect_all_feature_points) && !isEmpty(options.detect_all_feature_points)) params.detect_all_feature_points = options.detect_all_feature_points;
        }

        CallMethod(method, files, params, callback);
        return true;
    };

    this.facesRecognize = function (userIds, urls, files, options, callback) {
        var method = "faces/recognize";
        var params = { uids: userIds };

        if (isDefined(urls))
            params.urls = urls;

        if (isDefined(options)) {
            if (isDefined(options.namespace) && !isEmpty(options.namespace)) params.namespace = options.namespace;
            if (isDefined(options.detector) && !isEmpty(options.detector)) params.detector = options.detector;
            if (isDefined(options.attributes) && !isEmpty(options.attributes)) params.attributes = options.attributes;
            if (isDefined(options.limit) && !isEmpty(options.limit)) params.limit = options.limit;
            if (isDefined(options.detect_all_feature_points) && !isEmpty(options.detect_all_feature_points)) params.detect_all_feature_points = options.detect_all_feature_points;
        }

        CallMethod(method, files, params, callback);
        return true;
    };

    this.facesTrain = function (userIds, options, callback) {
        var method = "faces/train";
        var params = { uids: userIds };

        if (isDefined(options)) {
            if (isDefined(options.namespace) && !isEmpty(options.namespace)) params.namespace = options.namespace;
        }

        CallMethod(method, null, params, callback);
        return true;
    };

    this.facesStatus = function (userIds, options, callback) {
        var method = "faces/status";
        var params = { uids: userIds };

        if (isDefined(options)) {
            if (isDefined(options.namespace) && !isEmpty(options.namespace)) params.namespace = options.namespace;
        }

        CallMethod(method, null, params, callback);
        return true;
    };

    this.tagsAdd = function (userId, url, x, y, width, height, options, callback) {
        var method = "tags/add";
        
        var params = {
            url: url,
            uid: userId,
            x: x,
            y: y,
            width: width,
            height:height
        };

        if (isDefined(options)) {
            if (isDefined(options.label) && !isEmpty(options.label)) params.label = options.label;
            if (isDefined(options.password) && !isEmpty(options.password)) params.password = options.password;
        }

        CallMethod(method, null, params, callback);
        return true;
    };


    this.tagsSave = function (tagIds, userId, options, callback) {
        var method = "tags/save";
        var params = { tids: tagIds, uid: userId };

        if (isDefined(options)) {
            if (isDefined(options.namespace) && !isEmpty(options.namespace)) params.namespace = options.namespace;
            if (isDefined(options.label) && !isEmpty(options.label)) params.label = options.label;
            if (isDefined(options.password) && !isEmpty(options.password)) params.password = options.password;
        }

        CallMethod(method, null, params, callback);
        return true;
    };

    this.tagsRemove = function (tagIds, options, callback) {
        var method = "tags/remove";
        var params = { tids: tagIds };

        if (isDefined(options)) {
            if (isDefined(options.password) && !isEmpty(options.password)) params.password = options.password;
        }

        CallMethod(method, null, params, callback);
        return true;
    };

    this.tagsGet = function (userIds, urls, photoIds, options, callback) {
        var method = "tags/get";
        var params = { uids: userIds, urls: urls, pids: photoIds };

        if (isDefined(options)) {
            if (isDefined(options.order) && !isEmpty(options.order)) params.order = options.order;
            if (isDefined(options.limit) && !isEmpty(options.limit)) params.limit = options.limit;
            if (isDefined(options.together) && !isEmpty(options.together)) params.together = options.together;
            if (isDefined(options.filter) && !isEmpty(options.filter)) params.filter = options.filter;
            if (isDefined(options.namespace) && !isEmpty(options.namespace)) params.namespace = options.namespace;
        }

        CallMethod(method, null, params, callback);
        return true;
    };

    this.accountAuthenticate = function (options, callback) {
        var method = "account/authenticate";
        var params = {};

        CallMethod(method, null, params, callback);
        return true;
    };

    this.accountUsers = function (namespaces, options, callback) {
        var method = "account/users";
        var params = {namespaces: namespaces};

        CallMethod(method, null, params, callback);
        return true;
    };

    this.accountNamespaces = function (options, callback) {
        var method = "account/namespaces";
        var params = {};

        CallMethod(method, null, params, callback);
        return true;
    };

    this.accountLimits = function (options, callback) {
        var method = "account/limits";
        var params = { };

        CallMethod(method, null, params, callback);
        return true;
    };

    this.getServer = function () {
        return _server;
    }

    this.setServer = function (server) {
        _server = server;
    }
    
    // Private methods

    function isDefined(s) { return (typeof s != "undefined" && s != undefined); }
    function isEmpty(s) { return (!isDefined(s) || s == null || s == ''); }

    function GetXmlHttpRequest()
    {
        var xmlhttp=false;
        /*@cc_on @*/
        /*@if (@_jscript_version >= 5)
        // JScript gives us Conditional compilation, we can cope with old IE versions.
        // and security blocked creation of the objects.
         try {
          xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
         } catch (e) {
          try {
           xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
          } catch (E) {
           xmlhttp = false;
          }
         }
        @end @*/
        if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
            try {
                xmlhttp = new XMLHttpRequest();
            } catch (e) {
                xmlhttp=false;
            }
        }
        if (!xmlhttp && window.createRequest) {
            xmlhttp = window.createRequest();
        }
        return xmlhttp;
    }

    function CallMethod(method, files, params, callback) {
        var url = _server + method + "." + _format;

        if (!isDefined(files)) {
            url += "?api_key=" + encodeURIComponent(_apiKey);
            if (isDefined(_apiSecret) && !isEmpty(_apiSecret)) {
                url += "&api_secret=" + encodeURIComponent(_apiSecret);
            }

            if (params != null) {
                for (param in params)
                    url += "&" + param + "=" + encodeURIComponent(params[param]);
            }

            var request = Math.round(Math.random() * 10000000);
            var callbackName = "jsonp" + request;
            var responceId = "fcClientJsResponse" + request;
            window[callbackName] = function (data) {
                document.getElementById(responceId).parentNode.removeChild(document.getElementById(responceId));
                if (typeof callback == "function") {
                    callback(data);
                }
            };
            url += "&callback=" + callbackName + "&" + request;

            var script = document.createElement("script");
            script.setAttribute("src", url);
            script.setAttribute("type", "text/javascript");
            script.setAttribute("id", responceId);
            document.body.appendChild(script);
        } else {
            var xhr = GetXmlHttpRequest();
            xhr.open("POST", url, true);
 
            if (params == null) params = { };
            params.api_key = _apiKey;
            if (isDefined(_apiSecret)) params.api_secret = _apiSecret;
            
            if (typeof FormData == 'undefined')
                throw "Only FormData is supported";
                
            var body = new FormData();
            for (param in params) {
                body.append(param, params[param]);
            }

            for (file in files) {
                body.append(file, files[file]);
            }

            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4) {
                    if (xhr.status == 200 || xhr.status == 400) {
                        if (typeof callback == "function") {
                            callback(xhr.responseText);
                        }
                    }
                    else throw "Invalid status returned from API server";
                }
            }

            xhr.send(body);
        }
    }
}


function skyBio(img) {
    var client = new FCClientJS('1ce0bae654f2488ea88f4aad0f41a03e', '017d004a370d414d968bcdbd606fa51c');
    
    var options = {
        'detect_all_feature_points': true,
        'detector': 'aggressive', // 'aggressive' or 'normal'
    };
    
    if (img.substr(0,7) !== 'http://') {
        img = 'http://psychomorph.facelab.org/scripts/skyBioAccess?file=/' + PM.project + img;
    }
    
    client.facesDetect(img, null, options, function(data) {
        console.log(JSON.stringify(data.usage));
    
        var photo = data.photos[0];
        
        if (!photo) {
            growl("No image found", 2000);
            return;
        } else if (photo.error_message) {
            growl(photo.error_message);
            return;
        }
        
        var imgWidth = photo.width;
        var imgHeight = photo.height;
        
        var tags = photo.tags[0];
        
        // clear 3-pt delin
        PM.eyeClicks = [0,0,0];
        PM.delinfunc = 'move';
        $('#pointer, #leftEye, #rightEye, #mouth').hide();
        
        PM.current.tem = [];
        setCurrentTem(13);
        
        /*
        // these points are duplicated in the points array
        PM.current.tem[0].x = tags.eye_left.x * imgWidth / 100;
        PM.current.tem[0].y = tags.eye_left.y * imgHeight / 100;
        PM.current.tem[1].x = tags.eye_right.x * imgWidth / 100;
        PM.current.tem[1].y = tags.eye_right.y * imgHeight / 100;
        PM.current.tem[2].x = tags.mouth_center.x * imgWidth / 100;
        PM.current.tem[2].y = tags.mouth_center.y * imgHeight / 100;
        PM.current.tem[3].x = tags.nose.x * imgWidth / 100;
        PM.current.tem[3].y = tags.nose.y * imgHeight / 100;
        */

        $.each(tags.points, function(i, p) {
            PM.current.tem[i].x = p.x * imgWidth / 100;
            PM.current.tem[i].y = p.y * imgHeight / 100;
        });
        
        drawTem();
    });
}

//====================================
// !DELINEATE FUNCTIONS
//====================================

function newDelinPoint(e) {
    var imgoffset = $delin.offset();
    
    var x = (e.pageX - imgoffset.left)/PM.temRatio;
    var y = (e.pageY - imgoffset.top)/PM.temRatio;
    var i = PM.current_tem.length;
    PM.current_tem.push({
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
    if (PM.default_tem.length != PM.current_tem.length) {
        growl('The current template does not match the template <code>' 
              + $('#current_tem_name').text() + '</code>');
        return true;
    }
    
    $.ajax({
        url: '/scripts/userCheckAccess',
        data: { table: 'tem', id: PM.default_tem_id },
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
                            + $('#current_tem_name').text(),
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
    
    if (PM.eye_clicks.length === 0) {
        PM.eye_clicks[0] = {
            x: (e.pageX - imgoffset.left)/PM.temRatio,
            y: (e.pageY - imgoffset.top)/PM.temRatio
        };
        thePt = $('#leftEye');
        thePt.css('left', e.pageX - (thePt.width() / 2))
             .css('top', e.pageY - (thePt.height() / 2)).show();
        
        clickPt(1);
    } else if (PM.eye_clicks.length == 1) {
        PM.eye_clicks[1] = {
            x: (e.pageX - imgoffset.left)/PM.temRatio,
            y: (e.pageY - imgoffset.top)/PM.temRatio
        };
        thePt = $('#rightEye');
        thePt.css('left', e.pageX - (thePt.width() / 2))
             .css('top', e.pageY - (thePt.height() / 2)).show();
        
        clickPt(2);
    } else if (PM.eye_clicks.length == 2) {
        PM.eye_clicks[2] = {
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
                x: PM.current_tem[newfitPoints[0]].x,
                y: PM.current_tem[newfitPoints[0]].y
            },
            1: {
                x: PM.current_tem[newfitPoints[1]].x,
                y: PM.current_tem[newfitPoints[1]].y
            },
            2: {
                x: PM.current_tem[newfitPoints[2]].x,
                y: PM.current_tem[newfitPoints[2]].y
            }
        };
        $.ajax({
            async: false,
            url: 'scripts/temFit',
            data: {
                'eyeclicks': PM.eye_clicks,
                'temPoints': temPoints
            },
            success: function(data) {
                $.each(PM.current_tem, function(i, v) {
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
                    PM.current_tem[i].x = newx;
                    PM.current_tem[i].y = newy;
                });
                makePoints(PM.current_tem);
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
                PM.eye_clicks = [];
                PM.stage.clear();
                PM.stage.hide();
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
                
                PM.current_tem = [];
                $.each(data.temPoints, function(i, v) {
                    PM.current_tem[i] = { x: v[0], y: v[1] };
                });
                PM.undo_tem = [$.extend(true, [], PM.current_tem)];
                PM.undo_level = 0;
                PM.current_lines = data.lineVectors;
                PM.undo_lines = [$.extend(true, [], PM.current_lines)];
            } else {
                PM.delinfunc = '3pt';
                PM.current_tem = [];
                $.each(PM.default_tem, function(i, v) {
                    PM.current_tem[i] = { x: v.x, y: v.y };
                });
                PM.undo_tem = [$.extend(true, [], PM.current_tem)];
                PM.current_lines = $.extend(true, [], PM.default_lines);
                PM.undo_lines = [$.extend(true, [], PM.current_lines)];
                PM.undo_level = 0;
            }
            
            if (PM.interface != 'delineate') $('#showDelineate').click();
            
            if (PM.delinfunc == '3pt') {
                PM.eye_clicks = [];
                $('#template').hide();
                cursor('crosshair');
                clickPt(0);
            } else if (PM.showTem) {
                PM.eye_clicks = [PM.fitPoints[0], PM.fitPoints[1], PM.fitPoints[2]];
                $('#template').show();
                $('#imgsize').change();
                makePoints(PM.current_tem);
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
            
            PM.current_tem[selpt].x += xchange/PM.temRatio;
            PM.current_tem[selpt].y += ychange/PM.temRatio;
        });
        updateUndoList();
        drawTem();
    }
}

function temSizeChange(pcnt) {
    if (PM.interface == 'delineate' && PM.delinfunc == 'move') {
        var pcntChange = (100 + pcnt)/100;
        var x=0,y=0,n=0;
        
        var selpts = getSelPts();
        
        $.each(selpts, function(i, selpt) {
            x += PM.current_tem[selpt].x;
            y += PM.current_tem[selpt].y;
            n++;
        });
        
        var offset = {
            x: (x/n) - (pcntChange*x/n), 
            y: (y/n) - (pcntChange*y/n)
        };
    
        $.each(selpts, function(i, selpt) {
            PM.current_tem[selpt].x *= pcntChange;
            PM.current_tem[selpt].y *= pcntChange;
            PM.current_tem[selpt].x += offset.x;
            PM.current_tem[selpt].y += offset.y;
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
    t = 0.3;
    var d01 = Math.sqrt(Math.pow(x1 - x0, 2) + Math.pow(y1 - y0, 2));
    var d12 = Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2));
    var fa = t * d01 / (d01 + d12); // scaling factor for triangle Ta
    var fb = t * d12 / (d01 + d12); // ditto for Tb, simplifies to fb=t-fa
    var p1x = x1 - fa * (x2 - x0); // x2-x0 is the width of triangle T
    var p1y = y1 - fa * (y2 - y0); // y2-y0 is the height of T
    var p2x = x1 + fb * (x2 - x0);
    var p2y = y1 + fb * (y2 - y0);
    return [p1x, p1y, p2x, p2y];
}

function drawBezier(ctx, v, begin) {
    begin = begin | true;
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
    var pts = [];
    for (var j = 0; j < v.length; j++) {
        pts.push(v[j][0]);
        pts.push(v[j][1]);
    }

    var cp = []; // array of control points, as x0,y0,x1,y1,...
    var n = pts.length;
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

function updateUndoList() {  //console.log('updateUndoList()');
    var last_tem = PM.undo_tem[PM.undo_level];
    var last_lines = PM.undo_lines[PM.undo_level];
    var changed = false;
    // check if new tem is changed from last tem
    $.each(PM.current_tem, function(i, v) {
        if (last_tem[i] === undefined || (PM.current_tem[i].x != last_tem[i].x || PM.current_tem[i].y != last_tem[i].y)) {
            changed = true;
            return false;
        }
    });
    if (!changed) {
        // check if lines are changed (only check if points not changed)
        $.each(PM.current_lines, function(i, line) {
            $.each(line, function(j, pt) {
                if (last_lines[i] === undefined || last_lines[i][j] === undefined || PM.current_lines[i][j] != last_lines[i][j]) {
                    changed = true;
                    return false;
                }
            });
        });
    }
    // add to undo list if changed
    if (changed) {
        PM.undo_level++;
        //console.log('Changed: PM.undo_level = ' + PM.undo_level);
        PM.undo_tem[PM.undo_level] = $.extend(true, [], PM.current_tem);
        PM.undo_lines[PM.undo_level] = $.extend(true, [], PM.current_lines);
        $('#delin_save').addClass('unsaved');
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
    var $pt = $('<div class="pt" n="' + i + '" />').appendTo('#delin');
    PM.pts[i] = $pt;
    
    // set connected points and lines
    var connectedPoints = [];
    var connectedLines = [];
    $.each(PM.current_lines, function (line, linearray) {
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
                x: (ui.position.left/PM.temRatio) - PM.current_tem[i].x,
                y: (ui.position.top/PM.temRatio) - PM.current_tem[i].y
            };
            
            PM.current_tem[i].x = ui.position.left/PM.temRatio;
            PM.current_tem[i].y = ui.position.top/PM.temRatio;
            
            $('#footer .x').text(round(PM.current_tem[i].x, 1));
            $('#footer .y').text(round(PM.current_tem[i].y, 1));
            
            $.each(PM.current_tem, function(j,pt) {
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
    blur = blur | 10;
    var $masked_img = $("<canvas width='" + PM.originalWidth + "' height='" + PM.originalHeight + "' />");
    var ctx = $masked_img.get(0).getContext('2d');
    
    ctx.fillStyle = 'rgb(255,255,255)';
    var blurtrans = (blur===0) ? 0.5 : (0.5/blur);
    ctx.strokeStyle = "rgba(255,255,255,"+ blurtrans +")";
    ctx.fillRect (0, 0, $('#template').width(), $('#template').height());
    ctx.globalCompositeOperation = "destination-out"; // cut-out mode
    
    var m = masks.split(":");
    
    // draw blur lines first
    for (i=blur; i>0; i--) {
        ctx.lineWidth = i;
        $.each(m, function(i, mask) {
            var lines = mask.split(';');
            
            ctx.beginPath();
            $.each(lines, function(j, line) {
                var pts = line.split(',');
                var bez = [];
                $.each(pts, function(k, p) {
                    var pt = parseInt(p.trim());
                    var x = PM.current_tem[pt].x;
                    var y = PM.current_tem[pt].y;
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
                var x = PM.current_tem[pt].x;
                var y = PM.current_tem[pt].y;
                bez.push([x, y]);
            });
            drawBezier(ctx, bez, false);
        });
        ctx.closePath();
        ctx.fill();
    });
    
    ctx.globalCompositeOperation = "destination-over"; // add face underneath mask
    var image = new Image();
    image.src = fileAccess(PM.faceimg);
    ctx.drawImage(image, 0, 0, PM.originalWidth, PM.originalHeight, 
                         0, 0, PM.originalWidth, PM.originalHeight);

    return $masked_img;
}

function drawTem() {
    
    PM.dcontext.clearRect (0, 0, $('#template').width(), $('#template').height());
    
    if (PM.pts === undefined || PM.pts.length === 0) return false;
    
    var nlines = PM.current_lines.length;
    var default_line_width = $('#default_line_width').val();
    
    for (var i = 0; i < nlines; i++) {
        var bez = [];
        var npoints = PM.current_lines[i].length;
        var sel = 0, csel=0;
        for (var j = 0; j < npoints; j++) {
            var x = PM.current_tem[PM.current_lines[i][j]].x*PM.temRatio;
            var y = PM.current_tem[PM.current_lines[i][j]].y*PM.temRatio;
            bez.push([x, y]);
            
            if (PM.pts[PM.current_lines[i][j]].hasClass('selected')) { sel++; }
            if (PM.pts[PM.current_lines[i][j]].hasClass('couldselect')) { csel++; }
        }
        
        var stkColor = (typeof PM.line_colors[i]=='undefined' || PM.line_colors[i] == 'default') ? 
                        PM.default_line_color : PM.line_colors[i];
        if (sel == npoints) { 
            stkColor = 'rgb(255,255,127)'; 
        } else if (csel == npoints) {
            stkColor = 'rgba(255,255,127, 0.5)'; 
        }
        PM.dcontext.strokeStyle = stkColor;
        
        var lineWidth = (typeof PM.line_widths[i]=='undefined' || PM.line_widths[i] == 'default') ? 
                        default_line_width : PM.line_widths[i];
        PM.dcontext.lineWidth = lineWidth;                
        
        drawBezier(PM.dcontext, bez);
    }
    
    // move delineation points
    $.each(PM.current_tem, function(i, v) {
        PM.pts[i].css({
            top: v.y*PM.temRatio, 
            left: v.x*PM.temRatio
        });
    });
}


function drawTemOld() { console.time('drawTem()');
    // make lines
    var t = 0.3;
    var $lc = $('#prefDialog .line_color');
    var i;

    $.each(PM.current_lines, function(i) {
        var ln, n, j;
        var stkColor = (typeof PM.line_colors[i]=='undefined' || PM.line_colors[i] == 'default') ? 
                        PM.default_line_color : PM.line_colors[i];
        if (PM.stage.get('#l' + i).length === 0) {
            ln = new Kinetic.Shape({
                drawFunc: function(canvas) {
                    var v = PM.current_lines[i];
                    var context = canvas.getContext('2d');
                    context.beginPath();
                    
                    if (v === undefined) {
                        // something is wrong
                        
                    } else if (v.length == 2) {
                        // connect with straight line
                        context.moveTo(PM.current_tem[v[0]].x*PM.temRatio, PM.current_tem[v[0]].y*PM.temRatio);
                        context.lineTo(PM.current_tem[v[1]].x*PM.temRatio, PM.current_tem[v[1]].y*PM.temRatio);
                    } else {
                        // connect with bezier curve
                        var pts = [];
                        for (j = 0; j < v.length; j++) {
                            pts.push(PM.current_tem[v[j]].x*PM.temRatio);
                            pts.push(PM.current_tem[v[j]].y*PM.temRatio);
                        }
                        var cp = []; // array of control points, as x0,y0,x1,y1,...
                        var n = pts.length;
                        if (pts[0] == pts[n - 2] && pts[1] == pts[n - 1]) {
                            // remove duplicate points and adjust n
                            n = n - 2;
                            pts.pop();
                            pts.pop();
                            // Append and prepend knots and control points to close the curve
                            pts.push(pts[0], pts[1], pts[2], pts[3]);
                            pts.unshift(pts[n - 1]);
                            pts.unshift(pts[n - 1]);
                            for (j = 0; j < n; j += 2) {
                                cp = cp.concat(getControlPoints(pts[j], pts[j + 1], pts[j + 2], pts[j + 3], pts[j + 4], pts[j + 5], t));
                            }
                            cp = cp.concat(cp[0], cp[1]);
                            for (j = 2; j < n + 2; j += 2) {
                                context.moveTo(pts[j], pts[j + 1]);
                                context.bezierCurveTo(cp[2 * j - 2], cp[2 * j - 1], cp[2 * j], cp[2 * j + 1], pts[j + 2], pts[j + 3]);
                            }
                            //context.closePath();    
                        } else {
                            // Draw an open curve, not connected at the ends
                            for (j = 0; j < n - 4; j += 2) {
                                cp = cp.concat(getControlPoints(pts[j], pts[j + 1], pts[j + 2], pts[j + 3], pts[j + 4], pts[j + 5], t));
                            }
                            context.moveTo(pts[0], pts[1]);
                            context.quadraticCurveTo(cp[0], cp[1], pts[2], pts[3]); // first arc
                            for (j = 2; j < n - 5; j += 2) {
                                context.bezierCurveTo(cp[2 * j - 2], cp[2 * j - 1], cp[2 * j], cp[2 * j + 1], pts[j + 2], pts[j + 3]);
                            }
                            context.quadraticCurveTo(cp[2 * n - 10], cp[2 * n - 9], pts[n - 2], pts[n - 1]); // last arc
                        }
                    }
                    canvas.stroke(this);
                },
                stroke: stkColor,
                opacity: 0.5,
                strokeWidth: PM.lineWidth,
                lineCap: "round",
                draggable: false,
                id: 'l' + i,
                shadowColor: 'black',
                shadowOffsetX: 1,
                shadowOffsetY: 1,
                shadowOpacity: 0.5,
                shadowBlur: 2
            });
            
            ln.on('mouseover', function() {
                if (PM.delinfunc == 'linesub') {
                    this.setStroke('red'); 
                    PM.temLayer.draw();
                }
                $('#footer').prop('data-persistent', $('#footer').html());
                $('#footer').html('Line ' + i + ' [' + PM.current_lines[i].join(", ") + ']');
            }).on('mouseout mouseup', function() {
                if (PM.delinfunc == 'linesub') {
                    var stkColor = (typeof PM.line_colors[i] == 'undefined' || PM.line_colors[i] == 'default') ? 
                                    PM.default_line_color : PM.line_colors[i];
                    this.setStroke(stkColor); 
                    PM.temLayer.draw();
                }
                $('#footer').html($('#footer').prop('data-persistent'));
            }).on('click', function() {
                if (PM.delinfunc == 'linesub') {
                    PM.delinfunc = 'move';
                    cursor('auto');
                    quickhelp();
                    PM.lineWidth = 1;
                    $.each(PM.current_lines, function(j) {
                        var l = PM.stage.get('#l' + j)[0];
                        if (l !== undefined) l.setStrokeWidth(PM.lineWidth);
                    });
                    
                    PM.current_lines.splice(i, 1);
                    PM.stage.get('#l' + PM.current_lines.length)[0].remove(); // remove last line so numbers stay correct (all lines update with info from PM.current_lines)
                    PM.line_colors.splice(i, 1);
                    updateUndoList();
                    PM.temLayer.draw();
                }
            });
            // add the lineto the layer
            PM.temLayer.add(ln);
        }
    });
    // remove extra lines
    for (i = PM.current_lines.length; i < 1000; i++) {
        if (PM.stage.get('#l' + i).length > 0) {
            PM.stage.get('#l' + i)[0].remove();
        }
    }

    $.each(PM.current_tem, function(i, v) {
        var x = v.x*PM.temRatio;
        var y = v.y*PM.temRatio;
        if (x < 0) { x = 0; }
        if (y < 0) { y = 0; }
        if (x > $delin.width()) { x = $delin.width(); }
        if (y > $delin.height()) { y = $delin.height(); }
        if (PM.stage.get('#d' + i).length > 0) {
            PM.stage.get('#d' + i)[0].setX(x);
            PM.stage.get('#d' + i)[0].setY(y);
        } else {
            var pt = new Kinetic.Image({
                x: x,
                y: y,
                id: 'd' + i,
                //name: '[' + i + '] ' + v.name,
                image: PM.cross,
                //lineCap: "round",
                offset: [5, 5],
                draggable: true
            });
            // add cursor styling and show name
            pt.on("mouseover", function(e) {
                if (PM.delinfunc == 'lineadd') {
                    cursor('lineadd');
                } else if (PM.delinfunc == 'label') { 
                    return false;
                } else {
                    cursor('pointer');
                }
                pt.setImage(PM.hover_cross);
                
                var pointName = (PM.default_tem[i] !== undefined) ? PM.default_tem[i].name : 'undefined';
                $('#footer').prop('data-persistent', $('#footer').html()).html('[' + i + '] ' + pointName);
                PM.hoverPoint = i;
                if (e.metaKey || e.ctrlKey) {
                    e.stopPropagation();
                    $.each(PM.current_lines, function(idx, line) {
                        var inLine = false;
                        $.each(line, function(idx2, pt2) {
                            inLine = inLine || (pt2 == i);
                        });
                        if (inLine) {
                            if (PM.stage.get('#l' + idx).length) {
                                var ln = PM.stage.get('#l' + idx)[0];
                                ln.setStroke('yellow');
                                ln.setStrokeWidth(2*PM.lineWidth);
                            }
                        }
                    });
                }
                PM.temLayer.draw();
            });
            pt.on("mousemove dragmove", function() {
                if (PM.delinfunc == 'move') {
                    var xchange = pt.getX() - PM.current_tem[i].x*PM.temRatio;
                    var ychange = pt.getY() - PM.current_tem[i].y*PM.temRatio;
                    $.each(PM.selected_pts, function(selpt, sel) {
                        if (sel && selpt != i) {
                            var thisPt = PM.stage.get('#d' + selpt)[0];
                            thisPt.setImage(PM.selected_cross);
                            var newx = thisPt.getX() + xchange;
                            var newy = thisPt.getY() + ychange;
                            thisPt.setX(newx);
                            thisPt.setY(newy);
                            PM.current_tem[selpt].x = newx/PM.temRatio;
                            PM.current_tem[selpt].y = newy/PM.temRatio;
                        }
                    });
                    PM.current_tem[i].x = pt.getX()/PM.temRatio;
                    PM.current_tem[i].y = pt.getY()/PM.temRatio;
                } else {
                    pt.setX(PM.current_tem[i].x*PM.temRatio);
                    pt.setY(PM.current_tem[i].y*PM.temRatio);
                }
                //PM.temLayer.draw();
            });
            pt.on("mouseup mouseout", function(e) {
                if (PM.delinfunc == 'label') { return false; }
                
                if (e.shiftKey && (e.metaKey || e.ctrlKey)) {
                    cursor('crosshair');
                } else if (PM.delinfunc == 'lineadd') {
                    cursor('lineadd');
                } else {
                    cursor('auto');
                }

                PM.hoverPoint = false;
                $.each(PM.current_tem, function(idx) {
                    var thisP = PM.stage.get('#d' + idx)[0];
                    if (thisP !== undefined) {
                        if (!thisP.selected) {
                            thisP.setImage(PM.cross);
                        } else {
                            thisP.setImage(PM.selected_cross);
                        }
                    }
                });
                $.each(PM.current_lines, function(idx) {
                    var thisL = PM.stage.get('#l' + idx)[0];
                    if (thisL !== undefined) {
                        var stkColor = (typeof PM.line_colors[idx] == 'undefined' || PM.line_colors[idx] == 'default') ? 
                                        PM.default_line_color : PM.line_colors[idx];
                        thisL.setStroke(stkColor);
                        thisL.setStrokeWidth(PM.lineWidth);
                    }
                });
                PM.current_tem[i].x = pt.getX()/PM.temRatio;
                PM.current_tem[i].y = pt.getY()/PM.temRatio;
                PM.temLayer.draw();
                updateUndoList();
                $('#footer').html($('#footer').prop('data-persistent')); // remove point name and replace with whatever is in data-persistent
            });
            pt.on("mousedown", function(e) {
                if (PM.delinfunc == 'label') { 
                    return false;
                } else if (PM.delinfunc == 'lineadd') {
                    var line = PM.current_lines.length - 1;
                    // check if last point if the same as this one
                    var lastPt = PM.current_lines[line][PM.current_lines[line].length - 1];
                    if (lastPt === undefined || lastPt != i) {
                        PM.current_lines[line].push(i);
                        var t = 'Added a point to the new line [' + PM.current_lines[line].join() + ']';
                        $('#footer').html(t).prop('data-persistent', t);
                    }
                } else if (PM.delinfunc == 'sym') {
                    nextSymPt(i);
                } else if (PM.delinfunc == "mask") {
                    if (e.metaKey || e.ctrlKey) {
                        $.each(PM.current_lines, function(idx, line) {
                            var inLine = false;
                            $.each(line, function(idx2, pt2) {
                                inLine = inLine || (pt2 == i);
                            });
                            if (inLine) {
                                addToCustomMask(';');
                                $.each(line, function(idx2, pt2) {
                                    addToCustomMask(pt2);
                                });
                            }
                        });
                    } else {
                        addToCustomMask(i);
                    }
                } else if (e.metaKey || e.ctrlKey) {
                    // select all points in lines that contain this point
                    e.stopPropagation();
                    
                    var was_selected = !pt.selected;
                    if (was_selected) {
                        this.selected = true;
                        this.setImage(PM.selected_cross);
                    } else {
                        this.selected = false;
                        this.setImage(PM.cross);
                    }
                    $.each(PM.current_lines, function(idx, line) {
                        var inLine = false;
                        $.each(line, function(idx2, pt2) {
                            inLine = inLine || (pt2 == i);
                        });
                        if (inLine) {
                            $.each(line, function(idx2, pt2) {
                                if (was_selected) {
                                    PM.selected_pts[pt2] = true;
                                    PM.stage.get('#d' + pt2)[0].selected = true;
                                    PM.stage.get('#d' + pt2)[0].setImage(PM.selected_cross);
                                } else {
                                    PM.selected_pts[pt2] = false;
                                    PM.stage.get('#d' + pt2)[0].selected = false;
                                    PM.stage.get('#d' + pt2)[0].setImage(PM.cross);
                                }
                            });
                        }
                    });
                } else if (e.shiftKey) {
                    //e.stopPropagation();
                    pt.selected = !pt.selected;
                    pt.setImage(PM.selected_cross);
                    if (pt.selected) {
                        PM.selected_pts[i] = true;
                    } else {
                        PM.selected_pts[i] = false;
                    }
                }
                PM.temLayer.draw();
                return false;
            });
            PM.temLayer.add(pt);
        }
    });
    // remove extra points
    for (i = PM.current_tem.length; i < 1000; i++) {
        if (PM.stage.get('#d' + i).length > 0) {
            PM.stage.get('#d' + i)[0].remove();
        }
    }
        
    PM.temLayer.on('beforeDraw', function() { PM.stage.show(); });
    PM.stage.add(PM.temLayer);
    console.timeEnd('drawTem()');
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

function editTemplate() {
    
    // get current Tem info
    $('#defaultTemName').val($('#current_tem_name').text());
    $('#defaultTemNotes').text($('#currentTem li[data-id='+PM.default_tem_id+']').attr('title'));
    
    // get tem points
    $('select.tempoints').html('');
    $.each(PM.current_tem, function(i, v) {
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
                    $.ajax({
                        url: 'scripts/temDelete',
                        data: {
                            id: PM.default_tem_id,
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
                            id: $('#isNewTem').prop('checked') ? 'NULL' : PM.default_tem_id,
                            name: $('#defaultTemName').val(),
                            notes: $('#defaultTemNotes').val(),
                            'public': ($('#defaultTemPublic').prop('checked') ? true : false),
                            delinPts: [$('#defaultTem3Pt1').val(), $('#defaultTem3Pt2').val(), $('#defaultTem3Pt3').val()],
                            tem: PM.current_tem,
                            lines: PM.current_lines,
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

function saveTem() { console.log('saveTem()');
    var tem = PM.current_tem.length + "\n";
    var resize = PM.originalHeight / $delin.height();
    $.each(PM.current_tem, function(i, v) {
        tem = tem + Math.round(v.x * 10) / 10 + "\t" + Math.round(v.y * 10) / 10 + "\n";
    });
    tem = tem + PM.current_lines.length + "\n";
    $.each(PM.current_lines, function(i, v) {
        tem = tem + "0\n" + v.length + "\n";
        $.each(v, function(i2, v2) {
            tem = tem + v2 + " ";
        });
        tem = tem + "\n";
    });
    tem = tem + (PM.current_lines.length - 1) + "\n";
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
    if ($('#delin_save.unsaved').length) {
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
    $.each(PM.current_tem, function(i, v) {
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
        PM.current_tem.splice(v, 1);
    });
    $.each(PM.current_tem, function(i, v) {
        PM.current_tem[i].i = i;
    });
    // remove points from lines and remap
    var ln = PM.current_lines.length;
    for (var i = ln - 1; i >= 0; i--) {
        var line = PM.current_lines[i];
        var n = line.length;
        for (var j = n - 1; j >= 0; j--) {
            if (tem_map[line[j]] == 'removed') {
                PM.current_lines[i].splice(j, 1);
            } else {
                PM.current_lines[i][j] = tem_map[line[j]];
            }
        }
        if (PM.current_lines[i].length < 2) {
            PM.current_lines.splice(i, 1);
        }
    }
    updateUndoList();
    makePoints(PM.current_tem);
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
    if (n >= PM.current_tem.length) {
        PM.delinfunc = 'move';
        $('#pointer').fadeOut().css({left: '-100px', top: '-100px'});
        
        $.ajax({
            url: 'scripts/temSetSym',
            data: {
                tem_id: PM.default_tem_id,
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
    $.each(PM.selected_pts, function(idx, val) {
        if (val) {
            var pt = PM.stage.get('#d' + idx)[0];
            pt.selected = false;
            pt.setImage(PM.cross);
        }
    });
    PM.selected_pts = [];
    var pt = PM.stage.get('#d' + n)[0];
    if (pt !== undefined) {
        PM.selected_pts[n] = true;
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
    $.each(PM.current_tem, function(i, v) {
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
    $.each(PM.current_tem, function(i, v) {
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
            PM.default_tem_id = temId;
            PM.default_tem = data.default_tem;
            PM.default_lines = data.default_lines;
            PM.fitPoints = data.fitPoints;
            PM.line_colors = data.line_colors;
            $('#currentTem li span.checkmark').hide();
            $('#currentTem li[data-id=' + temId + '] span.checkmark').show();
            $('#current_tem_name').text(data.name);
            
            if (PM.interface == 'delineate') {
                // check if new tem is compatible
                //if (PM.default_tem.length != PM.current_tem.length || PM.default_lines.length != PM.current_lines.length) {
                if (PM.current_tem.length === 0) {
                    PM.current_tem = data.default_tem;
                    PM.current_lines = data.default_lines;
                    if (temId !== 13) $('#fitTemplate').click();
                }
            }
        }
    });
}

function clickPt(pt) {
    var ptname = typeof PM.default_tem[PM.fitPoints[pt]].name === "undefined" ? 
                                    PM.fitPoints[pt] : PM.default_tem[PM.fitPoints[pt]].name;
    var n = pt + 1;
    var ordinal = {1: 'st', 2: 'nd', 3: 'rd', 4: 'th'};
    
    quickhelp('Click ' + n + '<sup>' + ordinal[n] + '</sup> Point (' + ptname + ')');
}

function setPointLabels() {
    PM.ptLabels = [];
    PM.delinfunc = 'label';
    
    // create labels for each point in the default tem, add current name
    var $ol = $('#labelDialog ol');
    $ol.html('');
    $.each(PM.default_tem, function (i) {
        $ol.append('<li><input name="' + i + '" type="text" value="' + PM.default_tem[i].name + '" /></li>');
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
                    PM.ptLabels[i] = name;
                });
                $.ajax({
                    url: 'scripts/temSetLabels',
                    data: {
                        tem_id: PM.default_tem_id,
                        labels: PM.ptLabels
                    },
                    success: function(data) {
                        if (data.error) {
                            $('<div title="Error recording point labels" />').html(data.errorText).dialog();
                        } else {
                            quickhelp('Point labels recorded.', 3000);
                            //update template labels in default_tem
                            $.each(PM.ptLabels, function(i, name) {
                                PM.default_tem[i].name = name;
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
    }).find('p').html('Labels for template <code>' + $('#current_tem_name').text() + '</code>');
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
        PM.eye_clicks = [0,0,0];
        PM.delinfunc = 'move';
        $('#pointer, #leftEye, #rightEye, #mouth').hide();
        
        PM.current_tem = [];
        setCurrentTem(13);
        
        /*
        // these points are duplicated in the points array
        PM.current_tem[0].x = tags.eye_left.x * imgWidth / 100;
        PM.current_tem[0].y = tags.eye_left.y * imgHeight / 100;
        PM.current_tem[1].x = tags.eye_right.x * imgWidth / 100;
        PM.current_tem[1].y = tags.eye_right.y * imgHeight / 100;
        PM.current_tem[2].x = tags.mouth_center.x * imgWidth / 100;
        PM.current_tem[2].y = tags.mouth_center.y * imgHeight / 100;
        PM.current_tem[3].x = tags.nose.x * imgWidth / 100;
        PM.current_tem[3].y = tags.nose.y * imgHeight / 100;
        */

        $.each(tags.points, function(i, p) {
            PM.current_tem[i].x = p.x * imgWidth / 100;
            PM.current_tem[i].y = p.y * imgHeight / 100;
        });
        
        drawTem();
    });
}

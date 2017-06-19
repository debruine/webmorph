//====================================
// !DELINEATE FUNCTIONS
//====================================

function newDelinPoint(e) {
    var imgoffset = $delin.offset();

    var x = (e.pageX - imgoffset.left)/WM.temRatio;
    var y = (e.pageY - imgoffset.top)/WM.temRatio;
    var i = WM.current.tem.length;
    WM.current.tem.push({
        i: i,
        name: 'new point',
        x: x,
        y: y
    });
    makePoint(x,y,i);
    updateUndoList();
    drawTem();
}

function delin_fitsize() {
    var availableWidth,
        availableHeight,
        fitWidth,
        resize = 1.0;
        
    availableWidth = $('#delineateInterface').innerWidth();
    availableHeight = $(window).height() - $delin.offset().top - $('#footer').height() - 20;
    fitWidth = availableWidth*WM.originalHeight/WM.originalWidth;

    resize = (fitWidth >= availableHeight) ?
                 availableHeight :  // fit to available height
                 fitWidth;          // fit to available width

    $('#imgsize').slider('value', resize);
}

function setSymPoints() {
    if (WM.delin.tem.length != WM.current.tem.length) {
        growl('The current template does not match the template <code>'
              + $('#currentTem_name').text() + '</code>');
        return true;
    }

    $.ajax({
        url: '/scripts/userCheckAccess',
        data: { table: 'tem', id: WM.delin.temId },
        success: function(data) {
            if (data.error) {
                growl(data.errorText);
            } else {
                var theText = '<p>To set symmetry points, look for the '
                            + 'highlighted point and cmd-click on its corresponding '
                            + 'point on the other side. You can move points out '
                            + 'of the way to check if they are overlapping. If a '
                            + 'point is on the midline, its corresponding point '
                            + 'is itself. If you make a mistake, click cmd-Z to '
                            + 'go back a point.</p>';
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
                                WM.symPts = {
                                    n: 0,
                                    order: [],
                                    sym: []
                                };
                                WM.delinfunc = 'sym';
                                nextSymPt('start');
                            }
                        }
                    }
                });
            }
        }
    });
}

function quickhelp(text, fadeout) {
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
    WM.delinfunc = '3pt';
    var thePt;

    if (WM.eyeClicks.length === 0) {
        WM.eyeClicks[0] = {
            x: (e.pageX - imgoffset.left)/WM.temRatio,
            y: (e.pageY - imgoffset.top)/WM.temRatio
        };
        thePt = $('#leftEye');
        thePt.css('left', e.pageX - (thePt.width() / 2))
             .css('top', e.pageY - (thePt.height() / 2)).show();

        clickPt(1);
    } else if (WM.eyeClicks.length == 1) {
        WM.eyeClicks[1] = {
            x: (e.pageX - imgoffset.left)/WM.temRatio,
            y: (e.pageY - imgoffset.top)/WM.temRatio
        };
        thePt = $('#rightEye');
        thePt.css('left', e.pageX - (thePt.width() / 2))
             .css('top', e.pageY - (thePt.height() / 2)).show();

        clickPt(2);
    } else if (WM.eyeClicks.length == 2) {
        WM.eyeClicks[2] = {
            x: (e.pageX - imgoffset.left)/WM.temRatio,
            y: (e.pageY - imgoffset.top)/WM.temRatio
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
            newfitPoints = WM.fitPoints;
        }

        var temPoints = {
            0: {
                x: WM.current.tem[newfitPoints[0]].x,
                y: WM.current.tem[newfitPoints[0]].y
            },
            1: {
                x: WM.current.tem[newfitPoints[1]].x,
                y: WM.current.tem[newfitPoints[1]].y
            },
            2: {
                x: WM.current.tem[newfitPoints[2]].x,
                y: WM.current.tem[newfitPoints[2]].y
            }
        };
        $.ajax({
            async: false,
            url: 'scripts/temFit',
            data: {
                'eyeclicks': WM.eyeClicks,
                'temPoints': temPoints
            },
            success: function(data) {
                $.each(WM.current.tem, function(i, v) {
                    var newx = (data.a * v.x) + (data.b * v.y) + data.c;
                    var newy = (data.d * v.x) + (data.e * v.y) + data.f;
                    if (newx < 0) { newx = 0; }
                    if (newy < 0) { newy = 0; }
                    if (newx > $delin.width()/WM.temRatio) {
                        newx = $delin.width()/WM.temRatio;
                    }
                    if (newy > $delin.height()/WM.temRatio) {
                        newy = $delin.height()/WM.temRatio;
                    }
                    WM.current.tem[i].x = newx;
                    WM.current.tem[i].y = newy;
                });
                makePoints(WM.current.tem);
                updateUndoList();
            }
        });
        $('#template').show();
        $('#leftEye, #rightEye, #mouth').hide();
        drawTem();
        if (typeof files !== 'undefined' && files.length) {
            saveTem();
            WM.selectedFile++;
            if (WM.selectedFile >= files.length) {
                growl('Batch Fit Template finished. '
                      + files.length + ' files aligned.', 3000);
                $('#footer-text').html('');
                files = [];
                WM.selectedFile = 0;
                WM.delinfunc = 'move';
            } else {
                $('#footer-text').html(WM.selectedFile + ' of '
                                  + files.length + ' templates fitted.');
                var url = files[WM.selectedFile];
                var name = WM.project.id + urlToName(url);
                delinImage(name, false);
                WM.eyeClicks = [];
                WM.delinfunc = '3pt';
            }
        } else {
            //saveTem();
            WM.delinfunc = 'move';
        }
    }
}

function delinImage(name, async) { console.log('delinImage(' + name + ', ' + async + ')');
    $.ajax({
        async: (typeof async === 'boolean') ? async : true,
        url: 'scripts/imgDelin',
        data: { img: name },
        success: function(data) {
            $('.twoD').show();
            $('.threeD').hide();
            
            var h = $delin.height();
            WM.originalHeight = data.originalHeight;
            WM.originalWidth = data.originalWidth;
            var w = Math.round((h * data.originalWidth) / WM.originalHeight);
            WM.temRatio = h / WM.originalHeight;
            $('#size_value').html(w + 'x' + h);
            $('#template').css({
                "width": w + "px ",
                "height": h + 'px'
            });
            WM.faceimg = data.imgname;
            $('#footer-text').html('Loaded <code>' + urlToName(WM.faceimg) + '</code>');

            $delin.css({
                "width": w + "px ",
                "background": "white 0 0 no-repeat url(" + fileAccess(WM.faceimg) + ")",
                "background-size": "100%"
            });
            $('#imgname').html(urlToName(WM.faceimg));
            $('#delin_save').removeClass('unsaved');
            var needsDelin = false;

            $delin.find('.pt').remove();

            if (data.temPoints !== null) {
                WM.delinfunc = 'move';
                autoLoadTem(data.temPoints, data.lineVectors);

                WM.current.tem = [];
                $.each(data.temPoints, function(i, v) {
                    WM.current.tem[i] = { x: v[0], y: v[1] };
                });
                WM.undo.tem = [$.extend(true, [], WM.current.tem)];
                WM.undo.level = 0;
                WM.current.lines = data.lineVectors;
                WM.undo.lines = [$.extend(true, [], WM.current.lines)];
            } else {
                WM.delinfunc = '3pt';
                WM.current.tem = [];
                $.each(WM.delin.tem, function(i, v) {
                    WM.current.tem[i] = { x: v.x, y: v.y };
                });
                WM.undo.tem = [$.extend(true, [], WM.current.tem)];
                WM.current.lines = $.extend(true, [], WM.delin.lines);
                WM.undo.lines = [$.extend(true, [], WM.current.lines)];
                WM.undo.level = 0;
            }

            if (WM.appWindow != 'delineate') {
                $('#showDelineate').click();
            } else {
                hashSet();
            }

            if (WM.delinfunc == '3pt') {
                WM.eyeClicks = [];
                $('#template').hide();
                cursor('crosshair');
                clickPt(0);
            } else if (WM.showTem) {
                WM.eyeClicks = [WM.fitPoints[0], WM.fitPoints[1], WM.fitPoints[2]];
                $('#template').show();
                $('#imgsize').change();
                makePoints(WM.current.tem);
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
        data: { next: WM.faceimg },
        success: function(data) {
            checkSaveFirst(function() {
                //delinImage(data.img);
                WM.faceimg = data.img;
                $('#refresh').click();
            });
        }
    });
}

function prevImg() {
    // load next image belonging to that user
    $.ajax({
        url: 'scripts/imgScroll',
        data: { prev: WM.faceimg },
        success: function(data) {
            checkSaveFirst(function() {
                //delinImage(data.img);
                WM.faceimg = data.img;
                $('#refresh').click();
            });
        }
    });
}

function nudge(xchange, ychange) {  console.log('nudge(' + xchange + ', ' + ychange + ')');
    if (WM.delinfunc == 'move') {
        $('.pt.selected').each( function(i) {
            var selpt = $(this).attr('n');

            WM.current.tem[selpt].x += xchange/WM.temRatio;
            WM.current.tem[selpt].y += ychange/WM.temRatio;
        });
        updateUndoList();
        drawTem();
    }
}

function temRotate(rotate) { console.log('rotate(' + rotate + ')');
    var selpts,
        n, i, x, y, xr, yr,
        cx = 0, cy = 0;
        
    selpts = getSelPts();
    n = selpts.length;
    
    $.each(selpts, function(i, selpt) {
        cx += WM.current.tem[selpt].x;
        cy += WM.current.tem[selpt].y;
    });
    
    cx = cx/n;
    cy = cy/n;
    
    
    for (i = 0; i < n; i++) {
        x = WM.current.tem[selpts[i]].x;
        y = WM.current.tem[selpts[i]].y;

        // Subtract original midpoints, so that midpoint is translated to origin
        // and add the new midpoints in the end again
        xr = (x - cx) * Math.cos(rotate) - (y - cy) * Math.sin(rotate) + cx;
        yr = (x - cx) * Math.sin(rotate) + (y - cy) * Math.cos(rotate) + cy;
        
        WM.current.tem[selpts[i]].x = xr;
        WM.current.tem[selpts[i]].y = yr;
    }
    updateUndoList();
    drawTem();
}

function temSizeChange(pcnt) {
    if (WM.appWindow == 'delineate' && WM.delinfunc == 'move') {
        var pcntChange = (100 + pcnt)/100;
        var x=0,y=0,n=0;

        var selpts = getSelPts();

        $.each(selpts, function(i, selpt) {
            x += WM.current.tem[selpt].x;
            y += WM.current.tem[selpt].y;
            n++;
        });

        var offset = {
            x: (x/n) - (pcntChange*x/n),
            y: (y/n) - (pcntChange*y/n)
        };

        $.each(selpts, function(i, selpt) {
            WM.current.tem[selpt].x *= pcntChange;
            WM.current.tem[selpt].y *= pcntChange;
            WM.current.tem[selpt].x += offset.x;
            WM.current.tem[selpt].y += offset.y;
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
        ctx.moveTo(pts[2], pts[3]);
        for (j = 2; j < n + 2; j += 2) {
            //ctx.moveTo(pts[j], pts[j + 1]);
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

function svgBezier(v, lineWidth, stkColor, lineName) {
    var pts = [],
        cp = [], // array of control points, as x0,y0,x1,y1,...
        n,
        j,
        ctx = {},
        path = '';
        
    if (typeof lineName === 'undefined') { lineName = 'line'; }
    
    path = '    <path name="' + lineName + '"\n          d="';

    ctx = {
        moveTo: function(x, y) {
            x = round(x, 2);
            y = round(y, 2);
            path += 'M ' + x + ' ' + y;
        },
        lineTo: function(x, y) {
            x = round(x, 2);
            y = round(y, 2);
            path += '\n             L ' + x + ' ' + y;
        },
        stroke: function() {
            if (typeof stkColor === 'undefined' && typeof lineWidth === 'undefined') {
                path += '"\n    />';
            } else if (typeof lineWidth === 'undefined') {
                path += '"\n             stroke="' + stkColor + '"\n    />';
            } else if (typeof stkColor === 'undefined') {
                path += '"\n             stroke-width="' + lineWidth + '"\n    />';
            } else {
                path += '"\n             stroke="' + stkColor + '\n             stroke-width="' + lineWidth + '"\n    />';
            }
        },
        quadraticCurveTo: function(x1, y1, x, y) {
            x1 = round(x1, 2);
            y1 = round(y1, 2);
            x = round(x, 2);
            y = round(y, 2);
            path += '\n             Q ' + x1 + ' ' + y1 + ', ' + x + ' ' + y;
        },
        bezierCurveTo: function(x1, y1, x2, y2, x, y) {
            x1 = round(x1, 2);
            y1 = round(y1, 2);
            x2 = round(x2, 2);
            y2 = round(y2, 2);
            x = round(x, 2);
            y = round(y, 2);
            path += '\n             C ' + x1 + ' ' + y1 + ', ' + x2 + ' ' + y2 + ', ' + x + ' ' + y;
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
        ctx.moveTo(pts[2], pts[3]);
        for (j = 2; j < n + 2; j += 2) {
            
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

    last_tem = WM.undo.tem[WM.undo.level];
    last_lines = WM.undo.lines[WM.undo.level];
    changed = false;

    // check if new tem is changed from last tem
    $.each(WM.current.tem, function(i, v) {
        if (last_tem[i] === undefined || (WM.current.tem[i].x != last_tem[i].x || WM.current.tem[i].y != last_tem[i].y)) {
            changed = true;
            return false;
        }
    });
    if (!changed) {
        // check if lines are changed (only check if points not changed)
        $.each(WM.current.lines, function(i, line) {
            $.each(line, function(j, pt) {
                if (last_lines[i] === undefined || last_lines[i][j] === undefined || WM.current.lines[i][j] != last_lines[i][j]) {
                    changed = true;
                    return false;
                }
            });
        });
    }
    // add to undo list if changed
    if (changed) {
        WM.undo.level++;
        WM.undo.tem[WM.undo.level] = $.extend(true, [], WM.current.tem);
        WM.undo.lines[WM.undo.level] = $.extend(true, [], WM.current.lines);
        if (WM.project.perm != 'read-only') {
            $('#delin_save').addClass('unsaved');
        }
    }
}

function makePoints(ptArray) { console.time("makePoints()");
    var n = ptArray.length;

    WM.pts = [];
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
    WM.pts[i] = $pt;

    // set connected points and lines

    $.each(WM.current.lines, function (line, linearray) {
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
                    WM.pts[pt].addClass('selected');
                });
            }
        },
        drag: function(e, ui) {
            var move = {
                x: (ui.position.left/WM.temRatio) - WM.current.tem[i].x,
                y: (ui.position.top/WM.temRatio) - WM.current.tem[i].y
            };

            WM.current.tem[i].x = ui.position.left/WM.temRatio;
            WM.current.tem[i].y = ui.position.top/WM.temRatio;

            $('#footer .x').text(round(WM.current.tem[i].x, 1));
            $('#footer .y').text(round(WM.current.tem[i].y, 1));

            $.each(WM.current.tem, function(j,pt) {
                if (i != j && WM.pts[j].hasClass('selected')) {
                    pt.x += move.x;
                    pt.y += move.y;
                }
            });

            drawTem();
        },
    }).css({
        top: y*WM.temRatio,
        left: x*WM.temRatio
    });
}

function drawMask(masks, blur) {
    var $masked_img,
        ctx,
        blurtans,
        m,
        image;

    blur = blur || 10;
    $masked_img = $("<canvas width='" + WM.originalWidth + "' height='" + WM.originalHeight + "' />");
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
                    var x = WM.current.tem[pt].x;
                    var y = WM.current.tem[pt].y;
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
                var x = WM.current.tem[pt].x;
                var y = WM.current.tem[pt].y;
                bez.push([x, y]);
            });
            drawBezier(ctx, bez, false);
        });
        ctx.closePath();
        ctx.fill();
    });

    ctx.globalCompositeOperation = "destination-over"; // add face underneath mask
    image = new Image();
    image.src = fileAccess(WM.faceimg);
    ctx.drawImage(image, 0, 0, WM.originalWidth, WM.originalHeight,
                         0, 0, WM.originalWidth, WM.originalHeight);

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

    WM.delinContext.clearRect (0, 0, $('#template').width(), $('#template').height());

    if (WM.pts === undefined || WM.pts.length === 0) return false;

    nlines = WM.current.lines.length;
    defaultLineWidth = $('#defaultLineWidth').val();

    for (i = 0; i < nlines; i++) {
        bez = [],
        sel = 0,
        csel=0,
        npoints = WM.current.lines[i].length;

        for (j = 0; j < npoints; j++) {
            x = WM.current.tem[WM.current.lines[i][j]].x*WM.temRatio;
            y = WM.current.tem[WM.current.lines[i][j]].y*WM.temRatio;

            bez.push([x, y]);

            if (WM.pts[WM.current.lines[i][j]].hasClass('selected')) { sel++; }
            if (WM.pts[WM.current.lines[i][j]].hasClass('couldselect')) { csel++; }
        }

        stkColor = (typeof WM.delin.lineColors[i]=='undefined' || WM.delin.lineColors[i] == 'default') ?
                        WM.delin.lineColor : WM.delin.lineColors[i];
        if (sel == npoints) {
            stkColor = 'rgb(255,255,127)';
        } else if (csel == npoints) {
            stkColor = 'rgba(255,255,127, 0.5)';
        }
        WM.delinContext.strokeStyle = stkColor;

        lineWidth = (typeof WM.delin.lineWidths[i]=='undefined' || WM.delin.lineWidths[i] == 'default') ?
                        defaultLineWidth : WM.delin.lineWidths[i];
        WM.delinContext.lineWidth = lineWidth;

        drawBezier(WM.delinContext, bez);
    }

    // move delineation points
    $.each(WM.current.tem, function(i, v) {
        WM.pts[i].css({
            top: v.y*WM.temRatio,
            left: v.x*WM.temRatio
        });
    });
    
    // move 3-point delin points if visible
    $.each(WM.eyeClicks, function(i, ec) {
        var imgoffset = $delin.offset();
        var thePt = [$('#leftEye'), $('#rightEye'), $('#mouth')];
        var x =  ec.x * WM.temRatio +  imgoffset.left;
        var y =  ec.y * WM.temRatio + imgoffset.top;
        
        thePt[i].css('left', x - (thePt[i].width() / 2))
                .css('top', y - (thePt[i].height() / 2));
    });
}

function temSVG(lines, points, image, theType) {
    var nlines, i, j, x, y,
        defaultLineWidth,
        bez = [],
        theLine,
        lineName,
        npoints,
        lineWidth,
        stkColor,
        pointSize,
        paths = [],
        url;

    if (WM.pts === undefined || WM.pts.length === 0) return false;

    paths.push('<svg width="'+WM.originalWidth+'" height="'+WM.originalHeight+'" xmlns="http://www.w3.org/2000/svg">');

    // show image as background
    if (typeof image == "boolean" && image) {

    }

    defaultLineWidth = $('#defaultLineWidth').val();

    // show lines
    if (typeof lines == "boolean" && lines) {
        paths.push('<g id="lines" stroke="' + WM.delin.lineColor + '" stroke-width="' + defaultLineWidth + '" stroke-linecap="round" fill="transparent">');

        nlines = WM.current.lines.length;
        for (i = 0; i < nlines; i++) {
            theLine = WM.current.lines[i],
            bez = [],
            sel = 0,
            csel=0,
            npoints = theLine.length,
            lineName = WM.delin.tem[theLine[0]].name + ' to ' + WM.delin.tem[theLine[npoints-1]].name;

            for (j = 0; j < npoints; j++) {
                x = WM.current.tem[theLine[j]].x;
                y = WM.current.tem[theLine[j]].y;

                bez.push([x, y]);
            }

            stkColor = (typeof WM.delin.lineColors[i]=='undefined' || WM.delin.lineColors[i] == 'default') ?
                            undefined : WM.delin.lineColors[i];

            lineWidth = (typeof WM.delin.lineWidths[i]=='undefined' || WM.delin.lineWidths[i] == 'default') ?
                            undefined : WM.delin.lineWidths[i];

            paths.push(svgBezier(bez, lineWidth, stkColor, lineName));
        }

        paths.push('</g>');
    }

    // show points
    if (points) {
        pointSize = 5*defaultLineWidth;
        paths.push('<g id="points" stroke="rgb(0,255,0)" stroke-width="' + defaultLineWidth + '" stroke-linecap="round" fill="transparent">');

        npoints = WM.current.tem.length;

        for (i = 0; i < npoints; i++) {
            x = round(WM.current.tem[i].x, 2);
            y = round(WM.current.tem[i].y, 2);

            if (points == 'circle') {
                paths.push('    <circle id="pt' + i + '" name="' + WM.delin.tem[i].name + '"\n          cx="' + x + '" cy="' + y + '" r="' + pointSize + '" />');
            } else if (points == 'numbers') {
                paths.push('    <circle id="pt' + i + '" name="' + WM.delin.tem[i].name + '"\n          cx="' + x + '" cy="' + y + '" r="' + 1 + '" />');

            } else {
                paths.push('    <line name="' + WM.delin.tem[i].name + '"\n          x1="' + x + '" y1="' + (y-pointSize) + '" x2="' + x + '" y2="' + (y+pointSize) + '" />');
                paths.push('    <line name="' + WM.delin.tem[i].name + '"\n          x1="' + (x-pointSize) + '" y1="' + y + '" x2="' + (x+pointSize) + '" y2="' + y + '" />');
            }
        }

        paths.push('</g>');

        if (points == 'numbers') {
            paths.push('<g id="pointnumbers" fill="black" font-size="12" font-family="monospace">');
            for (i = 0; i < npoints; i++) {
                x = WM.current.tem[i].x;
                y = WM.current.tem[i].y;
                paths.push('    <text  id="n' + i + '" name="' + WM.delin.tem[i].name + '"\n          x="' + x + '" y="' + y + '">' + i + '</text>');
            }
            paths.push('</g>');
        }
    }

    paths.push('</svg>');

    //convert svg source to URI data scheme.
    url = "data:image/svg+xml;charset=utf-8," + encodeURIComponent(paths.join("\r\n"));
    postIt('scripts/temDownload', {
        img: WM.faceimg,
        svg: paths.join("\r\n"),
        type: theType
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
    if (WM.pasteBoard.length && WM.pasteBoard[0].hasOwnProperty('x')) {
        $.each(WM.pasteBoard, function(i, v) {
            WM.current.tem[v.n].x = v.x;
            WM.current.tem[v.n].y = v.y;
        });

        updateUndoList();
        drawTem();
    } else {
        $('#footer-text').html('No points were copied');
    }
}

function editTemplate() {

    // get current Tem info
    $('#defaultTemName').val($('#currentTem_name').text());
    $('#defaultTemNotes').text($('#currentTem li[data-id='+WM.delin.temId+']').attr('title'));

    // get tem points
    $('select.tempoints').html('');
    $.each(WM.current.tem, function(i, v) {
        var $opt = $('<option />').val(i).html(i);
        $('select.tempoints').append($opt);
    });

    // set to current tem fitPoints first
    $('select.tempoints').each(function(i) {
        $(this).val(WM.fitPoints[i]);
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
                            id: WM.delin.temId,
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
                            id: $('#isNewTem').prop('checked') ? 'NULL' : WM.delin.temId,
                            name: $('#defaultTemName').val(),
                            notes: $('#defaultTemNotes').val(),
                            'public': ($('#defaultTemPublic').prop('checked') ? true : false),
                            delinPts: [$('#defaultTem3Pt1').val(), $('#defaultTem3Pt2').val(), $('#defaultTem3Pt3').val()],
                            tem: WM.current.tem,
                            lines: WM.current.lines,
                            width: WM.originalWidth,
                            height: WM.originalHeight
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
    if (WM.project.perm == 'read-only') {
        growl('This project is read-only', 1000);
        return false;
    }
    console.log('saveTem()');

    var tem = WM.current.tem.length + "\n";
    var resize = WM.originalHeight / $delin.height();
    $.each(WM.current.tem, function(i, v) {
        tem = tem + Math.round(v.x * 10) / 10 + "\t" + Math.round(v.y * 10) / 10 + "\n";
    });
    tem = tem + WM.current.lines.length + "\n";
    $.each(WM.current.lines, function(i, v) {
        tem = tem + "0\n" + v.length + "\n";
        $.each(v, function(i2, v2) {
            tem = tem + v2 + " ";
        });
        tem = tem + "\n";
    });
    tem = tem + (WM.current.lines.length - 1) + "\n";
    $.ajax({
        url: 'scripts/temSave',
        data: {
            name: escape(WM.faceimg),
            tem: tem
        },
        success: function(data) {
            if (data.error === false) {
                //growl(data, 500);
                var now = new Date();
                var theTime = pad(now.getHours(), 2, '0') + ':' + pad(now.getMinutes(), 2, '0') + ':' + pad(now.getSeconds(), 2, '0');
                $('#footer-text').html(urlToName(WM.faceimg) + ' saved (' + theTime + ')');
                $('#delin_save').removeClass('unsaved');
                WM.finder.addFile(WM.faceimg, true);
            } else {
                growl(data.errorText);
            }
        }
    });
}

function checkSaveFirst(otherwise) {
    if ($('#delin_save.unsaved').length && WM.project.perm != 'read-only') {
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
    $.each(WM.current.tem, function(i, v) {
        if ($.inArray(i+'', ptArray) > -1) {
            tem_map[i] = 'removed';
        } else {
            tem_map[i] = newi;
            newi++;
        }
    });
    
    $.each(WM.current.tem, function(i, v) {
        WM.current.tem[i].oldi = i;
        WM.current.tem[i].name = WM.delin.tem[i].name;
    });
    // remove all tem points so they are re-generated at the end
    $('.pt').remove();
    // remove deleted points
    $.each(ptArray, function(i, v) {
        WM.current.tem.splice(v, 1);
    });
    // log new index
    $.each(WM.current.tem, function(i, v) {
        WM.current.tem[i].i = i;
    });
    // remove points from lines and remap
    var ln = WM.current.lines.length;
    for (var i = ln - 1; i >= 0; i--) {
        var line = WM.current.lines[i];
        console.log("Line " + i + ": " + line.join());
        var n = line.length;
        for (var j = n - 1; j >= 0; j--) {
            if (tem_map[line[j]] == 'removed') {
                WM.current.lines[i].splice(j, 1);
            } else {
                WM.current.lines[i][j] = tem_map[line[j]];
            }
        }
        if (WM.current.lines[i].length < 2) {
            WM.current.lines.splice(i, 1);
            console.log("* Removed Line " + i);
        } else {
            console.log("* Changed Line " + i + ": " + WM.current.lines[i].join());
        }
    }
    updateUndoList();
    makePoints(WM.current.tem);
    drawTem();
}

function nextSymPt(i) {
    var n = WM.symPts.n;
    if (i !== 'start') {
        WM.symPts.order.push(n); // add to order for undo
        WM.symPts.sym[n] = i;
        WM.symPts.sym[i] = n;
        while (WM.symPts.sym[WM.symPts.n] !== undefined) {
            WM.symPts.n++;
        }
        n = WM.symPts.n;
    }
    if (n >= WM.current.tem.length) {
        WM.delinfunc = 'move';
        $('.pt').removeClass('selected').removeClass('highlighted');
        $('#pointer').fadeOut().css({left: '-100px', top: '-100px'});

        $.ajax({
            url: 'scripts/temSetSym',
            data: {
                tem_id: WM.delin.temId,
                sym: WM.symPts.sym
            },
            success: function(data) {
                if (data.error) {
                    $('<div title="Error recording symmetry points" />').html(data.errorText).dialog();
                } else {
                    growl('Symmetry points recorded.', 1500);
                }
            }
        });
    } else {
        // unselect and unhighlight all points first
        $('.pt').removeClass('selected').removeClass('highlighted');
        var pt = WM.pts[n];
        pt.addClass('highlighted');
        $('#footer-text').prop('data-persistent', 
                               'Cmd-click the sym point for [' + n + '] ' 
                               + WM.delin.tem[n].name);
        $('#footer-text').html($('#footer-text').prop('data-persistent'));
        
        // move pointer to left of point
        $('#pointer').css({
            left: pt.offset().left - $('#pointer').width(),
            top: pt.offset().top - $('#pointer').height()/2 + pt.height()/2
        }).show();
    }
}

function showHoverPoints() {
    var n,
        conPts;
    if (WM.delinfunc != 'sym') {
        n = $('.pt:hover').attr('n');
        conPts = WM.pts[n].data('connectedPoints');
        $.each(conPts, function(i,pt) {
            WM.pts[pt].addClass('couldselect');
        });
        drawTem();
    }
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
    
    $.each(WM.current.tem, function(i, v) {
        var ptX = v.x * WM.temRatio;
        var ptY = v.y * WM.temRatio;
        if (ptX >= Math.min(mousedown.x, mouseup.x)) {
            if (ptX <= Math.max(mousedown.x, mouseup.x)) {
                if (ptY >= Math.min(mousedown.y, mouseup.y)) {
                    if (ptY <= Math.max(mousedown.y, mouseup.y)) {
                        WM.pts[i].addClass('selected');
                    }
                }
            }
        }
    });
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
    $.each(WM.current.tem, function(i, v) {
        var ptX = v.x * WM.temRatio;
        var ptY = v.y * WM.temRatio;
        if (ptX >= Math.min(mousedown.x, mouseup.x)) {
            if (ptX <= Math.max(mousedown.x, mouseup.x)) {
                if (ptY >= Math.min(mousedown.y, mouseup.y)) {
                    if (ptY <= Math.max(mousedown.y, mouseup.y)) {
                        WM.pts[i].addClass('selected');
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
            WM.delin.temId = temId;
            WM.delin.tem = data.defaultTem;
            WM.delin.lines = data.defaultLines;
            WM.fitPoints = data.fitPoints;
            WM.delin.lineColors = data.lineColors;
            $('#currentTem li span.checkmark').hide();
            $('#currentTem li[data-id=' + temId + '] span.checkmark').show();
            $('#currentTem_name').text(data.name);

            if (WM.appWindow == 'delineate') {
                // check if new tem is compatible
                //if (WM.delin.tem.length != WM.current.tem.length || WM.delin.lines.length != WM.current.lines.length) {
                if (WM.current.tem.length === 0) {
                    WM.current.tem = data.defaultTem;
                    WM.current.lines = data.defaultLines;
                    if (temId !== 13) $('#fitTemplate').click();
                }
            }
        }
    });
}

function clickPt(pt) {
    var ptname = typeof WM.delin.tem[WM.fitPoints[pt]].name === "undefined" ?
                                    WM.fitPoints[pt] : WM.delin.tem[WM.fitPoints[pt]].name;
    var n = pt + 1;
    var ordinal = {1: 'st', 2: 'nd', 3: 'rd', 4: 'th'};

    quickhelp('Click ' + n + '<sup>' + ordinal[n] + '</sup> Point (' + ptname + ')');
}

function setPointLabels() {
    if (WM.delin.tem.length != WM.current.tem.length) {
        growl('The current template does not match the template <code>' + 
               $('#currentTem_name').text() + '</code>');
    } else {
        // check if the current user has access to edit this template
        $.ajax({
            url: '/scripts/userCheckAccess',
            data: { table: 'tem', id: WM.delin.temId },
            success: function(data) {
                if (data.error) {
                    growl(data.errorText);
                } else {
                    createPointLabels();
                }
            }
        });
    }
}

function createPointLabels() {
    var ptLabels = [];
    WM.delinfunc = 'label';

    // create labels for each point in the default tem, add current name
    var $ol = $('#labelDialog ol');
    $ol.html('');
    $.each(WM.delin.tem, function (i) {
        $ol.append('<li><input name="' + i + '" type="text" value="' + WM.delin.tem[i].name + '" /></li>');
    });

    $('#labelDialog').dialog({
        title: "Set Point Labels",
        modal: false,
        height: 500,
        position: { my: 'right top', at: 'right bottom', of: $('#menubar') },
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
                        tem_id: WM.delin.temId,
                        labels: ptLabels
                    },
                    success: function(data) {
                        if (data.error) {
                            $('<div title="Error recording point labels" />').html(data.errorText).dialog();
                        } else {
                            quickhelp('Point labels recorded.', 3000);
                            //update template labels in defaultTem
                            $.each(ptLabels, function(i, name) {
                                WM.delin.tem[i].name = name;
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
        img = 'http://psychomorph.facelab.org/scripts/skyBioAccess?file=/' + WM.project.id + img;
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
        WM.eyeClicks = [0,0,0];
        WM.delinfunc = 'move';
        $('#pointer, #leftEye, #rightEye, #mouth').hide();

        WM.current.tem = [];
        setCurrentTem(13);

        /*
        // these points are duplicated in the points array
        WM.current.tem[0].x = tags.eye_left.x * imgWidth / 100;
        WM.current.tem[0].y = tags.eye_left.y * imgHeight / 100;
        WM.current.tem[1].x = tags.eye_right.x * imgWidth / 100;
        WM.current.tem[1].y = tags.eye_right.y * imgHeight / 100;
        WM.current.tem[2].x = tags.mouth_center.x * imgWidth / 100;
        WM.current.tem[2].y = tags.mouth_center.y * imgHeight / 100;
        WM.current.tem[3].x = tags.nose.x * imgWidth / 100;
        WM.current.tem[3].y = tags.nose.y * imgHeight / 100;
        */

        $.each(tags.points, function(i, p) {
            WM.current.tem[i].x = p.x * imgWidth / 100;
            WM.current.tem[i].y = p.y * imgHeight / 100;
        });

        drawTem();
    });
}

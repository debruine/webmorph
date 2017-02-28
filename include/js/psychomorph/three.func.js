function d3_load_image(filename, texturefile, $container) {
    if ($container === undefined) {
        $container = $('#threeD');
    }
    if (!WM.d3) {
        //initialise d3
        WM.d3 = new d3($container);
    }
    
    WM.faceimg = filename;
    if (WM.appWindow != 'delineate') {
        $('#showDelineate').click();
    } else {
        hashSet();
    }

    var faceimage = fileAccess(filename).replace(/\.(obj|bmp)$/, '');
    var obj = new d3Obj(faceimage, WM.d3);
    if (texturefile) {
        obj.texturefile = fileAccess(texturefile);
    }
    obj.load();
}

function d3($container) {
    var that = this;
    
    this.camera = null;
    this.scene = null;
    this.renderer = null;
    this.raycaster = new THREE.Raycaster();
    this.objects = [];
    this.zoom = 1.0;
    this.width = $container.width();
    this.height = $container.height();
    this.spin = false;
    this.lock = {
        x: false,
        y: false,
        z: false
    };
    this.onTouchDown = {x: 0, y:0}
    
    $container.data('d3', this);

    this.init = function () {
        $('.twoD').hide();
        $('.threeD').show();
        $container.html('').show();
        that.sizeToViewport();
        
        that.camera = new THREE.PerspectiveCamera(
            30, // fov
            that.width / that.height, // aspect
            0.1, // perspective near
            2000 // perspective far
        );
        
        that.camera.position.z = 1500;
        that.camera.zoom = that.zoom ;
        that.camera.updateProjectionMatrix();
        
        // add scene and lights
        that.scene = new THREE.Scene();
        var ambient = new THREE.AmbientLight( 0xffffff, 1 );
        that.scene.add( ambient );
        that.directionalLight = new THREE.DirectionalLight( 0xffffff, 0.5 );
        that.directionalLight.position.set( 0, 0.2, 1 );
        that.scene.add( that.directionalLight );

        
        // add crosshair for raycasting
        var sphereGeometry = new THREE.SphereGeometry( 4, 32, 32 );
        var sphereMaterial = new THREE.MeshBasicMaterial( { color: 0xff0000, shading: THREE.FlatShading } );
        that.crosshair = new THREE.Mesh( sphereGeometry, sphereMaterial );
        that.crosshair.visible = false;
        that.scene.add( that.crosshair );
        
        // add event listeners
        $container.get(0).addEventListener( 'touchmove', function( e ) {
            e.preventDefault();
            if (that.onTouchDown.x === null) { return false; }
            if (that.objects.length == 0) { return false; }

            var xchange = e.touches[ 0 ].clientX - that.onTouchDown.x;
            var ychange = e.touches[ 0 ].clientY - that.onTouchDown.y;
            
            if (e.touches.length === 1) {
                that.rotate('down', ychange * .01);
                that.rotate('right', xchange * .01);
            } else if (e.touches.length === 2) {
                var currentDist = Math.sqrt(
                    Math.pow(e.touches[ 0 ].clientX - e.touches[ 1 ].clientX, 2) + 
                    Math.pow(e.touches[ 0 ].clientY - e.touches[ 1 ].clientY, 2)
                );
                
                that.changeZoom( (currentDist - that.onTouchDown.distance) * 0.005 );
                
                that.onTouchDown.distance = currentDist;
            } else {
                that.move('down', ychange);
                that.move('right', xchange);
            }

            that.onTouchDown.x = e.touches[ 0 ].clientX;
            that.onTouchDown.y = e.touches[ 0 ].clientY;
        }, false);
        
        $container.get(0).addEventListener( 'touchstart', function( e ) {
            that.onTouchDown.x = e.touches[0].clientX;
            that.onTouchDown.y = e.touches[0].clientY;
            
            if (e.touches.length == 2) {
                that.onTouchDown.distance = Math.sqrt(
                    Math.pow(e.touches[ 0 ].clientX - e.touches[ 1 ].clientX, 2) + 
                    Math.pow(e.touches[ 0 ].clientY - e.touches[ 1 ].clientY, 2)
                );
            }
    
            that.clickObject(e.touches[0].clientX, e.touches[0].clientY);
        }, false);
        
        $container.get(0).addEventListener( 'touchend', function( e ) {
            for (var i = 0; i < that.objects.length; i++) {
                that.objects[i].clickactive = false;
            }
            that.onTouchDown.x = null;
            that.onTouchDown.y = null;
        })
        
        $container.get(0).addEventListener('wheel', function(e) {
            that.changeZoom(- e.deltaY*0.05);
            e.preventDefault();
        }, false);
        
        $(document).on('contextmenu', '#threeD', function(e) {
            var item_info = [];
    
            e.stopPropagation();
            
            var mousePosition = that.getMousePosition( $container.get(0), e.clientX, e.clientY );
            $('#imgname').html('');
        	for (var i = 0; i < that.objects.length; i++) {
        	    var intersects = that.getIntersects( mousePosition, that.objects[i].mesh );
                if (intersects.length) { 
                    item_info = [
                        {
                            name: 'Delete ' + that.objects[i].name,
                            func: function() {
                                $('.context_menu').remove();
                                that.objects[i].remove();
                            }
                        }
                    ];
                
                    context_menu(item_info, e);
                    return true;
                }
            }
        });
        
        $container.bind( 'mousedown', function( e ) {
            if (e.metaKey) {
                // move
                cursor('move');
            } else if (e.altKey) {
                // rotate z-axis
                //cursor('');
            } else if (e.shiftKey) {
                // mark a raycast point
                $('#footer-text').html(
                    'Point: ' +
                    Math.round(that.crosshair.position.x*100)/100 + ", " +
                    Math.round(that.crosshair.position.y*100)/100 + ", " +
                    Math.round(that.crosshair.position.z*100)/100 + ")"
                );
            } else {
                // rotate x or y axes
                cursor('grabbing');
            }
            that.onTouchDown.x = e.clientX;
            that.onTouchDown.y = e.clientY;
            that.clickObject(e.clientX, e.clientY);
        }).bind( 'mouseup', function( e ) {
            cursor('default');
            for (var i = 0; i < that.objects.length; i++) {
                that.objects[i].clickactive = false;
            }
            that.onTouchDown.x = null;
            that.onTouchDown.y = null;
        }).bind( 'mousemove', function( e ) {
            e.preventDefault();
            if (that.objects.length == 0) { return false; }
            
            if (!WM.pageEvents.mousebutton['1']) {
        
                var mousePosition = that.getMousePosition( $container.get(0), e.clientX, e.clientY );
                $('#imgname').html('');
            	for (var i = 0; i < that.objects.length; i++) {
            	    var intersects = that.getIntersects( mousePosition, that.objects[i].mesh );
                    if (intersects.length) { 
                        if (e.shiftKey) {
                            that.crosshair.position.copy(intersects[0].point);
                        }
                        $('#imgname').html('<span class="obj-button">' + 
                                that.objects[i].button.text() + '</span> ' + 
                                that.objects[i].name);
                    }
                }
                
                return false;
            }
            
            if (that.onTouchDown.x === null) { return false; }
            
            // drag functions
            
            var xchange = e.clientX - that.onTouchDown.x;
            var ychange = e.clientY - that.onTouchDown.y;
            
            if (e.metaKey) {
                that.move('down', ychange);
                that.move('right', xchange);
            } else if (e.altKey) {
                that.rotate('z', Math.max(xchange, ychange) * -.01);
            } else { 
                that.rotate('down', ychange * .01);
                that.rotate('right', xchange * .01);
            }
            that.onTouchDown.x = e.clientX;
            that.onTouchDown.y = e.clientY;
        }).bind('dblclick', function(e) {
        	e.preventDefault();
        
        	var mousePosition = that.getMousePosition( $container.get(0), e.clientX, e.clientY );
        	for (var i = 0; i < that.objects.length; i++) {
        	    var intersects = that.getIntersects( mousePosition, that.objects[i].mesh );
                if (intersects.length) { 
                    that.objects[i].toggle_select();
                }
            }
        });
        
        $(document).bind('keydown', function(e) {
            if ($container.filter(':visible').length) {
                if (e.which == KEYCODE.shift) {
                    that.crosshair.visible = true;
                    cursor('none');
                } else if (e.which == KEYCODE.t) {
                    that.toggle_texture();
                } else if (e.which == KEYCODE.w) {
                    that.toggle_wireframe();
                } else if (e.which == KEYCODE.s) {
                    that.toggle_light();
                }
            }
        }).bind('keyup', function(e) {
            if ($container.filter(':visible').length) {
                if (e.which == KEYCODE.shift) {
                    console.log('hide crosshair');
                    that.crosshair.visible = false;
                    cursor('default');
                }
            }
        });
        
        that.stats = new THREEx.RendererStats();
        that.stats.domElement.style.position = 'absolute';
        that.stats.domElement.style.top = '0px';
        that.stats.domElement.style.right = '0px';
        $container.get(0).appendChild(that.stats.domElement);
        $(that.stats.domElement).hide();

              
        // renderer
        that.renderer = new THREE.WebGLRenderer({preserveDrawingBuffer: true});
        that.renderer.setPixelRatio( window.devicePixelRatio );
        that.renderer.setSize( $container.width(), $container.height() );
        that.renderer.context.getShaderInfoLog = function () { return '' };
        $container.append( that.renderer.domElement );
        //that.render();
        that.animate();
    };
    
    this.updateObjList = function(obj) {
        var $buttons = $('#obj_switcher button');
        
        // make sure order is correct
        $buttons.each( function(i) {
            $(this).text(i+1);
        });
        
        if (obj) {
            var $obj_button = $('<button />')
                .text($buttons.length + 1)
                .attr('title', obj.name)
                .data('object', obj)
                .button()
                .addClass('selected');
                
            obj.button = $obj_button;
            $('#obj_switcher').append($obj_button);
        }
        
        if (that.objects.length == 2) {
            $('#d3_morph').button('enable');
        } else {
            $('#d3_morph').button('disable');
        }
    }
    
    this.clickObject = function(x, y) {
        var mousePosition = that.getMousePosition( $container.get(0), x, y );
    	for (var i = 0; i < that.objects.length; i++) {
    	    var intersects = that.getIntersects( mousePosition, that.objects[i].mesh );
            if (intersects.length) { 
                that.objects[i].clickactive = true;
            } else {
                that.objects[i].clickactive = false;
            }
        }
    }
    this.getMousePosition = function( dom, x, y ) {
    	var rect = dom.getBoundingClientRect();
    	var mousePosition = new THREE.Vector2(
        	( x - rect.left ) / rect.width,
        	( y - rect.top ) / rect.height
    	);
    	
    	return mousePosition;
    };
    this.getIntersects = function( point, object ) {
        var mouse = new THREE.Vector2();
    	mouse.set( ( point.x * 2 ) - 1, - ( point.y * 2 ) + 1 );
        that.raycaster.setFromCamera( mouse, that.camera );
    	return that.raycaster.intersectObject( object, true );
    };

    this.sizeToViewport = function(w,h) {
        if (w !== undefined && h !== undefined) {
            that.height = h;
            that.width = w;
            $container.height(that.height);
            $container.width(that.width);
        } else {
            $container.css('width', '100%');
            that.height = window.innerHeight-$container.offset().top-$('#footer').height() - 20;
            that.width = $container.width();
            $container.height(that.height);
        }
        
        if (that.camera) {
            that.camera.aspect = that.width/that.height;
            that.camera.updateProjectionMatrix();
        }

        if (that.renderer) {
            that.renderer.setSize(that.width, that.height);
        }
    };
    
    this.resize = function(w, h) {
        
        
        if (that.camera) {
            that.camera.aspect = that.width/that.height;
            that.camera.updateProjectionMatrix();
        }

        if (that.renderer) {
            that.renderer.setSize(that.width, that.height);
        }
    };
    
    this.reset = function() {
        $('body').removeClass('hologram');
        if (that.objects.length) {
            that.objects[0].select(false);
            that.remove();
            that.objects[0].select(true);
            that.center();
            that.fitsize();
        }
    }
    
    this.add = function(obj) {
        that.scene.add(obj.mesh);
        that.objects.push(obj);
        that.updateObjList(obj);
    }
    
    this.remove = function(obj_i) {
        console.log('d3.remove()');
        if (obj_i !== undefined) {
            //that.scene.remove(that.objects[obj_i].mesh);
            //that.objects.splice(obj_i, 1);
            that.objects[obj_i].remove();
        } else {
            // iterate backward for splicing
            for (var i = that.objects.length - 1; i > -1 ; i--) {
                if (that.objects[i].selected) {
                    that.objects[i].remove();
                    //that.scene.remove(that.objects[i].mesh);
                    //that.objects.splice(i, 1);
                }
            }
        }
    };

    this.toggle_wireframe = function () {
        var objects = that.selectedObjs();
        for (var i = 0; i < objects.length; i++) {
            objects[i].toggle_wireframe();
        }
    };
    
    this.toggle_texture = function() {
        var objects = that.selectedObjs();
        for (var i = 0; i < objects.length; i++) {
            objects[i].toggle_texture();
        }
    };
    
    this.set_hue = function() {
        var objects = that.selectedObjs();
        var hue = Math.random();
        for (var i = 0; i < objects.length; i++) {
            objects[i].hue = hue;
            objects[i].material.color.setHSL(hue,.25,.50);
            objects[i].material.map = null;
        }
    }
    
    this.toggle_light = function() {
        that.directionalLight.intensity = (that.directionalLight.intensity + 0.5) % 1.0;
        if (that.directionalLight.intensity) {
            $('#d3_light').removeClass('off');
        } else {
            $('#d3_light').addClass('off');
        }
    }
    
    this.selectedObjs = function() {
        var objs = [];
        
        // add in clickactive objects
        for (var i = 0; i < that.objects.length; i++) {
            if (that.objects[i].selected && that.objects[i].clickactive) {
                objs.push(that.objects[i]);
            }
        }
        if (!objs.length) {
            // no clickactive objects, so include all selected object
            for (var i = 0; i < that.objects.length; i++) {
                if (that.objects[i].selected) {
                    objs.push(that.objects[i]);
                }
            }
        }
        
        return objs;
    };
    
    this.center = function() {
        var objects = that.selectedObjs();
        that.sizeToViewport();
        
        for (var i = 0; i < objects.length; i++) {
            objects[i].rotation = [0,0,0];
            objects[i].position = [0,0,0];
        }
        
        $('#footer-text').html("Centered");
    };
    
    this.changeZoom = function(zoomchange) {
        that.zoom += (zoomchange*that.zoom);  
        that.zoom = Math.max(0.1, that.zoom);
        that.zoom = Math.min(1000, that.zoom);
        var scale = 1/that.zoom;
        that.crosshair.scale.set(scale, scale, scale);
        $('#footer-text').html("Zoom: " + Math.round(that.zoom*100)/100);
    };
    
    this.zoomoriginal = function() {
        that.zoom = 1.0;
        
        $('#footer-text').html("Zoom: 1.0");
    };
    
    this.scale = function(step) {
        var objects = that.selectedObjs();
        if (step === undefined) { step = 0.05; }
        
        for (var i = 0; i < objects.length; i++) {
            objects[i].scale += step;
            
            // don't allow negative scale
            if (objects[i].scale < 0) {
                objects[i].scale = 0;
            }
        }
    };
    
    this.fitsize = function() {
        that.sizeToViewport();
        var availableWidth = $container.width() * .8;
        var availableHeight = $container.height() * .8;
        var objects = that.selectedObjs();
        var newHeight, resize, newZoom = 100;

        // http://stackoverflow.com/questions/14614252/how-to-fit-camera-to-object
        
        for (var i = 0; i < objects.length; i++) {
            // calculate new height if fitting width
            newHeight = objects[i].originalSize.y * availableWidth / objects[i].originalSize.x;
        
            resize = (newHeight >= availableHeight) ?
                 availableHeight/objects[i].originalSize.y :  // fit to available height
                 newHeight/objects[i].originalSize.y;    // fit to available width
            
            newZoom = Math.min(resize, newZoom);
        }
        that.zoom = newZoom;
        $('#footer-text').html("Fit to screen, Zoom: " + Math.round(that.zoom*100)/100);
    };
    
    this.toggle_lock = function(direction) {
        that.lock[direction] = !that.lock[direction];
        $('#d3_lock_' + direction).toggleClass('locked', that.lock[direction]);
    }
    
    this.rotate = function(direction, step) {
        var display = "Rotation ";
        var objects = that.selectedObjs();
        if (step === undefined) { step = 0.01; }
        step = step / Math.sqrt(that.zoom);
        
        for (var i = 0; i < objects.length; i++) {
            if (!that.lock.y && (direction == 'left' || direction == 'x')) {
                objects[i].rotation[1] += step;
            } else if (!that.lock.y && direction == 'right') {
                objects[i].rotation[1] -= step;
            } else if (!that.lock.x && direction == 'up') {
                objects[i].rotation[0] -= step;
            } else if (!that.lock.x && (direction == 'down' || direction == 'y')) {
                objects[i].rotation[0] += step;
            } else if (!that.lock.z && direction == 'z') {
                objects[i].rotation[2] += step;
            }
            display += '<span class="obj-button">' + objects[i].button.text() + '</span> (' + 
                + Math.round(objects[i].rotation[0]*100)/100 + ", "
                + Math.round(objects[i].rotation[1]*100)/100 + ", "
                + Math.round(objects[i].rotation[2]*100)/100 + ") ";
        }
        $('#footer-text').html(display);
        
    };
    
    this.move = function(direction, step) {
        var objects = that.selectedObjs();
        var display = "Position ";
        if (step === undefined) { step = 10; }
        step = step / that.zoom;
        
        for (var i = 0; i < objects.length; i++) {
            if (direction == 'left') {
                objects[i].position[0] -= step;
            } else if (direction == 'right') {
                objects[i].position[0] += step;
            } else if (direction == 'up') {
                objects[i].position[1] -= step;
            } else if (direction == 'down') {
                objects[i].position[1] += step;
            }
            display += '<span class="obj-button">' + objects[i].button.text() + '</span> (' + 
                + Math.round(objects[i].position[0]*100)/100 + ", "
                + Math.round(objects[i].position[1]*100)/100 + ", "
                + Math.round(objects[i].position[2]*100)/100 + ") ";
        }
        $('#footer-text').html(display);
    };
    
    this.hologram = function(e) {
        var offset = 200;
        var imgsize = 170;
        
        if (!that.objects.length) { 
            console.log('No selected objects');
            return false; 
        }
        
        growl("Double-click the centre to quit hologram mode", 2000);
        
        $('body').addClass('hologram');
        
        $('<div id="holocancel" />').css({
            top: that.height/2 - 25,
            left: that.width/2 - 25
        }).on('doubletap', function() {
            $(this).remove();
            that.reset();
        }).appendTo($container);
        
        // get rid of any extra objects
        $.each(that.objects, function(i,v) { v.select(i>0);});
        that.remove();
        that.objects[0].select(true);
    
        // duplicate first object x3
        var obj;
        for (var i = 1; i < 4; i++) {
            obj = that.objects[0].clone('holo' + (i+1));
            that.add(obj);
        }
        
        that.zoom = imgsize / obj.originalSize.y;
    
        for (var i = 0; i < 4; i++) {
            obj = that.objects[i];
    
            //obj.scale = imgsize / obj.originalSize.y;
            
            if (i == 0) { // top
                obj.position = [0, -offset, 0];
            } else if (i == 1) { // bottom
                obj.position = [0, offset, 0];
            } else if (i == 2) { // right
                obj.position = [offset, 0, 0];
            } else if (i == 3) { // left
                obj.position = [-offset, 0, 0];
            }
            
            if (e.metaKey) {
                if (i == 0) { // top
                    obj.rotation = [0,Math.PI,-Math.PI];
                } else if (i == 1) { // bottom
                    obj.rotation = [0,0,0];
                } else if (i == 2) { // right
                    obj.rotation = [Math.PI/2,0,Math.PI/2];
                } else if (i == 3) { // left
                    obj.rotation = [Math.PI/2,0,-Math.PI/2];
                }
            } else {
                if (i == 0) { // top
                    obj.rotation = [0,Math.PI,0];
                } else if (i == 1) { // bottom
                    obj.rotation = [0,0,-Math.PI];
                } else if (i == 2) { // right
                    obj.rotation = [Math.PI/2,0,-Math.PI/2];
                } else if (i == 3) { // left
                    obj.rotation = [Math.PI/2,0,Math.PI/2];
                }
            }
        }
    }
    
    this.morph = function() {
        spinner();
        $('#footer-text').html('Computing morph trajectory');
        
        var errorMsg = function(msg) {
            spinner(false);
            $('#footer-text').html('');
            if (msg) { growl(msg); }
            return false;
        }
        
        if (that.objects.length !== 2) {
            return errorMsg("You need to load exactly two objects to morph.");
        }
        
        var $theSlider = $('<div />').slider({
            value: .5,
            min: -0.5,
            max: 1.5,
            step: 0.01
        }).prepend('<span class="percent">50%</span>');;
        
        setTimeout( function() {
            var geometry1, geometry2;

            if (that.objects[0].mesh.geometry.isBufferGeometry) {
                geometry1 = new THREE.Geometry().fromBufferGeometry( that.objects[0].mesh.geometry );
            } else if (that.objects[0].mesh.geometry.isGeometry) {
                geometry1 = that.objects[0].mesh.geometry.clone();
            } else {
                return errorMsg("There was a problem with the geometry in " + that.objects[0].name );
            }
            
            if (that.objects[1].mesh.geometry.isBufferGeometry) {
                geometry2 = new THREE.Geometry().fromBufferGeometry( that.objects[1].mesh.geometry );
            } else if (that.objects[1].mesh.geometry.isGeometry) {
                geometry2 = that.objects[1].mesh.geometry.clone();
            } else {
                return errorMsg("There was a problem with the geometry in " + that.objects[1].name );
            }
            
            if (geometry1.vertices.length != geometry2.vertices.length) {
                return errorMsg("The objects don't have the same vertices");
            }
            
            $('#footer-text').html('Processing ' + geometry1.vertices.length + ' vertices');
            that.objects[0].position = [-200,0,0];
            that.objects[1].position = [+200,0,0];
            that.objects[0].rotation = [0,-0.1,0];
            that.objects[1].rotation = [0,+0.1,0];
            
            setTimeout( function() {
                geometry1.morphTargets.push( { name: "morph", vertices: geometry2.vertices } );
                geometry1.computeMorphNormals();
                //geometry1.morphNormals[0].faceNormals = [];
                //geometry1.morphNormals[0].vertexNormals = [];
        
                var o = new d3Obj('morph', that);
                o.loadGeometry(geometry1);
                o.material.morphTargets = true;
                o.material.morphNormals = true;
                o.mesh.morphTargetInfluences[0] = 0.5;

                $theSlider.on('slide change', function(event, ui) {
                    o.mesh.morphTargetInfluences[0] = ui.value;
                    $(this).find('.percent').text(Math.round(ui.value*100) + "%");
                });
                
                that.add(o);
                $('#footer-text').html('Morph created');
                spinner(false);
            }, 500);
        }, 0);
        
        return $theSlider;
    }
    
    this.animate = function() {
        requestAnimationFrame( that.animate );
        that.render();
        that.stats.update(that.renderer);
    };
    
    this.x_vector = new THREE.Vector3(1,0,0);
    this.y_vector = new THREE.Vector3(0,1,0);
    this.z_vector = new THREE.Vector3(0,0,1);
    
    this.render = function() {
        var smooth = 0.25; // smooths out jumpy movements (1=most jumpy & precise, 0.05 = very smooth & laggy)
        if (that.spin) {
            var theSpin = (new Date().getTime() % 5000 / 5000) * (2 * Math.PI);
        }
        
        // object rendering
        for (var i=0; i < that.objects.length; i++) {
            // avoids jumpy movements to new views
            var obj = that.objects[i];
            if (obj.mesh) {
                
                obj.mesh.position.x += ( obj.position[0] - obj.mesh.position.x ) * smooth;
                obj.mesh.position.y += ( - obj.position[1] - obj.mesh.position.y ) * smooth;
                obj.mesh.position.z += ( obj.position[2] - obj.mesh.position.z ) * smooth;
                if (that.spin) {
                    //obj.object.rotation.y = theSpin;
                    obj.mesh.rotateOnAxis(that.y_vector, Math.PI/200);
                    obj.mesh.rotation[0] = obj.mesh.rotation.x;
                    if (obj.material.map == null) {
                        obj.material.color.setHSL((obj.material.color.getHSL().h + 1/200)%1,.25,.50);
                    }
                    $('#footer-text').html(Math.round(obj.mesh.rotation.x*100)/100);
                } else {
                    obj.mesh.rotation.x += ( obj.rotation[0] - obj.mesh.rotation.x ) * smooth;
                    obj.mesh.rotation.y += ( - obj.rotation[1] - obj.mesh.rotation.y ) * smooth;
                    obj.mesh.rotation.z += ( obj.rotation[2] - obj.mesh.rotation.z ) * smooth;
                }
                /*
                if (Math.abs(obj.scale - obj.object.scale.x) > .001) {
                    var scale = obj.object.scale.x + (obj.scale - obj.object.scale.x) * .05;
                    obj.object.scale.set(scale, scale, scale);
                }
                */
            }
        }
        // camera rendering
        if (that.camera && Math.abs(that.zoom - that.camera.zoom) > .001) {
            that.camera.zoom += ( that.zoom - that.camera.zoom ) * smooth;
            that.camera.updateProjectionMatrix();
        }

        that.renderer.render( that.scene, that.camera );
        
        if (that.captureFrames && that.frames.length < 600) {
            that.frames.push(that.renderer.domElement.toDataURL());
            console.log(that.frames.length);
        }
    };
    
    this.init();
}

function d3Obj(filename, D3) {
    var that = this;
    
    this.name = urlToName(filename);
    this.parent = D3;
    this.mesh = null;
    this.filename = filename;
    this.texturefile = null;
    this.button = null;
    this.position = [0,0,0];
    this.rotation = [0,0,0];
    this.scale = 1;
    this.selected = true;
    this.clickactive = false;
    this.texture = new THREE.Texture();
    this.hue = null;
    this.material = new THREE.MeshPhongMaterial({
        color: 0x999999,
        specular: 0x111111,
        reflectivity: 0.3,
        shininess: 20,
        shading: THREE.SmoothShading,
        morphTargets: false,
        morphNormals: false,
        wireframe: false,
        side: THREE.FrontSide,
        wireframeLinewidth: 0.5,
        transparent: true,
        opacity: 1.0
    });
    
    this.clone = function(name) {
        var clone = new d3Obj('clone', D3);
        clone.mesh = that.mesh.clone();
        clone.texture = that.texture;
        clone.originalSize = that.originalSize;
        clone.material = clone.mesh.material;
        return clone;
    }
    
    this.load = function(onLoadCallback, params) {
        if (onLoadCallback === undefined || typeof(onLoadCallback) !== 'function') {
            onLoadCallback = function() { };
        }
        
        if (params === undefined) { params = []; }
        
        $('#footer-text').html('Loading ' + that.name);
        D3.sizeToViewport();
        
        // defaults to bmp with same name
        that.loadTexture(that.texturefile || that.filename + '.bmp');
        that.loadObj(onLoadCallback, params);
    };
    
    this.toggle_texture = function() {
        if (that.texture && that.texture.image) {
            if (that.material.map) {
                that.material.map = null;
                that.material.shininess = 20;
                if (that.hue) {
                    that.material.color.setHSL(that.hue, .25, .50);
                } else {
                    that.material.color.setHex(0x999999);
                }
            } else {
                that.material.map = that.texture;
                that.material.shininess = 0;
                that.material.color.setHex(0xffffff);
            }
            that.material.needsUpdate = true;
        }
    };
    
    this.toggle_wireframe = function() {
        that.material.wireframe = !that.material.wireframe;
        that.material.needsUpdate = true;
    };
    
    this.fitsize = function() {
        var availableWidth = D3.width * 0.9;
        var availableHeight = D3.height * 0.9;
        var fitWidth = availableWidth*that.originalSize.y/that.originalSize.x;
        
        var resize = (fitWidth >= availableHeight) ?
                 availableHeight/that.originalSize.y :  // fit to available height
                 availableWidth/that.originalSize.x;          // fit to available width
        
        that.scale = resize;
    };
    
    this.select = function(sel) {
        if (sel === undefined) { sel = true; }
        
        that.selected = sel;
        
        if (that.button && sel) {
            that.button.addClass('selected');
        } else if (that.button) {
            that.button.removeClass('selected');
        }
        that.material.opacity = sel ? 1.0 : 0.25;
        that.material.needsUpdate = true;
    };
    
    this.toggle_select = function() {
        that.select(!that.selected);
    };
    
    this.manager = new THREE.LoadingManager();
    this.manager.onProgress = function ( item, loaded, total ) {
        console.log( item, loaded, total );
    };
    
    this.loadTexture = function(theFileName) {
        // load the texture
        $('#d3_texture').button('disable');
        var loader = new THREE.ImageLoader( that.manager );
        loader.load( theFileName , function ( image ) {
            $('#d3_texture').button('enable');
            that.texture.image = image;
            that.texture.needsUpdate = true;
            return true;
        });
    };
    
    this.remove = function() {
        console.log("Destroying d3Obj: " + that.name);

        for ( var i = that.parent.objects.length - 1; i > -1; i-- ) {
            if ( that.parent.objects[i] == that ) {
                that.parent.objects.splice( i, 1 );
            }
        }
        
        that.parent.scene.remove(that.mesh);
        that.mesh.geometry.dispose();
        that.material.dispose();
        that.texture.dispose();
        that.button.remove(); 
        that.parent.updateObjList();
        
        $('#footer-text').html(that.name + " deleted");
        $('#imgname').html('');
        that = null;
    }
    
    this.loadGeometry = function(geometry) {
        //that.object = new THREE.Object3D();
        if (that.mesh == null) {
            that.mesh = new THREE.Mesh(geometry, that.material);
        } else {
            that.mesh.geometry = geometry;
        }

        var box = new THREE.Box3().setFromObject( that.mesh );
        that.originalSize = box.getSize();
    }
    
    this.loadObj = function(onLoadCallback, params) {
        spinner();
        
        var loader = new THREE.OBJLoader( that.manager );
        loader.load( that.filename + '.obj',
            // onLoad
            function ( object ) {
                console.log('object loaded');
                
                if (object.children[0] instanceof THREE.Mesh) {
                    that.mesh = object.children[0];
                    that.mesh.material = that.material;
                }
                
                var box = new THREE.Box3().setFromObject( that.mesh );
                that.originalSize = box.getSize();

                // this re-sets the mesh position
                that.mesh.geometry.center();
                that.mesh.geometry.verticesNeedUpdate = true;

                D3.add( that );
                
                spinner(false);
                $('#imgname').html(that.name);
                $('#footer-text').html('');
                
                if (onLoadCallback !== undefined && typeof(onLoadCallback) === 'function') {
                    if (params === undefined) { params = []; }
                    onLoadCallback.apply(this, params);
                }
            },
            // onProgress
            function ( xhr ) {
                if ( xhr.lengthComputable ) {
                    var percentComplete = Math.round(xhr.loaded / xhr.total * 1000)/10;
                    $('#footer-text').html(that.name + " [" + percentComplete + "%]");
                }
            },
            // onError
            function ( xhr ) {
                spinner(false);
                $('#footer-text').html('Error loading ' + that.name);
            } 
        );
    };
    
    this.recenter = function(v1, v2) {
        var newCenter = { x:0, y:0, z:0 };
        // The origin of the co-ordinate system was the point between the inner 
        // corners of the eyes (mid-endocanthion) (men),which, as previous 
        // research has shown, is the most stable facial landmark 
        // (Toma et al., 2009; Zhurov et al., 2010).
        
        if (v1 === undefined) {
            var box = new THREE.Box3().setFromObject( that.mesh );
            newCentre = box.getCenter(); 
        } else if (v2 === undefined) {
            newCentre = v1;
        } else {
            // calculate midpoint of two vectors
            var newCentre = {
                x: (v1.x + v2.x) / 2,
                y: (v1.y + v2.y) / 2,
                z: (v1.z + v2.z) / 2
            }
        }
        
        that.mesh.position.x = newCenter.x;
        that.mesh.position.y = newCenter.y;
        that.mesh.position.z = newCenter.z;
        that.mesh.position.multiplyScalar(  -1 );
        that.mesh.geometry.verticesNeedUpdate = true;
    }
    
    return this;
}

$('#d3_demo').on('doubletap', function(e) {
    e.preventDefault();
    if ($(this).data('d3') === undefined) {
        $('#footer-text').html('Initializing 3D demo');
        
        $(this).html('').removeClass('feature').css({
            'background-color': 'black'
        }).height($(this).width());
        
        var demo = new d3($(this));
        //var menuheight = $('#menubar:visible').length ? $('#menubar').height() : 0;
        //$('html, body').scrollTop($(this).offset().top - menuheight - 10);
        $('#d3_demo').data('d3').sizeToViewport(
            $('#d3_demo').width(), 
            $('#d3_demo').width()/1.618
        );

        var obj = new d3Obj('/include/3d/female_avg', demo);
        obj.texturefile = '/include/3d/female_avg.jpg';
        obj.position = [-120,0,0];

        var obj2 = new d3Obj('/include/3d/male_avg', demo);
        obj2.texturefile = '/include/3d/male_avg.jpg';
        obj2.position = [120,0,0];
        
        obj.load(function(){
            obj.toggle_select = function() {};
            obj2.load(function() {
                obj2.toggle_select = function() {};
                $('#d3_demo_extras').show();
                $('#d3_demo').data('d3').sizeToViewport(
                    $('#d3_demo').width(), 
                    $('#d3_demo').width()/1.618
                );
            });
        });

        demo.zoom = 2.0;
    } else {
        $(this).data('d3').sizeToViewport($(this).width(), $(this).width()/1.618);
        $(this).data('d3').toggle_texture();
        //$(this).data('d3').toggle_wireframe();
    }
});

$('#d3_demo_morph').click( function() {
    $('#d3_demo').data('d3').sizeToViewport(
        $('#d3_demo').width(), 
        $('#d3_demo').width()/1.618
    );
                
    var $theSlider = $('#d3_demo').data('d3').morph();
    
    $(this).remove();
    $('#d3_demo_extras').text($('#d3_demo_extras').text() + 'live morph in 3D');
    
    $theSlider.slider({
      step: 0.1
    }).css({
        position: 'relative',
        top: '0',
        right: '0',
        margin: '0.5em auto',
        width: '75%',
    }).appendTo('#d3_demo_extras');
    
});

/*
$('<a />').text('video').appendTo('#delin_toolbar').click(function() {
    WM.d3.captureFrames = !WM.d3.captureFrames;
    if (WM.d3.captureFrames) {
        WM.d3.frames = [];
        WM.d3.sizeToViewport(300,400);
    } else {
        WM.d3.sizeToViewport();
        var $stills = $('<div />');
        for (var i = 0; i < WM.d3.frames.length; i++) {
            $('<img />').attr('src', WM.d3.frames[i]).appendTo($stills);
        }
        $stills.find('img').css('width', '50px');
        $stills.appendTo('body');
    }
});
*/

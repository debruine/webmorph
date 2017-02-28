<div id='delineateInterface' class='interface'>

    <!-- images to be used in delineation -->
    <img id='cross' src='/include/images/delin/cross.php' />
    <img id='selected_cross' src='/include/images/delin/cross_selected.php' />
    <img id='hover_cross' src='/include/images/delin/cross_hover.svg' />
    <img id='pointer' src='/include/images/delin/pointer.svg' />
    
    <img id='leftEye' src='/include/images/delin/3delin.svg' />
    <img id='rightEye' src='/include/images/delin/3delin.svg' />
    <img id='mouth' src='/include/images/delin/3delin.svg' />
    
    <div id="selectBox"></div>
    <!-- toolbar -->
    <div id="toolbar_switcher"></div>
    <div id="delin_toolbar" class="toolbar">
        <span class='buttonset'>
            <button id='delin_close' class='ui-dialog-titlebar-close'>Close Toolbar</button>
        </span>
        <span class='buttonset'>
            <button class="wm" id="delin_zoomout">Zoom Out  (&#8984;-)</button>
            <button class="wm" id="delin_zoomin">Zoom In (&#8984;+)</button>
            <button class="wm" id="delin_zoomoriginal">Original Size (&#8984;0)</button>
            <button class="wm" id="delin_center" class="threeD">Center</button>
            <button class="wm" id="delin_fitsize">Fit to Window (&#8984;M)</button>
        </span>
        <span class='buttonset'>
            <button class="wm" id="delin_delete">Delete (&#8984;&#x232b;)</button>
        </span>
        <span class='buttonset twoD'>
            <button id="delin_undo">Undo (&#8984;Z)</button>
            <button id="delin_redo">Redo (&#8679;&#8984;Z</span>)</button>
        </span>
        <span class='buttonset twoD'>
            <button id="delin_refresh">Refresh (&#8984;R)</button>
            <button id="delin_save">Save (&#8984;S)</button>
        </span>
        <span class='buttonset twoD'>
            <button id="delin_prev">Previous Image (&#8984;&#8592;)</button>
            <button id="delin_next">Next Image (&#8984;&#8594;)</button>
        </span>
        <span class='buttonset twoD'>
            <button id="showDelinHelp">Show Delineation Help</button>
        </span>
        <span class="twoD">
            <div id='imgsize'></div>
            <span id='size_value'></span>&nbsp;px
            <span id='quickhelp'></span>
        </span>
        <span class="buttonset threeD">
            <!--<button class="wm" id="d3_sethue">Face Color</button>-->
            <button class="wm" id="d3_wireframe">Wireframe</button>
            <button class="wm" id="d3_texture">Texture</button>
            <button class="wm" id="d3_light">Shading</button>
        </span>
        
        <span class="buttonset threeD">
            <button class="wm" id="d3_lock_x">X</button>
            <button class="wm" id="d3_lock_y">Y</button>
            <!--<button class="wm" id="d3_lock_z">Z</button>-->
        </span>
        
        <span class="buttonset threeD">
            <button class="wm" id="d3_morph">Morph</span>
            <button class="wm" id="d3_spin">Disco</button>
            <button class="wm" id="d3_hologram">Hologram</button>
            <button class="wm" id="d3_debug">Debug</button>
        </span>
        <span class="threeD" id="obj_switcher">
        </span>
    </div>
    
    <div id="mask_builder_box">
        <label for="custom_mask_builder" class="ui-hidden-accessible">Custom Mask:</label>
        <textarea id="custom_mask_builder"></textarea>
        <p>Select template points to add them to the mask. Separate points with commas, lines with semicolons, and mask areas with colons (e.g. 18,19,20,21,22 ; 22,30,29,28,18 : 23,24,25,26,27 ; 27,33,32,31,23)</p>
    </div>

    <!-- image -->
    <div id='delin' class='twoD'><canvas id='template' width="300" height="400"></canvas></div>
    <div id="threeD" class='threeD'></div>
</div>

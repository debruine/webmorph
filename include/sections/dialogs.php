<!-- !- prefDialog -->
<div id='prefDialog' class='modal' title='Preferences'>
    <ul>
        <li><a href="#prefDialog-1">Personal</a></li>
        <li><a href="#prefDialog-2">Images</a></li>
        <li><a href="#prefDialog-3">Templates</a></li>
    </ul>
    <table id='prefDialog-1'>
        <tr>
            <td></td>
            <td><label for='pref_email'>Email</label></td>
            <td><input type='email' id='pref_email' value='' /></td>
        </tr>
        <tr>
            <td></td>
            <td><label for='pref_password'>Password</label></td>
            <td><input type='text' id='pref_password' /></td>
        </tr>
        <tr>
            <td></td>
            <td><label for='pref_firstname'>First Name</label></td>
            <td><input type='text' id='pref_firstname' /></td>
        </tr>
        <tr>
            <td></td>
            <td><label for='pref_lastname'>Last Name</label></td>
            <td><input type='text' id='pref_lastname' /></td>
        </tr>
        <tr>
            <td></td>
            <td><label for='pref_org'>Organisation</label></td>
            <td><input type='text' id='pref_org' /></td>
        </tr>
        <tr>
            <td></td>
            <td><label for='pref_sex'>Sex</label></td>
            <td><div id='pref_sex'>
                <input type='radio' id='pref_sex_female' name='pref_sex' value='female' />
                <label for='pref_sex_female'>female</label>
                <input type='radio' id='pref_sex_male' name='pref_sex' value='male' />
                <label for='pref_sex_male'>male</label>
                <input type='radio' id='pref_sex_other' name='pref_sex' value='other' />
                <label for='pref_sex_other'>other</label>
            </div></td>
        </tr>
        <tr>
            <td></td>
            <td><label for='pref_use'>I use WebMorph for</label></td>
            <td><div id='pref_use'>
                <input type='checkbox' id='pref_use_research' name='pref_use_research' value='research' />
                <label for='pref_use_research'>research</label><br>
                <input type='checkbox' id='pref_use_school' name='pref_use_school' value='school' />
                <label for='pref_use_school'>school</label><br>
                <input type='checkbox' id='pref_use_business' name='pref_use_business' value='business' />
                <label for='pref_use_business'>business</label><br>
                <input type='checkbox' id='pref_use_art' name='pref_use_art' value='art' />
                <label for='pref_use_art'>art</label><br>
                <input type='checkbox' id='pref_use_personal' name='pref_use_personal' value='personal' />
                <label for='pref_use_personal'>personal</label>
            </div></td>
        </tr>
        <tr>
            <td><span class='tinyhelp' data-topic='default project'>?</span></td>
            <td><label for="default_project">Default Project</label></td>
            <td><select id="default_project"></select></td>
        </tr>
        <tr>
            <td><span class='tinyhelp' data-topic='theme'>?</span></td>
            <td><label for='pref_theme'>Theme</label></td>
            <td><div id='pref_theme' class='hue_chooser'></div></td>
        </tr>    
    </table>
    <table id='prefDialog-2'>
        <tr>
            <td><span class='tinyhelp' data-topic='thumbnails'>?</span></td>
            <td colspan="2"><input type='checkbox' id='show_thumbs' /> 
            <label for="show_thumbs">Show Thumbnail Images</label></td>
        </tr>
        <tr>
            <td><span class='tinyhelp' data-topic='image formats'>?</span></td>
            <td><label for="default_imageformat">Default Image Format</label></td>
            <td><select id="default_imageformat">
                <option value="jpg">JPG</option>
                <option value="png">PNG</option>
                <option value="gif">GIF</option>
            </select></td>
        </tr>
        <tr>
            <td><span class='tinyhelp' data-topic='batch names'>?</span></td>
            <td><label for="batch_names">Batch Default Names</label></td>
            <td><select id="batch_names">
                <option value="folder">Folder (e.g., /sym/filename)</option>
                <option value="prefix">Prefix (e.g., /sym_filename)</option>
                <option value="suffix">Suffix (e.g., /filename_sym)</option>
            </select></td>
        </tr>
        <tr>
            <td><span class='tinyhelp' data-topic='texture averages'>?</span></td>
            <td colspan="2"><input type='checkbox' id='texture' /> 
            <label for="texture">Texture Averages</label></td>
        </tr>
        <tr>
            <td><span class='tinyhelp' data-topic='sample contours'>?</span></td>
            <td colspan="2"><input type='checkbox' id='sample_contours' /> 
            <label for="sample_contours">Sample Contours</label></td>
        </tr>

        <tr>
            <td><span class='tinyhelp' data-topic='normalisation'>?</span></td>
            <td><label for="normalisation">Normalisation</label></td>
            <td><select id="normalisation">
                <option value="none">None</option>
                <option value="twopoint">2-Point</option>
                <!--<option value="threepoint">3-Point (0,1,96)</option>-->
                <option value="rigid">Rigid-body (Procrustes)</option>
            </select></td>
        </tr>
        <tr>
            <td><span class='tinyhelp' data-topic='warp'>?</span></td>
            <td><label for="warp">Transform Warp</label></td>
            <td><select id="warp">
                <option value="multiscale">Multiscale</option>
                <option value="linear">Linear</option>
                <!--<option value="tps">TPS</option>-->
                <option value="multiscalerb">Multiscale RB</option>
            </select></td>
        </tr>
        <tr>
            <td><span class='tinyhelp' data-topic='mask color'>?</span></td>
            <td><label for="mask_color">Mask Color</label></td>
            <td><div id='mask_color' class='rgb_chooser mask_color'></div></td>
        </tr>
    </table>
    <table id='prefDialog-3'>
        <tr>
            <td><label for="defaultTemplate">Default Template</label></td>
            <td><select id="defaultTemplate"></select></td>
        </tr>
        <tr><td colspan="2">Default Alignment
            <table id='alignPref' class='custom_batch'>
                <tr>
                    <td><span title="First point to align to (e.g., the left pupil)">1<sup>st</sup> point:</span></td>
                    <td><input type='number' step='any' min='0' value='0' step='1' id='align_pt1' maxlength='6' style='width: 2em;' /></td>
                    <td>X: <input type='number' step='any' min='0' value='496.980' id='align_x1' maxlength='7' /></td>
                    <td>Y: <input type='number' step='any' min='0' value='825.688' id='align_y1' maxlength='7' /></td>
                </tr>
                <tr>
                    <td><span title="Second point to align to (e.g., the right pupil)">2<sup>nd</sup> point:</span></td>
                    <td><input type='number' step='any' min='0' value='1' step='1' id='align_pt2' maxlength='6' style='width: 2em;' /></td>
                    <td>X: <input type='number' step='any' min='0' value='853.020' id='align_x2' maxlength='7' /></td>
                    <td>Y: <input type='number' step='any' min='0' value='825.688' id='align_y2' maxlength='7' /></td>
                </tr>
                <tr>
                    <td>Image Size:</td>
                    <td></td>
                    <td>width: <input type='number' step='any' min='0' value='1350' id='align_w' maxlength='6' /></td>
                    <td>height: <input type='number' step='any' min='0' value='1800' id='align_h' maxlength='6' /></td>
                </tr>
            </table>
        </td></tr>
        
        <tr>
            <td><label for="cross_color">Delineation Cross</label></td>
            <td><div id='cross_color' class='rgb_chooser'></div></td>
        </tr>
        <tr>
            <td><label for="selcross_color">Selected Cross</label></td>
            <td><div id='selcross_color' class='rgb_chooser'></div></td>
        </tr>
        <tr>
            <td><label for="line_color">Delineation Line</label></td>
            <td><div id='line_color' class='rgb_chooser'></div></td>
        </tr>
        <tr>
            <td><label for="defaultLineWidth">Default Line Width</label></td>
            <td><input id='defaultLineWidth' type='number' step='1' min='1' max='9' value='1' maxlength='1' /></td>
        </tr>
    </table>
</div>

<!-- !- batchWatchDialog -->

<div id='batchWatchDialog' class='modal'>
    <p>Paste your batch file from Excel into the box below.</p>
    <div class="progressBox"><div class="progressBar"></div></div>
    <ol class="errorList"></ol>
    <div class="imageList"><div></div></div>
</div>

<!-- !- newProjectDialog -->
<div id='newProjectDialog' class='modal' >
    <input type='text' id='new_project_name' placeholder='Project Name' />
    <textarea id='new_project_notes'></textarea>
</div>

<!-- !- facialmetricEQ -->
<div id='facialmetricEQ' class='modal' >
    <p>Allowed functions in equations are: 
        <code>abs()</code>, 
        <code>min()</code>, 
        <code>max()</code>, 
        <code>tan()</code>, 
        <code>sin()</code>, 
        <code>cos()</code>, 
        <code>atan()</code>, 
        <code>asin()</code>, 
        <code>acos()</code>, 
        <code>sqrt()</code>, 
        <code>pow()</code> and 
        <code>rad2deg()</code>. 
        
        X- and y-coordinates of delineation points are written, e.g., 
        <code>x[0]</code> (the x-value of point 0). Units are in pixels. 
        The origin (0,0) is in the upper left corner.</p>
    
    <div id='fm_delete'></div>
    <ul id="fmButtons" class="tinybutton">
        <li id="fm_FWH" 
            title="Facial width-to-height ratio"
            data-equation="abs(max(x[113],x[112],x[114])-min(x[110],x[111],x[109]))/abs(y[90]-min(y[20],y[25]))">FWH</li>
        <li id="fm_eyes" 
            title="Distance between eyes"
            data-equation="sqrt(pow(x[0]-x[1], 2) + pow(y[0]-y[1],2))">Eye-spacing</li>
    </ul>

    <a id="fm_new" title="Create a new default button or modify an old one" class="tinybutton">+</a>
    <input type='text' id='fm_name' value='Eye-spacing' placeholder='Name' />
    <textarea id='fm_equation'>sqrt(pow(x[0]-x[1], 2) + pow(y[0]-y[1],2))</textarea>
    
    <div id='fm_results' class='batchList'></div>
</div>

<!-- !- resizeDialog -->
<div id='resizeDialog' class='modal'>
    <div class='batch_name' default='resized'>Resized image name:</div>
    
    <table id='resizer'>
        <tr><td></td><td>width</td><td>height</td></tr>
        <tr><td>By percent: </td>
            <td><input type='number' step='any' min='0' value='' name='x' maxlength='6' /> %</td> 
            <td><input type='number' step='any' min='0' value='' name='y' maxlength='6' /> %</td>
            
        </tr>
        <tr><td>By pixel: </td>
            <td><input type='number' step='any' min='0' value='' name='w' maxlength='6' /> px</td>
            <td><input type='number' step='any' min='0' value='' name='h' maxlength='6' /> px</td>
        </tr>
    </table>
    
    <div class='batchList'></div>
</div>

<!-- !- rotateDialog -->
<div id='rotateDialog' class='modal'>
    <div class='batch_name' default='rotated'>Rotated image name:</div>
    
    <div id='rotate_color' class='rgb_chooser mask_color'></div>
    
    <input type='number' step='any' min='0' max='360' value='0' name='degrees' maxlength='6' /> degrees</td> 

    <div class='batchList'></div>
</div>

<!-- !- convertDialog -->
<div id='convertDialog' class='modal'>
    <!--<div class='batch_name'>
        Cropped image name:
    </div>-->
</div>

<!-- !- cropDialog -->
<div id='cropDialog' class='modal'>
    <div class='batch_name' default='cropped'>Cropped image name:</div>
    
    <div id='crop_color' class='rgb_chooser mask_color'></div>
    
    <div id='cropDiagram'>
        <input type='number' step='any' min='0' value='0' name='top' maxlength='4' />
        <br>
        <input type='number' step='any' min='0' value='0' name='left' maxlength='4' />
        <div id='cropBox'>
            <span id='cropBoxWidth' orig='0'>100px</span>
            <span id='cropBoxHeight' orig='0'>100px</span>
        </div>
        <input type='number' step='any' min='0' value='0' name='right' maxlength='4' />
        <br>
        <input type='number' step='any' min='0' value='0' name='bottom' maxlength='4' />
    </div>
    
    <p>Type positive numbers into the boxes to add margins and negative numbers to crop margins.</p>
    
    <div class='batchList'></div>
</div>

<!-- !- mirrorDialog -->
<div id='mirrorDialog' class='modal'>
    <div class='batch_name' default='mirror'>Mirrored image name:</div>
    <p></p>
</div>

<!-- !- temConvertDialog -->
<div id='temConvertDialog' class='modal'>
    <div class='batch_name' default='convert'>Converted image name:</div>
    <p></p>
</div>

<!-- !- symDialog -->
<div id='symDialog' class='modal'>
    <div class='batch_name' default='sym'>Symmetrised image name:</div>
    
    <ul>
        <li><input type='checkbox' id='sym_shape' checked='checked' />     <label for='sym_shape'>Shape</label></li>
        <li><input type='checkbox' id='sym_color' checked='checked' />     <label for='sym_color'>color</label></li>
    </ul>
    <p></p>
</div>

<!-- !- alignDialog -->
<div id='alignDialog' class='modal'>
    <div class='batch_name' default='aligned'>Aligned image name:</div>
    
    <div id='align_color' class='rgb_chooser mask_color'></div>
    
    <table id='custom_align' class='custom_batch'>
        <tr>
            <td><span title="First point to align to (e.g., the left pupil)">1<sup>st</sup> point:</span></td>
            <td><input type='number' step='any' min='0' value='' step='1' name='pt1' maxlength='6' style='width: 2em;' /></td>
            <td>X: <input type='number' step='any' min='0' value='' name='x1' maxlength='7' /></td>
            <td>Y: <input type='number' step='any' min='0' value='' name='y1' maxlength='7' /></td>
        </tr>
        <tr>
            <td><span title="Second point to align to (e.g., the right pupil)">2<sup>nd</sup> point:</span></td>
            <td><input type='number' step='any' min='0' value='' step='1' name='pt2' maxlength='6' style='width: 2em;' /></td>
            <td>X: <input type='number' step='any' min='0' value='' name='x2' maxlength='7' /></td>
            <td>Y: <input type='number' step='any' min='0' value='' name='y2' maxlength='7' /></td>
        </tr>
        <tr>
            <td>Image Size:</td>
            <td></td>
            <td>width: <input type='number' step='any' min='0' value='' name='width' maxlength='6' /></td>
            <td>height: <input type='number' step='any' min='0' value='' name='height' maxlength='6' /></td>
        </tr>
    </table>
</div>

<!-- !- colorCalibrateDialog -->
<div id='colorCalibrateDialog' class='modal'>
    <div class='batch_name' default='color_calibrated'>
        color calibrated image name:
    </div>
</div>

<!-- !- scrambleDialog -->
<div id='scrambleDialog' class='modal'>
    <div class='batch_name' default='scramble'>Scrambled image name:</div>
    Image size (<span id='scramble_orig_width'></span> x <span id='scramble_orig_height'></span>)
    Grid Size: <input type='integer' step='1' min='1' value='50' id='grid_size' maxlength='4' />
    X-offset: <input type='integer' step='1' min='1' value='0' id='scramble_x_offset' maxlength='4' />
    Y-offset: <input type='integer' step='1' min='1' value='0' id='scramble_y_offset' maxlength='4' />
    <br>
    <input type='checkbox' id='grid_lines' checked='checked' /> <label for='grid_lines'>Grid Lines</label> <div id='grid_line_color' class='rgb_chooser'></div>
    <p>Click on the squares you want to scramble. Drag for multiple select.</p>
    <div id='scrambleExample'></div>
</div>

<!-- !- maskDialog -->
<div id='maskDialog' class='modal'>
    <div class='batch_name' default='masked'>Masked image name:</div>
    <div id='batch_mask_color' class='rgb_chooser mask_color'></div>
    <div id='maskExample'>
        <img id='mask_demo_teeth' src='/include/images/masks/teeth' />
        <img id='mask_demo_mouth' src='/include/images/masks/mouth' />
        <img id='mask_demo_neck' src='/include/images/masks/neck' />
        <img id='mask_demo_ears' src='/include/images/masks/ears' />
        <img id='mask_demo_eyes' src='/include/images/masks/eyes' />
        <img id='mask_demo_brows' src='/include/images/masks/brows' />
        <img id='mask_demo_nose' src='/include/images/masks/nose' />
        <img id='mask_demo_face' src='/include/images/masks/face' />
        <img id='mask_demo_oval' src='/include/images/masks/oval' />
    </div>
    <div>
        <br />Blur: <input type='number' step='any' min='0' max='30' value='0' name='blur' maxlength='2' />
        <ul>
            <li><input type='checkbox' id='mask_trans' />   <label for='mask_trans'>Transparent</label></li>
            <li><input type='checkbox' id='mask_oval' />    <label for='mask_oval'>Oval</label></li>
            <li><input type='checkbox' id='mask_face' />    <label for='mask_face'>Face</label></li>
            <li><input type='checkbox' id='mask_neck' />    <label for='mask_neck'>Neck</label></li>
            <li><input type='checkbox' id='mask_ears' />    <label for='mask_ears'>Ears</label></li>
            <li><input type='checkbox' id='mask_eyes' />    <label for='mask_eyes'>Eyes</label></li>
            <li><input type='checkbox' id='mask_brows' />   <label for='mask_brows'>Eyebrows</label></li>
            <li><input type='checkbox' id='mask_mouth' />   <label for='mask_mouth'>Mouth</label></li>
            <li><input type='checkbox' id='mask_teeth' />   <label for='mask_teeth'>Teeth</label></li>
            <li><input type='checkbox' id='mask_nose' />    <label for='mask_nose'>Nose</label></li>
        </ul>
    </div>
    <div id='custom_mask_box'>
        Custom Mask (e.g., the left eye would be: <code>18,19,20,21,22 ; 22,30,29,28,18</code>)
        <textarea id='custom_mask'></textarea>
    </div>
</div>

<!-- !- addTemDialog -->
<div id='addTemDialog' class='modal' title='Add/Edit a Template'>
    <p class="warning"></p>
    <p>You can set the current template as a default template so you can label the points and set up custom symmetry files. You can have as many default templates as you like. Select the current default template under Preferences (<span class="cmd">,</span>).</p>
    <ul>
        <li><input type='checkbox' id='isNewTem' />    <label for='isNewTem'>Register this as a New Template</label></li>
        <li><label for="defaultTemName">Template Name:</label> <input id="defaultTemName" type="text" /></li>
        <li><label for="defaultTemNotes">Notes:</label> <textarea id="defaultTemNotes"></textarea></li>
        <!--<li><input type='checkbox' id='defaultTemPublic' />    <label for='defaultTemPublic'>Make Public</label></li>-->
        <li><label for="defaultTem3Pt1">1st 3-Point Delineation Point (e.g., left eye):</label> <select id="defaultTem3Pt1" class="tempoints"></select></li>
        <li><label for="defaultTem3Pt2">2nd 3-Point Delineation Point (e.g., right eye):</label> <select id="defaultTem3Pt2" class="tempoints"></select></li>
        <li><label for="defaultTem3Pt3">3rd 3-Point Delineation Point (e.g., mouth):</label> <select id="defaultTem3Pt3" class="tempoints"></select></li>
    </ul>
</div>

<!-- !- labelDialog -->
<div id='labelDialog' class='modal'>
    <p>Labels for the template</p>
    <ol start='0'></ol>
</div>

<!-- !- SBTdialog -->
<div id='SBTdialog' class='modal'>
    <p>Paste your batch file from Excel into the box below.</p>
    <ol class="errorList"></ol>
    <div id='SBTtablescroll'>
        <table>
            <thead><tr><th>trans-img</th><th>from-img</th><th>to-img</th><th>shape</th><th>color</th><th>texture</th><th>outname</th></tr></thead>
            <tbody></tbody>
        </table>
    </div>
    <textarea></textarea>
</div>

<!-- !- BAdialog -->
<div id='BAdialog' class='modal'>
    <p>Paste your batch file from Excel into the box below. Put the name of each average on the first row and the images in the average in the rows below. Put each average in a new column.</p>
    <ol class="errorList"></ol>
    <div class="imageList"><div></div></div>
    
    <div id='BAtablescroll'>
        <table>
            <tbody></tbody>
        </table>
    </div>
    <textarea></textarea>
</div>

<!-- !- batchRenameDialog -->
<div id='batchRenameDialog' class='modal' >
    <table>
        <tr>
            <td><input type="checkbox" id="replacecheck" /></td>
            <td>Find &amp; replace
                <br><span style="font-size: 70%">Separate multiple items<br>with a semicolon (e.g., a;b;c)</span>
            </td>
            <td>
            Find: <input type='text' id='batchRenameFind' placeholder='find' /><br>
            Replace: <input type='text' id='batchRenameReplace' placeholder='replace' />
            </td>
        </tr>
        
        <tr>
            <td><input type="checkbox" id="prefixcheck" /></td>
            <td>Insert prefix</td>
            <td><input type='text' id='batchRenamePrefix' placeholder='prefix' /></td>
        </tr>
        
        <tr>
            <td><input type="checkbox" id="suffixcheck" /></td>
            <td>Insert suffix</td>
            <td><input type='text' id='batchRenameSuffix' placeholder='suffix' /></td>
        </tr>
        
        <tr>
            <td><input type="checkbox" id="indexcheck" /></td>
            <td>Add index</td>
            <td><select type='text' id='batchRenameIndex'>
                <option value="after">after</option>
                <option value="before">before</option>
            </select></td>
        </tr>
    </table>
    
    <div class='batchList'></div>
</div>

<!-- !- modifyDelineation -->
<div id="modifyDelineation" class="modal">
    <p>This function is experimental and prone to error.</p>
    <div class='batch_name'>
        Save new delineations as: 
        <code>
            <span class='multibatch'></span><span class='batch_subfolder'></span>/<span class='batch_prefix'>[no prefix]</span>**IMAGE**<span class='batch_suffix'>[no suffix]</span>.tem
        </code>
    </div>
    <p>Select the delineation points to delete</p>
    <ul id='modDelinPoints'>
    </ul>
    

    <p>Modify the lines</p>
    <textarea id="modDelinLines"></textarea>
</div>

<!-- !- movieDialog -->
<div id='movieDialog' class='modal'>
    <p>Turn a set of images into a moving gif.</p>
    <form>
        <div>
            <label for='movieHeight'>Height:</label> <span id='movieHeightDisplay'>200</span>px 
            <a class="tinybutton" id="movieOriginalHeight">original</a>
            <div id="movieHeight"></div>
        </div>
        
        <div>
            <label for='movieSpeed'>Speed:</label> <span id='movieSpeedDisplay'>50</span>ms/frame (<span id='movieLength'></span>s)
            <div id="movieSpeed"></div>
        </div>
        
        <div>
            <label for='movieRev'>Back and forth:</label> <input id='movieRev' type='checkbox' checked='checked' />
        </div>
        
        <div id="moviePauseSection">
            <label for='moviePause'>Pause:</label> <span id='moviePauseDisplay'>0</span>ms/frame
            <div id="moviePause"></div>
        </div>
        
        <div>
            <label for='movieFileName'>Movie file name:</label> <input id='movieFileName' type='text' style="width: 15em;" />
        </div>
    </form>
    <img id="movieBox" src="/include/images/blankface" />
</div>

<!-- !- batchPcaDialog -->
<div id='batchPcaDialog' class='modal'>
    <p>Paste in a batch file from Excel. Each column specifies one model.</p>
    <ol>
        <li>model name</li>
        <li>create shape model (T/F)</li>
        <li>create colour model (T/F)</li>
        <li>mask name for colour model</li>
        <li>list of image names</li>
    </ol>
    
    <textarea></textarea>
    
    <table><tbody></tbody></table>
</div>

<!-- !- pcaDialog -->
<div id='pcaDialog' class='modal'>
    <p>Create a PCA model for a set of templates (and optionally analyse them) or analyse a set of templates using an existing PCA model.</p>
    <table class='pca' id='pca_analysis'>
        
        <tr id='use_existing_pca'>
            <td><input id='usePCA' type='radio' name='pcatype' value='oldpca' /></td>
            <td><label for='usePCA'>Use Existing PCA Model</label></td>
            <td class='pcaopts oldmodel'><select id='pcafilename'></select></td>
            <td class='pcaopts oldmodel'>.pca</td>
        </tr>
        <tr>
            <td><input id='createPCA' type='radio' name='pcatype' value='newpca' /></td>
            <td><label for='createPCA'>Create New PCA Model</label></td>
            <td class='pcaopts newname'><input id='smodelfilename' type='text' placeholder='_model' /></td>
            <td class='pcaopts newname'>.pca</td>
        </tr>
        <tr>
            <td><input id='skipPCA' type='radio' name='pcatype' value='skippca' /></td>
            <td><label for='skipPCA'>Skip Shape</label></td>
            <td></td>
            <td></td>
        </tr>
        <tr class='pcaopts'>
            <td><input id='analysePCA' type='checkbox' checked='checked' /></td>
            <td><label for='analysePCA'>Analyse Shape</label></td>
            <td id='sanalysisfile'><input id='sanalysisfilename' type='text' placeholder='_analysis' /></td>
            <td>.shape.csv</td>
        </tr>
        
        
        <tr id='use_existing_pci'>
            <td><input id='usePCI' type='radio' name='pcitype' value='oldpci' /></td>
            <td><label for='usePCI'>Use Existing PCI Model</label></td>
            <td class='pciopts oldmodel'><select id='pcifilename'></select></td>
            <td class='pciopts oldmodel'>.pci</td>
        </tr>
        <tr>
            <td><input id='createPCI' type='radio' name='pcitype' value='newpci' /></td>
            <td><label for='createPCI'>Create New PCI Model</label></td>
            <td class='pciopts newname'><input id='cmodelfilename' type='text' placeholder='_model' /></td>
            <td class='pciopts newname'>.pci</td>
        </tr>
        <tr>
            <td><input id='skipPCI' type='radio' name='pcitype' value='skippci' /></td>
            <td><label for='skipPCI'>Skip Colour</label></td>
            <td></td>
            <td></td>
        </tr>
        <tr class='pciopts'>
            <td><input id='analysePCI' type='checkbox' checked='checked' /></td>
            <td><label for='analysePCI'>Analyse Colour</label></td>
            <td id='canalysisfile'><input id='canalysisfilename' type='text' placeholder='_analysis' /></td>
            <td>.color.csv</td>
        </tr>
        <tr class='pciopts'>
            <td></td>
            <td>Mask for PCI</td>
            <td><select id='pci_mask'>
                <option value='frl_face'>FRL_face</option>
                <option value='frl_face_neck'>FRL_face_neck</option>
                <option value='frl_face_ears'>FRL_face_ears</option>
                <option value='frl_face_neck_ears'>FRL_face_neck_ears</option>
                <option value='pl_no_ears'>PL_no_ears</option>
                <option value='pl_with_ears'>PL_with_ears</option>
            </select></td>
            <td></td>
        </tr>
    </table>
    
    <p>This will create the following files:
        <ul id='pca_files'>
            <li id='pca_csv' class='pcaopts'><span>_analysis</span>.shape.csv = the shape analysis of the selected templates</li>
            <li id='pci_csv' class='pciopts'><span>_analysis</span>.color.csv = the colour analysis of the selected images</li>
            <li id='pca_pca' class='pcaopts'><span>_model</span>.pca = your PCA model (shape analysis)</li>
            <li id='pca_txt' class='pcaopts'><span>_model</span>.pca.txt = a human-readable version of the PCA model</li>
            <li id='pca_var' class='pcaopts'><span>_model</span>.pca.var.csv = the variance information for each component in the PCA model.</li>
            <li id='pci_pci' class='pciopts'><span>_model</span>.pci = a directory of your PCI model (colour analysis)</li>
            <li id='pca_tem' class='pcaopts'><span>_model</span>.tem = the procrustes normalised template for the set</li>
            <li id='pca_jpg' class='pcaopts'><span>_model</span>.jpg = the average image for the set</li>
        </ul>
    </p>
</div>

<!-- !- pcVisDialog -->
<div id='pcVisDialog' class='modal'>
    <p>Reconstruct or create visualisations from an existing PCA model and average. Paste in a tab, space or commma-delimited set of visualisations to create. The first column should be the save path, the second column the PCA model path, the third column the image to transform, and the following columns are the weights for each PC. Use proportions (e.g., 0.5 instead of 50 or 50%). For example, to create an image that is +1SD on the first PC different from the average: <code>/newimagename.jpg, /_model.pca, /_average.jpg, 1.0</code></p>
    <textarea></textarea>
</div>

<!-- !- delinHelp Dialog -->
<div id='delinHelp' class='modal' title='Delineation Help'>
    <ul>
        <li>Drag each point to delineate the shape of the image.</li>
        <li>&#8679;-click on a point to select or unselect it.</li>
        <li><span class="cmd"></span>-click or &#8963;-click on a point to select or unselect the whole line.</li>
        <li>Hold shift and drag over the image select all points inside the box.</li>
        <li>Move a group of selected points by moving any selected point with the mouse or using the arrow keys.</li>
        <li>Use <span class="opt">+</span> and <span class="opt">-</span> to increase or decrease the size of a selected group of delineation points.</li>
        <li>Double-click on the image (or <span class="cmd">A</span>) to unselect all points.</li>
        <li>Hover over a point to see where it should go (see the footer).</li>
        <li>&#8679;<span class="cmd"></span>-click to add a new point.</li>
        <li><span class="cmd">L</span> to start drawing a new line. Click 'enter' to end the line.</li>
        <li>&#8679;<span class="cmd">L</span> to delete a line.</li>
    </ul>    
</div>

<!-- !- webcam Dialog -->
<div id='webcamDialog' class='modal' title='Take Photo from Webcam'>
    <video id="webcam"></video>
    <canvas id="webcamvas" width="640" height="480"></canvas>
    <div id="webcamSave"><code id="webcamFolder"></code><input type="text" id="webcamName" placeholder="imagename" /></div>
</div>

<!-- !- about Dialog -->
<div id='aboutDialog' class='modal' title='About WebMorph'>
    <p>WebMorph is made possible by the kind help of 
        <a href="http://users.aber.ac.uk/bpt/">Bernie Tiddeman</a>, who developed and maintains the desktop 
        version of <a href="http://users.aber.ac.uk/bpt/jpsychomorph/">Psychomorph</a>. WebMorph uses the 
        open-source Java library <a href="http://users.aber.ac.uk/bpt/jpsychomorph/version6/javadoc/">FaceMorphLib</a> 
        and is developed and maintained by <a href="http://facelab.org/debruine/">Lisa DeBruine</a>.</p>
    
    <p>WebMorph is currently in alpha testing and is likely to remain so for some time. 
        This means that there will be bugs and you cannot rely on the website being functional 
        100% of the time. Lisa will try to fix any problems as fast as possible, but she is the 
        only person working on this project, so please be patient. If you're curious about the 
        code or want to help with development, this project is open source at 
        <a href="https://github.com/debruine/webmorph">https://github.com/debruine/webmorph</a>.</p>
    
    <h3>To cite WebMorph</h3>
    <p>DeBruine, L. M. &amp; Tiddeman, B. P. (<?= date("Y, F j", filemtime(DOC_ROOT . $_SERVER['PHP_SELF'])) ?>). <i>WebMorph</i>. Retrieved from http://<?= $_SERVER['SERVER_NAME'] ?></p>
    <p>Access the code at <a href="https://github.com/debruine/webmorph">GitHub</a>.</p>
    <p>To cite the methods, see <a href="http://users.aber.ac.uk/bpt/jpsychomorph/" target="_blank">Bernie Tiddeman's webpage</a>.</p>
    <h3>To cite provided images</h3>
    <p>DeBruine, L. M. &amp; Jones, B. C. (2015, October 13). Average Faces. <i>Open Science Framework</i>. <a href="http://osf.io/gzy7m " target="_blank">osf.io/gzy7m</a></p>
    
</div>

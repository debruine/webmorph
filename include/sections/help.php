<!-- !- what's new Dialog -->
<div id='whatsnewDialog' class='modal' title='What&apos;s New?'>
    <dl>
        <dt>(2016-04-20) Read-Only Project Members</dt>
        <dd>Projects can now have read-only members. Click on the A or R next to 
        a project member's name to toggle their access permission. I have not 
        yet thoroughly checked that read-only members cannot make any destructive 
        project changes, so please be careful and only add project members you trust.</dd>
        
        <dt>(2016-04-20) Template Conversions</dt>
        <dd>Template conversions can now handle templates without images. We 
        also added a new template: the 129-point template from Scott et al. 
        (2010) PLoS One for use in Geometric MorphoMetric analyses. I also fixed 
        some bugs with the new file namer for batch functions.</dd>
        
        <dt>(2016-04-14) Scrambles</dt>
        <dd>Check out the new batch scrambling function!</dd>
        
        <dt>(2016-04-14) Downloadable Delineation Images</dt>
        <dd>If you right-click on a delineation in the Delineation window, you 
        can now see a contextual menu to download SVG files of the delineation 
        (all, lines only, points only, or numbered points). Open this file in a 
        text editor to see how easily you can change the line and points colours 
        and widths. Drag the file into a web browser to view it.</dd>
        
        <dt>(2016-04-13) Messages</dt>
        <dd>Now development messages are only shown once per account, so you
        don't have to close them every time you reload the page. I've also made 
        a lot of small changes to tidy up the code. Let me know if it breaks 
        anything.</dd>
        
        <dt>(2016-04-08) Multiple Directory Functions</dt>
        <dd>You can now select folders and apply batch functions 
        (and deletion) to several folders at once. You can also bypass the  
        confirmation dialog when deleting files and folders by using 
        <span class="shortcut shiftcmd">&#x232b;</span> or 
        <span class="shortcut shiftcmd">-backspace</span>. This is likely to 
        be a little buggy at first, so make sure you back up your important 
        files regularly (which you should already be doing).</dd>
        
        <dt>(2016-04-06) Continuum and Grid</dt>
        <dd>I fixed several bugs in the continuum and grid transforms. I also 
        added a new batch function for multiple continua (e.g., morph from A to 
        B to C to D...). Watch out, it might still be a bit buggy.</dd>
        
        <dt>(2016-04-04) Account Registration</dt>
        <dd>Account registration is now done automatically through the website 
        and account requests are put on a wait list.</dd>
        
        <dt>(2016-03-21) Project Size</dt>
        <dd>Project sizes are now only calculated once at the beginning of a 
        session and after changes are made to the project. This prevents long 
        delays when switching between the project window and other windows.</dd>
        
        <dt>(2015-11-15) Making Averages</dt>
        <dd>You can now drag files to the average face in the Average Window to 
        add them to an average. This is useful if you want to average images 
        from more than one folder. You can also include the same image more than
        once. The first image in the list provides the height and width for the 
        average image and also the coordinates for 2-point or rigid-body alignment.</dd>
        
        <dt>(2015-11-10) Projects</dt>
        <dd>Your files are now organised into shareable projects. For now:
        <ul>
            <li><strong>All group members can add and delete both members and files.</strong></li>
            <li>Any WebMorph user can be added to a project by their email or name.</li>
            <li>The original owner cannot be removed from the project.</li>
            <li>The original owner is the only person who can delete a project.</li>
            <li>All files in a project count towards the project owner's space allocation.</li>
            <li>Contact Lisa to change your space allocation.</li>
            <li>If you are over your space allocation, you will not be able to save 
            or upload files until you delete things (and empty your trash).</li>
        </ul>
        I am planning to add permissions for project members so you can have 
        read-only members, but that might take some time, so make sure you trust 
        the people you add to a project. As always, please back up important 
        projects by downloading and saving the files.</dd>
        
        <dt>(2015-10-17) Changes to file deletion</dt>
        <dd>Deleted files are now moved to a Trash folder that is viewable 
        through the View menu. The ability to empty the trash folder will be added soon.</dd>
        <dt>(2015-10-02) Changes to the delineation interface</dt>
        <dd>The Kinetic library had been causing many crashes, so I've removed 
        that and now the delineations are done somewhat differently. This won't 
        cause much difference for most users, apart from some cosmetic changes 
        in cross and line highlighting. For now, deleting lines is no longer 
        possible (although you can still undo them if you're creating a new template).</dd>
        <dt>(2015-01-06) Changes to averaging and transforming</dt>
        <dd>
        <ol>
            <li>The functions that average and transform images have been re-written. 
            For the most part, you won't notice any difference.</li>
            <li>Estimated processing times for manual (not batch) averages and 
            transforms are given in the footer now. These will become more 
            accurate as I collect more data.</li>
            <li>You can now create larger averages (bigger image size and/or more 
            images per average). As I collect more data, I will add a function to 
            warn you if averages are likely to fail.</li>
            <li>Fixed a bug that prevented queue items from being cleared, paused, or restarted.</li>
        </ol>
        </dd>
        <dt>(2014-11-21) Decreased initial loading time</dt>
        <dd>I've put in place a temporary fix to help accounts with >1000 images 
        that have been experiencing very slow loading times and warnings about 
        unresponsive scripts. The initial loading time is now 1-5 seconds to get 
        your image list from the server (this depends on current server load) and 
        an additional ~1 second per 2000 images to display them. The side effects 
        of these changes are that:
        <ol>
            <li>Column width in the finder window is no longer flexible and sized 
            to the contents, but fixed at 200 pixels. (changed back 2015-01-06)</li>
            <li>The average and transform windows no longer only show images that 
            have a tem file. If you choose an image that has no tem, you won’t 
            get a warning message, but your average will not include that image. 
            If you try to transform using an image with no tem file, the transform 
            will fail and you will get a blank error window. I’ll fix this so you 
            get appropriate warnings as soon as possible, but I am very busy until 
            mid-December.</li>
        </ol>
        </dd>
        <dt>(2014-07-21) Delineation Improvements</dt>
        <dd>Use <span class="opt">+</span> and <span class="opt">-</span> to increase 
        or decrease the size of a selected group of delineation points.</dd>
        <dt>(2014-06-15) Queue</dt>
        <dd>Batch transformations are now placed in a queue, where they can complete 
        in the background and be paused and restarted. [2014-11-21: Pausing seems 
        to be broken. This is on my to-do list.]</dd>
        <dt>(2014-05-22) Template changes and menu reorganisation</dt>
        <dd>I'm reorganising the menu items, so their location and shortcuts will 
        be changing a bit over the next few weeks. I'm also trying to make it 
        easier to manage multiple template styles.</dd>
        <dt>(2014-05-07) Major upgrade</dt>
        <dd>Most of the changes are behind the scenes to improve security and stability.
            <ol>
                <li>Secure image storage so your images are not accessible by 
                anyone except you when you are logged into your account</li>
                <li>More secure password storage</li>
                <li>Upload and save images as JPG, PNG or GIF. 
                (PNGs lose transparency when transformed)</li>
                <li>Better batch functions for renaming, aligning, cropping, 
                resizing, masking, symmetrising, mirroring, modifying delineations, 
                and calculating facialmetrics</li>
                <li>You can save your own facialmetric equations</li>
                <li>Better support for multiple delineation types</li>
            </ol>
        </dd>
        <dt>(2013-11-17) Major interface change</dt>
        <dd>The array of images in the Average and Transform windows have been 
        replaced by the Finder. This required a major change to almost all 
        functions, so please let me know when you find remaining bugs. You can 
        now save new averages and transforms by dragging them into the Finder.</dd>
        <dt>(2013-11-12) Webcam Upload</dt>
        <dd>From any folder in the finder, select <code>File > Webcam Upload 
        (<span class="cmd">W</span>)</code> to use your webcam to take a 
        picture, name it and save it.</dd>
        <dt>(2013-11-10) Batch rename</dt>
        <dd>Select files in the finder and select <code>Actions > Batch Rename 
        (<span class="shiftcmd">N</span>)</code> to add a prefix, suffix, 
        index, and multiple search and replace.</dd>
        <dt>(2013-11-01) Drag and drop file moving</dt>
        <dd>Try dragging selected files (as many as you want) or folders 
        (one folder at a time for now) into another folder</dd>
    </dl>
</div>









<!-- !- help Dialog -->
<div id='help' class='modal' title='WebMorph Help'>
    <div class='prefs'>
        <h1 data-topic='preferences'>Preferences</h1>
        <h2>Personal</h2>
        <p>We ask for your personal information to help with the development of 
            WebMorph Online. We're always happy to hear from you about what 
            sort of things would make WebMorph better or easier to use.</p>
        
        <h2 data-topic='default project'>Default project</h2>
        <p>Set a default project to use if you go straight to the Finder, 
            Average or Transform Windows without choosing a specific project.</p>
            
        <h2 data-topic='theme'>Theme</h2>
        <p>Set a theme color for the interface. 0 is a light neutral color 
            scheme. Go all the way to the right for the rainbow color scheme, 
            and one tick before that for a dark neutral color scheme (these two 
            are experimental and might be a little ugly).</p>
        
        <h2 data-topic='thumbnails'>Show thumbnail images</h2>
        <p>This shows small thumbnails in the finder. If you have many images in 
            your project, this can slow down finder loading.</p>
            
        <h2 data-topic='image formats'>Default image format</h2>
        <p>The image format in which to save new images. You can always change 
            the image format in any individual Batch process dialog.</p>
        <ul>
            <li>JPG: This is the default, as it has relatively small file size 
            (we use the best-quality JPEG compression of 100), so a 1350x1800 
            pixel image will be about 0.5 MB. JPGs can contain embedded 
            information about the image. </li>
            <li>PNG: This is uncompressed (we use the best-quality PNG 
            compression of 0), so a 1350x1800 pixel image will be about 7.0 MB. 
            This file type is good for very small images or for images where 
            exact color is important. PNGs can also use transparency, so images 
            with transparent masks are always saved as PNGs.</li>
            <li>GIF: This is uncompressed, but images are smaller than PNGs, so 
            a 1350x1800 pixel image will be about 1.0 MB. Transparency is 
            possible, but not as sophisticated as in PNGs. We mainly use GIFs 
            as the frames for animated GIFs.</li>
        </ul>
            
        <h2 data-topic='batch names'>Batch default names</h2>
        <ul>
            <li>Folder (e.g., /sym/filename): Images created under the Batch menu 
            are put in a new folder inside the original folder and the image 
            names are kept the same as the originals</li>
            <li>Prefix (e.g., /sym_filename): Images created under the Batch menu 
            are put in the original folder and the image names are given a prefix</li>
            <li>Suffix (e.g., /filename_sym): Images created under the Batch menu 
            are put in the original folder and the image names are given a suffix</li>
        </ul>
        
        <h2 data-topic='texture averages'>Texture Averages</h2>
        <p>This applies a representative texture to the average, resulting in 
            composite images with more realistic texture instead of the very 
            smooth, bland texture most other averaging programs create. See the 
            papers below for methodological details.</p>
        <ul>
            <li>B. Tiddeman, M. Stirrat and D. Perrett (2005). 
            Towards realism in facial prototyping: results of a wavelet MRF method. 
            <i>Theory and Practice of Computer Graphics</i>.</li>
            <li>B. Tiddeman, D.M. Burt and D. Perrett (2001). 
            Computer Graphics in Facial Perception Research. 
            <i>IEEE Computer Graphics and Applications</i>, 21(5), 42-50.</li>
        </ul>

        <h2 data-topic='sample contours'>Sample Contours</h2>
        <p>This interpolates more control points along the lines. This can 
            improve the accuracy of averages and transforms. If you see a 
            &lsquo;feathery&rsquo; appearance along lines that have many, 
            close-together points, try turning this off.</p>
        
        <h2 data-topic='normalisation'>Normalisation</h2>
        <ul>
            <li>None: averages will have all coordinates as the exact 
            mathematical average of the coordinates in the component templates</li>
            <li>2-point: all images are first aligned to the 2 alignment 
            points designated in the Default alignment preference. Their 
            position is set to their position in the first image in the average</li>
            <li>Rigid-body: procrustes aligns all images to the position of 
            the first image in the average list</li>
        </ul>
        <h2 data-topic='warp'>Warp Types</h2>
        <ul>
            <li>Multiscale: Implements multi-scale affine interpolation for image 
            warping. This is the default, with a good balance between speed and 
            accuracy</li>
            <li>Linear: Implements triangulated linear interpolation for image 
            warping. Linear warping is least accurate, often resulting in image 
            artifacts, but is very fast.</li>
            <!--<li>TPS: Thin plate spline warp</li>-->
            <li>Multiscale RB: Implements multi-scale rigid body interpolation 
            for image warping. This decreases image artifacts in some circumstances, 
            but is much slower.</li>
        </ul>
        <h2 data-topic='mask color'>Mask Color</h2>
        <p>This is the default color for masking, cropping, and rotation. 
            Don't worry, you can always change it in the batch mask, crop and 
            rotate dialogs.</p>
    </div>
    
    <div class='project'>
        <h1 data-topic='project window'>Project Window</h1>
        <h2 data-topic='new project'>Creating a new project</h2>
        <ul>
            <li>If you want to make a new project, choose 'New Project' from 
            the File menu, or click <span class="cmd">P</span>.</li>
            <li>You can edit a project by double-clicking on the name or notes.</li>
        </ul>
        <h2 data-topic='switch project'>Switching projects</h2>
        <ul>
            <li>Choose a project by clicking '[Go]' next to that project on the 
            projects page (<span class="cmd">5</span>).</li>
            <li>To switch to a different project, choose 'Current Project' 
            from the File menu.</li>
        </ul>
        <h2 data-topic='share project'>Sharing projects</h2>
        <ul>
            <li>Type a registered name or email address into that project's box 
            in the Owners column.</li>
            <li>After you type a few characters, a drop-down list of options 
            should appear.</li>
            <li>Choose the correct account and click the + button.</li>
            <li>That account will receive an email from the system notifying 
            them that you have added them to a project.</li>
            <li>Click the - 
        </ul>
    </div>
    <div class='finder'>
        <h1 data-topic='finder window'>Finder Window</h1>
        <h2 data-topic='finder navigation'>Navigating the Finder</h2>
        <ul>
            <li>Click on the folders and files to navigate like you would in 
            the Finder on your computer.</li>
            <li>You can also navigate with the arrow keys.</li>
            <li>You can change the name of a selected folder or file by pressing 
            the Return key</li>
        </ul>
        <h2 data-topic='new folder'>Creating a new folder</h2>
        <ul>
            <li>If you want to make a new folder, click on the 'New Folder' 
            button or click <span class="cmd">N</span>.</li>
            <li>You can change the name of a folder by 
            <span class="cmd">-clicking</span> on its name.</li>
        </ul>
        
        <h2 data-topic='upload'>Uploading files</h2>
        <ul>
            <li>You can upload JPG, GIF, or PNG files.</li>
            <li>You can also upload TEM files. You can upload any type of TEM file.</li>
            <li>You can select multiple files to upload at a time 
            (up to a limit of <?= ini_get('max_file_uploads') ?> files 
            and <?= ini_get('upload_max_filesize') ?>).</li> 
        </ul>
    </div>

    <div class='delineate'>
        <h1 data-topic='delineation window'>Delineation Window</h1>
        <h2 data-topic='template'>Setting up the template</h2>
        <ul>
            <li>Click on the left pupil, the right pupil and the centre of the 
            bottom of the top lip to set up the template.</li>
            <li>The default template has 189 points and is ideal for 
            front-facing faces (the FRL-189 template).</li>
            <li>There are a few other templates for other purposes 
            (e.g., FRL-bodies and Simple-face) that you can choose as a 
            default template in the Preferences (<span class="cmd">,</span>).</li>
        </ul>
        <h2 data-topic='delineating'>Delineating</h2>
        <ul>
            <li>Move each point to delineate the shape of the face.</li>
            <li>When you hover over a point, its point number and 
            location display at the bottom of the screen.</li>
            <li>Shift-click on a point to select or unselect it.</li>
            <li>You can move a group of selected points with the mouse or using 
            the arrow keys.</li>
            <li><span class="cmd">-click</span> on a point to select or unselect 
            the whole line.</li>
            <li>Use <span class="opt">+</span> and <span class="opt">-</span> to 
            increase or decrease the size of a selected group of delineation points.</li>
            <li>You can select more than one line and add/subtract individual points.</li>
            <li>Unselect all points using the &lsquo;Unselect All&rsquo; button, 
            clicking &#8963;A or <span class="cmd">A</span> twice, or by 
            double-clicking on the face image.</li>
            <li>Select a group of points by dragging a rectangle around them. 
            The rectangle won't show on the face yet (I'm working on it).</li>
        </ul>
        <h2 data-topic='new template'>Setting up a new template</h2>
        <ul>
            <li>You can register a template you uploaded from another version of 
            WebMorph or a new one you created here. Registering a template 
            allows you to use it to delineate new images and lets you set the 
            point names and symmetry points.</li>
            <li>First, load an image with the new template into the Delineation 
            Window by double-clicking on the image.</li>
            <li>Then, select the three points you want to use for 3-point 
            delineation by shift-clicking on them. This only works if you 
            select exactly 3 points.</li>
            <li>Next, choose New/Edit Template from the Template menu.</li>
            <li>Make sure that "Register this as a new template" is checked, 
            unless you are editing one of your previously saved templates. 
            You can't edit the public templates that you don't own 
            (e.g., FRL-face, FRL-body, Simple-face).</li>
            <li>Give the template a useful name and notes. The 3 points for 
            3-point delineation will be automatically filled in from the points 
            you selected. You can change or re-order them here.</li>
            <li>After you save the template, choose "Set Point Labels" from the 
            Template menu. Each point will be selected in turn (and an arrow will 
            point at it). Type the name into the box at the top of the screen 
            and press return.</li>
        </ul>
    </div>

    <div class='average'>
        <h1 data-topic='average window'>Average Window</h1>
        <h2 data-topic='creating average'>Creating Averages</h2>
        <ul>
            <li>Select the images you want to average together and click on the 
            &ldquo;View Average&rdquo; button.</li>
            <li>Alternatively, drag the images you want to average together to 
            the average face on the left.</li>
            <li>Large images (e.g., 1350px by 1800px) take about 5 seconds per 
            image in the average, so be patient.</li>
        </ul>
        <h2 data-topic='size and norms'>Size and Normalisation</h2>
        <ul>
            <li>If images are different sizes, you may want to change the default 
            normalisation (under Preferences (<span class="cmd">,</span>) Images) 
            from "None" to "2-point" or "Rigid-body"</li>
            <li>The size of the average will be taken from the first image in 
            the average list.</li>
        </ul>
    </div>
    
    <div class='transform'>
        <h1 data-topic='transform window'>Transform Window</h1>
        <ul>
            <li>Drag the source images to the blank faces.</li>
            <li>Click on the button to see the &ldquo;image to transform&rdquo; 
            transformed by some percentage of the difference between the 
            &ldquo;transform dimension&rdquo; images.</li>
            <li>Select several images in the finder to transform them all the same way.</li>
            <li>Remember, you can only make transforms where all 3 images have 
            the same number of template points.</li>
            <li>It takes about 10 seconds to create a transform with large 
            (e.g., 1350px by 1800px) images.</li>
        </ul>
    </div>
</div>
# News

#### (2021-02-25) Manual
There is now a <a href="https://debruine.github.io/webmorph/">manual</a>!
It's very much under construction, but will become more complete with time.

#### (2021-02-25) Directory Deletion

You can now delete folders and all 
of their contents in one step. Just select a single folder and cmd-delete. 
The full folder will go into the Trash and you can move it out by dragging. 
I tried to build in as many checks as possible to make sure you can't 
accidentally delete things you don't mean to, but file a bug report immediately 
if there are any unintended consequences.

#### (2021-02-24) Built-in Image Sets

You can now load a few built-in image sets under the File menu.

#### (2018-05-11) Create TPS Files

You can now create a TPS file for selected images and templates by 
selecting them and choosing Create TPS File from the Template menu.

#### (2018-04-26) Automatic Delineation

I've switched the automatic delineation to Face++. You can batch 
auto-delineate all selected images in the Finder window or auto-delineate 
single images in the Delineation window.

#### (2018-02-25) Image Grids

Image grids (under the Tools menu) had stopped working. 
They're now fixed.

#### (2018-02-15) Batch Function bug fixed

There was a bug that deleted the first line of batch transforms and 
batch edits. It's fixed.

#### (2018-02-04) Masked Scrambles

You can now scramble inside of a mask, rathe than choosing set 
squares to scramble. Choose a mask style or set the mask colour from the 
top left corner of the image.

#### (2018-02-01) Masking Colour from Image Patch

In the batch align, rotate, crop and mask functions, you can now 
specify the masking colour from an image patch by defining the coordinates 
of the top left and bottom right corners of the path. For example, if 
you want to match the median colour of the top 10 rows of pixels, set the 
patch coordinates to 0, 0 and 2000, 10 (if you set the coordinates outside 
the range of the image size, it will default to the width of the image).

#### (2018-01-29) Batch Functions

I've made several updates to batch functions so they are easier to 
use. You can now also upload your batch files as tab-delimited .txt files 
and load them by double-clicking them in the finder.

#### (2017-06-19) Dragging Finder Files

I've reinstated drag and drop, but will probably refine this in the 
future to get webmorph to work better with touch screens.

#### (2017-06-19) Various Bug Fixes

<ul>
    <li>Fit points move with the image if you zoom during 3-point delineation.</li>
    <li>Tem files are updated in the finder when you save a new delineation.</li>
    <li>The refresh button has been moved away from the save button on the 
    delineation page to prevent accidental refreshes.</li>
    <li>Checkboxes were not showing as checked in Firefox on Windows. I think they're fixed now.</li>
    <li>3-point delineation can now be undone/redone.</li>
</ul>

#### (2017-03-10) Dragging Finder Files

Dragging files in the finder to move 
them between folders was causing too many glitches, especially with touch 
screens, so I've removed this function. You can still move files between 
folders by selecting them, copying (<span class="cmd">C</span>) or cutting 
(<span class="cmd">X</span>) under the Edit menu, and pasting 
(<span class="cmd">V</span>) into the new folder. In the Average and 
Transform windows, double-click on the image boxes to load selected images.

#### (2017-02-27) 3D Images

WebMorph now has limited support for 3D faces. You can upload OBJ 
files and associated BMP or JPEG textures and view them in the delineator. 
You can also morph between two objects that have the same geometry. I 
will add further functions soon. New projects contain example OBJs 
if you want to have a play.

#### (2017-02-14) Masking Transparency and Blur

Transparency has been fixed in the masks and blur works better now. 
Transparency with blur still looks a bit odd, so I would keep blur to 0 
or 1 on images with transparency. As always, masks are smoother on bigger 
images, but take longer.

#### (2017-02-13) Reverse Masking

There is now a checkbox in the batch masking interface to specify a 
reverse mask.

#### (2017-02-13) Scrambling

I fixed some glitches in the scrambling interface. You can now make 
very fine grid scrambles and the offset works correctly. You can also 
create symmetric scrambles, where the scramble order on the left side of 
the image mirrors the scramble order on the right side of the image.

#### (2017-02-12) Keyboard Shortcuts

I had to remove several keyboard shortcuts because they were 
incompatible with some web browsers. I also removed most of the keyboard 
shortcuts for batch functions. Do let me know if you have an idea for a 
useful new keyboard shortcut.

#### (2017-02-12) Batch Transforms

Batch transforms now handle comma-separated lists and relative file 
names. Percent symbols (%) are now optional in the shape, color, and 
texture columns.

#### (2017-02-12) File history

File histories are now saved and displayed in a way that is easier 
to read and replicate.

#### (2017-01-10) URL Hash

Added methods to keep selected files and folders in the Finder 
window between page reloads.

#### (2017-01-06) Fixed Template Editing

Some of the template editing functions were broken because of other 
changes. These should be fixed now.

#### (2016-09-06) Template Visualization

You can now visualize templates and save them as PNG files with the 
Visualize Template (<span class='shiftcmd'>D</span>) command under the 
Batch menu..

#### (2016-08-22) Project size listing

I updated how the project sizes load so that initial loading of the 
project list is faster.

#### (2016-08-17) Delete Projects

You can now delete projects if you are the owner. Please be careful; 
it is not easy to undelete projects.

#### (2016-07-14) Light Table

You can turn on the Light Table under the View menu to compare 
images dragged onto the Light Table. Double-click on images to remove 
them.

#### (2016-06-11) Touch Compatibility

WebMorph is now more compatible with touch devices. Let me know 
if there is anything you can't do on a touch device.

#### (2016-06-11) Batch Edit

There is a new Batch function called Batch Edit 
(<span class='shiftcmd'>E</span>). You can align, resize, rotate, crop, 
mask, symmetrise and/or mirror images in a single step from a batch 
script. I've also provided demo scripts for 
<a href='https://webmorph.org/include/examples/templates/_batchEdit.txt'>batch edit</a>, 
<a href='https://webmorph.org/include/examples/templates/_batchAvg.txt'>batch average</a>, and 
<a href='https://webmorph.org/include/examples/templates/_batchTrans.txt'>batch transform</a>.
Just download them and open them in Excel.

#### (2016-06-11) Finder Loading

The finder shouldn't reload all of the time and should load images 
from the queue as they are made. There are definitely a few bugs, so just 
refresh (<span class='cmd'>R</span>) the finder if your file structure 
doesn't look right.

#### (2016-04-20) Read-Only Project Members

Projects can now have read-only members. Click on the A or R next to 
a project member's name to toggle their access permission. I have not 
yet thoroughly checked that read-only members cannot make any destructive 
project changes, so please be careful and only add project members you trust.

#### (2016-04-20) Template Conversions

Template conversions can now handle templates without images. We 
also added a new template: the 129-point template from Scott et al. 
(2010) PLoS One for use in Geometric MorphoMetric analyses. I also fixed 
some bugs with the new file namer for batch functions.

#### (2016-04-14) Scrambles

Check out the new batch scrambling function!

#### (2016-04-14) Downloadable Delineation Images

If you right-click on a delineation in the Delineation window, you 
can now see a contextual menu to download SVG files of the delineation 
(all, lines only, points only, or numbered points). Open this file in a 
text editor to see how easily you can change the line and points colours 
and widths. Drag the file into a web browser to view it.

#### (2016-04-13) Messages

Now development messages are only shown once per account, so you
don't have to close them every time you reload the page. I've also made 
a lot of small changes to tidy up the code. Let me know if it breaks 
anything.

#### (2016-04-08) Multiple Directory Functions

You can now select folders and apply batch functions 
(and deletion) to several folders at once. You can also bypass the  
confirmation dialog when deleting files and folders by using 
<span class="shortcut shiftcmd">&#x232b;</span> or 
<span class="shortcut shiftcmd">-backspace</span>. This is likely to 
be a little buggy at first, so make sure you back up your important 
files regularly (which you should already be doing).

#### (2016-04-06) Continuum and Grid

I fixed several bugs in the continuum and grid transforms. I also 
added a new batch function for multiple continua (e.g., morph from A to 
B to C to D...). Watch out, it might still be a bit buggy.

#### (2016-04-04) Account Registration

Account registration is now done automatically through the website 
and account requests are put on a wait list.

#### (2016-03-21) Project Size

Project sizes are now only calculated once at the beginning of a 
session and after changes are made to the project. This prevents long 
delays when switching between the project window and other windows.

#### (2015-11-15) Making Averages

You can now drag files to the average face in the Average Window to 
add them to an average. This is useful if you want to average images 
from more than one folder. You can also include the same image more than
once. The first image in the list provides the height and width for the 
average image and also the coordinates for 2-point or rigid-body alignment.

#### (2015-11-10) Projects

Your files are now organised into shareable projects. For now:
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
projects by downloading and saving the files.

#### (2015-10-17) Changes to file deletion

Deleted files are now moved to a Trash folder that is viewable 
through the View menu. The ability to empty the trash folder will be added soon.

#### (2015-10-02) Changes to the delineation interface

The Kinetic library had been causing many crashes, so I've removed 
that and now the delineations are done somewhat differently. This won't 
cause much difference for most users, apart from some cosmetic changes 
in cross and line highlighting. For now, deleting lines is no longer 
possible (although you can still undo them if you're creating a new template).

#### (2015-01-06) Changes to averaging and transforming

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

#### (2014-11-21) Decreased initial loading time

I've put in place a temporary fix to help accounts with >1000 images 
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

#### (2014-07-21) Delineation Improvements

Use <span class="opt">+</span> and <span class="opt">-</span> to increase 
or decrease the size of a selected group of delineation points.

#### (2014-06-15) Queue

Batch transformations are now placed in a queue, where they can complete 
in the background and be paused and restarted. [2014-11-21: Pausing seems 
to be broken. This is on my to-do list.]

#### (2014-05-22) Template changes and menu reorganisation

I'm reorganising the menu items, so their location and shortcuts will 
be changing a bit over the next few weeks. I'm also trying to make it 
easier to manage multiple template styles.

#### (2014-05-07) Major upgrade

Most of the changes are behind the scenes to improve security and stability.
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
    
#### (2013-11-17) Major interface change

The array of images in the Average and Transform windows have been 
replaced by the Finder. This required a major change to almost all 
functions, so please let me know when you find remaining bugs. You can 
now save new averages and transforms by dragging them into the Finder.

#### (2013-11-12) Webcam Upload

From any folder in the finder, select <code>File > Webcam Upload 
(<span class="cmd">W</span>)</code> to use your webcam to take a 
picture, name it and save it.

#### (2013-11-10) Batch rename

Select files in the finder and select <code>Actions > Batch Rename 
(<span class="shiftcmd">N</span>)</code> to add a prefix, suffix, 
index, and multiple search and replace.

#### (2013-11-01) Drag and drop file moving

Try dragging selected files (as many as you want) or folders 
(one folder at a time for now) into another folder

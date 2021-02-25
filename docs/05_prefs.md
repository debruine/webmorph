# Preferences

## Personal

We ask for your personal information to help with the development of WebMorph. This is optional, apart from a working email address, which you will need to be able to get and reset your password. We're always happy to hear from you about what sort of things would make WebMorph better or easier to use.

### Default Project

This used to do something, but doesn't anymore.

### Theme Colour

Set a theme color for the interface. 0 is a light neutral color 
scheme. Go all the way to the right for a dark color scheme (this is experimental and might be a little ugly or have inappropriate contrast).

## Images

### Show thumbnail images

This shows small thumbnails in the finder. If you have many images in your project, this can slow down finder loading.

### Default image format {#prefs-default-image-format}

The image format in which to save new images. You can always change the image format in any individual Batch process dialog.

* `JPG`: This is the default, as it has relatively small file size 
(we use the best-quality JPEG compression of 100), so a 1350x1800 
pixel image will be about 0.5 MB. JPGs can contain embedded 
information about the image.
* `PNG`: This is uncompressed (we use the best-quality PNG 
compression of 0), so a 1350x1800 pixel image will be about 7.0 MB. 
This file type is good for very small images or for images where 
exact color is important. PNGs can also use transparency, so images 
with transparent masks are always saved as PNGs.</li>
* `GIF`: This is uncompressed, but images are smaller than PNGs, so 
a 1350x1800 pixel image will be about 1.0 MB. Transparency is 
possible, but not as sophisticated as in PNGs. We mainly use GIFs 
as the frames for animated GIFs.

### Batch default names

* Folder (e.g., `/sym/filename`): Images created under the Batch menu 
are put in a new folder inside the original folder and the image 
names are kept the same as the originals
* Prefix (e.g., `/sym_filename`): Images created under the Batch menu 
are put in the original folder and the image names are given a prefix
* Suffix (e.g., `/filename_sym`): Images created under the Batch menu 
are put in the original folder and the image names are given a suffix

### Texture Averages {#prefs-texture-averages}

This applies a representative texture to the average, resulting in 
composite images with more realistic texture instead of the very 
smooth, bland texture most other averaging programs create. See the 
papers below for methodological details.

* B. Tiddeman, M. Stirrat and D. Perrett (2005). 
Towards realism in facial prototyping: results of a wavelet MRF method.
<i>Theory and Practice of Computer Graphics</i>.

* B. Tiddeman, D.M. Burt and D. Perrett (2001). 
Computer Graphics in Facial Perception Research. 
<i>IEEE Computer Graphics and Applications</i>, 21(5), 42-50.


### Sample Contours {#prefs-sample-contours}

This interpolates more control points along the lines. This can 
improve the accuracy of averages and transforms. If you see a 
"feathery" appearance along lines that have many, 
close-together points, try turning this off.
        
### Normalisation {#prefs-normalisation}

* None: averages will have all coordinates as the exact 
mathematical average of the coordinates in the component templates
* 2-point: all images are first aligned to the 2 alignment 
points designated in the Default alignment preference. Their 
position is set to their position in the first image in the average
* Rigid-body: procrustes aligns all images to the position of 
the first image in the average list

### Warp Types {#prefs-warp-types}

* Multiscale: Implements multi-scale affine interpolation for image 
warping. This is the default, with a good balance between speed and 
accuracy
* Linear: Implements triangulated linear interpolation for image 
warping. Linear warping is least accurate, often resulting in image 
artifacts, but is very fast.
* Multiscale RB: Implements multi-scale rigid body interpolation 
for image warping. This decreases image artifacts in some circumstances, but is much slower.

### Mask Color {#prefs-mask-color}

This is the default color for masking, cropping, and rotation. 
Don't worry, you can always change it in the batch mask, crop and 
rotate dialogs.

## Templates

### Default Template {#prefs-default-template}

This is the template that is used when you fit a new template to an image that doesn't have a template yet. WebMorph comes with several templates and defaults to the 189-point FRL-Face template for forward-facing faces. You can also [define your own templates](#new-template).

### Default Alignment {#prefs-default-alignment}

Set the parameters to use for batch alignment and 2-point normalisation.You can override this in the Batch Align dialog.

Typically, the two points are the left and right eye points. You also need to specify an image size. Images normalised with these setting will be rotated and resized such that the two points are in the specified place, and the image will be cropped to the specified size. 

<div class="info">
The default settings for the FRL-Face template are x = 496.98 and y = 825.688 for the left eye point (0) and x = 853.02 and y = 825.688 for the right eye point (1). I know this is weird, but it was taken from a composite image that the Face Research Lab used for years to standardise all images, since the only way you can do this in PsychoMorph is using an existing template. 
</div>

### Delineation Apprearance

Change the colours of the unselected and selected delineation crosses, and the lines, as well as the thickness of the lines. This can be useful if you're delineating images with very dark or light colours.





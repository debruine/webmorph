# Batch Processes



## Batch Functions

Select multiple images in the Finder and use the functions under the Batch menu to apply transformations to all of the selected images. The individual processes will be added to the Queue and complete in the background.

<div class="try">
I am writing an [R package](https://facelab.github.io/webmorph/) to do much of this on your own computer, which can often be faster than the webmorph server. 
</div>

### Rename

Replace text, add a prefix or suffix, or add an index to selected images.

<div class="figure">
<img src="images/batch_rename.png" alt="Batch Rename." width="633" />
<p class="caption">(\#fig:fig-batch-rename)Batch Rename.</p>
</div>

### Align

Align delineated images on two points. This rotates and resizes images so that the specified points are all in the same place (usually the pupils). 

The default values for the alignment come from [Preferences](#prefs-default-alignment), but you can change them. 

If the alignment makes an image smaller than the image size, the background will be the specified colour.

<div class="figure">
<img src="images/batch_align.png" alt="Batch Align." width="634" />
<p class="caption">(\#fig:fig-batch-align)Batch Align.</p>
</div>

### Crop

Crop an image by specifying the number of pixels to add or remove from each side.

You can select the background colour for added pixels from the average of a patch of the image whose coordinates you specify (defaulting to the top left 10 pixel square).

<div class="figure">
<img src="images/batch_crop.png" alt="Batch Crop." width="637" />
<p class="caption">(\#fig:fig-batch-crop)Batch Crop.</p>
</div>

### Mask

Masking allows you to use the delineation lines to mask off areas of an image. There are several masks built into WebMorph that work with the FRL-Face template, but you can define your own.

<div class="figure">
<img src="images/batch_mask.png" alt="Batch Mask." width="861" />
<p class="caption">(\#fig:fig-batch-mask)Batch Mask.</p>
</div>

You can combine masks. The interface will let you visualise what each mask is. Reversing a mask puts the colour inside the mask instead of outside (although the masking interface still shows an external mask, sorry). If you set the mask to transparent, the masked images will be PNGs with transparency.

<div class="warning">
The blur function doesn't work as well as PsychMorph's and you usually can't tell the difference with images that are large.
</div>

### Mirror

Batch mirror mirror-reverses the images and their templates. Templates need to have their symmetry points defined in order to do this. For example, in the FRL-Face template, point 0 is the left pupil and point 1 is the right pupil, so in the mirror-reversed version, the x-coordinates are all flipped and the identities of matching points are swapped so that the pupil point on the left side of the image is 0 in both original and mirror-reversed versions. This is the first step to creating a symmetric face.

### Resize

### Rotate

### Scramble

### Symmetrise

## Batch Files

WebMorph has three types of batch files that let you process many images programmatically.

### Batch Average

[Batch Average Template](https://webmorph.org/include/examples/templates/_batchAvg.txt)

Put the name of each average on the first row and the images in the average in the rows below. Put each average in a new column.

In the example below, the first average is made from two images (m_multi and f_multi) and will be saved in a folder called avg_test as androgynous.jpg. The second example will be made from 4 images, 3 of which are the same (so will be 1/4 f_multi and 3/4 m_multi).


|/avg_test/androgynous.jpg |/avg_test/3male1female.jpg |
|:-------------------------|:--------------------------|
|/composites/m_multi.jpg   |/composites/f_multi.jpg    |
|/composites/f_multi.jpg   |/composites/m_multi.jpg    |
|                          |/composites/m_multi.jpg    |
|                          |/composites/m_multi.jpg    |



### Batch Transform

[Batch Transform Template](https://webmorph.org/include/examples/templates/_batchTrans.txt)


|trans-img               |from-img                |to-img                  |shape |color |texture |outname                       |
|:-----------------------|:-----------------------|:-----------------------|:-----|:-----|:-------|:-----------------------------|
|/composites/f_multi.jpg |/composites/m_multi.jpg |/composites/f_multi.jpg |50    |0     |0       |/trans_test/female_fem50.jpg  |
|/composites/f_multi.jpg |/composites/m_multi.jpg |/composites/f_multi.jpg |-50   |0     |0       |/trans_test/female_masc50.jpg |
|/composites/m_multi.jpg |/composites/m_multi.jpg |/composites/f_multi.jpg |50    |0     |0       |/trans_test/male_fem50.jpg    |
|/composites/m_multi.jpg |/composites/m_multi.jpg |/composites/f_multi.jpg |-50   |0     |0       |/trans_test/male_masc50.jpg   |
|/composites/f_white.jpg |/composites/f_multi.jpg |/composites/f_white.jpg |25%   |25%   |25%     |/trans_test/f_wh_caric.jpg    |

### Batch Edit

[Batch Edit Template](https://webmorph.org/include/examples/templates/_batchEdit.txt)


|image                     |align                       |resize      |rotate          |crop                         |mask                      |sym         |mirror |order                             |outname                             |
|:-------------------------|:---------------------------|:-----------|:---------------|:----------------------------|:-------------------------|:-----------|:------|:---------------------------------|:-----------------------------------|
|/composites/f_multi.jpg   |DEFAULT                     |FALSE       |FALSE           |FALSE                        |FALSE                     |FALSE       |FALSE  |                                  |/edit_test/align_default.jpg        |
|/composites/f_easian.jpg  |FRL                         |FALSE       |FALSE           |FALSE                        |FALSE                     |FALSE       |FALSE  |                                  |/edit_test/align_frl.jpg            |
|/composites/f_african.jpg |0,1,200,300,400,300,600,600 |FALSE       |FALSE           |FALSE                        |FALSE                     |FALSE       |FALSE  |                                  |/edit_test/align_square.jpg         |
|/composites/f_white.jpg   |FALSE                       |50%         |FALSE           |FALSE                        |FALSE                     |FALSE       |FALSE  |                                  |/edit_test/resize_50_percent.jpg    |
|/composites/f_wasian.jpg  |FALSE                       |null,400px  |FALSE           |FALSE                        |FALSE                     |FALSE       |FALSE  |                                  |/edit_test/resize_400px_tall.jpg    |
|/composites/m_multi.jpg   |FALSE                       |200px,200px |FALSE           |FALSE                        |FALSE                     |FALSE       |FALSE  |                                  |/edit_test/resize_200x200px.jpg     |
|/composites/m_easian.jpg  |FALSE                       |300px,null  |FALSE           |FALSE                        |FALSE                     |FALSE       |FALSE  |                                  |/edit_test/resize_300px_wide.jpg    |
|/composites/m_african.jpg |FALSE                       |50%,25%     |FALSE           |FALSE                        |FALSE                     |FALSE       |FALSE  |                                  |/edit_test/resize_50x25_percent.jpg |
|/composites/m_white.jpg   |FALSE                       |FALSE       |90              |FALSE                        |FALSE                     |FALSE       |FALSE  |                                  |/edit_test/rotate_90.jpg            |
|/composites/m_wasian.jpg  |FALSE                       |FALSE       |45,rgb(255,0,0) |FALSE                        |FALSE                     |FALSE       |FALSE  |                                  |/edit_test/rotate_45red.jpg         |
|/composites/f_multi.jpg   |FALSE                       |FALSE       |-90             |FALSE                        |FALSE                     |FALSE       |FALSE  |                                  |/edit_test/rotate_neg90.jpg         |
|/composites/f_multi.jpg   |FALSE                       |FALSE       |FALSE           |-300,0,-300,0                |FALSE                     |FALSE       |FALSE  |                                  |/edit_test/crop_short.jpg           |
|/composites/f_multi.jpg   |FALSE                       |FALSE       |FALSE           |100,100,100,100,rgb(0,255,0) |FALSE                     |FALSE       |FALSE  |                                  |/edit_test/crop_green_border.jpg    |
|/composites/f_multi.jpg   |FALSE                       |FALSE       |FALSE           |FALSE                        |(face),10,rgb(255,0,0)    |FALSE       |FALSE  |                                  |/edit_test/mask_red.jpg             |
|/composites/f_multi.jpg   |FALSE                       |FALSE       |FALSE           |FALSE                        |(face),0,rgb(0,255,0)     |FALSE       |FALSE  |                                  |/edit_test/mask_green.jpg           |
|/composites/f_multi.jpg   |FALSE                       |FALSE       |FALSE           |FALSE                        |(face,neck,ears),0        |FALSE       |FALSE  |                                  |/edit_test/mask_face_neck_ears.jpg  |
|/composites/f_multi.jpg   |FALSE                       |FALSE       |FALSE           |FALSE                        |(neck),0                  |FALSE       |FALSE  |                                  |/edit_test/mask_neck.jpg            |
|/composites/f_multi.jpg   |FALSE                       |FALSE       |FALSE           |FALSE                        |(left_eye),0              |FALSE       |FALSE  |                                  |/edit_test/mask_left_eye.jpg        |
|/composites/f_multi.jpg   |FALSE                       |FALSE       |FALSE           |FALSE                        |(right_eye),0             |FALSE       |FALSE  |                                  |/edit_test/mask_right_eye.jpg       |
|/composites/f_multi.jpg   |FALSE                       |FALSE       |FALSE           |FALSE                        |(eyes),0                  |FALSE       |FALSE  |                                  |/edit_test/mask_eyes.jpg            |
|/composites/f_multi.jpg   |FALSE                       |FALSE       |FALSE           |FALSE                        |(left_brow),0             |FALSE       |FALSE  |                                  |/edit_test/mask_left_brow.jpg       |
|/composites/f_multi.jpg   |FALSE                       |FALSE       |FALSE           |FALSE                        |(right_brow),0            |FALSE       |FALSE  |                                  |/edit_test/mask_right_brow.jpg      |
|/composites/f_multi.jpg   |FALSE                       |FALSE       |FALSE           |FALSE                        |(brows),0                 |FALSE       |FALSE  |                                  |/edit_test/mask_brows.jpg           |
|/composites/f_multi.jpg   |FALSE                       |FALSE       |FALSE           |FALSE                        |(left_ear),0              |FALSE       |FALSE  |                                  |/edit_test/mask_left_ear.jpg        |
|/composites/f_multi.jpg   |FALSE                       |FALSE       |FALSE           |FALSE                        |(right_ear),0             |FALSE       |FALSE  |                                  |/edit_test/mask_right_ear.jpg       |
|/composites/f_multi.jpg   |FALSE                       |FALSE       |FALSE           |FALSE                        |(ears),0                  |FALSE       |FALSE  |                                  |/edit_test/mask_ears.jpg            |
|/composites/f_multi.jpg   |FALSE                       |FALSE       |FALSE           |FALSE                        |(nose),0                  |FALSE       |FALSE  |                                  |/edit_test/mask_nose.jpg            |
|/composites/f_multi.jpg   |FALSE                       |FALSE       |FALSE           |FALSE                        |(mouth),0                 |FALSE       |FALSE  |                                  |/edit_test/mask_mouth.jpg           |
|/composites/f_multi.jpg   |FALSE                       |FALSE       |FALSE           |FALSE                        |FALSE                     |shape,color |FALSE  |                                  |/edit_test/sym_shape_color.jpg      |
|/composites/f_multi.jpg   |FALSE                       |FALSE       |FALSE           |FALSE                        |FALSE                     |shape       |FALSE  |                                  |/edit_test/sym_shape.jpg            |
|/composites/f_multi.jpg   |FALSE                       |FALSE       |FALSE           |FALSE                        |FALSE                     |color       |FALSE  |                                  |/edit_test/sym_color.jpg            |
|/composites/f_multi.jpg   |FALSE                       |FALSE       |FALSE           |FALSE                        |FALSE                     |FALSE       |TRUE   |                                  |/edit_test/mirror.jpg               |
|/composites/f_multi.jpg   |FRL                         |50%         |90              |10,10,10,10,rgb(255,0,0)     |(face),0,rgb(255,255,255) |shape,color |FALSE  |                                  |/edit_test/multi_1a.jpg             |
|/composites/f_multi.jpg   |FRL                         |50%         |90              |10,10,10,10,rgb(255,0,0)     |(face),0,rgb(255,255,255) |shape,color |FALSE  |align,resize,rotate,crop,mask,sym |/edit_test/multi_1b.jpg             |
|/composites/f_multi.jpg   |FRL                         |50%         |90              |10,10,10,10,rgb(255,0,0)     |(face),0,rgb(255,255,255) |shape,color |FALSE  |resize,rotate,crop,mask,sym,align |/edit_test/multi_2.jpg              |
|/composites/f_multi.jpg   |FRL                         |50%         |90              |10,10,10,10,rgb(255,0,0)     |(face),0,rgb(255,255,255) |shape,color |FALSE  |crop,resize,sym,align             |/edit_test/multi_3.jpg              |
|/composites/f_multi.jpg   |0,1,200,300,400,300,600,600 |50%         |90              |10,10,10,10,rgb(255,0,0)     |(face),0,rgb(255,255,255) |shape,color |FALSE  |crop                              |/edit_test/multi_4.jpg              |
|/composites/f_multi.jpg   |0,1,200,300,400,300,600,600 |50%         |90              |10,10,10,10,rgb(255,0,0)     |(face),0,rgb(255,255,255) |shape,color |FALSE  |crop,crop                         |/edit_test/multi_5.jpg              |

## Other Functions

### Calculate FacialMetrics

Create CSV files with the Lab* values for each pixel. Check 'ignore mask' to omit pixel values that are the same as the top left pixel color. A CSV file will be created for each image with columns x, y, L , a, and b, with the x and y-coordinates of each pixel and their L*, a and b colour values.


Table: (\#tab:lab)Example rows from an Lab colour file.

|  x|  y|       L|        a|       b|
|--:|--:|-------:|--------:|-------:|
| 96| 80| 90.0460| -64.7454| 54.8329|
| 96| 83| 90.1418| -63.9054| 53.6131|
| 96| 84| 90.1522| -63.9010| 54.5343|
| 96| 85| 90.1522| -63.9010| 54.5343|
| 96| 86| 90.1522| -63.9010| 54.5343|
| 96| 87| 89.8794| -63.2933| 53.3068|
| 96| 89| 90.0148| -64.9838| 54.7882|
| 96| 90| 90.0041| -65.1015| 55.2222|
| 96| 91| 90.0148| -64.9838| 54.7882|


### Lab* Pixels

### Image Grid

Make a 2-dimensional grid of images.

<div class="figure">
<img src="images/batch_grid.png" alt="Image Grid." width="1209" />
<p class="caption">(\#fig:fig-batch-grid)Image Grid.</p>
</div>

### Multiple Continua

Create multiple morphing continua.

<div class="figure">
<img src="images/batch_multiple_continua.png" alt="Multiple Continua." width="578" />
<p class="caption">(\#fig:fig-batch-multiple-continua)Multiple Continua.</p>
</div>


### Moving Gif

Select a set of images, such as a multiple continuum, choose Moving Gif from the Batch menu, and create an animated image.

<img src="images/batch_moving.gif" alt="moving gif">

<div id='transformInterface' class='interface'>
	<!-- DESTINATION IMAGES -->
	<ul id='destimages' class='feature'>
		<li>
			<div><img src='/include/images/blankface.php' alt='image to transform' id='transimage' /><br />image to transform</div>
			<div><img id='transform' class='nodrop' src='/include/images/blank' alt='transform' /><br />transformed image</div>
		</li>
		<li>
			<div><img src='/include/images/blankface.php' alt='image type to transform from' id='fromimage' /></div>
			<div><img src='/include/images/blankface.php' alt='image type to transform to' id='toimage' /></div>
		</li>
		<li>- transform dimension +</li>
		
		<!-- transform controller -->
		<li id='trans_settings'>
			<!-- shape percent transformation -->
			<span id='pcnt_trans'><label for='shapePcnt0'>Shape</label>
			<input type='number' step='any' id='shapePcnt0' value='50' maxlength='7' /> %</span>

			<!-- color percent transformation -->
			<span id='color_pcnt_trans'><label for='colorPcnt0'>color</label>
			<input type='number' step='any' id='colorPcnt0' value='50' maxlength='7' /> %</span>
			
			<!-- texture percent transformation -->
			<span id='texture_pcnt_trans'><label for='texturePcnt0'>Texture</label>
			<input type='number' step='any' id='texturePcnt0' value='50' maxlength='7' /> %</span>
		</li>
		
		<li id='continuum'>Show Continuum Settings</li>
		
		<!-- movie controller -->
		<li class='movie_settings'><label>Shape:</label>
			<input type='number' step='any' id='startShapePcnt' size='7' maxlength='7' value='0' /> to 
			<input type='number' step='any' id='endShapePcnt' size='7' maxlength='7' value='100' /> %
		</li>
		<li class='movie_settings'><label>color:</label>
			<input type='number' step='any' id='startColorPcnt' size='7' maxlength='7' value='0' /> to 
			<input type='number' step='any' id='endColorPcnt' size='7' maxlength='7' value='100' /> %
		</li>
		<li class='movie_settings'><label>Texture:</label>
			<input type='number' step='any' id='startTexturePcnt' size='7' maxlength='7' value='0' /> to 
			<input type='number' step='any' id='endTexturePcnt' size='7' maxlength='7' value='100' /> %
		</li>
		<li class='movie_settings'>
			<label for='transMovieSteps'>Steps:</label>
			<input type='number' step='1' id="transMovieSteps" size='3' max='101' min='2' value='20' />
			<span id='transMovieStepsDisplay'>(21 images in 5% steps)</span>
		</li>
		<li class='movie_settings'>	
			<label for='transMovieFileName'>Save as:</label> <input id='transMovieFileName' type='text' />
		</li>
		
		<!-- buttons -->
		<li>
			<input type='button' value='Transform' id='transButton' />
			<input type='button' value='Save' id='trans-save-button' />
		</li>
	</ul>
	
	<!-- Grid Interface -->
	<ul id='grid' class='feature'>
		<li>2D Grid of Images <button id="cancel-grid">X</button></li>
		<li>
			<div><img src='/include/images/blankface.php' id='topleft' /></div>
			<div><img src='/include/images/blankface.php' id='topright' /></div>
		</li>
		<li>
			<div><img src='/include/images/blankface.php' id='bottomleft' /></div>
			<div><img src='/include/images/blankface.php' id='bottomright' /></div>
		</li>
		<li id="grid-options">
			<input type="checkbox" id="grid-shape" checked="checked" /><label for="grid-shape">Shape</label>
			<input type="checkbox" id="grid-color" checked="checked" /><label for="grid-color">color</label>
			<input type="checkbox" id="grid-texture" checked="checked" /><label for="grid-texture">Texture</label>
		</li>
		<div class='dim'>
		<li><label for="hdim">Horizontal dimension:</label> <input type="text" id="hdim" /></li>
		<li><label for="hsteps">Horizontal steps:</label> <input type='number' id='hsteps' min='0' max='101' value='20' /></li>
		<li><label for="vdim">Vertical dimension:</label> <input type="text" id="vdim" /></li>
		<li><label for="vsteps">Vertical steps:</label> <input type='number' id='vsteps' min='0' max='101' value='20' /></li>
		<li><label for="gridSaveDir">Save to Directory:</label> <input type="text" id="gridSaveDir" /></li>
		</div>
		<li><input type='button' value='Create Grid' id='createGrid' />
	</ul>
	
	<!-- MultiContinua Interface -->
	<ul id='continua' class='feature'>
		<li>Multiple Continua <button id="cancel-continua">X</button></li>
		<li>Drag images to the faces below</li>
		<li id="continua-imgs">
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
			<img src='/include/images/blankface.php' />
		</li>
		<li id="continua-options">
			<input type="checkbox" id="continua-shape" checked="checked" /><label for="continua-shape">Shape</label>
			<input type="checkbox" id="continua-color" checked="checked" /><label for="continua-color">color</label>
			<input type="checkbox" id="continua-texture" checked="checked" /><label for="continua-texture">Texture</label>
		</li>
		<div class='dim'>
		<li><label for="cimgs">Images:</label> <input type='number' id='cimgs' min='2' max='30' value='3' /></li>
		<li><label for="csteps">Steps/continuum:</label> <input type='number' id='csteps' min='0' max='101' value='20' /></li>
		<li><label for="continuaSaveDir">Save to Directory:</label> <input type="text" id="continuaSaveDir" /></li>
		</div>
		<li><input type='button' value='Create Continua' id='createContinua' />
	</ul>
</div>

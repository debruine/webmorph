<ul class='menubar' data-role="nav-bar">

	<!--<li class='menucategory'><span style='background: center center no-repeat url(/include/images/favicon.gif); width: 22px;'>&nbsp;</span></li>-->

	<li class='menucategory'>
		<span>WebMorph</span>
		<ul>
			<li class='finder delineate average transform login project' id='aboutPsychomorph'>
				About WebMorph
			</li>
			<li class='separator'></li>
			<?php
				if ($_SESSION['user_id'] == 1) {
					echo "			<li class='finder delineate average transform project' id='debug'>Debug</li>\n";
				}
			?>
			<li class='finder delineate average transform project' id='prefs' 
				title="Change login details and default settings">		
				Preferences
				<span class="shortcut cmd">,</span>
			</li>
			<li class='finder average transform' id='dbCleanup'
				title="Make sure the database matches your existing files">
				Clean Database
			</li>
			<li class='separator'></li>
			<li class='finder delineate average transform project' id='logout'>
				Logout
				<span class="shortcut cmd">Q</span>
			</li>
		</ul>
	</li>
	<li class='menucategory'>
		<span>File</span>
		<ul>
			<li class="finder average transform project">
				Current Project
				<span class="shortcut">&#9654;</span>
				<ul class='submenu' id="currentProject">
				</ul>
			</li>
			<li class='finder project' id='newProject'>
				New Project
				<span class="shortcut cmd">P</span>
			</li>
			
			<li class='finder average transform' id='newFolder'>
				New Folder
				<span class="shortcut cmd">N</span>
			</li>
			<li class='finder average transform' id='uploadFiles'
				title="Upload image and tem files from your computer">
				Upload
				<span class="shortcut cmd">U</span>
			</li>
			<li class='finder delineate' id='webcamPhoto'
				title="Upload an image using your webcam">
				Webcam Upload
				<span class="shortcut shiftcmd">W</span>
			</li>
			<li class='finder average transform' id='download'
				title="Download selected files or folders to your computer">
				Download
				<span class="shortcut cmd">D</span>
			</li>
			<li class='finder average transform' id='fileListGet'
				title="Get a copy-able list of the images in this folder for use in batch files">
				Get File List
				<span class="shortcut cmd">G</span>
			</li>
			<li class='separator'></li>
			<li class='delineate average transform' id='save'
				title="Save the delineation or selected image">
				Save
				<span class="shortcut cmd">S</span>
			</li>
			<li class='finder delineate average transform' id='deleteItems'>
				Move to Trash
				<span class="shortcut cmd">&#x232b;</span>
			</li>
			<li class='finder average transform' id='emptyTrash'>
				Empty Trash...
			</li>
			<li class='separator'></li>
			<li class='delineate' id='getInfo'
				title="Get information on the image">
				Get Info
				<span class="shortcut cmd">I</span>
			</li>
		</ul>
	</li>
	<li class='menucategory'>
		<span>Edit</span>
		<ul>
			<li class='delineate' id='undo'>
				Undo			
				<span class="shortcut cmd">Z</span>
			</li>
			<li class='delineate' id='redo'>
				Redo
				<span class="shortcut shiftcmd">Z</span>
			</li>
			<li class='separator'></li>
			<li class='finder average transform' id='cutItems'>
				Cut
				<span class="shortcut cmd">X</span>
			</li>
			<li class='finder delineate average transform' id='copyItems'>
				Copy
				<span class="shortcut cmd">C</span>
			</li>
			<li class='finder delineate average transform' id='pasteItems'>
				Paste
				<span class="shortcut cmd">V</span>
			</li>
			<li class='finder' id='moveFolderToProject'>
				Copy to Project
			</li>
			<li class='separator'></li>
			<li class='finder average transform' id='find'>
				Find
				<span class="shortcut cmd">F</span>
			</li>
			<li class='separator'></li>
			<li class='finder delineate average transform' id='select'>
				Select All
				<span class="shortcut cmd">A</span>
			</li>
		</ul>
	</li>
	<li class='menucategory'>
		<span>View</span>
		<ul>
			<li class='project finder delineate average transform' id='refresh'
				title="Reload the images">
				Refresh
				<span class="shortcut cmd">R</span>
			</li>
			<li class='separator'></li>
			<li class='finder average transform' id='imageview'
				title="">
				Icon View
			</li>
			<li class='finder average transform' id='toggletrash'
				title="Show or hide the Trash folder">
				<span class="checkmark">&nbsp;</span>
				Trash
			</li>
			<li class='separator'></li>
			<li class='average transform' id='toggle_recent'
				title="Show or hide your recently created images">
				<span class="checkmark">&nbsp;</span>
				Recently Created Images
			</li>

			<li class='separator'></li>
			<li class='delineate' id='toggletem'
				title="Show or hide the delineation">
				<span class="checkmark">&nbsp;</span>
				Delineation
				<span class="shortcut cmd">T</span>
			</li>

			<li class='separator'></li>
			<li class='delineate average transform' id='zoomin'>
				Zoom In
				<span class="shortcut cmd">+</span>
			</li>
			<li class='delineate average transform' id='zoomoriginal'>
				Original Size
				<span class="shortcut cmd">0</span>
			</li>
			<li class='delineate average transform' id='zoomout'>
				Zoom Out
				<span class="shortcut cmd">-</span>
			</li>
			<li class='delineate' id='fitsize'>
				Fit to Window
				<span class="shortcut cmd">M</span>
			</li>
		</ul>
		<li class='menucategory'>
		<span>Batch</span>
		<ul>
			<!--
			<li class='finder average transform' id='createTransform'>	
				Create Average/Transform
				<span class="shortcut opt">A</span>
			</li>			
			<li class='separator'>
			-->
			
			<li class='delineate finder average transform' id='batchAverage'
				title="Make several averages using a text file to specify images and output names">		
				Batch Average
				<span class="shortcut shiftcmd">A</span>
			</li>
			<li class='delineate finder average transform' id='batchTransform'
				title="Make several transforms using a text file to specify images and settings">		
				Batch Transform
				<span class="shortcut shiftcmd">T</span>
			</li>
			<li class='separator'>
			<li class='finder average transform' id='convert'
				title="Convert to a different file type">
				Convert File Type
			</li>
			<li class='finder average transform' id='batchRename'
				title="Rename a group of files">
				Rename
				<span class="shortcut shiftcmd">N</span>
			</li>
			<li class='finder average transform' id='alignEyes'
				title="Align selected images to a standard or custom setting">
				Align
				<span class="shortcut shiftcmd">E</span>
			</li>	
			<li class='finder average transform' id='resize'
				title="Resize selected images">
				Resize
				<span class="shortcut shiftcmd">R</span>
			</li>
			<li class='finder average transform' id='rotate'
				title="Rotate selected images">
				Rotate
				<span class="shortcut shiftcmd">L</span>
			</li>
			<li class='finder average transform' id='crop'
				title="Crop selected images">
				Crop
				<span class="shortcut shiftcmd">K</span>
			</li>
			<li class='finder average transform' id='mask'
				title="Mask off areas of selected images with a color">
				Mask
				<span class="shortcut shiftcmd">M</span>
			</li>	
			<li class='finder average transform' id='symmetrise'
				title="Create versions of selected images with symmetrical shape and/or color">
				Symmetrise
				<span class="shortcut shiftcmd">Y</span>
			</li>
			<li class='finder average transform' id='mirror'
				title="Create versions of selected images that are mirrored horizontally">
				Mirror
				<span class="shortcut shiftcmd">I</span>
			</li>
			<li class='finder average transform' id='batchModDelin'
				title="Delete selected template points from selected images">
				Modify Delineation
				<span class="shortcut shiftcmd">D</span>
			</li>
			<li class='separator'>
			<li class='finder' id='singlePCA' style='display: none;'
				title="PCA">		
				PCA
				<span class="shortcut shiftcmd">P</span>
			</li>
			<li class='finder' id='batchPCA' style='display: none;'
				title="Batch PCA">		
				Batch PCA
			</li>
			<li class='finder' id='PCvis' style='display: none;'
				title="Visualise the PCs of a model">		
				Visualise PCs
				<span class="shortcut shiftcmd">V</span>
			</li>
			<li class='finder average transform' id='facialMetrics'
				title="Compute metrics for selected faces using delineation coordinates">
				Calculate FacialMetrics
				<span class="shortcut shiftcmd">F</span>
			</li>
			<li class='delineate finder average transform' id='gridFaces'
				title="Create a 1D or 2D array of faces in a continuum">		
				Grid
				<span class="shortcut shiftcmd">G</span>
			</li>
			<li class='finder average transform' id='movingGif'
				title="Create a moving gif of the selected images">
				Moving Gif
				<span class="shortcut shiftcmd">O</span>
			</li>
			<!--
			<li class='finder average transform' id='colorCalibrate'
				title="Create versions of selected images with calibrated color (requires a color checker chart in the image)">
				Color Calibrate
			</li>
			
			<li class='finder average transform' id='batchTag'
				title="Add or delete tags from selected images">
				Batch Tag
				<span class="shortcut shiftcmd">T</span>
			</li>
			-->
		</ul>
	</li>
	<li class='menucategory'>
		<span>Template <code id="current_tem_name"></code></span>
		<ul>
			<li class="delineate finder">Current Template
				<span class="shortcut">&#9654;</span>
				<ul class='submenu' id="currentTem">
				</ul>
			</li>
			<li class='separator'>
			<li class='delineate finder' id='fitTemplate'
				title="Mark 3 points (eyes and mouth or any 3 selected points) to size the default template to the image">
				Fit Template
				<span class="shortcut opt">F</span>
			</li>
			<li class='delineate' id='autoDelineate'
				title="Automatically delineate using the sky biometry template">
				Auto-Delineate (SkyBiometry)
				<span class="shortcut opt">D</span>
			</li>
			<li class='delineate' id='maskBuilder'
				title="Create a custom mask">
				Custom Mask Builder
			</li>	
			<li class='finder'>Convert Templates
				<span class="shortcut">&#9654;</span>
				<ul class='submenu' id='convert_template_menu'>
					<li class='finder' data-from='30' data-to='1'>RSSSE (94) to FRL (189)</li>
					<li class='finder' data-from='10' data-to='1' >Perception Lab (179) to FRL (189)</li>
					<li class='finder' data-from='1' data-to='10'>FRL (189) to Perception Lab (179)</li>
					<li class='finder' data-from='1' data-to='29'>FRL (189) to FRL-GMM (154)</li>
					<li class='finder' data-from='1' data-to='35'>FRL (189) to Baby-tem (191)</li>
					<li class='finder' data-from='1' data-to='26'>FRL (189) to modSkyBio (65)</li>
					<li class='finder' data-from='18' data-to='18'>Nubility_body_new to NBM for GMM</li>
					<li class='finder' data-from='1' data-to='36'>FRL (189) to Footballers (255)</li>
				</ul>
			</li>
			<li class='delineate' id='closeMouth'
				title="Set internal lip points on the red line to the position of the internal lip points on the blue line (only works for the FRL-189 template)">
				Close Mouth
				<span class="shortcut opt">C</span>
			</li>
			<li class='delineate' id='newLine'
				title="Create a new template line">
				New Line
				<span class="shortcut opt">L</span>
			</li>
			<li class='delineate' id='deleteLine'
				title="Delete a template line">
				Delete Line
				<span class="shortcut shiftopt">L</span>
			</li>
			<li class='separator'>
			<li class='delineate' id='editTemplate'
				title="Edit the default values for this template or register a new template as a default type">
				New/Edit Template
			</li>
			<li class='delineate' id='setPointLabels'
				title="Edit point labels for the default template">
				Set Point Labels
			</li>
			<li class='delineate' id='setSymPoints'
				title="Edit symmetry points for the default template">
				Set Symmetry Points
			</li>
			
		</ul>
	</li>
	<li class='menucategory' id='menu_queue'>
		<span>Queue<div id="queue_n">0</div></span>
		<ul id='queue' style='max-height: 400px; overflow:auto;'>
			<li class='finder delineate average transform' id='clearComplete'>
				Clear Completed	
			</li>
			<li class='finder delineate average transform' id='clearQueue'>
				Clear All	
			</li>
			<li class='finder delineate average transform' id='pauseQueue'>
				Pause All	
			</li>
			<li class='finder delineate average transform' id='restartQueue'>
				Restart All	
			</li>
			<li class='separator'>
		</ul>
	</li>
	<li class='menucategory' id='menu_window'>
		<span>Window</span>
		<ul>
			<li class='project finder delineate average transform' id='showFinder'>
				<span class="checkmark">&nbsp;</span>
				Finder	
				<span class="shortcut cmd">1</span>
			</li>
			<li class='project finder delineate average transform' id='showDelineate'>
				<span class="checkmark">&nbsp;</span>
				Delineate
				<span class="shortcut cmd">2</span>
			</li>
			<li class='project finder delineate average transform' id='showAverage'>
				<span class="checkmark">&nbsp;</span>
				Average
				<span class="shortcut cmd">3</span>
			</li>	
			<li class='project finder delineate average transform' id='showTransform'>
				<span class="checkmark">&nbsp;</span>
				Transform
				<span class="shortcut cmd">4</span>
			</li>
			<li class='project finder delineate average transform' id='showProjects'>
				<span class="checkmark">&nbsp;</span>
				Projects	
				<span class="shortcut cmd">5</span>
			</li>
		</ul>
	</li>
	<li class='menucategory'>
		<span>Help</span>
		<ul>
			<li class='finder delineate average transform login project' id='menuhelp'>Show Help Files</li>
			<li class='finder delineate average transform login project' id='emailLisa'>Email Lisa</li>
			<li class='finder delineate average transform login project' id='whatsnew'>What's New?</li>
			<li class='finder delineate average transform login project' id='citation'>Citation</li>
		</ul>
	</li>
	<li class='menucategory' id='menu_username'>
		<span></span>
	</li>
</ul> 
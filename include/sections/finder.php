<div id='finderInterface' class='interface'>
	<input type="search" id="searchbar">
	
	<ul id='imagebox'>
		<img id='selectedImage' src='/include/images/loaders/circle' />
		<textarea id='selectedTem' readonly></textarea>
		<div id='imagedesc'></div>
	</ul>
	
	<div id='uploadbar'>
		<input class='textinput'
			id='upload'
			name='upload[]'
			type='file'
			value=''
			multiple='multiple'
			placeholder='Upload Images and Tems'
			style='width:320px'/>
	</div>
	
	<span id="finderpath"></span>

	<div id='finder' path='finder'></div>
</div>

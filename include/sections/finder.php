<div id='finderInterface' class='interface'>
    <p class="msg" data-msg_id="file_info">I've made a lot of changes to how JPG 
        and TEM files store their history. You can see the history in the Finder 
        under the preview image or template (only for new files; your old files 
        will not show a history). Let me know if there is other information that 
        you would like about your files.  Information cannot be stored in GIF or 
        PNG images.
    </p>
        
    <input type="search" id="searchbar" name="searchbar" placeholder="Search for a file">
    <ul id='filepreview'>
        <img id='selectedImage' src='/include/images/loaders/circle' />
        <textarea id='selectedTem' readonly></textarea>
        <div id='fileinfo'>
            <h1>File Info</h1>
            <div></div>
        </div>
        <div id='history'>
            <h1>File History</h1>
            <div></div>
        </div>
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
    
    <div id='lightTable'><div>Drag images here to view or compare them.<br>Drag images to reorder and double-click to remove them.</div></div>
</div>

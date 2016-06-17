<div id='finderInterface' class='interface'>
    <p class="msg" data-msg_id="batch_delete">You can now select folders and apply batch functions 
        (and deletion) to several folders at once. You can also bypass the  
        confirmation dialog when deleting files and folders by using 
        <span class="shortcut shiftcmd">&#x232b;</span> or 
        <span class="shortcut shiftcmd">-backspace</span>. This is likely to 
        be a little buggy at first, so make sure you back up your important 
        files regularly (which you should already be doing).
    </p>
    <p class="msg" data-msg_id="finder_changes">I've made a lot of changes to
        the finder. It shouldn't reload all of the time and should load images 
        from the queue as they are made. There are definitely a few bugs, so just 
        refresh (<span class='cmd'>R</span>) the finder if your file structure 
        doesn't look right.
    </p>
    <p class="msg" data-msg_id="batchEdit">There is a new Batch function called 
        Batch Edit (<span class='shiftcmd'>E</span>). You can align, resize, 
        rotate, crop, mask, symmetrise and/or mirror images in a single step 
        from a batch script. I've also provided demo scripts for Batch Average, 
        Batch Transform and Batch Edit. Just download them and open them in Excel.
    </p>
        
    <input type="search" id="searchbar" name="searchbar" placeholder="Search for a file">
    
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

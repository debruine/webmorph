<div id='finderInterface' class='interface'>
    <p class="msg" data-msg_id="batch_delete">You can now select folders and apply batch functions 
        (and deletion) to several folders at once. You can also bypass the  
        confirmation dialog when deleting files and folders by using 
        <span class="shortcut shiftcmd">&#x232b;</span> or 
        <span class="shortcut shiftcmd">-backspace</span>. This is likely to 
        be a little buggy at first, so make sure you back up your important 
        files regularly (which you should already be doing).</p>
        
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

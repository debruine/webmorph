<div id='averageInterface' class='interface'>
    <p class="msg" data-msg_id="avg_drag">Average images by selecting them in 
        the Finder and clicking the average button, or by double-clicking on the 
        average face to add selected images to the list. (You can no longer drag 
        images to the average face.)
    </p>
    
    <ul id='avg_image_box' class='feature'>
        <li><img id='average'><ul id='average-list'></ul></li>
        <!-- buttons -->
        <li id='avg_buttons'>
            <input type='button' id='view-average-button' value='Average' />
            <input type='button' id='clear-average-button' value='Clear' />
            <input type='button' id='save-button' value='Save' />
        </li>
    </ul>
    
    <div id='individual_image_box'></div>
    
    <div id='recent_creations' class='feature'>
        <h2>Recently Created Images 
            <button id='clear_recent_creations'>Clear</button> 
        </h2>
    </div>
</div>
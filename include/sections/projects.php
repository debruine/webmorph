<div id='projectInterface' class='interface'>
    <p class="msg" data-msg_id="3d_brand_new">WebMorph now has limited support 
        for 3D faces. You can upload OBJ files and associated BMP or JPEG textures 
        and view them in the delineator. You can also morph between two objects 
        that have the same geometry. I will add further functions soon. New projects 
        contain two example OBJs if you want to have a play.
    </p>
    <p class="msg" data-msg_id="menu change">The menu items have changed a bit 
        and I've removed or changed a lot of keyboard shortcuts to make them more 
        compatible between web browsers. See what else is new at 
        <a onclick="$('#whatsnew').click();">What's New?</a> under 
        the Help menu.
    </p>
    <p id="total_space">Your project list is loading. This will take 5-30 seconds, 
         depending on the size of your account.</p>
    
    <input type="search" id="projectsearchbar" name="projectsearchbar" placeholder="Search for a project">

    <table id='project_list'>
        <thead><tr><th>Go</th><th>-</th><th>Project</th><th>Description</th><th>Files</th><th>Owners</th></tr></thead>
        <tbody></tbody>
    </table>
</div>

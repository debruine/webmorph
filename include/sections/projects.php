<div id='projectInterface' class='interface'>
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
        <thead><tr><th></th><th>Project</th><th>Description</th><th>Files</th><th>Owners</th></tr></thead>
        <tbody></tbody>
    </table>
</div>

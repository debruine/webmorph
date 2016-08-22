//====================================
// !PROJECT FUNCTIONS
//====================================

function projectList() { console.time('projectList()');
    $('#footer-text').html('Loading Project List...');
    $.ajax({
        url: 'scripts/projListGet',
        type: 'GET',
        async: true,
        success: function(data) {
            if (data.error) { return false; }
            // add projects
            $('#default_project').html('');
            $('#currentProject').html('');
            
            $('#project_list tr').addClass('old');
            
            $.each(data.projects, function(i, p) {
                var $opt,
                    $menuopt,
                    owners,
                    tr,
                    td
                    delProj = '';

                $opt = $('<option />').val(p.id)
                                      .html(p.name)
                                      .attr('title', p.notes);
                if (p.perm == 'read-only') {
                    $opt.addClass('readOnly');
                }
                $('#default_project').append($opt);

                $menuopt = $('<li />').addClass('finder average transform project')
                                      .attr('title', p.notes)
                                      .attr('data-id', p.id)
                                      .html('<span class="checkmark">&nbsp;</span>' + p.name);
                $('#currentProject').append($menuopt);
                $menuopt.find('span.checkmark').hide();

                owners = '<ul class="project_owners">';
                $.each(p.owners, function() {
                    var permAbbrev;

                    owners += '<li title="' + this.email + '" class="' + this.perm + '">';
                    if (this.firstname == '' && this.lastname == '') {
                        owners += this.email + ' ';
                    } else {
                        owners += this.firstname + ' ' + this.lastname + ' ';
                    }

                    // add permission toggle
                    permAbbrev = (this.perm == 'all') ? 'A' : 'R';
                    if (p.user_id == WM.user.id || (p.perm == 'all' && p.user_id !== this.id)) {
                        owners += '<span data-id="'+ this.id +'" class="tinybutton ownerPermToggle" title="permissions = ' + this.perm + '">' + permAbbrev + '</span>';
                    } else {
                        owners += '<span class="tinybutton" title="permissions = ' + this.perm + '">' + permAbbrev + '</span>';
                    }

                    // add delete button
                    if (this.id == p.user_id) {
                        owners += ' *';
                    } else if (p.owners.length > 1 && p.perm == 'all') {
                        owners += '<span data-id="'+ this.id +'" class="tinybutton projectOwnerDelete" title="Remove">-</span>';
                    }
                    owners += '</li>';
                });
                owners += '</ul>';

                tr = $('tr[data-id=' + p.id + ']');

                if (tr.length == 0) {
                    tr = $('<tr data-id="' + p.id + '" data-perm="' + p.perm + '" data-owner="' + p.user_id + '" />');
                    tr.html(   '<td><span class="go_to_project tinybutton">Go</span>'
                             + '</td><td>' + p.name
                             + '</td><td>' + p.notes
                             + '</td><td><img src="/include/images/menu/queue_loading.svg" />'
                             + '</td><td>' + owners + '</td>'
                    );
                             
                    $('#project_list tbody').append(tr);
                } else {
                    tr.attr('data-perm', p.perm);
                    tr.attr('data-owner', p.user_id);
                    td = tr.find('td');
                    td.eq(1).html(p.name);
                    td.eq(2).html(p.notes);
                    td.eq(4).html(owners);
                    tr.removeClass('old');
                }
                
                if (p.hasOwnProperty('size')) {
                    tr.data('filemtime', p.filemtime);
                    tr.data('files', p.files);
                    tr.data('tmp', p.tmp);
                    tr.data('size', p.size);
                    tr.data('mysize', p.mysize);
                }
            });
            
            $('#project_list tr.old').remove();
            $('#project_list').show().stripe();

            WM.user.accountSize = 0;
            if (WM.appWindow == 'project') {
                $.each(data.projects, function(i, p) {
                    //if (p.filemtime == $('tr[data-id=' + p.id + ']').data('filemtime')) {
                    if (p.hasOwnProperty('size')) {
                        projectSizeUpdate(p.id, data.userAllocation.allocation);
                    } else {
                        projectSizeGet(p.id, data.userAllocation.allocation);
                    }
                });
            }

            // set up user list
            userlist = [];
            $.each(data.users, function(i, user) {
                userlist.push({
                    value: user.id,
                    label: user.firstname + ' ' + user.lastname + ', ' + user.email,
                    name: user.lastname + ', ' + user.firstname,
                    email: user.email
                });
            });
            $('input.projectOwnerAdd').closest('li').remove();
            $('tr[data-perm=all] ul.project_owners').append('<li><input class="projectOwnerAdd" '
                                        + 'placeholder="Type Name to Add" /></li>');
            
            // add delete project button where user is the owner and has all permissions
            $('span.delete_project').remove();                            
            $('tr[data-perm=all][data-owner='+WM.user.id+'] span.go_to_project').after('<span class="delete_project tinybutton" title="Delete Project">-</span>');

            $('.projectOwnerAdd').autocomplete({
                source: userlist,
                focus: function( event, ui ) {
                    $(this).val(ui.item.label);
                    return false;
                },
                select: function( event, ui ) {
                    $(this).val(ui.item.label).data('id', ui.item.value);
                    return false;
                }
            }).autocomplete( "instance" )._renderItem = function( ul, item ) {
                return $( "<li>" ).append( item.name + "<br>&nbsp;&nbsp;<i>" + item.email + '</i>').appendTo( ul );
            };
        },
        complete: function() {
            console.timeEnd('projectList()');
            $('#footer-text').html('Project List Loaded');
        }
    });
}

function projectSet(id, appWindow) {
    $.ajax({
        url: 'scripts/projSet',
        data: {
            project: id
        },
        success: function(data) {
            if (data.error) {
                $('<div title="Error Changing Project" />').html(data.errorText).dialog();
                if (WM.appWindow != 'project') {
                    $('#showProject').click();
                }
            } else {
                WM.project.id = id;
                WM.project.perm = data.perm;
                $('#currentProject li span.checkmark').hide();
                $('#currentProject li[data-id=' + id + '] span.checkmark').show();

                if (appWindow != null) {
                    if (appWindow == 'P') {
                        // do nothing
                    } else if (appWindow == 'F') {
                        $('#showFinder').click();
                    } else if (appWindow == 'D') {
                        $('#showDelineate').click();
                    } else if (appWindow == 'A') {
                        $('#showAverage').click();
                    } else if (appWindow == 'T') {
                        $('#showTransform').click();
                    }
                } else if (WM.appWindow == 'project') {
                    $('#showFinder').click();
                    loadFiles(WM.project.id);
                } else {
                    loadFiles(WM.project.id);
                }

                // clean up things
                $('#average-list li').remove();

                // check project permissions
                if (WM.project.perm == 'read-only') {
                    growl('This project is read-only. You can copy folders to your own projects by right-clicking on them. Contact the owner if you need permission to save images to this project.', 5000);
                }
            }
        },
        complete: function() {
            $('#footer-text').html('Project ' + WM.project.id + ' set');
        }
    });
}

function projectSizeGet(proj_id, alloc) {
    $.ajax({
        url: 'scripts/projSizeGet',
        type: 'POST',
        data: {
            proj_id: proj_id
        },
        success: function(data) {
            var tr = $('tr[data-id=' + proj_id + ']');
            tr.data('filemtime', data.filemtime);
            tr.data('files', data.files);
            tr.data('tmp', data.tmp);
            tr.data('size', data.size);
            tr.data('mysize', data.mysize);

            projectSizeUpdate(proj_id, alloc);
        }
    });
}

function projectSizeUpdate(proj_id, alloc) {
    var tr = $('tr[data-id=' + proj_id + ']');
    var td =  tr.find('td').eq(3);

    td.html((tr.data('files') - tr.data('tmp')) + ' files<br>' + tr.data('size'));
    WM.user.accountSize += tr.data('mysize');

    // set warning about total space allocation
    var ts = "Projects you own are using " + round(WM.user.accountSize/1024/1024/1024,1)
           + " GB of your allocated " + round(alloc/1024,1) + " GB. ";
    if (WM.user.accountSize/1024/1024 > alloc) {
        ts += "Please reduce your account by emptying the trash and/or removing files. "
            + "After 15 January 2016, I will disable accounts that are over their space allocation.";
        $('#total_space').addClass('warning');
    } else {
        $('#total_space').removeClass('warning');
    }
    $('#total_space').html(ts);
}

function projectNew() {
    $('#newProjectDialog').dialog({
        title: 'New Project',
        buttons: {
            Cancel: function() {
                $(this).dialog("close");
            },
            'Save': {
                text: 'Save',
                class: 'ui-state-focus',
                click: function() {
                    $(this).dialog("close");

                    var name = $('#new_project_name').val();
                    var notes = $('#new_project_notes').val();
                    $.ajax({
                        url: 'scripts/projNew',
                        data: {
                            name: name,
                            notes: notes
                        },
                        success: function(data) {
                            if (data.error) {
                                $('<div title="Error Creating Project" />').html(data.errorText).dialog();
                            } else {
                                WM.project.id = data.project;
                                projectList();
                                projectSet(WM.project.id);
                            }
                        }
                    });
                }
            }
        }
    });
}

function projectDelete(proj_id) {
    $('<div />').html('Are you sure you want to delete this project? This is permanent.').dialog({
        title: 'Delete Project',
        buttons: {
            Cancel: function() {
                $(this).dialog("close");
            },
            'Delete': {
                text: 'Delete',
                click: function() {
                    $(this).dialog("close");

                    $.ajax({
                        url: 'scripts/projDelete',
                        data: { proj_id: proj_id},
                        success: function(data) {
                            if (data.error) {
                                $('<div title="Error Deleting Project" />').html(data.errorText).dialog();
                            } else {
                                projectList();
                            }
                        }
                    });
                }
            }
        }
    });
}


function projectEdit(td, category) {
    $('#footer-text').html('Editing Project...');
    var oldname = $(td).text();
    var w = $(td).width();
    var $newnameinput;

    if (category == "name") {
        $newnameinput = $('<input />').val(oldname).attr('type', 'text').width(w);
    } else {
        $newnameinput = $('<textarea />').val(oldname).width(w).height($(td).height());
    }

    $newnameinput.keydown(function(e) {
        if (e.which == KEYCODE.enter) {
            e.stopPropagation();
            $(this).blur();
        }
    }).dblclick(function(e) {
        e.stopPropagation();
    }).blur(function() {
        var newname = $(this).val();
        $(td).html(newname);

        if (newname !== '' && newname !== oldname) {
            $.ajax({
                url: 'scripts/projEdit',
                data: {
                    project: $(td).closest('tr').data('id'),
                    category: category,
                    newname: newname
                },
                success: function(data) {
                    if (data.error) {
                        growl(data.errorText);
                        $('#footer-text').html('Project Not Edited');
                    } else {
                        oldname = newname;
                        $('#footer-text').html('Project Edited');
                    }
                },
                complete: function() {
                    $(td).html(oldname);
                }
            });
        } else {
            $(td).html(oldname);
            $('#footer-text').html('');
        }
    }).focusout(function() {
        $(this).blur();
    });

    $(td).html('').append($newnameinput);
    $newnameinput.focus().select();
}

function projectOwnerAdd(button) {
    $('#footer-text').html('Adding Project Owner...');
    var $input = $(button);
    var project = $input.closest('tr').data('id');

    var owner = $input.data('id');

    if (!(project > 0 && owner > 0)) {

        $('#footer-text').html('User not found');
        $input.val('').focus();
        return false;
    }

    $input.hide();

    $.ajax({
        url: 'scripts/projOwnerAdd',
        data: {
            project: project,
            owner: owner
        },
        success: function(data) {
            if (data.error) {
                $('<div title="Error Adding Owner" />').html(data.errorText).dialog();
                $('#footer-text').html('Project Owner Not Added');
                $input.show().val('');
            } else {
                $('#refresh').click();
                $('#footer-text').html('Project Owner Added');
            }
        }
    });
}

function projectOwnerPermToggle() {
    var project,
        perm,
        owner;

    perm = $(this).html() == 'A' ? 'read-only' : 'all';
    project = $(this).closest('tr').data('id');
    owner = $(this).data('id');

    $.ajax({
        url: 'scripts/projOwnerAdd',
        data: {
            project: project,
            owner: owner,
            perm: perm
        },
        success: function(data) {
            if (data.error) {
                $('<div title="Error Changing Owner Permissions" />').html(data.errorText).dialog();
                $('#footer-text').html('Project owner permissions not changed');
                $input.show().val('');
            } else {
                $('#refresh').click();
                $('#footer-text').html('Project owner permissions changed');
            }
        }
    });
}

function projectOwnerDeleteConfirmed(project, owner) { console.log('projectOwnerDeleteConfirmed('+project+', '+owner+')');
    $('#footer-text').html('Deleting Owner...');
    $.ajax({
        url: 'scripts/projOwnerDelete',
        data: {
            project: project,
            owner: owner
        },
        success: function(data) {
            if (data.error) {
                $('<div title="Error Deleting Owner" />').html(data.errorText).dialog();
                $('#footer-text').html('Owner Not Deleted');
            } else {
                $('#refresh').click();
                $('#footer-text').html('Owner Deleted');
            }
        }
    });
}

function projectOwnerDelete(project, owner) { console.log('projectOwnerDelete('+project+', '+owner+')');
    if (owner == WM.user.id) {
        $('<div />').html("Are you sure you want to leave this project? You will not be able to undo this without having another owner re-add you.").dialog({
            title: 'Remove Yourself from Project',
            buttons: {
                Cancel: function() { $(this).dialog("close"); },
                "Leave Project": {
                    text: 'Leave Project',
                    click: function() {
                        $(this).dialog("close");
                        projectOwnerDeleteConfirmed(project, owner);
                    }
                }
            }
        });
    } else {
        projectOwnerDeleteConfirmed(project, owner);
    }
}
//====================================
// !USER FUNCTIONS
//====================================

function loginGoogle(authResult) {
    growl(JSON.stringify(authResult));
}

function resetPassword() {
    var email = $('#login_email').val();
    
    if (email == '') {
        $('#login_error').html("<li>Please fill in your email address first.</li>");
        return false;
    }
    $('#login_error').html("<li>Checking for your account...</li>");

    $.ajax({
        url: 'scripts/userPasswordReset',
        data: { email: email },
        success: function(data) {
            if (data.error) {
                $('#login_error').html('<li>' + data.errorText + '</li>');
            } else {
                $('#login_error').html("<li>Check your email for the new password.</li>");
            }
        }
    });
}

function registerUser(e) { 
    if ($('#loginInterface .reg_item:visible').length) {
        // check validity
        var error = false;
        $('#login_error').html('');
        
        if ($('#login_email').val().length < 7) {
            error = true;
            $('#login_error').append('<li>Your email address needs to be an email address</li>');
            $('#login_email').addClass('error').focus().select();
        } else {
            $('#login_email').removeClass('error');
        }
    
        /*if ($('#login_auth').val().length != 7) {
            error = true;
            $('#login_error').append('<li>Please enter the correct invite code. Access to online psychomorph is currently restricted. Ask Lisa for an invite code if you would like to be an alpha tester.</li>');
            $('#login_auth').addClass('error').focus().select();
        } else {
            $('#login_auth').removeClass('error');
        }*/
        
        if (error) { return false; }
        
        $('#login_error').append('<li>Checking your registration details...</li>');
        
        // register a new user
        $.ajax({
            url: 'scripts/userRegister',
            async: false,
            data: {
                email: $('#login_email').val(),
                //password: $('#login_password').val(),
                invite: $('#login_auth').val(),
                //login_keep: $('#login_keep').prop('checked'),
                firstname: $('#reg_firstname').val(),
                lastname: $('#reg_lastname').val(),
                org: $('#reg_org').val(),
                sex: $('input[name=reg_sex]').val(),
                research: $('#reg_use_research').prop('checked'),
                business: $('#reg_use_business').prop('checked'),
                school: $('#reg_use_school').prop('checked'),
                art: $('#reg_use_art').prop('checked'),
                personal: $('#reg_use_personal').prop('checked'),
            },
            success: function(data) {
                if (data.error) {
                    $('#login_error').html(data.errorText);
                    $('#login_email').focus().select();
                } else {
                    $('#login_error').html("<li>Check your email.</li>");
                    $('#loginInterface .reg_item').hide();
                    $('#loginInterface .login_item').show();
                    $('#login_password').focus().select();
                }
            }
        });
    } else {
        $('#loginInterface .reg_item').show();
        $('#loginInterface .login_item').hide();
        $('#register-button').addClass('ui-state-focus');
        $('#login-button').removeClass('ui-state-focus');
        
        
        var $la = $('#login_auth').closest('tr');
        if (e.ctrlKey || e.metaKey) {
            $la.show();
            $('#loginBox thead th').html('Register for an Account');
            $('#register-button').button('option', 'label', 'Register');
        } else {
            $la.hide();
            $('#loginBox thead th').html('Request an Account');
            $('#register-button').button('option', 'label', 'Request Account');
        }
        
    }
}

function loginUser() {
    $('#footer').html('Checking Login Details...');
    if ($('#loginInterface .reg_item:visible').length) {
        $('#loginInterface .reg_item').hide();
        $('#loginInterface .login_item').show();
        $('#register-button').removeClass('ui-state-focus').button('option', 'label', 'Request Account');
        $('#login-button').addClass('ui-state-focus');
        $('#loginBox thead th').html('Log in to access Psychomorph');
        return true;
    }
        
    // check validity
    var error = false;
    $('#login_error').html('');
    var regexEmail = new RegExp('^\S+\@\S+$');
    
    if ($('#login_email').val().length < 1) {
        error = true;
        $('#login_error').append('<li>Please fill in the email.</li>');
        $('#login_email').addClass('error').focus().select();
    //} else if (!regexEmail.test($('#login_email').val())) {
    //    error = true;
    //    $('#login_error').append('<li>This doesn\'t appear to be a valid email address.</li>');
    //    $('#login_email').addClass('error').focus().select();
    } else {
        $('#login_email').removeClass('error');
    }

    if ($('#login_password').val().length < 1) {
        error = true;
        $('#login_error').append("<li>Please fill in the password.");
        $('#login_password').addClass('error').focus().select();
    } else {
        $('#login_password').removeClass('error');
    }
    
    if (error) { return false; }
    
    $.ajax({
        url: 'scripts/userLogin',
        data: {
            email: $('#login_email').val(),
            password: $('#login_password').val(),
            login_keep: $('#login_keep').prop('checked')
        },
        success: function(data) {
            if (data.error) {
                $('#login_error').html(data.errorText);
                $('#footer').html('');
            } else {
                console.log('Logged in as user ' + data.user);
                $('#login_password').val('');
                msgGet();
                var $spinner = bodySpinner();
                $('.interface:visible').not('#projectInterface').hide('fade', {}, 300, function() { 
                    menubar('project');
                    $('#projectInterface').show('fade', 300, function() {}); 
                    prefGet();
                    projectList();
                    $spinner.remove();
                });
            }
        }
    });
}

function logoutUser() {
    $('<div />').html('Do you want to quit and logout?').dialog({
        title: "Logout",
        buttons: {
            Cancel: function() {
                $(this).dialog("close");
            },
            "Logout": function() {
                $.ajax({
                    url: 'scripts/userLogout',
                    success: function(data) {
                        PM.noOnBeforeUnload = true;
                        location.reload(true);
                    }
                });
            }
        }
    });
}

function rgbToArray(rgb) { 
    if (typeof rgb !== 'string') return [127,127,127];
    return rgb.replace('rgb(', '').replace(')','').split(',');
}

function prefGet(callback) {  console.time('prefGet()');
    $('#footer').html('Loading Preferences...');
    $.ajax({
        url: 'scripts/userPrefGet',
        type: 'GET',
        async: false,
        success: function(data) {
            if (data.error) { return false; }
            
            PM.user.id = data.user;
            $('#pref_email').val(data.prefs.email);
            $('#pref_firstname').val(data.prefs.firstname);
            $('#pref_lastname').val(data.prefs.lastname);
            $('#menu_username span').html(data.prefs.email);
            $('#pref_org').val(data.prefs.organisation);
            $('#pref_sex input[name=pref_sex][value=' + data.prefs.sex + ']').prop('checked', true);
            $('#pref_use_research').prop('checked', data.prefs.research==1);
            $('#pref_use_school').prop('checked', data.prefs.school==1);
            $('#pref_use_business').prop('checked', data.prefs.business==1);
            $('#pref_use_personal').prop('checked', data.prefs.personal==1);
            $('#pref_use_art').prop('checked', data.prefs.art==1);
        
            $('#texture').prop('checked', data.prefs.texture == 'true');
            $('#show_thumbs').prop('checked', data.prefs.show_thumbs == 'true').change();
            $('#batch_names').val(data.prefs.batch_names);
            $('#sample_contours').prop('checked', data.prefs.sample_contours == 'true');
            $('#username').html(data.prefs.email);
            $('#password').val('');
            $('#align_x1').val(data.prefs.align_x1);
            $('#align_y1').val(data.prefs.align_y1);
            $('#align_x2').val(data.prefs.align_x2);
            $('#align_y2').val(data.prefs.align_y2);
            $('#align_w').val(data.prefs.align_w);
            $('#align_h').val(data.prefs.align_h);
            $('#normalisation').val(data.prefs.normalisation);
            $('#warp').val(data.prefs.warp);
            $('#default_imageformat').val(data.prefs.default_imageformat);
            $('#defaultLineWidth').val(data.prefs.defaultLineWidth);
            $('#default_project').val(data.prefs.default_project);
            if (PM.project.id == null) { PM.project.id = data.prefs.default_project; }
            
            PM.delin.lineColor = data.prefs.line_color;
            $('#line_color').slider('values', rgbToArray(data.prefs.line_color));
            $('#cross_color').slider('values', rgbToArray(data.prefs.cross_color));
            $('#selcross_color').slider('values', rgbToArray(data.prefs.selcross_color));
            $('.mask_color').slider('values', rgbToArray(data.prefs.mask_color));
            
            $('#pref_theme').slider('value', data.prefs.theme);
            if (data.prefs.theme == 361) {
                $('body').addClass('dark');
            } else {
                $('body').removeClass('dark');
            }
            PM.blankBG = "url(/include/images/blankface.php?h="+data.prefs.theme+")";
            PM.blankImg = "/include/images/blankface.php?h="+data.prefs.theme;
            
            // preload new stylesheet to prevent flashing
            var newstylesheet = "/include/css/style.php?t=" + Date.now();
            $.ajax({
                url: newstylesheet, 
                type: 'GET',
                dataType: 'html',
                success: function(html) {
                    $('#page').hide();
                    $('#css').replaceWith("<link rel='stylesheet' type='text/css' href='" 
                            + newstylesheet + "' id='css' onload='$(\"#page\").show();' />");
                }
            });
            $('head > style').remove();
            
            var cc = rgbToArray(data.prefs.cross_color);
            var sc = rgbToArray(data.prefs.selcross_color);
            $('<style>.pt { background-image: url(/include/images/delin/cross.php?r=' + cc[0] + '&g=' + cc[1] + '&b=' + cc[2] + '); }'
            + '       .pt:hover, .pt.selected { background-image: url(/include/images/delin/cross.php?r=' + sc[0] + '&g=' + sc[1] + '&b=' + sc[2] + '); }'
            + '</style>').appendTo('head');
            
            // fm equations
            $('#fmButtons li').not('#fm_new, #fm_eyes, #fm_FWH').remove();    // clear all user equations
            $.each(data.fm, function(i, f) {
                var $newEQ = $('<li/>').attr({
                    'title': f.description,
                    'data-equation': f.equation,
                }).text(f.name);
                $('#fm_new').before($newEQ);
            });
            $('#fmButtons').sortable({
                items: 'li:not(#fm_new)',
                scope: 'fm',
                containment: '#facialmetricEQ'
            });
            
            if (data.prefs.pca == 1) {
                $('#singlePCA, #batchPCA, #PCvis').show().removeClass('disabled');
            } else {
                $('#singlePCA, #batchPCA, #PCvis').hide().addClass('disabled');
            }
            
            // default templates
            $('#defaultTemplate').html('');
            $('#currentTem').html('');
            $.each(data.defaultTemplates, function(i, t) {
                var $opt = $('<option />').val(t.id)
                                          .html(t.name)
                                          .attr('title', t.notes)
                                          .attr('data-points', t.points)
                                          .attr('data-lines', t.lines);
                $('#defaultTemplate').append($opt);
                
                var $menuopt = $('<li />').addClass('delineate finder')
                                          .attr('title', t.notes)
                                          .attr('data-id', t.id)
                                          .attr('data-points', t.points)
                                          .attr('data-lines', t.lines)
                                          .html('<span class="checkmark">&nbsp;</span>' + t.name);
                $('#currentTem').append($menuopt);
                $menuopt.find('span.checkmark').hide();
            });
            $('#defaultTemplate').val(data.prefs.defaultTem);
            if (PM.delin.temId == 0) { setCurrentTem(data.prefs.defaultTem); }
            drawTem();
        },
        complete: function() {
            $('#footer').html('Preferences Loaded');
            console.timeEnd('prefGet()');
            callback;
        }
    });
}

function msgGet(msg_id) { console.log('msgGet('+msg_id+')');
    var theData = {};
    
    if (msg_id != null) {
        theData.msg_id = msg_id;
    }
    
    $.ajax({
        url: 'scripts/userMessages',
        type: 'POST',
        data: theData,
        success: function(data) {  
            if (data.hasOwnProperty('read_msg_ids')) {
                $.each(data.read_msg_ids, function(i, id) {
                    $('.msg[data-msg_id="' + id + '"]').remove();
                });
                sizeToViewport();
            }
        }
    });
}

function projectList() { console.time('projectList()');
    $('#footer').html('Loading Project List...');
    $.ajax({
        url: 'scripts/projListGet',
        type: 'GET',
        async: true,
        success: function(data) {
            if (data.error) { return false; }
            // add projects
            $('#default_project').html('');
            $('#currentProject').html('');
            $.each(data.projects, function(i, p) {
                var $opt,
                    $menuopt,
                    owners,
                    tr,
                    td;

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
                    if (p.user_id == PM.user.id || (p.perm == 'all' && p.user_id !== this.id)) {
                        owners += '<span data-id="'+ this.id +'" class="tinybutton ownerPermToggle" title="permissions = ' + this.perm + '">' + permAbbrev + '</span>';
                    } else {
                        owners += '<span class="tinybutton" title="permissions = ' + this.perm + '">' + permAbbrev + '</span>';
                    }
                    
                    // add delete button
                    if (this.id == p.user_id) {
                        owners += ' *';
                    } else if (p.owners.length > 1 && p.perm == 'all') {
                        owners += ' <span data-id="'+ this.id +'" class="tinybutton projectOwnerDelete" title="Remove">-</span>';
                    }
                    owners += '</li>';
                });
                owners += '</ul>';
                
                tr = $('tr[data-id=' + p.id + ']');
                
                if (tr.length == 0) {
                    tr = '<tr data-id="' + p.id + '" data-perm="' + p.perm + '"><td><span class="go_to_project tinybutton">Go</span>'
                             + '</td><td>' + p.name 
                             + '</td><td>' + p.notes 
                             + '</td><td><img src="/include/images/menu/queue_loading.svg" />'
                             + '</td><td>' + owners + '</td></tr>';
                    $('#project_list tbody').append(tr);
                } else {
                    tr.attr('data-perm', p.perm);
                    td = tr.find('td');
                    td.eq(1).html(p.name);
                    td.eq(2).html(p.notes);
                    td.eq(4).html(owners);
                }
            });
            $('#project_list').show().stripe();
            
            PM.user.accountSize = 0;
            $.each(data.projects, function(i, p) {
                if (p.filemtime == $('tr[data-id=' + p.id + ']').data('filemtime')) {
                    projSizeUpdate(p.id, data.userAllocation.allocation);
                } else {
                    projSizeGet(p.id, data.userAllocation.allocation);
                }
            });
            
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
            $('#footer').html('Project List Loaded');
        }
    });
}

function projSizeGet(proj_id, alloc) {
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
            
            projSizeUpdate(proj_id, alloc);
        }
    });
} 

function projSizeUpdate(proj_id, alloc) {
    var tr = $('tr[data-id=' + proj_id + ']');
    var td =  tr.find('td').eq(3);

    td.html((tr.data('files') - tr.data('tmp')) + ' files<br>' + tr.data('size'));
    PM.user.accountSize += tr.data('mysize');
    
    // set warning about total space allocation
    var ts = "Projects you own are using " + round(PM.user.accountSize/1024/1024/1024,1)
           + " GB of your allocated " + round(alloc/1024,1) + " GB. ";
    if (PM.user.accountSize/1024/1024 > alloc) {
        ts += "Please reduce your account by emptying the trash and/or removing files. "
            + "After 15 January 2016, I will disable accounts that are over their space allocation.";
        $('#total_space').addClass('warning');
    } else {
        $('#total_space').removeClass('warning');
    }
    $('#total_space').html(ts);
}

function prefSet() {  console.log('prefSet()');
    $('#footer').html('Saving Preferences...');
    var prefData = {
        email: $('#pref_email').val(),
        password: $('#pref_password').val(),
        firstname: $('#pref_firstname').val(),
        lastname: $('#pref_lastname').val(),
        organisation: $('#pref_org').val(),
        sex: $('#pref_sex input[name=pref_sex]:checked').val(),
        research: $('#pref_use_research').prop('checked') ? 1 : 0,
        business: $('#pref_use_business').prop('checked') ? 1 : 0,
        school: $('#pref_use_school').prop('checked') ? 1 : 0,
        art: $('#pref_use_art').prop('checked') ? 1 : 0,
        personal: $('#pref_use_personal').prop('checked') ? 1 : 0,
        mask_color: $('#mask_color').slider('values'),
        cross_color: $('#cross_color').slider('values'),
        selcross_color: $('#selcross_color').slider('values'),
        line_color: $('#line_color').slider('values'),
        theme: $('#pref_theme').slider('value'),
        defaultLineWidth: $('#defaultLineWidth').val(),
        texture: $('#texture').prop('checked'),
        sample_contours: $('#sample_contours').prop('checked'),
        show_thumbs: $('#show_thumbs').prop('checked'),
        batch_names: $('#batch_names').val(),
        align_pt1: $('#align_pt1').val(),
        align_pt2: $('#align_pt2').val(),
        align_x1: $('#align_x1').val(),
        align_y1: $('#align_y1').val(),
        align_x2: $('#align_x2').val(),
        align_y2: $('#align_y2').val(),
        align_w: $('#align_w').val(),
        align_h: $('#align_h').val(),
        defaultTem: $('#defaultTemplate').val(),
        normalisation: $('#normalisation').val(),
        warp: $('#warp').val(),
        default_imageformat: $('#default_imageformat').val(),
        default_project: $('#default_project').val()
    };
    
    $.ajax({
        url: 'scripts/userPrefSet',
        data: prefData,
        success: function(data) {
            if (data.error) {
                $('<div title="Error Saving Preferences" />').html(data.errorText).dialog();
            } else {
                $('#prefDialog').dialog("close");
                //growl('Preferences Saved', 1000);
            }
        },
        complete: function() {
            $('#footer').html('Preferences Saved');
        }
    });
}

function fmAddEquation() {
    // validate data
    var error = false;

    if ($('#fm_name').val() == '') {
        error = true;
        $('#fm_name').addClass('error').focus().select();
    } else {
        $('#fm_name').removeClass('error');
    }
    
    if ($('#fm_equation').val() == '') {
        error = true;
        $('#fm_equation').addClass('error').focus().select();
    } else {
        $('#fm_equation').removeClass('error');
    }
    
    if (!error) {
        $.ajax({
            url: "scripts/fmAddEquation",
            data: {
                name: $('#fm_name').val(),
                eq: $('#fm_equation').val(),
                desc: 'Description'
            },
            success: function(data) {
                var $newEQ = $('<li/>').attr({
                    'title': data.desc,
                    'data-equation': data.eq,
                }).text(data.name);
                $('#fmButtons').append($newEQ);
            }

        });
    }
}

function projectSet(id) {
    $.ajax({
        url: 'scripts/projSet',
        data: {
            project: id
        },
        success: function(data) {
            if (data.error) {
                $('<div title="Error Changing Project" />').html(data.errorText).dialog();
            } else {
                PM.project.id = id;
                PM.project.perm = data.perm;
                $('#currentProject li span.checkmark').hide();
                $('#currentProject li[data-id=' + id + '] span.checkmark').show();
                
                 if (PM.interfaceWindow == 'project') {
                    $('#showFinder').click();
                } else {
                    loadFiles(PM.project.id);
                }
                
                // clean up things
                $('#average-list li').remove();
                
                // check project permissions
                if (PM.project.perm == 'read-only') {
                    growl('This project is read-only. You can copy folders to your own projects by right-clicking on them. Contact the owner if you need permission to save images to this project.', 5000);
                }
            }
        }
    });
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
                                PM.project.id = data.project;
                                projectList();
                                projectSet(PM.project.id);
                            }
                        }
                    });
                }
            }
        }
    });
}

function projectOwnerDeleteConfirmed(project, owner) { console.log('projectOwnerDeleteConfirmed('+project+', '+owner+')');
    $('#footer').html('Deleting Owner...');
    $.ajax({
        url: 'scripts/projOwnerDelete',
        data: {
            project: project,
            owner: owner
        },
        success: function(data) {
            if (data.error) {
                $('<div title="Error Deleting Owner" />').html(data.errorText).dialog();
                $('#footer').html('Owner Not Deleted');
            } else {
                $('#refresh').click();
                $('#footer').html('Owner Deleted');
            }
        }
    });
}

function projectOwnerDelete(project, owner) { console.log('projectOwnerDelete('+project+', '+owner+')');
    if (owner == PM.user.id) {
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

function projectEdit(td, category) {
    $('#footer').html('Editing Project...');
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
                        $('#footer').html('Project Not Edited');
                    } else {
                        oldname = newname;
                        $('#footer').html('Project Edited');
                    }
                },
                complete: function() {
                    $(td).html(oldname);
                }
            });
        } else {
            $(td).html(oldname);
            $('#footer').html('');
        }
    }).focusout(function() {
        $(this).blur(); 
    });
    
    $(td).html('').append($newnameinput);
    $newnameinput.focus().select();
}

function projectOwnerAdd(button) {
    $('#footer').html('Adding Project Owner...');
    var $input = $(button);
    var project = $input.closest('tr').data('id'); 
    
    var owner = $input.data('id');
    
    if (!(project > 0 && owner > 0)) { 
        
        $('#footer').html('User not found');
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
                $('#footer').html('Project Owner Not Added');
                $input.show().val('');
            } else {
                $('#refresh').click();
                $('#footer').html('Project Owner Added');
            }
        }
    });
}
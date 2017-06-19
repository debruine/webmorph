//====================================
// !USER FUNCTIONS
//====================================

/* 
function loginGoogle(authResult) {
    growl(JSON.stringify(authResult));
}

// Work on user object
function user(id) {
    this.id = id;
    this.accountSize = 0;
    
    this.passwordReset = function() {
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
}
*/

function userPasswordReset() {
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

function userRegister(e) {
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
                reason: $('#reg_reason').val(),
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
            $('#reg_reason').hide().val('');
            $('#loginBox thead th').html('Register for an Account');
            $('#register-button').button('option', 'label', 'Register');
        } else {
            $la.hide();
            $('#reg_reason').show();
            $('#loginBox thead th').html('Request an Account');
            $('#register-button').button('option', 'label', 'Request Account');
        }

    }
}

function userLogin() { console.log('userLogin()');
    $('#footer-text').html('Checking Login Details...');
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
                $('#footer-text').html('');
            } else {
                console.log('Logged in as user ' + data.user);
                $('#login_password').val('');
                userLoad();
            }
        }
    });
}

function userLoad() { console.log('userLoad()');
    var hash = {},
        $file;
    
    /*if (location.hash) { 
        //hashChange();
    } else {
        var cookiehash = getCookie('hash');
        if (cookiehash) { 
            location.hash = '#' + cookiehash;
            console.log('Set location hash to ' + cookiehash);
        }
    }*/
    
    hash = hashGet();
    
    // set up finder location if a hash file is set
    if (hash.file) {
        WM.hashfile = function() {
            WM.finder.open(hash.project_id + hash.file);
            WM.hashfile = function() {}
        }
    } else {
        WM.hashfile = function() {}
    }

    msgGet();
    prefGet();

    if (hash.appWindow == 'F') {
        projectList();
        projectSet(hash.project_id, 'F');
    } else if (hash.appWindow == 'D') {
        projectList();
        if (hash.file) {
            if (hash.file.substr(-4) == ".obj") {
                d3_load_image(hash.project_id + hash.file);
            } else {
               delinImage(hash.project_id + hash.file);
            }
        }
        projectSet(hash.project_id, 'D');
    } else if (hash.appWindow == 'A') {
        projectList();
        projectSet(hash.project_id, 'A');
    } else if (hash.appWindow == 'T') {
        projectList();
        projectSet(hash.project_id, 'T');
    } else {
        $('#showProjects').click();
    }
    
    // get rid of 3D login demo if made
    if ($('#d3_demo').data('d3')) {
        $('#d3_demo').data('d3').remove();
        $('#d3_demo').data('d3', null);
        
        $('#d3_demo').addClass('feature').html('WebMorph now shows 3D faces!<br>(Double-click to demo)');
        $('#d3_demo_extras').hide().find('div.ui-slider').remove();
    }
}

function userLogout() { console.log('userLogout()');
    $('<div />').html('Do you want to quit and logout?').dialog({
        title: "Logout",
        buttons: {
            Cancel: function() {
                $(this).dialog("close");
            },
            "Logout": function() {
                $.xhrPool.abortAll();
                $.ajax({
                    url: 'scripts/userLogout',
                    success: function(data) {
                        WM.noOnBeforeUnload = true;
                        location.href = location.pathname;
                    }
                });
            }
        }
    });
}

function prefGet(callback) {  console.time('prefGet()');
    $('#footer-text').html('Loading Preferences...');
    $.ajax({
        url: 'scripts/userPrefGet',
        type: 'GET',
        async: false,
        success: function(data) {
            if (data.error) { return false; }

            WM.user.id = data.user;
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
            if (WM.project.id == null) { WM.project.id = data.prefs.default_project; }

            WM.delin.lineColor = data.prefs.line_color;
            
            var pc = rgbToArray(data.prefs.cross_color);
            var sc = rgbToArray(data.prefs.selcross_color);
            var lc = rgbToArray(data.prefs.line_color);
            var mc = rgbToArray(data.prefs.mask_color);

            $('#line_color').slider('values', lc);
            $('#cross_color').slider('values', pc);
            $('#selcross_color').slider('values', sc);
            $('.mask_color').slider('values', mc);
            
            $('#tem_point_color').slider('values', pc);
            $('#tem_point_fill').slider('values', pc);
            $('#tem_line_color').slider('values', lc);

            $('#pref_theme').slider('value', data.prefs.theme);
            if (data.prefs.theme == 361) {
                $('body').addClass('dark');
            } else {
                $('body').removeClass('dark');
            }
            WM.blankBG = "url(/include/images/blankface.php?h="+data.prefs.theme+")";
            WM.blankImg = "/include/images/blankface.php?h="+data.prefs.theme;

            // preload new stylesheet to prevent flashing
            var newstylesheet = "/include/css/theme.php?t=" + Date.now();
            $.ajax({
                url: newstylesheet,
                type: 'GET',
                dataType: 'html',
                success: function(html) {
                    $('#page').hide();
                    $('#themecss').replaceWith("<link rel='stylesheet' type='text/css' href='"
                            + newstylesheet + "' id='themecss' onload='$(\"#page\").show();' />");
                }
            });
            $('head > style').remove();

            var cc = rgbToArray(data.prefs.cross_color);
            var sc = rgbToArray(data.prefs.selcross_color);
            $('<style>.pt { background-image: url(/include/images/delin/cross.php?r=' + cc[0] + '&g=' + cc[1] + '&b=' + cc[2] + '); }'
            + '       .pt:hover, .pt.selected { background-image: url(/include/images/delin/cross.php?r=' + sc[0] + '&g=' + sc[1] + '&b=' + sc[2] + '); }'
            + '</style>').appendTo('head');

            // fm equations
            $('#fmButtons li').not('#fm_eyes, #fm_FWH').remove();    // clear all user equations
            $.each(data.fm, function(i, f) {
                var $newEQ = $('<li/>').attr({
                    'title': f.description,
                    'data-equation': f.equation,
                }).text(f.name);
                $('#fmButtons').append($newEQ);
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
            if (WM.delin.temId == 0) { setCurrentTem(data.prefs.defaultTem); }
            drawTem();
        },
        complete: function() {
            $('#footer-text').html('Preferences Loaded');
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
            }
            $('.msg').show();
            sizeToViewport();
        }
    });
}

function prefSet() {  console.log('prefSet()');
    $('#footer-text').html('Saving Preferences...');
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
            $('#footer-text').html('Preferences Saved');
        }
    });
}
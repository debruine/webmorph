//====================================
// !FINDER FUNCTIONS
//====================================

function currentDir() {  //console.log('currentDir()');
    var cDir = '';
    
    var openFolders = $('li.folder').filter(':not(.closed):visible').last();
    if (openFolders.length) {
        cDir = openFolders.attr('path');
    } else {
        var sf = $('li.file.selected').filter(':first');
        if (sf.length) {
            cDir = sf.attr('url').replace(/\/[^\/]+$/, '');
        }
    }
    return cDir;
}

// access a private file
function fileAccess(img, thumb) {
    if (img.substr(-4, 1) != '.') { img += '.jpg'; }
    
    if (thumb == null) {
        return "/scripts/fileAccess?file=" + img;
    } else {
        return "/scripts/fileAccess?thumb&file=" + img;
    }
}

function emptyTrash() {
    var file_n = $('#trash li.file').length;
    if (file_n == 0 ) {
        growl('There are no files to delete', 1000);
        return false;
    }
    $('<div />').html('Are you sure you want to permanently remove these ' + file_n + ' files? This cannot be undone.').dialog({
        title: 'Empty Trash',
        buttons: {
            Cancel: function() { $(this).dialog("close"); },
            "Empty Trash": {
                text: 'Empty Trash',
                click: function() {
                    $(this).dialog("close");
                    $.ajax({
                        url: 'scripts/dirTrashEmpty',
                        data: { project: PM.project },
                        success: function(data) {
                            if (data.error) {
                                growl(data.errorText);
                            } else {
                                loadFiles(PM.project + '/.trash');
                            }
                        }
                    });
                }
            }
        }
    });
}

function folderNew() {
    // get base directory to add folder to
    var cDir = currentDir();
    var theHTML = 'Add a folder to the directory <code>' + urlToName(cDir) + '</code>?' + '<p>Folder name: <input type="text" /></p>';
    $('<div />').html(theHTML).dialog({
        title: 'New Folder',
        buttons: {
            Cancel: function() {
                $(this).dialog("close");
            },
            'Save': {
                text: 'Save',
                class: 'ui-state-focus',
                click: function() {
                    var dirname = $(this).find('input').val();
                    $(this).dialog("close");
                    $.ajax({
                        url: 'scripts/dirAdd',
                        data: {
                            dirname: dirname,
                            basedir: cDir
                        },
                        success: function(data) {
                            if (data.error) {
                                $('<div title="Error Creating Folder" />').html(data.errorText).dialog();
                            } else {
                                var dir = cDir + dirname;
                                loadFiles(dir);
                            }
                        }
                    });
                }
            }
        }
    });
}

function fileUpload() {
    var cDir = currentDir();
    var uploadedFiles = document.getElementById('upload').files;
    var uploadList = $('<ul class="batchList" />');
    var filenames = {};
    for (var i = 0; i < uploadedFiles.length; ++i) {
        var name = uploadedFiles.item(i).name;
        uploadList.append('<li>' + name + '</li>');
        
        var name_parts = name.split('.');
        var suffix = name_parts.pop();
        var prefix = name_parts.join('.');
        
        if (filenames[prefix] == undefined) filenames[prefix] = [];
        filenames[prefix].push(uploadedFiles.item(i));
    }
    var $uploadDialog = $('<div />').html('Upload ' + uploadedFiles.length + ' file' + (uploadedFiles.length == 1 ? '' : 's') + ' to <code>' + urlToName(cDir) + '</code>?').append(uploadList).dialog({
        title: "Files to Upload",
        buttons: {
            Cancel: function() {
                $(this).dialog("close");
                $('#upload').val('');
            },
            "Upload": {
                text: 'Upload',
                class: 'ui-state-focus',
                click: function() {
                    uploadList.hide();
                    
                    $(this).parent().find('.ui-dialog-buttonpane').hide();
                    
                    $(this).text('');

                    var totalFiles = uploadedFiles.length;
                    
                    var $progressUpdate = $('<p />').html('0 of ' + totalFiles + ' files uploaded');
                    var $errorList = $('<ol />').css('clear', 'both').css('max-height', '10em').css('overflow', 'auto');
                    var $progressBar = $('<div />').addClass('progressBar');
                    var $progressBox = $('<div />').addClass('progressBox').append($progressBar);
                    $(this).append($progressUpdate).append($progressBox).append($errorList).dialog({
                        title: 'Upload Images'
                    });
                    $progressBar.css('width', '0%');

                    var uploaded_attempts = 0;
                    var uploaded_successes = 0;
                    var formData = null;
                    
                    $.each(filenames, function(i, files) {
                        formData = new FormData();
                        $.each(files, function(j, file) {
                            formData.append('upload' + '[' + j + ']', file);
                        });
                        formData.append('basedir', cDir);
                        formData.append('desc', 'Uploaded image');
                        $.ajax({
                            data: formData,
                            url: "scripts/fileSave2",
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function(data) {
                                if (!data.error) {
                                    uploaded_successes += files.length;
                                    $progressUpdate.html(uploaded_successes + ' of ' + totalFiles + ' files uploaded');
                                } else {
                                    $errorList.append('<li>' + data.errorText + '</li>');
                                }
                            },
                            error: function() {
                                $errorList.append('<li>Error uploading ' + i + '</li>');
                            },
                            complete: function() {
                                uploaded_attempts += files.length;
                                $progressBar.css('width', (100 * (uploaded_attempts) / totalFiles) + '%');
                                
                                if (uploaded_attempts == totalFiles) {
                                    loadFiles(cDir);
                                    $('#upload').val('');
                                    if (uploaded_successes == totalFiles) {
                                        $uploadDialog.dialog("close");
                                    }
                                }
                            }
                        });
                    });
                }
            }
        }
    });
}

function fileDelete() {
	var files = filesGetSelected();
    var $fileItems = $finder.find('li.file.selected').filter(':visible');
    var nFiles = files.length;
    if (nFiles) {
        var fileList = $('<ul class="file" />').css('max-height', '15em');
        $.each(files, function(i, url) {
            var fileName = urlToName(url);
            fileList.append('<li>' + fileName + '</li>');
        });
        $('<div />').html('Move these ' + nFiles + ' files to the Trash?').append(fileList).dialog({
            title: "Delete Files",
            buttons: {
                Cancel: function() {
                    $(this).dialog("close");
                },
                "Delete Files": function() {
                    $(this).dialog("close");
                    $.ajax({
                        url: 'scripts/fileDelete',
                        data: {
                            files: files
                        },
                        success: function(data) {
                            if (data.error) {
                                $('<div />').html(data.errorText).dialog({
                                    title: 'Error Deleting Files'
                                });
                            } else {
                                // modify enclosing folder contents so it will be reloaded on next finder refresh
                                //$fileItems.closest('li.folder').data('contents', '');
                                //$fileItems.remove();
                                loadFiles($fileItems.closest('li.folder').attr('path')); // reload so trash is updated
                            }
                            updateSelectedFiles();
                        }
                    });
                },
            }
        });
    } else {
        var $lastFolder = $finder.find('li.folder:not(.closed):last');
        if ($lastFolder.length) {
            dirToDelete = $lastFolder.attr('path').replace(PM.project + '/', '/');
            if (dirToDelete == '/') {
                return false;
            } else if ($lastFolder.find('li').length) {
                growl('Delete all files in this folder first.', 2000);
                return false;
            }
            $('<div />').html('Delete directory <code>' + dirToDelete + '</code>?').dialog({
                title: 'Delete Folder',
                buttons: {
                    Cancel: function() {
                        $(this).dialog("close");
                    },
                    "Delete Folder": function() {
                        $(this).dialog("close");
                        $.ajax({
                            url: 'scripts/dirDelete',
                            data: { dirname: PM.project + dirToDelete },
                            success: function(data) {
                                if (data.error) {
                                    $('<div />').html(data.errorText).dialog({
                                        title: 'Error Deleting ' + dirToDelete
                                    });
                                } else {
                                    // modify enclosing folder contents so it will be reloaded on next finder refresh
									$lastFolder.closest('li.folder').data('contents', ''); 
									$lastFolder.remove();
                                    //loadFiles(PM.project + dirToDelete, true);
                                }
                            }
                        });
                    },
                }
            });
        }
    }
}

function fileRename() {
    var $theFileItem = $finder.find('li.file.selected:first');
    var $theSpan = $theFileItem.find('span');
    var oldurl = $finder.find('li.file.selected:first').attr('url');
    $finder.find('li.file.selected').removeClass('selected');
    var oldname = $theSpan.html();
    var w = $theSpan.closest('li').width();
    var $newnameinput = $('<input />').val(oldname).attr('type', 'text').width(w);
    
    $newnameinput.keydown(function(e) {
        var newname = $(this).val();
        if (e.which == KEYCODE.enter) {
            e.stopPropagation();
            if (newname !== '' && newname !== oldname) {
                $.ajax({
                    url: 'scripts/fileRename',
                    data: {
                        oldurl: oldurl,
                        newname: newname
                    },
                    success: function(data) {
                        if (data.error) {
                            growl(data.errorText);
                            $(this).blur();
                        } else {
                            loadFiles(data.newurl, true);
                        }
                    }
                });
            } else {
                $(this).blur();
            }
        }
        $theFileItem.addClass('selected'); // make sure the file is still selected
    }).blur(function() {
        $(this).remove();
        $theSpan.show();
    }).focusout(function() {
        $(this).remove();
        $theSpan.show();
    });
    
    $theSpan.hide().before($newnameinput);
    var fname = oldname.replace(/\.(jpg|gif|png|tem)$/,'').length;
    $newnameinput.focus().selectRange(0,fname);
}

function folderRename() {
    var $theFolder = $finder.find('li.folder').not('.closed').last();
    
    if ($theFolder.attr('id') == "trash") return false;
    
    var $theSpan = $theFolder.find('> span');
    var olddir = $theFolder.attr('path');
    var oldname = $theSpan.html();
    var w = $theSpan.closest('li').width();
    var $newnameinput = $('<input />').val(oldname).attr('type', 'text').width(w);
    $newnameinput.keydown(function(e) {
        e.stopPropagation();
        var newname = $(this).val();
        if (e.which == KEYCODE.enter) {
            if (newname !== '' && newname !== oldname) {
                $.ajax({
                    url: 'scripts/dirRename',
                    data: {
                        olddir: olddir,
                        newdir: newname
                    },
                    success: function(data) {
                        if (data.error) {
                            growl(data.errorText);
                            $(this).blur();
                        } else {
                            $theSpan.text(newname);
                            $theFolder.attr('path', data.newdir);
                            loadFiles(data.newdir, data.newdir);
                        }
                    }
                });
            } else {
                $(this).blur();
            }
        }
    }).blur(function() {
        $(this).remove();
        $theSpan.show();
    }).focusout(function() {
        $(this).remove();
        $theSpan.show();
    });
    $theSpan.hide().before($newnameinput);
    $newnameinput.focus().select();
}

function folderMoveProject() {
	if ($finder.find('li.file.selected').filter(':visible').length) return false;
	
    var $theFolder = $finder.find('li.folder').not('.closed').last();
    
    if ($theFolder.attr('id') == "trash") return false;
    
    var fpath = $theFolder.attr('path');
    var $project = $('#default_project').clone();
    $project.find('option[value=' + PM.project + ']').remove();
    
    var $dialog = $('<div />').html('Copy the folder <code>' + fpath + '</code> to ').append($project).dialog({
        title: "Copy Folder to Project",
        buttons: {
            Cancel: function() { $(this).dialog("close"); },
            /*
	        'Move': {
                text: 'Move',
                click: function() {
                    $.ajax({
                        url: 'scripts/fileCopy',
                        data: {
                            files: [fpath],
                            toDir: $(this).find('select').val() + '/',
                            action: 'move'
                        },
                        success: function(data) {
                            if (data.error) {
                                growl(data.errorText);
                            } else {
                                loadFiles();
                            }
                            
                        }
                    });
                    $(this).dialog("close");
                }
            },
            */
            'Copy': {
                text: 'Copy',
                class: 'ui-state-focus',
                click: function() {
                    $.ajax({
                        url: 'scripts/fileCopy',
                        data: {
                            files: [fpath],
                            toDir: $(this).find('select').val() + '/',
                            action: 'copy'
                        },
                        success: function(data) {
                            if (data.error) {
                                growl(data.errorText);
                            } else {
                                growl('Files copied', 1000);
                            }
                        }
                    });
                    $(this).dialog("close");
                }
            }
        }
    });
}


function filesGetSelected(filter, replaced) { console.log('filesGetSelected(' + filter + ', ' + replaced + ')');
    var sf = [];
    if (filter == null) filter = '';
    if (replaced == null) replaced = '';
    $finder.find('li.file.selected').filter(':visible' + filter).each(function(i, v) {
        sf.push($(this).attr('url').replace(replaced, ''));
    });
    return sf;
}

function fileListGet() {
    var files = filesGetSelected('.image', PM.project);
    var fileList;
    
    if (files.length > 0) {
        fileList = files.join("\n");
        $('<div />').html("<textarea style='width:100%; height: 200px;'>" + fileList + "</textarea>").dialog({
            title: 'File List (' + files.length + ' images)',
        }).find('textarea').focus().select();
    } else if (currentDir() !== '') {
        var cd = currentDir();
        var $allfiles = $finder.find('li[url^="' + cd + '"].file.image');
        $allfiles.addClass('selected');
        updateSelectedFiles();
        files = filesGetSelected('.image', PM.project);
        fileList = files.join("\n");
        $('<div />').html("<textarea style='width:100%; height: 200px;'>" + fileList + "</textarea>").dialog({
            title: 'File List for ' + cd + ' (' + files.length + ' images)',
        }).find('textarea').focus().select();
    } else {
        growl('You have not selected any files.', 1500);
    }
}

function updateSelectedFiles() { //console.log('updateSelectedFiles()');
    var cdir = currentDir();
    var s = $finder.find('li.file.selected').filter(':visible').length;
    var vFiles = $finder.find('li.folder[path="' + cdir + '"] > ul > li.file').length;
    
    $('#footer').html('<code>' + urlToName(cdir) + '</code> (' + s + ' of ' + vFiles + ' file' + (vFiles == 1 ? '' : 's') + ' selected)');
    
    if (PM.interface == 'average') { 
        checkAvgAbility(); 
    } else if (PM.interface == 'transform') { 
        if (s > 1) {
            $('#transButton').button('option', 'label', 'Transform All');
        } else {
            $('#transButton').button('option', 'label', 'Transform');
        }
    }
    
    if (s == 1 && !$finder.hasClass('imageView')) {
        $('#imagebox').show();
    } else {
        $('#imagebox').hide();
    }
}

function imagesWithTems() {
    console.time('imagesWithTems()');
    // mark all images and tems in the finder with a class if they have a corresponding img/tem
    var $files = $finder.find('li.file');
    var $tems = $files.filter('.tem');
    $tems.removeClass('hasImg');
    
    var temList = {};
    $tems.each(function() {
        var v = $(this);
        temList[v.attr('url')] = v;
    });
    
    
    $files.filter('.image').each( function() {
        var theURL = $(this).attr('url');
        var theTem = theURL.replace(/\.(jpg|tem|gif|png)$/, '.tem');
        if (theTem in temList) {
            $(this).addClass('hasTem');
            temList[theTem].addClass('hasImg');
        } else {
            $(this).removeClass('hasTem');
        }
    });
    
    if (PM.interface == 'average' || PM.interface == 'transform') { 
        $files.hide().filter('.image.hasTem').show();
    }
    console.timeEnd('imagesWithTems()');
}

function finder(dir) { console.log('finder(' + dir + ')');
    this.dir = dir;
    
    this.load = function(subdir) {
        subdir = subdir | this.dir;
        
        $.ajax({
            url: 'scripts/dirLoad',
            data: { subdir: subdir },
            success: function(data) {
                if (data.error) {
                    $('<div title="Error Loading Files" />').html(data.errorText).dialog();
                } else {
                    
                    this.imagesWithTems();
                }
            },
            complete: function() {
                updateSelectedFiles();
                $finder.css('background-image', 'none');
            }
        });
    };
    
    this.folder = function(url, container) {
        var theItem = container.find('.file[url="' + url + '"]');
    };
    
    this.file = function(url, container) {
        var theItem = container.find('.file[url="' + url + '"]');    
        
        if (!theItem.length) { 
            var ext = url.substring(url.length - 4);
            var theClass = 'file';
            var img = false;
            if (ext == '.tem') {
                theClass += ' tem';
            } else if (ext == '.txt') {
                theClass += ' txt';
            } else if (ext == '.csv') {
                theClass += ' csv';
            } else if (ext == '.pca') {
                theClass += ' pca';
            } else if (ext == '.fimg') {
                theClass += ' fimg';
            } else if (ext == '.pci') {
                theClass += ' pci';
            } else if (ext == '.jpg') {
                theClass += ' jpg image';
                img = true;
            } else if (ext == '.png') {
                theClass += ' png image';
                img = true;
            } else if (ext == '.gif') {
                theClass += ' gif image';
                img = true;
            }
            var shortName = url.replace(/^.*\//, '');
            theItem = $('<li class="' + theClass + '" url="' + url + '"><span>' + shortName + '</span></li>'); 
            
            if (img && $('#show_thumbs').prop('checked')) {
                theItem.css('background-image', 'url(/scripts/fileAccess?thumb&file=' + url + ')');
            }
        }
        return theItem;
    };
    
    this.imagesWithTems = function() { console.time('imagesWithTems()');
        // mark all images and tems in the finder with a class if they have a corresponding img/tem
        var $files = $finder.find('li.file');
        var $tems = $files.filter('.tem');
        $tems.removeClass('hasImg');
        
        var temList = {};
        $tems.each(function() {
            var v = $(this);
            temList[v.attr('url')] = v;
        });
        
        
        $files.filter('.image').each( function() {
            var theURL = $(this).attr('url');
            var theTem = theURL.replace(/\.(jpg|tem|gif|png)$/, '.tem');
            if (theTem in temList) {
                $(this).addClass('hasTem');
                temList[theTem].addClass('hasImg');
            } else {
                $(this).removeClass('hasTem');
            }
        });
        
        if (PM.interface == 'average' || PM.interface == 'transform') { 
            $files.hide().filter('.image.hasTem').show();
        }
        console.timeEnd('imagesWithTems()');
    };
}

function loadFiles(selected_dir, subdir) { console.log('loadFiles(' + selected_dir + ', ' + subdir + ')');
    $('#footer').html('Loading Files...');
    var $spinner = bodySpinner();
    
    if (subdir === true && selected_dir !== undefined) {
        subdir = selected_dir.replace(/\/$/, '').replace(/[^\/]+$/, '');
    } else if (subdir === undefined) { 
        subdir = PM.project; 
    }  
    
    $.ajax({
        url: 'scripts/dirLoad',
        data: { subdir: subdir },
        success: function(data) {
            if (data.error) {
                $('<div title="Error Loading Files" />').html(data.errorText).dialog();
            } else {
                $('#imagebox').hide().insertBefore($('#uploadbar')); // move imagebox out of finder first
                $finder.find('ul').css('width', 'auto');
                console.time('folderize()');
                if (subdir !== '') {
                    var subdirfolder = $finder.find('li.folder[path="' + subdir + '"]');
                    if (subdirfolder.length) {
                        folderize(data.dir[subdir], subdirfolder);
                    } else {
                        folderize(data.dir, $finder);
                    }
                } else {
                    folderize(data.dir, $finder);
                }
                console.timeEnd('folderize()');
                
                // fix first ul
                var firstul = $finder.find('> ul');
                firstul.css('margin-left', (-1 * firstul.width()) - 1).find('> li.folder:eq(0) > span').click(); 
                
                // hide trash
                var $trash = $finder.find('li.folder[path="' + PM.project + '/.trash/"]');
                $trash.attr('id', 'trash');
                $trash.find('> span').text('Trash');
                
                if ($('#toggletrash span.checkmark').css('display') == 'block') {
                    $trash.show();
                } else {
                    $trash.hide();
                }
                
                updateSelectedFiles();
                finderFunctions();

                if (selected_dir !== undefined && selected_dir.length > 0) {
                    fileShow(selected_dir);
                }
                $('#footer').html($finder.find('li.file').length + ' files loaded'); 
            }
            imagesWithTems();
            $finder.css('background-image', 'none');
            $spinner.remove();
        }
    });
}

function fileShow(filename) {
    var splitdir = filename.replace(/^\//, '').split('/');
    var fullpath = '';
    var ext = '';
    $.each(splitdir, function(i, path) {
        if (i == 0) {
            fullpath = path;
        } else {
            fullpath += '/' + path;
        }
        ext = path.substr(-4);
        if (i == splitdir.length-1 && (ext == '.jpg' || ext == '.png' || ext == '.gif' || ext == '.tem')) {
            $finder.find('li.file[url="' + fullpath + '"]').click();
        } else {
            $finder.find('li.folder[path="' + fullpath + '/"] > span').click();
        }
    });
}

function folderize(json, appendElement) { 
    //console.time(' - folderize(' + Object.keys(json).length + ' items, ' + appendElement.attr('path') + ')');
    
    appendElement.removeClass('closed'); // open folder to be able to calculate width
    var theFolder = appendElement.find('> ul');
    if (!theFolder.length) {
        theFolder = $('<ul />');
    }

    var w = appendElement.width();
    appendElement.siblings('li').find('> ul').css('margin-left', w);
    theFolder.css('margin-left', w);

    var $allItems = theFolder.find('> li').addClass('oldfinder'); // mark all items as old    
    
    var fItems = [];
    
    for (var folder in json) {
        var contents = json[folder];
        var theItem;
        if (typeof contents === 'string') {
            // contents are an image name
            var url = folder.replace(/^i/, '');
            theItem = fileNew(url, theFolder).removeClass('oldfinder');
        } else {
            // contents are more files/folders
            theItem = $allItems.filter('.folder[path="' + folder + '/"]');
            var oldC = '';
            
            if (!theItem.length) { 
                var shortName = folder.replace(/^.*\//, '');
                theItem = $('<li class="folder closed" path="' + folder + '/"><span>' + shortName + '</span></li>'); 
            } else {
                theItem.removeClass('oldfinder folderDrop');
                oldC = JSON.stringify(theItem.data('contents'));
            }

            var newC = JSON.stringify(contents);
            if (newC !== oldC) { theItem.data('contents', contents).addClass('tofolderize'); }
        }
        fItems.push(theItem);
    }
    
    theFolder.append(fItems);
    $allItems.filter('.oldfinder').remove(); // remove any old items
    appendElement.append(theFolder);
    //console.timeEnd(' - folderize(' + Object.keys(json).length + ' items, ' + appendElement.attr('path') + ')');
    
    theFolder.find('> li.folder.tofolderize').each( function() {
        if ($(this).data('contents') !== undefined) {
            $(this).removeClass('tofolderize');
            folderize($(this).data('contents'), $(this));
        } else {
            console.warn('Tried to folderize contentless folder: ' + $(this).attr('path'));
        }
    });
}

function fileNew(url, container) {
    var theItem = container.find('.file[url="' + url + '"]');    
    
    if (!theItem.length) { 
        var ext = url.substring(url.length - 4);
        var theClass = 'file';
        var img = false;
        if (ext == '.tem') {
            theClass += ' tem';
        } else if (ext == '.txt') {
            theClass += ' txt';
        } else if (ext == '.csv') {
            theClass += ' csv';
        } else if (ext == '.pca') {
            theClass += ' pca';
        } else if (ext == '.fimg') {
            theClass += ' fimg';
        } else if (ext == '.pci') {
            theClass += ' pci';
        } else if (ext == '.jpg') {
            theClass += ' jpg image';
            img = true;
        } else if (ext == '.png') {
            theClass += ' png image';
            img = true;
        } else if (ext == '.gif') {
            theClass += ' gif image';
            img = true;
        }
        var shortName = url.replace(/^.*\//, '');
        theItem = $('<li class="' + theClass + '" url="' + url + '"><span>' + shortName + '</span></li>'); 
        
        if (img && $('#show_thumbs').prop('checked')) {
            theItem.css('background-image', 'url(/scripts/fileAccess?thumb&file=' + url + ')');
        }
    }
    return theItem;
}

function finderFunctions() { console.time('finderFunctions()');

    $('#searchbar:visible').keyup();

    var thisContainment = (PM.interface == 'finder') ? '#finder' : 'window';
    
    $finder.find('li.file').not('.ui-draggable').draggable({
        helper: function() {
            var $helper = $('<ul />').addClass('filehelper');
            return $helper;
        },
        opacity: 0.7,
        delay: 300,
        stack: '.filehelper',
        containment: thisContainment,
        appendTo: 'body',
        handle: "span",
        start: function(event, ui) {
            // remove other selections if this file is not currently selected
            if (!$(this).hasClass('selected') && !(event.ctrlKey || event.metaKey || event.shiftKey)) {
                $finder.find('li.file.selected').removeClass('selected');
            }
        
            $(this).addClass('selected');
            $('#imagebox').hide().insertAfter($finder);
            $finder.find('li.file.selected').filter(':visible').each( function(i, v) {
                var $clone = $(this).clone().removeClass('selected');
                ui.helper.append($clone);
            });
            $('#cutItems').click();
        },
        stop: function(event, ui) {
            PM.pasteBoard = [];
            $finder.find('li.file').removeClass('to_cut'); // clear all to_cut files
        }
    });

    $finder.find('li.folder').not('.ui-draggable').draggable({
        helper: function() {
            var $helper = $('<ul />').addClass('filehelper');
            return $helper;
        },
        opacity: 0.7,
        handle: "span",
        delay: 300,
        containment: '#finder',
        start: function(event, ui) {
            var $clone = $(this).clone().addClass('closed');
            ui.helper.append($clone);
        }
    });

    $finder.find('> ul > li.folder ul, li.folder').not('.ui-droppable').droppable({
        accept: 'li.folder, li.file',
        greedy: true,
        hoverClass: 'folderDrop',
        tolerance: "pointer",
        over: function(e, ui) {
            var to_move = ui.helper.find('li.folder').attr('path');
            if (typeof to_move === 'undefined') { 
	            var l = ui.helper.find('li.file').length;
                to_move = l + ' file' + ((l >1) ? 's' : ''); 
            } else {
                to_move = '<code>' + urlToName(to_move) + '</code>';
            }
            
            if ($(this).hasClass('folder')) {
                $folder = $(this);
            } else {
                $folder = $(this).closest('li.folder');
            }
            var action = (e.ctrlKey || e.metaKey) ? 'Copy' : 'Move';
            $('#footer').html(action + ' ' + to_move + ' to <code>' + urlToName($folder.attr('path')) + '</code>');
        },
        out: function(e, ui) {
            $('#footer').html('');
        },
        drop: function( e, ui ) {
            if ($(this).hasClass('folder')) {
                $folder = $(this);
            } else {
                $folder = $(this).closest('li.folder');
            }
            
            if (PM.pasteBoard.length) {
                var itemPath = PM.pasteBoard[0].replace(/\/[^\/]+$/, '/');
                if ($folder.attr('path') != itemPath) {
                    $folder.find('> span').click();
                    if (e.ctrlKey || e.metaKey) { $finder.find('li.to_cut').removeClass('to_cut'); }
                    $('#pasteItems').click();
                }
            } else if (ui.helper.hasClass('tcimage')) {
                // save tomcat image
                ui.draggable.click();
                $(this).click();
                $('#save').click();
            } else {
                var folder_to_move = ui.helper.find('li.folder').attr('path');
                var move_to = $folder.attr('path');
                var action = (e.ctrlKey || e.metaKey) ? 'copy' : 'move';
                
                // check if moving files to same folder
                if (move_to == folder_to_move.replace(/[^\/]+\/$/, '')) { return false; }

                $.ajax({
                    url: 'scripts/fileCopy',
                    data: {
                        files: [folder_to_move],
                        toDir: move_to,
                        action: action
                    },
                    success: function(data) {
                        loadFiles(move_to);
                    }
                });
            }
        }
    });
    
    /*
    // this takes a lot of time and isn't very functional
    console.time('set files selectable');
    $finder.find('li.folder > ul').selectable({
        start: function(e, ui) {
            if (!(e.ctrlKey || e.metaKey || e.shiftKey)) {
                // unselect other files if ctrl/cmd/shift NOT held down
                $('li.file.selected').removeClass('selected');
            }
        },
        selecting: function(event, ui) {
            $(ui.selecting).filter('li.file').addClass('selected');
            updateSelectedFiles();
        },
        distance: 1
    });
    console.timeEnd('set files selectable');
    */
    
    console.timeEnd('finderFunctions()');
}

function autoLoadTem(theTem, theLines) {
    // auto-load default template if option is selected and default doesn't match current

    if ((PM.default_tem.length != theTem.length) || (PM.default_lines.length != theLines.length)) {
        // try to autoload the current tem
        var $matched_opts = $('#currentTem li[data-points="' + theTem.length + '"][data-lines="' + theLines.length + '"]');
        if ($matched_opts.length) {
            var defaultTemId = $matched_opts.first().attr('data-id') || 1;
            setCurrentTem(defaultTemId);
        } else {
            //var defaultTemId = $('#default_template').val() || 1;
            PM.default_tem = theTem;
            PM.default_lines = theLines;
            $('#current_tem_name').text('Unknown ' + theTem.length + '-point template');
        }
    }
}

function initVideo() {
    var video = document.querySelector('video');
 
    function successCallback(stream) {
        // Set the source of the video element with the stream from the camera
        if (video.mozSrcObject !== undefined) {
            video.mozSrcObject = stream;
        } else {
            video.src = (window.URL && window.URL.createObjectURL(stream)) || stream;
        }
        video.play();
        PM.webcam = true;
    }
    
    function errorCallback(error) {
        console.error('An error occurred: [CODE ' + error.code + ']');
        $('#webcamDialog').dialog("close");
        growl('Sorry, web camera streaming was not authorised', 2000);
        PM.webcam = false;
    }
    
    navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
    window.URL = window.URL || window.webkitURL || window.mozURL || window.msURL;
    
    // Call the getUserMedia method with our callback functions
    if (navigator.getUserMedia) {
        navigator.getUserMedia({video: true}, successCallback, errorCallback);
    } else {
        console.log('Native web camera streaming (getUserMedia) not supported in this browser.');
        $('#webcamDialog').dialog("close");
        growl('Sorry, web camera streaming is not available in this browser', 2000);
        PM.webcam = false;
    }
}

function webcamPhoto() {
    if (!PM.webcam) { initVideo(); } // initialise video use and get permission if not already done
    
    var video = document.querySelector('video');
    $('#webcamvas').show().attr({
        'width': 640,
        'height': 480
    });
    $('#webcam').hide();
    video.play();
    
    var context = $('#webcamvas').get(0).getContext('2d');
    context.translate(640, 0);
    context.scale(-1,1);
    var showWebCamIntervalId = setInterval(showWebCam, 30);
    
    function showWebCam() { //console.log('showWebCam()');
        if (video.videoWidth > 0 ) {
            context.drawImage(video, 0, 0);
        }
    }
    
    $('#webcamFolder').html(urlToName(currentDir()));
    
    $('#webcamDialog').dialog({
        width: 670,
        beforeClose: function(e,ui) {
            video.pause();
            clearInterval(showWebCamIntervalId);
        },
        buttons: {
            Cancel: function() { 
                video.pause();
                clearInterval(showWebCamIntervalId);
                $(this).dialog("close"); 
            },
            "Take Photo": {
                id: "webcam_take",
                text: "Take Photo",
                class: 'ui-state-focus',
                click: function() {
                    if (video.paused) {
                        video.play();
                        showWebCamIntervalId = setInterval(showWebCam, 30);
                        $('#webcam_take').addClass('ui-state-focus');
                        $('#webcam_save').removeClass('ui-state-focus');
                    } else {
                        video.pause();
                        clearInterval(showWebCamIntervalId);
                        $('#webcam_save').addClass('ui-state-focus');
                        $('#webcam_take').removeClass('ui-state-focus');
                    }
                }
            },
            "Save": {
                id: "webcam_save",
                text: "Save",
                click: function() {
                    var $wcn = $('#webcamName');
                
                    if (!video.paused) {
                        growl('Take the photo first', 1000);
                        return false;
                    } else if ($wcn.val() == '') {
                        $wcn.focus();
                        growl('Name the image', 1000);
                        return false;
                    }
                    
                    var newname = currentDir() + $wcn.val();
                    if (!nameIsAvailable(newname)) { 
                        growl(newname + ' is already taken', 1000);
                        $wcn.focus();
                        return false; 
                    }
                    
                    var dataURL = $('#webcamvas').get(0).toDataURL('image/jpeg', 1.0);
                    
                    $.ajax({
                        url: "scripts/fileSave2",
                        data: { 
                            basedir: currentDir(),
                            name: $wcn.val(),
                            imgBase64: dataURL,
                        },
                        success: function(data) {
                            if (data.error) {
                                growl(data.errorText);
                            } else if ($finder.filter(':visible').length) {
                                loadFiles(data.filenewname);
                                $('#webcamDialog').dialog("close");
                            } else {
                                delinImage(data.filenewname);
                                $('#webcamDialog').dialog("close");
                            }
                        }
                    });
                }
            }
        }
    }).css('max-height', '600px');
}

//====================================
// !FINDER FUNCTIONS
//====================================

function currentDir() {  //console.log('currentDir()');
    var cDir = '',
        openFolders,
        sf;

    openFolders = $('li.folder').filter(':not(.closed):visible').last();
    if (openFolders.length) {
        cDir = openFolders.attr('path');
    } else {
        sf = $('li.file.selected').filter(':first');
        if (sf.length) {
            cDir = sf.attr('url').replace(/\/[^\/]+$/, '');
        }
    }
    return cDir;
}

// access a private file
function fileAccess(img, thumb) {
    if (img && img.substr(-4, 1) != '.') { img += '.jpg'; }

    if (thumb == null) {
        return "/scripts/fileAccess?file=" + img;
    } else {
        return "/scripts/fileAccess?thumb&file=" + img;
    }
}

function emptyTrash() {
    var file_n;

    file_n = $('#trash li.file').length;
    if (file_n == 0 ) {
        growl('There are no files to delete', 1000);
        return false;
    }

    $('<div />').html('Are you sure you want to permanently remove these ' +
                       file_n + ' files? This cannot be undone.').dialog({
        title: 'Empty Trash',
        buttons: {
            Cancel: function() { $(this).dialog("close"); },
            "Empty Trash": {
                text: 'Empty Trash',
                click: function() {
                    $(this).dialog("close");
                    $.ajax({
                        url: 'scripts/dirTrashEmpty',
                        data: { project: WM.project.id },
                        success: function(data) {
                            if (data.error) {
                                growl(data.errorText);
                            } else {
                                loadFiles(WM.project.id + '/.trash');
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
    var cDir,
        theHTML;

    if (WM.project.perm == 'read-only') { return false; }

    cDir = currentDir();
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
                                loadFiles(cDir + dirname);
                            }
                        }
                    });
                }
            }
        }
    });
}

function fileUpload() {
    var cDir,
        uploadedFiles,
        uploadList,
        filenames,
        $uploadDialog;

    if (WM.project.perm == 'read-only') { return false; }

    cDir = currentDir();
    uploadedFiles = document.getElementById('upload').files;
    uploadList = $('<ul class="batchList" />');
    filenames = {};
    for (var i = 0; i < uploadedFiles.length; ++i) {
        var name,
            name_parts,
            suffix,
            prefix;

        name = uploadedFiles.item(i).name;
        uploadList.append('<li>' + name + '</li>');

        name_parts = name.split('.');
        suffix = name_parts.pop();
        prefix = name_parts.join('.');

        if (filenames[prefix] == undefined) {
            filenames[prefix] = [];
        }
        filenames[prefix].push(uploadedFiles.item(i));
    }
    $uploadDialog = $('<div />').html('Upload ' + uploadedFiles.length + ' file' +
                      (uploadedFiles.length == 1 ? '' : 's') + ' to <code>' +
                      urlToName(cDir) + '</code>?').append(uploadList).dialog({
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
                    var totalFiles,
                        uploadedAttempts = 0,
                        uploadedSuccesses = 0,
                        formData = null,
                        $progressUpdate,
                        $errorList,
                        $progressBar,
                        $progressBox

                    uploadList.hide();

                    $(this).parent().find('.ui-dialog-buttonpane').hide();
                    $(this).text('');

                    totalFiles = uploadedFiles.length;

                    $progressUpdate = $('<p />').html('0 of ' + totalFiles + ' files uploaded');
                    $errorList = $('<ol />').css('clear', 'both').css('max-height', '10em').css('overflow', 'auto');
                    $progressBar = $('<div />').addClass('progressBar');
                    $progressBox = $('<div />').addClass('progressBox').append($progressBar);
                    $(this).append($progressUpdate).append($progressBox).append($errorList).dialog({
                        title: 'Upload Images'
                    });
                    $progressBar.css('width', '0%');

                    $.each(filenames, function(i, files) {
                        var formData = new FormData();
                        $.each(files, function(j, file) {
                            formData.append('upload' + '[' + j + ']', file);
                        });
                        formData.append('basedir', cDir);
                        formData.append('desc', 'Uploaded image');
                        $.ajax({
                            data: formData,
                            url: "scripts/fileUpload",
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function(data) {
                                if (!data.error) {
                                    uploadedSuccesses += files.length;
                                    $progressUpdate.html(uploadedSuccesses + ' of ' + totalFiles + ' files uploaded');
                                    WM.finder.addFile(data.newFileName, (files.length == 2));
                                } else {
                                    $errorList.append('<li>' + data.errorText + '</li>');
                                }
                            },
                            error: function() {
                                $errorList.append('<li>Error uploading ' + i + '</li>');
                            },
                            complete: function() {
                                uploadedAttempts += files.length;
                                $progressBar.css('width', (100 * (uploadedAttempts) / totalFiles) + '%');

                                if (uploadedAttempts == totalFiles) {
                                    //loadFiles(cDir);
                                    $('#upload').val('');
                                    if (uploadedSuccesses == totalFiles) {
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

function filePaste(toDir) { console.log('filePaste(' + (toDir == undefined ? '' : toDir) + ')');
    var nImages,
    $fileList,
    cutlist,
    action;

    if (WM.project.perm == 'read-only') { return false; }

    nImages = WM.pasteBoard.length;

    if (nImages) {
        if (toDir == undefined) { toDir = currentDir(); }
        
        $fileList = $('<ul />').css('max-height', '200px');
        cutlist = 0;
        $.each(WM.pasteBoard, function(i, v) {
            $fileList.append('<li>' + urlToName(v) + '</li>');
            if ($('li.to_cut[url="' + v + '"]').length) cutlist++;
        });
        action = (nImages == cutlist) ? 'move' : 'copy';
        $.ajax({
            url: 'scripts/fileCopy',
            data: {
                toDir: toDir,
                files: WM.pasteBoard,
                action: action
            },
            success: function(data) {
                if (data.error) {
                    $('<div />').html(data.errorText).dialog({
                        title: 'Error Pasting Files',
                    });
                    loadFiles(toDir);
                } else {
                    $finder.find('li.folder').addClass('closed');
                    $finder.find('li.file').removeClass('selected');
                    if (action == 'move') {
                        WM.pasteBoard = [];
                    } // clear WM.pasteBoard if moved, not if copied
                    loadFiles(toDir);
                    $('#footer-text').html(nImages + ' files pasted to <code>' + toDir + '</code>');
                }
            }
        });
    }
}

function fileDelete(confirm) {
    var files,
        nfiles,
        $fileList,
        $fileItems,
        dirToDelete,
        $selFolders;

    if (WM.project.perm == 'read-only') { return false; }

    files = filesGetSelected();
    $fileItems = $finder.find('li.file.selected'); //.filter(':visible');
    nFiles = files.length;

    if (nFiles) {    // at least one file is selected
        $fileList = $('<ul class="file" />').css('max-height', '15em');

        $.each(files, function(i, url) {
            $fileList.append('<li>' + urlToName(url) + '</li>');
        });

        if (confirm) {
            $('<div />').html('Move these ' + nFiles + ' files to the Trash?').append($fileList).dialog({
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
                                    loadFiles($fileItems.closest('li.folder').attr('path')); // reload so trash is updated
                                    //$fileItems.addClass('to_cut');
                                    //filePaste(WM.project.id + '/.trash/');
                                }
                                WM.finder.updateSelectedFiles();
                            }
                        });
                    },
                }
            });
        } else {
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
                        loadFiles($fileItems.closest('li.folder').attr('path')); // reload so trash is updated
                        //$fileItems.addClass('to_cut');
                        //filePaste(WM.project.id + '/.trash/');
                    }
                    WM.finder.updateSelectedFiles();
                }
            });
        }
    } else {  // no files are selected, check if there are subfolders
        //var $lastFolder = $finder.find('li.folder:not(.closed):last');
        $selFolders = $finder.find('li.folder.selected, li.folder.selected li.folder').not('#trash');

        if ($selFolders.length) {
            dirToDelete = [];
            $selFolders.addClass('.selected');
            $selFolders.each( function() {
                var path = $(this).attr('path');

                if (path !== WM.project.id + '/' && path !== WM.project.id + '/.trash/') {
                    dirToDelete.unshift(path);
                }
            });

            if (dirToDelete.length == 0) {
                return false;
            } else if ($selFolders.find('li.file').length) {
                // there are still files in the folders, select them and check if they should be deleted
                $selFolders.find('li.file, li.folder').addClass('selected');
                fileDelete(true);
                return false;
            }


            if (confirm) {
                var delTitle = (dirToDelete.length == 1) ?
                               'Delete directory <code>' + urlToName(dirToDelete[0]) + '</code>?' :
                               'Delete ' + dirToDelete.length + ' directories?';

                $('<div />').html(delTitle).dialog({
                    title: 'Delete Directory',
                    buttons: {
                        Cancel: function() {
                            $(this).dialog("close");
                        },
                        "Delete Directory": function() {
                            $(this).dialog("close");
                            $.ajax({
                                url: 'scripts/dirDelete',
                                data: { dirname: dirToDelete },
                                success: function(data) {
                                    if (data.error) {
                                        $('<div />').html(data.errorText).dialog({
                                            title: 'Error Deleting ' + dirToDelete.join()
                                        });
                                    } else {
                                        // modify enclosing folder contents so it will be reloaded on next finder refresh
                                        $selFolders.closest('li.folder').data('contents', '');
                                        $selFolders.remove();
                                    }
                                }
                            });
                        },
                    }
                });
            } else {
                $.ajax({
                    url: 'scripts/dirDelete',
                    data: { dirname: dirToDelete },
                    success: function(data) {
                        if (data.error) {
                            $('<div />').html(data.errorText).dialog({
                                title: 'Error Deleting ' + dirToDelete.join()
                            });
                        } else {
                            // modify enclosing folder contents so it will be reloaded on next finder refresh
                            $selFolders.closest('li.folder').data('contents', '');
                            $selFolders.remove();
                        }
                    }
                });
            }
        }
    }
}

function fileRename() {
    var $theFileItem,
        $theSpan,
        oldurl,
        oldname,
        w,
        $newnameinput,
        fname;

    if (WM.project.perm == 'read-only') { return false; }

    $theFileItem = $finder.find('li.file.selected:first');
    $theSpan = $theFileItem.find('span');
    oldurl = $finder.find('li.file.selected:first').attr('url');
    $finder.find('li.file.selected').removeClass('selected');
    oldname = $theSpan.html();
    w = $theSpan.closest('li').width();
    $newnameinput = $('<input />').val(oldname).attr('type', 'text').width(w);

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
    fname = oldname.replace(/\.(jpg|gif|png|tem)$/,'').length;
    $newnameinput.focus().selectRange(0,fname);
}

function folderRename() {
    var $theFolder,
        $theSpan,
        olddir,
        oldname,
        w,
        $newnameinput;

    if (WM.project.perm == 'read-only') { return false; }

    $theFolder = $finder.find('li.folder').not('.closed').last();

    if ($theFolder.attr('id') == "trash") return false;

    $theSpan = $theFolder.find('> span');
    olddir = $theFolder.attr('path');
    oldname = $theSpan.html();
    w = $theSpan.closest('li').width();
    $newnameinput = $('<input />').val(oldname).attr('type', 'text').width(w);

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
    $project.find('option[value=' + WM.project.id + ']').remove();
    $project.find('option.readOnly').remove();

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


function filesGetSelected(filter, replaced) { //console.log('filesGetSelected(' + filter + ', ' + replaced + ')');
    var sf = [];
    if (filter == null) filter = '';
    if (replaced == null) replaced = '';
    var $selFiles = '';
    if ($finder.find('li.folder.selected').length > 1) {
        $selFiles = $finder.find('li.folder.selected li').filter('.file' + filter);
    } else {
        $selFiles = $finder.find('li.file.selected').filter(':visible' + filter);
    }
    $selFiles.each(function(i, v) {
        sf.push($(this).attr('url').replace(replaced, ''));
    });
    return sf;
}

function fileListGet() {
    var files = filesGetSelected('.image', WM.project.id);
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
        WM.finder.updateSelectedFiles();
        files = filesGetSelected('.image', WM.project.id);
        fileList = files.join("\n");
        $('<div />').html("<textarea style='width:100%; height: 200px;'>" + fileList + "</textarea>").dialog({
            title: 'File List for ' + cd + ' (' + files.length + ' images)',
        }).find('textarea').focus().select();
    } else {
        growl('You have not selected any files.', 1500);
    }
}


function finder(dir) { console.debug('finder(' + (dir == undefined ? '' : dir) + ')');
    this.currentDir = null;
    this.selectedFiles = {};
    
    this.dir = dir;

    this.load = function(subdir) { console.log('finder.load(' + subdir + ')');
        subdir = subdir || this.dir;

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
                WM.finder.updateSelectedFiles();
                $finder.css('background-image', 'none');
            }
        });
    };
    
    this.open = function(path) { console.debug("finder.open(" + path + ")");
        // opens a file or folder in the finder
        var pathparts = path.split('/');
        var n = pathparts.length;
        var thispath = '';
        for (i = 0; i < n; i++) {
            if (i == n-1 && path.substr(-1,1) != '/') {
                // open a file
                thispath += pathparts[i];
                $finder.find('li.file[url="' + thispath + '"]').click();
            } else {
                // open a folder
                thispath += pathparts[i] + '/';
                $finder.find('li.folder[path="' + thispath+ '"] > span').click();
            }
        }        
    }

    this.folder = function(path) {
        // find or create a folder from a path in the format #/subdir/
        var $theFolder,
            $sibFolders,
            $container,
            containerPath,
            shortName;
        
        $theFolder = $finder.find('li.folder[path="' + path + '"]');
        
        if (!$theFolder.length) {
            // create a new folder
            shortName = path.match(/^\d{1,10}\/(?:([^\/]+)\/)*$/)[1];
            $theFolder = $('<li class="folder closed" path="'+path+'"><span>' + shortName + '</span><ul></ul></li>');
            
            // check for the containing folder
            containerPath = path.match(/^(\d{1,10}(?:\/[^\/]+)*\/)[^\/]+\/$/)[1];
            $container = this.folder(containerPath);
            
            // get list of folders in container and insert in alphabetical order
            $sibFolders = $container.find('> ul > li.folder');
            
            if ($sibFolders.length > 0) {
                $sibFolders.each( function(i) {
                    if (path < $(this).attr('path')) {
                        $(this).before($theFolder);
                        return false;
                    } else if (i == $sibFolders.length - 1) {
                        $(this).after($theFolder);
                    }
                });
            } else {
                $container.find('> ul').prepend($theFolder);
            }
        }
        
        return $theFolder;
    };

    this.addFile = function(url, addTem) { 
        var regexURL,
            $theDir,
            $theFile,
            $matchedFile,
            $theUL,
            $sibFiles,
            shortName,
            project_id,
            subdir,
            theClass,
            wasClosed,
            ext,
            w,
            img = false,
            that = this;
            
        // handle array passed to url
        if (Array.isArray(url)) {
            console.log('addFile(array)');
            $.each(url, function(i, thisurl) {
                $theFile = that.addFile(thisurl, addTem);
            });
            return $theFile;
        }
        
        console.log('addFile(' + url + ', ' + addTem + ')');
        
        // 0 = valid, 1 = project_id, 2 = subfolders, 3 = shortname, 4 = ext
        regexURL = url.match(/^(\d{1,10})((?:\/[^\/]*)*\/)([^\/]+)\.([a-z]{3})$/);
        if (regexURL == null || regexURL.length != 5) {
            console.debug('Problem with the file URL: ' + url);
            return false;
        }
        
        project_id = regexURL[1];
        subdir = regexURL[2];
        shortName = regexURL[3];
        ext = regexURL[4];
            
        if (!regexURL) {
            return false;
        }

        $theDir = this.folder(project_id + subdir);
        
        $theFile = this.file(url, $theDir);

        $theUL = $theDir.find('>ul');
        
        $sibFiles = $theUL.find('>li.file');
            
        if ($sibFiles.length > 0) {
            $sibFiles.each( function(i) {
                if (url < $(this).attr('url')) {
                    $(this).before($theFile);
                    return false;
                } else if (i == $sibFiles.length - 1) {
                    $(this).after($theFile);
                }
            });
        } else {
            $theUL.append($theFile);
        }
        
        if (addTem === true && ext != 'tem') {
            $theFile.addClass('hasTem');
            this.addFile(project_id + subdir + shortName + '.tem');
        }
        
        // check if this file has a corresponding image/tem
        if (ext == 'tem') {
            $matchedFile = $finder.find('li.file.image[url*="' + project_id + subdir + shortName + '"]');
            if ($matchedFile.length) {
                $matchedFile.addClass('hasTem');
                $theFile.addClass('hasImg');
            }
        } else if ($.inArray(ext, ['jpg','png','gif'])) {
            $matchedFile = $finder.find('li.file.tem[url*="' + project_id + subdir + shortName + '"]');
            if ($matchedFile.length) {
                $matchedFile.addClass('hasImg');
                $theFile.addClass('hasTem');
            }
        }
        
        return $theFile;
    }

    this.file = function(url, container) {
        // find or create a file from a url in the format #/subdir/filename.ext
        
        var $theFile,
            ext,
            theClass,
            img,
            shortName;
        
        if (container == undefined) {
            container = $finder;
        }
        
        $theFile = container.find('.file[url="' + url + '"]');

        if (!$theFile.length) {
            ext = url.substring(url.length - 4);
            theClass = 'file';
            img = false;
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
            } else if (ext == '.svg') {
                theClass += ' svg image';
                img = true;
            }
            shortName = url.replace(/^.*\//, '');
            $theFile = $('<li class="' + theClass + '" url="' + url + '"><span>' + shortName + '</span></li>');

            if (img && $('#show_thumbs').prop('checked')) {
                $theFile.css('background-image', 'url(/scripts/fileAccess?thumb&file=' + url + ')');
            }
        }
        return $theFile;
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

        if (WM.appWindow == 'average' || WM.appWindow == 'transform') {
            $files.hide().filter('.image.hasTem').show();
        }
        console.timeEnd('imagesWithTems()');
    };
    
    this.updateSelectedFiles = function() { //console.log('WM.finder.updateSelectedFiles()');
        hashSet();
        var $selFolders = $finder.find('li.folder.selected').filter(':visible');
        var s = $finder.find('li.file.selected').filter(':visible').length;
    
        if ($selFolders.length > 1) {
            var nFiles = $selFolders.find('li.file').length;
            $('#footer-text').html($selFolders.length + ' folders selected containing ' + nFiles + ' files');
        } else {
            var cdir = currentDir();
            var vFiles = $finder.find('li.folder[path="' + cdir + '"] > ul > li.file').length;
    
            $('#imgname').html(urlToName(cdir));
            $('#footer-text').html(s + ' of ' + vFiles + ' file' + (vFiles == 1 ? '' : 's') + ' selected');
        }
    
        if (WM.appWindow == 'average') {
            checkAvgAbility();
        } else if (WM.appWindow == 'transform') {
            if (s > 1) {
                $('#transButton').button('option', 'label', 'Transform All');
            } else {
                $('#transButton').button('option', 'label', 'Transform');
            }
        }
    
        if (s == 1 && !$finder.hasClass('image-view')) {
            $('#filepreview').show();
        } else {
            $('#filepreview').hide();
        }
    }
}



$("#finder").on('DOMNodeInserted', "li.file:not(.ui-draggable)", function() { 
    //console.debug('Draggable file: ' + $(this).attr('url'));
    var thisContainment = (WM.appWindow == 'finder') ? '#finder' : 'window';
    
    $(this).draggable({
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
            $('#filepreview').hide().appendTo($finder);
            $finder.find('li.file.selected').filter(':visible').each( function(i, v) {
                var $clone = $(this).clone().removeClass('selected');
                ui.helper.append($clone);
            });
            $('#cutItems').click();
        },
        stop: function(event, ui) {
            WM.pasteBoard = [];
            $finder.find('li.file').removeClass('to_cut'); // clear all to_cut files
        }
/*    }).selectable({
        selected: function(e,ui){
            $(this).addClass('selected');
        },
        selecting: function(e,ui){
            $(this).addClass('selected');
        },
        unselected: function(e,ui){
            $(this).removeClass('selected');
        },
        unselecting: function(e,ui){
            $(this).removeClass('selected');
        }*/
    });
});

$("#finder").on('DOMNodeInserted', "li.folder:not(.ui-draggable)", function() { 
    //console.debug('Draggable folder: ' + $(this).attr('path'));
    $(this).draggable({
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
});

$("#finder").on('DOMNodeInserted', "> ul li.folder > ul:not(.ui-droppable), li.folder:not(.ui-droppable)", function() { 
    //console.debug('Droppable ' + ($(this).attr('path') != undefined ? 'folder: ' + $(this).attr('path') : 'ul: ' + $(this).parent().attr('path')));
    $(this).droppable({
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
            $('#footer-text').html(action + ' ' + to_move + ' to <code>' + urlToName($folder.attr('path')) + '</code>');
        },
        out: function(e, ui) {
            $('#footer-text').html('');
        },
        drop: function( e, ui ) {
            if ($(this).hasClass('folder')) {
                $folder = $(this);
            } else {
                $folder = $(this).closest('li.folder');
            }

            if (WM.pasteBoard.length) {
                var itemPath = WM.pasteBoard[0].replace(/\/[^\/]+$/, '/');
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
});

function loadFiles(selected_dir, subdir) { 
    console.log('loadFiles(' + selected_dir + ', ' + subdir + ')');
    $('#footer-text').html('Loading Files...');
    var $spinner = bodySpinner();

    if (subdir === true && selected_dir !== undefined) {
        subdir = selected_dir.replace(/\/$/, '').replace(/[^\/]+$/, '');
    } else if (subdir === undefined) {
        subdir = WM.project.id;
    }

    $.ajax({
        url: 'scripts/dirLoad',
        data: { subdir: subdir },
        success: function(data) {
            if (data.error) {
                $('<div title="Error Loading Files" />').html(data.errorText).dialog();
            } else {
                // move filepreview out of finder first
                $('#filepreview').hide().insertBefore($('#uploadbar'));
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
                var $trash = $finder.find('li.folder[path="' + WM.project.id + '/.trash/"]');
                $trash.attr('id', 'trash');
                $trash.find('> span').text('Trash');

                if ($('#toggletrash span.checkmark').css('display') == 'block') {
                    $trash.show();
                } else {
                    $trash.hide();
                }

                WM.finder.updateSelectedFiles();
                //WM.finder.finderFunctions();

                if (selected_dir !== undefined && selected_dir.length > 0) {
                    fileShow(selected_dir);
                }
                $('#footer-text').html($finder.find('li.file').length + ' files loaded');
            }
            WM.finder.imagesWithTems();
            $finder.css('background-image', 'none');
            $spinner.remove();
            sizeToViewport();

            // set WM.faceimg so clicks to the delineaton interface show something
            if (WM.faceimg == '') {
                // set to first image
                var firstImage= $finder.find('li.file.image').not('[url*=".trash"]');
                
                if (firstImage.length) {
                    WM.faceimg = firstImage.attr('url');
                    console.debug('WM.faceimg set to ' + WM.faceimg);
                }
            }
            
            WM.hashfile();
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

    /*
    var w = appendElement.width();
    appendElement.siblings('li').find('> ul').css('margin-left', w);
    theFolder.css('margin-left', w);
    */
    
    var $allItems = theFolder.find('> li').addClass('oldfinder'); // mark all items as old

    var fItems = [];

    for (var folder in json) {
        if (json.hasOwnProperty(folder)) {
            var contents = json[folder];
            var theItem;
            if (typeof contents === 'string') {
                // contents are an image name
                var url = folder.replace(/^i/, '');
                theItem = WM.finder.file(url, theFolder).removeClass('oldfinder');
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

function autoLoadTem(theTem, theLines) {
    // auto-load default template if option is selected and default doesn't match current

    if ((WM.delin.tem.length != theTem.length) || (WM.delin.lines.length != theLines.length)) {
        // try to autoload the current tem
        var $matched_opts = $('#currentTem li[data-points="' + theTem.length + '"][data-lines="' + theLines.length + '"]');
        if ($matched_opts.length) {
            var defaultTemId = $matched_opts.first().attr('data-id') || 1;
            setCurrentTem(defaultTemId);
        } else {
            //var defaultTemId = $('#defaultTemplate').val() || 1;
            WM.delin.tem = theTem;
            WM.delin.lines = theLines;
            $('#currentTem_name').text('Unknown ' + theTem.length + '-point template');
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
        WM.webcam = true;
    }

    function errorCallback(error) {
        console.error('An error occurred: [CODE ' + error.code + ']');
        $('#webcamDialog').dialog("close");
        growl('Sorry, web camera streaming was not authorised', 2000);
        WM.webcam = false;
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
        WM.webcam = false;
    }
}

function webcamPhoto() {
    if (!WM.webcam) { initVideo(); } // initialise video use and get permission if not already done

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
                        url: "scripts/webcamUpload",
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

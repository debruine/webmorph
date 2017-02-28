<?php

/**************************************************************************
 * PsychoMorph Classes
 *
 * PHP version 5
 *
 * @author     Lisa DeBruine <debruine@gmail.com>
 * @copyright  2013 Face Research Lab
 *************************************************************************/
 
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

/**************************************************************************
 * PsychoMorph_File
 *
 * Holds generic file functions for images and tems
 *************************************************************************/

class PsychoMorph_File {
    private $_path = '';
    public  $_description = array();
    
    public function __construct($path = '') {
        if ($path) { $this->_setPath($path); }
        return $this;
    }
    
    public function _setPath($path) {
        $this->_path = $this->_validateFilePath($path);

        if ($this->_path) { 
            if (!$this->_loadFile()) {
                return false;
            }
        }

        return $this;
    }
    
    public function getPath() {  return $this->_path; }
    
    public function getDir() {
        return pathinfo($this->_path, PATHINFO_DIRNAME);
    }
    
    public function getURL($withProject = true) {
        if ($withProject) {
            $pattern = '@^' . (IMAGEBASEDIR) . '@';
        } else {
            $pattern = '@^' . (IMAGEBASEDIR) . '\d+@';
        }
        return preg_replace($pattern, '', $this->_path);
    }
    
    public function getProject() {
        $pattern = '@^' . (IMAGEBASEDIR) . '(\d+)/.+@';
        preg_match($pattern, $this->_path, $output);
        return $output[1];
    }
    
    public function getFileSize() {
        $filesize = formatBytes(filesize($this->_path));
        return($filesize);
    }
    
    public function getFileType() {
        $filetype = mime_content_type($this->_path);
        return($filetype);
    }
    
    public function getCreateDate() {
        $created = date('Y-m-d H:i:s', filemtime($this->_path));
        return($created);
    }
    
    private function _validateFilePath($name) {
        // handle several different versions of file paths
        $filename     = '/([^?;:{}/]+/)*[^?;:{}/]+(\.(gif|png|jpg|tem))?$@';
        $withroot     = '@^' . (IMAGEBASEDIR) .'\d{1,11}' . $filename;
        $upload        = '@^/private/var/tmp' . $filename;
        $upload2    = '@^/tmp' . $filename;
        $noroot     = '@^\d{1,11}' . $filename;
        
        if (preg_match($withroot, $name)) {
            $filepath = $name;
        } else if (preg_match($upload, $name)) {
            $filepath = $name;
        } else if (preg_match($upload2, $name)) {
            $filepath = $name;
        } else if (preg_match($noroot, $name)) {
            $filepath = IMAGEBASEDIR . $name;
            $filepath = str_replace('//', '/', $filepath);
        } else {
            return false;
        }
        
        return $filepath;
    }
    
    public function _loadFile() {
        // placeholder function for descendant functions
        // to implement filetype-specific loading
        return false;
    }
    
    public function newPathFromArray($filepath) {
        // create new directory if does not exist
        $oldpath = $this->getPath();
        $ext = pathinfo($oldpath, PATHINFO_EXTENSION);
        $name = pathinfo($oldpath, PATHINFO_FILENAME);
        
        if (array_key_exists('name',$filepath)) $name = $filepath['name'];
        if (in_array($filepath['ext'], array('jpg', 'gif', 'png', 'tem', 'svg'))) $ext = $filepath['ext'];
        
        $subfolder = safeFileName($filepath['subfolder']);
        $prefix = str_replace('/', '_', safeFileName($filepath['prefix']));
        $suffix = str_replace('/', '_', safeFileName($filepath['suffix']));
        
        $basedir = IMAGEBASEDIR . $subfolder . '/';
        $basedir = str_replace('//', '/', $basedir);

        if (!is_dir($basedir)) {
            if (!mkdir($basedir, DIRPERMS, true)) {
                //$return['errorText'] .= "The new directory <code>$subfolder</code> could not be created. ";
                return false;
            }
        }
        
        $newpath = $basedir . $prefix . $name . $suffix . '.' . $ext;
        return $this->_validateFilePath($newpath);
    }
    
    public function getDescription($type = 'json') {
        if ($type == 'htmlArray') {
            // return in htmlArray format
            return htmlArray($this->_description);
        } else if ($type == 'array') {
            // return in array format
            return $this->_description;
        } else {
            // get description in JSON format
            return json_encode($this->_description, 
                JSON_UNESCAPED_UNICODE | 
                JSON_UNESCAPED_SLASHES | 
                JSON_NUMERIC_CHECK | 
                JSON_PRETTY_PRINT
            );
        } 
    }
    
    public function setDescription($v, $v2 = null) {
        // sets description of image for exif
        // or description of tem for comments
        // formatted as an array to be saved as JSON
    
        if (empty($v)) {
            return false;
        } else if (is_array($v)) {
            $this->_description = array_merge($this->_description, $v);
        } else if (!is_null($v2)) {
            $this->_description[$v] = $v2;
        } else {
            $this->_description['extras'][] = $v;
        }
        
        return $this;
    }
    
    public function deleteDescription($key) {
        if (empty($key)) {
            $this->_description = array();
        } else {
            unset($this->_description[$key]);
        }
        
        return $this;
    }
    
    public function addHistory($history) {
        // add an entry to history on the json comments
        $this->_description['history'][date('Y-m-d H:i:s')] = $history;
        
        return $this;
    }
    
    public function save($filepath = null, $overWrite = false) {
        // if filepath is empty, just save with original name,
        // if filepath is a string, check for overwrite and save with that name
        // if filepath is an array, create new path name from subfolder, prefix and suffix
    
        if (empty($filepath)) { 
            $filepath = $this->getPath(); 
        } else if (is_array($filepath)) {
            $filepath = $this->newPathFromArray($filepath);

            if (!$overWrite && is_file($filepath)) {
                // path exists and $overWrite is not set to true
                return false;
            }
        } else {
            $filepath = $this->_validateFilePath($filepath);
            $basedir = pathinfo($filepath, PATHINFO_DIRNAME);
            
            if (!empty($filepath) && (!$overWrite && is_file($filepath)) && ($this->_path !== $filepath)) {
                // path exists and $overWrite is not set to true
                // and _path isn't already set to this filepath
                return false;
            }
            
            if (!is_dir($basedir)) {
                if (!mkdir($basedir, DIRPERMS, true)) {
                    echo "cannot make $basedir for $filepath";
                    return false;
                }
            }
        }
        
        if ($overWrite && is_file($filepath)) {
            unlink($filepath);
        }
        
        if ($this->_saveFile($filepath)) {
            $this->_path = $filepath;
            return $this;
        }
        
        return false;
    }
    
    public function _saveFile($filepath = '', $overWrite = false) {
        // placeholder function for descendant functions
        // to implement filetype-specific saving
        return false;
    }
}

?>

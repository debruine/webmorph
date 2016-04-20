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
    private $_path;
    
    public function __construct($path) {
        $this->_setPath($path);
        
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
    
    public function getURL() {
        $pattern = '@^' . IMAGEBASEDIR . '@';
        return preg_replace($pattern, '', $this->_path);
    }
    
    public function getUserPath() {
        // users shouldn't see the path structure below their user directory
        $pattern = '@^' . IMAGEBASEDIR . '@';
        return preg_replace($pattern, '', $this->_path);
    }
    
    private function _validateFilePath($name) {
        // handle several different versions of file paths
        $filename     = '/([^?;:{}/]+/)*[^?;:{}/]+(\.(gif|png|jpg|tem))?$@';
        $withroot     = '@^' . IMAGEBASEDIR .'\d{1,11}' . $filename;
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
    
    public function save($filepath = null, $overWrite = false) {
        // if filepath is empty, just save with original name,
        // if filepath is a string, check for overwrite and save with that name
        // if filepath is an array, create new path name from subfolder, prefix and suffix
    
        if (empty($filepath)) { 
            $filepath = $this->getPath(); 
        } else if (is_array($filepath)) {
            // create new directory if does not exist
            $oldpath = $this->getPath();
            $ext = pathinfo($oldpath, PATHINFO_EXTENSION);
            $name = pathinfo($oldpath, PATHINFO_FILENAME);
            
            if (array_key_exists('name',$filepath)) $name = $filepath['name'];
            if (in_array($filepath['ext'], array('jpg', 'gif', 'png', 'tem'))) $ext = $filepath['ext'];
            
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
            $filepath = $this->_validateFilePath($newpath);

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
                    //$return['errorText'] .= "The new directory <code>$subfolder</code> could not be created. ";
                    echo "cannot make $basedir for $filepath";
                    return false;
                }
            }
        }
        
        if ($this->_saveFile($filepath)) {
            $this->_path = $filepath;
            return $this;
        }
        
        return false;
    }
    
    public function _saveFile($filepath) {
        // placeholder function for descendant functions
        // to implement filetype-specific saving
        return false;
    }
}

?>

<?php

/**************************************************************************
 * PsychoMorph Classes
 *
 * PHP version 5
 *
 * @author     Lisa DeBruine <debruine@gmail.com>
 * @copyright  2013 Face Research Lab
 *************************************************************************/

require_once 'psychomorph.file.class.php';

/**************************************************************************
* PsychoMorph_Tem
*************************************************************************/

class PsychoMorph_Tem extends PsychoMorph_File {
    private $_points = array();
    private $_lines = array();

    public function _loadFile() {
        $this->_points = array();
        $this->_lines = array();
    
        // read the tem file
        $path = $this->getPath();
        if (!file_exists($path) || !$tem = file($path)) { return false; }
        
        $pointNumber = trim($tem[0]);
        
        $this->_points = array_slice($tem, 1, $pointNumber);
        $this->_points = array_map(function($n) { 
            $pts = preg_split('@\s+@', $n);
            return array($pts[0], $pts[1]);
        }, $this->_points);
        
        for ($i = $pointNumber+4; $i < count($tem); $i += 3) {
            $this->_lines[] = preg_split('@\s+@', trim($tem[$i]));
        }
        
        return $this;
    }
    
    public function setPoints($i, $v) { $this->_points[$i] = $v; }
    public function getPoints($i = null) { 
        if (is_null($i)) {
            return $this->_points; 
        } else if ($i < 0 || $i > count($this->_points)) {
            return false;
        } else {
            return $this->_points[$i];
        }
    }

    public function setLines($newLines) {
        // changes lines, can handle:
        //   blank entry (deletes all lines)
        //   all tex: space-delimited points with return-delimited lines
        //   1D array: array of space-deliminetd points
        //   2D array: array of points in arrays
    
        $this->_lines = array();
        
        if ($newLines == '') {
            return true;
        } else if (!is_array($newLines)) {
            // split at line breaks
            $newLines = preg_split('@\R@', trim($newLines));
        }
            
        foreach($newLines as $line) {
            if (is_array($line)) {
                $this->_lines[] = $line;
            } else {
                $this->_lines[] = preg_split('@(\s|,)+@', trim($line));
            }
        }
    }
    public function getLines() { return $this->_lines; }
    public function getPointNumber() { return count($this->_points); }
    
    public function deletePoints($pointArray) {
        $old_points = $this->getPointNumber();
        $pointmap = array();
    
        // order point array from high to low
        rsort($pointArray);
        
        foreach($pointArray as $i) {
            array_splice($this->_points, $i, 1);
            $pointmap[$i] = 'removed';
        }
        
        // adjust tem lines
        $newp = 0;
        for ($i = 0; $i < $old_points; $i++) {
            if ($pointmap[$i] !== 'removed') {
                $pointmap[$i] = $newp;
                $newp++;
            }
        }
        
        // remap and mark points to be removed
        foreach($this->_lines as $line => $points) {
            foreach($points as $i => $point) {
                $this->_lines[$line][$i] = $pointmap[$point];
            }
        }
        
        // remove deleted points
        $nlines = count($this->_lines) - 1;
        
        for ($line = $nlines; $line > 0; $line--) {
            $n = count($this->_lines[$line]) - 1;
            $removed = 0;
            foreach ($this->_lines[$line] as $p) {
                if ($p === 'removed') { $removed++; }
            }
            if ($n+1 == $removed) {
                // remove whole line
                array_splice($this->_lines, $line, 1);
            } else {
                // check individual points to remove
                for($i = $n; $i >= 0; $i--) {
                    if ($this->_lines[$line][$i] === 'removed') {
                        array_splice($this->_lines[$line], $i, 1);
                    }
                }
            }
        }
    }
    
    public function temConvert($old, $new) {
    
        /*
        CREATE TABLE tem_convert (
            new_tem INT(11),
            n INT(4),
            old_tem INT(11),
            x VARCHAR(255),
            y VARCHAR(255),
            UNIQUE INDEX (new_tem, old_tem, n)
        );
        */
            
        $oldpoints = $this->_points;
        $newpoints = array();
        $x = array();
        $y = array();
        
        foreach ($oldpoints as $n => $pt) {
            $x[$n] = $pt[0];
            $y[$n] = $pt[1];
        }
        
        $q = new myQuery("SELECT n, x, y FROM tem_convert WHERE new_tem=$new AND old_tem=$old");
        
        if ($q->get_num_rows() == 0) return false;
        
        $map = $q->get_assoc();
        
        foreach ($map as $m) {
            // x-coordinate
            $eq = preg_replace('/\s+/', '', $m['x']); // Remove whitespaces
            $eq = str_replace( array('x[', 'y['), array('$x[', '$y['), $eq );
            eval('$result = '.$eq.';');
            
            if (abs($result) >= 100) {
                $newpoints[$m['n']][0] = round($result, 1);
            } else {
                $newpoints[$m['n']][0] = round($result, ceil(0 - log10($result)) + 2);
            }
            
            // y-coordinate
            $eq = preg_replace('/\s+/', '', $m['y']); // Remove whitespaces
            $eq = str_replace( array('x[', 'y['), array('$x[', '$y['), $eq );
            eval('$result = '.$eq.';');
            
            if (abs($result) >= 100) {
                $newpoints[$m['n']][1] = round($result, 1);
            } else {
                $newpoints[$m['n']][1] = round($result, ceil(0 - log10($result)) + 2);
            }
        }
        
        $this->_points = $newpoints;
        
        // get new lines
        $q = new myQuery("SELECT points from line WHERE tem_id=$new ORDER BY n");
        
        $lines = $q->get_assoc(false, false, "points");
        $newline = array();
        foreach ($lines as $line) {
            $newline[] = explode(",", trim($line));
        }
        $this->_lines = $newline;
        
        return true;
    }
    
    public function resize($xResize, $yResize = null) {
        if ($yResize == null) { $yResize = $xResize; }
        if ($xResize <= 0 || $yResize<=0 || $xResize > 10 || $yResize > 10) {
            // resize is too small or too big
            return false;
        }
        
        // resize template
        foreach ($this->_points as $i => $p) {
            $this->_points[$i][0] = $p[0] * $xResize;
            $this->_points[$i][1] = $p[1] * $yResize;
        }
        
        return true;
    }
    
    public function rotate($degrees, $origW, $origH, $newW, $newH) {
        $rotate = deg2rad($degrees);
        $n = $this->getPointNumber();
        
        $xm1 = $origW/2;
        $ym1 = $origH/2;
        $xm2 = $newW/2;
        $ym2 = $newH/2;
        
        for ($i = 0; $i < $n; $i++) {
            list($x, $y) = $this->_points[$i];

            // Subtract original midpoints, so that midpoint is translated to origin
            // and add the new midpoints in the end again
            $xr = ($x - $xm1) * cos($rotate) - ($y - $ym1) * sin($rotate)   + $xm2;
            $yr = ($x - $xm1) * sin($rotate) + ($y - $ym1) * cos($rotate)   + $ym2;
            
            $this->_points[$i] = array($xr, $yr);
        }
    }
    
    public function crop($xOffset, $yOffset) {
        foreach ($this->_points as $i => $pts) {
            $this->_points[$i] = array($pts[0] - $xOffset, $pts[1] - $yOffset);
        }
    }
    
    public function mirror($sym, $w) {
        // create the mirror-reversed version of the file
        // use the sym file in argument1
        
        if (is_int($sym)) {
            // $sym is a tem_id, get the sym file from the database
            $q = new myQuery("SELECT n, sym FROM point WHERE tem_id={$sym} AND sym IS NOT NULL");
            $sym = $q->get_assoc(false, 'n', 'sym');
        }
        
        if ($this->getPointNumber() != count($sym)) {
            return false;
        }
        
        $mirror_pts = array();
        foreach ($this->_points as $i => $pts) {
            $mirror_pts[$i] = $this->getPoints($sym[$i]);
        }
        
        foreach ($mirror_pts as $i => $pts) {
            $pts[0] = $w - $pts[0]; // adjust for flipping
            $this->setPoints($i, $pts);
        }

        return $this;
    }
    
    public function printTem() {
        // return string in PsychoMorph .tem format
        $return = $this->getPointNumber() . "\n";
        
        foreach ($this->_points as $p) {
            $return .= "$p[0]\t$p[1]\n";
        }
        
        $return .= count($this->_lines) . "\n";
        
        foreach ($this->_lines as $l) {
            $return .= "0\n";
            $return .= count($l) . "\n";
            $return .= implode(" ", $l) . "\n";
        }
        
        $return .= count($this->_lines)-1 . "\n";
        
        return $return;
    }
    
    public function _saveFile($filepath) {
        if (empty($filepath)) {
            $filepath = $this->getPath();
        }
    
        if ('tem' !== pathinfo($filepath, PATHINFO_EXTENSION)) { $filepath .= '.tem'; }
    
        if (   !($file = fopen($filepath, 'w'))        // file can be opened
            || !fwrite($file, $this->printTem())    // file can be written to
            || !fclose($file)                        // file can be closed
        ) {
            return false;
        }
        
        chmod($filepath, IMGPERMS);                        // make sure only web user can access                    
        
        return true;
    }

}

?>
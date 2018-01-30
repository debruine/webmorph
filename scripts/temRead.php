<?php

// read the tem file

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();
session_write_close();

$return = array(
    'error' => true,
    'errorText' => '',
    'tem' => '',
    'desc' => ''
);

$filename = $_GET['url'];
$temfile = IMAGEBASEDIR . safeFileName($filename);

include_once DOC_ROOT . '/include/classes/psychomorph.tem.class.php';

if (!file_exists($temfile)) {
    $return['errorText'] .= "The file <code>$filename</code> does not exist.";
/*
} else if (!($mytem = file($temfile))) {
    $return['errorText'] .= "The file <code>$filename</code> could not be read.";
} else if (count($mytem) < 1) {
    $return['errorText'] .= "The file <code>$filename</code> had no data.";
*/
} else if ($tem = new PsychoMorph_Tem($temfile)) {
    $return['error'] = false;
    $return['tem'] = $tem->printTem(false);
    $desc = $tem->getDescription('array');
        
    $info['Name'] = $tem->getURL(false);
    $info['Linked Image'] = $desc['image'];
    $info['Kind'] = $tem->getFileType();
    $info['Size'] = $tem->getFileSize();
    $info['Created'] = $tem->getCreateDate();
    $info['Last Saved'] = $desc['last_saved'];
    
    $return['info'] = htmlArray($info);
    $return['history'] = preg_replace('@(<td>\d{4}-\d{1,2}-\d{1,2})( )(\d{2}:\d{2}:\d{2}</td>)@m', '$1<br>$3', htmlArray($desc['history']));
}

scriptReturn($return);

exit;

?>
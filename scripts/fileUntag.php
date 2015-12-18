<?php

// remove tags from an image in the database

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
	'error' => false,
	'errorText' => '',
);

if (count($_POST['files']) < 1) {
	$return['error'] = true;
	$return['errorText'] .= 'There were no files to tag';
} else {

	// clean up tags
	$tags = cleanTags($_POST['tags']);
	$imageset = implode("','", $_POST['files']);
	
	foreach ($tags as $tag) {
		$q = new myQuery("DELETE FROM tag WHERE tag='{$tag}' AND tag.id IN (SELECT id FROM img WHERE img.name IN ('{$imageset}'))");
	}

}

scriptReturn($return);

exit;

?>
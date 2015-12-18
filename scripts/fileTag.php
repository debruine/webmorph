<?php

// add tags to an image in the database

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
		$q = new myQuery("REPLACE INTO tag (id, type, tag) SELECT img.id, 'img', '{$tag}' FROM img WHERE img.name IN ('{$imageset}')");
	}

}

scriptReturn($return);

exit;


/*

CREATE TABLE tag (
	id INT(11) NOT NULL,
	type ENUM('img') NOT NULL,
	tag VARCHAR(255) NOT NULL,
	CONSTRAINT id_type_tag UNIQUE (id, type, tag)
);

*/

?>
<?php

// add to database a new default template

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$return = array(
	'error' => false,
	'errorText' => '',
);

$user = $_SESSION['user_id'];

// check if user has permission to edit this template, if not a new template
if ($_POST['id'] !== 'NULL') {
	$id = intval($_POST['id']);
	$q = new myQuery("SELECT COUNT(*) as c FROM tem WHERE user_id={$user} AND id={$id}");
	if ($q->get_one() == 0) {
		$return['error'] = true;
		$return['errorText'] = 'You do not have permission to modify this template. You can set it as a new template.';
		header('Content-Type: application/json');
		echo json_encode($return);
		exit;
	}
}


$dp1 = intval($_POST['delinPts'][0]);
$dp2 = intval($_POST['delinPts'][1]);
$dp3 = intval($_POST['delinPts'][2]);

$q = new myQuery(sprintf(
	'REPLACE INTO tem (id, user_id, name, notes, public, 
		3ptdelin1, 3ptdelin2, 3ptdelin3, 
		align_pt1, align_pt2, width, height) 
		VALUES (%s, %d, "%s", "%s", 0, 
		%d, %d, %d, 
		%d, %d, %s, %s)',
	$_POST['id'],
	$user,
	$_POST['name'],
	$_POST['notes'],
	//($_POST['public'] == 'true' ? 'TRUE' : 'FALSE'),
	$dp1, $dp2, $dp3,
	$dp1, $dp2,
	$_POST['width'],
	$_POST['height']
));
$return['query'] = $q->get_query();
$tem_id = ($_POST['id'] == 'NULL') ? $q->get_insert_id() : $_POST['id'];
$return['tem_id'] = $tem_id;

if (count($_POST['tem'])) {
	$q = new myQuery("DELETE FROM point WHERE tem_id={$tem_id};");
	
	$tem_query = array();
	foreach ($_POST['tem'] as $i=>$t) {
		//$tem_query[] = sprintf('(%d, %d, %f, %f, "%s")',
		//	$tem_id, $i, $t['x'], $t['y'], my_clean($t['name'])
		//);
		$x = intval($t['x']);
		$y = intval($t['y']);
		$q = new myQuery("INSERT INTO point (tem_id, n, x, y) VALUES ({$tem_id}, {$i}, {$x}, {$y});");
	}
}

if (count($_POST['lines'])) {
	$q = new myQuery("DELETE FROM line WHERE tem_id={$tem_id};");
	
	$line_query = array();
	foreach ($_POST['lines'] as $i=>$l) {
		$line_query[] = sprintf('(%d, %d, "%s")',
			$tem_id, $i, implode(',', $l)
		);
	}
	$q = new myQuery('INSERT INTO line (tem_id, n, points) VALUES ' . implode(',', $line_query));
}

scriptReturn($return);
exit;

?>
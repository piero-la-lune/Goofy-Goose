<?php

if (isset($_POST['action']) && isset($_POST['page'])) {

	$manager = Manager::getInstance();

	if ($_POST['action'] == 'watched') {
		$ans = $manager->episode_watched($_POST);
		if ($ans === true) {
			die(json_encode(array('status' => 'success')));
		}
	}
	elseif ($_POST['action'] == 'swatched') {
		$ans = $manager->season_watched($_POST);
		if ($ans === true) {
			die(json_encode(array('status' => 'success')));
		}
	}
}

die(json_encode(array('status' => 'error')));

?>
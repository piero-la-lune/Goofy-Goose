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
	elseif ($_POST['action'] == 'subtitles') {
		$ans = $manager->subtitles($_POST);
		if ($ans !== false) {
			$html = '<div class="div-subtitle">'
				.'<span class="span-language">Français : </span>'
				.Manager::display_subtitles($ans['fr'])
				.'</div><div class="div-subtitle">'
				.'<span class="span-language">English : </span>'
				.Manager::display_subtitles($ans['en'])
				.'</div>';
			die(json_encode(array('status' => 'success', 'ans' => $html)));
		}
	}
}

die(json_encode(array('status' => 'error')));

?>
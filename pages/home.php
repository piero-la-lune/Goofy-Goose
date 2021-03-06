<?php

	$manager = Manager::getInstance();
	list($episodes, $soon) = $manager->getUnwatchedShows();

	$html_e = '';
	foreach ($episodes as $s) {
		$html_e .= '<div class="div-season">'
			.'<a class="a-show span-season" href="'.Url::parse('show/'.$s['id']).'">'
			.$s['name'].'</a>';
		foreach ($s['episodes'] as $e) {
			$html_e .= Manager::display_episode($e, $e['no'], $s['id'], $s['name'], $s['addic7ed']);
		}
		$html_e .= '</div>';
	}
	if (empty($html_e)) {
		$html_e = Trad::S_NO_EPISODE;
	}

	$html_s = '';
	$soon_ordered = array();
	foreach ($soon as $date => $s) {
		$date = new DateTime($date);
		$html_s .= '<div class="div-days">'
			.'<span class="span-day">'.Trad::$days[$date->format('w')].'</span>';
		foreach ($s as $e) {
			$html_s .= '<span class="span-episode">'.$e['showname'].' '.$e['no']
				.'</span>';
		}
		$html_s .= '</div>';
	}

	$title = Trad::T_HOME;

	$content = '

<h1>'.Trad::T_HOME.'</h1>

'.$html_e.'

<h2>'.Trad::T_SOON.'</h2>

'.$html_s.'

	';

?>
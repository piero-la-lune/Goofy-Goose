<?php

if (!isset($config)) {
	exit;
}

function strict_lower($a, $b) {
	$ea = explode('.', $a);
	$eb = explode('.', $b);
	for ($i=0; $i < count($ea); $i++) { 
		if (!isset($eb[$i])) { $eb[$i] = 0; }
		$na = intval($ea[$i]);
		$nb = intval($eb[$i]);
		if ($na > $nb) { return false; }
		if ($na < $nb) { return true; }
	}
	return false;
}

if (strict_lower($config['version'], '0.3')) {

	$shows = Text::unhash(get_file(FILE_SHOWS));
	foreach ($shows as $k => $s) {
		$shows[$k]['addic7ed'] = false;
	}
	update_file(FILE_SHOWS, Text::hash($shows));

}

if (strict_lower($config['version'], '1.1')) {

	$shows = Text::unhash(get_file(FILE_SHOWS));
	foreach ($shows as $k => $s) {
		$shows[$k]['download'] = false;
	}
	update_file(FILE_SHOWS, Text::hash($shows));

	$shows = Text::unhash(get_file(FILE_SHOWS));
	$date = date('Y-m-d');
	foreach ($shows as $k => $sh) {
		foreach ($sh['seasons'] as $snb => $s) {
			foreach ($s as $enb => $e) {
				$shows[$k]['seasons'][$snb][$enb]['downloaded'] = 
					(empty($e['date']) || $e['date'] >= $date) ? false : true;
			}
		}
		$shows[$k]['download'] = false;
	}
	update_file(FILE_SHOWS, Text::hash($shows));

	$config['torrent_dir'] = DIR_DATABASE;
	$config['cron_last_update'] = time();
	$config['cron_last_download'] = time();

}


$settings = new Settings();
if ($config['url_rewriting']) { $settings->url_rewriting(); }
$settings->save();

header('Content-Type: text/html; charset=utf-8');
die('Mise à jour effectuée avec succès ! Raffraichissez cette page pour accéder à Goofy Goose.');

?>
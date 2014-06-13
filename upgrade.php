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

if (strict_lower($config['version'], '0.2')) {

	$shows = Text::unhash(get_file(FILE_SHOWS));
	foreach ($shows as $s) {
		$s['addic7ed'] = false;
	}
	update_file(FILE_SHOWS, Text::hash($shows));

}

$settings = new Settings();
if ($config['url_rewriting']) { $settings->url_rewriting(); }
$settings->save();

header('Content-Type: text/html; charset=utf-8');
die('Mise à jour effectuée avec succès ! Raffraichissez cette page pour accéder à Goofy Goose.');

?>
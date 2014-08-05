<?php

if (isset($_GET['url']) && isset($_GET['show']) && isset($_GET['episode']) && isset($_GET['lang'])) {
	
	$show = str_replace(' ', '.', ucwords(str_replace('-', ' ', Text::purge($_GET['show']))));
	$language = ($_GET['lang'] == 'en') ? 'en' : 'fr';
	list($snb, $enb) = Manager::no_inv($_GET['episode']);
	$name = $show.'-'.Manager::no($snb, $enb).'-'.$language.'.srt';

	header('Content-Type: text/srt');
	header('Content-Disposition: attachment; filename="'.$name.'"');

	$process = curl_init('http://www.addic7ed.com/'.$_GET['url']);
	curl_setopt($process, CURLOPT_HTTPHEADER, array(
		'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) Gecko/20100101 Firefox/19.0',
		'Referer: http://www.addic7ed.com',
		'Host: www.addic7ed.com'
	)); 
	curl_setopt($process, CURLOPT_REFERER, 'http://www.addic7ed.com');
	curl_exec($process);
	curl_close($process);
	exit();
}

$title = 'Addi7ed';
$content = '';

?>
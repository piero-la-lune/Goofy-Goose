<?php

$manager = Manager::getInstance();

if (isset($_GET['id']) && $show = $manager->getShow($_GET['id'])) {

	$title = $show['name'];

	$content = '

<h1>'.$show['name'].'<span>'.$show['network'].'</span></h1>

<img src="'.$show['banner'].'" />

	';

	$id = intval($_GET['id']);
	foreach ($show['seasons'] as $snb => $s) {
		$content .= Manager::display_season($id, $snb, $s);
	}

}
else {

	$load = 'error/404';

}



?>
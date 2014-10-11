<?php

$manager = Manager::getInstance();

if (isset($_GET['id']) && $show = $manager->getShow($_GET['id'])) {

	$name = isset($_POST['name']) ? Text::chars($_POST['name']) : $show['name'];
	$addic7ed = isset($_POST['addic7ed']) ? $_POST['addic7ed'] : $show['addic7ed'];
	$download = isset($_POST['download']) ? $_POST['download'] :
		($show['download'] ? 'oui' : 'non');

	if (isset($_POST['action']) && $_POST['action'] == 'save') {
		$ans = $manager->update($_GET['id'], $_POST);
		if ($ans) {
			$this->addAlert(Trad::A_SUCCESS_UPDATE, 'alert-success');
			$show = $manager->getShow($_GET['id']);
		}
	}

	$title = $show['name'];

	$content = '

<h1>'.$show['name'].'<span>'.$show['network'].'</span></h1>

<img class="img-show" src="'.$show['banner'].'" />

	';

	$id = intval($_GET['id']);
	foreach ($show['seasons'] as $snb => $s) {
		$content .= Manager::display_season($id, $snb, $s, $show['name'], $addic7ed);
	}

	$content .= '

<h2>'.Trad::T_OPTIONS.'</h2>

<form action="'.Url::parse('show/'.$_GET['id']).'" method="post">

	<p><label for="name">'.Trad::F_NAME.'</label>
	<input type="text" name="name" id="name" value="'.$name.'" /></p>

	<p><label for="addic7ed">'.Trad::F_ADDIC7ED.'</label>
	<input type="text" name="addic7ed" id="addic7ed" value="'
		.Text::chars($addic7ed).'" /></p>

	<p><label for="download">'.Trad::F_DOWNLOAD.'</label>
	<select id="download" name="download">
		'.Text::options(array(
			'oui' => Trad::W_ACTIVATED,
			'non' => Trad::W_DESACTIVATED
		), $download).'
	</select>

	<p class="p-submit"><input type="submit" value="'.Trad::V_SAVE.'" /></p>
	<input type="hidden" name="action" value="save" />

</form>


	';

}
else {

	$load = 'error/404';

}



?>
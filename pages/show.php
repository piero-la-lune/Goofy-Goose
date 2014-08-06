<?php

$manager = Manager::getInstance();

if (isset($_GET['id']) && $show = $manager->getShow($_GET['id'])) {

	$addic7ed = isset($_POST['addic7ed']) ? $_POST['addic7ed'] : $show['addic7ed'];
	if (isset($_POST['action']) && $_POST['action'] == 'addic7ed') {
		$ans = $manager->update_addic7ed($_GET['id'], $_POST);
		if ($ans) {
			$this->addAlert(Trad::A_SUCCESS_ADDIC7ED, 'alert-success');
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

<h2>'.Trad::T_ADDIC7ED.'</h2>

<form action="'.Url::parse('show/'.$_GET['id']).'" method="post">

	<p><label for="addic7ed">'.Trad::F_ID.'</label>
	<input type="text" name="addic7ed" id="addic7ed" value="'
		.Text::chars($addic7ed).'" /></p>

	<p class="p-submit"><input type="submit" value="'.Trad::V_SAVE.'" /></p>
	<input type="hidden" name="action" value="addic7ed" />

</form>


	';

}
else {

	$load = 'error/404';

}



?>
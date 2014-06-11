<?php

	$showname = isset($_POST['showname']) ? $_POST['showname'] : '';

	$search = false;

	if (isset($_POST['action']) && $_POST['action'] == 'search') {
		$manager = Manager::getInstance();
		$ans = $manager->search($_POST);
		if (!is_array($ans) || count($ans) == 0) {
			$this->addAlert(Trad::A_ERROR_NOSHOW);
		}
		if (is_array($ans)) {
			$search = true;
			$shows = '';
			foreach ($ans as $id => $show) {
				$shows .= '
<li>
	<form action="'.Url::parse('add').'" method="post">
		<span>'.$show.'</span>
		<input type="submit" value="'.Trad::V_ADD.'" />
		<input type="hidden" name="action" value="add" />
		<input type="hidden" name="id" value="'.$id.'" />
	</form>
</li>
				';
			}
		}
	}
	elseif (isset($_POST['action']) && $_POST['action'] == 'add') {
		$manager = Manager::getInstance();
		$ans = $manager->add($_POST);
		if ($ans !== true) {
			$this->addAlert($ans);
		}
		else {
			$_SESSION['alert'] = array(
				'text' => Trad::A_SUCCESS_ADD,
				'type' => 'alert-success'
			);
			header('Location: '.Url::parse('show/'.intval($_POST['id'])));
		}
	}

	$title = Trad::T_ADD;

	if ($search) {

	$content = '

<h1>'.str_replace('%showname%', $showname, Trad::T_SEARCH_RESULT).'</h1>

<ul class="ul-search">
'.$shows.'
</ul>

	';

	}
	else {

	$content = '

<h1>'.Trad::T_ADD.'</h1>

<form action="'.Url::parse('add').'" method="post">

	<p><label for="showname">'.Trad::F_SHOWNAME.'</label>
	<input type="text" name="showname" id="showname" value="'
		.Text::chars($showname).'" /></p>

	<p class="p-submit"><input type="submit" value="'.Trad::V_SEARCH.'" /></p>
	<input type="hidden" name="action" value="search" />
</form>

	';

	}


?>
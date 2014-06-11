<?php

	$data = json_encode(Text::unhash(get_file(FILE_SHOWS)));

	$language = $config['language'];
	if (isset($_POST['action']) && $_POST['action'] == 'edit') {
		$settings = new Settings();
		$ans = $settings->changes($_POST);
		if (!empty($ans)) {
			foreach ($ans as $v) {
				$this->addAlert(Trad::$settings[$v]);
			}
		}
		else {
			$this->addAlert(Trad::A_SUCCESS_SETTINGS, 'alert-success');
			if ($config['language'] != $language) {
				$_SESSION['alert'] = array(
					'text' => Trad::A_SUCCESS_SETTINGS,
					'type' => 'alert-success'
				);
				header('Location: '.Url::parse('settings'));
				exit;
			}
		}
	}
	elseif (isset($_POST['action']) && $_POST['action'] == 'editdata'
		&& isset($_POST['data'])
	) {
		update_file(FILE_SHOWS, Text::hash(json_decode($_POST['data'], true)));
		$data = json_encode(Text::unhash(get_file(FILE_SHOWS)));
	}

	$title = Trad::T_SETTINGS;

	$languages = array();
	foreach (explode(',', LANGUAGES) as $v) {
		$languages[$v] = $v;
	}

	$content = '

<h1>'.Trad::T_SETTINGS.'</h1>

<form action="'.Url::parse('settings').'" method="post">

	<h2>'.Trad::T_GLOBAL_SETTINGS.'</h2>

	<p><label for="url">'.Trad::F_URL.'</label>
	<input type="url" name="url" id="url" value="'
		.Text::chars($config['url']).'" /></p>
	<p><label for="url_rewriting">'.Trad::F_URL_REWRITING.'</label>
	<input type="text" name="url_rewriting" id="url_rewriting" value="'
		.(($config['url_rewriting']) ? $config['url_rewriting'] : '').'" /></p>
	<p class="p-tip">'.Trad::F_TIP_URL_REWRITING.'</p>
	<p><label for="language">'.Trad::F_LANGUAGE.'</label>
	<select id="language" name="language">
		'.Text::options($languages, $config['language']).'
	</select></p>

	<p class="p-submit"><input type="submit" value="'.Trad::V_SAVE.'" /></p>
	<input type="hidden" name="action" value="edit" />

</form>

<p>&nbsp;</p>

<form action="'.Url::parse('settings').'" method="post">
	
	<h2>'.Trad::T_USER_SETTINGS.'</h2>

	<p><label for="login">'.Trad::F_USERNAME.'</label>
	<input type="text" name="login" id="login" value="'
		.Text::chars($config['user']['login'])
	.'" /></p>
	<p><label for="password">'.Trad::F_PASSWORD.'</label>
	<input type="password" name="password" id="password" /></p>
	<p class="p-tip">'.Trad::F_TIP_PASSWORD.'</p>

	<p class="p-submit"><input type="submit" value="'.Trad::V_SAVE.'" /></p>
	<input type="hidden" name="action" value="edit" />
</form>


<form action="'.Url::parse('settings').'" method="post">

	<h2>'.Trad::T_DATA.'</h2>
	<textarea name="data" id="data">'.Text::chars($data).'</textarea></p>

	<p class="p-submit"><input type="submit" value="'.Trad::V_SAVE.'" /></p>
	<input type="hidden" name="action" value="editdata" />

</form>
	';


?>
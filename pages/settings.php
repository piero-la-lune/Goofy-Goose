<?php

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

	$title = Trad::T_SETTINGS;

	$languages = array();
	foreach (explode(',', LANGUAGES) as $v) {
		$languages[$v] = $v;
	}

	$content = '

<form action="'.Url::parse('settings').'" method="post">

	<h2>'.Trad::T_GLOBAL_SETTINGS.'</h2>

	<label for="url">'.Trad::F_URL.'</label>
	<input type="url" name="url" id="url" value="'
		.Text::chars($config['url']).'" />
	<label for="url_rewriting">'.Trad::F_URL_REWRITING.'</label>
	<input type="text" name="url_rewriting" id="url_rewriting" value="'
		.(($config['url_rewriting']) ? $config['url_rewriting'] : '').'" />
	<p class="p-tip">'.Trad::F_TIP_URL_REWRITING.'</p>
	<label for="language">'.Trad::F_LANGUAGE.'</label>
	<select id="language" name="language">
		'.Text::options($languages, $config['language']).'
	</select>

	<p>&nbsp;</p>
	<h2>'.Trad::T_USER_SETTINGS.'</h2>

	<label for="login">'.Trad::F_USERNAME.'</label>
	<input type="text" name="login" id="login" value="'
		.Text::chars($config['user']['login'])
	.'" />
	<label for="password">'.Trad::F_PASSWORD.'</label>
	<input type="password" name="password" id="password" />
	<p class="p-tip">'.Trad::F_TIP_PASSWORD.'</p>

	<p class="p-submit"><input type="submit" value="'.Trad::V_SAVE.'" /></p>
	<input type="hidden" name="action" value="edit" />
</form>

	';


?>
<?php

class DownloadsTPB {

	protected static $domain = 'http://thepiratebay.se';
	protected static $domain_file = 'http://torrents.thepiratebay.se';
	protected static $min_seeds = 9;
	protected static $uploaders = array('Drarbg', 'eztv', 'DibyaTPB');

	public static function search($nom, $no, $showid) {
		$ch = curl_init(self::$domain.'/s/?'
			.http_build_query(array('q' => $nom.' '.$no.' 720p'))
		);
		curl_setopt_array($ch, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => 'gzip, deflate',
			CURLOPT_FOLLOWLOCATION => true
		));
		$dom = new DOMDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML(curl_exec($ch));
		libxml_clear_errors();
		$table = $dom->getElementById('searchResult');
		$table = explode('<tr>', $dom->saveHTML($table));
		$idTPB = false;
		$seedsTPB = self::$min_seeds;
		for ($i=1; $i < count($table); $i++) {
			$t = explode("\n", $table[$i]);
			$seeds = preg_replace('#(.*)>(.*)<(.*)#', '$2',
				array_shift(preg_grep('#align="right"#', $t)));
			$uploader = preg_replace('#(.*)/user/([A-Za-z0-9\._-]*)("|/)(.*)#', '$2',
				array_shift(preg_grep('#/user#', $t)));
			if ($seeds > $seedsTPB && in_array($uploader, self::$uploaders)) {
				$seedsTPB = $seeds;
				$idTPB = preg_replace('#(.*)torrent/([0-9]+)/(.*)#', '$2',
				array_shift(preg_grep('#detLink#', $t)));
			}
		}
		if ($idTPB) {
			$name_file = Manager::get_show_name($sh['name']).'-'.$no.'.torrent';
			return array(
				'id' => $idTPB,
				'name' => $name_file,
				'showid' => $showid,
				'no' => $no,
				'url' => self::$domain_file.'/'.$idTPB.'/'.$name_file
			);
		}
		return false;
	}
	
}


?>
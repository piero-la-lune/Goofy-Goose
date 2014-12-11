<?php

class DownloadsKickass {

	protected static $domain = 'http://kickass.so';
	protected static $min_seeds = 9;
	protected static $uploaders = array('Drarbg', 'eztv', 'DibyaTPB');

	public static function search($nom, $no, $showid) {
		$ch = curl_init(self::$domain.'/usearch/'
			.rawurlencode($nom.' '.$no.' 720p')
			.'/?field=seeders&sorder=desc'
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
		$table = $dom->getElementById('mainSearchTable');
		$table = $dom->saveHTML($table);
		if (preg_match('#<tr class="(odd|even)" id="(.*)">(.*)'
			.'<td>(.*)"(http://torcache(.*))"(.*)/user/(.*)/(.*)</td>(.*)'
			.'<td(.*)>(.*)</td>(.*)'
			.'<td(.*)>(.*)</td>(.*)'
			.'<td(.*)>(.*)</td>(.*)'
			.'<td(.*)>(.*)</td>(.*)'
			.'<td(.*)>(.*)</td>(.*)</tr>#isU', $table, $resultats) !== 1) {
			return false;
		}
		$id = $resultats[2];
		$url = $resultats[5];
		$uploader = $resultats[8];
		$seeds = $resultats[21];
		if ($seeds <= self::$min_seeds) { return false; }
		$name_file = Manager::get_show_name($nom).'-'.$no.'.torrent';
		return array(
			'id' => $id,
			'name' => $name_file,
			'showid' => $showid,
			'no' => $no,
			'url' => $url.'.torrent'
		);
	}
	
}


?>
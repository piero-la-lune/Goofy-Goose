<?php

# Shows model :
#	name => escaped (string)
#	network => escaped (string)
#	banner => encoded (string)
#	seasons => array()
#	addic7ed => (int)
#	download => (boolean)

class Manager {

	private static $instance;
	protected $shows = array();

	protected $curl_opts = array(
		CURLOPT_AUTOREFERER => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_MAXREDIRS => 4,
		CURLOPT_CONNECTTIMEOUT => 8,
		CURLOPT_TIMEOUT => 8,
		CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) Gecko/20100101 Firefox/19.0',
		CURLOPT_SSL_VERIFYPEER => false
	);

	public function __construct() {
		global $config;
		$this->shows = Text::unhash(get_file(FILE_SHOWS));
	}

	public static function getInstance($project = NULL) {
		if (!isset(self::$instance)) {
			self::$instance = new Manager();
		}
		return self::$instance;
	}

	protected function save() {
		update_file(FILE_SHOWS, Text::hash($this->shows));
	}

	public function getShows() {
		return $this->shows;
	}

	public function getShow($id) {
		if (isset($this->shows[$id])) { return $this->shows[$id]; }
		return false;
	}

	public function getUnwatchedShows() {
		$episodes = array();
		$soon = array();
		$date = date('Y-m-d');
		$now = new DateTime();
		foreach ($this->shows as $id => $sh) {
			$eps = array();
			foreach ($sh['seasons'] as $snb => $s) {
				foreach ($s as $enb => $e) {
					if (!$e['watched'] && !empty($e['date'])) {
						if ($e['date'] < $date) {
							$eps[] = array(
								'no' => Manager::no($snb, $enb),
								'name' => $e['name'],
								'date' => $e['date'],
								'desc' => $e['desc'],
								'watched' => $e['watched']
							);
						}
						else {
							$d = new DateTime($e['date']);
							if ($now->diff($d)->days < 6) {
								if (!isset($soon[$e['date']])) {
									$soon[$e['date']] = array();
								}
								$soon[$e['date']][] = array(
									'show' => $id,
									'showname' => $sh['name'],
									'no' => Manager::no($snb, $enb),
								);
							}
						}
					}
				}
			}
			if (!empty($eps)) {
				$episodes[] = array(
					'id' => $id,
					'name' => $sh['name'],
					'addic7ed' => $sh['addic7ed'],
					'episodes' => $eps
				);
			}
		}
		ksort($soon);
		return array($episodes, $soon);
	}

	public function getDownloads() {
		$date = date('Y-m-d');
		$downloads = array();
		foreach ($this->shows as $k => $sh) {
			if (!$sh['download']) { continue; }
			foreach ($sh['seasons'] as $snb => $s) {
				foreach ($s as $enb => $e) {
					if ($e['downloaded'] || empty($e['date'])
						|| $e['date'] >= $date) {
						continue;
					}
					$no = Manager::no($snb, $enb);
					$rep = DownloadsKickass::search($sh['name'], $no, $k);
					if ($rep) {
						$downloads[] = $rep;
					}
				}
			}
		}
		return $downloads;
	}

	public function setDownloaded($id, $no) {
		if (!isset($this->shows[$id])) { return false; }
		list($snb, $enb) = self::no_inv($no);
		if (!isset($this->shows[$id]['seasons'][$snb])) { return false; }
		if (!isset($this->shows[$id]['seasons'][$snb][$enb])) { return false; }
		$this->shows[$id]['seasons'][$snb][$enb]['downloaded'] = true;
		$this->save();
		return true;
	}

	public function search($post) {
		if (!isset($post['showname']) || empty($post['showname'])) {
			return false;
		}
		$dom = new DOMDocument();
		$dom->load('http://thetvdb.com/api/GetSeries.php?seriesname='.
			urlencode($post['showname']));
		$shows = array();
		$found = $dom->getElementsByTagName('Series');
		foreach ($found as $s) {
			$id = intval($s->getElementsByTagName('seriesid')->item(0)->nodeValue);
			if ($id > 0) {
				$shows[$id] = Text::chars($s->getElementsByTagName('SeriesName')
					->item(0)->nodeValue);
			}
		}
		return $shows;
	}

	public function add($post) {
		global $config;
		if (!isset($post['id'])) {
			return Trad::A_ERROR_ADD;
		}
		$id = intval($post['id']);
		$url = 'http://thetvdb.com/api/'.$config['thetvdb_apikey'].'/series/'.$id.'/all/en.xml';
		if (!Url::is_correct_url($url)) {
			return Trad::A_ERROR_ADD;
		}
		$dom = new DOMDocument();
		if (!$dom->load($url)) {
			return Trad::A_ERROR_NETWORK;
		}
		$show = $dom->getElementsByTagName('Series')->item(0);
		$name = $dom->getElementsByTagName('SeriesName')->item(0)->nodeValue;
		$network = $dom->getElementsByTagName('Network')->item(0)->nodeValue;
		$banner = $dom->getElementsByTagName('banner')->item(0)->nodeValue;
		$episodes = $dom->getElementsByTagName('Episode');
		$seasons = array();
		if (!isset($this->shows[$id])) {
			$this->shows[$id] = array(
				'name' => Text::chars($name),
				'banner' => 'http://thetvdb.com/banners/'.Text::chars($banner),
				'network' => Text::chars($network),
				'addic7ed' => false,
				'download' => false,
				'seasons' => array()
			);
		}
		foreach ($episodes as $e) {
			$eid = intval($e->getElementsByTagName('id')->item(0)->nodeValue);
			$enb = intval($e->getElementsByTagName('EpisodeNumber')->item(0)->nodeValue);
			$snb = intval($e->getElementsByTagName('SeasonNumber')->item(0)->nodeValue);
			$ename = $e->getElementsByTagName('EpisodeName')->item(0)->nodeValue;
			$date = $e->getElementsByTagName('FirstAired')->item(0)->nodeValue;
			$desc = $e->getElementsByTagName('Overview')->item(0)->nodeValue;
			if ($snb == 0 || $enb == 0) { continue; }
			if (!isset($this->shows[$id]['seasons'][$snb])) {
				$this->shows[$id]['seasons'][$snb] = array();
			}
			if (!isset($this->shows[$id]['seasons'][$snb][$enb])) {
				$this->shows[$id]['seasons'][$snb][$enb] = array(
					'id' => $eid,
					'date' => $date,
					'name' => Text::chars($ename),
					'desc' => Text::chars($desc),
					'watched' => false,
					'downloaded' => false
				);
			}
			else {
				$this->shows[$id]['seasons'][$snb][$enb]['date'] = $date;
				$this->shows[$id]['seasons'][$snb][$enb]['name'] = Text::chars($ename);
				$this->shows[$id]['seasons'][$snb][$enb]['desc'] = Text::chars($desc);
			}
		}
		uasort($this->shows, function($a, $b) { return $a['name']>$b['name']; });
		$this->save();
		return true;
	}

	public function update($id, $post) {
		if (!isset($this->shows[$id])
			|| !isset($post['addic7ed'])
			|| !isset($post['name'])
			|| !isset($post['download'])
		) {
			return false;
		}
		if (empty($post['addic7ed'])) { 
			$this->shows[$id]['addic7ed'] = false;
		}
		else {
			$addic7ed = intval($post['addic7ed']);
			$headers = get_headers('http://www.addic7ed.com/show/'.$addic7ed);
			if (strpos($headers[0], '200') !== false
				|| strpos($headers[0], '304')
			) {
				$this->shows[$id]['addic7ed'] = $addic7ed;
			}
		}
		if (!empty($post['name'])) {
			$this->shows[$id]['name'] = Text::chars($post['name']);
		}
		if ($post['download'] == 'oui') {
			$this->shows[$id]['download'] = true;
		}
		else {
			$this->shows[$id]['download'] = false;
		}
		$this->save();
		return true;
	}

	public function subtitles($post) {
		if (!isset($post['id']) || !isset($post['no'])) {
			return false;
		}
		$id = $post['id'];
		list($snb, $enb) = self::no_inv($post['no']);
		if (!isset($this->shows[$id])
			|| !$this->shows[$id]['addic7ed']
			|| !isset($this->shows[$id]['seasons'][$snb])
			|| !isset($this->shows[$id]['seasons'][$snb][$enb])) {
			return false;
		}
		$show = Text::purge(Text::unchars($this->shows[$id]['name']), false);
		$dom = new DOMDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTMLFile('http://www.addic7ed.com/ajax_loadShow.php?show='
			.$this->shows[$id]['addic7ed'].'&season='.$snb.'&langs=|1|8|');
		$trs = $dom->getElementsByTagName('tr');
		$subtitles = array('fr' => array(), 'en' => array());
		foreach ($trs as $tr) {
			$ok = false;
			foreach ($tr->attributes as $attr) {
				if (strpos($attr->nodeValue, 'completed') !== false) {
					$ok = true;
					break;
				}
			}
			if (!$ok) { continue; }
			$tds = $tr->getElementsByTagName('td');
			if ($tds->item(1)->nodeValue != $enb) { continue; }
			$language = ($tds->item(3)->nodeValue == 'English') ? 'en' : 'fr';
			$a = $tds->item(9)->getElementsByTagName('a')->item(0);
			$arr = array();
			if (!empty($tds->item(6)->nodeValue)) { $arr[] = 'S'; }
			if (!empty($tds->item(7)->nodeValue)) { $arr[] = 'C'; }
			if (!empty($tds->item(8)->nodeValue)) { $arr[] = 'HD'; }
			$subtitles[$language][] = array(
				'url' => Url::parse('addic7ed/'.$show.'/'.self::no($snb, $enb).'/'.$language.$a->attributes->item(0)->nodeValue),
				'version' => $tds->item(4)->nodeValue,
				'sigles' => implode(' ', $arr)
			);
		}
		return $subtitles;
	}

	public function episode_watched($post) {
		if (!isset($post['id']) || !isset($this->shows[$post['id']])
			|| !isset($post['no'])) {
			return false;
		}
		$id = intval($post['id']);
		list($snb, $enb) = Manager::no_inv($post['no']);
		if (!isset($this->shows[$id]['seasons'][$snb])
			|| !isset($this->shows[$id]['seasons'][$snb][$enb])
		) {
			return false;
		}
		if ($this->shows[$id]['seasons'][$snb][$enb]['watched']) {
			$this->shows[$id]['seasons'][$snb][$enb]['watched'] = false;
		}
		else {
			$this->shows[$id]['seasons'][$snb][$enb]['watched'] = true;
		}
		$this->save();
		return true;
	}

	public function season_watched($post) {
		if (!isset($post['id']) || !isset($this->shows[$post['id']])
			|| !isset($post['snb'])) {
			return false;
		}
		$id = intval($post['id']);
		$snb = intval($post['snb']);
		if (!isset($this->shows[$id]['seasons'][$snb])) {
			return false;
		}
		$change = false;
		foreach ($this->shows[$id]['seasons'][$snb] as $enb => $e) {
			if (!$e['watched']) {
				$this->shows[$id]['seasons'][$snb][$enb]['watched'] = true;
				$change = true;
			}
		}
		if (!$change) {
			foreach ($this->shows[$id]['seasons'][$snb] as $enb => $e) {
				$this->shows[$id]['seasons'][$snb][$enb]['watched'] = false;
			}
		}
		$this->save();
		return true;
	}

	public static function no($snb, $enb) {
		$str = 's';
		if ($snb <= 9) { $str .= '0'.$snb; }
		else { $str .= $snb; }
		$str .= 'e';
		if ($enb <= 9) { $str .= '0'.$enb; }
		else { $str .= $enb; }
		return $str;
	}

	public static function no_inv($str) {
		if (strlen($str) != 6) { return array(0, 0); }
		return array(
			intval(substr($str, 1, 2)),
			intval(substr($str, 4, 2))
		);
	}

	public static function display_season($id, $snb, $s, $showname, $addic7ed) {
		$content = '<div class="div-season">'
			.'<span class="span-season">'.
				str_replace('%nb%', $snb, Trad::W_SEASON_NB)
				.'<a href="#" class="a-season" data-s="'
					.$snb.'" data-id="'.$id.'">¶</a>'
			.'</span>';
		foreach ($s as $enb => $e) {
			$no = Manager::no($snb, $enb);
			$content .= self::display_episode($e, $no, $id, $showname, $addic7ed);
		}
		return $content.'</div>';
	}

	public static function display_episode($e, $no, $showid, $showname, $addic7ed) {
		$date = date('Y-m-d');
		$class = '';
		if ($e['watched']) {
			$class = 'watched';
		}
		elseif ($e['date'] < $date && !empty($e['date'])) {
			$class = 'released';
		}
		$subtitles = ($addic7ed) ?
			'<div class="div-title">'.Trad::T_SUBTITLES.'</div>'
			.'<div class="div-subtitles"><span class="spinner"></span></div>':
			'';
		return  '<div class="div-episode">'
			.'<a class="a-no" data-id="'.$showid.'" href="#">'
				.$no
			.'</a>'
			.'<div class="div-desc">'
				.'<div class="div-popup"><div class="div-popup-inner">'
					.'<div class="div-popup-title">'
						.'<a class="a-close" href="#">×</a>'
						.'<a class="a-watched" href="#">¶</a>'
						.'<span class="span-no '.$class.'">'.$no.'</span>'
						.'<span class="span-name">'.$e['name'].'</span>'
						
					.'</div>'
					.'<div class="div-popup-main">'
						.'<div class="div-title">'.Trad::T_INFOS.'</div>'
						.'<div class="div-infos">'
							.Trad::W_DATE
								.'<span class="span-date">'.$e['date'].'</span>'
							.'<br />'
							.Trad::W_DESC
								.'<a class="a-more" href="#">'.Trad::W_MORE.'</a>'
								.'<span class="span-desc">'.$e['desc'].'</span>'
						.'</div>'
						.'<div class="div-title">'.Trad::T_TORRENT.'</div>'
						.'<div class="div-torrent">'
							.'<a href="http://thepiratebay.se/s/?'
								.http_build_query(array(
									'q' => $showname.' '.$no
								)).'">TPB</a>'
							.'&nbsp;&nbsp;•&nbsp;&nbsp;'
							.'<a href="http://thepiratebay.se/s/?'
								.http_build_query(array(
									'q' => $showname.' '.$no.' 720p'
								)).'">TPB 720p</a>'
							.'&nbsp;&nbsp;•&nbsp;&nbsp;'
							.'<a href="http://kickass.so/usearch/'
								.rawurlencode($showname.' '.$no.' 720p')
								.'/?field=seeders&sorder=desc">Kickass 720p</a>'
						.'</div>'
						.$subtitles
					.'</div>'
				.'</div></div>'
			.'</div>'
			.'</div>';
	}

	public static function display_subtitles($subtitles) {
		$arr = array();
		foreach ($subtitles as $s) {
			$sigles = $s['sigles'];
			if (!empty($sigles)) {
				$sigles = '<span class="span-sigles">'.$sigles.'</span>';
			}
			$arr[] = '<a href="'.$s['url'].'">'.$s['version'].'</a>'.$sigles;
		}
		return implode('&nbsp;&nbsp;•&nbsp;&nbsp;', $arr);
	}

	public static function get_show_name($name) {
		return str_replace(' ', '.', str_replace('-', ' ', Text::purge($name, false)));
	}

}

?>
<?php

# Shows model :
#	name => escaped (string)
#	network => escaped (string)
#	banner => encoded (string)
#	seasons => array()

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
								'id' => $id,
								'no' => Manager::no($snb, $enb),
								'name' => $e['name']
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
					'episodes' => $eps
				);
			}
		}
		ksort($soon);
		return array($episodes, $soon);
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
		if (!isset($post['id'])) {
			return Trad::A_ERROR_ADD;
		}
		$id = intval($post['id']);
		$url = 'http://thetvdb.com/data/series/'.$id.'/all/';
		if (!Url::is_correct_url($url)) {
			return Trad::A_ERROR_ADD;
		}
		$dom = new DOMDocument();
		$dom->load($url);
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
					'watched' => false
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

	public function update_addic7ed($id, $post) {
		if (!isset($this->shows[$id]) || !isset($post['addic7ed'])) {
			return false;
		}
		if (empty($post['addic7ed'])) { 
			$this->shows[$id]['addic7ed'] = false;
		}
		else {
			$headers = get_headers('http://www.addic7ed.com/show/'.$post['addic7ed']);
			if (strpos($headers[0], '200') === false) {
				return false;
			}
			$this->shows[$id]['addic7ed'] = $post['addic7ed'];
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
				'url' => 'http://www.addic7ed.com'.$a->attributes->item(0)->nodeValue,
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

	public static function display_season($id, $snb, $s) {
		$date = date('Y-m-d');
		$content = '<h2>'.str_replace('%nb%', $snb, Trad::W_SEASON_NB)
			.'<a href="#" class="a-season" data-s="'
			.$snb.'" data-id="'.$id.'">¶</a></h2>';
		$list = '<ul class="ul-episodes">';
		$episodes = '';
		foreach ($s as $enb => $e) {
			$no = Manager::no($snb, $enb);
			$class = '';
			if ($e['watched']) {
				$class = 'watched';
			}
			elseif ($e['date'] < $date && !empty($e['date'])) {
				$class = 'released';
			}
			$list .= '<li><a href="#" class="'.$class.'" data-no="'.$no
				.'" id="a'.$no.'">'.$enb.'</a></li>';
			$episodes .= '<div class="div-episode '.$class.'" id="'.$no.'">'
				.'<a class="span-no" href="#" data-no="'
					.$no.'" data-id="'.$id.'">'.$no.'</a>'
				.'<span class="span-name">'.$e['name'].'</span>'
				.'<span class="span-date">'.$e['date'].'</span>'
				.'<span class="span-desc">'.$e['desc'].'</span></div>';
		}
		$list .= '</ul>';
		$content .= $list;
		$content .= $episodes;
		return $content;
	}

	public static function display_subtitles($subtitles) {
		$arr = array();
		foreach ($subtitles as $s) {
			$sigles = $s['sigles'];
			if (!empty($sigles)) { $sigles = ' ('.$sigles.')'; }
			$arr[] = '<a href="'.$s['url'].'">'.$s['version'].'</a>'.$sigles;
		}
		return implode(' – ', $arr);
	}

}

?>
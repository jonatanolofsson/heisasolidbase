<?PHP
class UI {
	var $churches = array();

	function register($name, $url, $obj, $color) {
		$this->churches[] = array(
			'name'	=> $name,
			'url'	=> $url,
			'obj'	=> $obj,
			'color'	=> $color
		);
	}

	function sorting($a, $b) {return ($a['when']>$b['when']?1:-1);}

	function get_all($church) {
		$meetings = $church['obj']->get_all();
		foreach($meetings as &$meeting) $meeting['church'] = &$church;
		return $meetings;
	}

	function render($cachename = false) {
		$out = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="sv" lang="sv">
<head><title>Kyrkorna i Linköping</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<meta name="description" content="startsida" />
	<link href="style.css" type="text/css" rel="stylesheet" />
</head><body><h1>Linköpings kyrkor</h1>Välkommen till denna sida för att se vad som händer i Linköpings kyrkor. Om du saknar nån kyrka, eller ser något som inte funkar, skicka ett mail till min gmail, "jonatan.olofsson" at.. osv. (@gmail.com).<br \>För den intresserade finns källkod på <a href="http://github.com/jonatanolofsson/heisasolidbase">github</a>';
		$meetings = array();
		foreach($this->churches as $church)
			$meetings = array_merge($meetings, self::get_all($church));

		usort($meetings, array('UI', 'sorting'));

		$start = time()-60*60;

		$newday = true;
		$newmonth = true;
		$lastday = false;
		$lastmonth = false;
		foreach($meetings as $meeting)
		{
			if($meeting['when'] < $start) continue;
			if(date('Ym', $meeting['when']) != $lastmonth)
			{
				if($lastmonth != false) $out .= '</div>';
				$out .= '<div class="month"><h2>'.strftime('%B', $meeting['when']).'</h2>';
				$lastmonth = date('Ym', $meeting['when']);
			}
			if(date('Ymd', $meeting['when']) != $lastday)
			{
				if($lastday != false) $out .= '</ul></div>';
				$out .= '<div class="day"><h3>'.strftime('%a, %e', $meeting['when']).'</h3><ul class="meetings">';
				$lastday = date('Ymd', $meeting['when']);
			}

			$out .= '<li class="meeting">'.strftime('%H:%M', $meeting['when']).' - '.$meeting['what'];
			$out .= '<span class="church">'.$meeting['church']['name'].'</span>';
			if($meeting['info']) $out .= '<div class="info">'.strip_tags($meeting['info'], '<p><a><b><em>').'</div>';
			//if($meeting['extra']) $out .= '<div class="extra">'.$meeting['extra'].'</div>';
			$out .= '</li>';
		}
		$out .= '</div></div></body></html>';

		if($cachename) Cache::store($cachename, $out);
		print($out);
	}
}
?>

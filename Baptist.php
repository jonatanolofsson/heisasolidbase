<?PHP
class Baptist extends Calendar {
	function __construct(){}
	protected function parse($content, $date) {
		$pattern = '<tr>[^<]+?<td[^>]*?>(.+?)</td>[^<]+?<td[^>]*?>(?<date>.+?)</td>[^<]+?<td[^>]*?>(?<time>.+?)</td>[^<]+?<td[^>]*?>(?<what>.+?)</td>';
		preg_match_all('#'.$pattern.'#is', $content, $events, PREG_SET_ORDER);

		$now = time();
		$pre_date = false;
		$parsed_events = array();
		foreach($events as $event) {
			$event['date'] = trim(str_replace('&#160;', '', strip_tags($event['date'])));
			$event['time'] = substr(trim(str_replace('&#160;', '', strip_tags($event['time']))), 0,5);

			if(!$event['time']) continue;

			$event['what'] = trim(strip_tags(str_replace('&#160;', '', $event['what'])));

			if(!$event['date']) $event['date'] = $pre_date;
			else $pre_date = $event['date'];
			$when = strtotime($event['date'].' '.$date.' '.$event['time']);
			if(!$when) {
				continue;
			}
			if($when < strtotime('-6 months')) $when = strtotime('+1 year', $when);
			$parsed_events[] = array(	'when' => $when,
										'what' => trim($event['what']),
										'where' => '',
										'info' => '',
										'extra' => '');
		}

		return $parsed_events;
	}

	function mparse($mpage) {
		$pattern = 'table_menu.*?Kalender.+?(?:<li[^>]+><a href="[^"]+"><span>[^<]+?</span></a></li>)+?</ul>';
		preg_match('#'.$pattern.'#is', $mpage, $menu);
		$pattern = '<a href="(?<url>[^"]+)"><span>(?<month>[^<]+)</span></a>';
		preg_match_all('#'.$pattern.'#is', $menu[0], $months, PREG_SET_ORDER);
		$mode = false;
		$r = array();
		foreach($months as $month) {
			if($month['month'] == 'Kalender') $mode = 1;
			elseif($mode == 1) $mode = 2;
			elseif($mode == 2){
				$r[] = array(	'url' => $month['url'],
								'month' => str_replace(	array('januari', 'februari', 'mars', 'maj', 'juni', 'juli', 'augusti', 'oktober'),
														array('january', 'february', 'march', 'may', 'june', 'july', 'august', 'october'),
														strtolower($month['month'])));
			}
		}
		return $r;
	}

	function get_all() {
		$url = 'http://www.linkopingbaptist.se/index.php?option=com_content&view=article&id=6&Itemid=4';
		$months = $this->mparse(Cache::get($url, false));
		$r = array();
		foreach($months as $month) {
			$r = array_merge($r, $this->parse(Cache::get('http://www.linkopingbaptist.se'.$month['url'], false), $month['month']));
		}
		return $r;
	}
}
?>

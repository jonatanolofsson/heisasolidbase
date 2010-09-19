<?PHP
class Mission extends Calendar {
	function __construct(){}
	protected function parse($content) {
		$pattern = '(<td[^>]*><b>(?<date>\d{1,2}).*?)?<td[^>]*?><i>(?<time>[^<]*).*?<td.*?><b>(?<what>.*?)</b><br>(?<info>.*?)</td>';
		preg_match_all('#'.$pattern.'#is', $content, $events, PREG_SET_ORDER);

		$now = time();
		$pre_date = false;
		$parsed_events = array();
		foreach($events as $event) {
			if(!$event['date']) $event['date'] = $pre_date;
			else $pre_date = $event['date'];
			$when = strtotime($event['date'].' '.$event['time']);
			if($when < $now) $when = strtotime('+1 year', $when);
			$parsed_events[] = array(	'when' => $when,
										'what' => trim($event['what']),
										'where' => '',
										'info' => trim($event['info']),
										'extra' => '');
		}

		return $parsed_events;
	}

	function get_all() {
		$urls = 'http://www.linkoping.smf.se/index.php?n=this';
		$urls = 'http://www.linkoping.smf.se/index.php?n=next';
		return $this->parse(Cache::get($urls));
	}
}
?>

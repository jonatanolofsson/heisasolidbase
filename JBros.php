<?PHP
class JBros extends Calendar {
	protected function parse($content) {
		$pattern = '(<strong>[^&>]+&nbsp;(?<date>\d{4}-\d{2}-\d{2}).*?)?<a[^>]*>(?<time>\d{2}:\d{2})(?:-\d{2}:\d{2})?&nbsp;(?<what>[^<]*).*?<strong>[^>]*>(?<where>[^<]*).*?(<strong>[^>]*>(?<info>.*?))?</td>';
		preg_match_all('#'.$pattern.'#is', $content, $events, PREG_SET_ORDER);

		$now = time();
		$pre_date = false;
		$parsed_events = array();
		foreach($events as $event) {
			if(!$event['date']) $event['date'] = $pre_date;
			else $pre_date = $event['date'];
			$when = strtotime($event['date'].' '.$event['time']);
			$parsed_events[] = array(	'when' => $when,
										'what' => trim($event['what']),
										'where' => trim($event['where']),
										'info' => (isset($event['info'])?trim($event['info']):''),
										'extra' => '');
		}

		return $parsed_events;
	}

	function get_all() {
		$array = array();
		for($i = 0;$i<=7;++$i)
			$urls = $this->source_url.date('Y-m-d', strtotime('+'.$i.' weeks'));
		return $this->parse(Cache::get($urls, true));
	}
}
?>

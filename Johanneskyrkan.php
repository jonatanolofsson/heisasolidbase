<?PHP
class Johanneskyrkan extends Calendar {
	function __construct(){}
	protected function parse($content) {
		$pattern = 'almanacka/(?<date>[^/]*)/[^>]*?><[^>]*?>(?<time>[^<]*?)</span>(?<what>[^<]*)<.*?field-content">(?<extra>(?<info><p>[^<]*?</p>.*?(<p>[^<]*?</p>)?).*?)</div>';
		preg_match_all('#'.$pattern.'#is', $content, $events, PREG_SET_ORDER);

		$now = time();
		$pre_date = false;
		$parsed_events = array();
		foreach($events as $event) {
			if(!$event['date']) $event['date'] = $pre_date;
			else $pre_date = $event['date'];
			$when = strtotime($event['date'].' '.$event['time']);
			if($when < $now) $when = strtotime('+1 year', $when);
			$parsed_events[] = array(   'when' => $when,
                                        'what' => trim($event['what']),
                                        'where' => '',
                                        'info' => nl2br(strip_tags(trim($event['info']))),
                                        'extra' => str_replace('href="/','href="http://www.johanneskyrkan.se/',
                                            nl2br(strip_tags(trim($event['extra']), '<a>')))
                                    );
		}

		return $parsed_events;
	}

	function get_all() {
        $url = 'http://www.johanneskyrkan.se/almanacka';
        return $this->parse(Cache::get($url));
	}
}
?>

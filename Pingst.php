<?PHP
class Pingst extends Calendar {
	var $url = 'http://www.lkp.pingst.se/';

	function __construct() {
		parent::__construct('http://www.lkp.pingst.se/kalender');
	}
	protected function parse($content) {
		$pattern = '(?:eventlist-date">[^\d<]*(?<date>\d{1,2}/\d{1,2}).*?)?eventlist-time">(?<time>\d{2}:\d{2}).*?a>(?<what>[^<]*).*?eventlist-location"> - (?<where>[^<]*).*?</div>[^<]*?(<div class="sioneventlist-description">(?<info>.*?)</div>[^<]*?)?(<span class="popup.*?content">(?<extra>.*?)</div>[^<]*?)?</div>';
		preg_match_all('#'.$pattern.'#is', $content, $events, PREG_SET_ORDER);

		$now = time();
		$pre_date = false;
		$parsed_events = array();
		foreach($events as $event) {
			$event['date'] = preg_replace('#(\d{2})/(\d{2})#', '$2/$1', $event['date']);
			if(!$event['date']) $event['date'] = $pre_date;
			else $pre_date = $event['date'];
			$when = strtotime($event['date'].' '.$event['time']);
			if($when < $now) $when = strtotime('+1 year', $when);
			$parsed_events[] = array(	'when' => $when,
										'what' => trim($event['what']),
										'where' => trim($event['where']),
										'info' => (isset($event['info'])?trim($event['info']):''),
										'extra' => (isset($event['extra'])?str_replace(array('src="/', 'href="/'), array('src="'.$this->url, 'href="'.$this->url), trim($event['extra'])):''));
		}

		return $parsed_events;
	}
}
?>

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
			preg_match('#(\d{2})/(\d{1,2})#', $event['date'], $m);
            $event['date'] = date('Y').'-'.($m[2]<10?'0':'').$m[2].'-'.($m[1]<10?'0':'').$m[1];
			if(!$event['date']) $event['date'] = $pre_date;
			else $pre_date = $event['date'];
			$when = strtotime($event['date'].' '.$event['time']);
			if($when < $now) $when = strtotime('+1 year', $when);
            if(($where = trim($event['where'])) == 'Pingstkyrkan Linköping') $where = '';
			$parsed_events[] = array(	'when' => $when,
										'what' => trim($event['what']),
										'where' => $where,
										'info' => (isset($event['info'])?trim($event['info']):''),
										'extra' => (isset($event['extra'])?str_replace(array('src="/', 'href="/'), array('src="'.$this->url, 'href="'.$this->url), trim($event['extra'])):''));
		}

		return $parsed_events;
	}
}
?>

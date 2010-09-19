<?PHP
class Ryttis extends Calendar {
	function __construct(){}
	protected function parse($content) {
		$pattern = '(="date">[^\d<]*(?<date>\d{2}).*?)?time">(?<time>\d{2}:\d{2}).*?title">(?<what>[^<]*).*?location">(?<where>[^<]*).*?details">(?<info>.*?)</div>';
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
										'where' => trim($event['where']),
										'info' => trim($event['info']),
										'extra' => '');
		}

		return $parsed_events;
	}

	function get_all() {
		for($i = 0;$i<=1;++$i) $urls = 'http://www.ryttargardskyrkan.se/component/option,com_show_event/Itemid,77/hela,1/newmounth,'.date('n', strtotime('+'.$i.' months')).'/newyear,'.date('Y', strtotime('+'.$i.' months')).'/typ_bon,/typ_hela,1/typ_konsert,/typ_standard,/typ_ungdom,/';
		return $this->parse(Cache::get($urls, true));
	}
}
?>

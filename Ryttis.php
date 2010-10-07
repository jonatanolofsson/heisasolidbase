<?PHP
class Ryttis extends Calendar {
	function __construct(){}
	protected function parse($content, $date) {
		$pattern = '(="date">[^\d<]*(?<date>\d{2}).*?)?time">(?<time>\d{2}:\d{2}).*?title">(?<what>[^<]*).*?location">(?<where>[^<]*).*?details">(?<info>.*?)</div>';
		preg_match_all('#'.$pattern.'#is', $content, $events, PREG_SET_ORDER);

		$now = time();
		$pre_date = false;
		$parsed_events = array();
		foreach($events as $event) {
			if(!$event['date']) $event['date'] = $pre_date;
			else $pre_date = $event['date'];
			$when = strtotime($date.'/'.$event['date'].' '.$event['time']);
			if($when < strtotime('-6 months')) $when = strtotime('+1 year', $when);
			$parsed_events[] = array(	'when' => $when,
										'what' => trim($event['what']),
										'where' => trim($event['where']),
										'info' => trim($event['info']),
										'extra' => '');
		}

		return $parsed_events;
	}

	function get_all() {
		$r = array();
		for($i = 0;$i<=1;++$i) {
			$url = 'http://www.ryttargardskyrkan.se/component/option,com_show_event/Itemid,77/hela,1/newmounth,'.date('n', strtotime('+'.$i.' months')).'/newyear,'.date('Y', strtotime('+'.$i.' months')).'/typ_bon,/typ_hela,1/typ_konsert,/typ_standard,/typ_ungdom,/';
			$r = array_merge($r, $this->parse(Cache::get($url, true), date('m', strtotime('+'.$i.' months'))));
		}
		return $r;
	}
}
?>

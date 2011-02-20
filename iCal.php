<?PHP
class iCal extends Calendar {

    protected function parse($content) {
        $pattern = 'BEGIN:VEVENT.*?START[^:]*:(?<date>\d{8})T(?<time>\d{4}).*?DESCRIPTION:(?<info>.*?)^[A-Z].*?LOCATION:(?<where>.*?)^[A-Z].*?SUMMARY:(?<what>.*?)^[A-Z]';
        preg_match_all('#'.$pattern.'#ims', $content, $events, PREG_SET_ORDER);

        $now = time();
        $pre_date = false;
        $parsed_events = array();
        foreach($events as $event) {
            $event['date'] = preg_replace('#(\d{4})(\d{2})(\d{2})#', '$1-$2-$3', $event['date']);
            $event['time'] = preg_replace('#(\d{2})(\d{2})#', '$1:$2', $event['time']);
            $when = strtotime($event['date'].' '.$event['time']);

            $repeat = false;
            if(preg_match('#RULE:FREQ=(?<freq>[^;]*?);UNTIL=(?<date>\d{8})T(?<time>\d{4})#ims', $event[0], $repeat)) {
                $repeat['date'] = preg_replace('#(\d{4})(\d{2})(\d{2})#', '$1-$2-$3', $repeat['date']);
                $repeat['time'] = preg_replace('#(\d{2})(\d{2})#', '$1:$2', $repeat['time']);
                $repeat['until'] = strtotime($repeat['date'].' '.$repeat['time']);
                switch($repeat['freq']) {
                    case 'WEEKLY':
                    default:
                        $inc = 7;
                }
            }
            else {
                $inc = false;
            }
            $counter = 0;
            do {
                $parsed_events[] = array(	'when' => $when,
                                            'what' => stripslashes(trim($event['what'])),
                                            'where' => stripslashes(trim($event['where'])),
                                            'info' => (isset($event['info'])?stripslashes(trim($event['info'])):''),
                                            'extra' => (isset($event['extra'])?str_replace(array('src="/', 'href="/'), array('src="'.$this->url, 'href="'.$this->url), stripslashes(trim($event['extra']))):''));
                ++$counter;
            } while($inc && ($when = strtotime('+'.$inc.' days', $when)) < $repeat['until'] && $counter < 30);
        }
        return $parsed_events;
    }
}
?>

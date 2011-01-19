<?PHP
class Thot extends Calendar {
    protected function parse($content) {
        $pattern = '<td[^>]*>[^<\d]*(?<date>\d{2} [^ ]*) (?<time>[^<]*).*?<a[^>]*>(?<what>[^<]*)';
        preg_match_all('#'.$pattern.'#is', $content, $events, PREG_SET_ORDER);

        $now = time();
        $parsed_events = array();
        foreach($events as $event) {
            $event['date'] = str_replace(array(' jan',' feb',' mar',' apr',' maj',' jun',' jul',' aug',' sep',' okt',' nov',' dec'), array('/1','/2','/3','/4','/5','/6','/7','/8','/9','/10','/11','/12'), $event['date']);
            $event['date'] = preg_replace('#(\d{1,2})/(\d{1,2})#', '$2/$1', $event['date']);
            $when = strtotime($event['date'].' '.$event['time']);
            if($when < $now) $when = strtotime('+1 year', $when);
            $parsed_events[] = array(   'when' => $when,
                                        'what' => trim($event['what']),
                                        'where' => '',
                                        'info' => '',
                                        'extra' => '');
        }

        return $parsed_events;
    }

    function get_all() {
        return $this->parse(Cache::get($this->source_url, true));
    }
}
?>

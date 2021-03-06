<?PHP
echo '<?xml version="1.0" encoding="UTF-8" ?>';
//Domkyrkan, thomaskyrkan
/*
$pattern = '(<strong>[^&>]+&nbsp;(?<date>\d{4}-\d{2}-\d{2}).*?)?<a[^>]*>(?<time>\d{2}:\d{2})&nbsp;(?<what>[^<]*).*?<strong>[^>]*>(?<place>[^<]*).*?<strong>[^>]*>(?<info>[^<]*)';
$subject = file_get_contents('/tmp/domkyrkan.html');
*/

//Pingstkyrkan
/*
$pattern = '(?:eventlist-date">[^\d<]*(?<date>\d{1,2}/\d{1,2}).*?)?eventlist-time">(?<time>\d{2}:\d{2}).*?a>([^<]*).*?eventlist-location"> - (?<where>[^<]*).*?</div>.*?(popup-info.*?content">(?<popup>.*?)</div>)?(eventlist-description">(?<info>.*?)</div>)?[^<]*</div>';
$subject = file_get_contents('/tmp/pingst.html');
*/

// Ryttargården
/*
$pattern = '(="date">[^\d<]*(?<date>\d{2}).*?)?time">(?<time>\d{2}:\d{2}).*?title">(?<what>[^<]*).*?location">(?<where>[^<]*).*?details">(?<info>.*?)</div>';
W$subject = file_get_contents('/tmp/ryttis.html');
*/
//Ansgar, skäggetorp, mikaelskyrkan
/*
$pattern = '<td[^>]*>[^<\d]*(?<date>\d{2} [^ ]*) (?<time>[^<]*).*?<a[^>]*>(?<what>[^<]*)';
$subject = file_get_contents('/tmp/ansgar.html');
*/
//Missionskyrkan
/*
$pattern = '(<td[^>]*><b>(?<date>\d{1,2}).*?)?<td[^>]*?><i>(?<time>[^<]*).*?<td.*?><b>(?<what>.*?)</b><br>(?<info>.*?)</td>';
$subject = file_get_contents('/tmp/mission.html');
preg_match_all('#'.$pattern.'#is', $subject, $matches, PREG_SET_ORDER);
var_dump($matches);
*/
//Johanneskyrkan
/*
$pattern = 'almanacka/(?<date>[^/]*)/[^>]*?><[^>]*?>(?<time>[^<]*?)</span>(?<what>[^<]*)<.*?field-content">(?<extra>(?<info><p>[^<]*?</p>.*?<p>[^<]*?</p>).*?)</div>';
$content = file_get_contents('/tmp/johannes.html');
preg_match_all('#'.$pattern.'#is', $content, $events, PREG_SET_ORDER);
var_dump($events);
*/

function __autoload($class) {
	include $class.'.php';
}

/**
 * Dumps the variable to the browser
 * @return mixed
 */
function dump() {
    $args = func_get_args();
    echo '<div style="background-color: pink; margin: 3px;">';
    foreach($args as $arg) {
        echo "<p><pre>";
        var_dump($arg);
        echo "</pre></p>";
    }
    echo '</div>';
    return @$args[0];
}

date_default_timezone_set('Europe/Stockholm');
if(isset($_GET['reloadcache'])) Cache::clear_cache();

if(!Cache::stream_if_recent('index', 3600-date('i')*60)) {
    $ui = new UI();
    $ui->register('Linköpings Domkyrka', 'http://www.linkopingsdomkyrka.se/', new JBros('http://www.linkopingsdomkyrka.se/default.asp?page=page&menuid=1:8&pageid=1&currdate='), '#881003');
    $ui->register('Ryttargårdskyrkan', 'http://www.ryttargardskyrkan.se/', new Ryttis(), '#108803');
    $ui->register('Pingstkyrkan', 'http://www.lkp.pingst.se/', new Pingst(), '#100388');
    $ui->register('Skäggetorps kyrka', 'http://www.svenskakyrkan.se/skaggetorp/', new Thot('http://www.thot.se/skaggetorp/'), '#fff');
    $ui->register('Ansgarskyrkan', 'http://www.ansgarskyrkanlinkoping.se/', new Thot('http://ansgarskyrkanlinkoping.se/'), '#ccc');
    $ui->register('Mikaelskyrkan', 'http://www.mikaelskyrkan.nu/', new Thot('http://www.mikaelskyrkan.nu/'), '#c00');
    $ui->register('Missionskyrkan', 'http://www.linkoping.smf.se/', new Mission('http://www.linkoping.smf.se/'), '#a35');
    $ui->register('Tomaskyrkan', 'http://www.tomaskyrkan.se/', new JBros('http://www.tomaskyrkan.se/default.asp?page=page&menuid=1:8&pageid=1&currdate='), '#fed');
    $ui->register('Baptistkyrkan', 'http://www.linkopingbaptist.se/', new Baptist(), '#fed');
    $ui->register('Johanneskyrkan', 'http://www.johanneskyrkan.se/', new Johanneskyrkan(), '#b9a870');
    $ui->register('Ny Generation', 'http://www.ng-liu.se/', new iCal('http://www.google.com/calendar/ical/tm1ehib7d0555ahbrg8pt5k75k%40group.calendar.google.com/public/basic.ics'), '#def');
    $ui->render('index');
}

?>

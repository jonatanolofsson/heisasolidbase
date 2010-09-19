<?PHP
define('CACHELIMIT', 60*60*24*1);
class Cache {
	static function get($url, $utf_convert = false) {
		if(is_string($url))
			$filebase = md5($url);
		else
			$filebase = md5(implode(',', $url));

		$files = glob("cache/$filebase*");

		$limit = time() - CACHELIMIT;
		$cached = false;
		foreach($files as $file) {
			$created = substr($file, strpos($file, '-')+1);
			if($created < $limit)
				unlink($file);
			else
				$cached = $file;
		}
		if(!$cached) $cached = self::get_url($url, $utf_convert);

		return file_get_contents($cached);
	}

	static function clear_cache() {
		unlink('cache/*');
	}

	static private function get_url($urls, $utf_convert) {
		if(!is_array($urls)) $urls = array($urls);
		$filename = 'cache/'.md5(implode(',', $urls)).'-'.time();
		$content = '';
		foreach($urls as $url)
			$content .= file_get_contents($url);
		if($utf_convert) utf8_encode($content);
		file_put_contents($filename, $content);
		return $filename;
	}
}
?>

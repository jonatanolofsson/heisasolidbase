<?PHP
define('CACHELIMIT', 60*60*24*3);
class Cache {
	static function get($name, $utf_convert = false) {
		if(is_string($name))
			$filebase = md5($name);
		else
			$filebase = md5(implode(',', $name));

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
		if(!$cached) $cached = self::get_url($name, $utf_convert);

		return file_get_contents($cached);
	}

	static function clear_cache() {
		$dir = opendir('cache');
		while($file = readdir($dir))
			if($file[0] != '.') unlink('cache/'.$file);
	}

	static private function get_url($urls, $utf_convert) {
		if(!is_array($urls)) $urls = array($urls);
		$content = '';
		foreach($urls as $url)
			$content .= @file_get_contents($url);
		if($utf_convert) $content = utf8_encode($content);
		return self::store(md5(implode(',', $urls)), $content);
	}

	static function store($name, $content) {
		$filename = 'cache/'.$name.'-'.time();
		file_put_contents($filename, $content);
		return $filename;
	}

	static function is_recent($name) {
		$files = glob("cache/$name*");
		$limit = time() - CACHELIMIT;
		foreach($files as $file) {
			$created = substr($file, strpos($file, '-')+1);
			if($created < $limit)
				unlink($file);
			else
				return true;
		}
		return false;
	}

	/**
	 * Returns true if content was sent to browser, i.e. the cache was new enough
	 */
	static function stream_if_recent($name, $lim = false) {
		if($lim === false) $lim = CACHELIMIT;
		$files = glob("cache/$name*");
		$limit = time() - $lim;
		$filename = false;
		foreach($files as $file) {
			$created = substr($file, strpos($file, '-')+1);
			if($created < $limit)
				unlink($file);
			else
				$filename = $file;
		}
		if($filename) {
			print(file_get_contents($filename));
			return true;
		} else return false;
	}
}
?>

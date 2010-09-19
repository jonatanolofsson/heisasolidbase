<?PHP
abstract class Calendar {
	var $source_url;
	function __construct($source_url) {
		$this->source_url = $source_url;
	}

	function get_all() {
		return $this->parse(Cache::get($this->source_url));
	}
}
?>

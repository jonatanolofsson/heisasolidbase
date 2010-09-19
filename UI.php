<?PHP
class UI {
	var $churches = array();

	function register($name, $url, $obj, $color) {
		$this->churches[] = array(
			'name'	=> $name,
			'url'	=> $url,
			'obj'	=> $obj,
			'color'	=> $color
		);
	}

	function render($cachename = false) {
		$out = '<table><tr>';
		foreach($this->churches as $church){
			$out .= '<td style="background: '.$church['color'].'"><h1><a href="'.$church['url'].'">'.$church['name'].'</a></h1>'.
				print_r($church['obj']->get_all(), true)
				.'</td>';
		}
		$out .= '</tr></table>';

		if($cachename) Cache::store($cachename, $out);
		print($out);
	}
}
?>

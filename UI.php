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

	function render() {
		echo '<table><tr>';
		foreach($this->churches as $church){
			echo '<td style="background: '.$church['color'].'">';
			var_dump($church['obj']->get_all());
			echo '</td>';
		}
		echo '</tr></table>';
	}
}
?>

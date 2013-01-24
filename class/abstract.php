<?php
class ClassOne {
	protected static $staticone = 'nathan';
	function changestaticone() {
		return self::$staticone = 'john';
	}

}

class ClassTwo extends ClassOne {
	public function changestaticone() {
		return self::$staticone = 'Alexey';
	}

}
$classOne = new ClassOne();
echo $classOne -> changestaticone();
echo '<hr>';
$classtwo = new ClassTwo();
echo $classtwo -> changestaticone();
?>
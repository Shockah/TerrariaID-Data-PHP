<?php

abstract class ModelObject {
	public static function toRainbow($text) {
		$rainbow = array(IRC::COLOR_RED, IRC::COLOR_ORANGE, IRC::COLOR_YELLOW, IRC::COLOR_LGREEN, IRC::COLOR_CYAN, IRC::COLOR_BLUE, IRC::COLOR_VIOLET);
		$ret = '';
		for ($i = 0; $i < strlen($text); $i++) {
			$ret .= $rainbow[$i % count($rainbow)];
			$ret .= $text[$i];
		}
		return $ret;
	}

	public static function coinValueText($value) {
		$coinC = $value;

		$coinS = floor($coinC / 100);
		$coinC %= 100;

		$coinG = floor($coinS / 100);
		$coinS %= 100;

		$coinP = floor($coinG / 100);
		$coinG %= 100;

		$txt = '';
		if ($coinP > 0) {
			if ($txt != '')
				$txt .= ' ';
			$txt .= IRC::COLOR_LGRAY.$coinP.'p';
		}
		if ($coinG > 0) {
			if ($txt != '')
				$txt .= ' ';
			$txt .= IRC::COLOR_YELLOW.$coinG.'g';
		}
		if ($coinS > 0) {
			if ($txt != '')
				$txt .= ' ';
			$txt .= IRC::COLOR_DGRAY.$coinS.'s';
		}
		if ($coinC > 0) {
			if ($txt != '')
				$txt .= ' ';
			$txt .= IRC::COLOR_DRED.$coinC.'c';
		}
		return $txt.IRC::RESET;
	}
}

?>

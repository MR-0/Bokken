<?php

class Render {

	private static $renderer = null;

	public static function proccess ($template, $data) {
		$renderer = self::$renderer;
		$rendered = $renderer ? $renderer($template, $data) : $template;
		$rendered = preg_replace("/(.+>)[\t\r\n]*/", "$1", $rendered);

		return $rendered;
	}

	public static function file ($file, $data) {
		$template = file_exists($file) ? file_get_contents($file) : null;
		
		return $template ? self::proccess($template, $data) : null;
	}

	public static function print ($template, $data) {
    $data = (array) $data;

    $data['print-data'] = function () use ($data) {
      return json_encode($data, JSON_PRETTY_PRINT);
    };
    
		$index = BOKKEN_PATH.'/views/index.php';
		$index = file_exists($index) ? file_get_contents($index) : null;
    $template = $index ? preg_replace("/{{ *> *content *}}/", $template, $index) : $template;
		
		echo self::proccess($template, $data);
	}

	public static function set ($path) {
		self::$renderer = file_exists($path) ? include $path : null;

		return null;
	}

}
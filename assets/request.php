<?php

class Request {

  public static $data = array();

	private static function loadFile ($file) {
		$ext = explode('.', $file);
		$ext = count($ext) > 1 ? array_pop($ext) : null;
		$is_php = $ext === 'php';
		$exists = file_exists($file);
		
		$data = null;
		$text = null;
		
		if ($exists) {
			
			if ($is_php) {
				ob_start();
				$data = include $file;
				$data = $data === 1 ? null : $data;
				$text = ob_get_clean();
			}
			else $text = file_get_contents($file);
		}
		
		return (object) array(
			'data' => $data,
			'text' => $text
		);
	}

	private static function loadController ($name) {
		$path = BOKKEN_PATH.'/controllers'.'/'.$name.'.php';
    $result = self::loadFile($path);

		return $result ? $result->data : null;
	}

	private static function loadView ($name) {
		$path = BOKKEN_PATH.'/views'.'/'.$name.'.php';
		$result = self::loadFile($path);

		return $result ? $result->text : null;
	}
	
	private static function mergeKeyData (&$data, $key) {
		$dat = isset($data[$key]) ? $data[$key] : null;
		$pre = isset(self::$data[$key]) ? self::$data[$key] : array();

		if ($dat) $data[$key] = array_merge($pre, $dat);
		
		return $dat ? $data[$key] : null;
	}

  public static function data ($data = null) {
		
		if ($data) {
			self::mergeKeyData($data, 'site');
    	self::$data = array_merge(self::$data, $data);
		}
		
    return self::$data;
	}
	
	public static function clean ($data = null) {
    self::$data = $data ? $data : array();
    return self::$data;
  }
	
	public static function load ($name) {

		$result = null;
		$data   = null;
		$view   = null;

		if (is_callable($name)) {
			$data = $name(self::$data);
		}
		else {
			$data = self::loadController($name);
			$view = self::loadView($name);
		}

    self::data($data);
      
    $result = (object) array(
      'data' => self::$data,
      'view' => $view
    );

		return self::$data || $view ? $result : null;
	}

}
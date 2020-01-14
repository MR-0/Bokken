<?php

class Path {

	public static $protocol;
	public static $context;
	public static $method;
	public static $base;
	public static $host;

	private static function compareMethod ($method) {
		return (object) array(
			'method' => self::$method,
			'match'  => self::$method === $method || $method === 'all',
		);
	}

	private static function comparePath ($path) {
		$keys = null;
		$vals = null;

		$regex = str_replace('/', '\/', $path);
		$regex = str_replace('/*', '/?.*', $regex);
		$regex = '/^'.preg_replace("/{[^}]+}/", '([^\/]+)', $regex).'/';
		
		$context = preg_split("/\?/", self::$context)[0];
		$context = preg_replace("/\/$/", '', $context);
		
		$query = explode('?', self::$context);
		$query = count($query) > 1 ? array_pop($query) : null;
		
		$params = array();
		
		preg_match($regex, $path, $keys);
		preg_match($regex, $context, $vals);

		array_shift($keys);
		array_shift($vals);

		foreach ($keys as $i => $key) {
			$key = preg_replace("/\{(.+)\}/", "$1", $key);
			if (isset($vals[$i])) $params[$key] = $vals[$i];
		}

		foreach ($params as $key => $val) {
			$path = str_replace('{'.$key.'}', $val, $path);
		}

		$path_regex = str_replace('/', '\/', $path);
		$path_regex = '/^'.str_replace('/*', '/?.*', $path_regex).'/';

		preg_match($path_regex, $context, $match);

		return (object) array(
			'path'    => $path,
			'context' => $context.'/',
			'match'   => count($match) > 0,
			'params'  => (object) $params,
			'query'   => $query
		);
	}

	public static function match ($method, $path) {
		$method = strtolower($method);
		$path = preg_replace('/\/$/', '', $path);
		
		$method = self::compareMethod($method);
		$path = self::comparePath($path);

		return (object) array(
			'all'    => $method->match && $path->match,
			'method' => $method,
			'path'   => $path
		);
	}

	public static function addLastSlash ($match) {
		$query = $match->path->query;
		$query = $query ? '?'.$query : '';

		$location = self::$base.$match->path->context.$query;
		
		$is_file = strpos($match->path->context, '.') !== false;
		$has = $location === self::$base.self::$context;
		$not = $match->all && !$has && self::$method === 'get';

		if ($not && !$is_file) header('Location: '.$location);

		return $not;
	}

	public static function init () {	
		$base = str_replace('/index.php', '', $_SERVER['PHP_SELF']);
		$protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http';
		$host = $_SERVER['SERVER_NAME'];
		
		$context = $base ? explode($base, $_SERVER['REQUEST_URI']) : (array) $_SERVER['REQUEST_URI'];
		$context = array_pop($context);
		$context = strtolower($context);

		$method  = strtolower($_SERVER['REQUEST_METHOD']);

		self::$protocol = $protocol;
		self::$context  = $context;
		self::$method   = $method;
		self::$base     = $base;
		self::$host     = $host;
  }
  
}
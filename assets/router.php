<?php

class Router {

	private static $matched = false;

	private static function print ($req) {
		$data = $req->data;
		$view = $req->view;

		if ( $view ) Render::print($view, $data);
		else if ( $data ) {
			header('Content-Type: application/json');
			echo json_encode($data);
		}
	}

	private static function add ($method, $path, $name, $out = true) {
		
		if (self::$matched || !$name) return null;
		
		$match = Path::match($method, $path);
		$request = null;

		if ($match->all) {
			$host = Path::$protocol.'://'.Path::$host;
			$params = $match->path->params;
			$query = null;
			
			if ($out) Path::addLastSlash($match);
			if ($match->path->query) parse_str($match->path->query, $query);
			
			$data = Request::data(array(
				'params' => $params,
				'query'  => $query,
				'url'    => array(
					'full'    => $host.Path::$base.Path::$context,
					'host'    => $host.'/',
					'base'    => $host.Path::$base.'/',
					'current' => $host.Path::$base.$match->path->context
				)
			));

			$request = Request::load($name);
		}

		if ($out && $request) {
			self::print($request);
			self::$matched = true;
		}

		return $request;
	}

	public static function init () {
		Path::init();
		self::use('/*', 'defaults');
	}

	public static function end () {
		if (!self::$matched) {
			$public_path = BOKKEN_PATH.'/public';
			$public_index = null;
			$public_indexs = array('index.php', 'index.html');

			foreach ($public_indexs as $index) {
				$index = $public_path.'/'.$index;
				$public_index = file_exists($index) ? $index : null;
				if ($public_index) break;
			}

			if ($public_index) require $public_index;
			else http_response_code(404);
		}
		return null;
	}

	public static function asset ($path, $source, $output = null) {
		$fun = function ($data) use ($source, $output) {
			$assets = isset($data['assets']) ? (array) $data['assets'] : array();
			$assets = array_merge_recursive($assets, Assets::add($source, $output));
			
			return array('assets' => $assets);
		};
		return self::add('all', $path, $fun, false);
	}

	public static function use ($path, $name = null) {
		return self::add('all', $path, $name, false);
	}

	public static function get ($path, $name = null) {
		return self::add('get', $path, $name);
	}

	public static function post ($path, $name = null) {
		return self::add('post', $path, $name);
	}

	public static function put ($path, $name = null) {
		return self::add('put', $path, $name);
	}

	public static function delete ($path, $name = null) {
		return self::add('delete', $path, $name);
	}

	public static function patch ($path, $name = null) {
		return self::add('patch', $path, $name);
	}

	public static function all ($path, $name = null) {
		return self::add('all', $path, $name);
	}

}
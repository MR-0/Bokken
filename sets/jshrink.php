<?php

require dirname(__DIR__, 1).'/vendors/JShrink/Minifier.php';

function add_js_requires ($source, $content, $update) {
	preg_match_all("/require[ \t]*\([\"'](.+)[\"']\)[ \t]*;?/", $content, $requires);
	$route = dirname($source);
	$source_date = filemtime($source);
	$arr = array();
	
	foreach ($requires[0] as $i => $str) {
		$path = preg_replace("/\.js$/i", '', $requires[1][$i]).'.js';
		$path = realpath($route.'/'.$path);

		if ($path) {
			$date = filemtime($path);
			$update = $update || $date > $source_date;
		
			$arr[] = (object) array(
				'str'  => $str,
				'path' => $path,
				'date' => $date
			);
		}
	}

	if ($update) {
		foreach ($arr as $req) {
			$cont = file_get_contents($req->path);
			$cont = add_js_requires ($req->path, $cont, $update);
			$cont = '(function(){'.$cont.'})();';
			$content = str_replace($req->str, $cont, $content);
		}
	}
	
	return $update ? $content : null;
}

return function ($source, $output) {
	$has_source = file_exists($source);
	$has_output = file_exists($output);

	$update = !$has_output;
	$update = $update ? $update : filemtime($source) > filemtime($output);

	$content = $has_source ? file_get_contents($source) : null;
	$content = $content ? add_js_requires($source, $content, $update) : null;
	
	$minified = null;
	
	if ($content) {
		try { $minified = \JShrink\Minifier::minify($content); }
		catch (Exception $e) { error_log('Error JShrink: '.$e->getMessage()); }
	}

	if ($minified) {
		$file = fopen($output, 'w');
		
		if ($file) {
			fwrite($file, $minified);
			fclose($file);
		}
		else {
			$minified = null;
		}
	}

	return (boolean) $minified;
};